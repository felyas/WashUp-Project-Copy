import { handleSidebar, handleDisplayCurrentTime, openModal, showToaster, Modal } from "./dashboards-main.js";

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

    const data = await fetch(`./backend/complaint-table_action.php?readAll=1&page=${page}&column=${column}&order=${order}&query=${searchQuery}&status=${statusQuery}`, {
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

    // Target Delete Link
    if (e.target && (e.target.matches('a.deleteLink') || e.target.closest('a.deleteLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.deleteLink') ? e.target : e.target.closest('a.deleteLink');
      let id = targetElement.getAttribute('id');
      const deleteWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal');
      deleteWarningModal.show(deleteComplaint, id);
    }


  });

  // View Complaint Info Ajax Request
  const complainInfo = async (id) => {
    const data = await fetch(`./backend/complaint-table_action.php?read=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    console.log(response);
    document.getElementById('created_at').innerText = response.created_at;
    document.getElementById('display-id').innerText = response.complaint_id;
    document.getElementById('display-full-name').innerText = response.first_name + ' ' + response.last_name;
    document.getElementById('display-phone-number').innerText = response.phone_number;
    document.getElementById('display-email').innerText = response.email;
    document.getElementById('display-reason').innerText = response.reason;
    document.getElementById('display-description').innerText = response.description;
  }

  // Delete Complaint Record Ajax Request
  const deleteComplaint = async () => {
    const data = await fetch(`./backend/complaint-table_action.php?delete=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    console.log(response);
    if (response.status === 'success') {
      showToaster(response.message, 'check', '#047857', '#065f46');
      fetchAll();
    } else {
      showToaster('Something went wrong!', 'exclamation-error', '#dc2626', '#b91c1c');
    }
  }

  function notificationCopy() {
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
          totalNotificationsElement.innerText = 0;
          notificationContainer.innerHTML = '';

          // Append each notification to the container
          notifications.forEach(notification => {
            const notificationElement = document.createElement('div');
            notificationElement.classList.add('flex', 'items-center', 'justify-between', 'bg-gray-200', 'mb-1');

            // Check the status and modify the message accordingly
            let message;
            if (notification.status === 'on process') {
              message = `The proof of kilo for your booking (ID: ${notification.id}) has been added. We're now processing your laundry.`;
            } else if (notification.status === 'delivered') {
              message = `Your laundry (Booking ID: ${notification.id}) has been successfully delivered. A receipt and proof of delivery have been sent to you.`;
            } else {
              message = `Booking with ID ${notification.id} updated to <span class="font-semibold text-celestial">${notification.status}</span>`;
            }

            notificationElement.innerHTML = `
          <div class="flex items-center p-4 bg-blue-100 border border-blue-200 rounded-lg shadow-md">
            <img src="./img/about-bg1.png" alt="Notification Image" class="w-12 h-12 mr-4 rounded-full">
            <div class="flex-1">
              <p class="text-sm">${message}</p>
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
          notificationDot.classList.remove('hidden');
          
        } else {
          totalNotificationsElement.innerText = '0';
          notificationDot.classList.add('hidden');
        }

        // Continue long polling after 5 seconds
        setTimeout(() => {
          const currentTimestamp = new Date().toISOString(); // Use current time as last check
          fetchNotifications(currentTimestamp);
        }, 10000); // Check every 10 seconds
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
  }
  notificationCopy();



});