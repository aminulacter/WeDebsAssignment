<?php

namespace Core;

use \Firebase\JWT\JWT;

class JWTHelper
{

    public function authenticate($token, $Curret_User_id)
    {
        try {
            $decoded = JWT::decode($token, App::get('jwtKey'), array('HS256'));
            $payload = json_decode(json_encode($decoded), true);

            if ($payload['uId'] == $Curret_User_id) {
                $res = array("status" => true);
            } else {
                $this->throwError(411, "Invalid Token or Token Exipred, So Please login Again!");
                //$res = array("status" => false, "Code" => 411, "Error" => "Invalid Token or Token Exipred, So Please login Again!");
            }
        } catch (\UnexpectedValueException $e) {
            $res = array("status" => false, "Code" => 411, "Error" => $e->getMessage());
            $this->throwError(411, $e->getMessage());
            //$this->throwError(404, $e->getMessage());
        }
        return $res;
    }

    /**
     * get access token from header
     * */
    public function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        $this->throwError(402, 'Access Token Not found');
    }
    /**
     * Get hearder Authorization
     * */
    public function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    public function throwError($code, $message)
    {
        header("content-type: application/json");
        $errorMsg = json_encode(['error' => ['status' => $code, 'message' => $message]]);
        echo $errorMsg;
        exit;
    }
}
