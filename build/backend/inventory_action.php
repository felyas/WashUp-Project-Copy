<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once './inventory_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

// Handle Add Items Ajax Request
if (isset($_POST['add'])) {
  $product_name = $util->testInput($_POST['product']);
  $bar_code = $util->testInput($_POST['bar_code']);
  $quantity = $util->testInput($_POST['quantity']);
  $max_quantity = $quantity;

  // Call the method in db.php to handle the insertion
  $result = $db->addItem($product_name, $bar_code, $quantity, $max_quantity);

  // Check the result and return a JSON response
  if ($result) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Item added successfully.'
    ]);
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => 'Failed to add item.'
    ]);
  }
  exit();
}

// Add Quantity Ajax Request
if (isset($_POST['add-quantity'])) {
  $product_name = $util->testInput($_POST['product']);
  $bar_code = $util->testInput($_POST['bar_code']);
  $quantity = $util->testInput($_POST['quantity']);
  $newMaxQuantity;

  $item = $db->productExist($bar_code);
  if ($item) {
    $current_quantity = $item['quantity'];
    $newMaxQuantity = $current_quantity +  $quantity;
  }

  $result = $db->addQuantity($newMaxQuantity, $bar_code);
  if ($result) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Item added successfully.'
    ]);
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => 'Failed to add item.'
    ]);
  }
  exit();
}

if (isset($_GET['get-current-critical-point'])) {
  $currentCriticalPoint = $db->getCurrentCriticalPoint();

  if ($currentCriticalPoint) {
    echo json_encode([
      'status' => 'success',
      'current_critical_point' => $currentCriticalPoint['setting_value'],
    ]);
  } else {
    echo json_encode([
      'status' => 'error',
    ]);
  }
}

$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['criticalPoint'])) {
  $newCriticalPoint = (int) $data['criticalPoint'];

  // echo json_encode([
  //   'new_critical_point' => $newCriticalPoint,
  // ]);

  $result = $db->updateCriticalPoint($newCriticalPoint);
  if ($result) {
    echo json_encode([
      'status' => 'success',
    ]);
  } else {
    echo json_encode([
      'status' => 'error',
    ]);
  }
}

// Fetch critical point from settings table
$criticalPoint = $db->getSettingValue('critical_point') / 100;

if (isset($_GET['readAll'])) {
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = 7;
  $start = ($page - 1) * $limit;

  $column = isset($_GET['column']) ? $_GET['column'] : 'id';
  $order = isset($_GET['order']) ? $_GET['order'] : 'desc';
  $query = isset($_GET['query']) ? $_GET['query'] : '';
  $status = isset($_GET['status']) ? $_GET['status'] : '';

  // Get filtered and paginated items
  $items = $db->readAll($start, $limit, $column, $order, $query, $status);
  $totalRows = $db->getTotalRows($query, $status);
  $totalPages = ceil($totalRows / $limit);

  $output = '';
  if ($items) {
    foreach ($items as $row) {
      $maxQuantity = $row['max_quantity']; // Fetch this column from your database
      $quantity = $row['quantity'];
      $currentStatus = $row['status']; // Get current status

      // Check if the quantity is less than 15% of the max quantity
      if ($quantity < $criticalPoint * $maxQuantity) {
        // Update status to 'critical' in the database
        if ($currentStatus !== 'critical') {
          $db->updateItemStatus($row['product_id'], 'critical'); // Update to 'critical'
          $row['status'] = 'critical'; // Update status for display
        }
      } else {
        // Update status back to 'good' in the database
        if ($currentStatus !== 'good') {
          $db->updateItemStatus($row['product_id'], 'good'); // Update to 'good'
          $row['status'] = 'good'; // Update status for display
        }
      }

      // Determine the color classes based on the status
      switch ($row['status']) {
        case 'good':
          $statusClasses = 'bg-green-700 text-white';
          break;
        case 'critical':
          $statusClasses = 'bg-red-700 text-white';
          break;
        default:
          $statusClasses = 'bg-gray-700 text-white';
          break;
      }

      $output .= '
              <tr class="border-b border-gray-200">
                  <td class="px-4 py-2">' . htmlspecialchars($row['product_id']) . '</td>
                  <td class="px-4 py-2">' . htmlspecialchars($row['product_name']) . '</td>
                  <td class="px-4 py-2">' . htmlspecialchars($row['quantity']) . '</td>
                  <td class="px-4 py-2">
                    <div class="sm:w-1/2 py-1 ' . $statusClasses . ' font-bold rounded-lg text-center text-xs">
                      ' . strtoupper($row['status']) . '
                    </div>
                  </td>
                  <td class="min-w-[100px] h-auto flex items-center justify-start space-x-2 flex-grow">
                    <div class="flex relative group">
                      <a href="#" id="' . htmlspecialchars($row['product_id']) . '" class="editModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition editLink">
                          <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                      </a>
                      <!-- Tooltip -->
                      <span class="absolute hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded shadow-md top-9 right-0 transform -translate-x-1/2 whitespace-nowrap z-50">
                        Edit Item
                      </span>
                    </div>
                      
                      <div class="flex relative group">
                        <a href="#" data-origin="inventory" 
                        data-key="product_id" data-value="'.$row['product_id'].'"
                        id="' . htmlspecialchars($row['product_id']) . '" class="px-3 py-2 bg-red-700 hover:bg-red-800 rounded-md transition deleteLink">
                          <img class="w-4 h-4" src="./img/icons/trash.svg" alt="delete">
                        </a>
                        <!-- Tooltip -->
                        <span class="absolute hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded shadow-md top-9 right-0 transform -translate-x-1/2 whitespace-nowrap z-50">
                          Delete Item
                        </span>
                      </div>
                      
                  </td>
              </tr>';
    }

    $paginationOutput = '<div class="flex justify-center items-center space-x-2">';

    // Previous button
    if ($page > 1) {
      $paginationOutput .= '<a href="#" data-page="' . ($page - 1) . '" class="pagination-link bg-gray-200 text-gray-600 px-4 py-2 rounded">Previous</a>';
    } else {
      $paginationOutput .= '<span class="bg-gray-300 text-gray-500 px-4 py-2 rounded">Previous</span>';
    }

    // Next button
    if ($page < $totalPages) {
      $paginationOutput .= '<a href="#" data-page="' . ($page + 1) . '" class="pagination-link bg-gray-200 text-gray-600 px-4 py-2 rounded">Next</a>';
    } else {
      $paginationOutput .= '<span class="bg-gray-300 text-gray-500 px-4 py-2 rounded">Next</span>';
    }

    $paginationOutput .= '</div>';

    echo json_encode([
      'items' => $output,
      'pagination' => $paginationOutput,
    ]);
  } else {
    echo json_encode([
      'items' => '<tr><td colspan="8" class="text-center py-4 text-gray-200">No Items Found!</td></tr>',
      'pagination' => ''
    ]);
  }
  exit();
}



// Handle View Item Detail Ajax Request
if (isset($_GET['item-detail'])) {
  $product_id = $_GET['product_id'];
  $itemDetail = $db->itemDetail($product_id);
  echo json_encode($itemDetail);
  exit();
}

// Handle Update Item Ajax Request
if (isset($_POST['update'])) {
  $id = $util->testInput($_POST['product_id']);
  $quantity = $util->testInput($_POST['quantity']);

  $result = $db->updateItem($id, $quantity);

  if ($result === true) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Item updated successfully.'
    ]);
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => 'Failed to update item.'
    ]);
  }
  exit();
}

// Handle Delete Item Ajax Request
if (isset($_GET['delete'])) {
  $product_id = $_GET['product_id'];

  $result = $db->deleteItem($product_id);

  if ($result === true) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Item deleted successfully.'
    ]);
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => 'Failed to delete item.'
    ]);
  }
  exit();
}

// Handle Product Checking If Already Exist Ajax Request
if (isset($_GET['isExist'])) {
  $barcode = $_GET['barcode'];

  $exist = $db->productExist($barcode);

  if ($exist) {
    echo json_encode([
      'status' => 'success',
      'item' => $exist,
    ]);
  } else {
    echo json_encode([
      'status' => 'item not found',
    ]);
  }
}
