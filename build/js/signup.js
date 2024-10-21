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

  const signupForm = document.getElementById('signup-form');
  const signupBtn = document.getElementById('signup-button');
  const errorContainer = document.getElementById('error-container');
  const errorMessage = document.getElementById('error-message');

  signupForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    signupBtn.value = 'Please wait...';

    const formData = new FormData(signupForm);
    formData.append('signup', 1);

    try {
      const data = await fetch('./backend/authentication_action.php', {
        method: 'POST',
        body: formData,
      });

      const response = await data.json();
      if(response.redirect) {
        window.location.href = response.redirect;
      } else if(response.error) {
        errorContainer.classList.remove('hidden');
        errorMessage.innerText = response.error;
        signupBtn.value = 'Sign Up';
      }
    } catch (error) {
      errorContainer.classList.remove('hidden');
      errorMessage.innerText = "An error occurred. Please try again.";
      location.reload();
      signupBtn.value = 'Sign Up';
    }





  })

});