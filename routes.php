<?php
include 'db_config.php';
$routeCards = '';
$result = $conn->query("SELECT * FROM routes");
while ($row = $result->fetch_assoc()) {
  $origin = htmlspecialchars($row['origin']);
  $destination = htmlspecialchars($row['destination']);
  $price = htmlspecialchars($row['price_per_seat']);
  $operator = htmlspecialchars($row['operator']);
  $routeCards .= "<div class='route-card'>
    <h3>{$origin} → {$destination}</h3>
    <p><strong>Price:</strong> RM{$price}</p>
    <p><strong>Operator:</strong> {$operator}</p>
    <a href='index.php?origin={$origin}&dest={$destination}' class='btn'>Book Now</a>
  </div>";
}
?>
