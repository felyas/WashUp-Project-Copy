import { handleDisplayCurrentTime, showToaster, Modal } from "./dashboards-main.js";

// Initialize functions from external imports
handleDisplayCurrentTime();

// Event: Redirect to customer dashboard
const backToDashboardBtn = document.getElementById('back-to-dashboard-btn');
backToDashboardBtn.addEventListener('click', () => {
  window.location.href = './customer-dashboard.php';
});

// Function: Format time to 12-hour format with AM/PM
const formatTime = (date) => {
  const hours = date.getHours();
  const minutes = date.getMinutes();
  const period = hours >= 12 ? 'PM' : 'AM';
  const formattedHours = hours % 12 || 12;
  const formattedMinutes = String(minutes).padStart(2, '0');
  return `${formattedHours}:${formattedMinutes} ${period}`;
};

// Function: Set current date and time in the input fields
const setCurrentDateTime = () => {
  const now = new Date();
  const pickupDateInput = document.querySelector('input[name="pickup_date"]');
  const pickupTimeInput = document.querySelector('input[name="pickup_time"]');

  // Set current date and time + 30 minutes
  pickupDateInput.value = now.toISOString().split('T')[0];
  now.setMinutes(now.getMinutes() + 30);
  pickupTimeInput.value = now.toTimeString().substring(0, 5); // Format time
};

const addBookingForm = document.getElementById('add-booking-form');
const addBookingBtn = document.getElementById('add-booking-btn');
// Initialize date/time when the page loads
document.addEventListener("DOMContentLoaded", () => {
  setCurrentDateTime();

  // Handle the input validation from add bookings
  addBookingForm.addEventListener('input', (e) => {
    const target = e.target;
    const feedback = target.nextElementSibling;

    if (target.tagName === 'INPUT' && (target.type === 'text' || target.type === 'date' || target.type === 'time' || target.type === 'number')) {
      if (target.checkValidity()) {
        target.classList.remove('border-red-500');
        target.classList.add('border-green-700');
        if (feedback && feedback.classList.contains('text-red-500')) {
          feedback.classList.add('hidden');
        }
      } else {
        target.classList.remove('border-green-700');
        target.classList.add('border-red-500');
        if (feedback && feedback.classList.contains('text-red-500')) {
          feedback.classList.remove('hidden');
        }
      }
    }
  });

  addBookingForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(addBookingForm);
    formData.append('add', 1);

    if (addBookingForm.checkValidity() === false) {
      e.stopPropagation();

      [...addBookingForm.elements].forEach((input) => {
        if (input.tagName === 'INPUT' && (input.type === 'text' || input.type === 'date' || input.type === 'time' || input.type === 'number')) {
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
      // Scroll to the top of the page smoothly
      document.querySelector('#top').scrollIntoView({ behavior: 'smooth' });
      const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
      errorWarningModal.show();

      return false;
    } else {
      addBookingBtn.value = 'Please Wait...';

      const data = await fetch('./backend/customer_action.php', {
        method: 'POST',
        body: formData,
      });
      const response = await data.text();

      if (response.includes('success')) {
        const green600 = '#047857';
        const green700 = '#065f46';
        showToaster('Your booking has been placed !', 'check', green600, green700);

        // Disable the submit button to prevent further clicks
        addBookingBtn.disabled = true;
        addBookingBtn.classList.add('opacity-50', 'cursor-not-allowed'); // Optionally change the button style

        setTimeout(() => {
          window.location.href = './customer-dashboard.php';
        }, 500);
      } else {
        const red600 = '#d95f5f';
        const red700 = '#c93c3c';
        showToaster('Something went wrong !', 'exclamation-error', red600, red700);
      }
    }
  });

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
          notificationContainer.innerHTML = '';

          // Append each notification to the container
          notifications.forEach(notification => {
            const notificationElement = document.createElement('div');
            notificationElement.classList.add('p-2', 'flex', 'items-center', 'justify-between', 'bg-gray-200', 'mb-1');
            notificationElement.innerHTML = `
          <p class="w-auto">Booking #${notification.id} status updated to "${notification.status}"</p>
          <button class="w-12 p-0 border-none font-bold js-notification-close" data-id="${notification.id}">&#10005;</button>
        `;
            notificationContainer.appendChild(notificationElement);
          });

          // Update total notification count
          totalNotificationsElement.textContent = notifications.length;
          notificationDot.classList.remove('hidden');
          // Calling other function to update the UI
          fetchAllBookings();
          fetchBookingCounts();
        } else {
          notificationDot.classList.add('hidden');
        }

        // Continue long polling after 5 seconds
        setTimeout(() => {
          const currentTimestamp = new Date().toISOString(); // Use current time as last check
          fetchNotifications(currentTimestamp);
        }, 10000); // Check every 5 seconds
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

