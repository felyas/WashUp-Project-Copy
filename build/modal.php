<!-- Success Modal Overlay -->
<div id="success-modal" class="hidden p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white px-4 py-4 rounded-md shadow-lg w-full max-w-sm flex items-center flex-col">
    <div class="grid grid-cols-4 mb-4">
      <!-- First child taking 1/4 of the parent's width -->
      <div class="col-span-1 flex items-center">
        <div class="flex justify-center items-center col-span-1 bg-green-600 rounded-full w-16 h-16">
          <img class="w-8 h-8" src="./img/icons/circle-success.svg" alt="">
        </div>
      </div>
      <!-- Second child taking 3/4 of the parent's width -->
      <div class="col-span-3">
        <h1 id="modal-title" class="text-lg font-bold mb-2 text-green-700">Success !</h1>
        <p id="modal-message" class="text-md text-gray-500 text-wrap">Booking updated successfully!</p>
      </div>
    </div>

    <div class="w-full flex justify-end items-center space-x-2 text-sm font-semibold">
      <button id="success-confirm-modal" class="hidden bg-green-700 border-2 border-solid border-green-700 text-white hover:bg-green-800 hover:border-green-800 py-2 px-4 rounded transition">
        Yes
      </button>
      <button id="success-close-modal" class="bg-green-700 border-2 border-solid border-green-700 text-white hover:bg-green-800 hover:border-green-800 py-2 px-4 rounded transition">
        Ok
      </button>
    </div>
  </div>
</div>

<!-- Warning Confirmation Modal Overlay -->
<div id="warning-confirmation-modal" class="hidden p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white px-4 py-4 rounded-md shadow-lg w-full max-w-sm flex items-center flex-col">
    <div class="grid grid-cols-4 mb-4">
      <!-- First child taking 1/4 of the parent's width -->
      <div class="col-span-1 flex items-center">
        <div class="flex justify-center items-center col-span-1 bg-[#f9d6a0] rounded-full w-16 h-16">
          <img class="w-8 h-8" src="./img/icons/circle-question.svg" alt="">
        </div>
      </div>
      <!-- Second child taking 3/4 of the parent's width -->
      <div class="col-span-3">
        <h1 id="modal-title" class="text-lg font-bold mb-2">Warning!</h1>
        <p id="modal-message" class="text-md text-gray-500 text-wrap">Do you really want to perform this action?</p>
      </div>
    </div>

    <div class="w-full flex justify-end items-center space-x-2 text-sm font-semibold">
      <button id="warning-confirmation-modal" class="bg-[#e69500] border-2 border-solid border-[#e69500] text-white hover:bg-[#cc8400] hover:border-[#cc8400] py-2 px-4 rounded transition">
        Yes
      </button>
      <button id="warning-confirmation-close-modal" class="bg-white border-2 border-solid border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white py-2 px-4 rounded transition">
        No
      </button>
    </div>
  </div>
</div>

<!-- Warning Modal Overlay -->
<div id="warning-modal" class="hidden p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white px-4 py-4 rounded-md shadow-lg w-full max-w-sm flex items-center flex-col">
    <div class="grid grid-cols-4 mb-4">
      <!-- First child taking 1/4 of the parent's width -->
      <div class="col-span-1 flex items-center">
        <div class="flex justify-center items-center col-span-1 bg-[#f9d6a0] rounded-full w-16 h-16">
          <img class="w-8 h-8" src="./img/icons/triangle-warning.svg" alt="">
        </div>
      </div>
      <!-- Second child taking 3/4 of the parent's width -->
      <div class="col-span-3">
        <h1 id="modal-title" class="text-lg font-bold mb-2">Warning!</h1>
        <p id="modal-message" class="text-md text-gray-500 text-wrap">Do you really want to perform this action?</p>
      </div>
    </div>

    <div class="w-full flex justify-end items-center space-x-2 text-sm font-semibold">
      <button id="confirm-modal" class="bg-[#e69500] border-2 border-solid border-[#e69500] text-white hover:bg-[#cc8400] hover:border-[#cc8400] py-2 px-4 rounded transition">
        Yes
      </button>
      <button id="close-modal" class="bg-white border-2 border-solid border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white py-2 px-4 rounded transition">
        No
      </button>
    </div>
  </div>
</div>

<!-- Delete Warning Modal Overlay -->
<div id="delete-modal" class="hidden p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white px-4 py-4 rounded-md shadow-lg w-full max-w-sm flex items-center flex-col">
    <div class="grid grid-cols-4 mb-4">
      <!-- First child taking 1/4 of the parent's width -->
      <div class="col-span-1 flex items-center">
        <div class="flex justify-center items-center col-span-1 bg-red-500 rounded-full w-16 h-16">
          <img class="w-8 h-8" src="./img/icons/circle-error.svg" alt="">
        </div>
      </div>
      <!-- Second child taking 3/4 of the parent's width -->
      <div class="col-span-3">
        <h1 id="modal-title" class="text-lg font-bold mb-2 text-red-600">Warning !</h1>
        <p id="modal-message" class="text-md text-gray-500 text-wrap">Do you want to delete this item?</p>
      </div>
    </div>

    <div class="w-full flex justify-end items-center space-x-2 text-sm font-semibold">
      <button id="delete-confirm-modal" class="bg-red-600 border-2 border-solid border-red-600 text-white hover:bg-red-700 hover:border-red-700 py-2 px-4 rounded transition">
        Yes
      </button>
      <button id="delete-close-modal" class="bg-white border-2 border-solid border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white py-2 px-4 rounded transition">
        No
      </button>
    </div>
  </div>
</div>

<!-- Error Modal Overlay -->
<div id="error-modal" class="hidden p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white px-4 py-4 rounded-md shadow-lg w-full max-w-sm flex items-center flex-col">
    <div class="grid grid-cols-4 mb-4">
      <!-- First child taking 1/4 of the parent's width -->
      <div class="col-span-1 flex items-center">
        <div class="flex justify-center items-center col-span-1 bg-red-500 rounded-full w-16 h-16">
          <img class="w-8 h-8" src="./img/icons/circle-error.svg" alt="">
        </div>
      </div>
      <!-- Second child taking 3/4 of the parent's width -->
      <div class="col-span-3">
        <h1 id="modal-title" class="text-lg font-bold mb-2 text-red-600">Error !</h1>
        <p id="modal-message" class="text-md text-gray-500 text-wrap">Please complete all required fields !</p>
      </div>
    </div>

    <div class="w-full flex justify-end items-center space-x-2 text-sm font-semibold">
      <button id="error-confirm-modal" class="hidden bg-red-600 border-2 border-solid border-red-600 text-white hover:bg-red-700 hover:border-red-700 py-2 px-4 rounded transition">
        Yes
      </button>
      <button id="error-close-modal" class="bg-red-600 border-2 border-solid border-red-600 text-white hover:bg-red-700 hover:border-red-700 py-2 px-4 rounded transition">
        Ok
      </button>
    </div>
  </div>
</div>

<!-- Delete Warning Modal Overlay -->
<div id="delete-user-modal" class="hidden p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white px-4 py-4 rounded-md shadow-lg w-full max-w-sm flex items-center flex-col">
    <div class="grid grid-cols-4 mb-4">
      <!-- First child taking 1/4 of the parent's width -->
      <div class="col-span-1 flex items-center">
        <div class="flex justify-center items-center col-span-1 bg-[#f9d6a0] rounded-full w-16 h-16">
          <img class="w-8 h-8" src="./img/icons/triangle-warning.svg" alt="">
        </div>
      </div>
      <!-- Second child taking 3/4 of the parent's width -->
      <div class="col-span-3">
        <h1 id="modal-title" class="text-lg font-bold mb-2 text-[#e69500]">Warning !</h1>
        <p id="modal-message" class="text-md text-gray-500 text-wrap">Do you want to delete this user?</p>
      </div>
    </div>

    <div class="w-full flex justify-end items-center space-x-2 text-sm font-semibold">
      <button id="deleteUser-confirm-modal" class="bg-[#e69500] border-2 border-solid border-[#e69500] text-white hover:bg-[#cc8400] hover:border-[#cc8400] py-2 px-4 rounded transition">
        Yes
      </button>
      <button id="deleteUser-close-modal" class="bg-white border-2 border-solid border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white py-2 px-4 rounded transition">
        No
      </button>
    </div>
  </div>
</div>

<!-- Delete Warning Modal Overlay -->
<div id="delete-event-modal" class="hidden p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white px-4 py-4 rounded-md shadow-lg w-full max-w-sm flex items-center flex-col">
    <div class="grid grid-cols-4 mb-4">
      <!-- First child taking 1/4 of the parent's width -->
      <div class="col-span-1 flex items-center">
        <div class="flex justify-center items-center col-span-1 bg-[#f9d6a0] rounded-full w-16 h-16">
          <img class="w-8 h-8" src="./img/icons/triangle-warning.svg" alt="">
        </div>
      </div>
      <!-- Second child taking 3/4 of the parent's width -->
      <div class="col-span-3">
        <h1 id="modal-title" class="text-lg font-bold mb-2 text-[#e69500]">Warning !</h1>
        <p id="modal-message" class="text-md text-gray-500 text-wrap">Do you want to delete this event?</p>
      </div>
    </div>

    <div class="w-full flex justify-end items-center space-x-2 text-sm font-semibold">
      <button id="deleteEvent-confirm-modal" class="bg-[#e69500] border-2 border-solid border-[#e69500] text-white hover:bg-[#cc8400] hover:border-[#cc8400] py-2 px-4 rounded transition">
        Yes
      </button>
      <button id="deleteEvent-close-modal" class="bg-white border-2 border-solid border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white py-2 px-4 rounded transition">
        No
      </button>
    </div>
  </div>
</div>