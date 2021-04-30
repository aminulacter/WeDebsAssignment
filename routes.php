<?php

$router->post('login', 'LoginController@login');
$router->post('register', 'LoginController@register');
$router->post('products', 'ProductsController@index');
$router->post('storeProduct', 'ProductsController@store');
$router->post('editProduct', 'ProductsController@update');
$router->post('deleteProduct', 'ProductsController@delete');

$router->post('orders', 'OrdersController@index');
$router->post('createOrder', 'OrdersController@store');
$router->post('editOrder', 'OrdersController@update');
