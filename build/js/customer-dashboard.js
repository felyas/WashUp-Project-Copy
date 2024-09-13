import { handleDisplayCurrentTime, handleNotification, handleTdColor } from "./dashboards-main.js";

const bookNowBtn = document.querySelector('.js-book-now');
bookNowBtn.addEventListener('click', () => {
  window.location.href = './booking.php';
})

document.addEventListener("DOMContentLoaded", () => {
  handleTdColor();
  handleDisplayCurrentTime();
  handleNotification();
  openModal('editModalTrigger', 'toEditBookingModal', 'closeEditBookingModal', 'closeEditBookingModal2');
  openModal('viewModalTrigger', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');
});

// Function to open and close modals using classes
function openModal(modalTriggerClass, modalClass, closeModalClass, closeModalClass2) {
  // Open modal when element with modalTriggerClass is clicked
  document.body.addEventListener('click', function (event) {
    if (event.target.closest(`.${modalTriggerClass}`)) {
      event.preventDefault(); // Prevent default anchor behavior
      document.querySelector(`.${modalClass}`).classList.remove('hidden');
    }
  });

  // Close modal when element with closeModalClass is clicked
  document.body.addEventListener('click', function (event) {
    if (event.target.closest(`.${closeModalClass}`)) {
      document.querySelector(`.${modalClass}`).classList.add('hidden');
    }
  });

  // Close modal when element with closeModalClass2 is clicked
  document.body.addEventListener('click', function (event) {
    if (event.target.closest(`.${closeModalClass2}`)) {
      document.querySelector(`.${modalClass}`).classList.add('hidden');
    }
  });
}


const tbody = document.getElementById('users-booking-list');

// Fetch All Users Ajax Request
const fetchUserAllBooking = async () => {
  const data = await fetch('./backend/customer_action.php?read=1', {
    method: 'GET',
  });
  const response = await data.text();
  tbody.innerHTML = response;
}
fetchUserAllBooking();

// Edit User Ajax Request
tbody.addEventListener('click', (e) => {
  if(e.target && e.target.matches('a.editLink')) {
    e.preventDefault();
    let id = e.target.getAttribute('id');
    console.log(id);
  }
});