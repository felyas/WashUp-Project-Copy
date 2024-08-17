const sidebar = document.getElementById('sidebar');
const hamburger = document.getElementById('hamburger');
const closeSidebar = document.getElementById('close-sidebar');

// Toggle sidebar visibility
hamburger.addEventListener('click', function() {
  sidebar.classList.toggle('-translate-x-full');
});

// Hide sidebar
closeSidebar.addEventListener('click', function() {
  sidebar.classList.add('-translate-x-full');
});

// Initialize the Chart.js chart
const ctx = document.getElementById('bookingChart').getContext('2d');
const bookingChart = new Chart(ctx, {
  type: 'bar', // Change to 'line', 'pie', etc. if you want a different chart type
  data: {
    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], // Example labels
    datasets: [{
      label: 'Bookings',
      data: [12, 19, 3, 5], // Example data
      backgroundColor: 'rgba(75, 192, 192, 0.2)', // Bar color
      borderColor: 'rgba(75, 192, 192, 1)', // Border color
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});

// Select all table cells with the "td" tag
const tableCells = document.querySelectorAll("td");

// Loop through each cell
tableCells.forEach(cell => {
  // Check if the cell's inner text is "Pending"
  if (cell.innerText.trim() === 'Pending') {
    // Apply the background color (you can change the color as needed)
    cell.style.color = "#FFB000"; // Example color: light yellow
  } else if (cell.innerText.trim() === 'Pick-up') {
    // Apply the background color (you can change the color as needed)
    cell.style.color = "#3b7da3"; // Example color: light yellow
  } else if (cell.innerText.trim() === 'Delivery') {
    // Apply the background color (you can change the color as needed)
    cell.style.color = "#15803d"; // Example color: light yellow
  } 
});

function updateTime() {
  const now = new Date();
  // Options for the day and time format
  const options = { 
      weekday: 'long', 
      hour: 'numeric', 
      minute: 'numeric', 
      second: 'numeric',
      hour12: true // For 12-hour format with AM/PM
  };
  
  // Format the day and time string
  const formattedTime = now.toLocaleTimeString('en-US', options);
  document.querySelector('.js-current-time').textContent = `${formattedTime}`;
}
// Update the time every second
setInterval(updateTime, 1000);
// Initial call to display the time immediately on load
updateTime();

