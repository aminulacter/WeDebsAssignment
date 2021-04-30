<?php

namespace App\controller;

use \Firebase\JWT\JWT;
use Core\App;
use App\traits\Cors;
use Exception;

class LoginController
{

    use Cors;
    public function login()
    {

        $this->corsCheck();

        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];

            $user = App::get('database')->login('users', 'User', $email);

            if (count($user) > 0) {

                if (strcmp($user[0]->password, $_POST['password'])) {

                    echo json_encode(array("status" => false, "Error" => "ID or password error"));
                } else {

                    echo json_encode($this->createToken($user[0]));
                }
            } else {

                echo json_encode(array("status" => false, "Error" => "ID or password error"));
            }
        } else {

            echo json_encode(array("status" => false, "Error" => "Please complete the form"));
        }
    }

    protected function createToken($user)
    {
        $payload = array(
            'iss' => $_SERVER['HOST_NAME'],
            'exp' => time() + 6000,
            'uId' => $user->id,


        );

        try {
            $jwt = JWT::encode($payload, App::get('jwtKey'), 'HS256');
            return array(
                "status" => true,
                "Token" => $jwt,
                "user" => $user,
            );
        } catch (\UnexpectedValueException $e) {
            $res = array("status" => false, "Error" => $e->getMessage());
        }
    }
    public function register()
    {
        $this->corsCheck();
        if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['passwordconfirm']) && isset($_POST['name'])) {
            if (strlen($_POST['password']) < 6) {
                $this->throwError(401, "password too  small");
            }

            if (strcmp($_POST['password'], $_POST['passwordconfirm']) != 0) {
                $this->throwError(401, "password doesnt match");
            }
            if (strlen($_POST['name']) < 5) {
                $this->throwError(401, "name too  small");
            }
            if (!filter_var($_POST['email'])) {
                $this->throwError(401, "not a valid email");
            }
            $user =  App::get('database')->login('users', 'User', $_POST['email']);
            if ($user[0]) {
                $this->throwError(401, "Already registered");
            }
            $parameter = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'role' => 'Customer'

            ];

            try {
                App::get('database')->insert('users', $parameter);
                echo json_encode(array("status" => true, "message" => "Registration Complete, Login to continue"));
            } catch (Exception $e) {
                $this->throwError(404, $e->getMessage());
            }
        } else {
            $this->throwError(401, "Complete the form");
        }
    }
}
