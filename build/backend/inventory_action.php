<?php
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

  if ($db->addItem($product_name, $bar_code, $quantity, $max_quantity)) {
    echo json_encode(['status' => 'success', 'message' => 'Item added successfully']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add item']);
  }
  exit;
}

// Handle View Items Ajax Request with Pagination, Sorting, and Search
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
      $output .= generateItemRow($row);
    }
    $paginationOutput = generatePagination($page, $totalPages);
  } else {
    $output = '<tr><td colspan="8" class="text-center py-4 text-gray-200">No Items Found!</td></tr>';
    $paginationOutput = '';
  }

  echo json_encode([
    'items' => $output,
    'pagination' => $paginationOutput,
  ]);
  exit;
}

// Handle View Item Detail Ajax Request
if (isset($_GET['item-detail'])) {
  $product_id = $_GET['product_id'];
  $itemDetail = $db->itemDetail($product_id);
  echo json_encode($itemDetail);
  exit;
}

// Handle Update Item Ajax Request
if (isset($_POST['update'])) {
  $id = $util->testInput($_POST['product_id']);
  $quantity = $util->testInput($_POST['quantity']);

  if ($db->updateItem($id, $quantity)) {
    echo json_encode(['status' => 'success', 'message' => 'Item updated successfully']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update item']);
  }
  exit;
}

// Handle Delete Item Ajax Request
if (isset($_GET['delete'])) {
  $product_id = $_GET['product_id'];

  if ($db->deleteItem($product_id)) {
    echo json_encode(['status' => 'success', 'message' => 'Item deleted successfully']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete item']);
  }
  exit;
}

function generateItemRow($row)
{
  return '
        <tr class="border-b border-gray-200">
            <td class="px-4 py-2">' . htmlspecialchars($row['product_id']) . '</td>
            <td class="px-4 py-2">' . htmlspecialchars($row['product_name']) . '</td>
            <td class="px-4 py-2">' . htmlspecialchars($row['quantity']) . '</td>
            <td class="px-4 py-2">' . htmlspecialchars($row['status']) . '</td>
            <td class="min-w-[100px] h-auto flex items-center justify-center space-x-2 flex-grow">
                <a href="#" id="' . htmlspecialchars($row['product_id']) . '" class="editModalTrigger px-3 py-2 bg-green-700 hover:bg-green-800 rounded-md transition editLink">
                    <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                </a>
                <a href="#" id="' . htmlspecialchars($row['product_id']) . '" class="px-3 py-2 bg-red-700 hover:bg-red-800 rounded-md transition deleteLink">
                    <img class="w-4 h-4" src="./img/icons/trash.svg" alt="delete">
                </a>
            </td>
        </tr>
    ';
}

function generatePagination($page, $totalPages)
{
  $output = '<div class="flex justify-center items-center space-x-2">';

  // Previous button
  if ($page > 1) {
    $output .= '<a href="#" data-page="' . ($page - 1) . '" class="pagination-link bg-gray-200 text-gray-600 px-4 py-2 rounded">Previous</a>';
  } else {
    $output .= '<span class="bg-gray-300 text-gray-500 px-4 py-2 rounded">Previous</span>';
  }

  // Next button
  if ($page < $totalPages) {
    $output .= '<a href="#" data-page="' . ($page + 1) . '" class="pagination-link bg-gray-200 text-gray-600 px-4 py-2 rounded">Next</a>';
  } else {
    $output .= '<span class="bg-gray-300 text-gray-500 px-4 py-2 rounded">Next</span>';
  }

  $output .= '</div>';
  return $output;
}
