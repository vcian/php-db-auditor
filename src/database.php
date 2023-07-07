<?php
require_once 'config.php';

function createConnection()
{
    try {
        // Create a new database connection
        $connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Check if the connection was successful
        if (!$connection) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        // Get the database name
        $databaseName = mysqli_select_db($connection, DB_NAME);
        if (!$databaseName) {
            die("Failed to get database name: " . mysqli_error($connection));
        }
        return $connection;

    } catch (PDOException $e) {
        // Handle the database connection error
        die("Database connection failed: " . $e->getMessage());
    }
}

?>