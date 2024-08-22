
// JavaScript function to redirect to login.php
function directToLoginPage (buttonId) {
  document.getElementById(buttonId).addEventListener('click', function () {
    window.location.href = './login.php';
  });
}

directToLoginPage('signInButton1');
directToLoginPage('signInButton2');
directToLoginPage('signInButton3');

export function updateCopyRightYear() {
  const currentYear = new Date().getFullYear();
  const yearElement = document.getElementById('year');

  yearElement.textContent = currentYear;
}

const initApp = () => {
  const hamburgerBtn = document.getElementById('hamburger-button');
  const mobileMenu = document.getElementById('mobile-menu');
  const closeButton = document.getElementById('close-button');
  const menuItems = mobileMenu.querySelectorAll('.menu-item'); // Select all menu items

  const toggleMenu = () => {
    mobileMenu.classList.toggle('show');
  }

  const closeMenu = () => {
    mobileMenu.classList.remove('show');
  }

  hamburgerBtn.addEventListener('click', toggleMenu);
  closeButton.addEventListener('click', closeMenu);

  // Add click event to all menu items
  menuItems.forEach(item => {
    item.addEventListener('click', closeMenu);
  });

  // Optionally: close menu when clicking outside
  document.addEventListener('click', (event) => {
    if (!mobileMenu.contains(event.target) && !hamburgerBtn.contains(event.target)) {
      closeMenu();
    }
  });
}

document.addEventListener('DOMContentLoaded', updateCopyRightYear);
document.addEventListener('DOMContentLoaded', initApp);