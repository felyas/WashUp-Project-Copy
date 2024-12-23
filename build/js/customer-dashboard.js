import { handleDisplayCurrentTime, handleSidebar, openModal, showToaster, Modal } from "./dashboards-main.js";

const bookNowBtn = document.querySelector('.js-book-now');
const editBookingForm = document.getElementById('edit-booking-form');
const userAccountBtn = document.getElementById('js-account-setting');

bookNowBtn.addEventListener('click', () => {
  window.location.href = './booking.php';
})

userAccountBtn.addEventListener('click', () => {
  window.location.href = './user-setting.php';
})

// TO OPEN AND CLOSE THE SETTING
const settingBtn = document.getElementById('js-setting-button');
const settingDiv = document.getElementById('js-setting');

settingBtn.addEventListener('click', () => {
  settingDiv.classList.toggle('hidden');
});

// Close the settingDiv when clicking outside of it
document.addEventListener('click', (event) => {
  if (!settingDiv.contains(event.target) && !settingBtn.contains(event.target)) {
    settingDiv.classList.add('hidden');
  }
});



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
  handleSidebar();
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
          // Send confirmation request
          const data = await fetch(`./backend/customer_action.php?confirmYes=1&id=${bookingId}`, {
            method: 'GET',
          });
          const response = await data.text();

          // Check if confirmation was successful
          if (response.includes('success')) {
            // Hide the modal and refresh bookings
            isReceiveModal.classList.add('hidden');
            fetchAllBookings();

            // Show success toaster
            showToaster('Booking is completed, thank you so much!', 'check', '#047857', '#065f46');

            const feedbackForm = document.getElementById('feedback-form');
            const submitFeedbackBtn = document.getElementById('submit-review');

            feedbackForm.addEventListener('submit', async (e) => {
              e.preventDefault();

              // Prepare form data for feedback
              const formData = new FormData(feedbackForm);
              formData.append('new-feedback', 1);
              formData.append('booking_id', bookingId);
              submitFeedbackBtn.value = 'Please wait...';

              // Submit feedback via AJAX request
              const feedbackResponse = await fetch('./backend/customer_action.php', {
                method: 'POST',
                body: formData,
              });
              const feedbackResult = await feedbackResponse.json();

              // Handle feedback submission result
              if (feedbackResult.status === 'success') {
                submitFeedbackBtn.value = 'Submit';
                showToaster(feedbackResult.message, 'check', '#047857', '#065f46');
                feedbackForm.reset();
                document.querySelector('.toViewFeedbackModal').classList.add('hidden');
              } else {
                showToaster('Something went wrong!', 'exclamation-error', '#dc2626', '#b91c1c');
              }
            });
          }
        });


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

    // Archive Booking Ajax Request
    if (e.target && (e.target.matches('a.archiveLink') || e.target.closest('a.archiveLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.archiveLink') ? e.target : e.target.closest('a.archiveLink');
      let id = targetElement.getAttribute('id');
      let origin = targetElement.getAttribute('data-origin');
      let key = targetElement.getAttribute('data-key');
      let value = targetElement.getAttribute('data-value');
      const deleteWarningModal = new Modal('delete-modal', 'delete-confirm-modal', 'delete-close-modal', 'modal-message');
      deleteWarningModal.show(archiveBooking, {id, origin, key, value}, 'Do you want to cancel the booking?');
      // archiveBooking(id, origin, key, value);
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


  validateForm(editBookingForm);
  // Function to validate a phone number in the format 09691026692
  const isPhoneNumberValid = (phoneNumber) => {
    // Check if the phone number starts with 09 and has exactly 11 digits
    const phoneRegex = /^09\d{9}$/;
    return phoneRegex.test(phoneNumber);
  };

  // Update Booking Ajax Request
  editBookingForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(editBookingForm);
    formData.append('update', 1);

    let hasError = false;

    // Validate Phone Number
    const phoneInput = document.getElementById('phone_number');
    const phoneFeedback = phoneInput?.nextElementSibling; // Feedback element for phone number
    if (phoneInput && !isPhoneNumberValid(phoneInput.value)) {
      phoneInput.classList.add('border-red-500');
      phoneFeedback?.classList.remove('hidden');
      phoneFeedback.textContent = 'Phone number must be a valid 11-digit number.';
      hasError = true;
    } else {
      phoneInput?.classList.remove('border-red-500');
      phoneFeedback?.classList.add('hidden');
    }

    // Validate First Name and Last Name
    const fnameInput = document.getElementById('fname');
    const lnameInput = document.getElementById('lname');
    const fnameFeedback = fnameInput.nextElementSibling;
    const lnameFeedback = lnameInput.nextElementSibling;

    const nameRegex = /^[a-zA-Z\s]+$/;

    if (!nameRegex.test(fnameInput.value)) {
      fnameInput.classList.add('border-red-500');
      fnameFeedback.classList.remove('hidden');
      fnameFeedback.textContent = 'First name must contain only letters.';
      hasError = true;
    } else {
      fnameInput.classList.remove('border-red-500');
      fnameFeedback.classList.add('hidden');
    }

    if (!nameRegex.test(lnameInput.value)) {
      lnameInput.classList.add('border-red-500');
      lnameFeedback.classList.remove('hidden');
      lnameFeedback.textContent = 'Last name must contain only letters.';
      hasError = true;
    } else {
      lnameInput.classList.remove('border-red-500');
      lnameFeedback.classList.add('hidden');
    }

    // Validate all inputs
    if (!editBookingForm.checkValidity()) {
      [...editBookingForm.elements].forEach((input) => {
        if (
          input.tagName === 'INPUT' &&
          (input.type === 'text' || input.type === 'number')
        ) {
          const feedback = input.nextElementSibling;

          if (!input.value.trim()) {
            input.classList.add('border-red-500');
            if (feedback && feedback.classList.contains('text-red-500')) {
              feedback.classList.remove('hidden');
              feedback.textContent = 'This field is required.';
            }
            hasError = true;
          } else {
            input.classList.remove('border-red-500');
            if (feedback && feedback.classList.contains('text-red-500')) {
              feedback.classList.add('hidden');
            }
          }
        }
      });

      document.querySelector('#top').scrollIntoView({ behavior: 'smooth' });
    }

    // Show modal if there are errors
    if (hasError) {
      const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
      errorWarningModal.showWithoutMessage();
      return;
    } else {
      document.getElementById('edit-booking-btn').value = 'Please Wait...';

      const data = await fetch('./backend/customer_action.php', {
        method: 'POST',
        body: formData,
      });
      const response = await data.text();
      if (response.includes('success')) {
        const successModal = new Modal('success-modal', 'success-confirm-modal', 'success-close-modal');
        successModal.showWithoutMessage();
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
  const archiveBooking = async (dataset) => {
    const {id, origin, key, value} = dataset;
    const data = await fetch(`./backend/user-archive_action.php?archive=1&id=${id}&origin_table=${origin}&key=${key}&value=${value}`, {
      method: 'GET',
    });

    const response = await data.json();

    if (response.status === 'success') {
      showToaster(response.message, 'archive', '#047857', '#065f46');
      fetchAllBookings();
      fetchBookingCounts();
    } else {
      showToaster(response.message, 'exclamation-error', '#dc2626', '#b91c1c');
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

  let timeoutId = null; // Variable to store the timeout ID

  // Long polling function for fetching notifications
  const fetchNotifications = async (lastCheckTime) => {
    try {
      const response = await fetch(`./backend/customer_action.php?fetch_notifications=1&last_check=${lastCheckTime}`);
      const notifications = await response.json();

      const notificationContainer = document.querySelector('.js-notification-messages');
      const notificationDot = document.querySelector('.js-notification-dot');
      const totalNotificationsElement = document.querySelector('.js-total-notifications');

      if (notifications.length > 0) {
        // Clear existing messages
        notificationContainer.innerHTML = '';
        totalNotificationsElement.innerText = '0';

        // Append each notification to the container
        notifications.forEach(notification => {
          const notificationElement = document.createElement('div');
          notificationElement.classList.add('flex', 'items-center', 'justify-between', 'bg-gray-200', 'mb-1');

          // Determine message based on status
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

        // Update other UI components
        fetchAllBookings();
        fetchBookingCounts();
      } else {
        totalNotificationsElement.innerText = '0';
        notificationDot.classList.add('hidden');
      }

      // Clear the previous timeout if it exists
      if (timeoutId) {
        clearTimeout(timeoutId);
      }

      // Set a new timeout and store its ID
      timeoutId = setTimeout(() => {
        const currentTimestamp = new Date().toISOString();
        fetchNotifications(currentTimestamp);
      }, 10000);
    } catch (error) {
      console.error('Error fetching notifications:', error);
    }
  };

  // Handle closing individual notifications
  document.addEventListener('click', async (e) => {
    if (e.target.classList.contains('js-notification-close')) {
      const id = e.target.dataset.id;

      try {
        const response = await fetch(`./backend/customer_action.php?mark_as_read=1&id=${id}`);
        const result = await response.json();

        if (result.success) {
          // Remove the notification element
          e.target.closest('.flex').remove();

          // Update notification count
          const totalElement = document.querySelector('.js-total-notifications');
          const currentTotal = parseInt(totalElement.textContent);
          const newTotal = currentTotal - 1;
          totalElement.textContent = newTotal;

          // Hide dot if no more notifications
          if (newTotal === 0) {
            document.querySelector('.js-notification-dot').classList.add('hidden');
          }

          // Refresh other UI components
          fetchAllBookings();
          fetchBookingCounts();
        }
      } catch (error) {
        console.error('Error marking notification as read:', error);
      }
    }
  });


  // Initial call to start long polling
  let initialTimestamp = new Date().toISOString();
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

    let hasError = false;

    // Validate Phone Number
    const phoneInput = document.getElementById('complaint-phone_number');
    const phoneFeedback = phoneInput?.nextElementSibling; // Feedback element for phone number
    if (phoneInput && !isPhoneNumberValid(phoneInput.value)) {
      phoneInput.classList.add('border-red-500');
      phoneFeedback?.classList.remove('hidden');
      phoneFeedback.textContent = 'Phone number must be a valid 11-digit number.';
      hasError = true;
    } else {
      phoneInput?.classList.remove('border-red-500');
      phoneFeedback?.classList.add('hidden');
    }

    // Validate First Name and Last Name
    const fnameInput = document.getElementById('complaint-fname');
    const lnameInput = document.getElementById('complaint-lname');
    const fnameFeedback = fnameInput.nextElementSibling;
    const lnameFeedback = lnameInput.nextElementSibling;

    const nameRegex = /^[a-zA-Z\s]+$/; // Allows letters and spaces

    if (!nameRegex.test(fnameInput.value)) {
      fnameInput.classList.add('border-red-500');
      fnameFeedback.classList.remove('hidden');
      fnameFeedback.textContent = 'First name must contain only letters.';
      hasError = true;
    } else {
      fnameInput.classList.remove('border-red-500');
      fnameFeedback.classList.add('hidden');
    }

    if (!nameRegex.test(lnameInput.value)) {
      lnameInput.classList.add('border-red-500');
      lnameFeedback.classList.remove('hidden');
      lnameFeedback.textContent = 'Last name must contain only letters.';
      hasError = true;
    } else {
      lnameInput.classList.remove('border-red-500');
      lnameFeedback.classList.add('hidden');
    }

    // Validate all inputs
    if (!customerComplaintForm.checkValidity()) {
      [...customerComplaintForm.elements].forEach((input) => {
        if (
          input.tagName === 'INPUT' &&
          (input.type === 'text' || input.type === 'email' || input.tagName === 'TEXTAREA' || input.type === 'number')
        ) {
          const feedback = input.nextElementSibling;

          if (!input.value.trim()) {
            input.classList.add('border-red-500');
            if (feedback && feedback.classList.contains('text-red-500')) {
              feedback.classList.remove('hidden');
              feedback.textContent = 'This field is required.';
            }
            hasError = true;
          } else {
            input.classList.remove('border-red-500');
            if (feedback && feedback.classList.contains('text-red-500')) {
              feedback.classList.add('hidden');
            }
          }
        }
      });

      document.querySelector('#top').scrollIntoView({ behavior: 'smooth' });
    }

    // Show modal if there are errors
    if (hasError) {
      document.querySelector('#top').scrollIntoView({ behavior: 'smooth' });
      const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
      errorWarningModal.showWithoutMessage();
      return;
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


  // const feedbackForm = document.getElementById('feedback-form');
  // const submitFeedbackBtn = document.getElementById('submit-review');
  // // Submit Feedback Ajax Request
  // feedbackForm.addEventListener('submit', async (e) => {
  //   e.preventDefault();

  //   const formData = new FormData(feedbackForm);
  //   formData.append('new-feedback', 1);
  //   submitFeedbackBtn.value = 'Please wait...';

  //   const data = await fetch('./backend/customer_action.php', {
  //     method: 'POST',
  //     body: formData,
  //   });
  //   const response = await data.json();
  //   if (response.status === 'success') {
  //     submitFeedbackBtn.value = 'Submit';
  //     showToaster(response.message, 'check', '#047857', '#065f46');
  //     feedbackForm.reset();
  //     document.querySelector('.toViewFeedbackModal').classList.add('hidden');
  //   } else {
  //     showToaster('Something went wrong !', 'exclamation-error', '#dc2626', '#b91c1c');
  //   }
  // })

  // Fetch Feedback Ajax Request 
  const fetchFeedback = async () => {
    const data = await fetch(`./backend/customer_action.php?fetch-feedback=1`, {
      method: 'GET',
    })
    const response = await data.text();
    document.getElementById('feedback-container').innerHTML = response;
  }
  fetchFeedback();



});













