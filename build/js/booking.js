import { handleDisplayCurrentTime, showToaster, Modal } from "./dashboards-main.js";

// Initialize functions from external imports
handleDisplayCurrentTime();

// Event: Redirect to customer dashboard
const backToDashboardBtn = document.getElementById('back-to-dashboard-btn');
backToDashboardBtn.addEventListener('click', () => {
  window.location.href = './customer-dashboard.php';
});

// Function to set the minimum and maximum dates for the pick-up date input
function setMinDate() {
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
  const dd = String(today.getDate()).padStart(2, '0');

  const currentDate = `${yyyy}-${mm}-${dd}`;

  // Calculate the date 10 days from today
  const futureDate = new Date(today);
  futureDate.setDate(today.getDate() + 10);
  const futureYyyy = futureDate.getFullYear();
  const futureMm = String(futureDate.getMonth() + 1).padStart(2, '0');
  const futureDd = String(futureDate.getDate()).padStart(2, '0');

  const maxDate = `${futureYyyy}-${futureMm}-${futureDd}`;

  const dateInput = document.getElementById('pickup-date');

  // Set both min and max attributes
  dateInput.setAttribute('min', currentDate);
  dateInput.setAttribute('max', maxDate);
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

  // Fetch Customer Address From Previous Booking Ajax Request
  const customerAddress = async () => {
    const data = await fetch(`./backend/customer_action.php?customer-address=1`, {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('js-address').value = response.address ?? "";
  }
  customerAddress();

  // Fetch Customer Data From Users Table
  const customerInfo = async () => {
    const data = await fetch(`./backend/customer_action.php?customer-data=1`, {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('js-fname').value = response.first_name ?? "";
    document.getElementById('js-lname').value = response.last_name ?? "";
    document.getElementById('js-phone_number').value = response.phone_number ?? "";
  }
  customerInfo();

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

  // Function to validate a phone number in the format 09691026692
  const isPhoneNumberValid = (phoneNumber) => {
    // Check if the phone number starts with 09 and has exactly 11 digits
    const phoneRegex = /^09\d{9}$/;
    return phoneRegex.test(phoneNumber);
  };


  addBookingForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(addBookingForm);
    formData.append('add', 1);

    let hasError = false;

    // Validate Phone Number
    const phoneInput = document.getElementById('js-phone_number');
    const phoneFeedback = phoneInput?.nextElementSibling; // Feedback element for phone number
    if (phoneInput && !isPhoneNumberValid(phoneInput.value)) {
      phoneInput.classList.add('border-red-500');
      phoneFeedback?.classList.remove('hidden');
      phoneFeedback.textContent = 'Phone number must be a valid 11-digit number.';
      hasError = true;
    } else {
      phoneInput?.classList.remove('border-red-500');
      phoneFeedback?.classList.add('hidden');
    }

    // Validate First Name and Last Name
    const fnameInput = document.getElementById('js-fname');
    const lnameInput = document.getElementById('js-lname');
    const fnameFeedback = fnameInput.nextElementSibling;
    const lnameFeedback = lnameInput.nextElementSibling;

    const nameRegex = /^[a-zA-Z\s]+$/; // Allows letters and spaces

    if (!nameRegex.test(fnameInput.value)) {
      fnameInput.classList.add('border-red-500');
      fnameFeedback.classList.remove('hidden');
      fnameFeedback.textContent = 'First name must contain only letters.';
      hasError = true;
    } else {
      fnameInput.classList.remove('border-red-500');
      fnameFeedback.classList.add('hidden');
    }

    if (!nameRegex.test(lnameInput.value)) {
      lnameInput.classList.add('border-red-500');
      lnameFeedback.classList.remove('hidden');
      lnameFeedback.textContent = 'Last name must contain only letters.';
      hasError = true;
    } else {
      lnameInput.classList.remove('border-red-500');
      lnameFeedback.classList.add('hidden');
    }

    // Validate all inputs
    if (!addBookingForm.checkValidity()) {
      [...addBookingForm.elements].forEach((input) => {
        if (
          input.tagName === 'INPUT' &&
          (input.type === 'text' || input.type === 'date' || input.type === 'time' || input.type === 'number')
        ) {
          const feedback = input.nextElementSibling;

          if (!input.value.trim()) {
            input.classList.add('border-red-500');
            if (feedback && feedback.classList.contains('text-red-500')) {
              feedback.classList.remove('hidden');
              feedback.textContent = 'This field is required.';
            }
            hasError = true;
          } else {
            input.classList.remove('border-red-500');
            if (feedback && feedback.classList.contains('text-red-500')) {
              feedback.classList.add('hidden');
            }
          }
        }
      });

      document.querySelector('#top').scrollIntoView({ behavior: 'smooth' });
    }

    // Show modal if there are errors
    if (hasError) {
      const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
      errorWarningModal.show();
      return;
    } else {
      // If no errors, proceed to submit
      addBookingBtn.value = 'Please Wait...';

      const data = await fetch('./backend/customer_action.php', {
        method: 'POST',
        body: formData,
      });

      const response = await data.text();

      if (response.includes('success')) {
        showToaster('Your booking has been placed!', 'check', '#047857', '#065f46');
        addBookingBtn.disabled = true;
        addBookingBtn.classList.add('opacity-50', 'cursor-not-allowed');
        window.location.href = './customer-dashboard.php';
      } else {
        showToaster('Something went wrong!', 'exclamation-error', '#d95f5f', '#c93c3c');
      }
    }
  });




});

