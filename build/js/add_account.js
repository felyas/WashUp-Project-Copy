import { handleSidebar, handleDisplayCurrentTime, handleDropdown } from "./dashboards-main.js";

document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();
  handleDropdown();

  // Reusable SweetAlert Function
  const showAlert = (icon, title, text, confirmButtonClass = 'bg-gray-500 hover:bg-gray-600') => {
    return Swal.fire({
      icon: icon,
      title: title,
      text: text,
      confirmButtonText: 'OK',
      customClass: {
        confirmButton: `${confirmButtonClass} text-white px-5 py-3 font-semibold rounded-lg`
      },
      buttonsStyling: false
    });
  };

  const addUserForm = document.getElementById('add-user-form');

  // Handle the input validation from add items
  function validateForm(form) {
    form.addEventListener('input', (e) => {
      const target = e.target;
      const feedback = target.nextElementSibling;

      if (target.tagName === 'INPUT') {
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
  validateForm(addUserForm);

  // Handle Add Administrator Ajax Request
  addUserForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(addUserForm);
    formData.append('add', 1);

    if (addUserForm.checkValidity() === false) {
      // Perform validation
      return false;
    } else {
      document.getElementById('add-user-btn').value = 'Please Wait...';

      try {
        const response = await fetch('./backend/account_action.php', {
          method: 'POST',
          body: formData,
        });

        const result = await response.json();  // Expect a JSON response

        // Handle the response
        if (result.status === 'success') {
          document.querySelector('.error-div').classList.add('hidden');

          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: result.message,
          }).then(() => {
            addUserForm.reset();  // Optionally reset the form after success
            document.getElementById('add-user-btn').value = 'Add User';
            window.location.href = './account.php';
          });
        } else {
          document.querySelector('.error-div').classList.remove('hidden');

          document.getElementById('js-error-message').innerHTML = result.message;

          document.getElementById('add-user-btn').value = 'Add User';
        }
      } catch (error) {
        document.querySelector('.error-div').classList.remove('hidden');

        document.getElementById('js-error-message').innerHTML = result.message;

        document.getElementById('add-user-btn').value = 'Add User';
      }
    }
  });
});