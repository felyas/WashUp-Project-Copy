import { handleDisplayCurrentTime, handleSidebar, openModal, showToaster, Modal } from "./dashboards-main.js";
const userAccountBtn = document.getElementById('js-account-setting');

userAccountBtn.addEventListener('click', () => {
  window.location.href = './user-setting.php';
})

// Event: Redirect to customer dashboard
const backToDashboardBtn = document.getElementById('back-to-dashboard-btn');
backToDashboardBtn.addEventListener('click', () => {
  window.location.href = './customer-dashboard.php';
});

// TO OPEN AND CLOSE THE SETTING
const settingBtn = document.getElementById('js-setting-button');
const settingDiv = document.getElementById('js-setting');

settingBtn.addEventListener('click', () => {
  settingDiv.classList.toggle('hidden');
});

// Close the settingDiv when clicking outside of it
document.addEventListener('click', (event) => {
  if (!settingDiv.contains(event.target) && !settingBtn.contains(event.target)) {
    settingDiv.classList.add('hidden');
  }
});

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

function validateForm(form) {
  form.addEventListener('input', (e) => {
    const target = e.target;
    const feedback = target.nextElementSibling;

    if (target.tagName === 'INPUT' && (target.type === 'text' || target.type === 'date' || target.type === 'time' || target.type === 'number')) {
      if (target.checkValidity()) {
        target.classList.remove('border-red-500');
        target.classList.add('border-green-700');
        if (feedback && feedback.classList.contains('text-red-500')) {
          feedback.classList.add('hidden');
        }
      } else {
        target.classList.remove('border-green-700');
        target.classList.add('border-red-500');
        if (feedback && feedback.classList.contains('text-red-500')) {
          feedback.classList.remove('hidden');
        }
      }
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  openModal('change-password-trigger', 'toViewChangePasswordModal', 'closeViewChangePasswordModal', 'closeViewChangePasswordModal2');


  // Fetch Customer Data From Users Table
  const customerInfo = async () => {
    const data = await fetch(`./backend/customer_action.php?customer-data=1`, {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('js-fname').value = response.first_name;
    document.getElementById('js-lname').value = response.last_name;
    document.getElementById('js-phone_number').value = response.phone_number;
    document.getElementById('js-email').value = response.email;
  }
  customerInfo();

  const updateUserInfoForm = document.getElementById('update-info-form');
  const saveBtn = document.getElementById('save-info-btn');
  const errorContainer = document.getElementById('error-container');
  const errorMessage = document.getElementById('error-message');

  // Handle the input validation from add bookings
  validateForm(updateUserInfoForm);


  // Function to validate a phone number in the format 09691026692
  // const isPhoneNumberValid = (phoneNumber) => {
  //   // Check if the phone number starts with 09 and has exactly 11 digits
  //   const phoneRegex = /^09\d{9}$/;
  //   return phoneRegex.test(phoneNumber);
  // };



  updateUserInfoForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(updateUserInfoForm);
    formData.append('save', 1);

    if (updateUserInfoForm.checkValidity() === false) {
      e.stopPropagation();

      [...updateUserInfoForm.elements].forEach((input) => {
        if (input.tagName === 'INPUT' && (input.type === 'text' || input.type === 'date' || input.type === 'time' || input.type === 'number')) {
          const feedback = input.nextElementSibling;

          if (!input.checkValidity()) {
            input.classList.add('border-red-500');
            if (feedback && feedback.classList.contains('text-red-500')) {
              feedback.classList.remove('hidden');
            }
          } else {
            input.classList.remove('border-red-500');
            if (feedback && feedback.classList.contains('text-red-500')) {
              feedback.classList.add('hidden');
            }
          }
        }
      });

      const errorWarningModal = new Modal('error-modal', 'error-confirm-modal', 'error-close-modal');
      errorWarningModal.show();

      return false;
    } else {
      saveBtn.value = 'Please Wait...';
      const data = await fetch('./backend/setting_action.php', {
        method: 'POST',
        body: formData,
      });
      const response = await data.json();
      console.log(response);
      if (response.redirect) {
        saveBtn.value = 'Save';
        window.location.href = response.redirect;
      } else if (response.error) {
        errorContainer.classList.remove('hidden');
        errorMessage.innerText = response.error;
        saveBtn.value = 'Save';
      }

    }
  });


  const errorContainerUpdatePassword = document.getElementById('error-container-update-password');
  const errorMessageUpdatePassword = document.getElementById('error-message-update-password');
  const successContainerUpdatePassword = document.getElementById('success-container-update-password');
  const successMessageUpdatePassword = document.getElementById('success-message-update-password');

  const updatePasswordForm = document.getElementById('change-password-form');
  const changePasswordBtn = document.getElementById('change-password-btn');

  let timeoutId;

  updatePasswordForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    changePasswordBtn.value = 'Please Wait';

    const formData = new FormData(updatePasswordForm);
    formData.append('update-password', 1);

    const data = await fetch('./backend/setting_action.php', {
      method: 'POST',
      body: formData,
    })

    const response = await data.json();
    if (response.status === 'success') {
      errorMessageUpdatePassword.classList.add('hidden');
      errorMessageUpdatePassword.innerText = '';
      successContainerUpdatePassword.classList.remove('hidden');
      successMessageUpdatePassword.innerText = 'Password Updated Successfully'
      updatePasswordForm.reset();
      changePasswordBtn.value = 'Update Password';

      if(timeoutId) {
        clearTimeout(timeoutId);
      }

      // Hide the success message after 3 seconds
      timeoutId = setTimeout(() => {
        successContainerUpdatePassword.classList.add('hidden');
        successMessageUpdatePassword.innerText = '';
      }, 3000);
    } else if (response.error) {
      errorContainerUpdatePassword.classList.remove('hidden');
      errorMessageUpdatePassword.innerText = response.error;
      changePasswordBtn.value = 'Update Password';
    } else {
      errorContainerUpdatePassword.classList.remove('hidden');
      errorMessageUpdatePassword.innerText = 'Something went wrong, please try again later!';
      changePasswordBtn.value = 'Update Password';
    }



  });


});