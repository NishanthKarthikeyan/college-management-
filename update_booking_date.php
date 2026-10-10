<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id']) && isset($data['start'])) {
    $id = intval($data['id']);
    
    // Parse ISO8601 date string from JS
    $start_dt = new DateTime($data['start']);
    $booking_date = $start_dt->format('Y-m-d');
    $start_time = $start_dt->format('H:i:s');
    
    // If end is provided, use it, otherwise add 1 hour to start
    if (isset($data['end']) && !empty($data['end'])) {
        $end_dt = new DateTime($data['end']);
        $end_time = $end_dt->format('H:i:s');
        
        // If it's an all day event, end might be next day 00:00:00, but we just want time.
        // If end_time is 00:00:00 and start isn't, they dragged an all-day block.
    } else {
        $end_dt = clone $start_dt;
        $end_dt->modify('+1 hour');
        $end_time = $end_dt->format('H:i:s');
    }

    $stmt = $conn->prepare("UPDATE bookings SET booking_date = ?, start_time = ?, end_time = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("sssi", $booking_date, $start_time, $end_time, $id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
}
?>
