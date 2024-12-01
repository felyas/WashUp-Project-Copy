import { handleSidebar, handleDisplayCurrentTime, handleTdColor, openModal, handleDropdown, showToaster, Modal } from "./dashboards-main.js";

document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();
  handleDropdown();





  const tbody = document.getElementById('complete-list');
  const paginationContainer = document.getElementById('pagination-container');
  const searchInput = document.getElementById('js-search-bar'); 

  // FUNCTION TO TOGGLE SORTING ICONS
  const toggleSortIcon = (th, order) => {
    const allIcons = document.querySelectorAll('.sort-icon img');

    //RESET ALL ICONS TO CARET-DOWN BY DEFAULT
    allIcons.forEach(icon => {
      icon.setAttribute('src', './img/icons/caret-down.svg');
    });

    const icon = th.querySelector('.sort-icon img');
    if (order === 'desc') {
      icon.setAttribute('src', './img/icons/caret-down.svg');
    } else {
      icon.setAttribute('src', './img/icons/caret-up.svg');
    }
  }

  // FETCH ALL COMPLETE BOOKING AJAX REQUEST
  const fetchAll = async (page = 1, column = 'id', order = 'desc', query = '') => {
    const searchQuery = searchInput.value || query;

    const data = await fetch(`./backend/complete_action.php?readAll=1&page=${page}&column=${column}&order=${order}&query=${searchQuery}`, {
      method: 'GET',
    });
    const response = await data.json();
    tbody.innerHTML = response.bookings;
    paginationContainer.innerHTML = response.pagination;
  }

  // Handle column sorting
  document.querySelectorAll('.sortable').forEach(th => {
    th.addEventListener('click', function () {
      const column = this.getAttribute('data-column');
      let order = this.getAttribute('data-order');
      order = order === 'desc' ? 'asc' : 'desc'; // Toggle order

      this.setAttribute('data-order', order);
      toggleSortIcon(this, order); // Change icon direction

      fetchAll(1, column, order);
    });
  });

  // Handle search input
  searchInput.addEventListener('input', () => {
    fetchAll();
  });

  // Handle pagination clicks
  paginationContainer.addEventListener('click', function (event) {
    if (event.target.classList.contains('pagination-link')) {
      event.preventDefault();
      const page = event.target.getAttribute('data-page');
      fetchAll(page);
    }
  });

  // Initial fetch
  fetchAll();

});