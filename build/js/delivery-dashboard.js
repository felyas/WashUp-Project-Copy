import { handleDisplayCurrentTime, handleSidebar, openModal, showToaster, Modal } from "./dashboards-main.js";

handleSidebar();
handleDisplayCurrentTime();
openModal('viewModalTrigger', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');
openModal('updateKiloTrigger', 'toUpdateKiloModal', 'closeUpdateKiloModal', 'closeUpdateKiloModal2');
openModal('updateProofOfDeliveryTrigger', 'toUpdateDeliveryProofModal', 'closeUpdateDeliveryProofModal', 'closeUpdateDeliveryProofModal2');
openModal('editModalTrigger', 'toEditBookingModal', 'closeEditBookingModal', 'closeEditBookingModal2');
openModal('openCameraModalTrigger', 'toOpenCameraModal', 'closeCameraModal', 'closeCameraModal2');

// Function to set the minimum and maximum dates for the pick-up date input
function setMinDate() {
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
  const dd = String(today.getDate()).padStart(2, '0');

  const currentDate = `${yyyy}-${mm}-${dd}`;
  const lastDayOfYear = `${yyyy}-12-31`; // Last date of the current year

  const dateInput = document.getElementById('pickup-date');

  // Set both min and max attributes
  dateInput.setAttribute('min', currentDate);
  dateInput.setAttribute('max', lastDayOfYear);
}

// Function to generate 20-minute interval time options from 8:00 AM to 9:00 PM, with unavailable times disabled
function populateTimeOptions(unavailableTimes = []) {
  const selectTime = document.getElementById('pickup-time');
  selectTime.innerHTML = ''; // Clear previous options

  const startTime = 8 * 60; // 8:00 AM in minutes
  const endTime = 21 * 60; // 9:00 PM in minutes
  const interval = 20; // 20 minutes

  // Get the current date and time
  const currentDate = new Date();
  const currentDay = currentDate.getDate();
  const currentMonth = currentDate.getMonth(); // Zero-indexed months
  const currentYear = currentDate.getFullYear();
  const currentTimeInMinutes = currentDate.getHours() * 60 + currentDate.getMinutes(); // Current time in minutes

  // Get the selected date from the date input
  const selectedDate = new Date(document.getElementById('pickup-date').value);
  const isToday = selectedDate.getDate() === currentDay &&
    selectedDate.getMonth() === currentMonth &&
    selectedDate.getFullYear() === currentYear;

  for (let time = startTime; time <= endTime; time += interval) {
    const hours = Math.floor(time / 60);
    const minutes = time % 60;

    const isPM = hours >= 12;
    const displayHours = hours % 12 || 12; // Convert to 12-hour format
    const displayMinutes = minutes.toString().padStart(2, '0');
    const ampm = isPM ? 'PM' : 'AM';

    const timeFormatted = `${displayHours}:${displayMinutes} ${ampm}`;

    const option = document.createElement('option');
    option.value = timeFormatted;
    option.textContent = timeFormatted;

    // Disable the option if it's in the unavailable times array
    if (unavailableTimes.includes(timeFormatted)) {
      option.disabled = true;
      option.textContent += ' (Unavailable)';
    }

    // Only apply past hour restriction if the selected date is today
    if (isToday) {
      const todayTime = new Date(currentYear, currentMonth, currentDay, hours, minutes); // Time being checked
      if (todayTime < currentDate) {
        option.disabled = true;
        option.textContent += ' (Past)';
      }
    }

    selectTime.appendChild(option);
  }
}
// Add an event listener to update the time options when the date is changed
document.getElementById('pickup-date').addEventListener('change', function () {
  populateTimeOptions(); // Call the function to repopulate the time options when the date changes
});





// Handle the input validation from add items
function validateForm(form) {
  form.addEventListener('input', (e) => {
    const target = e.target;
    const feedback = target.nextElementSibling;

    // Ensure validation only applies to 'file' and 'number' input types
    if (target.tagName === 'INPUT' && (target.type === 'file' || target.type === 'number')) {
      if (target.checkValidity()) {
        target.classList.remove('border-red-500');
        target.classList.add('border-green-700'); // Change border to green if valid
        feedback.classList.add('hidden'); // Hide feedback on valid input
      } else {
        target.classList.remove('border-green-700');
        target.classList.add('border-red-500'); // Change border to red if invalid
        feedback.classList.remove('hidden'); // Show feedback on invalid input
      }
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {

  const tbody = document.getElementById('users-booking-list');
  const paginationContainer = document.getElementById('pagination-container');
  const searchInput = document.getElementById('js-search-bar');
  const statusFilter = document.getElementById('status-filter');

  setMinDate();
  populateTimeOptions();

  document.getElementById('pickup-date').addEventListener('change', async function () {
    const selectedDate = this.value; // Get the selected date
    if (selectedDate) {
      const response = await fetch(`./backend/customer_action.php?get_unavailable_times=1&date=${selectedDate}`);
      const unavailableTimes = await response.json(); // Fetch unavailable times from the backend
      populateTimeOptions(unavailableTimes); // Populate time options with unavailable times disabled
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

  // Fetch All items with pagination, search, sorting, and filtering
  const dateFilter = document.getElementById('date-filter');
  let currentDateFilter = '';

  const fetchAll = async (page = 1, column = 'id', order = 'desc', query = '') => {
    const searchQuery = searchInput.value.trim() || query;
    const statusQuery = status || statusFilter.value; // Get status from dropdown or passed value
    const dateQuery = currentDateFilter;

    const data = await fetch(`./backend/delivery_action.php?readAll=1&page=${page}&column=${column}&order=${order}&query=${searchQuery}&status=${statusQuery}&date=${dateQuery}`, {
      method: 'GET',
    });
    const response = await data.json();
    tbody.innerHTML = response.bookings;
    paginationContainer.innerHTML = response.pagination;

    // Populate date filter options
    if (response.dates) {
      populateDateFilter(response.dates);
    }
  }

  const populateDateFilter = (dates) => {
    let options = '<option value="">Date: All</option>';
    dates.forEach(date => {
      const selected = date === currentDateFilter ? 'selected' : '';
      options += `<option value="${date}" ${selected}>${formatDate(date)}</option>`;
    });
    dateFilter.innerHTML = options;
  }

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
  }

  dateFilter.addEventListener('change', () => {
    currentDateFilter = dateFilter.value;
    fetchAll(1); // Reset to first page when changing filter
  });

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
  fetchAll();

  //Targeting anchor tags from tbody
  const tbodyList = document.querySelectorAll('tbody');
  tbodyList.forEach(tbody => {
    tbody.addEventListener('click', (e) => {
      // Target View Link
      if (e.target && (e.target.matches('a.viewLink') || e.target.closest('a.viewLink'))) {
        e.preventDefault();
        let targetElement = e.target.matches('a.viewLink') ? e.target : e.target.closest('a.viewLink');
        let id = targetElement.getAttribute('id');
        viewSummary(id);
      }

      // Target Pickup Link
      if (e.target && (e.target.matches('a.pickupLink') || e.target.closest('a.pickupLink'))) {
        e.preventDefault();
        let targetElement = e.target.matches('a.pickupLink') ? e.target : e.target.closest('a.pickupLink');
        let id = targetElement.getAttribute('id');
        viewInfoForKiloUpdate(id);
      }

      // Target DeliveryLink
      if (e.target && (e.target.matches('a.deliveryLink') || e.target.closest('a.deliveryLink'))) {
        e.preventDefault();
        let targetElement = e.target.matches('a.deliveryLink') ? e.target : e.target.closest('a.deliveryLink');
        let id = targetElement.getAttribute('id');
        viewInfoForProofAndReceipt(id);
      }

      // Target AdmitLink
      if (e.target && (e.target.matches('a.admitLink') || e.target.closest('a.admitLink'))) {
        e.preventDefault();
        let targetElement = e.target.matches('a.admitLink') ? e.target : e.target.closest('a.admitLink');
        let id = targetElement.getAttribute('id');
        const admitWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal', 'modal-message')
        admitWarningModal.show(admitBooking, id, 'Do you really want to admit this booking?');
      }

      // Target DeniedLink
      if (e.target && (e.target.matches('a.deniedLink') || e.target.closest('a.deniedLink'))) {
        e.preventDefault();
        let targetElement = e.target.matches('a.deniedLink') ? e.target : e.target.closest('a.deniedLink');
        let id = targetElement.getAttribute('id');
        const deniedWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal', 'modal-message');
        deniedWarningModal.show(deniedBooking, id, 'Do you really want to denied this booking?');
      }

      // Target EditLink
      if (e.target && (e.target.matches('a.editLink') || e.target.closest('a.editLink'))) {
        e.preventDefault();
        let targetElement = e.target.matches('a.editLink') ? e.target : e.target.closest('a.editLink');
        let id = targetElement.getAttribute('id');
        viewEditInfo(id);
      }


    });
  })

  // View edit info Ajax Request
  const viewEditInfo = async (id) => {
    const data = await fetch(`./backend/delivery_action.php?edit-info=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();

    document.getElementById('display-id-editInfo').innerText = response.id;
    document.getElementById('display-full-name-editInfo').innerText = response.fname + ' ' + response.lname;
    document.getElementById('display-phone-number-editInfo').innerText = response.phone_number;
    document.getElementById('display-pickup-time-editInfo').innerText = response.pickup_time;
    document.getElementById('display-pickup-date-editInfo').innerText = response.pickup_date;

  }


  const editForm = document.getElementById('edit-booking-form');

  editForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(editForm);
    formData.append('update', 1);
    formData.append('id', document.getElementById('display-id-editInfo').textContent);

    if (editForm.checkValidity() === false) {
      e.stopPropagation();

      // Add validation error handling
      [...editForm.elements].forEach((input) => {
        const feedback = input.nextElementSibling; // Assume error message follows the input

        if (['INPUT', 'SELECT'].includes(input.tagName)) {
          const isInvalid = input.tagName === 'SELECT' ? !input.value : !input.checkValidity();

          if (isInvalid) {
            input.classList.add('border-red-500');
            if (feedback) feedback.classList.remove('hidden');
          } else {
            input.classList.remove('border-red-500');
            if (feedback) feedback.classList.add('hidden');
          }
        }
      });

      const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
      errorWarningModal.show();
      return false;
    } else {
      document.getElementById('edit-booking-btn').value = 'Please Wait...';

      const data = await fetch(`./backend/delivery_action.php`, {
        method: 'POST',
        body: formData,
      });
      const response = await data.text();
      if (response.includes('success')) {
        const successModal = new Modal('success-modal', 'success-confirm-modal', 'success-close-modal');
        successModal.show();
        document.getElementById('edit-booking-btn').value = 'Save';
        editForm.reset();
        fetchAll();
        fetchAllPendingBooking();
        document.querySelector('.toEditBookingModal').classList.add('hidden');
      } else {
        showToaster('Something went wrong !', 'exclamation-error', '#dc2626', '#b91c1c');
      }
    }
  })


  // Admit Ajax Request
  const admitBooking = async (id) => {
    const data = await fetch(`./backend/delivery_action.php?admit=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    if (response.status === 'success') {
      showToaster('Booking admitted successfully !', 'check', '#047857', '#065f46');
      fetchAll();
      fetchAllPendingBooking();
      fetchBookingCounts();
      fetchAllNotifications();
      displayPickupAndDeliveries();
    } else {
      showToaster('Something went wrong!', 'exclamation-error', '#dc2626', '#b91c1c');
    }
  }

  // DENIED BOOKING AJAX REQUEST
  const deniedBooking = async (id) => {
    const data = await fetch(`./backend/delivery_action.php?denied=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    if (response.status === 'success') {
      showToaster(response.message, 'check', '#047857', '#065f46');
      fetchAll();
      fetchAllPendingBooking();
      fetchBookingCounts();
      displayPickupAndDeliveries();
    } else {
      showToaster('Something went wrong!', 'exclamation-error', '#dc2626', '#b91c1c');
    }
  }



  // View Booking Details Ajax Request
  const viewSummary = async (id) => {
    const data = await fetch(`./backend/delivery_action.php?view=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('created_at').innerHTML = response.created_at;
    document.getElementById('display-id').innerHTML = response.id;
    document.getElementById('display-full-name').innerHTML = response.fname + ' ' + response.lname;
    document.getElementById('display-phone-number').innerHTML = response.phone_number;
    document.getElementById('display-address').innerHTML = response.address;
    document.getElementById('display-pickup-date').innerHTML = response.pickup_date;
    document.getElementById('display-pickup-time').innerHTML = response.pickup_time;
    document.getElementById('display-service-selection').innerHTML = response.service_selection;
    document.getElementById('display-service-type').innerHTML = response.service_type;
    document.getElementById('display-suggestions').innerHTML = response.suggestions;
  }

  // View Booking Details for Update Kilo Ajax Request
  const viewInfoForKiloUpdate = async (id) => {
    const data = await fetch(`./backend/delivery_action.php?info-for-kilo-update=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('display-id-forkilo').innerText = response.id;
    document.getElementById('display-full-name-forkilo').innerText = response.fname + ' ' + response.lname;
    document.getElementById('display-phone-number-forkilo').innerText = response.phone_number;
  }

  const updateKiloForm = document.getElementById('upload-kilo-form');
  validateForm(updateKiloForm);

  const closeCameraBtn = document.getElementById('js-close-camera');
  const takePhotoBtn = document.getElementById('js-take-photo');
  const cameraModal = document.getElementById('camera-modal');
  const videoElement = document.getElementById('js-camera'); // Video element in your modal
  let targetInputId = ''; // Store target input ID
  let stream = null; // Store the camera stream

  // Listen for clicks on any button that opens the camera
  document.querySelectorAll('.openCameraModalTrigger').forEach(button => {
    button.addEventListener('click', async (event) => {
      try {
        // Set the target input ID from the data-target attribute
        targetInputId = event.currentTarget.getAttribute('data-target');

        cameraModal.classList.remove('hidden');

        // Access the back camera
        stream = await navigator.mediaDevices.getUserMedia({
          video: {
            facingMode: { exact: "environment" }
          }
        });

        // Attach the stream to the video element
        videoElement.srcObject = stream;
      } catch (error) {
        try {
          cameraModal.classList.remove('hidden');

          const stream = await navigator.mediaDevices.getUserMedia({
            video: true
          });
          videoElement.srcObject = stream;
        } catch (fallbackError) {
          console.error("Error accessing the camera:", fallbackError.message);
          alert("Camera access denied or not available.");
        }
      }
    });
  });

  // Capture the image and assign it to the specified input
  takePhotoBtn.addEventListener('click', () => {
    // GETTING THE CORRECT CANVAS TO DISPLAY IMAGE
    const imagePreview = document.getElementById(`image-preview-${targetInputId}`)

    imagePreview.classList.remove('hidden');

    if (imagePreview) {
      // Get the canvas context
      const context = imagePreview.getContext('2d');

      // Match the canvas size to the video dimensions
      imagePreview.classList.add('w-auto');
      imagePreview.classList.add('h-auto');

      // Draw the video frame onto the imagePreview
      context.drawImage(videoElement, 0, 0, imagePreview.width, imagePreview.height);

      // Convert canvas to a Data URI (Base64)
      const dataUri = imagePreview.toDataURL('image/jpeg');

      // Convert Data URI to a File object
      const capturedImageFile = dataURItoFile(dataUri);

      // Assign the file to the specified input field
      const fileInput = document.getElementById(targetInputId);
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(capturedImageFile);
      fileInput.files = dataTransfer.files;
      console.log(fileInput.files);

      // Reset the camera and close the modal
      closeCamera();
    }
  });

  // Close the camera and stop the stream
  closeCameraBtn.addEventListener('click', closeCamera);

  function closeCamera() {
    if (stream) {
      stream.getTracks().forEach(track => track.stop()); // Stop all tracks
      stream = null;
    }
    cameraModal.classList.add('hidden');
    videoElement.srcObject = null; // Clear the video stream
  }

  // Helper function to convert Data URI to a File object with a unique filename
  function dataURItoFile(dataUri) {
    const arr = dataUri.split(',');
    const mime = arr[0].match(/:(.*?);/)[1];
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);

    while (n--) {
      u8arr[n] = bstr.charCodeAt(n);
    }

    // Generate a unique filename with a timestamp
    const filename = `captured-image-${Date.now()}.jpg`;

    return new File([u8arr], filename, { type: mime });
  }



  const warningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal');
  const updateKiloInp = document.querySelector('.update-kilo');
  // Add Kilo and Proof of Kilo Ajax Request
  updateKiloForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Form Validation
    let isValid = true;

    const formData = new FormData(updateKiloForm);
    formData.append('add-kilo', 1); // To differentiate request in backend
    formData.append('booking_id', document.getElementById('display-id-forkilo').textContent); // Append booking ID dynamically

    [...updateKiloForm.elements].forEach((input) => {
      const feedback = input.nextElementSibling;

      if (input.tagName === 'INPUT' && input.type === 'number') {
        const value = parseInt(input.value, 10); // Convert value to integer

        // Check range and validity
        if (!input.checkValidity() || value < 3 || value > 22) {
          isValid = false;
          input.classList.add('border-red-500');
          if (feedback) feedback.classList.remove('hidden');
        } else {
          input.classList.remove('border-red-500');
          if (feedback) feedback.classList.add('hidden');
        }
      } else if (input.tagName === 'INPUT' && input.type === 'file') {
        // Handle file validation (if needed)
        if (!input.checkValidity()) {
          isValid = false;
          input.classList.add('border-red-500');
          if (feedback) feedback.classList.remove('hidden');
        } else {
          input.classList.remove('border-red-500');
          if (feedback) feedback.classList.add('hidden');
        }
      }
    });

    if (!isValid) {
      const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
      errorWarningModal.showWithoutMessage();
      return false;
    } else {
      // Show confirmation modal before proceeding with the submission
      warningModal.show(async () => {
        // This code runs when the user confirms the action in the modal
        document.getElementById('update-kilo-button').value = 'Please Wait...';

        const data = await fetch('./backend/delivery_action.php', {
          method: 'POST',
          body: formData,
        });

        const response = await data.json(); // Assuming the response is JSON

        // Handle success or error response
        if (response.status === 'success') {
          showToaster('Kilo and status updated successfully!', 'check', '#047857', '#065f46');
          updateKiloForm.reset();
          document.querySelector('.toUpdateKiloModal').classList.add('hidden');
          document.getElementById('update-kilo-button').value = 'Submit';
          fetchAll();
          fetchBookingCounts();
          displayPickupAndDeliveries();

          // Remove validation classes after reset
          [...updateKiloForm.elements].forEach((input) => {
            if (input.tagName === 'INPUT') {
              input.classList.remove('border-green-700', 'border-red-500');
            }
          });
        } else {
          showToaster('Something went wrong!', 'exclamation-error', '#dc2626', '#b91c1c');
        }
      }, document.getElementById('display-id-forkilo').textContent); // Pass the booking ID if needed
    }
  });

  const viewInfoForProofAndReceipt = async (id) => {
    const data = await fetch(`./backend/delivery_action.php?info-for-proof-receipt=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('display-id-forProof').innerText = response.id;
    document.getElementById('display-full-name-forProof').innerText = response.fname + ' ' + response.lname;
    document.getElementById('display-phone-number-forProof').innerText = response.phone_number;
  }

  const ProofAndReceiptForm = document.getElementById('upload-proofAndReceipt-form');
  validateForm(ProofAndReceiptForm);

  ProofAndReceiptForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(ProofAndReceiptForm);
    formData.append('add-receipt', 1); // To differentiate request in backend
    formData.append('booking_id', document.getElementById('display-id-forProof').textContent); // Append booking ID dynamically

    // Form Validation
    if (ProofAndReceiptForm.checkValidity() === false) {
      e.stopPropagation();

      [...ProofAndReceiptForm.elements].forEach((input) => {
        const feedback = input.nextElementSibling;

        if (input.tagName === 'INPUT' && (input.type === 'file')) {
          if (!input.checkValidity()) {
            input.classList.add('border-red-500');
            feedback.classList.remove('hidden');
          } else {
            input.classList.remove('border-red-500');
            feedback.classList.add('hidden');
          }
        }
      });

      const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
      errorWarningModal.show();
      return false;
    } else {
      // Show confirmation modal before proceeding with the submission
      warningModal.show(async () => {
        // This code runs when the user confirms the action in the modal
        document.getElementById('update-delivery-proof-button').value = 'Please Wait...';

        const data = await fetch('./backend/delivery_action.php', {
          method: 'POST',
          body: formData,
        });

        const response = await data.json(); // Assuming the response is JSON

        // Handle success or error response
        if (response.status === 'success') {
          showToaster('Proof of delivery, receipt and status updated successfully!', 'check', '#047857', '#065f46');
          ProofAndReceiptForm.reset();
          document.querySelector('.toUpdateDeliveryProofModal').classList.add('hidden');
          document.getElementById('update-delivery-proof-button').value = 'Submit';
          fetchAll();
          fetchBookingCounts();

          // Remove validation classes after reset
          [...ProofAndReceiptForm.elements].forEach((input) => {
            if (input.tagName === 'INPUT') {
              input.classList.remove('border-green-700', 'border-red-500');
            }
          });
        } else {
          showToaster('Something went wrong!', 'exclamation-error', '#dc2626', '#b91c1c');
        }
      }, document.getElementById('display-id-forProof').textContent); // Pass the booking ID if needed
    }
  });

  // Fetch the total number of bookings for each status: "for pick-up", "for delivery", and "complete"
  const fetchBookingCounts = async () => {
    try {
      const data = await fetch('./backend/delivery_action.php?count_all=1', {
        method: 'GET',
      });
      const response = await data.json(); // Assuming the response is in JSON format

      // Update the HTML elements with the counts
      document.getElementById('js-pending-count').textContent = response.pendingCount; // Complete count
      document.getElementById('js-for-pickup').textContent = response.pickupCount; // For Pick-Up count
      document.getElementById('js-for-delivery').textContent = response.deliveryCount; // For Delivery count
    } catch (error) {
      console.error('Error fetching booking counts:', error);
    }
  };
  fetchBookingCounts();

  // Calendar Section
  const fetchEvents = async () => {
    const response = await fetch('./backend/delivery_action.php?fetch_events=1', {
      method: 'GET',
    });
    const events = await response.json();
    return events.map(event => ({
      id: event.event_id,
      title: event.event_name,
      start: event.event_start_date,
      end: event.event_end_date
    }));
  };
  const calendarEl = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'listWeek',
    height: '100%',
    events: fetchEvents,
    buttonText: {
      today: 'Today',
      listWeek: 'Week List',
      listDay: 'Day List'
    }, headerToolbar: {
      start: 'title',  // Title will be on the left
      center: '',      // Center will be empty
      end: 'today,prev,next' // Today, Prev, and Next buttons on the right
    },
    views: {
      listWeek: {                    // Week view
        titleFormat: {              // Format for week view
          year: 'numeric',
          month: 'short',
          day: 'numeric'          // e.g., 'Sep 13 2009'
        }
      }
    },
    // Add eventRender function to style the title
    eventDidMount: function (info) {
      // Select the title element and apply inline styles
      const titleElement = document.querySelector('.fc-toolbar-title');
      if (titleElement) {
        titleElement.style.fontSize = '1rem'; // Adjust the font size as needed
        titleElement.style.fontWeight = 'bold'; // Adjust weight if desired
      }
    },
  });
  calendar.render();

  // Notification Ajax Request
  // Long polling function for fetching delivery-related notifications
  // Combined notification fetching function
  const fetchAllNotifications = async () => {
    try {
      // Fetch both types of notifications in parallel
      const [deliveryResponse, bookingResponse] = await Promise.all([
        fetch(`./backend/delivery_action.php?fetch_new_deliveries=1`),
        fetch(`./backend/admin_action.php?fetch_new_bookings_delivery=1`)
      ]);

      const deliveryNotifications = await deliveryResponse.json();
      const bookingNotifications = await bookingResponse.json();

      const notificationContainer = document.querySelector('.js-notification-messages');
      const notificationDot = document.querySelector('.js-notification-dot');
      const totalNotificationsElement = document.querySelector('.js-total-notifications');

      // Combine notifications and add a type identifier
      const combinedNotifications = [
        ...deliveryNotifications.map(n => ({ ...n, type: 'delivery' })),
        ...bookingNotifications.map(n => ({ ...n, type: 'booking' }))
      ].sort((a, b) => b.id - a.id); // Sort by ID descending

      if (combinedNotifications.length > 0) {
        // Clear existing messages
        notificationContainer.innerHTML = '';

        // Append each notification to the container
        combinedNotifications.forEach(notification => {
          const notificationElement = document.createElement('div');
          notificationElement.classList.add('flex', 'items-center', 'justify-between', 'bg-gray-200', 'mb-1');

          // Different message content based on notification type
          const message = notification.type === 'delivery'
            ? `Delivery status update for (ID: ${notification.id}) - ${notification.status}`
            : `New booking request received (ID: ${notification.id})`;

          notificationElement.innerHTML = `
          <div class="flex items-center p-4 bg-blue-100 border border-blue-200 rounded-lg shadow-md">
            <img src="./img/about-bg1.png" alt="Notification Image" class="w-12 h-12 mr-4 rounded-full">
            <div class="flex-1">
              <p class="text-sm">
                ${message}
              </p>
            </div>
            <button class="w-12 p-0 border-none font-bold js-notification-close" 
                    data-id="${notification.id}" 
                    data-type="${notification.type}">
              &#10005;
            </button>
          </div>
        `;
          notificationContainer.appendChild(notificationElement);
        });

        // Update total notification count
        totalNotificationsElement.textContent = combinedNotifications.length;
        notificationDot.classList.remove('hidden');
        fetchAll();
        fetchAllPendingBooking();
      } else {
        notificationDot.classList.add('hidden');
      }

      // Continue polling
      setTimeout(fetchAllNotifications, 10000);
    } catch (error) {
      console.error('Error fetching notifications:', error);
      // Retry after error
      setTimeout(fetchAllNotifications, 10000);
    }
  };

  // Handle notification close clicks
  document.addEventListener('click', async (e) => {
    if (e.target.classList.contains('js-notification-close')) {
      const id = e.target.dataset.id;
      const type = e.target.dataset.type;

      try {
        // Send request to mark notification as read based on type
        const endpoint = type === 'delivery'
          ? './backend/delivery_action.php'
          : './backend/admin_action.php';

        await fetch(`${endpoint}?mark_as_read=1&id=${id}`);

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
      } catch (error) {
        console.error('Error marking notification as read:', error);
      }
    }
  });

  // Start the combined polling
  fetchAllNotifications();

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

  // Function to handle file input change and image preview
  function handleImagePreview(fileInputId, imagePreviewId) {
    const fileInput = document.getElementById(fileInputId);
    const imagePreview = document.getElementById(imagePreviewId);

    // Add an event listener for when the user selects an image
    // fileInput.addEventListener('change', function (event) {
    //   const file = event.target.files[0]; // Get the selected file
    //   if (file) {
    //     // Create a FileReader to read the file
    //     const reader = new FileReader();

    //     // When the file is read, update the image preview
    //     reader.onload = function (e) {
    //       imagePreview.src = e.target.result; // Set the image source to the file's data URL
    //       imagePreview.classList.remove('hidden'); // Unhide the image preview
    //     };

    //     // Read the selected image file as a data URL
    //     reader.readAsDataURL(file);
    //   } else {
    //     // If no file is selected, hide the image preview
    //     imagePreview.src = '';
    //     imagePreview.classList.add('hidden');
    //   }
    // });
  }

  // Call the function for each file input and its respective image preview
  handleImagePreview('file-upload', 'image-preview');  // For the main image upload
  handleImagePreview('file-proof-upload', 'image-preview-delivery-proof');  // For proof of delivery
  handleImagePreview('file-receipt-upload', 'image-preview-receipt');  // For receipt


  const pendingTbody = document.getElementById('js-pending-tbody');
  const paginationContainerPending = document.getElementById('pagination-container-pending');
  // Fetch Pending Ajax Request with pagination and search
  const fetchAllPendingBooking = async (page = 1) => {
    const searchQuery = document.getElementById('js-search-pending').value.trim();
    const data = await fetch(`./backend/delivery_action.php?read-pending=1&page=${page}&query=${searchQuery}`, {
      method: 'GET',
    });
    const response = await data.json();
    pendingTbody.innerHTML = response.bookings;
    paginationContainerPending.innerHTML = response.pagination; // Pagination displayed here
  }

  // Search Input Event Listener
  document.getElementById('js-search-pending').addEventListener('input', () => {
    fetchAllPendingBooking();
  });

  //Pagination Links Event Delegation
  paginationContainerPending.addEventListener('click', (e) => {
    if (e.target.classList.contains('pagination-link')) {
      e.preventDefault();
      const page = e.target.getAttribute('data-page');
      fetchAllPendingBooking(page); // Fetch data for the clicked page
    }
  });

  //Initial Fetch
  fetchAllPendingBooking();

  // Listen for changes on the dropdown and call displayPickupAndDeliveries with selected time
  document.querySelector("select[name='time']").addEventListener("change", (event) => {
    const selectedTime = parseInt(event.target.value) || 30; // default to 30 minutes if none selected
    displayPickupAndDeliveries(selectedTime);
  });

  const displayPickupAndDeliveries = async (timeInterval = 30) => {
    const data = await fetch(`./backend/delivery_action.php?pickup-and-deliveries=1&time_interval=${timeInterval}`, {
      method: 'GET',
    });
    const response = await data.text();
    const datetimeElm = document.getElementById('delivery-displays');
    datetimeElm.innerHTML = response;
  };
  displayPickupAndDeliveries(); // initial call with default 30 minutes



});
