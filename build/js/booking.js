document.addEventListener('DOMContentLoaded', function() {
  // Buttons
  const nextToStep2 = document.getElementById('nextToStep2');
  const backToStep1 = document.getElementById('backToStep1');
  const nextToStep3 = document.getElementById('nextToStep3');
  const backToStep2 = document.getElementById('backToStep2');

  // Event Listeners
  nextToStep2.addEventListener('click', function() {
      showStep(2);
  });

  backToStep1.addEventListener('click', function() {
      showStep(1);
  });

  nextToStep3.addEventListener('click', function() {
      showStep(3);
  });

  backToStep2.addEventListener('click', function() {
      showStep(2);
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
              element.classList.add('bg-sunrise', 'text-white');
              element.classList.remove('bg-gray-300', 'text-gray-600');
          } else {
              element.classList.add('bg-gray-300', 'text-gray-600');
              element.classList.remove('bg-sunrise', 'text-white');
          }
      });
  }
});

// Get the current date and time
const now = new Date();

// Format the date as YYYY-MM-DD
const currentDate = now.toISOString().split('T')[0];

// Format the time as HH:MM (24-hour format)
const currentTime = now.toTimeString().split(' ')[0].substring(0, 5);

// Set the value of the date and time inputs
document.querySelector('input[type="date"]').value = currentDate;
document.querySelector('input[type="time"]').value = currentTime;


