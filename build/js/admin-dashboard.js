import { handleSidebar, handleDisplayCurrentTime, handleTdColor, openModal, handleDropdown, showToaster, Modal } from "./dashboards-main.js";


document.addEventListener("DOMContentLoaded", () => {
  handleTdColor();
  handleSidebar();
  handleDisplayCurrentTime();
  handleDropdown();
  openModal('viewModalTrigger', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');



  const pendingTbody = document.getElementById('js-pending-tbody');
  const paginationContainer = document.getElementById('pagination-container');

  // Fetch All Pending Bookings with Pagination
  const fetchAllPendingBooking = async (page = 1) => {
    const searchQuery = document.getElementById('search-input').value.trim();
    const data = await fetch(`./backend/admin_action.php?readPending=1&page=${page}&query=${searchQuery}`, {
      method: 'GET',
    });
    const response = await data.json();
    pendingTbody.innerHTML = response.bookings;
    paginationContainer.innerHTML = response.pagination; // Pagination displayed here
  }

  // Search Input Event Listener
  document.getElementById('search-input').addEventListener('input', function () {
    fetchAllPendingBooking(); // Fetch data when search input changes
  });

  // Pagination Links Event Delegation
  paginationContainer.addEventListener('click', function (event) {
    if (event.target.classList.contains('pagination-link')) {
      event.preventDefault();
      const page = event.target.getAttribute('data-page');
      fetchAllPendingBooking(page); // Fetch data for the clicked page
    }
  });

  // Initial Fetch
  fetchAllPendingBooking();

  const tbodyList = document.querySelectorAll('tbody');
  tbodyList.forEach(tbody => {
    // View Booking Ajax Request
    tbody.addEventListener('click', (e) => {

      // Target the viewLink
      if (e.target && (e.target.matches('a.viewLink') || e.target.closest('a.viewLink'))) {
        e.preventDefault();
        let targetElement = e.target.matches('a.viewLink') ? e.target : e.target.closest('a.viewLink');
        let id = targetElement.getAttribute('id');
        // console.log(id);
        bookingSummary(id);
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
        const deniedWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal');
        deniedWarningModal.show(deniedBooking, id);
      }
    });
  });



  // Fetch Booking Details Ajax Request
  const bookingSummary = async (id) => {
    const data = await fetch(`./backend/admin_action.php?view=1&id=${id}`, {
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
      fetchAllPendingBooking();
      fetchCount();
    }
  }


  // Denied Booking and Update Status to For Pick-Up Ajax Request.
  const deniedBooking = async (id) => {
    const data = await fetch(`./backend/admin_action.php?denied=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.text();
    if (response.includes('success')) {
      const green600 = '#047857';
      const green700 = '#065f46';
      showToaster('Booking denied !', 'check', green600, green700);
      fetchAllPendingBooking();
      fetchCount();
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

  // Long polling function for fetching new booking requests
  const fetchNewBookings = async () => {
    try {
      const response = await fetch(`./backend/admin_action.php?fetch_new_bookings=1`);
      const notifications = await response.json();

      const notificationContainer = document.querySelector('.js-notification-messages');
      const notificationDot = document.querySelector('.js-notification-dot'); // Red dot element
      const totalNotificationsElement = document.querySelector('.js-total-notifications'); // Total notifications element

      // If there are new booking notifications, display them
      if (notifications.length > 0) {
        // Clear existing messages
        notificationContainer.innerHTML = '';

        // Append each notification to the container
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

        // Update total notification count
        totalNotificationsElement.textContent = notifications.length;
        notificationDot.classList.remove('hidden');  // Show red dot

      } else {
        notificationDot.classList.add('hidden');  // Hide red dot if no new notifications
      }

      // Continue long polling after 1 seconds
      setTimeout(fetchNewBookings, 10000);  // Poll every 5 seconds
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

  // Handle closing individual notifications
  document.addEventListener('click', async (e) => {
    if (e.target.classList.contains('js-notification-close')) {
      const bookingId = e.target.getAttribute('data-id');
      e.target.parentElement.remove();  // Remove notification from UI

      // Send a request to the server to mark this booking notification as read
      const response = await fetch(`./backend/admin_action.php?mark_admin_booking_read=1&id=${bookingId}`);
      const data = await response.json();

      if (data.success) {
        console.log('Booking notification marked as read.');
      } else {
        console.error('Failed to mark booking notification as read.');
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

        const red600 = '#dc2626';
        const red700 = '#b91c1c';
        showToaster('Event deleted successfully!', 'trash', red600, red700);
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

        const green600 = '#047857';
        const green700 = '#065f46';
        showToaster('Event added successfully!', 'check', green600, green700);

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





