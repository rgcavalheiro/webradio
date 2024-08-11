<?php
include_once 'models/UserModel.php';

class UserController {
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function handleRequest() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $session_id = session_id();

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = $session_id;
            $this->model->registerVisit($ip, $session_id);
        }

        // Atualiza ou insere o usuário online
        $this->model->updateUserOnline($ip, $session_id);

        // Limpa sessões antigas
        $this->model->cleanupOldUsers();

        if (isset($_GET['get_online_users'])) {
            echo $this->model->countOnlineUsers();
            exit();
        }

        return [
            'onlineUsers' => $this->model->countOnlineUsers(),
            'totalVisits' => $this->model->countTotalVisits()
        ];
    }
}
?>
