import { handleDisplayCurrentTime, openModal, showToaster, Modal } from "./dashboards-main.js";

handleDisplayCurrentTime();
openModal('viewModalTrigger', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');

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

  // Calendar Section
  const fetchEvents = async () => {
    const response = await fetch('./backend/delivery_action.php?fetch_events=1', {
      method: 'GET',
    });
    const events = await response.json();
    return events.map(event => ({
      id: event.event_id,
      title: event.event_name,
      start: event.event_start_date,
      end: event.event_end_date
    }));
  };
  const calendarEl = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'listWeek',
    height: '100%',
    events: fetchEvents,
    buttonText: {
      today: 'Today',
      listWeek: 'Week List',
      listDay: 'Day List'
    }, headerToolbar: {
      start: 'title',  // Title will be on the left
      center: '',      // Center will be empty
      end: 'today,prev,next' // Today, Prev, and Next buttons on the right
    },
    views: {
      listWeek: {                    // Week view
        titleFormat: {              // Format for week view
          year: 'numeric',
          month: 'short',
          day: 'numeric'          // e.g., 'Sep 13 2009'
        }
      }
    },
    // Add eventRender function to style the title
    eventDidMount: function (info) {
      // Select the title element and apply inline styles
      const titleElement = document.querySelector('.fc-toolbar-title');
      if (titleElement) {
        titleElement.style.fontSize = '1rem'; // Adjust the font size as needed
        titleElement.style.fontWeight = 'bold'; // Adjust weight if desired
      }
    },
  });
  calendar.render();

  // Notification Ajax Request
  function deliveryNotification() {
    // Long polling function for fetching delivery-related notifications
    const fetchNewDeliveries = async () => {
      try {
        const response = await fetch(`./backend/delivery_action.php?fetch_new_deliveries=1`);
        const notifications = await response.json();

        const notificationContainer = document.querySelector('.js-notification-messages');
        const notificationDot = document.querySelector('.js-notification-dot'); // Red dot element
        const totalNotificationsElement = document.querySelector('.js-total-notifications'); // Total notifications element

        // If there are new delivery notifications, display them
        if (notifications.length > 0) {
          // Clear existing messages
          notificationContainer.innerHTML = '';

          // Append each notification to the container
          notifications.forEach(notification => {
            const notificationElement = document.createElement('div');
            notificationElement.classList.add('flex', 'items-center', 'justify-between', 'bg-gray-200', 'mb-1');
            notificationElement.innerHTML = `
                    <div class="flex items-center p-4 bg-blue-100 border border-blue-200 rounded-lg shadow-md">
                        <img src="./img/about-bg1.png" alt="Notification Image" class="w-12 h-12 mr-4 rounded-full">
                        <div class="flex-1">
                            <p class="text-sm">
                                Delivery status update for 
                                <span class="font-semibold text-celestial">
                                    (ID: ${notification.id})
                                </span> - ${notification.status}
                            </p>
                        </div>
                        <button class="w-12 p-0 border-none font-bold js-notification-close" data-id="${notification.id}">
                            &#10005;
                        </button>
                    </div>
                `;
            notificationContainer.appendChild(notificationElement);
          });

          // Update total notification count
          totalNotificationsElement.textContent = notifications.length;
          notificationDot.classList.remove('hidden');  // Show red dot
        } else {
          notificationDot.classList.add('hidden');  // Hide red dot if no new notifications
        }

        // Continue long polling after 1 second
        setTimeout(fetchNewDeliveries, 10000);  // Poll every 10 seconds
      } catch (error) {
        console.error('Error fetching new deliveries:', error);
      }
    };

    // Initial call to start long polling
    fetchNewDeliveries();

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
        const deliveryId = e.target.getAttribute('data-id');
        e.target.parentElement.remove();  // Remove notification from UI

        // Send a request to the server to mark this delivery notification as read
        const response = await fetch(`./backend/delivery_action.php?mark_delivery_read=1&id=${deliveryId}`);
        const data = await response.json();

        if (data.success) {
          console.log('Delivery notification marked as read.');
        } else {
          console.error('Failed to mark delivery notification as read.');
        }
      }
    });
  }
  deliveryNotification();

});
