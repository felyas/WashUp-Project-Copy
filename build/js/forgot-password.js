import { updateCopyRightYear } from "./main.js";

updateCopyRightYear();

document.addEventListener("DOMContentLoaded", () => {

  const forgotPasswordForm = document.getElementById('forgot-password-form');
  const forgotPasswordBtn = document.getElementById('forgot-password-button');
  const errorContainer = document.getElementById('error-container');
  const errorMessage = document.getElementById('error-message')

  forgotPasswordForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    forgotPasswordBtn.value = "Please wait...";

    const formData = new FormData(forgotPasswordForm);
    formData.append('forgot-password', 1);

    try {
      const data = await fetch('./backend/authentication_action.php', {
        method: 'POST',
        body: formData,
      });

      const response = await data.json();
      if (response.redirect) {
        window.location.href = response.redirect;
      } else if (response.error) {
        errorContainer.classList.remove('hidden');
        errorMessage.innerText = response.error;
        forgotPasswordBtn.value = "Send";
      } else if (response.success) {
        errorMessage.classList.remove('text-red-700');
        errorContainer.classList.remove('hidden');
        errorMessage.classList.add('text-green-700');
        errorMessage.innerText = response.success;
        forgotPasswordBtn.value = "Send";
        forgotPasswordForm.reset();

        setInterval(() => {
          window.location.href = './login.php';
        }, 5000);
      }

    } catch (error) {
      errorContainer.classList.remove('hidden');
      errorMessage.innerText = "An error occurred. Please try again.";
      location.reload();
      forgotPasswordBtn.value = 'Submit';
    }
  })

});