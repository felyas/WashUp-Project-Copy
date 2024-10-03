import { handleDisplayCurrentTime, handleNotification, showToaster, Modal } from "./dashboards-main.js";

// Initialize functions from external imports
handleDisplayCurrentTime();
handleNotification();

// Event: Redirect to customer dashboard
const backToDashboardBtn = document.getElementById('back-to-dashboard-btn');
backToDashboardBtn.addEventListener('click', () => {
  window.location.href = './customer-dashboard.php';
});

// Function: Format time to 12-hour format with AM/PM
const formatTime = (date) => {
  const hours = date.getHours();
  const minutes = date.getMinutes();
  const period = hours >= 12 ? 'PM' : 'AM';
  const formattedHours = hours % 12 || 12;
  const formattedMinutes = String(minutes).padStart(2, '0');
  return `${formattedHours}:${formattedMinutes} ${period}`;
};

// Function: Set current date and time in the input fields
const setCurrentDateTime = () => {
  const now = new Date();
  const pickupDateInput = document.querySelector('input[name="pickup_date"]');
  const pickupTimeInput = document.querySelector('input[name="pickup_time"]');

  // Set current date and time + 30 minutes
  pickupDateInput.value = now.toISOString().split('T')[0];
  now.setMinutes(now.getMinutes() + 30);
  pickupTimeInput.value = now.toTimeString().substring(0, 5); // Format time
};

// Initialize date/time when the page loads
window.addEventListener('DOMContentLoaded', setCurrentDateTime);


// Cache frequently accessed DOM elements
const addBookingForm = document.getElementById('add-booking-form');
const addBookingBtn = document.getElementById('add-booking-btn');


// Handle the input validation from add items
function validateForm(form) {
  form.addEventListener('input', (e) => {
    const target = e.target;
    const feedback = target.nextElementSibling;

    if (target.tagName === 'INPUT') {
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
}
validateForm(addBookingForm);

addBookingForm.addEventListener('submit', async (e) => {
  e.preventDefault();

  const formData = new FormData(addBookingForm);
  formData.append('add', 1);

  if (addBookingForm.checkValidity() === false) {
    e.stopPropagation();

    [...addBookingForm.elements].forEach((input) => {
      const feedback = input.nextElementSiblimg;

      if (input.tagName === 'INPUT' && (input.type === 'text' || input.type === 'date' || input.type === 'time')) {
        if (!input.checkValidity()) {
          input.classList.add('border-red-500');
          feedback.classList.remove('hidden');
        } else {
          input.classList.remove('border-red-500');
          feedback.classList.add('hidden');
        }
      }
    });


    const deleteWarningModal = new Modal('delete-modal', 'delete-confirm-modal', 'delete-close-modal');
    deleteWarningModal.show(deleteBooking, id);
  }
})

// // Function: Submit booking form via Ajax
// const submitBooking = async (formData) => {
//   const response = await fetch('./backend/customer_action.php', {
//     method: 'POST',
//     body: formData,
//   });

//   const result = await response.text();
//   return result;
// };

// // Event: Add New Booking Ajax Request
// addBookingForm.addEventListener('submit', async (e) => {
//   e.preventDefault();

//   // Validate the form
//   if (!addBookingForm.checkValidity()) {
//     [...addBookingForm.elements].forEach(validateInput);
//     showAlert('error', 'Validation Error', 'Please fill out all required fields correctly.');
//     return;
//   }

//   addBookingBtn.value = 'Please Wait...';

//   // Show confirmation SweetAlert
//   const confirm = await Swal.fire({
//     title: 'Are you sure?',
//     text: 'Do you want to submit the form?',
//     icon: 'warning',
//     showCancelButton: true,
//     confirmButtonText: 'Yes',
//     cancelButtonText: 'Cancel',
//     buttonsStyling: false,
//     customClass: {
//       confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-5 py-3 mr-2 font-semibold rounded-lg',
//       cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white px-5 py-3 font-semibold rounded-lg',
//     },
//   });

//   // If user confirms
//   if (confirm.isConfirmed) {
//     try {
//       const formData = new FormData(addBookingForm);
//       formData.append('add', 1);

//       const result = await submitBooking(formData);

//       if (result.includes('success')) {
//         showAlert('success', 'Success', 'Booked successfully!', 'bg-green-700 hover:bg-green-800 text-white font-semibold py-3 px-5 rounded-lg');
//         window.location.href = './customer-dashboard.php';
//       } else {
//         showAlert('error', 'Error', 'Something went wrong!');
//       }
//     } catch (error) {
//       showAlert('error', 'Error', 'Failed to submit booking!');
//     } finally {
//       addBookingBtn.value = 'Submit';
//     }
//   }
// });
