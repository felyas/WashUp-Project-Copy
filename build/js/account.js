import { handleSidebar, handleDisplayCurrentTime, openModal, handleDropdown } from "./dashboards-main.js";

document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();
  handleDropdown();
  openModal('viewModalTrigger', 'toViewUsersModal', 'closeViewUsesrModal', 'closeViewUsersModal2');

  // Preload SweetAlert
  Swal.fire({
    title: 'Initializing...',
    text: 'Please wait...',
    showConfirmButton: false,
    timer: 10,
    willOpen: () => {
      Swal.showLoading();
    },
    willClose: () => {
      Swal.hideLoading();
    }
  }).then(() => {
    Swal.close();
  });

  // Reusable SweetAlert Function (moved outside of DOMContentLoaded for global access)
  window.showAlert = (icon, title, text, confirmButtonClass = 'bg-gray-500 hover:bg-gray-600') => {
    return Swal.fire({
      icon, title, text,
      confirmButtonText: 'OK',
      customClass: {
        confirmButton: `${confirmButtonClass} text-white px-5 py-3 font-semibold rounded-lg`
      },
      buttonsStyling: false
    });
  };

  const tbody = document.getElementById('js-users-tbody');
  const paginationContainer = document.getElementById('pagination-container');
  const searchInput = document.getElementById('js-search-bar');
  const statusFilter = document.getElementById('status-filter');

  let currentPage = 1;
  let currentColumn = 'id';
  let currentOrder = 'desc';
  let debounceTimer;
  let isFetching = false;

  // Function to toggle sorting icons
  const toggleSortIcon = (th, order) => {
    document.querySelectorAll('.sort-icon img').forEach(icon => {
      icon.setAttribute('src', './img/icons/caret-down.svg');
    });

    const icon = th.querySelector('.sort-icon img');
    icon.setAttribute('src', `./img/icons/caret-${order === 'desc' ? 'down' : 'up'}.svg`);
  };

  // Fetch All Users with pagination, search, and sorting
  const fetchAll = async () => {
    if (isFetching) return;
    isFetching = true;

    const searchQuery = searchInput.value.trim();
    const statusQuery = statusFilter.value;

    try {
      const response = await fetch(`./backend/account_action.php?readAll=1&page=${currentPage}&column=${currentColumn}&order=${currentOrder}&query=${searchQuery}&status=${statusQuery}`);
      const data = await response.json();
      tbody.innerHTML = data.users;
      paginationContainer.innerHTML = data.pagination;
    } catch (error) {
      console.error('Error fetching data:', error);
      showAlert('error', 'Error', 'Failed to fetch data. Please try again.');
    } finally {
      isFetching = false;
    }
  };

  // Debounce function
  const debounce = (func, delay) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(func, delay);
  };

  // Handle Column Sorting 
  document.querySelectorAll('.sortable').forEach(th => {
    th.addEventListener('click', () => {
      if (isFetching) return;
      currentColumn = th.getAttribute('data-column');
      currentOrder = currentOrder === 'desc' ? 'asc' : 'desc';
      th.setAttribute('data-order', currentOrder);
      toggleSortIcon(th, currentOrder);
      currentPage = 1; // Reset to first page when sorting
      fetchAll();
    });
  });

  // Handle search input with debounce
  searchInput.addEventListener('input', () => {
    debounce(() => {
      currentPage = 1; // Reset to first page when searching
      fetchAll();
    }, 300);
  });

  // Handle Pagination
  paginationContainer.addEventListener('click', (e) => {
    if (e.target.classList.contains('pagination-link') && !isFetching) {
      e.preventDefault();
      currentPage = parseInt(e.target.getAttribute('data-page'), 10);
      fetchAll();
    }
  });

  // Handle status filter change
  statusFilter.addEventListener('change', () => {
    if (isFetching) return;
    currentPage = 1; // Reset to first page when changing filter
    fetchAll();
  });

  // Initial fetch
  fetchAll();

  // User Details and Delete functionality
  tbody.addEventListener('click', async (e) => {
    const targetLink = e.target.closest('a');
    if (!targetLink || isFetching) return;

    e.preventDefault();
    const id = targetLink.getAttribute('id');

    if (targetLink.classList.contains('viewLink')) {
      try {
        const response = await fetch(`./backend/account_action.php?view=1&id=${id}`);
        const data = await response.json();
        document.getElementById('js-user-id').textContent = data.id;
        document.getElementById('display-full-name').textContent = `${data.first_name} ${data.last_name}`;
        document.getElementById('display-email').textContent = data.email;
        document.getElementById('display-role').textContent = data.role;
      } catch (error) {
        console.error('Error fetching user details:', error);
        showAlert('error', 'Error', 'Failed to fetch user details. Please try again.');
      }
    } else if (targetLink.classList.contains('deleteLink')) {
      const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to delete this user?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        customClass: {
          confirmButton: 'bg-red-700 hover:bg-red-800 text-white px-5 py-3 mr-4 font-semibold rounded-lg',
          cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white px-5 py-3 font-semibold rounded-lg'
        },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        try {
          const response = await fetch(`./backend/account_action.php?delete=1&id=${id}`);
          const data = await response.text();
          if (data.includes('success')) {
            await showAlert('success', 'Deleted!', 'User deleted successfully.', 'bg-green-700 hover:bg-green-800');
            fetchAll();
          } else {
            throw new Error('Failed to delete user');
          }
        } catch (error) {
          console.error('Error deleting user:', error);
          await showAlert('error', 'Error!', 'Failed to delete user. Please try again.', 'bg-red-700 hover:bg-red-800');
        }
      }
    }
  });
});