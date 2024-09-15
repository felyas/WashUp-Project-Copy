import { handleDisplayCurrentTime, handleNotification, handleTdColor } from "./dashboards-main.js";

const bookNowBtn = document.querySelector('.js-book-now');
const editBookingForm = document.getElementById('edit-booking-form');
const tbody = document.getElementById('users-booking-list');

bookNowBtn.addEventListener('click', () => {
  window.location.href = './booking.php';
})

document.addEventListener("DOMContentLoaded", () => {
  handleTdColor();
  handleDisplayCurrentTime();
  handleNotification();
  openModal('editModalTrigger', 'toEditBookingModal', 'closeEditBookingModal', 'closeEditBookingModal2');
  openModal('viewModalTrigger', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');
});

// Function to open and close modals using classes
function openModal(modalTriggerClass, modalClass, closeModalClass, closeModalClass2) {
  // Open modal when element with modalTriggerClass is clicked
  document.body.addEventListener('click', function (event) {
    if (event.target.closest(`.${modalTriggerClass}`)) {
      event.preventDefault(); // Prevent default anchor behavior
      document.querySelector(`.${modalClass}`).classList.remove('hidden');
    }
  });

  // Close modal when element with closeModalClass is clicked
  document.body.addEventListener('click', function (event) {
    if (event.target.closest(`.${closeModalClass}`)) {
      document.querySelector(`.${modalClass}`).classList.add('hidden');
    }
  });

  // Close modal when element with closeModalClass2 is clicked
  document.body.addEventListener('click', function (event) {
    if (event.target.closest(`.${closeModalClass2}`)) {
      document.querySelector(`.${modalClass}`).classList.add('hidden');
    }
  });
}





// Fetch All Users Ajax Request
const fetchUserAllBooking = async () => {
  const data = await fetch('./backend/customer_action.php?read=1', {
    method: 'GET',
  });
  const response = await data.text();
  tbody.innerHTML = response;
}
fetchUserAllBooking();

// Edit User Ajax Request
tbody.addEventListener('click', (e) => {
  if (e.target && (e.target.matches('a.editLink') || e.target.closest('a.editLink'))) {
    e.preventDefault();
    let targetElement = e.target.matches('a.editLink') ? e.target : e.target.closest('a.editLink');
    let id = targetElement.getAttribute('id');
    editBooking(id);
  }

  // View User Ajax Request
  if (e.target && (e.target.matches('a.viewLink') || e.target.closest('a.viewLink'))) {
    e.preventDefault();
    let targetElement = e.target.matches('a.viewLink') ? e.target : e.target.closest('a.viewLink');
    let id = targetElement.getAttribute('id');
    bookingSummary(id);
  }

  // Delete User Ajax Request
  if (e.target && (e.target.matches('a.deleteLink') || e.target.closest('a.deleteLink'))) {
    e.preventDefault();
    let targetElement = e.target.matches('a.deleteLink') ? e.target : e.target.closest('a.deleteLink');
    let id = targetElement.getAttribute('id');
    deleteBooking(id);
  }
});

const editBooking = async (id) => {
  const data = await fetch(`./backend/customer_action.php?edit=1&id=${id}`, {
    method: 'GET',
  });
  const response = await data.json();
  document.getElementById('id').value = response.id;
  document.getElementById('fname').value = response.fname;
  document.getElementById('lname').value = response.lname;
  document.getElementById('pickup_date').value = response.pickup_date;
  document.getElementById('pickup_time').value = response.pickup_time;
  document.getElementById('phone_number').value = response.phone_number;
  document.getElementById('address').value = response.address;
}


// Update Booking Ajax Request
editBookingForm.addEventListener('submit', async (e) => {
  e.preventDefault();

  const formData = new FormData(editBookingForm);
  formData.append('update', 1);

  // Form validation
  if (editBookingForm.checkValidity() === false) {
    e.stopPropagation();

    // Add validation error handling
    [...editBookingForm.elements].forEach((input) => {
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
    document.getElementById('edit-booking-btn').value = 'Please Wait...';

    const data = await fetch('./backend/customer_action.php', {
      method: 'POST',
      body: formData,
    });
    const response = await data.text();
    console.log(response);

    //Handle response and show SweetAlert
    if (response.includes('success')) {
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Booking updated successfully!',
      }).then(() => {
        document.getElementById('edit-booking-btn').value = 'Save';
        editBookingForm.reset();
        fetchUserAllBooking();
        document.querySelector('.toEditBookingModal').classList.add('hidden');
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

// View Booking Ajax Request
const bookingSummary = async (id) => {
  const data = await fetch(`./backend/customer_action.php?edit=1&id=${id}`, {
    method: 'GET',
  });
  const response = await data.json();
  console.log(response);
  document.getElementById('display-full-name').innerHTML = response.fname + " " + response.lname;
  document.getElementById('display-phone-number').innerHTML = response.phone_number;
  document.getElementById('display-address').innerHTML = response.address;
  document.getElementById('display-pickup-date').innerHTML = response.pickup_date;
  document.getElementById('display-pickup-time').innerHTML = response.pickup_time;
  document.getElementById('display-service-selection').innerHTML = response.service_selection;
  document.getElementById('display-service-type').innerHTML = response.service_type;
  document.getElementById('display-suggestions').innerHTML = response.suggestions;
}

const deleteBooking = async (id) => {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: 'Do you want to cancel the booking?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
  });

  if (result.isConfirmed) {
    const data = await fetch(`./backend/customer_action.php?delete=1&id=${id}`, { 
      method: 'GET',
    });
    const response = await data.text();
    if (response.includes('success')) {
      Swal.fire('Deleted!', 'Booking deleted successfully.', 'success');
      fetchUserAllBooking();
      fetchForPickUpCount();
    } else {
      Swal.fire('Error!', 'Failed to delete booking.', 'error');
      fetchUserAllBooking();
      fetchForPickUpCount();
    }
  }
};

// Fetch the total number of bookings for each status: "for pick-up", "for delivery", and "complete"
const fetchBookingCounts = async () => {
  try {
    const response = await fetch('./backend/customer_action.php?count_all=1', {
      method: 'GET',
    });
    const data = await response.json(); // Assuming the response is in JSON format
    
    // Update the HTML elements with the counts
    document.getElementById('js-for-pickup').textContent = data.pickupCount; // For Pick-Up count
    document.getElementById('js-for-delivery').textContent = data.deliveryCount; // For Delivery count
    document.getElementById('js-for-complete-booking').textContent = data.completeCount; // Complete count
  } catch (error) {
    console.error('Error fetching booking counts:', error);
  }
};
fetchBookingCounts();


