import { handleDisplayCurrentTime, openModal } from "./dashboards-main.js";

const bookNowBtn = document.querySelector('.js-book-now');
const editBookingForm = document.getElementById('edit-booking-form');

bookNowBtn.addEventListener('click', () => {
  window.location.href = './booking.php';
})

document.addEventListener("DOMContentLoaded", () => {
  handleDisplayCurrentTime();
  openModal('editModalTrigger', 'toEditBookingModal', 'closeEditBookingModal', 'closeEditBookingModal2');
  openModal('viewModalTrigger', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');
});



const tbody = document.getElementById('users-booking-list');
const paginationContainer = document.getElementById('pagination-container');
const searchInput = document.getElementById('search-input');

// Fetch All Bookings with Pagination and Search
const fetchAllBookings = async (page = 1) => {
  const searchQuery = searchInput.value.trim();
  const response = await fetch(`./backend/customer_action.php?read=1&page=${page}&search=${searchQuery}`, {
    method: 'GET',
  });
  const data = await response.json();

  tbody.innerHTML = data.rows;
  paginationContainer.innerHTML = data.pagination; // Pagination displayed here
};

// Search Input Event Listener
searchInput.addEventListener('input', () => {
  fetchAllBookings(1); // Fetch data when search input changes
});

// Pagination Links Event Delegation
paginationContainer.addEventListener('click', (event) => {
  if (event.target.classList.contains('page-link')) {
    event.preventDefault();
    const page = event.target.dataset.page;
    fetchAllBookings(page); // Fetch data for the clicked page
  }
});

// Initial Fetch
fetchAllBookings();




// Edit Booking Ajax Request
tbody.addEventListener('click', (e) => {
  if (e.target && (e.target.matches('a.editLink') || e.target.closest('a.editLink'))) {
    e.preventDefault();
    let targetElement = e.target.matches('a.editLink') ? e.target : e.target.closest('a.editLink');
    let id = targetElement.getAttribute('id');
    editBooking(id);
  }

  // View Booking Ajax Request
  if (e.target && (e.target.matches('a.viewLink') || e.target.closest('a.viewLink'))) {
    e.preventDefault();
    let targetElement = e.target.matches('a.viewLink') ? e.target : e.target.closest('a.viewLink');
    let id = targetElement.getAttribute('id');
    bookingSummary(id);
  }

  // Delete Booking Ajax Request
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
      buttonsStyling: false, // Disable default button styling
      customClass: {
        confirmButton: 'bg-gray-700 hover:bg-gray-800 text-white font-semibold py-3 px-5 rounded-lg'
      }
    });

    return false;
  } else {
    document.getElementById('edit-booking-btn').value = 'Please Wait...';

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
        text: 'Booking updated successfully!',
        buttonsStyling: false, // Disable default button styling
        customClass: {
          confirmButton: 'bg-green-700 hover:bg-green-800 text-white font-semibold py-3 px-5 rounded-lg'
        }
      }).then(() => {
        document.getElementById('edit-booking-btn').value = 'Save';
        editBookingForm.reset();
        fetchAllBookings();
        document.querySelector('.toEditBookingModal').classList.add('hidden');
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Something went wrong!',
        buttonsStyling: false, // Disable default button styling
        customClass: {
          confirmButton: 'bg-gray-700 hover:bg-gray-800 text-white font-semibold py-3 px-5 rounded-lg'
        }
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
  document.getElementById('created_at').innerHTML = response.created_at;
  document.getElementById('display-full-name').innerHTML = response.fname + " " + response.lname;
  document.getElementById('display-phone-number').innerHTML = response.phone_number;
  document.getElementById('display-address').innerHTML = response.address;
  document.getElementById('display-pickup-date').innerHTML = response.pickup_date;
  document.getElementById('display-pickup-time').innerHTML = response.pickup_time;
  document.getElementById('display-service-selection').innerHTML = response.service_selection;
  document.getElementById('display-service-type').innerHTML = response.service_type;
  document.getElementById('display-suggestions').innerHTML = response.suggestions;
}

// Deleting Booking Ajax Request
const deleteBooking = async (id) => {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: 'Do you want to cancel the booking?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    customClass: {
      confirmButton: 'bg-red-700 hover:bg-red-800 text-white px-5 py-3 mr-4 font-semibold rounded-lg',
      cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white px-5 py-3 font-semibold rounded-lg'
    },
    buttonsStyling: false
  });

  if (result.isConfirmed) {
    const data = await fetch(`./backend/customer_action.php?delete=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.text();
    if (response.includes('success')) {
      Swal.fire('Deleted!', 'Booking deleted successfully.', 'success');
      fetchAllBookings();
      fetchForPickUpCount();
    } else {
      Swal.fire('Error!', 'Failed to delete booking.', 'error');
      fetchAllBookings();
      fetchForPickUpCount();
    }
  }
};

// Fetch the total number of bookings for each status: "for pick-up", "for delivery", and "complete"
const fetchBookingCounts = async () => {
  try {
    const data = await fetch('./backend/customer_action.php?count_all=1', {
      method: 'GET',
    });
    const response = await data.json(); // Assuming the response is in JSON format

    // Update the HTML elements with the counts
    document.getElementById('js-for-pickup').textContent = response.pickupCount; // For Pick-Up count
    document.getElementById('js-for-delivery').textContent = response.deliveryCount; // For Delivery count
    document.getElementById('js-for-complete-booking').textContent = response.completeCount; // Complete count
  } catch (error) {
    console.error('Error fetching booking counts:', error);
  }
};
fetchBookingCounts();

// Long polling function for fetching notifications
const fetchNotifications = async (lastCheckTime) => {
  try {
    const response = await fetch(`./backend/customer_action.php?fetch_notifications=1&last_check=${lastCheckTime}`);
    const notifications = await response.json();

    const notificationContainer = document.querySelector('.js-notification-messages');
    const notificationDot = document.querySelector('.js-notification-dot'); // Red dot element
    const totalNotificationsElement = document.querySelector('.js-total-notifications'); // Total notifications element

    // If there are new notifications, display them
    if (notifications.length > 0) {
      // Clear existing messages
      notificationContainer.innerHTML = '';

      // Append each notification to the container
      notifications.forEach(notification => {
        const notificationElement = document.createElement('div');
        notificationElement.classList.add('p-2', 'flex', 'items-center', 'justify-between', 'bg-gray-200', 'mb-1');
        notificationElement.innerHTML = `
          <p class="w-auto">Booking #${notification.id} status updated to "${notification.status}"</p>
          <button class="w-12 p-0 border-none font-bold js-notification-close" data-id="${notification.id}">&#10005;</button>
        `;
        notificationContainer.appendChild(notificationElement);
      });

      // Update total notification count
      totalNotificationsElement.textContent = notifications.length;
      notificationDot.classList.remove('hidden');
      // Calling other function to update the UI
      fetchAllBookings();
      fetchBookingCounts();
    } else {
      notificationDot.classList.add('hidden');
    }

    // Continue long polling after 5 seconds
    setTimeout(() => {
      const currentTimestamp = new Date().toISOString(); // Use current time as last check
      fetchNotifications(currentTimestamp);
    }, 1000); // Check every 5 seconds
  } catch (error) {
    console.error('Error fetching notifications:', error);
  }
};

// Initial call to start long polling
let initialTimestamp = new Date().toISOString(); // Start with the current time
fetchNotifications(initialTimestamp);

// Toggle notification dropdown visibility on bell icon click
document.querySelector('.js-notification-button').addEventListener('click', () => {
  const notificationDropdown = document.querySelector('.js-notification');
  const notificationDot = document.querySelector('.js-notification-dot');

  // Show or hide the notification dropdown
  notificationDropdown.classList.toggle('hidden');

  // Hide the red dot once notifications are viewed
  if (!notificationDropdown.classList.contains('hidden')) {
    notificationDot.classList.add('hidden'); // Hide the red dot
  }
});

// Handle closing individual notifications
document.addEventListener('click', async (e) => {
  if (e.target.classList.contains('js-notification-close')) {
    const notificationId = e.target.getAttribute('data-id');
    e.target.parentElement.remove(); // Remove notification from UI

    // Send a request to the server to mark this notification as read
    const response = await fetch(`./backend/customer_action.php?mark_as_read=1&id=${notificationId}`);
    const data = await response.json();

    if (data.success) {
      console.log('Notification marked as read.');
    } else {
      console.error('Failed to mark notification as read.');
    }
  }
});













