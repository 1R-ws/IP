<?php
include 'db_config.php';

$name = $_POST['name'];
$email = $_POST['email'];
$origin = $_POST['origin'];
$destination = $_POST['destination'];
$date = $_POST['travel_date'];
$payment_type = $_POST['payment_type'];
$seats = $_POST['seats'];
$selected_seats = $_POST['selected_seats'];

// 🚫 Reject if travel_date is in the past
if (strtotime($date) < strtotime(date('Y-m-d'))) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Failed</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      margin-top: 100px;
      background-color: #f9f9f9;
    }
    h2 {
      color: #d32f2f;
    }
    p {
      font-size: 18px;
    }
    .btn {
      margin-top: 30px;
      display: inline-block;
      background-color: #00695c;
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      font-size: 16px;
      border-radius: 5px;
    }
    .btn:hover {
      background-color: #004d40;
    }
  </style>
</head>
<body>
  <h2>❌ Booking Failed</h2>
  <p>You cannot book a ticket for a past date. Please go back and choose a valid date.</p>
  <a href="index.php" class="btn">⬅ Back to Home</a>
</body>
</html>
<?php
  exit();
}

// ✅ Check seat availability
$seats_array = explode(',', $selected_seats);
$conflict = false;

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Result</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      margin-top: 100px;
      background-color: #f9f9f9;
    }
    h2 {
      color: #00695c;
    }
    p {
      font-size: 18px;
    }
    .btn {
      margin-top: 30px;
      display: inline-block;
      background-color: #00695c;
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      font-size: 16px;
      border-radius: 5px;
    }
    .btn:hover {
      background-color: #004d40;
    }
  </style>
</head>
<body>

<?php if ($conflict): ?>
  <h2>❌ Booking Failed</h2>
  <p>One or more of your selected seats have already been booked.</p>
  <p>Please go back and choose different seats.</p>
<?php else: ?>
  <?php
    $stmt = $conn->prepare("INSERT INTO bookings (name, email, origin, destination, travel_date, payment_type, seats, selected_seats) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssis", $name, $email, $origin, $destination, $date, $payment_type, $seats, $selected_seats);
    $stmt->execute();
  ?>
  <h2>✅ Booking Confirmed</h2>
  <p>Thank you, <strong><?= htmlspecialchars($name) ?></strong>!</p>
  <p>Your booking for <strong><?= $seats ?></strong> seat(s) from <strong><?= $origin ?></strong> to <strong><?= $destination ?></strong> on <strong><?= $date ?></strong> is confirmed.</p>
  <p>Seat Numbers: <?= $selected_seats ?></p>
<?php endif; ?>

<a href="index.php" class="btn">⬅ Back to Home</a>

</body>
</html>
