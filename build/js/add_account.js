import { handleSidebar, handleDisplayCurrentTime, handleDropdown, showToaster, Modal } from "./dashboards-main.js";

document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();
  handleDropdown();

  const addUserForm = document.getElementById('add-user-form');
  const addUserBtn = document.getElementById('add-user-btn');

  // Handle the input validation from add items
  addUserForm.addEventListener('input', (e) => {
    const target = e.target;
    const feedback = target.nextElementSibling;

    if (target.tagName === 'INPUT' && (target.type === 'text' || target.type === 'email' || target.type === 'password')) {
      if (target.checkValidity()) {
        target.classList.remove('border-red-500');
        target.classList.add('border-green-700'); // Change border to green
        feedback.classList.add('hidden');
      } else {
        target.classList.remove('border-green-700');
        target.classList.add('border-red-500'); // Change border to red if still invalid
        feedback.classList.remove('hidden');
      }
    }
  });


  // Handle Add Administrator Ajax Request
  addUserForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(addUserForm);
    formData.append('add', 1);

    if (addUserForm.checkValidity() === false) {
      e.stopPropagation();

      [...addUserForm.elements].forEach((input) => {
        if (input.tagName === 'INPUT' && (input.type === 'text' || input.type === 'email' || input.type === 'password')) {
          const feedback = input.nextElementSibling;

          if (!input.checkValidity()) {
            input.classList.add('border-red-500');
            if (feedback && feedback.classList.contains('text-red-500')) {
              feedback.classList.remove('hidden');
            }
          } else {
            input.classList.remove('border-red-500');
            if (feedback && feedback.classList.contains('text-red-500')) {
              feedback.classList.add('hidden');
            }
          }
        }
      });
      const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
      errorWarningModal.show();
      return false;
    } else {
      addUserBtn.value = 'Please Wait...';

      try {
        const data = await fetch('./backend/account_action.php', {
          method: 'POST',
          body: formData,
        });

        const response = await data.json();  // Expect a JSON response

        // Handle the response
        if (response.status === 'success') {
          const green600 = '#047857';
          const green700 = '#065f46';
          showToaster('User added successfully !', 'check', green600, green700);
          addUserBtn.disabled = true;
          addUserBtn.classList.add('opacity-50', 'cursor-not-allowed');

          setTimeout(() => {
            window.location.href = './account.php';
            addUserForm.reset();
          }, 500);
        } else {
          const red600 = '#d95f5f';
          const red700 = '#c93c3c';
          showToaster(`${response.message}`, 'exclamation-error', red600, red700);
          document.getElementById('add-user-btn').value = 'Add User';
        }
      } catch (error) {
        const red600 = '#d95f5f';
        const red700 = '#c93c3c';
        showToaster(`Something went wrong !`, 'exclamation-error', red600, red700);
        document.getElementById('add-user-btn').value = 'Add User';
      }
    }
  });

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

          // Update total notification count
          totalNotificationsElement.textContent = notifications.length;
          notificationDot.classList.remove('hidden');  // Show red dot

        } else {
          totalNotificationsElement.innerText = '0';
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
});