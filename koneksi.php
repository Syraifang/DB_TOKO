<?php
require_once __DIR__ . '/classes/respon.php';

class DbConnection {
    private string $host = '127.0.0.1'; 
    private string $user = 'root';
    private string $pass = '';
    private string $db   = 'db_toko';
    private ?mysqli $conn = null;

    public function __construct() {
        $this->init_connect();
    }

    private function init_connect(): void {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);
        if ($this->conn->connect_error) {
            die('Koneksi database gagal: ' . $this->conn->connect_error);
        }
    }

    public function send_query(string $query): Respon {
        $result = $this->conn->query($query);
        if ($this->conn->error) return new Respon(false, $this->conn->error);
        if ($result === true) return new Respon(true, "Sukses");
        return new Respon(true, "Data diambil", $result->fetch_all(MYSQLI_ASSOC));
    }

    public function send_secure_query(string $query, array $params, string $types): Respon {
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return new Respon(false, $this->conn->error);
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result === false) return new Respon(true, "Eksekusi sukses");
        return new Respon(true, "Data diambil", $result->fetch_all(MYSQLI_ASSOC)); 
    }

    public function close_connection(): void {
        if ($this->conn) $this->conn->close();
    }
    
    public function escape_string(string $value): string {
        return $this->conn->real_escape_string($value);
    }
    public function get_last_insert_id(): int {
        return $this->conn->insert_id;
    }
}
?>