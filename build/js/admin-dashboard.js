import { handleSidebar, handleDisplayCurrentTime, handleNotification, handleTdColor } from "./dashboards-main.js";

document.addEventListener("DOMContentLoaded", () => {
  handleTdColor();
  handleSidebar();
  handleDisplayCurrentTime();
  handleNotification();




  const pendingTbody = document.getElementById('js-pending-tbody');
  // Fetch All Pending Booking Ajax Request
  const fetchAllPendingBooking = async () => {
    const data = await fetch('./backend/admin_action.php?readPending=1', {
      method: 'GET',
    });
    const response = await data.text();
    pendingTbody.innerHTML = response;
  }
  fetchAllPendingBooking();

  const pickupTbody = document.getElementById('js-pickup-tbody');
  // Fetch All For Pick-up Booking Ajax Request
  const fetchAllForPickupBooking = async () => {
    const data = await fetch('./backend/admin_action.php?readPickup=1', {
      method: 'GET',
    });
    const response = await data.text();
    pickupTbody.innerHTML = response;
    
  }
  fetchAllForPickupBooking();

  const deliveryTbody = document.getElementById('js-delivery-tbody');
  // Fetch All Delivery Booking Ajax Request
  const fetchAllDeliveryBooking = async () => {
    const data = await fetch('./backend/admin_action.php?readDelivery=1', {
      method: 'GET',
    });
    const response = await data.text();
    deliveryTbody.innerHTML = response;
  }
  fetchAllDeliveryBooking();

});


// //Function to open modal, should be edit according to logic.
// function openModal(openModal, modalName, closeModal, closeModal2) {
//   document.getElementById(openModal).addEventListener('click', function () {
//     document.getElementById(modalName).classList.remove('hidden');
//   });

//   document.getElementById(closeModal).addEventListener('click', function () {
//     document.getElementById(modalName).classList.add('hidden');
//   });

//   document.getElementById(closeModal2).addEventListener('click', function () {
//     document.getElementById(modalName).classList.add('hidden');
//   });

//   document.getElementById('editForm').addEventListener('submit', function (event) {
//     event.preventDefault();
//     // Add your form submission logic here
//     document.getElementById(modalName).classList.add('hidden');
//   });
// }
// openModal('openViewBookingModal', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');
