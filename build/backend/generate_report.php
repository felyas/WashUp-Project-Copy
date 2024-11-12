<?php

require __DIR__ . '/../../vendor/autoload.php';
require_once './delivery_db.php';

$db = new Database();

use Dompdf\Dompdf;
use Dompdf\Options;

$period = $_POST['period'] ?? 'today';
$bookingData = $db->getTotalBookings($period);
$complaints = $db->getTotalComplaints($period);
$users = $db->getTotalUsers($period);

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultFont', 'Helvetica');
$options->setChroot(__DIR__); // Set chroot to two levels up

$dompdf = new Dompdf($options);

// Get period label for the report
$periodLabel = [
  'today' => 'Today',
  'yesterday' => 'Yesterday',
  'last-week' => 'Last Week',
  'last-month' => 'Last Month'
][$period];

// Create HTML content
$htmlContent = '
<!DOCTYPE html>
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0; }
    .header {
      text-align: center;
      border-bottom: 2px solid #ddd;
      padding-bottom: 20px;
      margin-bottom: 20px;
    }
    .header img { width: 80px; margin-bottom: 10px; }
    .report-title { font-size: 24px; font-weight: bold; color: #555; }
    .report-address, .period { font-size: 14px; color: #777; margin-top: 5px; }
    
    /* Main report table styles */
    .report-table {
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
      border-collapse: collapse;
    }
    .report-table th, .report-table td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
      font-size: 14px;
    }
    .report-table th {
      background-color: #f3f3f3;
      font-weight: bold;
      color: #555;
    }
    .report-table td { background-color: #f9f9f9; color: #333; }
    
    .date-generated {
      margin-top: 30px;
      font-size: 12px;
      color: #888;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="header">
    <img src="original-logo.png" style="width: 100px">
    <div class="report-title">WashUp Laundry Report</div>
    <div class="report-address">Blk 1 Lot 2, Morales Subdivision, Calamba Laguna</div>
    <div class="period">Period: ' . $periodLabel . '</div>
  </div>
  
  <!-- Data Table for Report -->
  <table class="report-table">
    <tr>
      <th>Booking</th>
      <th>Total</th>
    </tr>
    <tr>
      <td>Total Bookings</td>
      <td>' . $bookingData['total'] . '</td>
    </tr>
    <tr>
      <td>Complete Bookings</td>
      <td>' . $bookingData['complete'] . '</td>
    </tr>
  </table>

  <!-- Data Table for Report -->
  <table class="report-table">
    <tr>
      <th>Complaints</th>
      <th>Total</th>
    </tr>
    <tr>
      <td>Total Complaints</td>
      <td>' . $complaints['total'] . '</td>
    </tr>
    <tr>
      <td>Resolve Complaints</td>
      <td>' . $complaints['resolved'] . '</td>
    </tr>
  </table>

  <table class="report-table">
    <tr>
      <th>Users</th>
      <th>Total</th>
    </tr>
    <tr>
      <td>Total User</td>
      <td>' . $complaints['total'] . '</td>
    </tr>
  </table>
  
  <div class="date-generated">
    Generated on: ' . date('Y-m-d H:i:s') . '
  </div>
</body>
</html>';


$dompdf->setPaper("A4", "landscape");

$dompdf->loadHtml($htmlContent);

$dompdf->render();
// Generate unique filename
$filename = 'booking_report_' . $period . '_' . date('Y-m-d_His') . '.pdf';

// Stream the file to the browser
$dompdf->stream($filename, array("Attachment" => 0));
