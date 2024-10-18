import { handleSidebar, handleDisplayCurrentTime, handleDropdown, openModal, showToaster, Modal } from "./dashboards-main.js";

openModal('viewModalTrigger', 'toViewBookingModal', 'closeViewBookingModal', 'closeViewBookingModal2');

document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();

  const tbody = document.getElementById('customer-complaint-list');
  const paginationContainer = document.getElementById('pagination-container');
  const searchInput = document.getElementById('js-search-bar');
  const statusFilter = document.getElementById('status-filter');

  // Function to toggle sorting icons
  const toggleSortIcon = (th, order) => {
    const allIcons = document.querySelectorAll('.sort-icon img');

    // Reset all icons to caret-down by default
    allIcons.forEach(icon => {
      icon.setAttribute('src', './img/icons/caret-down.svg'); // Set to down arrow
    });

    const icon = th.querySelector('.sort-icon img');
    if (order === 'desc') {
      icon.setAttribute('src', './img/icons/caret-down.svg'); // Down arrow
    } else {
      icon.setAttribute('src', './img/icons/caret-up.svg'); // Up arrow
    }
  };

  // Fetch All items with pagination, search, and sorting
  const fetchAll = async (page = 1, column = 'complaint_id', order = 'desc', query = '') => {
    const searchQuery = searchInput.value.trim() || query;
    const statusQuery = status || statusFilter.value; // Get status from dropdown or passed value

    const data = await fetch(`./backend/customer-complaint_action.php?readAll=1&page=${page}&column=${column}&order=${order}&query=${searchQuery}&status=${statusQuery}`, {
      method: 'GET',
    });
    const response = await data.json();
    tbody.innerHTML = response.items;
    paginationContainer.innerHTML = response.pagination;
  }

  // Handle Column Sorting 
  document.querySelectorAll('.sortable').forEach(th => {
    th.addEventListener('click', () => {
      const column = th.getAttribute('data-column');
      let order = th.getAttribute('data-order');
      order = order === 'desc' ? 'asc' : 'desc';

      th.setAttribute('data-order', order);
      toggleSortIcon(th, order);

      fetchAll(1, column, order);
    });
  });

  // Handle search input
  searchInput.addEventListener('input', () => {
    fetchAll();
  });

  paginationContainer.addEventListener('click', (e) => {
    if (e.target.classList.contains('pagination-link')) {
      e.preventDefault();
      const page = e.target.getAttribute('data-page');
      fetchAll(page);
    }
  });

  statusFilter.addEventListener('change', () => {
    fetchAll();
  })

  // Initial fetch
  fetchAll();


  tbody.addEventListener('click', (e) => {
    // Target View Link
    if (e.target && (e.target.matches('a.viewLink') || e.target.closest('a.viewLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.viewLink') ? e.target : e.target.closest('a.viewLink');
      let id = targetElement.getAttribute('id');
      complainInfo(id);
    }

    // Target Resolved Link
    if (e.target && (e.target.matches('a.resolvedLink') || e.target.closest('a.resolvedLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.resolvedLink') ? e.target : e.target.closest('a.resolvedLink');
      let id = targetElement.getAttribute('id');
      const resolvedWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal');
      resolvedWarningModal.show(toResolved, id);
    }

    // Target Delete Link
    if (e.target && (e.target.matches('a.deleteLink') || e.target.closest('a.deleteLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.deleteLink') ? e.target : e.target.closest('a.deleteLink');
      let id = targetElement.getAttribute('id');
      const resolvedWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal');
      resolvedWarningModal.show(closed, id);
    }


  });

  // View Complaint Info Ajax Request
  const complainInfo = async (id) => {
    const data = await fetch(`./backend/customer-complaint_action.php?read=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    console.log(response);
    document.getElementById('created_at').innerText = response.created_at;
    document.getElementById('display-id').innerText = response.complaint_id;
    document.getElementById('display-full-name').innerText = response.first_name + ' ' + response.last_name;
    document.getElementById('display-phone-number').innerText = response.phone_number;
    document.getElementById('display-email').innerText = response.email;
    document.getElementById('display-reason').innerText = response.reason;
    document.getElementById('display-description').innerText = response.description;
  }

  // Update Status from Pending to Resolved Ajax Request
  const toResolved = async (id) => {
    const data = await fetch(`./backend/customer-complaint_action.php?resolved=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    if (response.status === 'success') {
      showToaster(response.message, 'check', '#047857', '#065f46');
      fetchAll();
    } else {
      showToaster('Something went wrong!', 'exclamation-error', '#dc2626', '#b91c1c');
    }
  }

  // Delete Complaint Record Ajax Request
  const closed = async (id) => {
    const data = await fetch(`./backend/customer-complaint_action.php?delete=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    if (response.status === 'success') {
      showToaster(response.message, 'check', '#047857', '#065f46');
      fetchAll();
    } else {
      showToaster('Something went wrong!', 'exclamation-error', '#dc2626', '#b91c1c');
    }
  }

  // FETCH TOTAL COUNT FOR CARDS AJAX REQUEST
  const fetchComplaintCounts = async () => {
    const data = await fetch('./backend/customer-complaint_action.php?count_all=1', {
      method: 'GET',
    });
    const response = await data.json();
    document.getElementById('js-pending-count').textContent = response.pendingCount;
    document.getElementById('js-resolved').textContent = response.resolvedCount;
  };
  fetchComplaintCounts();

});