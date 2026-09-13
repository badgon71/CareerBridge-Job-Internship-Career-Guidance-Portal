<?php

require_once __DIR__ . "/../../Common/Config/db.php";

class AdminModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = getConnection();
    }

    public function getDashboardCounts()
    {
        $data = [
            "users" => 0,
            "categories" => 0,
            "jobs" => 0
        ];

        $result = $this->conn->query("SELECT COUNT(*) AS total FROM users");
        if ($result) {
            $data["users"] = (int)$result->fetch_assoc()["total"];
        }

        $result = $this->conn->query("SELECT COUNT(*) AS total FROM categories");
        if ($result) {
            $data["categories"] = (int)$result->fetch_assoc()["total"];
        }

        $result = $this->conn->query("SELECT COUNT(*) AS total FROM jobs");
        if ($result) {
            $data["jobs"] = (int)$result->fetch_assoc()["total"];
        }

        return $data;
    }

    public function getRecentCategories($limit = 5)
    {
        $limit = (int)$limit;
        if ($limit < 1) {
            $limit = 5;
        }

        $sql = "SELECT category_id, category_name, description, created_at
                FROM categories
                ORDER BY category_id DESC
                LIMIT " . $limit;

        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getCategories()
    {
        $sql = "SELECT category_id, category_name, description, created_at
                FROM categories
                ORDER BY category_name ASC";

        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getCategoryById($categoryId)
    {
        $sql = "SELECT category_id, category_name, description
                FROM categories
                WHERE category_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function categoryNameExists($categoryName, $excludeId = 0)
    {
        if ($excludeId > 0) {
            $sql = "SELECT category_id
                    FROM categories
                    WHERE category_name = ? AND category_id != ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $categoryName, $excludeId);
        } else {
            $sql = "SELECT category_id
                    FROM categories
                    WHERE category_name = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $categoryName);
        }

        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function createCategory($categoryName, $description, $adminId)
    {
        $sql = "INSERT INTO categories (category_name, description, created_by)
                VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $categoryName, $description, $adminId);
        return $stmt->execute();
    }

    public function updateCategory($categoryId, $categoryName, $description)
    {
        $sql = "UPDATE categories
                SET category_name = ?, description = ?
                WHERE category_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $categoryName, $description, $categoryId);
        return $stmt->execute();
    }

    public function deleteCategory($categoryId)
    {
        $sql = "DELETE FROM categories WHERE category_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $categoryId);
        return $stmt->execute();
    }

    public function getUsers($currentAdminId)
    {
        $sql = "SELECT user_id, name, email, phone, role, status, created_at
                FROM users
                WHERE user_id != ?
                ORDER BY user_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $currentAdminId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserById($userId)
    {
        $sql = "SELECT user_id, name, email, phone, role, status
                FROM users
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function updateUserStatus($userId, $status)
    {
        $sql = "UPDATE users
                SET status = ?
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $userId);
        return $stmt->execute();
    }

    public function emailExistsForAnotherUser($email, $userId)
    {
        $sql = "SELECT user_id
                FROM users
                WHERE email = ? AND user_id != ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();

        return $stmt->get_result()->num_rows > 0;
    }

    public function updateProfile($userId, $name, $email, $phone)
    {
        $sql = "UPDATE users
                SET name = ?, email = ?, phone = ?
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $phone, $userId);
        return $stmt->execute();
    }

    public function getPasswordHash($userId)
    {
        $sql = "SELECT password
                FROM users
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $user = $stmt->get_result()->fetch_assoc();
        return $user ? $user["password"] : null;
    }

    public function updatePassword($userId, $passwordHash)
    {
        $sql = "UPDATE users
                SET password = ?
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $passwordHash, $userId);
        return $stmt->execute();
    }
}
