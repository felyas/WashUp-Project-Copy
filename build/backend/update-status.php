<?php

require_once './db_connection.php'; // Include the database connection class

class BookingUpdater extends Database
{
    public function updateDeliveredToComplete()
    {
        // SQL query to update bookings that have a 'Delivered' status for more than 24 hours
        $sql = "UPDATE booking
                SET status = 'complete',
                is_read = :is_read,
                WHERE status = 'delivered' 
                AND TIMESTAMPDIFF(HOUR, delivery_date, NOW()) > 24";

        try {
            // Prepare and execute the query using PDO
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([ 'is_read' => 0]);

            // Check how many rows were updated
            $rowsUpdated = $stmt->rowCount();
            echo "$rowsUpdated booking(s) status updated to 'Complete'.";
        } catch (PDOException $e) {
            echo "Error updating records: " . $e->getMessage();
        }
    }
}

// Create an instance of the BookingUpdater class
$updater = new BookingUpdater();
$updater->updateDeliveredToComplete(); // Call the method to update bookings
