<?php
class UserModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function registerVisit($ip, $session_id) {
        // Insere uma nova visita no banco de dados
        $stmt = $this->conn->prepare("INSERT INTO visitas (ip, session_id) VALUES (?, ?)");
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param("ss", $ip, $session_id);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
        } else {
            error_log("Visit registered: IP = $ip, Session ID = $session_id");
        }
        $stmt->close();
    }

    public function updateUserOnline($ip, $session_id) {
        // Insere ou atualiza a última atividade do usuário
        $stmt = $this->conn->prepare("INSERT INTO usuarios_online (ip, session_id, last_activity) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE last_activity = NOW()");
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param("ss", $ip, $session_id);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
        } else {
            error_log("User online updated: IP = $ip, Session ID = $session_id");
        }
        $stmt->close();
    }

    public function cleanupOldUsers() {
        // Remove sessões que não estão ativas há mais de 5 minutos
        $timeout = 300; // 5 minutos
        $stmt = $this->conn->prepare("DELETE FROM usuarios_online WHERE last_activity < NOW() - INTERVAL ? SECOND");
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param("i", $timeout);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
        } else {
            error_log("Old users cleaned up");
        }
        $stmt->close();
    }

    public function countOnlineUsers() {
        // Conta as sessões ainda ativas (última atividade dentro de 5 minutos)
        $timeout = 300; // 5 minutos
        $stmt = $this->conn->prepare("SELECT COUNT(DISTINCT session_id) AS online_users FROM usuarios_online WHERE last_activity >= NOW() - INTERVAL ? SECOND");
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param("i", $timeout);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
        } else {
            error_log("Counting online users...");
        }
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        error_log("Online users: " . $row['online_users']);
        return $row['online_users'];
    }

    public function countTotalVisits() {
        // Conta o número total de visitas registradas
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total_visits FROM visitas");
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
        }
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
        } else {
            error_log("Counting total visits...");
        }
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        error_log("Total visits: " . $row['total_visits']);
        return $row['total_visits'];
    }
}
?>
