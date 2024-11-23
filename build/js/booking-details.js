import { handleSidebar, handleDisplayCurrentTime, openModal, handleDropdown, showToaster, Modal } from "./dashboards-main.js";

// Select the modal and modal image elements
const modal = document.getElementById('imageModal');
const modalImage = document.getElementById('modal-image');
const closeModalButton = document.getElementById('closeImageModal');

// Event delegation: attach the click event listener to a parent element
document.addEventListener('click', function (e) {
  // Check if the clicked element has the class 'image-proof'
  if (e.target.classList.contains('image-proof')) {
    // Set the modal image source to the clicked image's source
    modalImage.src = e.target.src;

    // Display the modal
    modal.classList.remove('hidden');
  }
});

// Close the modal when the close button is clicked
closeModalButton.addEventListener('click', function () {
  modal.classList.add('hidden');
});

// Optionally, close the modal when clicking outside the modal content
window.addEventListener('click', function (e) {
  if (e.target === modal) {
    modal.classList.add('hidden');
  }
});

// Close the modal when clicking outside the modal content
window.addEventListener('click', function (e) {
  if (e.target === modal) {
    modal.classList.add('hidden');
  }
});

document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();
  handleDropdown();
  openModal('viewModalTrigger', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');
  openModal('kiloModalTrigger', 'toUpdateKiloModal', 'closeUpdateKiloModal', 'closeUpdateKiloModal2');

  const tbody = document.getElementById('js-list-tbody');
  const paginationContainer = document.getElementById('pagination-container');
  const searchInput = document.getElementById('js-search-bar');
  const statusFilter = document.getElementById('status-filter');
  const serviceTypeFilter = document.getElementById('service-type-filter');

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

  // Fetching all bookings with pagination, search, sorting, and status filtering
  const fetchAll = async (page = 1, column = 'id', order = 'desc', query = '', status = '') => {
    const searchQuery = searchInput.value.trim() || query;
    const statusQuery = status || statusFilter.value; // Get status from dropdown or passed value
    const serviceQuery = status || serviceTypeFilter.value;

    const data = await fetch(`./backend/booking-details_action.php?readAll=1&page=${page}&column=${column}&order=${order}&query=${searchQuery}&status=${statusQuery}&service=${serviceQuery}`, {
      method: 'GET',
    });
    const response = await data.json();
    tbody.innerHTML = response.bookings;
    paginationContainer.innerHTML = response.pagination; // For pagination
  };

  // Handle column sorting
  document.querySelectorAll('.sortable').forEach(th => {
    th.addEventListener('click', function () {
      const column = this.getAttribute('data-column');
      let order = this.getAttribute('data-order');
      order = order === 'desc' ? 'asc' : 'desc'; // Toggle order

      this.setAttribute('data-order', order);
      toggleSortIcon(this, order); // Change icon direction

      fetchAll(1, column, order);
    });
  });

  // Handle search input
  searchInput.addEventListener('input', () => {
    fetchAll();
  });

  // Handle pagination clicks
  paginationContainer.addEventListener('click', function (event) {
    if (event.target.classList.contains('pagination-link')) {
      event.preventDefault();
      const page = event.target.getAttribute('data-page');
      fetchAll(page);
    }
  });

  // Handle status filter change
  statusFilter.addEventListener('change', () => {
    fetchAll();
  });

  // Handle service type filter change
  serviceTypeFilter.addEventListener('change', () => {
    fetchAll();
  });

  // Initial fetch
  fetchAll();

  // Target Anchor Tags
  tbody.addEventListener('click', (e) => {

    // Target the viewLink
    if (e.target && (e.target.matches('a.viewLink') || e.target.closest('a.viewLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.viewLink') ? e.target : e.target.closest('a.viewLink');
      let id = targetElement.getAttribute('id');
      bookingSummary(id);
    }

    // Target the doneProcessLink
    if (e.target && (e.target.matches('a.doneProcessLink') || e.target.closest('a.doneProcessLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.doneProcessLink') ? e.target : e.target.closest('a.doneProcessLink');
      let id = targetElement.getAttribute('id');
      const confirmationWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal');
      confirmationWarningModal.show(done, id);
    }


    // Target the admitLink
    if (e.target && (e.target.matches('a.admitLink') || e.target.closest('a.admitLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.admitLink') ? e.target : e.target.closest('a.admitLink');
      let id = targetElement.getAttribute('id');
      const admitWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal');
      admitWarningModal.show(admitBooking, id);
    }

    // Target the deniedLink
    if (e.target && (e.target.matches('a.deniedLink') || e.target.closest('a.deniedLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.deniedLink') ? e.target : e.target.closest('a.deniedLink');
      let id = targetElement.getAttribute('id');
      const admitWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal');
      admitWarningModal.show(deniedBooking, id);
    }

    // Target the Update Kilo
    if (e.target && (e.target.matches('a.kiloLink') || e.target.closest('a.kiloLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.kiloLink') ? e.target : e.target.closest('a.kiloLink');
      let id = targetElement.getAttribute('id');
      // console.log(id);
      customerInfo(id);
    }
  })

  // Fetch Booking Details Ajax Request
  const bookingSummary = async (id) => {
    const data = await fetch(`./backend/booking-details_action.php?view=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('booking-date').innerHTML = response.created_at;
    document.getElementById('display-full-name').innerHTML = response.fname + " " + response.lname;
    document.getElementById('display-phone-number').innerHTML = response.phone_number;
    document.getElementById('display-address').innerHTML = response.address;
    document.getElementById('display-pickup-date').innerHTML = response.pickup_date;
    document.getElementById('display-pickup-time').innerHTML = response.pickup_time;
    document.getElementById('display-service-selection').innerHTML = response.service_selection;
    document.getElementById('display-service-type').innerHTML = response.service_type;
    document.getElementById('display-suggestions').innerHTML = response.suggestions;
  }

  // Update Status of Booking Once Done Ajax Request
  const done = async (id) => {
    const data = await fetch(`./backend/booking-details_action.php?done=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.text();
    if (response.includes('success')) {
      const green600 = '#047857';
      const green700 = '#065f46';
      showToaster('Status updated successfully !', 'check', green600, green700);
      fetchAll();
    } else {
      const red600 = '#dc2626'; // Hex value for green-600
      const red700 = '#b91c1c'; // Hex value for green-700
      showToaster('Something went wrong !', 'exclamation-error', red600, red700);
      fetchAll();
    }
  }

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

  // Admit Booking and Update Status to For Pick-Up Ajax Request.
  const admitBooking = async (id) => {
    const data = await fetch(`./backend/admin_action.php?admit=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.text();
    if (response.includes('success')) {
      const green600 = '#047857';
      const green700 = '#065f46';
      showToaster('Booking admitted successfully !', 'check', green600, green700);
      fetchAll();
    } else {
      const red600 = '#dc2626';
      const red700 = '#b91c1c';
      showToaster('Something went wrong !', 'exclamation-error', red600, red700);
      fetchAll();
    }
  }

  // Denied Booking and Update Status to For Pick-Up Ajax Request.
  const deniedBooking = async (id) => {
    const data = await fetch(`./backend/booking-details_action.php?denied=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.text();
    if (response.includes('success')) {
      const green600 = '#047857';
      const green700 = '#065f46';
      showToaster('Booking denied !', 'check', green600, green700);
      fetchAll();
    } else {
      const red600 = '#dc2626';
      const red700 = '#b91c1c';
      showToaster('Something went wrong !', 'exclamation-error', red600, red700);
      fetchAll();
    }
  }

  // Uploading Image and Updating Inventory Ajax Request
  const maxInputs = 3;
  let itemCount = 1; // Initial count is 1 because the first set of fields already exists
  const addItemButton = document.getElementById('add-item');
  const itemContainer = document.getElementById('item-quantity-container');

  const fetchInventory = async () => {
    const data = await fetch(`./backend/booking-details_action.php?items=1`, {
      method: 'GET',
    });
    const response = await data.json();

    return response;
  }

  const firstSelection = async () => {
    const inventory = await fetchInventory();
    const selection1 = document.getElementById('item-1');
    const defaultOption = document.createElement('option');
    defaultOption.textContent = 'Select a product';
    defaultOption.value = '';
    selection1.appendChild(defaultOption);

    inventory.forEach(product => {
      const option = document.createElement('option');
      option.value = product.product_name;
      option.textContent = product.product_name;
      selection1.appendChild(option);
    });
  }
  firstSelection();

  // Function to add new item and quantity inputs
  const addItem = async () => {
    if (itemCount < maxInputs) {
      itemCount++;

      const inventory = await fetchInventory();

      // Create a new div for item and quantity, with relative positioning for the delete button
      const newItemDiv = document.createElement('div');
      newItemDiv.classList.add('relative', 'grid', 'grid-cols-2', 'gap-4', 'p-2', 'border', 'border-solid', 'border-gray-200', 'rounded-md', 'shadow-sm');
      newItemDiv.setAttribute('id', `item-set-${itemCount}`);

      // Create a div for the first column (Item Used label and input)
      const itemDiv = document.createElement('div');

      // Create the label for the dropdown/
      const itemLabel = document.createElement('label');
      itemLabel.setAttribute('for', `item${itemCount}`);
      itemLabel.classList.add('block', 'text-sm', 'font-medium', 'text-gray-500');
      itemLabel.textContent = 'Item Used';

      // Create the dropdown
      const itemSelect = document.createElement('select');
      itemSelect.setAttribute('id', `item${itemCount}`);
      itemSelect.setAttribute('name', `item${itemCount}`); // Unique name
      itemSelect.setAttribute('required', '');
      itemSelect.classList.add('mt-1', 'block', 'w-full', 'border-gray-300', 'rounded-sm', 'py-2', 'px-2', 'border', 'border-solid', 'border-ashblack');

      // Populate dropdown options
      const defaultOption = document.createElement('option');
      defaultOption.textContent = 'Select a product';
      defaultOption.value = '';
      itemSelect.appendChild(defaultOption);

      inventory.forEach(product => {
        const option = document.createElement('option');
        option.value = product.product_name;
        option.textContent = product.product_name;
        itemSelect.appendChild(option);
      })

      // Error message for item dropdown
      const itemError = document.createElement('div');
      itemError.classList.add('text-red-500', 'text-sm', 'hidden');
      itemError.textContent = 'Item is required!';

      // Append label, dropdown, and error to the item div
      itemDiv.appendChild(itemLabel);
      itemDiv.appendChild(itemSelect);
      itemDiv.appendChild(itemError);

      // Create a div for the second column (Quantity label and input)
      const quantityDiv = document.createElement('div');

      // Create the input for quantity
      const quantityLabel = document.createElement('label');
      quantityLabel.setAttribute('for', `quantity${itemCount}`);
      quantityLabel.classList.add('block', 'text-sm', 'font-medium', 'text-gray-500');
      quantityLabel.textContent = 'Quantity';

      const quantityInput = document.createElement('input');
      quantityInput.setAttribute('type', 'number');
      quantityInput.setAttribute('id', `quantity${itemCount}`);
      quantityInput.setAttribute('name', `quantity${itemCount}`); // Unique name
      quantityInput.setAttribute('placeholder', 'e.g., 2');
      quantityInput.setAttribute('required', '');
      quantityInput.classList.add('mt-1', 'block', 'w-full', 'border-gray-300', 'rounded-sm', 'py-2', 'px-2', 'border', 'border-solid', 'border-ashblack');

      // Error message for quantity input
      const quantityError = document.createElement('div');
      quantityError.classList.add('text-red-500', 'text-sm', 'hidden');
      quantityError.textContent = 'Quantity is required!';

      // Append label, input, and error to the quantity div
      quantityDiv.appendChild(quantityLabel);
      quantityDiv.appendChild(quantityInput);
      quantityDiv.appendChild(quantityError);

      // Create delete button wrapper div
      const deleteButtonDiv = document.createElement('div');
      deleteButtonDiv.classList.add('absolute', 'h-5', 'w-5', 'flex', 'items-center', 'justify-center', 'top-1', 'right-1', 'px-2', 'py-1', 'bg-red-700', 'rounded-full', 'cursor-pointer'); // Apply padding and background color

      // Create delete button with image
      const deleteButton = document.createElement('button');
      deleteButton.innerHTML = '<img class="w-3 h-3" src="./img/icons/x.svg" alt="">';
      deleteButton.classList.add('text-red-500', 'hover:text-red-700', 'bg-transparent', 'font-bold');

      // Set the onclick event handler for the delete button
      deleteButtonDiv.onclick = () => {
        itemContainer.removeChild(newItemDiv);
        itemCount--;

        // Show the "Add More Items" button if items are fewer than the limit
        if (itemCount < maxInputs) {
          addItemButton.style.display = 'block';
        }
      };

      // Append itemDiv and quantityDiv to the newItemDiv
      newItemDiv.appendChild(itemDiv);
      newItemDiv.appendChild(quantityDiv);
      deleteButtonDiv.appendChild(deleteButton);

      // Append the new div to the container
      itemContainer.appendChild(newItemDiv);
      newItemDiv.appendChild(deleteButtonDiv);

      // Hide "Add More Items" button if the limit is reached
      if (itemCount === maxInputs) {
        addItemButton.style.display = 'none';
      }
    }
  }

  // Attach event listener to the "Add More Items" button
  addItemButton.addEventListener('click', addItem);


  const customerInfo = async (id) => {
    const data = await fetch(`./backend/booking-details_action.php?customer-info=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('display-id-info').innerText = response.id;
    document.getElementById('display-full-name-info').innerText = response.fname + ' ' + response.lname;
    document.getElementById('display-phone-number-info').innerText = response.phone_number;
  }

  const updateKiloForm = document.getElementById('upload-kilo-form');
  const updateKiloBtn = document.getElementById('update-kilo-button');

  // Handle the input validation for update kilo form
  updateKiloForm.addEventListener('input', (e) => {
    const target = e.target;
    const feedback = target.nextElementSibling;

    if (target.tagName === 'INPUT') {
      if (target.checkValidity()) {
        target.classList.remove('border-red-500');
        target.classList.add('border-green-700');
        feedback.classList.add('hidden');
      } else {
        target.classList.remove('border-green-700');
        target.classList.add('border-red-500');
        feedback.classList.remove('hidden');
      }
    }
  });

  // Handle form submission
  updateKiloForm.addEventListener('submit', async (e) => {
    e.preventDefault(); // Prevent the form from submitting

    const bookingId = document.getElementById('display-id-info').innerText;
    const formData = new FormData(updateKiloForm);
    formData.append('updatekilo', 1);
    formData.append('bookingId', bookingId);

    // Form validation
    if (updateKiloForm.checkValidity() === false) {
      e.stopPropagation();

      // Add validation error handling
      [...updateKiloForm.elements].forEach((input) => {
        const feedback = input.nextElementSibling;

        if (input.tagName === 'INPUT' && (input.type === 'text' || input.type === 'number')) {
          if (!input.checkValidity()) {
            input.classList.add('border-red-500');
            feedback.classList.remove('hidden');
          } else {
            input.classList.remove('border-red-500');
            feedback.classList.add('hidden');
          }
        }
      });
      // Show error modal if validation fails
      const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
      errorWarningModal.show();
      return false; // Stop form submission

    } else {
      // If validation passes
      updateKiloBtn.value = 'Please Wait...';

      const data = await fetch('./backend/booking-details_action.php', {
        method: 'POST',
        body: formData,
      });

      const response = await data.json();
      console.log(response);
      if (response.status === 'success') {
        updateKiloBtn.value = 'Submit';
        const green600 = '#047857';
        const green700 = '#065f46';
        showToaster('Inventory updated successfully!', 'check', green600, green700);
        updateKiloForm.reset();
        document.querySelector('.toUpdateKiloModal').classList.add('hidden');

        // Remove validation classes after reset
        [...updateKiloForm.elements].forEach((input) => {
          if (input.tagName === 'INPUT') {
            input.classList.remove('border-green-700', 'border-red-500');
          }
        });

      } else if (response.status = 'out of stock') {
        const red600 = '#dc2626';
        const red700 = '#b91c1c';
        showToaster(`${response.message}`, 'exclamation-error', red600, red700);
        updateKiloBtn.value = 'Submit';
        updateKiloForm.reset();
        document.querySelector('.toUpdateKiloModal').classList.add('hidden');

        [...updateKiloForm.elements].forEach((input) => {
          if (input.tagName === 'INPUT') {
            input.classList.remove('border-green-700', 'border-red-500');
          }
        });

      } else if (response.status = 'insufficient') {
        const blue600 = '#0E4483';
        const blue700 = '#60A5FA';
        showToaster(`${response.message}`, 'exclamation-error', blue600, blue700);
        updateKiloBtn.value = 'Submit';
        updateKiloForm.reset();
        document.querySelector('.toUpdateKiloModal').classList.add('hidden');

        [...updateKiloForm.elements].forEach((input) => {
          if (input.tagName === 'INPUT') {
            input.classList.remove('border-green-700', 'border-red-500');
          }
        });
      } else {
        const red600 = '#dc2626';
        const red700 = '#b91c1c';
        showToaster('Something went wrong !', 'exclamation-error', red600, red700);
        updateKiloBtn.value = 'Submit';
        updateKiloForm.reset();
        document.querySelector('.toUpdateKiloModal').classList.add('hidden');

        [...updateKiloForm.elements].forEach((input) => {
          if (input.tagName === 'INPUT') {
            input.classList.remove('border-green-700', 'border-red-500');
          }
        });
      }

    }
  });


});

