document.addEventListener('DOMContentLoaded', function () {
    // Buttons
    const nextToStep2 = document.getElementById('nextToStep2');
    const backToStep1 = document.getElementById('backToStep1');
    const nextToStep3 = document.getElementById('nextToStep3');
    const backToStep2 = document.getElementById('backToStep2');
    const submitButton = document.querySelector('button[type="submit"]');

    // Event Listeners
    nextToStep2.addEventListener('click', function () {
        showStep(2);
    });

    backToStep1.addEventListener('click', function () {
        showStep(1);
    });

    nextToStep3.addEventListener('click', function () {
        showStep(3);
    });

    backToStep2.addEventListener('click', function () {
        showStep(2);
    });

    // SweetAlert Confirmation on Submit
    submitButton.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the form from submitting immediately

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure?',
            text: "Please confirm your booking details before submitting.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, submit it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, show success message and submit the form
                Swal.fire({
                    title: 'Submitted!',
                    text: 'Your booking has been successfully submitted.',
                    icon: 'success'
                }).then(() => {
                    // After showing the success message, submit the form
                    document.querySelector('form').submit();
                });
            }
        });
    });

    // Function to show specific step and update the step indicators
    function showStep(step) {
        document.querySelectorAll('.step-content').forEach(content => content.classList.add('hidden'));
        document.getElementById('step' + step + 'Content').classList.remove('hidden');
        updateSteps(step);
    }

    // Function to update step indicators
    function updateSteps(step) {
        document.querySelectorAll('.step').forEach((element, index) => {
            if (index < step) {
                element.classList.add('bg-green-700', 'text-white');
                element.classList.remove('bg-gray-300', 'text-gray-600');
            } else {
                element.classList.add('bg-gray-300', 'text-gray-600');
                element.classList.remove('bg-green-700', 'text-white');
            }
        });
    }

    // Highlight Step 1 on page load
    showStep(1);

    // Format time in 24-hour format (as per your existing code)
    function formatTime24Hour(date) {
        let hours = date.getHours();
        const minutes = date.getMinutes();
        const minutesFormatted = minutes < 10 ? '0' + minutes : minutes;
        const hoursFormatted = hours < 10 ? '0' + hours : hours;

        return hoursFormatted + ':' + minutesFormatted;
    }

    // Get the current date and time
    const now = new Date();

    // Format the date as YYYY-MM-DD
    const currentDate = now.toISOString().split('T')[0];

    // Format the time as 24-hour format (HH:MM)
    const currentTime = formatTime24Hour(now);

    // Set the value of the date and time inputs
    document.querySelector('input[type="date"]').value = currentDate;
    document.querySelector('input[type="time"]').value = currentTime;

    // Getting the inputs and displaying them in the summary (as per your existing code)
    const dateInput = document.querySelector('.js-date');
    const timeInput = document.querySelector('.js-time');
    const radioButtons = document.querySelectorAll('input[name="service"]');
    const otherSuggestionInput = document.querySelector('.js-suggestion');
    const fnameInput = document.querySelector('.js-fname-input');
    const lnameInput = document.querySelector('.js-lname-input');
    const phoneNumberInput = document.querySelector('.js-phone-number');
    const emailInput = document.querySelector('.js-email-input');
    const addressInput = document.querySelector('.js-address-input');
    const shippingRadios = document.querySelectorAll('input[name="shipping_method"]');

    const serviceDisplay = document.querySelector('.js-service');
    const dateDisplay = document.querySelector('.js-preffered-date');
    const timeDisplay = document.querySelector('.js-preffered-time');
    const suggestionDisplay = document.querySelector('.js-other-suggestions');
    const displayCurrentDate = document.querySelector('.js-current-date');
    const displayCurrentTime = document.querySelector('.js-current-time');
    const displayFirstName = document.querySelector('.js-fname');
    const displayLastName = document.querySelector('.js-lname');
    const displayPhoneNumber = document.querySelector('.js-phone_number');
    const displayEmail = document.querySelector('.js-email');
    const displayAddress = document.querySelector('.js-address');
    const shippingDisplay = document.querySelector('.js-shipping-method');

    // Function to update the display for date and time (as per your existing code)
    function updateDisplay() {
        dateDisplay.textContent = dateInput.value || '';
        timeDisplay.textContent = timeInput.value || '';
        const selectedRadio = document.querySelector('input[name="service"]:checked');
        serviceDisplay.textContent = selectedRadio ? selectedRadio.value : 'Wash, Dry, Fold';
        suggestionDisplay.textContent = otherSuggestionInput.value || 'None';
        const text = otherSuggestionInput.value.trim();
        suggestionDisplay.textContent = text ? text : 'None';
        displayCurrentDate.textContent = currentDate;
        displayCurrentTime.textContent = currentTime;
        displayFirstName.textContent = fnameInput.value;
        displayLastName.textContent = lnameInput.value;
        displayPhoneNumber.textContent = phoneNumberInput.value;
        displayEmail.textContent = emailInput.value;
        displayAddress.textContent = addressInput.value;
        const selectedShippingRadio = document.querySelector('input[name="shipping_method"]:checked');
        shippingDisplay.textContent = selectedShippingRadio ? selectedShippingRadio.value : '2-day Standard';
    }

    updateDisplay();
    // Add event listeners to update the display whenever the user changes the input (as per your existing code)
    dateInput.addEventListener('input', updateDisplay);
    timeInput.addEventListener('input', updateDisplay);
    radioButtons.forEach(radio => {
        radio.addEventListener('change', updateDisplay);
    });
    otherSuggestionInput.addEventListener('input', updateDisplay);
    fnameInput.addEventListener('input', updateDisplay);
    lnameInput.addEventListener('input', updateDisplay);
    phoneNumberInput.addEventListener('input', updateDisplay);
    emailInput.addEventListener('input', updateDisplay);
    addressInput.addEventListener('input', updateDisplay);
    shippingRadios.forEach(radio => {
        radio.addEventListener('change', updateDisplay);
    });
});
