import { updateCopyRightYear } from "./main.js";

updateCopyRightYear();

document.addEventListener("DOMContentLoaded", () => {

  document.querySelectorAll('.show-password').forEach((icon) => {
    icon.addEventListener('click', () => {
      const input = icon.previousElementSibling; // Get the input field before the icon
  
      // Toggle the type of the input field
      if (input.type === 'password') {
        input.type = 'text'; // Show the password
        icon.src = './img/icons/eye-open.svg'; // Change the icon to eye-open
      } else {
        input.type = 'password'; // Hide the password
        icon.src = './img/icons/eye-close.svg'; // Change the icon back to eye-close
      }
    });
  });
  

});