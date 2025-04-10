<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class UserModel extends Database
{
    /**
     * Get list of users (for reviews or other data).
     */
    public function getUsers($limit)
    {
        try {
            // Perform the database query and fetch results
            $query = "SELECT * FROM reviews LIMIT ?";
            $params = [['i', $limit]];
            $result = $this->select($query, $params);
            return $result;  // Ensure this is always an array
        } catch (Exception $e) {
            // Handle the exception or return an empty array
            return [];
        }
    }
    

    /**
     * Get a user by their username.
     */
    public function getUserByUsername($username)
    {
        $result = $this->select("SELECT * FROM users WHERE username = ?", ["s", $username]);
        return $result[0] ?? null;
    }

    /**
     * Login a user by validating the username and password.
     */
    public function loginUser($username, $password)
    {
        // Fetch the user from the database
        $result = $this->select(
            "SELECT * FROM users WHERE username = ?",
            [["s", $username]]

        );

        // If the user exists, verify the password
        if (!empty($result)) {
            $user = $result[0]; // Assuming the first result is the correct one
            if (password_verify($password, $user['password'])) {
                return $user; // Password is correct, return the user data
            }
        }

        return false; // Invalid credentials
    }

    /**
     * Create a new user with hashed password.
     */
   public function createUser($username, $password)
{
    try {
        // Check if user already exists
        $existingUser = $this->select("SELECT * FROM users WHERE username = ?", [['s', $username]]);
        if (!empty($existingUser)) {
            return false; // Username taken
        }


        // Insert user using correct parameter binding
        $this->execute("INSERT INTO users (username, password) VALUES (?, ?)", [
            ['s', $username],
            ['s', $password]
        ]);

        return true;
    } catch (Exception $e) {
        error_log("Error in createUser: " . $e->getMessage());
        return false;
    }
}

    
}
?>