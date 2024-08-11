<?php
include_once 'config/config.php';
include_once 'models/UserModel.php';
include_once 'controllers/UserController.php';

$model = new UserModel($conn);
$controller = new UserController($model);
$data = $controller->handleRequest();

include 'views/index.php';
?>