import { updateCopyRightYear, initApp, directToLoginPage, } from "./main.js";

import { showToaster, Modal } from "./dashboards-main.js";

document.addEventListener('DOMContentLoaded', updateCopyRightYear);
document.addEventListener('DOMContentLoaded', initApp);
document.addEventListener('DOMContentLoaded', directToLoginPage('signInButton1'));
document.addEventListener('DOMContentLoaded', directToLoginPage('signInButton2'));
document.addEventListener('DOMContentLoaded', directToLoginPage('signInButton3'));

const testimonialsContainer = document.getElementById('js-testimonials');
const fetchTestimonials = async () => {
  const data = await fetch(`./backend/admin_action.php?fetch-testimonials=1`, {
    method: 'GET',
  });
  const response = await data.text();
  // console.log(response);
  testimonialsContainer.innerHTML = response;
}
fetchTestimonials();

const contactUsForm = document.getElementById('contact-us-form');
const submitBtn = document.getElementById('submit-button');

// Function to validateForm
function validateForm(form) {
  form.addEventListener('input', (e) => {
    const target = e.target;
    const feedback = target.nextElementSibling;

    if (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA') {
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
}
validateForm(contactUsForm);

contactUsForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  let hasError = false;

  const formData = new FormData(contactUsForm);
  formData.append('new-message', 1);
  submitBtn.value = 'Please wait...';

  // Validate all inputs
  [...contactUsForm.elements].forEach((input) => {
    const feedback = input.nextElementSibling;

    // Check for required fields (text, email, textarea)
    if (input.hasAttribute('required')) {
      if (!input.value.trim()) {
        // Show error styles and message
        input.classList.add('border-red-500');
        if (feedback && feedback.classList.contains('text-red-500')) {
          feedback.classList.remove('hidden');
        }
        hasError = true;
      } else {
        // Remove error styles and message if valid
        input.classList.remove('border-red-500');
        if (feedback && feedback.classList.contains('text-red-500')) {
          feedback.classList.add('hidden');
        }
      }
    }
  });

  if (hasError) {
    const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
    errorWarningModal.show();
    return;
  } else {
    submitBtn.value = 'Please wait...';
    const data = await fetch('./backend/landing_page_action.php', {
      method: 'POST',
      body: formData,
    });

    const response = await data.json();
    if (response.status === 'success') {
      showToaster('Message sent successfully', 'check', '#047857', '#065f46');
      contactUsForm.reset();
      submitBtn.value = 'Send';

      // Remove validation styles from all inputs and textareas
      [...contactUsForm.elements].forEach((element) => {
        if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
          element.classList.remove('border-red-500', 'border-green-700'); // Remove all validation styles
        }
      });
    } else if (response.status === 'error') {
      document.getElementById('error-msg').innerHTML = `<p class="text-red-700">${response.message}</p>`;
      submitBtn.value = 'Send';
    }

  }

});