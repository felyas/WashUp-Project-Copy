import { handleSidebar, handleDisplayCurrentTime, handleDropdown, showToaster, Modal } from "./dashboards-main.js";

document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();
  handleDropdown();

  document.querySelectorAll('.show-password').forEach((icon) => {
    icon.addEventListener('click', () => {
      const container = icon.closest('.relative');
      const input = container.querySelector('input[type="password"], input[type="text"]');

      // Toggle the type of the input field
      if (input.type === 'password') {
        input.type = 'text';
        icon.src = './img/icons/eye-open.svg';
      } else {
        input.type = 'password';
        icon.src = './img/icons/eye-close.svg';
      }
    });
  });

  const generatePasswordBtn = document.getElementById('generate-password');
  generatePasswordBtn.addEventListener('click', () => {
    const passwordInp = document.getElementById('js-password');
    const cpasswordInp = document.getElementById('js-cpassword');

    // Function to generate a random password
    const generatePassword = (length) => {
      const uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      const lowercase = "abcdefghijklmnopqrstuvwxyz";
      const numbers = "0123456789";
      const specialChars = "!@#$%&*_?";
      const allChars = uppercase + lowercase + numbers + specialChars;

      let password = "";

      // Ensure the password includes at least one character of each type
      password += uppercase[Math.floor(Math.random() * uppercase.length)];
      password += lowercase[Math.floor(Math.random() * lowercase.length)];
      password += numbers[Math.floor(Math.random() * numbers.length)];
      password += specialChars[Math.floor(Math.random() * specialChars.length)];

      // Fill the rest of the password with random characters
      for (let i = password.length; i < length; i++) {
        password += allChars[Math.floor(Math.random() * allChars.length)];
      }

      // Shuffle the password to randomize character positions
      password = password.split('').sort(() => Math.random() - 0.5).join('');
      return password;
    };

    // Generate a 12-character password
    const newPassword = generatePassword(12);

    // Set the generated password to the input fields
    passwordInp.value = newPassword;
    cpasswordInp.value = newPassword;
  });



  const addUserForm = document.getElementById('add-user-form');
  const addUserBtn = document.getElementById('add-user-btn');

  const errorDiv = document.getElementById('error-div');
  const errorMsg = document.getElementById('js-error-message');
  const successDiv = document.getElementById('success-div');

  let timeoutId;

  // Handle Add Administrator Ajax Request
  addUserForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    addUserBtn.value = 'Please Wait...';

    const formData = new FormData(addUserForm);
    formData.append('add', 1);

    if (formData) {
      const data = await fetch('./backend/account_action.php', {
        method: 'POST',
        body: formData,
      });

      const response = await data.json();  // Expect a JSON response

      // Handle the response
      if (response.status === 'success') {
        showToaster('User added successfully !', 'check', '#047857', '#065f46');
        addUserBtn.disabled = true;
        addUserBtn.classList.add('opacity-50', 'cursor-not-allowed');
        addUserForm.reset();
        addUserBtn.value = 'Add';

        if (timeoutId) {
          clearTimeout(timeoutId);
        }

        timeoutId = setTimeout(() => {
          window.location.href = './account.php';
          addUserForm.reset();
        }, 500);
      } if (response.status === 'error') {
        errorDiv.classList.remove('hidden');
        errorMsg.innerText = response.message;
        addUserBtn.value = 'Add';

        if (timeoutId) {
          clearTimeout(timeoutId);
        }

        timeoutId = setTimeout(() => {
          errorDiv.classList.add('hidden');
          errorMsg.innerText = '';
        }, 3000);
      } else {
        console.log(response);
        
        showToaster(`${response.message}`, 'exclamation-error', '#d95f5f', '#c93c3c');
        addUserBtn.value = 'Add';
      }
    }
  });

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