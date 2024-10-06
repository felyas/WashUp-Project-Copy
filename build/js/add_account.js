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
});