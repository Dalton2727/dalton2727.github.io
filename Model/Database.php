<?php
require_once __DIR__ . '/../inc/config.php';

class Database
{
    protected $connection = null;

    public function __construct()
    {
        try {
            error_log("Attempting to connect to database with host: " . DB_HOST);
            // Establish database connection
            $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);

            if (mysqli_connect_errno()) {
                throw new Exception("Could not connect to database: " . mysqli_connect_error());
            }
            
            error_log("Database connection successful");
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Select data from the database
     */
    public function select($query = "", $params = [])
    {
        try {
            error_log("Executing select query: " . $query);
            error_log("With parameters: " . print_r($params, true));
            
            // Execute query with parameters
            $stmt = $this->executeStatement($query, $params);
            
            // Get the result
            $result = $stmt->get_result();
            
            // Log result for debugging
            if ($result === false) {
                throw new Exception("Query execution failed: " . $stmt->error);
            }
            
            // Fetch all rows as an associative array
            $resultArray = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            
            // Log the result to see what it contains
            error_log("Query result: " . print_r($resultArray, true));
            
            // Ensure result is always an array
            if (!is_array($resultArray)) {
                // If it's not an array, log the error and return an empty array
                error_log("Expected an array result, but got: " . gettype($resultArray));
                return [];
            }
            
            // Return the result (array or empty array)
            return $resultArray;
        } catch (Exception $e) {
            // Log error
            error_log("Error in select query: " . $e->getMessage());
            // Return an empty array instead of throwing an exception to avoid breaking the flow
            return [];
        }
    }
    
    


    /**
     * Insert, Update, Delete (Execute statements without fetching results)
     */
    public function execute($query = "", $params = [])
    {
        try {
            // Execute query with parameters
            $stmt = $this->executeStatement($query, $params);
            $stmt->close();
            return true;
        } catch (Exception $e) {
            throw new Exception("Error executing query: " . $e->getMessage());
        }
    }

    /**
     * Executes a prepared statement
     */
    private function executeStatement($query = "", $params = [])
    {
        try {
            // Prepare the statement
            $stmt = $this->connection->prepare($query);
            if ($stmt === false) {
                throw new Exception("Unable to prepare statement: " . $query);
            }

            // Bind parameters dynamically
            if (!empty($params)) {
                $types = '';
                $values = [];
                foreach ($params as $param) {
                    // Ensure that $param is an array with type and value
                    if (count($param) !== 2) {
                        throw new Exception("Invalid parameter format. Expected an array with type and value.");
                    }
                    $types .= $param[0];  // Parameter type (e.g., 's' for string)
                    $values[] = $param[1]; // Parameter value
                }
                // Ensure the number of placeholders matches the number of parameters
                if (substr_count($query, '?') !== count($values)) {
                    throw new Exception("Mismatch between placeholders and parameters.");
                }

                // Bind parameters
                $stmt->bind_param($types, ...$values);
            }

            // Execute the statement
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            throw new Exception("Error preparing or executing statement: " . $e->getMessage());
        }
    }

    /**
     * Close the database connection
     */
    public function close()
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

?>
