<?php

namespace App\controller;

use Core\App;
use Core\JWTHelper;
use \Firebase\JWT\JWT;
use App\traits\Cors;
use Exception;

class ProductsController
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
        if ($res['status']) {
            $products = App::get('database')->selectAll('products', 'Product');
            $categories = App::get('database')->selectAll('categories', 'Category');
            echo json_encode(array("status" => true, "products" => $products, "categories" => $categories));
        } else {
            $this->throwError(404, $res['Error']);
        }
    }

    public function store()
    {
        $this->corsCheck();


        $res = $this->jwtHelper->authenticate($this->jwtHelper->getBearerToken(), $_POST['userId']);
        $user =  App::get('database')->selectWhere('users', 'User', 'id', $_POST['userId']);

        if ($res['status'] && ($user[0]->role == 'Admin')) {
            $file = $this->uploadFile();
            $parameter = [
                'Name' => $_POST['name'],
                'SKU' => uniqid("product", true),
                'Description' => $_POST['description'],
                'Category_id' => $_POST['category'],
                'Price' => $_POST['price'],
                'Image' => "http://" . $_SERVER['HTTP_HOST'] . "/" . $file
            ];

            try {
                App::get('database')->insert('products', $parameter);
            } catch (Exception $e) {
                $this->throwError(404, $e->getMessage());
            }
            echo json_encode(array("status" => true, "message" => "created Product"));
        } else {
            $this->throwError(403, "You need to be a admin");
        }
    }

    public function update()
    {
        $this->corsCheck();


        $res = $this->jwtHelper->authenticate($this->jwtHelper->getBearerToken(), $_POST['userId']);

        $user =  App::get('database')->selectWhere('users', 'User', 'id', $_POST['userId']);

        if ($res['status'] && ($user[0]->role == 'Admin')) {
            $file = null;
            if ($_FILES["image"]['size'] == 0 && $_FILES["image"]['error'] == 0) {

                $file = null;
            } else {
                $file = $this->uploadFile();
            }


            $parameter = [
                'Name' => $_POST['name'],

                'Description' => $_POST['description'],
                'Category_id' => $_POST['category'],
                'Price' => $_POST['price'],
                'id' => $_POST['id']

            ];

            if ($file) {
                $parameter['Image'] = "http://" . $_SERVER['HTTP_HOST'] . "/" . $file;
            }


            try {
                App::get('database')->update('products', $parameter);
            } catch (Exception $e) {
                $this->throwError(404, $e->getMessage());
            }
            echo json_encode(array("status" => true, "message" => "updated product"));
        } else {
            $this->throwError(403, "You need to be a admin");
        }
    }

    protected function uploadFile()
    {
        $target_dir = "resources/images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
            exit;
        }
        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
            exit;
        }
        if ($_FILES["image"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
            exit;
        }
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        return $target_file;
    }

    public function delete()
    {
        $this->corsCheck();


        $res = $this->jwtHelper->authenticate($this->jwtHelper->getBearerToken(), $_POST['userId']);

        $user =  App::get('database')->selectWhere('users', 'User', 'id', $_POST['userId']);

        if ($res['status'] && ($user[0]->role == 'Admin')) {
            try {
                App::get('database')->delete('products', $_POST['id']);
                $products = App::get('database')->selectAll('products', 'Product');
                echo json_encode(array("status" => true, "products" => $products));
            } catch (Exception $e) {
                $this->throwError(404, $e->getMessage());
            }
        } else {
            $this->throwError(403, "You need to be a admin");
        }
    }
}
