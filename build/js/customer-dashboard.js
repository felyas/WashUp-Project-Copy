import { handleDisplayCurrentTime, handleNotification, handleTdColor } from "./dashboards-main.js";

document.addEventListener("DOMContentLoaded", () => {
  handleTdColor();
  handleDisplayCurrentTime();
  handleNotification();
});

//Function to open modal, should be edit according to logic.
function openModal(openModal, modalName, closeModal, closeModal2) {
  document.getElementById(openModal).addEventListener('click', function () {
    document.getElementById(modalName).classList.remove('hidden');
  });

  document.getElementById(closeModal).addEventListener('click', function () {
    document.getElementById(modalName).classList.add('hidden');
  });

  document.getElementById(closeModal2).addEventListener('click', function () {
    document.getElementById(modalName).classList.add('hidden');
  });

  document.getElementById('editForm').addEventListener('submit', function (event) {
    event.preventDefault();
    // Add your form submission logic here
    document.getElementById(modalName).classList.add('hidden');
  });
}

const bookNowBtn = document.querySelector('.js-book-now');
bookNowBtn.addEventListener('click', () => {
  window.location.href = 'http://localhost/WashUp-Project/build/booking.php';
});

openModal('openEditBookingModal', 'toEditBookingModal', 'closeEditBookingModal', 'closeEditBookingModal2');
openModal('openViewBookingModal', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');