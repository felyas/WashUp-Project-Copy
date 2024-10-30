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
          totalNotificationsElement.innerText = '0';
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