<?php
include 'db_config.php';

$origin = $_GET['origin'];
$destination = $_GET['destination'];
$date = $_GET['date'];

$bookedSeats = [];

$stmt = $conn->prepare("SELECT selected_seats FROM bookings WHERE origin = ? AND destination = ? AND travel_date = ?");
$stmt->bind_param("sss", $origin, $destination, $date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $seats = explode(',', $row['selected_seats']);
  $bookedSeats = array_merge($bookedSeats, $seats);
}

echo json_encode(array_unique($bookedSeats));
