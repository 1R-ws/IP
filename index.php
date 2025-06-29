<?php include 'routes.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EcoTransit - Terminal Sentral Kuantan Style</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="container">
      <h1>EcoTransit</h1>
      <nav>
        <a href="#">Home</a>
        <a href="#routes">Routes</a>
        <a href="#about">About</a>
        <a href="#faq">FAQ</a>
      </nav>
    </div>
  </header>

  <section class="hero">
    <img src="https://upload.wikimedia.org/wikipedia/commons/e/e2/Arrival_platforms_of_Terminal_Bersepadu_Selatan.jpg" alt="Terminal Sentral Kuantan" />
    <div class="hero-text">
      <h2>Book Your Bus Journey Online</h2>
      <p>Comfortable, affordable and sustainable travel.</p>
    </div>
  </section>

  <main class="container">
    <section class="booking">
      <h2>Book a Seat</h2>
      <form action="book.php" method="POST" id="bookingForm">
        <div class="form-grid">
          <input type="text" name="name" placeholder="Full Name" required>
          <input type="email" name="email" placeholder="Email" required>

          <select name="origin" id="origin" required>
            <option value="">Origin</option>
            <option value="Kuala Lumpur">Kuala Lumpur</option>
            <option value="Penang">Penang</option>
          </select>

          <select name="destination" id="destination" required>
            <option value="">Destination</option>
            <option value="Johor Bahru">Johor Bahru</option>
            <option value="Melaka">Melaka</option>
            <option value="Seremban">Seremban</option>
            <option value="Ipoh">Ipoh</option>
            <option value="Kuala Lumpur">Kuala Lumpur</option>
          </select>

          <input type="date" name="travel_date" required min="<?= date('Y-m-d') ?>">



          <select name="payment_type" required>
            <option value="">Payment Method</option>
            <option value="Cash">Cash</option>
            <option value="Online Banking">Online Banking</option>
            <option value="E-wallet">E-wallet</option>
          </select>

          <input type="number" name="seats" id="seats" min="1" max="10" placeholder="Seats (1–10)" required>
        </div>

        <div class="seats-section">
          <label>Select Seat(s):</label>
          <div id="seatLayout"></div>
          <input type="hidden" name="selected_seats" id="selected_seats">
        </div>

        <div class="price-summary">
          <p id="priceDisplay">Total Price: RM0.00</p>
          <div id="co2-message"></div>
        </div>

        <button type="submit" class="btn-primary">🚍 Book Now</button>
      </form>
    </section>

    <section id="routes">
      <h2>Popular Routes</h2>
      <div class="route-list">
        <?php echo $routeCards; ?>
      </div>
    </section>

    <section id="about">
      <h2>About EcoTransit</h2>
      <p>EcoTransit is committed to reducing CO₂ emissions by promoting clean and efficient bus transportation. Join us in creating a greener tomorrow.</p>
    </section>

    <section id="faq">
      <h2>FAQs</h2>
      <div class="faq-item">
        <h3>Can I cancel my ticket?</h3>
        <p>Yes, up to 24 hours before departure.</p>
      </div>
     
    </section>
  </main>

  <footer>
    <div class="container">
      <p>Contact: support@ecotransit.my | Hotline: 1-300-88-8888</p>
      <p>&copy; 2025 EcoTransit. All rights reserved.</p>
    </div>
  </footer>

  <script src="script.js"></script>
</body>
</html>
