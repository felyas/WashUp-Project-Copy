<?php

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

class Config
{
    private $dbHost;
    private $dbUser;
    private $dbPass;
    private $dbName;
    private $dsn;

    protected $conn = null;

    // Method for connection to the database
    public function __construct()
    {
        // Load environment variables into class properties
        $this->dbHost = $_ENV["DB_HOST"];
        $this->dbUser = $_ENV["DB_USER"];
        $this->dbPass = $_ENV["DB_PASS"];
        $this->dbName = $_ENV["DB_NAME"];

        // Set DSN using loaded values
        $this->dsn = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName;

        try {
            $this->conn = new PDO($this->dsn, $this->dbUser, $this->dbPass);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            //   echo 'Success';
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Method to return the connection
    public function getConnection()
    {
        return $this->conn;
    }
}
