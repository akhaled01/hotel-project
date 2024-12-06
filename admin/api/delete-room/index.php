<?php

include "../../../db_connection.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Decode JSON input
    $input = json_decode(file_get_contents("php://input"), true);

    // Validate input
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Room ID is required."
        ]);
        return;
    }

    $room_id = $input['id'];

    try {
        $db = db_connect();

        // Delete related bookings
        $stmt = $db->prepare("DELETE FROM Bookings WHERE room_id = ?");
        $stmt->execute([$room_id]);

        // Delete room
        $stmt = $db->prepare("DELETE FROM Rooms WHERE room_id = ?");
        $stmt->execute([$room_id]);

        echo json_encode([
            "success" => true,
            "message" => "Room and related bookings deleted successfully."
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
}
