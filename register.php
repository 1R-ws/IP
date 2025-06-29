<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

  $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $check->bind_param("s", $username);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows > 0) {
    echo "<script>alert('Username already taken.');</script>";
  } else {
    $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $username, $password);
    $stmt->execute();
    echo "<script>alert('Registration successful! Please login.'); window.location='login.php';</script>";
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - EcoTransit</title>
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
    .register-box {
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
    .login-link {
      margin-top: 1rem;
      text-align: center;
      font-size: 14px;
    }
    .login-link a {
      color: #00695c;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="register-box">
    <h2>EcoTransit Register</h2>
    <form method="POST">
      <input type="text" name="email" placeholder="E-mail" required>
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Register</button>
    </form>
    <div class="login-link">
      <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
  </div>
</body>
</html>