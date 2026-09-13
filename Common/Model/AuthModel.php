<?php

require_once __DIR__ . "/../Config/db.php";

class AuthModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = getConnection();
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function emailExists($email)
    {
        return $this->findByEmail($email) !== null;
    }

    public function register($name, $email, $phone, $passwordHash, $role)
    {
        $status = "active";
        $sql = "INSERT INTO users (name, email, phone, password, role, status)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $email, $phone, $passwordHash, $role, $status);
        return $stmt->execute();
    }
}
