<?php

namespace App\controller;

use Core\App;
use Core\JWTHelper;

use App\traits\Cors;
use Exception;

class OrdersController
{
    use Cors;
    protected $jwtHelper;

    function __construct()
    {
        $this->jwtHelper = new JWTHelper;
    }

    public function index()
    {
        $this->corsCheck();

        $res = $this->jwtHelper->authenticate($this->jwtHelper->getBearerToken(), $_POST['userId']);
        $user =  App::get('database')->selectWhere('users', 'User', 'id', $_POST['userId']);
        if ($res['status']) {
            if ($user[0]->role == 'Admin') {
                try {
                    $orders = App::get('database')->orderAll();
                    echo json_encode(array("orders" => $orders));
                } catch (Exception $e) {
                    $this->throwError(404, $e->getMessage());
                }
                exit;
            } else {
                try {
                    $orders = App::get('database')->orderCustomer($_POST['userId']);
                    echo json_encode(array("orders" => $orders));
                } catch (Exception $e) {
                    $this->throwError(404, $e->getMessage());
                }
                exit;
            }
        }
    }

    public function store()
    {

        $this->corsCheck();
        $res = $this->jwtHelper->authenticate($this->jwtHelper->getBearerToken(), $_POST['userId']);
        if ($res['status']) {
            $parameter = [
                'user_id' => $_POST['userId'],
                'product_id' => $_POST['product_id'],
                'status' => 'Processing',
            ];

            try {
                App::get('database')->insert('orders', $parameter);
                $orders = App::get('database')->orderCustomer($_POST['userId']);
            } catch (Exception $e) {
                $this->throwError(404, $e->getMessage());
            }
            echo json_encode(array("status" => true, "message" => "created order", "orders" => $orders));
        } else {
            $this->throwError(404, $res['Error']);
        }
    }


    public function update()
    {

        $this->corsCheck();

        $res = $this->jwtHelper->authenticate($this->jwtHelper->getBearerToken(), $_POST['userId']);
        $user =  App::get('database')->selectWhere('users', 'User', 'id', $_POST['userId']);

        if ($res['status'] && ($user[0]->role == 'Admin')) {

            $parameter = [

                'status' => $_POST['status'],
                'id' => $_POST['id']

            ];
            try {
                App::get('database')->update('orders', $parameter);
                $order =  App::get('database')->selectWhere('orders', 'Order', 'id', $_POST['id']);
            } catch (Exception $e) {
                $this->throwError(404, $e->getMessage());
            }
            echo json_encode(array("status" => true, "order" => $order[0]));
        } else {
            $this->throwError(403, "You need to be a admin");
        }
    }
}
