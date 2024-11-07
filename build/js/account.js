import { handleSidebar, handleDisplayCurrentTime, openModal, handleDropdown, showToaster, Modal } from "./dashboards-main.js";

document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();
  handleDropdown();
  openModal('viewModalTrigger', 'toViewUsersModal', 'closeViewUsesrModal', 'closeViewUsersModal2');

  const tbody = document.getElementById('js-users-tbody');
  const paginationContainer = document.getElementById('pagination-container');
  const searchInput = document.getElementById('js-search-bar');
  const statusFilter = document.getElementById('status-filter');

  let currentPage = 1;
  let currentColumn = 'id';
  let currentOrder = 'desc';
  let debounceTimer;
  let isFetching = false;

  // Function to toggle sorting icons
  const toggleSortIcon = (th, order) => {
    document.querySelectorAll('.sort-icon img').forEach(icon => {
      icon.setAttribute('src', './img/icons/caret-down.svg');
    });

    const icon = th.querySelector('.sort-icon img');
    icon.setAttribute('src', `./img/icons/caret-${order === 'desc' ? 'down' : 'up'}.svg`);
  };

  // Fetch All Users with pagination, search, and sorting
  const fetchAll = async () => {
    if (isFetching) return;
    isFetching = true;

    const searchQuery = searchInput.value.trim();
    const statusQuery = statusFilter.value;

    try {
      const response = await fetch(`./backend/account_action.php?readAll=1&page=${currentPage}&column=${currentColumn}&order=${currentOrder}&query=${searchQuery}&status=${statusQuery}`);
      const data = await response.json();
      tbody.innerHTML = data.users;
      paginationContainer.innerHTML = data.pagination;
    } catch (error) {
      console.error('Error fetching data:', error);
      showAlert('error', 'Error', 'Failed to fetch data. Please try again.');
    } finally {
      isFetching = false;
    }
  };

  // Debounce function
  const debounce = (func, delay) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(func, delay);
  };

  // Handle Column Sorting 
  document.querySelectorAll('.sortable').forEach(th => {
    th.addEventListener('click', () => {
      if (isFetching) return;
      currentColumn = th.getAttribute('data-column');
      currentOrder = currentOrder === 'desc' ? 'asc' : 'desc';
      th.setAttribute('data-order', currentOrder);
      toggleSortIcon(th, currentOrder);
      currentPage = 1; // Reset to first page when sorting
      fetchAll();
    });
  });

  // Handle search input with debounce
  searchInput.addEventListener('input', () => {
    debounce(() => {
      currentPage = 1; // Reset to first page when searching
      fetchAll();
    }, 300);
  });

  // Handle Pagination
  paginationContainer.addEventListener('click', (e) => {
    if (e.target.classList.contains('pagination-link') && !isFetching) {
      e.preventDefault();
      currentPage = parseInt(e.target.getAttribute('data-page'), 10);
      fetchAll();
    }
  });

  // Handle status filter change
  statusFilter.addEventListener('change', () => {
    if (isFetching) return;
    currentPage = 1; // Reset to first page when changing filter
    fetchAll();
  });

  // Initial fetch
  fetchAll();


  // User Details and Delete functionality
  tbody.addEventListener('click', (e) => {
    if (e.target && (e.target.matches('a.editLink') || e.target.closest('a.editLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.editLink') ? e.target : e.target.closest('a.editLink');
      let id = targetElement.getAttribute('id');
      editUser(id);
    }

    // View Booking Ajax Request
    if (e.target && (e.target.matches('a.viewLink') || e.target.closest('a.viewLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.viewLink') ? e.target : e.target.closest('a.viewLink');
      let id = targetElement.getAttribute('id');
      userInfo(id);
    }

    // Delete Booking Ajax Request
    if (e.target && (e.target.matches('a.deleteLink') || e.target.closest('a.deleteLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.deleteLink') ? e.target : e.target.closest('a.deleteLink');
      let id = targetElement.getAttribute('id');
      const deleteWarningModal = new Modal('delete-user-modal', 'deleteUser-confirm-modal', 'deleteUser-close-modal');
      deleteWarningModal.show(deleteUser, id);
    }
  });

  // Function to View User Info Ajax Request
  const userInfo = async (id) => {
    const data = await fetch(`./backend/account_action.php?view=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    console.log(response);

    document.getElementById('js-user-id').textContent = response.id;
    document.getElementById('display-full-name').textContent = `${response.first_name} ${response.last_name}`;
    document.getElementById('display-email').textContent = response.email;
    document.getElementById('display-role').textContent = response.role;
  }

  const deleteUser = async (id) => {
    const data = await fetch(`./backend/account_action.php?delete=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.text();
    if (response.includes('success')) {
      const green600 = '#047857';
      const green700 = '#065f46';
      showToaster('User deleted successfully!', 'check', green600, green700);
      fetchAll();
    } else {
      const red600 = '#dc2626'; // Hex value for green-600
      const red700 = '#b91c1c'; // Hex value for green-700
      showToaster('Something went wrong !', 'exclamation-error', red600, red700);
    }
  }

  function notificationCopy() {
    // Long polling function for fetching new booking requests
    let timeoutId; // Variable to store the timeout ID

    const fetchNewBookings = async () => {
      try {
        const response = await fetch(`./backend/admin_action.php?fetch_new_bookings=1`);
        const notifications = await response.json();

        const notificationContainer = document.querySelector('.js-notification-messages');
        const notificationDot = document.querySelector('.js-notification-dot');
        const totalNotificationsElement = document.querySelector('.js-total-notifications');

        if (notifications.length > 0) {
          notificationContainer.innerHTML = '';

          notifications.forEach(notification => {
            const notificationElement = document.createElement('div');
            notificationElement.classList.add('flex', 'items-center', 'justify-between', 'bg-gray-200', 'mb-1');
            notificationElement.innerHTML = `
          <div class="flex items-center p-4 bg-blue-100 border border-blue-200 rounded-lg shadow-md">
            <img src="./img/about-bg1.png" alt="Notification Image" class="w-12 h-12 mr-4 rounded-full">
            <div class="flex-1">
              <p class="text-sm">
                New booking request received 
                <span class="font-semibold text-celestial">
                  (ID: ${notification.id})
                </span>
              </p>
            </div>
            <button class="w-12 p-0 border-none font-bold js-notification-close" data-id="${notification.id}">
              &#10005;
            </button>
          </div>
        `;
            notificationContainer.appendChild(notificationElement);
          });

          totalNotificationsElement.textContent = notifications.length;
          notificationDot.classList.remove('hidden');
        } else {
          notificationDot.classList.add('hidden');
        }

        // Clear the previous timeout if it exists
        if (timeoutId) {
          clearTimeout(timeoutId);
        }

        // Set a new timeout and store its ID
        timeoutId = setTimeout(fetchNewBookings, 10000);
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

    // Handle notification close clicks
    document.addEventListener('click', async (e) => {
      if (e.target.classList.contains('js-notification-close')) {
        const id = e.target.dataset.id;

        try {
          const response = await fetch(`./backend/admin_action.php?mark_admin_booking_read=1&id=${id}`);
          const result = await response.json();

          if (result.success) {
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
          }
        } catch (error) {
          console.error('Error marking notification as read:', error);
        }
      }
    });
  }
  notificationCopy();

});