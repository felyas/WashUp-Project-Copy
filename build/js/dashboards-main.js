//Dashboard functionalities

// Function to Handle Status Colors
function handleTdColor() {
  // Select all table cells with the "td" tag
  const tableCells = document.querySelectorAll("td");

  // Loop through each cell
  tableCells.forEach(cell => {
    if (cell.innerText.trim() === 'Pending') {
      cell.style.color = "#FFB000";
    } else if (cell.innerText.trim() === 'Pick-up') {
      cell.style.color = "#3b7da3";
    } else if (cell.innerText.trim() === 'Delivery') {
      cell.style.color = "#B91C1C";
    } else if (cell.innerText.trim() === 'Completed') {
      cell.style.color = "#15803d";
    }
  });
}

// Function to handle Notification
function handleNotification() {
  const notificationBtn = document.querySelector('.js-notification-button');
  const notificationElm = document.querySelector('.js-notification');

  notificationBtn.addEventListener('click', (event) => {
    event.stopPropagation();
    notificationElm.classList.toggle('hidden');
    notificationElm.classList.toggle('block');
  });

  document.addEventListener('click', (event) => {
    if (!notificationElm.classList.contains('hidden')) {
      const isClickInsideNotification = notificationElm.contains(event.target);
      const isClickInsideButton = notificationBtn.contains(event.target);

      if (!isClickInsideNotification && !isClickInsideButton) {
        notificationElm.classList.remove('block');
        notificationElm.classList.add('hidden');
      }
    }
  });
}

// Function to Handle Sidebar 
function handleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const hamburger = document.getElementById('hamburger');
  const closeSidebar = document.getElementById('close-sidebar');

  hamburger.addEventListener('click', () => {
    sidebar.classList.toggle('-translate-x-full');
  });

  closeSidebar.addEventListener('click', () => {
    sidebar.classList.add('-translate-x-full');
  });
}

// Function to Display Current Time
function handleDisplayCurrentTime() {
  const now = new Date();
  const options = {
    weekday: 'long',
    hour: 'numeric',
    minute: 'numeric',
    second: 'numeric',
    hour12: true
  };
  const formattedTime = now.toLocaleTimeString('en-US', options);
  document.querySelector('.js-current-time').textContent = `${formattedTime}`;
  setInterval(handleDisplayCurrentTime, 1000);
}

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

// Export functions without calling them
export { handleSidebar, handleDisplayCurrentTime, handleNotification, handleTdColor, openModal };
