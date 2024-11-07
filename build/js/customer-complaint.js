import { handleSidebar, handleDisplayCurrentTime, handleDropdown, openModal, showToaster, Modal } from "./dashboards-main.js";

openModal('viewModalTrigger', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');

document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();

  const tbody = document.getElementById('customer-complaint-list');
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
  const fetchAll = async (page = 1, column = 'complaint_id', order = 'desc', query = '') => {
    const searchQuery = searchInput.value.trim() || query;
    const statusQuery = status || statusFilter.value; // Get status from dropdown or passed value

    const data = await fetch(`./backend/customer-complaint_action.php?readAll=1&page=${page}&column=${column}&order=${order}&query=${searchQuery}&status=${statusQuery}`, {
      method: 'GET',
    });
    const response = await data.json();
    tbody.innerHTML = response.items;
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


  tbody.addEventListener('click', (e) => {
    // Target View Link
    if (e.target && (e.target.matches('a.viewLink') || e.target.closest('a.viewLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.viewLink') ? e.target : e.target.closest('a.viewLink');
      let id = targetElement.getAttribute('id');
      complainInfo(id);
    }

    // Target Resolved Link
    if (e.target && (e.target.matches('a.receivedLink') || e.target.closest('a.receivedLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.receivedLink') ? e.target : e.target.closest('a.receivedLink');
      let id = targetElement.getAttribute('id');
      const resolvedWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal');
      resolvedWarningModal.show(toResolved, id);
    }

    // Target Delete Link
    if (e.target && (e.target.matches('a.resolvedLink') || e.target.closest('a.resolvedLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.resolvedLink') ? e.target : e.target.closest('a.resolvedLink');
      let id = targetElement.getAttribute('id');
      const resolvedWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal');
      resolvedWarningModal.show(closed, id);
    }


  });

  // View Complaint Info Ajax Request
  const complainInfo = async (id) => {
    const data = await fetch(`./backend/customer-complaint_action.php?read=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    console.log(response);
    document.getElementById('created_at').innerText = response.created_at;
    document.getElementById('display-id').innerText = response.user_id;
    document.getElementById('display-full-name').innerText = response.first_name + ' ' + response.last_name;
    document.getElementById('display-phone-number').innerText = response.phone_number;
    document.getElementById('display-email').innerText = response.email;
    document.getElementById('display-reason').innerText = response.reason;
    document.getElementById('display-description').innerText = response.description;
  }

  // Update Status from Pending to Resolved Ajax Request
  const toResolved = async (id) => {
    const data = await fetch(`./backend/customer-complaint_action.php?onAction=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    if (response.status === 'success') {
      showToaster(response.message, 'check', '#047857', '#065f46');
      fetchAll();
    } else {
      showToaster('Something went wrong!', 'exclamation-error', '#dc2626', '#b91c1c');
    }
  }

  // Resolved Complaint Record Ajax Request
  const closed = async (id) => {
    const data = await fetch(`./backend/customer-complaint_action.php?resolved=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    if (response.status === 'success') {
      showToaster(response.message, 'check', '#047857', '#065f46');
      fetchAll();
    } else {
      showToaster('Something went wrong!', 'exclamation-error', '#dc2626', '#b91c1c');
    }
  }

  // FETCH TOTAL COUNT FOR CARDS AJAX REQUEST
  const fetchComplaintCounts = async () => {
    const data = await fetch('./backend/customer-complaint_action.php?count_all=1', {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('js-pending-count').textContent = response.pendingCount;
    document.getElementById('js-resolved').textContent = response.resolvedCount;
  };
  fetchComplaintCounts();

  // Notification Ajax Request
  function deliveryNotification() {
    // Long polling function for fetching delivery-related notifications
    // Combined notification fetching function
    const fetchAllNotifications = async () => {
      try {
        // Fetch both types of notifications in parallel
        const [deliveryResponse, bookingResponse] = await Promise.all([
          fetch(`./backend/delivery_action.php?fetch_new_deliveries=1`),
          fetch(`./backend/admin_action.php?fetch_new_bookings=1`)
        ]);

        const deliveryNotifications = await deliveryResponse.json();
        const bookingNotifications = await bookingResponse.json();

        const notificationContainer = document.querySelector('.js-notification-messages');
        const notificationDot = document.querySelector('.js-notification-dot');
        const totalNotificationsElement = document.querySelector('.js-total-notifications');

        // Combine notifications and add a type identifier
        const combinedNotifications = [
          ...deliveryNotifications.map(n => ({ ...n, type: 'delivery' })),
          ...bookingNotifications.map(n => ({ ...n, type: 'booking' }))
        ].sort((a, b) => b.id - a.id); // Sort by ID descending

        if (combinedNotifications.length > 0) {
          // Clear existing messages
          notificationContainer.innerHTML = '';

          // Append each notification to the container
          combinedNotifications.forEach(notification => {
            const notificationElement = document.createElement('div');
            notificationElement.classList.add('flex', 'items-center', 'justify-between', 'bg-gray-200', 'mb-1');

            // Different message content based on notification type
            const message = notification.type === 'delivery'
              ? `Delivery status update for (ID: ${notification.id}) - ${notification.status}`
              : `New booking request received (ID: ${notification.id})`;

            notificationElement.innerHTML = `
          <div class="flex items-center p-4 bg-blue-100 border border-blue-200 rounded-lg shadow-md">
            <img src="./img/about-bg1.png" alt="Notification Image" class="w-12 h-12 mr-4 rounded-full">
            <div class="flex-1">
              <p class="text-sm">
                ${message}
              </p>
            </div>
            <button class="w-12 p-0 border-none font-bold js-notification-close" 
                    data-id="${notification.id}" 
                    data-type="${notification.type}">
              &#10005;
            </button>
          </div>
        `;
            notificationContainer.appendChild(notificationElement);
          });

          // Update total notification count
          totalNotificationsElement.textContent = combinedNotifications.length;
          notificationDot.classList.remove('hidden');
        } else {
          notificationDot.classList.add('hidden');
        }

        // Continue polling
        setTimeout(fetchAllNotifications, 10000);
      } catch (error) {
        console.error('Error fetching notifications:', error);
        // Retry after error
        setTimeout(fetchAllNotifications, 10000);
      }
    };

    // Handle notification close clicks
    document.addEventListener('click', async (e) => {
      if (e.target.classList.contains('js-notification-close')) {
        const id = e.target.dataset.id;
        const type = e.target.dataset.type;

        try {
          // Send request to mark notification as read based on type
          const endpoint = type === 'delivery'
            ? './backend/delivery_action.php'
            : './backend/admin_action.php';

          await fetch(`${endpoint}?mark_as_read=1&id=${id}`);

          // Remove the notification element
          e.target.closest('.flex').remove();

          // Update notification count
          const totalElement = document.querySelector('.js-total-notifications');
          const currentTotal = parseInt(totalElement.textContent);
          totalElement.textContent = currentTotal - 1;

          // Hide dot if no more notifications
          if (currentTotal - 1 === 0) {
            document.querySelector('.js-notification-dot').classList.add('hidden');
          }
        } catch (error) {
          console.error('Error marking notification as read:', error);
        }
      }
    });

    // Start the combined polling
    fetchAllNotifications();

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
  }
  deliveryNotification();

});