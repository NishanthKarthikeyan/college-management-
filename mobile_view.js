document.addEventListener("DOMContentLoaded", function() {
    // Inject mobile toggle button and overlay into body
    const body = document.querySelector('body');
    
    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    body.appendChild(overlay);
    
    // Create toggle button
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'mobile-toggle';
    toggleBtn.innerHTML = '<i class="bi bi-list"></i>';
    body.appendChild(toggleBtn);
    
    const sidebar = document.querySelector('.sidebar');
    
    // Toggle sidebar on button click
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('active');
        
        // Change icon based on state
        if (sidebar.classList.contains('show')) {
            toggleBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
        } else {
            toggleBtn.innerHTML = '<i class="bi bi-list"></i>';
        }
    });
    
    // Close sidebar when clicking outside (on overlay)
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('show');
        overlay.classList.remove('active');
        toggleBtn.innerHTML = '<i class="bi bi-list"></i>';
    });
    
    // Auto-close sidebar when clicking a link (optional, good for mobile)
    const sidebarLinks = document.querySelectorAll('.sidebar a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                sidebar.classList.remove('show');
                overlay.classList.remove('active');
                toggleBtn.innerHTML = '<i class="bi bi-list"></i>';
            }
        });
    });
});
