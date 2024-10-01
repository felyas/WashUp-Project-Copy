import { handleSidebar, handleDisplayCurrentTime, openModal, handleDropdown } from "./dashboards-main.js";


handleSidebar();
handleDisplayCurrentTime();
handleDropdown();
openModal('editModalTrigger', 'toEditItemModal', 'closeEditItemModal', 'closeEditItemModal2');
openModal('addModalTrigger', 'toAddItemModal', 'closeAddItemModal', 'closeAddItemModal2');

const addItemForm = document.getElementById('add-items-form');
const editItemForm = document.getElementById('update-items-form');

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
validateForm(addItemForm);
validateForm(editItemForm);

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

      if (input.tagName === 'INPUT' && (input.type === 'text')) {
        if (!input.checkValidity()) {
          input.classList.add('border-red-500');
          feedback.classList.remove('hidden');
        } else {
          input.classList.remove('border-red-500');
          feedback.classList.add('hidden');
        }
      }
    });

    // Show validation error using the reusable function
    if (!Swal.isVisible()) {
      showAlert('error', 'Validation Error', 'Please fill out all required fields correctly.');
    }

    return false;
  } else {
    document.getElementById('add-item-btn').value = 'Please Wait...';

    const data = await fetch('./backend/inventory_action.php', {
      method: 'POST',
      body: formData,
    });

    const response = await data.text();

    // Handle success or error response
    if (response.includes('success')) {
      showAlert('success', 'Success', 'Item added successfully!', 'bg-green-700 hover:bg-green-800').then(() => {
        document.getElementById('add-item-btn').value = 'Add';
        addItemForm.reset();
        document.querySelector('.toAddItemModal').classList.add('hidden');

        // Remove validation classes after reset
        [...addItemForm.elements].forEach((input) => {
          if (input.tagName === 'INPUT') {
            input.classList.remove('border-green-700', 'border-red-500');
          }
        });

        fetchAll();
      });
    } else {
      showAlert('error', 'Error', 'Something went wrong!', 'bg-gray-700 hover:bg-gray-800');
    }
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const tbody = document.getElementById('js-inventory-tbody');
  const paginationContainer = document.getElementById('pagination-container');
  const searchInput = document.getElementById('js-search-bar');
  const statusFilter = document.getElementById('status-filter');

  // Function to toggle sorting icons
  const toggleSortIcon = (th, order) => {
    const allIcons = document.querySelectorAll('.sort-icon img');

    // Reset all icons to caret-down by default
    allIcons.forEach(icon => {
      icon.setAttribute('src', './img/icons/caret-down.svg'); // Set to down arrow
    });

    const icon = th.querySelector('.sort-icon img');
    if (order === 'desc') {
      icon.setAttribute('src', './img/icons/caret-down.svg'); // Down arrow
    } else {
      icon.setAttribute('src', './img/icons/caret-up.svg'); // Up arrow
    }
  };

  // Fetch All items with pagination, search, and sorting
  const fetchAll = async (page = 1, column = 'product_id', order = 'desc', query = '') => {
    const searchQuery = searchInput.value.trim() || query;
    const statusQuery = status || statusFilter.value; // Get status from dropdown or passed value

    const data = await fetch(`./backend/inventory_action.php?readAll=1&page=${page}&column=${column}&order=${order}&query=${searchQuery}&status=${statusQuery}`, {
      method: 'GET',
    });
    const response = await data.json();
    tbody.innerHTML = response.items;
    paginationContainer.innerHTML = response.pagination;
  }

  // Handle Column Sorting 
  document.querySelectorAll('.sortable').forEach(th => {
    th.addEventListener('click', () => {
      const column = th.getAttribute('data-column');
      let order = th.getAttribute('data-order');
      order = order === 'desc' ? 'asc' : 'desc';

      th.setAttribute('data-order', order);
      toggleSortIcon(th, order);

      fetchAll(1, column, order);
    });
  });

  // Handle search input
  searchInput.addEventListener('input', () => {
    fetchAll();
  });

  paginationContainer.addEventListener('click', (e) => {
    if (e.target.classList.contains('pagination-link')) {
      e.preventDefault();
      const page = e.target.getAttribute('data-page');
      fetchAll(page);
    }
  });

  statusFilter.addEventListener('change', () => {
    fetchAll();
  })

  // Initial fetch
  fetchAll();

  //Targeting anchor tags from tbody
  tbody.addEventListener('click', (e) => {
    if (e.target && (e.target.matches('a.editLink') || e.target.closest('a.editLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.editLink') ? e.target : e.target.closest('a.editLink');
      let id = targetElement.getAttribute('id');
      itemDetail(id);
    }

    // Delete Booking Ajax Request
    if (e.target && (e.target.matches('a.deleteLink') || e.target.closest('a.deleteLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.deleteLink') ? e.target : e.target.closest('a.deleteLink');
      let id = targetElement.getAttribute('id');
      deleteItem(id);
    }
  });

  // View Item Detail Ajax Request
  const itemDetail = async (id) => {
    const data = await fetch(`./backend/inventory_action.php?item-detail=1&product_id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('product_id').value = response.product_id;
    document.getElementById('display-product-name').innerHTML = response.product_name;
    document.getElementById('display-bar-code').innerHTML = response.bar_code;
    document.getElementById('display-max-qty').innerHTML = response.max_quantity;
    document.getElementById('quantity').value = response.quantity;
  }

  // Update Item Ajax Request 
  editItemForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(editItemForm);
    formData.append('update', 1);

    // Immediately update the button text to indicate the form is being processed
    document.getElementById('edit-booking-btn').value = 'Please Wait...';

    // Send the form data via POST to the backend
    const data = await fetch('./backend/inventory_action.php', {
      method: 'POST',
      body: formData,
    });

    const response = await data.text();

    // Handle success or error response
    if (response.includes('success')) {
      showAlert('success', 'Success', 'Item updated successfully!', 'bg-green-700 hover:bg-green-800').then(() => {
        document.getElementById('edit-booking-btn').value = 'Save';
        editItemForm.reset();
        document.querySelector('.toEditItemModal').classList.add('hidden');

        // Call the function to refresh or display updated data
        fetchAll();
      });
    } else {
      showAlert('error', 'Error', 'Something went wrong!', 'bg-gray-700 hover:bg-gray-800');
    }
  });

  // Delete Item Ajax Request
  const deleteItem = async (id) => {
    const result = await Swal.fire({
      title: 'Are you sure?',
      text: 'Do you want to delete this item?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      cancelButtonText: 'No',
      customClass: {
        confirmButton: 'bg-red-700 hover:bg-red-800 text-white px-5 py-3 mr-4 font-semibold rounded-lg',
        cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white px-5 py-3 font-semibold rounded-lg'
      },
      buttonsStyling: false
    });

    if (result.isConfirmed) {
      const data = await fetch(`./backend/inventory_action.php?delete=1&product_id=${id}`, {
        method: 'GET',
      });
      const response = await data.text();

      if (response.includes('success')) {
        await showAlert('success', 'Deleted!', 'Item deleted successfully.', 'bg-green-700 hover:bg-green-800');
        fetchAll();
      } else {
        await showAlert('error', 'Error!', 'Failed to delete item.', 'bg-red-700 hover:bg-red-800');
      }
    }
  };


  
});






