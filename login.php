<?php
include 'db_config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['username'] = $username;
      echo "<script>alert('Login successful!'); window.location='index.php';</script>";
      exit();
    } else {
      echo "<script>alert('Incorrect password.');</script>";
    }
  } else {
    echo "<script>alert('User not found.');</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - EcoTransit</title>
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(to right, #004d40, #26a69a);
    }
    .login-box {
      background: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 105, 92, 0.3);
      width: 320px;
    }
    h2 {
      text-align: center;
      margin-bottom: 1rem;
      color: #00695c;
    }
    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 0.6rem;
      margin: 0.5rem 0;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      width: 100%;
      padding: 0.6rem;
      background-color: #00695c;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
    }
    button:hover {
      background-color: #004d40;
    }
    .register-link {
      margin-top: 1rem;
      text-align: center;
      font-size: 14px;
    }
    .register-link a {
      color: #00695c;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>EcoTransit Login</h2>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <div class="register-link">
      <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>
  </div>
</body>
</html>