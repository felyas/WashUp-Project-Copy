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

// Function to handle account dropdown
function handleDropdown() {
  const accountDropdown = document.getElementById('account-dropdown');
  const dropdownMenu = document.getElementById('dropdown-menu');
  const dropdownIcon = document.getElementById('dropdown-icon');

  // Toggle dropdown visibility on click
  accountDropdown.addEventListener('click', function () {
    dropdownMenu.classList.toggle('hidden');
    dropdownIcon.classList.toggle('rotate-180');
  });
}

// Function to call toaster
const showToaster = (function () {
  let timeoutId;

  return function (message, icon, color600, color700) {
    const toaster = document.getElementById('toaster');
    toaster.classList.add('hidden');

    // Set the styles directly using inline styles
    toaster.innerHTML = `
      <div style="background-color: ${color600}; border: 2px solid ${color700};" class="h-14 py-2 px-4 rounded flex items-center justify-between w-full space-x-2">
        <div class="flex items-center justify-center space-x-2">
          <div id="toaster-icon" style="background-color: ${color700};" class="p-2 h-8 w-8 rounded-full flex items-center justify-center">
            <img class="w-5 h-5" src="./img/icons/${icon}.svg" alt="Check icon">
          </div>
          <p id="toaster-msg" class="text-sm font-semibold">${message}</p>
        </div>
        <div class="h-full flex items-center justify-center">
          <button id="close-toaster" class="text-white hover:text-gray-200 font-bold text-3xl">&times;</button>
        </div>
      </div>
    `;

    toaster.classList.remove('hidden');

    // Clear any existing timeout to prevent stacking
    if (timeoutId) {
      clearTimeout(timeoutId);
    }

    // Hide toaster after 3 seconds
    timeoutId = setTimeout(() => {
      toaster.classList.add('hidden');
    }, 3000);

    // Add event listener for close button to hide the toaster immediately
    const closeToasterBtn = document.getElementById('close-toaster');
    closeToasterBtn.addEventListener('click', () => {
      clearTimeout(timeoutId); // Clear the timeout so it doesn't reappear
      toaster.classList.add('hidden'); // Hide the toaster immediately
    });
  };
})();




class Modal {
  constructor(modalId, confirmBtnId, closeBtnId) {
    this.modal = document.getElementById(modalId);
    this.confirmBtn = document.getElementById(confirmBtnId);
    this.closeBtn = document.getElementById(closeBtnId);
    this.currentAction = null;
    this.bookingId = null;

    this.init();
  }

  init() {
    this.closeBtn.addEventListener('click', () => this.hide());
    this.confirmBtn.addEventListener('click', () => this.confirm());
  }

  show(callback, id) {
    this.currentAction = callback;
    this.bookingId = id;
    this.modal.classList.remove('hidden');
  }

  hide() {
    this.modal.classList.add('hidden');
    this.bookingId = null;
  }

  confirm() {
    if (this.currentAction && this.bookingId) {
      this.currentAction(this.bookingId);
      this.hide();
    }
  }
}

// Export functions without calling them
export { handleSidebar, handleDisplayCurrentTime, handleNotification, handleTdColor, openModal, handleDropdown, showToaster, Modal };
