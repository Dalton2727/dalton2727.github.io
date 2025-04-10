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
                $arrUsers = $userModel->getUsers($intLimit);
                $responseData = json_encode($arrUsers);
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        // send output 
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
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
            echo json_encode(["success" => true, "message" => "User created successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "User already exists"]);
        }
    }
}
?>