<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
class UserModel extends Database
{
    public function getUsers($limit)
    {
        return $this->select("SELECT * FROM reviews LIMIT ?", ["i", $limit]);
    }
    public function getUserByUsername($username)
    {
        $result = $this->select("SELECT * FROM users WHERE username = ?", ["s", $username]);
        return $result[0] ?? null;
    }
    public function loginUser($username, $password)
    {
        // Fetch the user from the database
        $result = $this->select(
            "SELECT * FROM users WHERE username = ?",
            ["s", $username]
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
}
?>