import { handleSidebar, handleDisplayCurrentTime, openModal } from "./dashboards-main.js";

document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();
  openModal('viewModalTrigger', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');

  const tbody = document.getElementById('js-list-tbody');
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

  // Fetching all bookings with pagination, search, sorting, and status filtering
  const fetchAll = async (page = 1, column = 'id', order = 'desc', query = '', status = '') => {
    const searchQuery = searchInput.value.trim() || query;
    const statusQuery = status || statusFilter.value; // Get status from dropdown or passed value

    const data = await fetch(`./backend/booking-details_action.php?readAll=1&page=${page}&column=${column}&order=${order}&query=${searchQuery}&status=${statusQuery}`, {
      method: 'GET',
    });
    const response = await data.json();
    tbody.innerHTML = response.bookings;
    paginationContainer.innerHTML = response.pagination; // For pagination
  };

  // Handle column sorting
  document.querySelectorAll('.sortable').forEach(th => {
    th.addEventListener('click', function () {
      const column = this.getAttribute('data-column');
      let order = this.getAttribute('data-order');
      order = order === 'desc' ? 'asc' : 'desc'; // Toggle order

      this.setAttribute('data-order', order);
      toggleSortIcon(this, order); // Change icon direction

      fetchAll(1, column, order);
    });
  });

  // Handle search input
  searchInput.addEventListener('input', () => {
    fetchAll();
  });

  // Handle pagination clicks
  paginationContainer.addEventListener('click', function (event) {
    if (event.target.classList.contains('pagination-link')) {
      event.preventDefault();
      const page = event.target.getAttribute('data-page');
      fetchAll(page);
    }
  });

  // Handle status filter change
  statusFilter.addEventListener('change', () => {
    fetchAll();
  });

  // Initial fetch
  fetchAll();

  // Target Anchor Tags
  tbody.addEventListener('click', (e) => {

    // Target the viewLink
    if (e.target && (e.target.matches('a.viewLink') || e.target.closest('a.viewLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.viewLink') ? e.target : e.target.closest('a.viewLink');
      let id = targetElement.getAttribute('id');
      bookingSummary(id);
    }

    // Target the doneProcessLink
    if (e.target && (e.target.matches('a.doneProcessLink') || e.target.closest('a.doneProcessLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.doneProcessLink') ? e.target : e.target.closest('a.doneProcessLink');
      let id = targetElement.getAttribute('id');

      // SweetAlert confirmation pop-up
      Swal.fire({
        title: 'Are you sure?',
        text: "Do you really want to perform this action?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'No',
        buttonsStyling: false,
        customClass: {
          confirmButton: 'bg-green-700 hover:bg-green-800 text-white font-semibold py-3 px-5 rounded-lg mr-2',
          cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-5 rounded-lg'
        }
      }).then((result) => {
        if (result.isConfirmed) {
          // If the user confirms, call the done function
          done(id);
        }
      });
    }


    // Target the admitLink
    if (e.target && (e.target.matches('a.admitLink') || e.target.closest('a.admitLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.admitLink') ? e.target : e.target.closest('a.admitLink');
      let id = targetElement.getAttribute('id');
      adminBooking(id);
    }

    // Target the deniedLink
    if (e.target && (e.target.matches('a.deniedLink') || e.target.closest('a.deniedLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.deniedLink') ? e.target : e.target.closest('a.deniedLink');
      let id = targetElement.getAttribute('id');
      deniedBooking(id);
    }

  })

  // Fetch Booking Details Ajax Request
  const bookingSummary = async (id) => {
    const data = await fetch(`./backend/booking-details_action.php?view=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('booking-date').innerHTML = response.created_at;
    document.getElementById('display-full-name').innerHTML = response.fname + " " + response.lname;
    document.getElementById('display-phone-number').innerHTML = response.phone_number;
    document.getElementById('display-address').innerHTML = response.address;
    document.getElementById('display-pickup-date').innerHTML = response.pickup_date;
    document.getElementById('display-pickup-time').innerHTML = response.pickup_time;
    document.getElementById('display-service-selection').innerHTML = response.service_selection;
    document.getElementById('display-service-type').innerHTML = response.service_type;
    document.getElementById('display-suggestions').innerHTML = response.suggestions;
  }

  // Update Status of Booking Once Done Ajax Request
  const done = async (id) => {
    const data = await fetch(`./backend/booking-details_action.php?done=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.text();
    if (response.includes('success')) {
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Booking status updated successfully!',
        buttonsStyling: false,
        customClass: {
          confirmButton: 'bg-green-700 hover:bg-green-800 text-white font-semibold py-3 px-5 rounded-lg'
        }
      }).then(() => {
        window.location.href = './booking-details.php';
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









  function notificationCopy() {
    // Long polling function for fetching new booking requests
    const fetchNewBookings = async () => {
      try {
        const response = await fetch(`./backend/admin_action.php?fetch_new_bookings=1`);
        const notifications = await response.json();

        const notificationContainer = document.querySelector('.js-notification-messages');
        const notificationDot = document.querySelector('.js-notification-dot'); // Red dot element
        const totalNotificationsElement = document.querySelector('.js-total-notifications'); // Total notifications element

        // If there are new booking notifications, display them
        if (notifications.length > 0) {
          // Clear existing messages
          notificationContainer.innerHTML = '';

          // Append each notification to the container
          notifications.forEach(notification => {
            const notificationElement = document.createElement('div');
            notificationElement.classList.add('p-2', 'flex', 'items-center', 'justify-between', 'bg-gray-200', 'mb-1');
            notificationElement.innerHTML = `
          <p class="w-auto">New booking request received (ID: ${notification.id})</p>
          <button class="w-12 p-0 border-none font-bold js-notification-close" data-id="${notification.id}">&#10005;</button>
        `;
            notificationContainer.appendChild(notificationElement);
          });

          // Update total notification count
          totalNotificationsElement.textContent = notifications.length;
          notificationDot.classList.remove('hidden');  // Show red dot

        } else {
          notificationDot.classList.add('hidden');  // Hide red dot if no new notifications
        }

        // Continue long polling after 1 seconds
        setTimeout(fetchNewBookings, 10000);  // Poll every 5 seconds
      } catch (error) {
        console.error('Error fetching new bookings:', error);
      }
    };

    // Initial call to start long polling
    fetchNewBookings();

    // Toggle notification dropdown visibility on bell icon click
    document.querySelector('.js-notification-button').addEventListener('click', () => {
      const notificationDropdown = document.querySelector('.js-notification');
      const notificationDot = document.querySelector('.js-notification-dot');

      // Show or hide the notification dropdown
      notificationDropdown.classList.toggle('hidden');

      // Hide the red dot once notifications are viewed
      if (!notificationDropdown.classList.contains('hidden')) {
        notificationDot.classList.add('hidden');  // Hide the red dot
      }
    });

    // Handle closing individual notifications
    document.addEventListener('click', async (e) => {
      if (e.target.classList.contains('js-notification-close')) {
        const bookingId = e.target.getAttribute('data-id');
        e.target.parentElement.remove();  // Remove notification from UI

        // Send a request to the server to mark this booking notification as read
        const response = await fetch(`./backend/admin_action.php?mark_admin_booking_read=1&id=${bookingId}`);
        const data = await response.json();

        if (data.success) {
          console.log('Booking notification marked as read.');
        } else {
          console.error('Failed to mark booking notification as read.');
        }
      }
    });
  }
  notificationCopy();

  // Admit Booking and Update Status to For Pick-Up Ajax Request.
  const adminBooking = async (id) => {
    const data = await fetch(`./backend/admin_action.php?admit=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.text();
    if (response.includes('success')) {
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Booking admited successfully!',
        buttonsStyling: false, // Disable default button styling
        customClass: {
          confirmButton: 'bg-green-700 hover:bg-green-800 text-white font-semibold py-3 px-5 rounded-lg'
        }
      }).then(() => {
        window.location.href = './booking-details.php';
      });
    }
  }

  // Denied Booking and Update Status to For Pick-Up Ajax Request.
  const deniedBooking = async (id) => {
    const data = await fetch(`./backend/admin_action.php?denied=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.text();
    if (response.includes('success')) {
      Swal.fire({
        icon: 'error',
        title: 'Denied',
        text: 'Booking denied successfully!',
        buttonsStyling: false, // Disable default button styling
        customClass: {
          confirmButton: 'bg-gray-700 hover:bg-gray-800 text-white font-semibold py-3 px-5 rounded-lg'
        }
      }).then(() => {
        window.location.href = './booking-details.php';
      });
    }
  }

});

