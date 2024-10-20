import { handleDisplayCurrentTime, openModal, showToaster, Modal } from "./dashboards-main.js";

const bookNowBtn = document.querySelector('.js-book-now');
const editBookingForm = document.getElementById('edit-booking-form');

bookNowBtn.addEventListener('click', () => {
  window.location.href = './booking.php';
})

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


document.addEventListener("DOMContentLoaded", () => {
  handleDisplayCurrentTime();
  openModal('editModalTrigger', 'toEditBookingModal', 'closeEditBookingModal', 'closeEditBookingModal2');
  openModal('viewModalTrigger', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');
  openModal('reportComplainTrigger', 'toRequestComplianceModal', 'closeRequestComplianceModal', 'closeRequestComplianceModal2');
  openModal('feedbackModalTrigger', 'toViewFeedbackModal', 'closeFeedbackModal', 'closeFeedbackModal2');




  const tbody = document.getElementById('users-booking-list');
  const paginationContainer = document.getElementById('pagination-container');
  const searchInput = document.getElementById('search-input');

  let modalShown = false; // Flag to track if modal has been shown
  // Fetch All Bookings with Pagination and Search
  const fetchAllBookings = async (page = 1) => {
    const searchQuery = searchInput.value.trim();
    const response = await fetch(`./backend/customer_action.php?read=1&page=${page}&search=${searchQuery}`, {
      method: 'GET',
    });
    const data = await response.json();

    tbody.innerHTML = data.rows;
    paginationContainer.innerHTML = data.pagination;

    if (!modalShown) {
      const isReceiveRow = document.querySelector('tr[data-status="delivered"]');
      if (isReceiveRow) {
        const bookingId = isReceiveRow.getAttribute('data-id');

        // console.log(`I think it woks ${bookingId}`);
        const isReceiveModal = document.querySelector('.toConfirmReceiveModal');
        isReceiveModal.classList.remove('hidden');
        document.getElementById('bookingId-text').innerText = bookingId;
        document.getElementById('bookingId-input').value = bookingId;

        const confirmYesBtn = document.getElementById('confirmYes');
        const confirmNoBtn = document.getElementById('confirmNo');

        confirmYesBtn.addEventListener('click', async () => {
          const data = await fetch(`./backend/customer_action.php?confirmYes=1&id=${bookingId}`, {
            method: 'GET',
          });
          const response = await data.text();
          console.log(response);
          if (response.includes('success')) {
            isReceiveModal.classList.add('hidden');
            fetchAllBookings();
            const green600 = '#047857';
            const green700 = '#065f46';
            showToaster('Booking is completed, thank you so much!', 'check', green600, green700);
          }
        })

        confirmNoBtn.addEventListener('click', async () => {
          const data = await fetch(`./backend/customer_action.php?confirmNo=1&id=${bookingId}`, {
            method: 'GET',
          });
          const response = await data.text();
          console.log(response)
          if (response.includes('success')) {
            isReceiveModal.classList.add('hidden');
            fetchAllBookings();
            const blue600 = '#0E4483';
            const blue700 = '#60A5FA';
            showToaster("We're sorry, we'll work on it right away!", 'check', blue600, blue700);
          }
        })

        modalShown = true;
      }
    }
  };

  // Search Input Event Listener
  searchInput.addEventListener('input', () => {
    fetchAllBookings(1); // Fetch data when search input changes
  });

  // Pagination Links Event Delegation
  paginationContainer.addEventListener('click', (event) => {
    if (event.target.classList.contains('page-link')) {
      event.preventDefault();
      const page = event.target.dataset.page;
      fetchAllBookings(page); // Fetch data for the clicked page
    }
  });

  // Initial Fetch
  fetchAllBookings();




  // Edit Booking Ajax Request
  tbody.addEventListener('click', (e) => {
    if (e.target && (e.target.matches('a.editLink') || e.target.closest('a.editLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.editLink') ? e.target : e.target.closest('a.editLink');
      let id = targetElement.getAttribute('id');
      editBooking(id);
    }

    // View Booking Ajax Request
    if (e.target && (e.target.matches('a.viewLink') || e.target.closest('a.viewLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.viewLink') ? e.target : e.target.closest('a.viewLink');
      let id = targetElement.getAttribute('id');
      bookingSummary(id);
    }

    // Delete Booking Ajax Request
    if (e.target && (e.target.matches('a.deleteLink') || e.target.closest('a.deleteLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.deleteLink') ? e.target : e.target.closest('a.deleteLink');
      let id = targetElement.getAttribute('id');
      const deleteWarningModal = new Modal('delete-modal', 'delete-confirm-modal', 'delete-close-modal');
      deleteWarningModal.show(deleteBooking, id);
      // deleteBooking(id);
    }
  });

  const editBooking = async (id) => {
    const data = await fetch(`./backend/customer_action.php?edit=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('id').value = response.id;
    document.getElementById('fname').value = response.fname;
    document.getElementById('lname').value = response.lname;
    document.getElementById('phone_number').value = response.phone_number;
    document.getElementById('address').value = response.address;
  }


  // Update Booking Ajax Request
  editBookingForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(editBookingForm);
    formData.append('update', 1);

    // Form validation
    if (editBookingForm.checkValidity() === false) {
      e.stopPropagation();

      // Add validation error handling
      [...editBookingForm.elements].forEach((input) => {
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

      const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
      errorWarningModal.show();
      return false;
    } else {
      document.getElementById('edit-booking-btn').value = 'Please Wait...';

      const data = await fetch('./backend/customer_action.php', {
        method: 'POST',
        body: formData,
      });
      const response = await data.text();
      if (response.includes('success')) {
        const successModal = new Modal('success-modal', 'success-confirm-modal', 'success-close-modal');
        successModal.show();
        document.getElementById('edit-booking-btn').value = 'Save';
        editBookingForm.reset();
        fetchAllBookings();
        document.querySelector('.toEditBookingModal').classList.add('hidden');
      } else {
        showToaster('Something went wrong !', 'exclamation-error', '#dc2626', '#b91c1c');
      }

    }
  });

  // View Booking Ajax Request
  const bookingSummary = async (id) => {
    const data = await fetch(`./backend/customer_action.php?edit=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('created_at').innerHTML = response.created_at;
    document.getElementById('display-full-name').innerHTML = response.fname + " " + response.lname;
    document.getElementById('display-phone-number').innerHTML = response.phone_number;
    document.getElementById('display-address').innerHTML = response.address;
    document.getElementById('display-pickup-date').innerHTML = response.pickup_date;
    document.getElementById('display-pickup-time').innerHTML = response.pickup_time;
    document.getElementById('display-service-selection').innerHTML = response.service_selection;
    document.getElementById('display-service-type').innerHTML = response.service_type;
    document.getElementById('display-suggestions').innerHTML = response.suggestions;
  }

  // Deleting Booking Ajax Request
  const deleteBooking = async (id) => {
    const data = await fetch(`./backend/customer_action.php?delete=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.text();
    if (response.includes('success')) {
      // Example: Trigger the toaster with hex values
      const green600 = '#047857'; // Hex value for green-600
      const green700 = '#065f46'; // Hex value for green-700
      showToaster('Booking deleted successfully!', 'check', green600, green700);
      fetchAllBookings();
      fetchForPickUpCount();
    } else {
      // Example: Trigger the toaster with hex values
      const red600 = '#dc2626'; // Hex value for green-600
      const red700 = '#b91c1c'; // Hex value for green-700
      showToaster('Something went wrong !', 'exclamation-error', red600, red700);
      fetchAllBookings();
      fetchForPickUpCount();
    }
  }

  // Fetch the total number of bookings for each status: "for pick-up", "for delivery", and "complete"
  const fetchBookingCounts = async () => {
    try {
      const data = await fetch('./backend/customer_action.php?count_all=1', {
        method: 'GET',
      });
      const response = await data.json(); // Assuming the response is in JSON format

      // Update the HTML elements with the counts
      document.getElementById('js-for-pickup').textContent = response.pickupCount; // For Pick-Up count
      document.getElementById('js-for-delivery').textContent = response.deliveryCount; // For Delivery count
      document.getElementById('js-for-complete-booking').textContent = response.completeCount; // Complete count
    } catch (error) {
      console.error('Error fetching booking counts:', error);
    }
  };
  fetchBookingCounts();

  // Long polling function for fetching notifications
  const fetchNotifications = async (lastCheckTime) => {
    try {
      const response = await fetch(`./backend/customer_action.php?fetch_notifications=1&last_check=${lastCheckTime}`);
      const notifications = await response.json();

      const notificationContainer = document.querySelector('.js-notification-messages');
      const notificationDot = document.querySelector('.js-notification-dot'); // Red dot element
      const totalNotificationsElement = document.querySelector('.js-total-notifications'); // Total notifications element

      // If there are new notifications, display them
      if (notifications.length > 0) {
        // Clear existing messages
        totalNotificationsElement.innerText = 0;
        notificationContainer.innerHTML = '';

        // Append each notification to the container
        notifications.forEach(notification => {
          const notificationElement = document.createElement('div');
          notificationElement.classList.add('flex', 'items-center', 'justify-between', 'bg-gray-200', 'mb-1');

          // Check the status and modify the message accordingly
          let message;
          if (notification.status === 'on process') {
            message = `The proof of kilo for your booking (ID: ${notification.id}) has been added. We're now processing your laundry.`;
          } else if (notification.status === 'delivered') {
            message = `Your laundry (Booking ID: ${notification.id}) has been successfully delivered. A receipt and proof of delivery have been sent to you.`;
          } else {
            message = `Booking with ID ${notification.id} updated to <span class="font-semibold text-celestial">${notification.status}</span>`;
          }

          notificationElement.innerHTML = `
          <div class="flex items-center p-4 bg-blue-100 border border-blue-200 rounded-lg shadow-md">
            <img src="./img/about-bg1.png" alt="Notification Image" class="w-12 h-12 mr-4 rounded-full">
            <div class="flex-1">
              <p class="text-sm">${message}</p>
            </div>
            <button class="w-12 p-0 border-none font-bold js-notification-close" data-id="${notification.id}">
              &#10005;
            </button>
          </div>
        `;
          notificationContainer.appendChild(notificationElement);
        });

        // Update total notification count
        totalNotificationsElement.textContent = notifications.length;
        notificationDot.classList.remove('hidden');

        // Calling other function to update the UI
        fetchAllBookings();
        fetchBookingCounts();
      } else {
        notificationDot.classList.add('hidden');
      }

      // Continue long polling after 5 seconds
      setTimeout(() => {
        const currentTimestamp = new Date().toISOString(); // Use current time as last check
        fetchNotifications(currentTimestamp);
      }, 10000); // Check every 10 seconds
    } catch (error) {
      console.error('Error fetching notifications:', error);
    }
  };


  // Initial call to start long polling
  let initialTimestamp = new Date().toISOString(); // Start with the current time
  fetchNotifications(initialTimestamp);

  // Toggle notification dropdown visibility on bell icon click
  document.querySelector('.js-notification-button').addEventListener('click', () => {
    const notificationDropdown = document.querySelector('.js-notification');
    const notificationDot = document.querySelector('.js-notification-dot');

    // Show or hide the notification dropdown
    notificationDropdown.classList.toggle('hidden');

    // Hide the red dot once notifications are viewed
    if (!notificationDropdown.classList.contains('hidden')) {
      notificationDot.classList.add('hidden'); // Hide the red dot
    }
  });

  // Handle closing individual notifications
  document.addEventListener('click', async (e) => {
    if (e.target.classList.contains('js-notification-close')) {
      const notificationId = e.target.getAttribute('data-id');
      e.target.parentElement.remove(); // Remove notification from UI

      // Send a request to the server to mark this notification as read
      const response = await fetch(`./backend/customer_action.php?mark_as_read=1&id=${notificationId}`);
      const data = await response.json();

      if (data.success) {
        console.log('Notification marked as read.');
      } else {
        console.error('Failed to mark notification as read.');
      }
    }
  });

  // Calendar Section
  const fetchEvents = async () => {
    const response = await fetch('./backend/customer_action.php?fetch_events=1', {
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


  const customerComplaintForm = document.getElementById('report-complain-form');
  const addComplaintBtn = document.getElementById('add-complaint-btn');
  validateForm(customerComplaintForm);
  customerComplaintForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(customerComplaintForm);
    formData.append('add-complaint', 1);

    // Form Validation
    if (customerComplaintForm.checkValidity() === false) {
      e.stopPropagation();

      // Add validation error handling
      [...customerComplaintForm.elements].forEach((input) => {
        const feedback = input.nextElementSibling;

        // Validation for text, number, email, and textarea fields
        if ((input.tagName === 'INPUT' && (input.type === 'text' || input.type === 'number' || input.type === 'email')) || input.tagName === 'TEXTAREA') {
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
      addComplaintBtn.value = "Please wait...";
      const data = await fetch('./backend/customer_action.php', {
        method: 'POST',
        body: formData,
      });
      const response = await data.json();

      if (response.status === 'success') {
        addComplaintBtn.value = 'Submit';
        showToaster('Complaint sent successfully!', 'check', '#047857', '#065f46');
        customerComplaintForm.reset();
        document.querySelector('.toRequestComplianceModal').classList.add('hidden');

        // Remove validation classes after reset
        [...customerComplaintForm.elements].forEach((input) => {
          if (input.tagName === 'INPUT' || input.tagName === 'TEXTAREA' || input.tagName === 'SELECT') {
            input.classList.remove('border-green-700', 'border-red-500');
          }
        });
      }
      else {
        showToaster('Something went wrong !', 'exclamation-error', '#dc2626', '#b91c1c');
      }

    }
  })


  const stars = document.querySelectorAll('input[name="rating"]');

  // Function to update star colors
  function updateStarColors() {
    stars.forEach((star, index) => {
      const label = star.nextElementSibling;
      if (star.checked) {
        // If the star is checked, color it and all previous stars yellow
        for (let i = 0; i <= index; i++) {
          stars[i].nextElementSibling.style.color = '#fbbf24'; // yellow
        }
      } else {
        // If the star is not checked, set its label to gray
        label.style.color = '#d1d5db'; // gray
      }
    });
  }

  // Initialize star colors based on the checked state
  updateStarColors();

  // Add event listeners to stars
  stars.forEach((star) => {
    star.addEventListener('change', updateStarColors);
  });


  const feedbackForm = document.getElementById('feedback-form');
  const submitFeedbackBtn = document.getElementById('submit-review');
  // Submit Feedback Ajax Request
  feedbackForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(feedbackForm);
    formData.append('new-feedback', 1);
    submitFeedbackBtn.value = 'Please wait...';

    const data = await fetch('./backend/customer_action.php', {
      method: 'POST',
      body: formData,
    });
    const response = await data.json();
    if(response.status === 'success') {
      submitFeedbackBtn.value = 'Submit';
      showToaster(response.message, 'check', '#047857', '#065f46');
      feedbackForm.reset();
      document.querySelector('.toViewFeedbackModal').classList.add('hidden');
    } else {
      showToaster('Something went wrong !', 'exclamation-error', '#dc2626', '#b91c1c');
    }
  })

  // Fetch Feedback Ajax Request 
  const fetchFeedback = async () => {
    const data = await fetch(`./backend/customer_action.php?fetch-feedback=1`, {
      method: 'GET',
    })
    const response = await data.text();
    // document.getElementById('feedback-container').innerHTML = response;
  }
  fetchFeedback();



});













