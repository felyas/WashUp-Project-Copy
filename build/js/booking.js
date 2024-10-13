import { handleDisplayCurrentTime, showToaster, Modal } from "./dashboards-main.js";

// Initialize functions from external imports
handleDisplayCurrentTime();

// Event: Redirect to customer dashboard
const backToDashboardBtn = document.getElementById('back-to-dashboard-btn');
backToDashboardBtn.addEventListener('click', () => {
  window.location.href = './customer-dashboard.php';
});

// Function to set the minimum date and display the current date in the pick-up date input
function setMinDate() {
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
  const dd = String(today.getDate()).padStart(2, '0');
  const currentDate = `${yyyy}-${mm}-${dd}`;

  const dateInput = document.getElementById('pickup-date');

  // Set both min and value attributes to today's date
  dateInput.setAttribute('min', currentDate);
  dateInput.setAttribute('value', currentDate);
}

// Function to generate 20-minute interval time options from 8:00 AM to 9:00 PM
function populateTimeOptions() {
  const selectTime = document.getElementById('pickup-time');
  const startTime = 8 * 60; // 8:00 AM in minutes
  const endTime = 21 * 60; // 9:00 PM in minutes
  const interval = 20; // 20 minutes

  for (let time = startTime; time <= endTime; time += interval) {
    const hours = Math.floor(time / 60);
    const minutes = time % 60;

    const isPM = hours >= 12;
    const displayHours = hours % 12 || 12; // Convert to 12-hour format
    const displayMinutes = minutes.toString().padStart(2, '0');
    const ampm = isPM ? 'PM' : 'AM';

    const timeFormatted = `${displayHours}:${displayMinutes} ${ampm}`;

    const option = document.createElement('option');
    option.value = timeFormatted; // Set the value to the same format as the text
    option.textContent = timeFormatted; // Set the text content

    selectTime.appendChild(option);
  }
}


const addBookingForm = document.getElementById('add-booking-form');
const addBookingBtn = document.getElementById('add-booking-btn');
// Initialize date/time when the page loads
document.addEventListener("DOMContentLoaded", () => {
  setMinDate();
  populateTimeOptions();

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
        window.location.href = './customer-dashboard.php';

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

