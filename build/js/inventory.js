import { handleSidebar, handleDisplayCurrentTime, openModal } from "./dashboards-main.js";

document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();
  openModal('editModalTrigger', 'toEditItemModal', 'closeEditItemModal', 'closeEditItemModal2');
  openModal('addModalTrigger', 'toAddItemModal', 'closeAddItemModal', 'closeAddItemModal2');

  const now = new Date();

  // Set the current date to the added_date input
  document.getElementById('added_date').value = now.toISOString().split('T')[0]; // yyyy-mm-dd

  // Set the current time to the added_time input
  document.getElementById('added_time').value = now.toTimeString().slice(0, 5); // HH:mm


  const addItemForm = document.getElementById('add-items-form');

  // Handle the input validation
  addItemForm.addEventListener('input', (e) => {
    const target = e.target;
    const feedback = target.nextElementSibling;

    if (target.tagName === 'INPUT' && target.type !== 'radio' || target.tagName === 'TEXTAREA') {
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

  // Add New Booking Ajax Request
  addItemForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(addItemForm);
    formData.append('add', 1);

    // Form validation
    if (addItemForm.checkValidity() === false) {
      e.stopPropagation();

      // Add validation error handling
      [...addItemForm.elements].forEach((input) => {
        const feedback = input.nextElementSibling;

        if (input.tagName === 'INPUT' && (input.type === 'text' || input.type === 'date' || input.type === 'time')) {
          // Handle text input validation feedback
          if (!input.checkValidity()) {
            input.classList.add('border-red-500');
            feedback.classList.remove('hidden');
          } else {
            input.classList.remove('border-red-500');
            feedback.classList.add('hidden');
          }
        }
      });

      // **Ensure SweetAlert isn't stacking up**
      if (!Swal.isVisible()) {
        Swal.fire({
          icon: 'error',
          title: 'Validation Error',
          text: 'Please fill out all required fields correctly.',
          confirmButtonText: 'OK',
          customClass: {
            confirmButton: 'bg-gray-500 hover:bg-gray-600 text-white px-5 py-3 font-semibold rounded-lg'
          },
          buttonsStyling: false
        });
      }

      return false;
    } else {
      document.getElementById('add-item-btn').value = 'Please Wait...';

      const data = await fetch('./backend/inventory_action.php', {
        method: 'POST',
        body: formData,
      }); 

      const response = await data.text();
      console.log(response);

    }
  });




});






