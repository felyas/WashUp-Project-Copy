import { handleSidebar, handleDisplayCurrentTime, handleTdColor, openModal, handleDropdown, showToaster, Modal } from "./dashboards-main.js";


document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();
  handleDropdown();
  openModal('viewModalTrigger', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');
  openModal('openGenerateReportModalTrigger', 'toOpenGenerateReportModal', 'closeGenerateReport', 'closeGenerateReport2');


  function setTime() {
    // Get the element
    const todayDateElement = document.querySelector('.js-today-date-report');

    // Get the current date
    const today = new Date();

    // Format the date as MM/DD/YYYY
    const formattedDate = today.toLocaleDateString('en-US', {
      month: '2-digit',
      day: '2-digit',
      year: 'numeric',
    });

    // Set the formatted date in the element
    todayDateElement.textContent = formattedDate;
  }
  setTime();



  const feedbackTbody = document.getElementById('js-pending-tbody');
  const paginationContainer = document.getElementById('pagination-container');
  const searchInput = document.getElementById('js-search-input');
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

  // Fetch All Feedback with pagiunation, search, and sorting
  const fetchAllFeedback = async (page = 1, column = 'id', order = 'desc', query = '') => {
    const searchQuery = searchInput.value.trim() || query;

    const data = await fetch(`./backend/admin_action.php?readAllFeedback=1&page=${page}&column=${column}&order=${order}&query=${searchQuery}`, {
      method: 'GET',
    });

    const response = await data.json();
    feedbackTbody.innerHTML = response.feedback;
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

      fetchAllFeedback(1, column, order);
    });
  });

  // Handle search input
  searchInput.addEventListener('input', () => {
    fetchAllFeedback();
  });

  paginationContainer.addEventListener('click', (e) => {
    if (e.target.classList.contains('pagination-link')) {
      e.preventDefault();
      const page = e.target.getAttribute('data-page');
      fetchAllFeedback(page);
    }
  });

  // Initial fetch
  fetchAllFeedback();

  const tbodyList = document.querySelectorAll('tbody');
  tbodyList.forEach(tbody => {
    // View Booking Ajax Request
    tbody.addEventListener('click', (e) => {

      // Target the feedbackView
      if (e.target && (e.target.matches('a.feedbackView') || e.target.closest('a.feedbackView'))) {
        e.preventDefault();
        let targetElement = e.target.matches('a.feedbackView') ? e.target : e.target.closest('a.feedbackView');
        let id = targetElement.getAttribute('id');
        // console.log(id);
        feedbackSummary(id);
      }

      // Target the toDisplayLink
      if (e.target && (e.target.matches('a.toDisplayLink') || e.target.closest('a.toDisplayLink'))) {
        e.preventDefault();
        let targetElement = e.target.matches('a.toDisplayLink') ? e.target : e.target.closest('a.toDisplayLink');
        let id = targetElement.getAttribute('id');
        const admitWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal');
        admitWarningModal.show(displayFeedback, id);
      }
    });
  });


  // Fetch Feedback Details Ajax Request
  const feedbackSummary = async (id) => {
    const data = await fetch(`./backend/admin_action.php?feedback-view=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();

    // Set other feedback details
    document.getElementById('display-date-feedback').innerText = response.details.created_at;
    document.getElementById('display-user-id-feedback').innerText = response.details.user_id;
    document.getElementById('display-full-name-feedback').innerText = response.details.first_name + ' ' + response.details.last_name;
    document.getElementById('display-description-feedback').innerText = response.details.description;

    // Display stars based on rating
    const ratingContainer = document.getElementById('display-rating-feedback');
    ratingContainer.innerHTML = ''; // Clear any previous stars

    const starCount = response.details.rating; // Assume rating is between 1 and 5
    for (let i = 0; i < starCount; i++) {
      const starImg = document.createElement('img');
      starImg.src = './img/icons/star-rating.svg';
      starImg.alt = 'star';
      starImg.classList.add('w-5', 'h-5');
      ratingContainer.appendChild(starImg);
    }
  };

  // DISPLAY FEEDBACK AJAX REQUEST
  const displayFeedback = async (id) => {
    const data = await fetch(`./backend/admin_action.php?display-feed=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    if (response.status === 'success') {
      showToaster('Feedback display successfully!', 'check', '#047857', '#065f46');
      fetchAllFeedback();
    } else {
      showToaster('Something went wrong!', 'trash', '#dc2626', '#b91c1c');
    }
  }

  // Fetch the Total Number of Booking for Each Summary Card
  const fetchCount = async () => {
    const data = await fetch(`./backend/admin_action.php?count_all=1`, {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('js-pending-count').innerHTML = response.pendingCount;
    document.getElementById('js-for-pickup-count').innerHTML = response.pickupCount;
    document.getElementById('js-for-delivery-count').innerHTML = response.deliveryCount;
    document.getElementById('js-complete-count').innerHTML = response.completeCount;
  }
  fetchCount();

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
                A booking with ID <span class="font-semibold text-celestial">
                  (ID: ${notification.id})
                </span> is ready to be processed.
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

  const ctx = document.getElementById('userPerMonthChart').getContext('2d');

  // Fetch user data per month and render chart
  const fetchUserPerMonthData = async () => {
    const response = await fetch('./backend/admin_action.php?fetchUsersPerMonth=1');
    const data = await response.json();

    // Extract months and total users from the response data
    const months = data.map(item => item.month);
    const totalUsers = data.map(item => item.total_users);

    // Get the context for the canvas
    const ctx = document.getElementById('userPerMonthChart').getContext('2d');

    // Create the chart using Chart.js
    const userPerMonthChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: months, // X-axis labels (months)
        datasets: [{
          label: 'Total Users Per Month',
          data: totalUsers, // Y-axis data (total users)
          backgroundColor: [
            'rgba(2, 132, 199, 0.6)',   // #0284c7
            'rgba(3, 105, 161, 0.6)',   // #0369a1
            'rgba(14, 68, 131, 0.6)',   // #0E4483
            'rgba(59, 125, 163, 0.6)',  // #3b7da3
            'rgba(9, 15, 77, 0.6)',
          ], // Customize colors for each section
          borderWidth: 1
        }]
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom', // Position the labels horizontally at the bottom
            labels: {
              boxWidth: 20, // Control the size of the colored box next to the labels
              padding: 15,  // Add space between the labels
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 5 // Set step size to 5 for the Y-axis
            }
          }
        }
      }
    });
  };


  // Call the function to fetch data and render the chart
  fetchUserPerMonthData();




  const ctx2 = document.getElementById('totalBookingChart').getContext('2d');

  // Fetch booking data for the line chart
  const fetchBookingData = async () => {
    const response = await fetch('./backend/admin_action.php?fetchBookingData=1');
    const data = await response.json();

    // Assuming the response data has properties for month and totals
    const labels = data.map(item => item.month); // e.g., ['January', 'February', 'March']
    const totals = data.map(item => item.total); // e.g., [10, 20, 5]

    // Create the line chart
    const totalBookingChart = new Chart(ctx2, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Total Bookings',
          data: totals,
          backgroundColor: 'rgba(2, 132, 199, 0.6)',
          borderWidth: 1,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Total Bookings per Month'
          }
        }
      }
    });
  };
  fetchBookingData();







  // Calendar Section
  const fetchEvents = async () => {
    const response = await fetch('./backend/admin_action.php?fetch_events=1', {
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

  // Modal initialization
  const deleteWarningModal = new Modal('delete-event-modal', 'deleteEvent-confirm-modal', 'deleteEvent-close-modal');

  // Delete Event Handler (using the modal)
  const handleEventDelete = async (eventId) => {
    // Show modal instead of default confirm dialog
    deleteWarningModal.show(async (id) => {
      const response = await fetch(`./backend/admin_action.php?delete_event=1&event_id=${id}`, {
        method: 'DELETE'
      });
      const result = await response.json();

      if (result.success) {
        // Remove the event from the calendar UI
        const event = calendar.getEventById(id);
        if (event) {
          event.remove();  // Remove from the calendar view
        }

        showToaster('Event deleted successfully!', 'trash', '#dc2626', '#b91c1c');
      } else {
        alert('Failed to delete event.');
      }
    }, eventId);
  };

  const calendarEl = document.getElementById('calendar');
  const addEventForm = document.getElementById('add-event-form');
  const eventStart = document.getElementById('event-start');
  const eventEnd = document.getElementById('event-end');

  // Handle the input validation for Add Event Form
  function validateEventForm(form) {
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
          target.classList.add('border-red-500'); // Change border to red if invalid
          feedback.classList.remove('hidden');
        }
      }
    });
  }
  validateEventForm(addEventForm);

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: '100%',
    events: fetchEvents, // Pass the function reference, not the result
    eventColor: '#3b7da3',
    eventTextColor: '#ffffff',
    buttonText: {
      today: 'Today',
      month: 'Month',
      week: 'Week',
      day: 'Day'
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
    dateClick: function (info) {
      // Show the add event form when a date is clicked
      addEventForm.classList.remove('hidden');
      eventStart.value = info.dateStr; // Corrected here
      eventEnd.value = info.dateStr; // Set end date to the same as start date
    },
    // Handle event click for deletion
    eventClick: function (info) {
      handleEventDelete(info.event.id);
    }
  });

  // Add Event Form submission
  addEventForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (addEventForm.checkValidity() === false) {
      e.stopPropagation();
      [...addEventForm.elements].forEach((input) => {
        const feedback = input.nextElementSibling;
        if (input.tagName === 'INPUT') {
          if (!input.checkValidity()) {
            input.classList.add('border-red-500');
            feedback.classList.remove('hidden');
          } else {
            input.classList.remove('border-red-500');
            feedback.classList.add('hidden');
          }
        }
      });
      const red600 = '#dc2626';
      const red700 = '#b91c1c';
      showToaster('Fill the required input !', 'exclamation-error', red600, red700);
      return false;
    } else {
      const title = document.getElementById('event-title').value;
      const start = document.getElementById('event-start').value;
      const end = document.getElementById('event-end').value;

      const data = await fetch('./backend/admin_action.php?add_event=1', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ title, start, end }),
      });

      const response = await data.json();
      if (response.success) {
        calendar.addEvent({
          id: response.event_id,
          title: title,
          start: start,
          end: end
        });

        showToaster('Event added successfully!', 'check', '#047857', '#065f46');

        // Reset and hide the form
        addEventForm.reset();
        addEventForm.classList.add('hidden');

        // Remove validation classes after reset
        [...addEventForm.elements].forEach((input) => {
          if (input.tagName === 'INPUT') {
            input.classList.remove('border-green-700', 'border-red-500');
            const feedback = input.nextElementSibling;
            if (feedback) feedback.classList.add('hidden'); // Hide feedback after reset
          }
        });
      }
    }
  });

  // Cancel Button
  document.getElementById('cancel-event').addEventListener('click', () => {
    addEventForm.reset();
    [...addEventForm.elements].forEach(input => {
      if (input.tagName === 'INPUT') {
        input.classList.remove('border-red-500', 'border-green-700');
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('text-red-500')) {
          feedback.classList.add('hidden');
        }
      }
    });
    addEventForm.classList.add('hidden');
  });

  calendar.render();


});





