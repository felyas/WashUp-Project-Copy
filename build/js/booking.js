import { handleDisplayCurrentTime, handleNotification } from "./dashboards-main.js";

handleDisplayCurrentTime();
handleNotification();

const backToDashboardBtn = document.getElementById('back-to-dashboard-btn');
const addBookingForm = document.getElementById('add-booking-form');

// Redirect to customer dashboard
backToDashboardBtn.addEventListener('click', () => {
  window.location.href = './customer-dashboard.php';
});

// Handle the input validation
addBookingForm.addEventListener('input', (e) => {
  const target = e.target;
  const feedback = target.nextElementSibling;

  if (target.tagName === 'INPUT' && target.type !== 'radio' || target.tagName === 'TEXTAREA') {
    if (target.checkValidity()) {
      target.classList.remove('border-red-500');
      target.classList.add('border-green-700'); // Change border to green
      feedback.classList.add('hidden');
    } else {
      target.classList.remove('border-green-700');
      target.classList.add('border-red-500'); // Change border to red if still invalid
      feedback.classList.remove('hidden');
    }
  }
});

// Function to format the time to 12-hour format with AM/PM
function formatTime(date) {
  const hours = date.getHours();
  const minutes = date.getMinutes();
  const period = hours >= 12 ? 'PM' : 'AM';
  const formattedHours = hours % 12 || 12;
  const formattedMinutes = minutes.toString().padStart(2, '0');
  return `${formattedHours}:${formattedMinutes} ${period}`;
}

// Function to set the current date and time in the input fields
function setCurrentDateTime() {
  const now = new Date();
  const pickupDateInput = document.querySelector('input[name="pickup_date"]');
  const pickupTimeInput = document.querySelector('input[name="pickup_time"]');

  // Set current date
  const currentDate = now.toISOString().split('T')[0];
  pickupDateInput.value = currentDate;

  // Set current time + 30 minutes
  now.setMinutes(now.getMinutes() + 30);
  const currentTime = now.toTimeString().split(' ')[0].substring(0, 5);
  pickupTimeInput.value = currentTime;
}

// Call the function when the page loads
window.addEventListener('DOMContentLoaded', (event) => {
  setCurrentDateTime();
});




// Add New Booking Ajax Request
addBookingForm.addEventListener('submit', async (e) => {
  e.preventDefault();

  const formData = new FormData(addBookingForm);
  formData.append('add', 1);

  // Form validation
  if (addBookingForm.checkValidity() === false) {
    e.stopPropagation();

    // Add validation error handling
    [...addBookingForm.elements].forEach((input) => {
      const feedback = input.nextElementSibling;

      if (input.tagName === 'INPUT' && (input.type === 'text' || input.type === 'date' || input.type === 'time')) {
        // Handle text input validation feedback
        if (!input.checkValidity()) {
          input.classList.add('border-red-500');
          feedback.classList.remove('hidden');
        } else {
          input.classList.remove('border-red-500');
          feedback.classList.add('hidden');
        }
      }
    });

    // Show SweetAlert
    Swal.fire({
      icon: 'error',
      title: 'Validation Error',
      text: 'Please fill out all required fields correctly.',
      confirmButtonText: 'OK'
    });

    return false;
  } else {
    document.getElementById('add-booking-btn').value = 'Please Wait...';
    // Show SweetAlert confirmation immediately
    Swal.fire({
      title: 'Are you sure?',
      text: "Do you want to submit the form?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      cancelButtonText: 'Cancel',
    }).then(async (result) => {
      if (result.isConfirmed) {
        const data = await fetch('./backend/customer_action.php', {
          method: 'POST',
          body: formData,
        });
        
        const response = await data.text();

        //Handle response and show SweetAlert
        if (response.includes('success')) {
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Booked successfully!',
          }).then(() => {
            document.getElementById('add-booking-btn').value = 'Submit';
            window.location.href = './customer-dashboard.php';
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Something went wrong!',
          });
        }

      }
    });
  }
});

