import { handleSidebar, handleDisplayCurrentTime, openModal, handleDropdown, showToaster, Modal } from "./dashboards-main.js";


handleSidebar();
handleDisplayCurrentTime();
handleDropdown();
openModal('editModalTrigger', 'toEditItemModal', 'closeEditItemModal', 'closeEditItemModal2');
openModal('addModalTrigger', 'toAddItemModal', 'closeAddItemModal', 'closeAddItemModal2');
openModal('openBarcodeScannerModal', 'toScanBarcodeModal', 'closeScannerModal', 'closeScannerModal2');

const addItemForm = document.getElementById('add-items-form');
const editItemForm = document.getElementById('update-items-form');
const addItemBtn = document.getElementById('add-item-btn');
const editItemBtn = document.getElementById('edit-booking-btn');

// Handle the input validation from add items
function validateForm(form) {
  form.addEventListener('input', (e) => {
    const target = e.target;

    if (target.tagName === 'INPUT') {
      // Locate the closest parent `.mb-4` container
      const parentContainer = target.closest('.mb-4');
      // Find the feedback message inside the parent container
      const feedback = parentContainer.querySelector('.text-red-500');

      if (target.checkValidity()) {
        target.classList.remove('border-red-500');
        target.classList.add('border-green-700'); // Change border to green
        if (feedback) feedback.classList.add('hidden');
      } else {
        target.classList.remove('border-green-700');
        target.classList.add('border-red-500'); // Change border to red if still invalid
        if (feedback) feedback.classList.remove('hidden');
      }
    }
  });
}
validateForm(addItemForm);
validateForm(editItemForm);

document.addEventListener("DOMContentLoaded", () => {
  const tbody = document.getElementById('js-inventory-tbody');
  const paginationContainer = document.getElementById('pagination-container');
  const searchInput = document.getElementById('js-search-bar');
  const statusFilter = document.getElementById('status-filter');

  const currentCriticalPoint = async () => {
    // Fetch current critical point value from the server on page load
    const data = await fetch(`./backend/inventory_action.php?get-current-critical-point=1`, {
      method: 'GET',
    });
    const response = await data.json();
    if (response.status === 'success') {
      // Set the dropdown's selected option to the current critical point
      document.getElementById('critical-point-dropdown').value = response.current_critical_point;
      fetchAll();
    } else {
      console.error('Failed to fetch critical point:', response.message);
    }
  }
  currentCriticalPoint();



  // Event listener to handle dropdown change and update critical point in database
  const criticalPointDropdown = document.getElementById('critical-point-dropdown');
  criticalPointDropdown.addEventListener('change', async () => {
    const newCriticalPoint = criticalPointDropdown.value;

    // Send AJAX request to update the critical point in the database
    const data = await fetch('./backend/inventory_action.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ criticalPoint: newCriticalPoint }),
    });

    const response = await data.json();
    if (response.status === 'success') {
      currentCriticalPoint();
    } else {
      showToaster('Something went wrong !', 'exclamation-error', '#dc2626', '#b91c1c');
    }
  });

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


  // SCANNING BARCODE SECTION
  const scanBtn = document.getElementById('start-scanning-button');
  const stopScanningBtn = document.getElementById('stop-scanning-button');
  const scannerContainer = document.getElementById('scanner-container');
  let isScanning = false;
  let addQuantity = false;

  scanBtn.addEventListener('click', () => {
    document.getElementById('bar-code-input').value = '';
    document.getElementById('add-product').value = '';
    document.getElementById('add-quantity').value = '';
    if (isScanning) return; // Prevent initializing multiple times
    isScanning = true;

    // Ensure the scanner container's size is appropriate on mobile
    const scannerContainer = document.getElementById('scanner-container');
    scannerContainer.style.width = '100%';
    scannerContainer.style.height = '100%';

    Quagga.init({
      inputStream: {
        name: "Live",
        type: "LiveStream",
        target: scannerContainer, // Use the scanner container
        constraints: {
          facingMode: "environment" // Use the rear camera
        }
      },
      decoder: {
        readers: ["code_128_reader", "ean_reader", "upc_reader"] // Add other barcode formats as needed
      }
    }, (err) => {
      if (err) {
        console.error("Quagga initialization failed:", err);
        return;
      }
      Quagga.start();
    });

    Quagga.onDetected((result) => {
      const scannerModal = document.getElementById('scanner-modal');
      scannerModal.classList.add('hidden');
      const code = result.codeResult.code;

      const scanResult = document.getElementById('bar-code-input');
      scanResult.value = code;

      if (scanResult) {
        itsExist(code);
        addQuantity = true;
      }

      // Stop scanning after detection
      Quagga.stop();
      isScanning = false;
    });
  });

  stopScanningBtn.addEventListener('click', () => {
    Quagga.stop();
    isScanning = false;
  });

  const itsExist = async (scanResult) => {
    const data = await fetch(`./backend/inventory_action.php?isExist=1&barcode=${scanResult}`, {
      method: 'GET',
    });

    const response = await data.json();

    if (response.status === 'success') {
      // Update the input fields with product response
      document.getElementById('add-product').value = response.item.product_name;
      document.getElementById('add-quantity').value = response.item.quantity;
      // Set `addQuantity` to true and update the button to 'Update'
      addItemBtn.value = 'Update';
      addQuantity = true;

    } else if (response.status === 'item not found') {
      // Clear the input fields
      document.getElementById('add-product').value = '';
      document.getElementById('add-quantity').value = '';
      // Set `addQuantity` to false and set the button to 'Add'
      addItemBtn.value = 'Add';
      addQuantity = false;
    }
  };

  // Add New Booking Ajax Request
  addItemForm.addEventListener('submit', async (e) => {

    e.preventDefault();
    if (addQuantity) {

      const formData = new FormData(addItemForm);
      formData.append('add-quantity', 1);

      // Form validation
      if (addItemForm.checkValidity() === false) {
        e.stopPropagation();

        // Add validation error handling
        [...addItemForm.elements].forEach((input) => {
          if (input.tagName === 'INPUT' && input.type === 'text') {
            // Locate the closest parent `.mb-4` container
            const parentContainer = input.closest('.mb-4');
            // Find the feedback message inside the parent container
            const feedback = parentContainer.querySelector('.text-red-500');

            if (!input.checkValidity()) {
              input.classList.add('border-red-500');
              if (feedback) feedback.classList.remove('hidden');
            } else {
              input.classList.remove('border-red-500');
              if (feedback) feedback.classList.add('hidden');
            }
          }
        });
        const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
        errorWarningModal.showWithoutMessage();
        return false;

      } else {
        addItemBtn.value = 'Please Wait...';
        const data = await fetch('./backend/inventory_action.php', {
          method: 'POST',
          body: formData,
        });

        const response = await data.text();

        // Handle success or error response
        if (response.includes('success')) {
          fetchAll();
          showToaster('Item added successfully!', 'check', '#047857', '#065f46');
          addItemBtn.value = 'Add';
          addItemForm.reset();
          document.querySelector('.toAddItemModal').classList.add('hidden');

          // Remove validation classes after reset
          [...addItemForm.elements].forEach((input) => {
            if (input.tagName === 'INPUT') {
              input.classList.remove('border-green-700', 'border-red-500');
            }
          });
        } else {
          showToaster('Something went wrong !', 'exclamation-error', '#dc2626', '#b91c1c');
        }
      }
    } else {
      e.preventDefault();

      const formData = new FormData(addItemForm);
      formData.append('add', 1);

      // Form validation
      if (addItemForm.checkValidity() === false) {
        e.stopPropagation();

        // Add validation error handling
        [...addItemForm.elements].forEach((input) => {
          if (input.tagName === 'INPUT' && input.type === 'text') {
            // Locate the closest parent `.mb-4` container
            const parentContainer = input.closest('.mb-4');
            // Find the feedback message inside the parent container
            const feedback = parentContainer.querySelector('.text-red-500');

            if (!input.checkValidity()) {
              input.classList.add('border-red-500');
              if (feedback) feedback.classList.remove('hidden');
            } else {
              input.classList.remove('border-red-500');
              if (feedback) feedback.classList.add('hidden');
            }
          }
        });
        const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
        errorWarningModal.showWithoutMessage();
        return false;

      } else {
        addItemBtn.value = 'Please Wait...';
        const data = await fetch('./backend/inventory_action.php', {
          method: 'POST',
          body: formData,
        });

        const response = await data.text();

        // Handle success or error response
        if (response.includes('success')) {
          fetchAll();
          showToaster('Item added successfully!', 'check', '#047857', '#065f46');
          addItemBtn.value = 'Add';
          addItemForm.reset();
          document.querySelector('.toAddItemModal').classList.add('hidden');

          // Remove validation classes after reset
          [...addItemForm.elements].forEach((input) => {
            if (input.tagName === 'INPUT') {
              input.classList.remove('border-green-700', 'border-red-500');
            }
          });
        } else {
          showToaster('Something went wrong !', 'exclamation-error', '#dc2626', '#b91c1c');
        }
      }
    }
  });

  //Targeting anchor tags from tbody
  tbody.addEventListener('click', (e) => {
    if (e.target && (e.target.matches('a.editLink') || e.target.closest('a.editLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.editLink') ? e.target : e.target.closest('a.editLink');
      let id = targetElement.getAttribute('id');
      itemDetail(id);
    }

    // Delete Item Ajax Request
    if (e.target && (e.target.matches('a.deleteLink') || e.target.closest('a.deleteLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.deleteLink') ? e.target : e.target.closest('a.deleteLink');
      let id = targetElement.getAttribute('id');
      let origin = targetElement.getAttribute('data-origin');
      let key = targetElement.getAttribute('data-key');
      let value = targetElement.getAttribute('data-value');
      const deleteWarningModal = new Modal('delete-modal', 'delete-confirm-modal', 'delete-close-modal', 'modal-message');
      deleteWarningModal.show(deleteItem, {id, origin, key, value}, 'Do you really want to delete this item?');
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
    editItemBtn.value = 'Please Wait...';

    // Send the form data via POST to the backend
    const data = await fetch('./backend/inventory_action.php', {
      method: 'POST',
      body: formData,
    });

    const response = await data.text();

    // Handle success or error response
    if (response.includes('success')) {
      const green600 = '#047857';
      const green700 = '#065f46';
      showToaster('Item updated successfully!', 'check', green600, green700);
      editItemBtn.value = 'Save';
      editItemForm.reset();
      document.querySelector('.toEditItemModal').classList.add('hidden');

      // Call the function to refresh or display updated data
      fetchAll();
    } else {
      const red600 = '#dc2626';
      const red700 = '#b91c1c';
      showToaster('Something went wrong !', 'exclamation-error', red600, red700);
    }
    fetchAll();
  });

  // Delete Item Ajax Request
  const deleteItem = async (dataset) => {
    const {id, origin, key, value} = dataset;
    const data = await fetch(`./backend/admin-archive_action.php?archive=1&id=${id}&origin_table=${origin}&key=${key}&value=${value}`, {
      method: 'GET',
    });
    const response = await data.json();
    if (response.status === 'success') {
      const green600 = '#047857';
      const green700 = '#065f46';
      showToaster(response.message, 'archive', green600, green700);
      fetchAll();
    } else {
      const red600 = '#dc2626';
      const red700 = '#b91c1c';
      showToaster('Something went wrong !', 'exclamation-error', red600, red700);
      fetchAll();
    }
  };

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






