import { updateCopyRightYear } from "./main.js";
updateCopyRightYear();

const otp = document.querySelectorAll('.js-otp-field');

// Focus on the first input
otp[0].focus();

otp.forEach((field, index) => {
	// Handle input events
	field.addEventListener('input', () => {
		// Move to the next input if the current input is filled
		if (field.value.length >= 1 && otp[index + 1]) { // Check if the next input exists
			otp[index + 1].focus();
		}
	});

	// Handle keydown events for keyboard input
	field.addEventListener('keydown', (e) => {
		if (e.key === 'Backspace' && field.value === '') {
			// Move focus to the previous input when backspace is pressed
			if (otp[index - 1]) { // Check if the previous input exists
				setTimeout(() => {
					otp[index - 1].focus();
				}, 4);
			}
		}
	});

	// Optional: Handle touch events to improve mobile usability
	field.addEventListener('touchend', () => {
		field.focus();
	});
});

document.addEventListener("DOMContentLoaded", () => {

	const otpForm = document.getElementById('otp-form');
	const sendOtpBtn = document.getElementById('otp-submit-button');
	const errorContainer = document.getElementById('error-container');
	const errorMessage = document.getElementById('error-message');

	otpForm.addEventListener('submit', async (e) => {
		e.preventDefault();

		sendOtpBtn.value = 'Please wait...';
		
		const formData = new FormData(otpForm);
		formData.append('verify', 1);

		try {
			const data = await fetch('./backend/authentication_action.php', {
				method: 'POST',
				body: formData,
			})

			const response = await data.json();

			if(response.redirect) {
				window.location.href = response.redirect;
			} else if(response.error) { 
				errorContainer.classList.remove('hidden');
        errorMessage.innerText = response.error;
        sendOtpBtn.value = 'Submit';
			}
		} catch (error) {
			errorContainer.classList.remove('hidden');
      errorMessage.innerText = "An error occurred. Please try again.";
			location.reload();
			sendOtpBtn.value = 'Submit';
		}
	})


});

