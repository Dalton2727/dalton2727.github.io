<?php
class UserController extends BaseController
{
    /** 
     * "/user/list" Endpoint - Get list of users 
     */
    public function listAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        
        try {
            if (strtoupper($requestMethod) == 'GET') {
                $userModel = new UserModel();
                $intLimit = 10;
                if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                    $intLimit = $arrQueryStringParams['limit'];
                }
                
                error_log("Attempting to fetch reviews with limit: " . $intLimit);
                $arrUsers = $userModel->getUsers($intLimit);
                
                if ($arrUsers === null) {
                    throw new Exception("Failed to fetch reviews from database");
                }
                
                $responseData = json_encode($arrUsers);
                $this->sendOutput(
                    $responseData,
                    array('Content-Type: application/json', 'HTTP/1.1 200 OK')
                );
                return;
            } else {
                throw new Exception('Method not supported');
            }
        } catch (Exception $e) {
            error_log("Error in listAction: " . $e->getMessage());
            $strErrorDesc = $e->getMessage();
            $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }

        if ($strErrorDesc) {
            $this->sendOutput(
                json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    /**
     * "/user/login" Endpoint - Login a user
     */
    public function loginAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        
        if (strtoupper($requestMethod) == 'POST') {
            $postData = json_decode(file_get_contents("php://input"), true);
            $username = $postData['username'] ?? '';
            $password = $postData['password'] ?? '';
            error_log("Login attempt: username = '$username', password = '$password'");
    
            if ($username && $password) {
                $userModel = new UserModel();
                $user = $userModel->loginUser($username, $password);
    
                if ($user) {
                    // Set session variables
                    session_start();
                    $_SESSION["loggedin"] = true;
                    $_SESSION["userid"] = $username;
                    error_log("Session variables set: loggedin = " . $_SESSION["loggedin"] . ", userid = " . $_SESSION["userid"]);
                    
                    $this->sendOutput(json_encode([
                        'success' => true,
                        'username' => $user['username'],
                    ]), ['Content-Type: application/json']);
                    return;
                } else {
                    $this->sendOutput(json_encode([
                        'success' => false,
                        'message' => 'Invalid credentials'
                    ]), ['Content-Type: application/json']);
                    return;
                }
            } else {
                $this->sendOutput(json_encode([
                    'success' => false,
                    'message' => 'Username or password missing'
                ]), ['Content-Type: application/json']);
                return;
            }
        } else {
            $this->sendOutput(json_encode([
                'error' => 'Method not supported'
            ]), ['Content-Type: application/json', 'HTTP/1.1 422 Unprocessable Entity']);
        }
    }

    /**
     * "/user/signup" Endpoint - Signup a user
     */
    public function signupAction()
    {
        session_start();
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['username']) || !isset($data['password'])) {
            echo json_encode(["success" => false, "message" => "Username and password required"]);
            return;
        }

        $username = $data['username'];
        $password = $data['password'];

        // Hash the password before storing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $userModel = new UserModel();
        $result = $userModel->createUser($username, $hashedPassword);

        if ($result) {
            // Automatically log in the user after successful signup
            $_SESSION["loggedin"] = true;
            $_SESSION["userid"] = $username;
            error_log("Session variables set after signup: loggedin = " . $_SESSION["loggedin"] . ", userid = " . $_SESSION["userid"]);
            
            echo json_encode(["success" => true, "message" => "User created successfully", "username" => $username]);
        } else {
            echo json_encode(["success" => false, "message" => "User already exists"]);
        }
    }

    /**
     * "/user/delete" Endpoint - Delete a review
     */
    public function deleteAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        
        if (strtoupper($requestMethod) == 'POST') {
            try {
                session_start();
                $postData = json_decode(file_get_contents("php://input"), true);
                $revid = $postData['revid'] ?? '';
                $userid = $postData['userid'] ?? '';

                error_log("Delete review attempt - revid: $revid, userid: $userid");
                error_log("Session status: " . (isset($_SESSION["loggedin"]) ? "Logged in" : "Not logged in"));
                error_log("Session userid: " . ($_SESSION['userid'] ?? 'not set'));

                if (empty($revid) || empty($userid)) {
                    throw new Exception('Review ID and User ID are required');
                }

                if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
                    throw new Exception('You must be logged in to delete reviews');
                }

                if ($userid !== $_SESSION['userid']) {
                    throw new Exception('You can only delete your own reviews');
                }

                $userModel = new UserModel();
                $result = $userModel->deleteReview($revid, $userid);

                if ($result) {
                    $response = json_encode(['success' => true, 'message' => 'Review deleted successfully']);
                    error_log("Sending success response: " . $response);
                    $this->sendOutput($response, array('Content-Type: application/json', 'HTTP/1.1 200 OK'));
                    return;
                } else {
                    throw new Exception('Failed to delete review');
                }
            } catch (Exception $e) {
                error_log("Error in deleteAction: " . $e->getMessage());
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        if ($strErrorDesc) {
            $response = json_encode(['error' => $strErrorDesc]);
            error_log("Sending error response: " . $response);
            $this->sendOutput($response, array('Content-Type: application/json', $strErrorHeader));
        }
    }

    public function editAction() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'You must be logged in to edit reviews']);
                    return;
                }

                $data = json_decode(file_get_contents('php://input'), true);
                $revid = $data['revid'] ?? '';
                $userid = $data['userid'] ?? '';
                $location = $data['location'] ?? '';
                $meal = $data['meal'] ?? '';
                $rating = $data['rating'] ?? '';

                if (empty($revid) || empty($userid) || empty($location) || empty($meal) || empty($rating)) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'All fields are required']);
                    return;
                }

                if ($userid !== $_SESSION['userid']) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'You can only edit your own reviews']);
                    return;
                }

                $userModel = new UserModel();
                $result = $userModel->editReview($revid, $userid, $location, $meal, $rating);
                
                if ($result) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'Failed to edit review']);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Method not supported']);
            }
        } catch (Exception $e) {
            error_log("Error in editAction: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'An error occurred while editing the review']);
        }
    }
}
?>