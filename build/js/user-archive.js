import { handleDisplayCurrentTime, handleSidebar, openModal, showToaster, Modal } from "./dashboards-main.js";

const userAccountBtn = document.getElementById('js-account-setting');
userAccountBtn.addEventListener('click', () => {
  window.location.href = './user-setting.php';
})

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




document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();

  const tbody = document.getElementById('customer-archive-list');
  const paginationContainer = document.getElementById('pagination-container');
  const searchInput = document.getElementById('js-search-bar');
  const originFilter = document.getElementById('origin-filter');

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

  // FETCH ALL ITEMS AJAX REQUEST
  const fetchAll = async (page = 1, column = 'id', order = 'desc', query = '') => {
    const searchQuery = searchInput.value || query;
    const originQuery = originFilter.value;

    const data = await fetch(`./backend/user-archive_action.php?readAll=1&page=${page}&column=${column}&order=${order}&$query=${searchQuery}&origin=${originQuery}`, {
      method: 'GET',
    });
    const response = await data.json();
    tbody.innerHTML = response.items;
    paginationContainer.innerHTML = response.pagination;
  }
  
  // HANDLE COLUMN SORTING 
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

  // HANDLE SEARCH INPUT
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

  originFilter.addEventListener('change', () => {
    fetchAll();
  })

  // INITIAL CALL
  fetchAll();

  tbody.addEventListener('click', (e) => {
    // TARGET UNARCHIVE LINK
    if (e.target && (e.target.matches('a.unarchiveLink') || e.target.closest('a.unarchiveLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.unarchiveLink') ? e.target : e.target.closest('a.unarchiveLink');
      let archiveId = targetElement.getAttribute('data-archiveId');
      // console.log(id);
      const deleteWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal', 'modal-message');
      deleteWarningModal.show(recoverData, archiveId, 'Do you really want to recover this record?');
    }

    // TARGET DELETE LINK
    if (e.target && (e.target.matches('a.deleteLink') || e.target.closest('a.deleteLink'))) {
      e.preventDefault();
      let targetElement = e.target.matches('a.deleteLink') ? e.target : e.target.closest('a.deleteLink');
      let id = targetElement.getAttribute('id');
      // console.log(id);
      const deleteWarningModal = new Modal('warning-modal', 'confirm-modal', 'close-modal', 'modal-message');
      deleteWarningModal.show(deleteData, id, 'Do you really want to delete this record?');
    }
  });

  // FUNCTION TO RECOVER DATA AJAX REQUEST/
  const recoverData = async (archiveId) => {
    const data = await fetch(`./backend/user-archive_action.php?recover=1&archive_id=${archiveId}`, {
      method: 'GET',
    });

    const response = await data.json();
    if(response.status === 'success') {
      showToaster(response.message, 'check', '#047857', '#065f46');
      fetchAll();
    } else {
      showToaster(response.message, 'exclamation-error', '#dc2626', '#b91c1c');
    }
  }

  // FUNCTION TO DELETE DATA AJAX REQUEST
  const deleteData = async (id) => {
    const data = await fetch(`./backend/user-archive_action.php?delete=1&id=${id}`, {
      method: 'GET',
    });
    const response = await data.json();
    if(response.status === 'success') {
      showToaster(response.message, 'check', '#047857', '#065f46');
      fetchAll();
    } else {
      showToaster(response.message, 'exclamation-error', '#dc2626', '#b91c1c');
    }
  }

});