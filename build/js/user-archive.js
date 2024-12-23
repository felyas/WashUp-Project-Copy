import { handleDisplayCurrentTime, handleSidebar, openModal, showToaster, Modal } from "./dashboards-main.js";

const userAccountBtn = document.getElementById('js-account-setting');
userAccountBtn.addEventListener('click', () => {
  window.location.href = './user-setting.php';
})

// TO OPEN AND CLOSE THE SETTING
const settingBtn = document.getElementById('js-setting-button');
const settingDiv = document.getElementById('js-setting');

settingBtn.addEventListener('click', () => {
  settingDiv.classList.toggle('hidden');
});

// Close the settingDiv when clicking outside of it
document.addEventListener('click', (event) => {
  if (!settingDiv.contains(event.target) && !settingBtn.contains(event.target)) {
    settingDiv.classList.add('hidden');
  }
});




document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();

  const tbody = document.getElementById('customer-archive-list');
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

    const data = await fetch(`./backend/user-archive_action.php?readAll=1&page=${page}&column=${column}&order=${order}&$query=${searchQuery}&origin=${originQuery}`, {
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
      // console.log(id);
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
  });

  // FUNCTION TO RECOVER DATA AJAX REQUEST/
  const recoverData = async (archiveId) => {
    const data = await fetch(`./backend/user-archive_action.php?recover=1&archive_id=${archiveId}`, {
      method: 'GET',
    });

    const response = await data.json();
    if(response.status === 'success') {
      showToaster(response.message, 'unarchive', '#047857', '#065f46');
      fetchAll();
    } else {
      showToaster(response.message, 'exclamation-error', '#dc2626', '#b91c1c');
    }
  }

  // FUNCTION TO DELETE DATA AJAX REQUEST
  const deleteData = async (id) => {
    const data = await fetch(`./backend/user-archive_action.php?delete=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    if(response.status === 'success') {
      showToaster(response.message, 'check', '#047857', '#065f46');
      fetchAll();
    } else {
      showToaster(response.message, 'exclamation-error', '#dc2626', '#b91c1c');
    }
  }




  let timeoutId = null; // Variable to store the timeout ID

  // Long polling function for fetching notifications
  const fetchNotifications = async (lastCheckTime) => {
    try {
      const response = await fetch(`./backend/customer_action.php?fetch_notifications=1&last_check=${lastCheckTime}`);
      const notifications = await response.json();

      const notificationContainer = document.querySelector('.js-notification-messages');
      const notificationDot = document.querySelector('.js-notification-dot');
      const totalNotificationsElement = document.querySelector('.js-total-notifications');

      if (notifications.length > 0) {
        // Clear existing messages
        notificationContainer.innerHTML = '';
        totalNotificationsElement.innerText = '0';

        // Append each notification to the container
        notifications.forEach(notification => {
          const notificationElement = document.createElement('div');
          notificationElement.classList.add('flex', 'items-center', 'justify-between', 'bg-gray-200', 'mb-1');

          // Determine message based on status
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

        // Update other UI components
        fetchAllBookings();
        fetchBookingCounts();
      } else {
        totalNotificationsElement.innerText = '0';
        notificationDot.classList.add('hidden');
      }

      // Clear the previous timeout if it exists
      if (timeoutId) {
        clearTimeout(timeoutId);
      }

      // Set a new timeout and store its ID
      timeoutId = setTimeout(() => {
        const currentTimestamp = new Date().toISOString();
        fetchNotifications(currentTimestamp);
      }, 10000);
    } catch (error) {
      console.error('Error fetching notifications:', error);
    }
  };

  // Handle closing individual notifications
  document.addEventListener('click', async (e) => {
    if (e.target.classList.contains('js-notification-close')) {
      const id = e.target.dataset.id;

      try {
        const response = await fetch(`./backend/customer_action.php?mark_as_read=1&id=${id}`);
        const result = await response.json();

        if (result.success) {
          // Remove the notification element
          e.target.closest('.flex').remove();

          // Update notification count
          const totalElement = document.querySelector('.js-total-notifications');
          const currentTotal = parseInt(totalElement.textContent);
          const newTotal = currentTotal - 1;
          totalElement.textContent = newTotal;

          // Hide dot if no more notifications
          if (newTotal === 0) {
            document.querySelector('.js-notification-dot').classList.add('hidden');
          }

          // Refresh other UI components
          fetchAllBookings();
          fetchBookingCounts();
        }
      } catch (error) {
        console.error('Error marking notification as read:', error);
      }
    }
  });


  // Initial call to start long polling
  let initialTimestamp = new Date().toISOString();
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

});