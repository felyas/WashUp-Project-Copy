//Dashvoard functionality
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

// Export functions without calling them
export { handleSidebar, handleDisplayCurrentTime, handleNotification, handleTdColor };
