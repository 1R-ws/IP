<?php
include 'db_config.php';

$name = $_POST['name'];
$email = $_POST['email'];
$origin = $_POST['origin'];
$destination = $_POST['destination'];
$date = $_POST['travel_date'];
$payment_type = $_POST['payment_type'];
$seats = (int)$_POST['seats'];
$selected_seats = $_POST['selected_seats'];
$price = (float)$_POST['price'];

$total_price = $price * $seats;

// ❌ Past date check
if (strtotime($date) < strtotime(date('Y-m-d'))) {
  echo "<script>alert('❌ You cannot book a ticket for a past date.'); window.location='index.php';</script>";
  exit();
}

// ✅ Check seat conflicts
$conflict = false;
$seats_array = explode(',', $selected_seats);

foreach ($seats_array as $seat) {
  $stmt = $conn->prepare("SELECT * FROM bookings WHERE origin = ? AND destination = ? AND travel_date = ? AND FIND_IN_SET(?, selected_seats)");
  $stmt->bind_param("ssss", $origin, $destination, $date, $seat);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $conflict = true;
    break;
  }
}

// ❌ Seat already taken
if ($conflict) {
  echo "<script>alert('❌ One or more selected seats are already booked. Please choose other seats.'); window.location='index.php';</script>";
  exit();
}

// ✅ Insert booking
$stmt = $conn->prepare("INSERT INTO bookings 
  (name, email, origin, destination, travel_date, payment_type, seats, selected_seats, price_per_seat, total_price)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssisdd", 
  $name, $email, $origin, $destination, $date, $payment_type, $seats, $selected_seats, $price, $total_price);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Confirmed - EcoTransit</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f0f0f0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }

    .ticket {
      background: #ffffff;
      border: 2px dashed #00695c;
      padding: 30px;
      width: 500px;
      border-radius: 15px;
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
      position: relative;
    }

    .ticket::before, .ticket::after {
      content: '';
      position: absolute;
      width: 40px;
      height: 40px;
      background: #f0f0f0;
      border-radius: 50%;
      top: 50%;
      transform: translateY(-50%);
      z-index: 1;
    }

    .ticket::before {
      left: -20px;
    }

    .ticket::after {
      right: -20px;
    }

    h2 {
      color: #00695c;
      margin-bottom: 10px;
      text-align: center;
    }

    .info-row {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid #e0e0e0;
    }

    .info-row strong {
      color: #333;
    }

    .info {
      margin: 20px 0;
    }

    .btn {
      display: block;
      text-align: center;
      margin-top: 25px;
      background-color: #00695c;
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      font-size: 16px;
      border-radius: 6px;
    }

    .btn:hover {
      background-color: #004d40;
    }
  </style>
</head>
<body>

  <div class="ticket">
    <h2>🎫 Booking Confirmed</h2>

    <div class="info">
      <div class="info-row">
        <span>Name:</span>
        <strong><?= htmlspecialchars($name) ?></strong>
      </div>
      <div class="info-row">
        <span>Email:</span>
        <strong><?= htmlspecialchars($email) ?></strong>
      </div>
      <div class="info-row">
        <span>From:</span>
        <strong><?= $origin ?></strong>
      </div>
      <div class="info-row">
        <span>To:</span>
        <strong><?= $destination ?></strong>
      </div>
      <div class="info-row">
        <span>Date:</span>
        <strong><?= $date ?></strong>
      </div>
      <div class="info-row">
        <span>Seat(s):</span>
        <strong><?= htmlspecialchars($selected_seats) ?></strong>
      </div>
      <div class="info-row">
        <span>Payment:</span>
        <strong><?= $payment_type ?></strong>
      </div>
      <div class="info-row">
        <span>Price/Seat:</span>
        <strong>RM<?= number_format($price, 2) ?></strong>
      </div>
      <div class="info-row">
        <span>Total Price:</span>
        <strong>RM<?= number_format($total_price, 2) ?></strong>
      </div>
    </div>

    <a href="index.php" class="btn">⬅ Back to Home</a>
  </div>

</body>
</html>
