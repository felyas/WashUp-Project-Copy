import { updateCopyRightYear } from "./main.js";

updateCopyRightYear();

document.querySelectorAll('.show-password').forEach((icon) => {
  icon.addEventListener('click', () => {
    const input = icon.previousElementSibling; // Get the input field before the icon

    // Toggle the type of the input field
    if (input.type === 'password') {
      input.type = 'text'; // Show the password
      icon.src = './img/icons/eye-open.svg'; // Change the icon to eye-open
    } else {
      input.type = 'password'; // Hide the password
      icon.src = './img/icons/eye-close.svg'; // Change the icon back to eye-close
    }
  });
});

document.addEventListener("DOMContentLoaded", () => {

  const loginForm = document.getElementById('login-form');
  const loginBtn = document.getElementById
    ('login-submit-button');
  const errorContainer = document.getElementById('error-container');
  const errorMessage = document.getElementById('error-message');

  let loginAttempts = 0;
  let isFrozen = false;
  let countdown = 30;
  let countdownInterval;

  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (isFrozen) {
      errorContainer.classList.remove('hidden');
      errorMessage.innerText = `Login is frozen. Please wait ${countdown} seconds.`;
      return;
    }

    loginBtn.value = 'Please wait...';

    const formData = new FormData(loginForm);
    formData.append('login', 1);

    try {
      const data = await fetch('./backend/authentication_action.php', {
        method: 'POST',
        body: formData,
      });

      const response = await data.json();

      if (response.redirect) {
        window.location.href = response.redirect;  // Redirect to the appropriate page
      } else if (response.error) {
        errorContainer.classList.remove('hidden');
        errorMessage.innerText = response.error;
        loginBtn.value = 'Login';  // Reset the button text

        loginAttempts++;
        if (loginAttempts >= 3) {
          freezeLogin();
        }
      }
    } catch (error) {
      errorContainer.classList.remove('hidden');
      errorMessage.innerText = "An error occurred. Please try again.";
      location.reload();
      loginBtn.value = 'Login';
    }
  });

  function freezeLogin() {
    isFrozen = true;
    loginBtn.disabled = true;
    countdown = 30;

    errorContainer.classList.remove('hidden');
    errorMessage.innerText = `Too many failed login attempts. Please wait ${countdown} seconds before trying again.`;

    countdownInterval = setInterval(() => {
      countdown--;
      errorMessage.innerText = `Too many failed login attempts. Please wait ${countdown} seconds before trying again.`;

      if (countdown <= 0) {
        clearInterval(countdownInterval);
        isFrozen = false;
        loginAttempts = 0;
        loginBtn.disabled = false;
        errorContainer.classList.add('hidden');
      }
    }, 1000);
  }


});