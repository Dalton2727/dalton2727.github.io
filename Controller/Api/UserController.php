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

        if (strtoupper($requestMethod) == 'GET') {
            try {
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
            } catch (Exception $e) {
                error_log("Error in listAction: " . $e->getMessage());
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output 
        if ($strErrorDesc) {
            $response = json_encode(['error' => $strErrorDesc]);
            $this->sendOutput($response, array('Content-Type: application/json', $strErrorHeader));
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
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    // Configure session cookie
                    session_set_cookie_params([
                        'lifetime' => 0,
                        'path' => '/',
                        'domain' => '',
                        'secure' => false,
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                    
                    $_SESSION["loggedin"] = true;
                    $_SESSION["userid"] = $username;
                    error_log("Session variables set: loggedin = " . $_SESSION["loggedin"] . ", userid = " . $_SESSION["userid"]);

                    $this->sendOutput(json_encode([
                        'success' => true,
                        'username' => $user['username'],
                    ]), [
                        'Content-Type: application/json',
                        'Set-Cookie: PHPSESSID=' . session_id() . '; Path=/; SameSite=Lax'
                    ]);
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
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
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
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
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

    /**
     * "/user/edit" Endpoint - Edit a review
     */
    public function editAction()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Start session if not already started
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // Configure session cookie
                session_set_cookie_params([
                    'lifetime' => 0,
                    'path' => '/',
                    'domain' => '',
                    'secure' => false,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);

                // Get the raw POST data
                $rawData = file_get_contents('php://input');
                error_log("Raw edit request data: " . $rawData);
                
                $data = json_decode($rawData, true);
                error_log("Decoded edit data: " . print_r($data, true));
                
                // Log session status
                error_log("Session status: " . (isset($_SESSION["loggedin"]) ? "Logged in" : "Not logged in"));
                error_log("Session userid: " . ($_SESSION['userid'] ?? 'not set'));

                if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                    $this->sendOutput(
                        json_encode(['error' => 'You must be logged in to edit reviews']),
                        ['Content-Type: application/json', 'HTTP/1.1 401 Unauthorized', 'Set-Cookie: PHPSESSID=' . session_id() . '; Path=/; SameSite=Lax']
                    );
                    return;
                }

                $revid = $data['revid'] ?? '';
                $userid = $data['userid'] ?? '';
                $location = $data['location'] ?? '';
                $meal = $data['meal'] ?? '';
                $rating = $data['rating'] ?? '';

                if (empty($revid) || empty($userid) || empty($location) || empty($meal) || empty($rating)) {
                    $this->sendOutput(
                        json_encode(['error' => 'All fields are required']),
                        ['Content-Type: application/json', 'HTTP/1.1 400 Bad Request', 'Set-Cookie: PHPSESSID=' . session_id() . '; Path=/; SameSite=Lax']
                    );
                    return;
                }

                if ($userid !== $_SESSION['userid']) {
                    $this->sendOutput(
                        json_encode(['error' => 'You can only edit your own reviews']),
                        ['Content-Type: application/json', 'HTTP/1.1 403 Forbidden', 'Set-Cookie: PHPSESSID=' . session_id() . '; Path=/; SameSite=Lax']
                    );
                    return;
                }

                $userModel = new UserModel();
                $result = $userModel->editReview($revid, $userid, $location, $meal, $rating);

                if ($result) {
                    $this->sendOutput(
                        json_encode(['success' => true]),
                        ['Content-Type: application/json', 'HTTP/1.1 200 OK', 'Set-Cookie: PHPSESSID=' . session_id() . '; Path=/; SameSite=Lax']
                    );
                } else {
                    $this->sendOutput(
                        json_encode(['error' => 'Failed to edit review']),
                        ['Content-Type: application/json', 'HTTP/1.1 500 Internal Server Error', 'Set-Cookie: PHPSESSID=' . session_id() . '; Path=/; SameSite=Lax']
                    );
                }
            } else {
                $this->sendOutput(
                    json_encode(['error' => 'Method not supported']),
                    ['Content-Type: application/json', 'HTTP/1.1 405 Method Not Allowed', 'Set-Cookie: PHPSESSID=' . session_id() . '; Path=/; SameSite=Lax']
                );
            }
        } catch (Exception $e) {
            error_log("Error in editAction: " . $e->getMessage());
            $this->sendOutput(
                json_encode(['error' => 'An error occurred while editing the review']),
                ['Content-Type: application/json', 'HTTP/1.1 500 Internal Server Error', 'Set-Cookie: PHPSESSID=' . session_id() . '; Path=/; SameSite=Lax']
            );
        }
    }
     /**
     * "/user/addreview" Endpoint - Add a review
     */
    public function addReviewAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        
        if (strtoupper($requestMethod) != 'POST') {
            $this->sendOutput(
                json_encode(['error' => 'Method not supported']),
                ['Content-Type: application/json', 'HTTP/1.1 422 Unprocessable Entity']
            );
            return;
        }
    
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            
            // Validate required fields
            if (!isset($input['username'], $input['location'], $input['meal'], $input['rating'])) {
                throw new InvalidArgumentException('Missing required fields');
            }
    
            // Sanitize and validate inputs
            $username = trim($input['username']);
            $location = trim($input['location']);
            $meal = trim($input['meal']);
            $rating = filter_var($input['rating'], FILTER_VALIDATE_INT, [
                'options' => [
                    'min_range' => 1,
                    'max_range' => 10
                ]
            ]);
    
            if ($rating === false) {
                throw new InvalidArgumentException('Rating must be integer between 1-10');
            }
    
            $userModel = new UserModel();
            $result = $userModel->insertReview($username, $location, $meal, $rating);
    
            if (!$result) {
                throw new RuntimeException('Failed to insert review');
            }
    
            $this->sendOutput(
                json_encode([
                    'success' => true,
                    'data' => [
                        'username' => $username,
                        'location' => $location,
                        'meal' => $meal,
                        'rating' => $rating
                    ]
                ]),
                ['Content-Type: application/json', 'HTTP/1.1 201 Created']
            );
    
        } catch (InvalidArgumentException $e) {
            $this->sendOutput(
                json_encode(['error' => $e->getMessage()]),
                ['Content-Type: application/json', 'HTTP/1.1 400 Bad Request']
            );
        } catch (Exception $e) {
            error_log("Review Error: " . $e->getMessage());
            $this->sendOutput(
                json_encode(['error' => 'Internal server error']),
                ['Content-Type: application/json', 'HTTP/1.1 500 Internal Server Error']
            );
        }
    }
    
}
?>
