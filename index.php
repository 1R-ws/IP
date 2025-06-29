<?php
session_start();
$today = date('Y-m-d');

$routes = [
  ['origin' => 'Kuala Lumpur', 'destination' => 'Melaka', 'price' => 15.00],
  ['origin' => 'Kuala Lumpur', 'destination' => 'Johor Bahru', 'price' => 25.00],
  ['origin' => 'Kuala Lumpur', 'destination' => 'Ipoh', 'price' => 35.00],
  ['origin' => 'Kuala Lumpur', 'destination' => 'Seremban', 'price' => 24.00],
  ['origin' => 'Penang', 'destination' => 'Ipoh', 'price' => 18.00],
  ['origin' => 'Penang', 'destination' => 'Seremban', 'price' => 48.00],
  ['origin' => 'Penang', 'destination' => 'Melaka', 'price' => 16.00],
  ['origin' => 'Melaka', 'destination' => 'Kuala Lumpur', 'price' => 15.00],
  ['origin' => 'Melaka', 'destination' => 'Johor Bahru', 'price' => 26.00],
  ['origin' => 'Melaka', 'destination' => 'Seremban', 'price' => 15.00]
];

$origins = array_unique(array_column($routes, 'origin'));
$destinations = array_unique(array_column($routes, 'destination'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>EcoTransit</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: #f2f2f2;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header {
      background: #00695c;
      color: white;
      padding: 15px;
    }

    .container {
      width: 100%;
      max-width: 800px;
      margin: auto;
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    nav a {
      color: white;
      margin: 0 10px;
      text-decoration: none;
    }

    .booking {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      width: 100%;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      margin-bottom: 20px;
    }

    .form-grid input,
    .form-grid select {
      padding: 10px;
      font-size: 14px;
    }

    .seats-section {
      margin-top: 20px;
      text-align: center;
    }

    #seatLayout {
      display: flex;
      flex-direction: column;
      gap: 10px;
      align-items: center;
      margin-top: 10px;
    }

    .seat-row {
      display: flex;
      gap: 10px;
      justify-content: center;
    }

    .aisle-space {
      width: 30px;
    }

    .seat {
      width: 40px;
      height: 40px;
      background: #ccc;
      border: 1px solid #aaa;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.2s;
    }

    .seat:hover {
      background: #b2dfdb;
    }

    .seat.selected {
      background: #43a047;
      color: white;
      border-color: #2e7d32;
    }

    .seat.booked {
      background: #999;
      cursor: not-allowed;
      border-color: #888;
    }

    .price-summary {
      text-align: center;
      margin-top: 15px;
      font-size: 18px;
      font-weight: bold;
    }

    .btn-primary {
      background: #00695c;
      color: white;
      padding: 12px 24px;
      border: none;
      cursor: pointer;
      font-size: 16px;
      border-radius: 6px;
      margin-top: 20px;
      display: block;
      width: 100%;
    }
  </style>
</head>
<body>
  <header>
    <div class="container" style="justify-content: space-between;">
      <h1>EcoTransit</h1>
      <nav>
        <a href="#">Home</a>
        <?php if (isset($_SESSION['username'])): ?>
          <span>👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
          <a href="logout.php">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="container">
    <section class="booking">
      <h2 style="text-align:center;">Book a Seat</h2>
      <form action="book.php" method="POST" id="bookingForm">
        <div class="form-grid">
          <input type="text" name="name" placeholder="Full Name" required>
          <input type="email" name="email" placeholder="Email" required>

          <select name="origin" id="origin" required>
            <option value="">Origin</option>
            <?php foreach ($origins as $o): ?>
              <option value="<?= $o ?>"><?= $o ?></option>
            <?php endforeach; ?>
          </select>

          <select name="destination" id="destination" required>
            <option value="">Destination</option>
            <?php foreach ($destinations as $d): ?>
              <option value="<?= $d ?>"><?= $d ?></option>
            <?php endforeach; ?>
          </select>

          <input type="date" name="travel_date" id="travel_date" min="<?= $today ?>" required>

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

        <input type="hidden" name="price" id="priceField" value="0">

        <div class="price-summary">
          <p id="priceDisplay">Total Price: RM0.00</p>
        </div>

        <button type="submit" class="btn-primary">🚍 Book Now</button>
      </form>
    </section>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const routes = <?= json_encode($routes) ?>;
      const origin = document.getElementById('origin');
      const destination = document.getElementById('destination');
      const priceField = document.getElementById('priceField');
      const priceDisplay = document.getElementById('priceDisplay');
      const seatLayout = document.getElementById('seatLayout');
      const seatsInput = document.getElementById('seats');
      const dateInput = document.getElementById('travel_date');
      const selectedSeatsInput = document.getElementById('selected_seats');
      const form = document.getElementById('bookingForm');

      const totalSeats = 30;
      let selected = new Set();
      let bookedSeats = [];
      let pricePerSeat = 0;

      function updatePrice() {
        const route = routes.find(r => r.origin === origin.value && r.destination === destination.value);
        pricePerSeat = route ? parseFloat(route.price) : 0;
        priceField.value = pricePerSeat;
        priceDisplay.textContent = `Total Price: RM${(pricePerSeat * selected.size).toFixed(2)}`;
      }

      async function loadBookedSeats() {
        if (!origin.value || !destination.value || !dateInput.value) return;
        const res = await fetch(`load_booked_seats.php?origin=${origin.value}&destination=${destination.value}&date=${dateInput.value}`);
        bookedSeats = await res.json();
        renderSeats();
        updatePrice();
      }

      function renderSeats() {
        seatLayout.innerHTML = '';
        selected.clear();

        const leftSeats = [1, 4, 7, 10, 13, 16, 19, 22, 25, 28];
        const rightSeatPairs = [
          [2, 3], [5, 6], [8, 9], [11, 12], [14, 15],
          [17, 18], [20, 21], [23, 24], [26, 27], [29, 30]
        ];

        for (let i = 0; i < 10; i++) {
          const rowDiv = document.createElement('div');
          rowDiv.className = 'seat-row';

          // Left single seat
          rowDiv.appendChild(createSeatButton(leftSeats[i]));

          // Aisle
          const spacer = document.createElement('div');
          spacer.className = 'aisle-space';
          rowDiv.appendChild(spacer);

          // Right pair
          rightSeatPairs[i].forEach(num => rowDiv.appendChild(createSeatButton(num)));

          seatLayout.appendChild(rowDiv);
        }
      }

      function createSeatButton(seatNumber) {
        const seat = document.createElement('button');
        seat.textContent = seatNumber.toString().padStart(2, '0');
        seat.className = 'seat';
        seat.type = 'button';

        if (bookedSeats.includes(seatNumber.toString())) {
          seat.classList.add('booked');
          seat.disabled = true;
        }

        seat.addEventListener('click', () => {
          const max = parseInt(seatsInput.value || 0);
          if (selected.has(seatNumber)) {
            selected.delete(seatNumber);
            seat.classList.remove('selected');
          } else {
            if (selected.size >= max) {
              alert(`You can only select ${max} seat(s).`);
              return;
            }
            selected.add(seatNumber);
            seat.classList.add('selected');
          }

          selectedSeatsInput.value = Array.from(selected).join(',');
          updatePrice();
        });

        return seat;
      }

      form.addEventListener('submit', (e) => {
        const expected = parseInt(seatsInput.value);
        if (selected.size !== expected) {
          alert(`You selected ${selected.size} seat(s), but specified ${expected}. Please match them.`);
          e.preventDefault();
        }

        if (origin.value === destination.value) {
          alert("❌ Origin and destination cannot be the same.");
          e.preventDefault();
        }
      });

      dateInput.addEventListener('change', () => {
        const selectedDate = new Date(dateInput.value);
        const today = new Date();
        if (selectedDate < new Date(today.toDateString())) {
          alert("❌ You can't book for a past date.");
          dateInput.value = '';
          seatLayout.innerHTML = '';
          return;
        }
        loadBookedSeats();
      });

      [origin, destination].forEach(el => el.addEventListener('change', () => {
        if (origin.value === destination.value) {
          alert("❌ Origin and destination cannot be the same.");
          destination.value = "";
          priceField.value = 0;
          priceDisplay.textContent = "Total Price: RM0.00";
          seatLayout.innerHTML = '';
          selectedSeatsInput.value = '';
        }
      }));

      [origin, destination, seatsInput].forEach(el => el.addEventListener('change', renderSeats));
      [origin, destination].forEach(el => el.addEventListener('change', loadBookedSeats));
    });
  </script>
</body>
</html>
