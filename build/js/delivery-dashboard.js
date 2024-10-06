import { handleDisplayCurrentTime, openModal, showToaster, Modal } from "./dashboards-main.js";

handleDisplayCurrentTime();
openModal('viewModalTrigger', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');

// Get the modal and the close button
// const modal = document.getElementById('warning-modal');
// const confirmModalBtn = document.getElementById('confirm-modal');
// const closeModalBtn = document.getElementById('close-modal');
// let currentAction = null; // To store the action function
// let bookingId = null; // To store the booking ID

// // Function to show the modal with callback and booking ID
// function showModal(callback, id) {
//   currentAction = callback; // Store the action to execute when 'Yes' is clicked
//   bookingId = id; // Store the booking ID
//   modal.classList.remove('hidden'); // Show the modal
// }

// // Function to hide the modal
// closeModalBtn.addEventListener('click', () => {
//   modal.classList.add('hidden'); // Hide the modal
// });

// // Function to execute the action when 'Yes' is clicked
// confirmModalBtn.addEventListener('click', () => {
//   if (currentAction && bookingId) {
//     currentAction(bookingId); // Call the stored action function with booking ID
//     modal.classList.add('hidden'); // Hide the modal after confirmation
//   }
// });


document.addEventListener("DOMContentLoaded", () => {
  const tbody = document.getElementById('users-booking-list');
  const paginationContainer = document.getElementById('pagination-container');
  const searchInput = document.getElementById('js-search-bar');
  const statusFilter = document.getElementById('status-filter');

  // Function to toggle sorting icons 
  const toggleSortIcon = (th, order) => {
    const allIcons = document.querySelectorAll('.sort-icon img');

    // Reset all icons to caret-down by default
    allIcons.forEach(icon => {
      icon.setAttribute('src', './img/icons/caret-down.svg'); // Set to down arrow
    });

    const icon = th.querySelector('.sort-icon img');
    if (order === 'desc') {
      icon.setAttribute('src', './img/icons/caret-down.svg'); // Down arrow
    } else {
      icon.setAttribute('src', './img/icons/caret-up.svg'); // Up arrow
    }
  };

  // Fetch All items with pagination, search, and sorting
  const fetchAll = async (page = 1, column = 'id', order = 'desc', query = '') => {
    const searchQuery = searchInput.value.trim() || query;
    const statusQuery = status || statusFilter.value; // Get status from dropdown or passed value

    const data = await fetch(`./backend/delivery_action.php?readAll=1&page=${page}&column=${column}&order=${order}&query=${searchQuery}&status=${statusQuery}`, {
      method: 'GET',
    });
    const response = await data.json();
    tbody.innerHTML = response.bookings;
    paginationContainer.innerHTML = response.pagination;
  }

  // Handle Column Sorting 
  document.querySelectorAll('.sortable').forEach(th => {
    th.addEventListener('click', () => {
      const column = th.getAttribute('data-column');
      let order = th.getAttribute('data-order');
      order = order === 'desc' ? 'asc' : 'desc';

      th.setAttribute('data-order', order);
      toggleSortIcon(th, order);

      fetchAll(1, column, order);
    });
  });

  // Handle search input
  searchInput.addEventListener('input', () => {
    fetchAll();
  });

  paginationContainer.addEventListener('click', (e) => {
    if (e.target.classList.contains('pagination-link')) {
      e.preventDefault();
      const page = e.target.getAttribute('data-page');
      fetchAll(page);
    }
  });

  statusFilter.addEventListener('change', () => {
    fetchAll();
  })

  // Initial fetch
  fetchAll();

  //Targeting anchor tags from tbody
  tbody.addEventListener('click', (e) => {
    // Target View Link
    if (e.target && (e.target.matches('a.viewLink') || e.target.closest('a.viewLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.viewLink') ? e.target : e.target.closest('a.viewLink');
      let id = targetElement.getAttribute('id');
      viewSummary(id);
    }

    // Target Pickup Link
    if (e.target && (e.target.matches('a.pickupLink') || e.target.closest('a.pickupLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.pickupLink') ? e.target : e.target.closest('a.pickupLink');
      let id = targetElement.getAttribute('id');
      // Customer Confirmation Modal
      const warningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal');
      warningModal.show(updatePickup, id);
      // showModal(updatePickup, id);
    }

    // Target DeliveryLink
    if (e.target && (e.target.matches('a.deliveryLink') || e.target.closest('a.deliveryLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.deliveryLink') ? e.target : e.target.closest('a.deliveryLink');
      let id = targetElement.getAttribute('id');
      const warningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal');
      warningModal.show(updateDelivery, id);
      // showModal(updateDelivery, id);
    }
  });


  // View Booking Details Ajax Request
  const viewSummary = async (id) => {
    const data = await fetch(`./backend/delivery_action.php?view=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('created_at').innerHTML = response.created_at;
    document.getElementById('display-id').innerHTML = response.id;
    document.getElementById('display-full-name').innerHTML = response.fname + ' ' + response.lname;
    document.getElementById('display-phone-number').innerHTML = response.phone_number;
    document.getElementById('display-address').innerHTML = response.address;
    document.getElementById('display-pickup-date').innerHTML = response.pickup_date;
    document.getElementById('display-pickup-time').innerHTML = response.pickup_time;
    document.getElementById('display-service-selection').innerHTML = response.service_selection;
    document.getElementById('display-service-type').innerHTML = response.service_type;
    document.getElementById('display-suggestions').innerHTML = response.suggestions;

  }

  // Update Status From Pickup to On Process Ajax Request
  const updatePickup = async (id) => {
    const data = await fetch(`./backend/delivery_action.php?update-pickup=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.text();
    if (response.includes('success')) {
      // Example: Trigger the toaster with hex values
      const green600 = '#047857'; // Hex value for green-600
      const green700 = '#065f46'; // Hex value for green-700
      showToaster('Status updated successfully!', 'check', green600, green700);
      fetchAll();
      fetchBookingCounts();
    } else {
      // Example: Trigger the toaster with hex values
      const green600 = '#d95f5f'; // Hex value for green-600
      const red700 = '#c93c3c'; // Hex value for green-700
      showToaster('Something went wrong !', 'exclamation-error', green600, red700);
    }
  }

  // Update Status From Delivery to isReceive Ajax Request
  const updateDelivery = async (id) => {
    const data = await fetch(`./backend/delivery_action.php?update-delivery=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.text();
    if (response.includes('success')) {
      // Example: Trigger the toaster with hex values
      const green600 = '#047857'; // Hex value for green-600
      const green700 = '#065f46'; // Hex value for green-700
      showToaster('Status updated successfully!', 'check', green600, green700);
      fetchAll();
      fetchBookingCounts();
    } else {
      // Example: Trigger the toaster with hex values
      const red600 = '#d95f5f'; // Hex value for red-600
      const red700 = '#c93c3c'; // Hex value for green-700
      showToaster('Something went wrong !', 'exclamation-error', red600, red700);
    }
  }

  // Fetch the total number of bookings for each status: "for pick-up", "for delivery", and "complete"
  const fetchBookingCounts = async () => {
    try {
      const data = await fetch('./backend/delivery_action.php?count_all=1', {
        method: 'GET',
      });
      const response = await data.json(); // Assuming the response is in JSON format

      // Update the HTML elements with the counts
      document.getElementById('js-pending-count').textContent = response.pendingCount; // Complete count
      document.getElementById('js-for-pickup').textContent = response.pickupCount; // For Pick-Up count
      document.getElementById('js-for-delivery').textContent = response.deliveryCount; // For Delivery count
    } catch (error) {
      console.error('Error fetching booking counts:', error);
    }
  };
  fetchBookingCounts();

});
