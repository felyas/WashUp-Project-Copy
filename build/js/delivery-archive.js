import { handleDisplayCurrentTime, handleSidebar, handleDropdown, openModal, showToaster, Modal } from "./dashboards-main.js";



document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();

  const tbody = document.getElementById('delivery-archive-list');
  const paginationContainer = document.getElementById('pagination-container');
  const searchInput = document.getElementById('js-search-bar');
  const originFilter = document.getElementById('origin-filter');

  // FUNCTION TO TOGGLE SORTING ICONS
  const toggleSortIcon = (th, order) => {
    const allIcons = document.querySelectorAll('.sort-icon img');

    //RESET ALL ICONS TO CARET-DOWN BY DEFAULT
    allIcons.forEach(icon => {
      icon.setAttribute('src', './img/icons/caret-down.svg');
    });

    const icon = th.querySelector('.sort-icon img');
    if (order === 'desc') {
      icon.setAttribute('src', './img/icons/caret-down.svg');
    } else {
      icon.setAttribute('src', './img/icons/caret-up.svg');
    }
  }

  // FETCH ALL ITEMS AJAX REQUEST
  const fetchAll = async (page = 1, column = 'id', order = 'desc', query = '') => {
    const searchQuery = searchInput.value || query;
    const originQuery = originFilter.value;

    const data = await fetch(`./backend/delivery-archive_action.php?readAll=1&page=${page}&column=${column}&order=${order}&$query=${searchQuery}&origin=${originQuery}`, {
      method: 'GET',
    });
    const response = await data.json();
    tbody.innerHTML = response.items;
    paginationContainer.innerHTML = response.pagination;
  }

  // HANDLE COLUMN SORTING 
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

  // HANDLE SEARCH INPUT
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

  originFilter.addEventListener('change', () => {
    fetchAll();
  })

  // INITIAL CALL
  fetchAll();

  tbody.addEventListener('click', (e) => {
    // TARGET UNARCHIVE LINK
    if (e.target && (e.target.matches('a.unarchiveLink') || e.target.closest('a.unarchiveLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.unarchiveLink') ? e.target : e.target.closest('a.unarchiveLink');
      let archiveId = targetElement.getAttribute('data-archiveId');
      // console.log(archiveId);
      const deleteWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal', 'modal-message');
      deleteWarningModal.show(recoverData, archiveId, 'Do you really want to recover this record?');
    }

    // TARGET DELETE LINK
    if (e.target && (e.target.matches('a.deleteLink') || e.target.closest('a.deleteLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.deleteLink') ? e.target : e.target.closest('a.deleteLink');
      let id = targetElement.getAttribute('id');
      // console.log(id);
      const deleteWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal', 'modal-message');
      deleteWarningModal.show(deleteData, id, 'Do you really want to delete this record?');
    }
  })

  // FUNCTION TO RECOVER DATA AJAX REQUEST/
  const recoverData = async (archiveId) => {
    const data = await fetch(`./backend/delivery-archive_action.php?recover=1&archive_id=${archiveId}`, {
      method: 'GET',
    });

    const response = await data.json();
    console.log(response);

    if (response.status === 'success') {
      showToaster(response.message, 'unarchive', '#047857', '#065f46');
      fetchAll();
    } else {
      showToaster(response.message, 'exclamation-error', '#dc2626', '#b91c1c');
    }
  }

  // FUNCTION TO DELETE DATA AJAX REQUEST
  const deleteData = async (id) => {
    const data = await fetch(`./backend/delivery-archive_action.php?delete=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    if (response.status === 'success') {
      showToaster(response.message, 'trash', '#047857', '#065f46');
      fetchAll();
    } else {
      showToaster(response.message, 'exclamation-error', '#dc2626', '#b91c1c');
    }
  }




  // Combined notification fetching function
  const fetchAllNotifications = async () => {
    try {
      // Fetch both types of notifications in parallel
      const [deliveryResponse, bookingResponse] = await Promise.all([
        fetch(`./backend/delivery_action.php?fetch_new_deliveries=1`),
        fetch(`./backend/admin_action.php?fetch_new_bookings_delivery=1`)
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
        fetchAll();
        fetchAllPendingBooking();
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

});