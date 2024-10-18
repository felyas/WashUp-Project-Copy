import { handleDisplayCurrentTime, showToaster, Modal } from "./dashboards-main.js";

// Initialize functions from external imports
handleDisplayCurrentTime();

// Event: Redirect to customer dashboard
const backToDashboardBtn = document.getElementById('back-to-dashboard-btn');
backToDashboardBtn.addEventListener('click', () => {
  window.location.href = './customer-dashboard.php';
});

// Function to set the minimum date and display the current date in the pick-up date input
function setMinDate() {
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
  const dd = String(today.getDate()).padStart(2, '0');
  const currentDate = `${yyyy}-${mm}-${dd}`;

  const dateInput = document.getElementById('pickup-date');

  // Set both min and value attributes to today's date
  dateInput.setAttribute('min', currentDate);
}

// Function to generate 20-minute interval time options from 8:00 AM to 9:00 PM, with unavailable times disabled
function populateTimeOptions(unavailableTimes = []) {
  const selectTime = document.getElementById('pickup-time');
  selectTime.innerHTML = ''; // Clear previous options

  const startTime = 8 * 60; // 8:00 AM in minutes
  const endTime = 21 * 60; // 9:00 PM in minutes
  const interval = 20; // 20 minutes

  // Get the current date and time
  const currentDate = new Date();
  const currentDay = currentDate.getDate();
  const currentMonth = currentDate.getMonth(); // Zero-indexed months
  const currentYear = currentDate.getFullYear();
  const currentTimeInMinutes = currentDate.getHours() * 60 + currentDate.getMinutes(); // Current time in minutes

  // Get the selected date from the date input
  const selectedDate = new Date(document.getElementById('pickup-date').value);
  const isToday = selectedDate.getDate() === currentDay &&
    selectedDate.getMonth() === currentMonth &&
    selectedDate.getFullYear() === currentYear;

  for (let time = startTime; time <= endTime; time += interval) {
    const hours = Math.floor(time / 60);
    const minutes = time % 60;

    const isPM = hours >= 12;
    const displayHours = hours % 12 || 12; // Convert to 12-hour format
    const displayMinutes = minutes.toString().padStart(2, '0');
    const ampm = isPM ? 'PM' : 'AM';

    const timeFormatted = `${displayHours}:${displayMinutes} ${ampm}`;

    const option = document.createElement('option');
    option.value = timeFormatted;
    option.textContent = timeFormatted;

    // Disable the option if it's in the unavailable times array
    if (unavailableTimes.includes(timeFormatted)) {
      option.disabled = true;
      option.textContent += ' (Unavailable)';
    }

    // Only apply past hour restriction if the selected date is today
    if (isToday) {
      const todayTime = new Date(currentYear, currentMonth, currentDay, hours, minutes); // Time being checked
      if (todayTime < currentDate) {
        option.disabled = true;
        option.textContent += ' (Past)';
      }
    }

    selectTime.appendChild(option);
  }
}

// Previous Code for populateTimeOptions
// function populateTimeOptions(unavailableTimes = []) {
//   const selectTime = document.getElementById('pickup-time');
//   selectTime.innerHTML = ''; // Clear previous options

//   const startTime = 8 * 60; // 8:00 AM in minutes
//   const endTime = 21 * 60; // 9:00 PM in minutes
//   const interval = 20; // 20 minutes

//   for (let time = startTime; time <= endTime; time += interval) {
//     const hours = Math.floor(time / 60);
//     const minutes = time % 60;

//     const isPM = hours >= 12;
//     const displayHours = hours % 12 || 12; // Convert to 12-hour format
//     const displayMinutes = minutes.toString().padStart(2, '0');
//     const ampm = isPM ? 'PM' : 'AM';

//     const timeFormatted = `${displayHours}:${displayMinutes} ${ampm}`;

//     const option = document.createElement('option');
//     option.value = timeFormatted;
//     option.textContent = timeFormatted;

//     // Disable the option if it's in the unavailable times array
//     if (unavailableTimes.includes(timeFormatted)) {
//       option.disabled = true; // Disable the option if it's already booked
//       option.textContent += ' (Unavailable)'; // Append (Unavailable) to the text
//     }

//     selectTime.appendChild(option);
//   }
// }

// Add an event listener to update the time options when the date is changed
document.getElementById('pickup-date').addEventListener('change', function () {
  populateTimeOptions(); // Call the function to repopulate the time options when the date changes
});




const addBookingForm = document.getElementById('add-booking-form');
const addBookingBtn = document.getElementById('add-booking-btn');
// Initialize date/time when the page loads
document.addEventListener("DOMContentLoaded", () => {
  setMinDate();
  populateTimeOptions();

  document.getElementById('pickup-date').addEventListener('change', async function () {
    const selectedDate = this.value; // Get the selected date
    if (selectedDate) {
      const response = await fetch(`./backend/customer_action.php?get_unavailable_times=1&date=${selectedDate}`);
      const unavailableTimes = await response.json(); // Fetch unavailable times from the backend
      populateTimeOptions(unavailableTimes); // Populate time options with unavailable times disabled
    }
  });

  // Handle the input validation from add bookings
  addBookingForm.addEventListener('input', (e) => {
    const target = e.target;
    const feedback = target.nextElementSibling;

    if (target.tagName === 'INPUT' && (target.type === 'text' || target.type === 'date' || target.type === 'time' || target.type === 'number')) {
      if (target.checkValidity()) {
        target.classList.remove('border-red-500');
        target.classList.add('border-green-700');
        if (feedback && feedback.classList.contains('text-red-500')) {
          feedback.classList.add('hidden');
        }
      } else {
        target.classList.remove('border-green-700');
        target.classList.add('border-red-500');
        if (feedback && feedback.classList.contains('text-red-500')) {
          feedback.classList.remove('hidden');
        }
      }
    }
  });

  addBookingForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(addBookingForm);
    formData.append('add', 1);

    if (addBookingForm.checkValidity() === false) {
      e.stopPropagation();

      [...addBookingForm.elements].forEach((input) => {
        if (input.tagName === 'INPUT' && (input.type === 'text' || input.type === 'date' || input.type === 'time' || input.type === 'number')) {
          const feedback = input.nextElementSibling;

          if (!input.checkValidity()) {
            input.classList.add('border-red-500');
            if (feedback && feedback.classList.contains('text-red-500')) {
              feedback.classList.remove('hidden');
            }
          } else {
            input.classList.remove('border-red-500');
            if (feedback && feedback.classList.contains('text-red-500')) {
              feedback.classList.add('hidden');
            }
          }
        }
      });
      // Scroll to the top of the page smoothly
      document.querySelector('#top').scrollIntoView({ behavior: 'smooth' });
      const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
      errorWarningModal.show();

      return false;
    } else {
      addBookingBtn.value = 'Please Wait...';

      const data = await fetch('./backend/customer_action.php', {
        method: 'POST',
        body: formData,
      });
      const response = await data.text();

      if (response.includes('success')) {
        const green600 = '#047857';
        const green700 = '#065f46';
        showToaster('Your booking has been placed !', 'check', green600, green700);

        // Disable the submit button to prevent further clicks
        addBookingBtn.disabled = true;
        addBookingBtn.classList.add('opacity-50', 'cursor-not-allowed'); // Optionally change the button style
        window.location.href = './customer-dashboard.php';

      } else {
        const red600 = '#d95f5f';
        const red700 = '#c93c3c';
        showToaster('Something went wrong !', 'exclamation-error', red600, red700);
      }
    }
  });
});

