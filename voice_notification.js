document.addEventListener("DOMContentLoaded", function() {

    // ONLY trigger notification based on URL parameters (never scan DOM)
    // This prevents false triggers from status badges like "Available"
    const urlParams = new URLSearchParams(window.location.search);

    let textToSpeak = "";

    // --- Pattern 1: ?msg=some message ---
    let urlMsg = urlParams.get('msg') || urlParams.get('message');
    if (urlMsg && urlMsg.trim().length > 2) {
        textToSpeak = urlMsg.trim().replace(/\+/g, ' ');
    }

    // --- Pattern 2: ?success=1 ---
    if (!textToSpeak && urlParams.get('success') === '1') {
        textToSpeak = "Record saved successfully!";
    }

    // --- Pattern 3: ?status=updated / deleted / added / approved ---
    if (!textToSpeak) {
        let status = urlParams.get('status');
        if (status === 'updated')  textToSpeak = "Record updated successfully!";
        else if (status === 'deleted')  textToSpeak = "Record deleted successfully!";
        else if (status === 'added')    textToSpeak = "Record added successfully!";
        else if (status === 'approved') textToSpeak = "Booking approved successfully!";
        // 'error' and anything else: no announcement
    }

    // If we have a message, play sound + show toast + speak!
    if (textToSpeak && textToSpeak.length > 2) {

        // --- STEP 1: PLAY A NOTIFICATION SOUND ---
        let audio = new Audio("https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3");
        audio.play().catch(e => console.log("Audio play blocked by browser:", e));

        // --- STEP 2: SHOW A BEAUTIFUL TOAST NOTIFICATION (TOP RIGHT) ---
        let toastContainer = document.getElementById('admin-toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'admin-toast-container';
            toastContainer.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;';
            document.body.appendChild(toastContainer);
        }

        // Add animation CSS if not already added
        if (!document.getElementById('toast-style')) {
            let style = document.createElement('style');
            style.id = 'toast-style';
            style.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(120%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOutRight {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(120%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }

        let toastEl = document.createElement('div');
        toastEl.id = 'toast-' + Date.now();
        toastEl.innerHTML = `
            <div style="
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: white;
                padding: 16px 20px;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(16,185,129,0.4);
                min-width: 300px;
                max-width: 400px;
                display: flex;
                align-items: center;
                gap: 12px;
                font-family: 'Poppins', sans-serif;
                font-size: 14px;
                font-weight: 500;
                animation: slideInRight 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
                border-left: 4px solid rgba(255,255,255,0.5);
            ">
                <span style="font-size:22px;">✅</span>
                <span style="flex:1;">${textToSpeak}</span>
                <button onclick="this.closest('div[id]').remove()" style="
                    background:none;border:none;color:white;font-size:18px;
                    cursor:pointer;padding:0;line-height:1;opacity:0.8;
                ">×</button>
            </div>
        `;
        toastContainer.appendChild(toastEl);

        // Auto-remove toast after 4.5 seconds with slide-out animation
        setTimeout(() => {
            let inner = toastEl.querySelector('div');
            if (inner) {
                inner.style.animation = 'slideOutRight 0.4s ease forwards';
                setTimeout(() => toastEl.remove(), 400);
            }
        }, 4500);

        // --- STEP 3: SPEAK THE TEXT OUT LOUD (with slight delay after ding sound) ---
        setTimeout(() => {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel(); // cancel any ongoing speech
                let utterance = new SpeechSynthesisUtterance(textToSpeak);
                utterance.lang = 'en-US';
                utterance.rate = 0.95;
                utterance.pitch = 1.1;
                utterance.volume = 1;
                window.speechSynthesis.speak(utterance);
            }
        }, 600);
        // --- STEP 4: SAVE TO DATABASE SO IT SHOWS IN DASHBOARD ---
        fetch('save_notification.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: textToSpeak })
        }).catch(e => console.log("Failed to save notification:", e));

        // --- STEP 5: CLEAN UP URL TO PREVENT DUPLICATES ON REFRESH ---
        if (window.history.replaceState) {
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.delete('msg');
            newUrl.searchParams.delete('message');
            newUrl.searchParams.delete('status');
            newUrl.searchParams.delete('success');
            window.history.replaceState({ path: newUrl.href }, '', newUrl.href);
        }
    }
});
