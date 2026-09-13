<?php
require_once __DIR__ . "/../../Common/Config/db.php";

class MentorModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = getConnection();
    }

    public function getDashboardCounts($mentorId)
    {
        $data = ["tips" => 0, "this_month" => 0];

        $stmt = $this->conn->prepare("SELECT COUNT(*) total FROM career_tips WHERE mentor_id = ?");
        $stmt->bind_param("i", $mentorId);
        $stmt->execute();
        $data["tips"] = (int)$stmt->get_result()->fetch_assoc()["total"];

        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) total FROM career_tips
             WHERE mentor_id = ?
             AND YEAR(created_at)=YEAR(CURDATE())
             AND MONTH(created_at)=MONTH(CURDATE())"
        );
        $stmt->bind_param("i", $mentorId);
        $stmt->execute();
        $data["this_month"] = (int)$stmt->get_result()->fetch_assoc()["total"];

        return $data;
    }

    public function getRecentTips($mentorId)
    {
        $stmt = $this->conn->prepare(
            "SELECT tip_id,title,category,created_at
             FROM career_tips
             WHERE mentor_id=?
             ORDER BY tip_id DESC LIMIT 5"
        );
        $stmt->bind_param("i", $mentorId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getTips($mentorId)
    {
        $stmt = $this->conn->prepare(
            "SELECT tip_id,title,category,content,created_at,updated_at
             FROM career_tips
             WHERE mentor_id=?
             ORDER BY tip_id DESC"
        );
        $stmt->bind_param("i", $mentorId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getTipById($tipId, $mentorId)
    {
        $stmt = $this->conn->prepare(
            "SELECT tip_id,title,category,content
             FROM career_tips
             WHERE tip_id=? AND mentor_id=?"
        );
        $stmt->bind_param("ii", $tipId, $mentorId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createTip($mentorId, $title, $category, $content)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO career_tips (mentor_id,title,category,content)
             VALUES (?,?,?,?)"
        );
        $stmt->bind_param("isss", $mentorId, $title, $category, $content);
        return $stmt->execute();
    }

    public function updateTip($tipId, $mentorId, $title, $category, $content)
    {
        $stmt = $this->conn->prepare(
            "UPDATE career_tips
             SET title=?, category=?, content=?
             WHERE tip_id=? AND mentor_id=?"
        );
        $stmt->bind_param("sssii", $title, $category, $content, $tipId, $mentorId);
        return $stmt->execute();
    }

    public function deleteTip($tipId, $mentorId)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM career_tips WHERE tip_id=? AND mentor_id=?"
        );
        $stmt->bind_param("ii", $tipId, $mentorId);
        return $stmt->execute();
    }

    public function getProfile($userId)
    {
        $stmt = $this->conn->prepare(
            "SELECT user_id,name,email,phone,role,status,expertise
             FROM users WHERE user_id=?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function emailExistsForAnotherUser($email, $userId)
    {
        $stmt = $this->conn->prepare(
            "SELECT user_id FROM users WHERE email=? AND user_id!=?"
        );
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function updateProfile($userId, $name, $email, $phone, $expertise)
    {
        $stmt = $this->conn->prepare(
            "UPDATE users SET name=?, email=?, phone=?, expertise=? WHERE user_id=?"
        );
        $stmt->bind_param("ssssi", $name, $email, $phone, $expertise, $userId);
        return $stmt->execute();
    }

    public function getPasswordHash($userId)
    {
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE user_id=?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? $row["password"] : null;
    }

    public function updatePassword($userId, $hash)
    {
        $stmt = $this->conn->prepare("UPDATE users SET password=? WHERE user_id=?");
        $stmt->bind_param("si", $hash, $userId);
        return $stmt->execute();
    }
}
