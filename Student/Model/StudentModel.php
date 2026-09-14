<?php
require_once __DIR__ . "/../../Common/Config/db.php";

class StudentModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = getConnection();
    }

    public function getDashboardCounts($studentId)
    {
        $data = [
            "available_jobs" => 0,
            "applications" => 0,
            "pending" => 0,
            "accepted" => 0
        ];

        $result = $this->conn->query(
            "SELECT COUNT(*) AS total
             FROM jobs
             WHERE deadline >= CURDATE()"
        );
        $data["available_jobs"] = (int)$result->fetch_assoc()["total"];

        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total
             FROM applications
             WHERE student_id = ?"
        );
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $data["applications"] = (int)$stmt->get_result()->fetch_assoc()["total"];

        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total
             FROM applications
             WHERE student_id = ? AND status = 'pending'"
        );
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $data["pending"] = (int)$stmt->get_result()->fetch_assoc()["total"];

        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total
             FROM applications
             WHERE student_id = ? AND status = 'accepted'"
        );
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $data["accepted"] = (int)$stmt->get_result()->fetch_assoc()["total"];

        return $data;
    }

    public function getRecentJobs($studentId, $limit = 4)
    {
        $limit = (int)$limit;
        if ($limit < 1) {
            $limit = 4;
        }

        $sql = "SELECT j.job_id, j.job_title, j.job_type, j.location,
                       j.salary, j.deadline,
                       c.category_name,
                       u.company_name, u.name AS employer_name,
                       CASE WHEN a.application_id IS NULL THEN 0 ELSE 1 END AS already_applied
                FROM jobs j
                INNER JOIN categories c ON j.category_id = c.category_id
                INNER JOIN users u ON j.employer_id = u.user_id
                LEFT JOIN applications a
                    ON a.job_id = j.job_id AND a.student_id = ?
                WHERE j.deadline >= CURDATE()
                ORDER BY j.job_id DESC
                LIMIT " . $limit;

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $studentId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getRecentApplications($studentId, $limit = 4)
    {
        $limit = (int)$limit;
        if ($limit < 1) {
            $limit = 4;
        }

        $sql = "SELECT a.application_id, a.status, a.applied_at,
                       j.job_id, j.job_title,
                       u.company_name, u.name AS employer_name
                FROM applications a
                INNER JOIN jobs j ON a.job_id = j.job_id
                INNER JOIN users u ON j.employer_id = u.user_id
                WHERE a.student_id = ?
                ORDER BY a.application_id DESC
                LIMIT " . $limit;

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $studentId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getJobs($studentId)
    {
        $sql = "SELECT j.job_id, j.job_title, j.job_type, j.location,
                       j.salary, j.deadline, j.description,
                       c.category_name,
                       u.company_name, u.name AS employer_name,
                       CASE WHEN a.application_id IS NULL THEN 0 ELSE 1 END AS already_applied,
                       a.status AS application_status
                FROM jobs j
                INNER JOIN categories c ON j.category_id = c.category_id
                INNER JOIN users u ON j.employer_id = u.user_id
                LEFT JOIN applications a
                    ON a.job_id = j.job_id AND a.student_id = ?
                WHERE j.deadline >= CURDATE()
                ORDER BY j.job_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $studentId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getJobById($jobId, $studentId)
    {
        $sql = "SELECT j.job_id, j.employer_id, j.category_id, j.job_title,
                       j.job_type, j.location, j.salary, j.deadline,
                       j.description, j.created_at,
                       c.category_name,
                       u.company_name, u.name AS employer_name,
                       a.application_id, a.status AS application_status
                FROM jobs j
                INNER JOIN categories c ON j.category_id = c.category_id
                INNER JOIN users u ON j.employer_id = u.user_id
                LEFT JOIN applications a
                    ON a.job_id = j.job_id AND a.student_id = ?
                WHERE j.job_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $studentId, $jobId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function applicationExists($studentId, $jobId)
    {
        $stmt = $this->conn->prepare(
            "SELECT application_id
             FROM applications
             WHERE student_id = ? AND job_id = ?"
        );
        $stmt->bind_param("ii", $studentId, $jobId);
        $stmt->execute();

        return $stmt->get_result()->num_rows > 0;
    }

    public function createApplication($jobId, $studentId, $expectedSalary, $coverNote)
    {
        if ($expectedSalary === null) {
            $stmt = $this->conn->prepare(
                "INSERT INTO applications
                 (job_id, student_id, expected_salary, cover_note)
                 VALUES (?, ?, NULL, ?)"
            );
            $stmt->bind_param("iis", $jobId, $studentId, $coverNote);
        } else {
            $stmt = $this->conn->prepare(
                "INSERT INTO applications
                 (job_id, student_id, expected_salary, cover_note)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("iids", $jobId, $studentId, $expectedSalary, $coverNote);
        }

        return $stmt->execute();
    }

    public function getApplications($studentId)
    {
        $sql = "SELECT a.application_id, a.job_id, a.expected_salary,
                       a.cover_note, a.status, a.applied_at, a.updated_at,
                       j.job_title, j.location, j.deadline,
                       c.category_name,
                       u.company_name, u.name AS employer_name
                FROM applications a
                INNER JOIN jobs j ON a.job_id = j.job_id
                INNER JOIN categories c ON j.category_id = c.category_id
                INNER JOIN users u ON j.employer_id = u.user_id
                WHERE a.student_id = ?
                ORDER BY a.application_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $studentId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getApplicationById($applicationId, $studentId)
    {
        $sql = "SELECT a.application_id, a.job_id, a.expected_salary,
                       a.cover_note, a.status, a.applied_at,
                       j.job_title, j.deadline, j.location,
                       c.category_name,
                       u.company_name, u.name AS employer_name
                FROM applications a
                INNER JOIN jobs j ON a.job_id = j.job_id
                INNER JOIN categories c ON j.category_id = c.category_id
                INNER JOIN users u ON j.employer_id = u.user_id
                WHERE a.application_id = ? AND a.student_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $applicationId, $studentId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function updateApplication($applicationId, $studentId, $expectedSalary, $coverNote)
    {
        if ($expectedSalary === null) {
            $stmt = $this->conn->prepare(
                "UPDATE applications
                 SET expected_salary = NULL, cover_note = ?, updated_at = NOW()
                 WHERE application_id = ? AND student_id = ? AND status = 'pending'"
            );
            $stmt->bind_param("sii", $coverNote, $applicationId, $studentId);
        } else {
            $stmt = $this->conn->prepare(
                "UPDATE applications
                 SET expected_salary = ?, cover_note = ?, updated_at = NOW()
                 WHERE application_id = ? AND student_id = ? AND status = 'pending'"
            );
            $stmt->bind_param("dsii", $expectedSalary, $coverNote, $applicationId, $studentId);
        }

        return $stmt->execute();
    }

    public function deleteApplication($applicationId, $studentId)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM applications
             WHERE application_id = ? AND student_id = ? AND status = 'pending'"
        );
        $stmt->bind_param("ii", $applicationId, $studentId);

        return $stmt->execute();
    }

    public function getCareerTips()
    {
        $sql = "SELECT t.tip_id, t.title, t.category, t.content,
                       t.created_at, t.updated_at,
                       u.name AS mentor_name, u.expertise
                FROM career_tips t
                INNER JOIN users u ON t.mentor_id = u.user_id
                ORDER BY t.tip_id DESC";

        $result = $this->conn->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProfile($userId)
    {
        $stmt = $this->conn->prepare(
            "SELECT user_id, name, email, phone, role, status, cv_file
             FROM users
             WHERE user_id = ?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function emailExistsForAnotherUser($email, $userId)
    {
        $stmt = $this->conn->prepare(
            "SELECT user_id
             FROM users
             WHERE email = ? AND user_id != ?"
        );
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();

        return $stmt->get_result()->num_rows > 0;
    }

    public function updateProfile($userId, $name, $email, $phone, $cvFile)
    {
        $stmt = $this->conn->prepare(
            "UPDATE users
             SET name = ?, email = ?, phone = ?, cv_file = ?
             WHERE user_id = ?"
        );
        $stmt->bind_param("ssssi", $name, $email, $phone, $cvFile, $userId);

        return $stmt->execute();
    }

    public function getPasswordHash($userId)
    {
        $stmt = $this->conn->prepare(
            "SELECT password
             FROM users
             WHERE user_id = ?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        return $row ? $row["password"] : null;
    }

    public function updatePassword($userId, $passwordHash)
    {
        $stmt = $this->conn->prepare(
            "UPDATE users
             SET password = ?
             WHERE user_id = ?"
        );
        $stmt->bind_param("si", $passwordHash, $userId);

        return $stmt->execute();
    }
}
