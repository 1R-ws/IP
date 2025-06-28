document.addEventListener('DOMContentLoaded', async () => {
  const seatLayout = document.getElementById('seatLayout');
  const selectedSeatsInput = document.getElementById('selected_seats');
  const priceDisplay = document.getElementById('priceDisplay');
  const seatCountInput = document.getElementById('seats');
  const form = document.getElementById('bookingForm');
  const origin = document.getElementById('origin');
  const destination = document.getElementById('destination');
  const date = document.querySelector('input[name="travel_date"]');

  const pricePerSeat = 30;
  const totalSeats = 40;
  let selected = new Set();
  let bookedSeats = [];

  async function loadBookedSeats() {
    if (!origin.value || !destination.value || !date.value) return;

    const res = await fetch(`load_booked_seats.php?origin=${origin.value}&destination=${destination.value}&date=${date.value}`);
    bookedSeats = await res.json();
    renderSeats();
  }

  function renderSeats() {
    seatLayout.innerHTML = '';
    selected.clear();

    for (let i = 1; i <= totalSeats; i++) {
      const seat = document.createElement('button');
      seat.textContent = i;
      seat.className = 'seat';
      seat.type = 'button';

      if (bookedSeats.includes(i.toString())) {
        seat.classList.add('booked');
        seat.disabled = true;
      }

      seat.addEventListener('click', () => {
        const max = parseInt(seatCountInput.value || 0);

        if (selected.has(i)) {
          selected.delete(i);
          seat.classList.remove('selected');
        } else {
          if (selected.size >= max) {
            alert(`You can only select ${max} seat(s).`);
            return;
          }
          selected.add(i);
          seat.classList.add('selected');
        }

        selectedSeatsInput.value = Array.from(selected).join(',');
        priceDisplay.textContent = `Total Price: RM${pricePerSeat * selected.size}`;
      });

      seatLayout.appendChild(seat);
    }
  }

  // Prevent form submission if selected seats don't match number
  form.addEventListener('submit', (e) => {
    const expected = parseInt(seatCountInput.value);
    if (selected.size !== expected) {
      alert(`You selected ${selected.size} seat(s), but you specified ${expected}. Please adjust.`);
      e.preventDefault();
    }
  });

  // Update seats if user changes count
  seatCountInput.addEventListener('input', renderSeats);

  [origin, destination, date].forEach(el => el.addEventListener('change', loadBookedSeats));
  loadBookedSeats();
});
