<?php
require_once __DIR__ . "/../../Common/Config/db.php";

class EmployerModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = getConnection();
    }

    public function getDashboardCounts($employerId)
    {
        $data = [
            "jobs" => 0,
            "active_jobs" => 0,
            "applications" => 0,
            "pending" => 0
        ];

        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM jobs WHERE employer_id = ?");
        $stmt->bind_param("i", $employerId);
        $stmt->execute();
        $data["jobs"] = (int)$stmt->get_result()->fetch_assoc()["total"];

        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total
             FROM jobs
             WHERE employer_id = ? AND deadline >= CURDATE()"
        );
        $stmt->bind_param("i", $employerId);
        $stmt->execute();
        $data["active_jobs"] = (int)$stmt->get_result()->fetch_assoc()["total"];

        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total
             FROM applications a
             INNER JOIN jobs j ON a.job_id = j.job_id
             WHERE j.employer_id = ?"
        );
        $stmt->bind_param("i", $employerId);
        $stmt->execute();
        $data["applications"] = (int)$stmt->get_result()->fetch_assoc()["total"];

        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total
             FROM applications a
             INNER JOIN jobs j ON a.job_id = j.job_id
             WHERE j.employer_id = ? AND a.status = 'pending'"
        );
        $stmt->bind_param("i", $employerId);
        $stmt->execute();
        $data["pending"] = (int)$stmt->get_result()->fetch_assoc()["total"];

        return $data;
    }

    public function getRecentJobs($employerId, $limit = 5)
    {
        $limit = (int)$limit;
        if ($limit < 1) {
            $limit = 5;
        }

        $sql = "SELECT j.job_id, j.job_title, j.job_type, j.deadline,
                       c.category_name,
                       COUNT(a.application_id) AS applicant_count
                FROM jobs j
                INNER JOIN categories c ON j.category_id = c.category_id
                LEFT JOIN applications a ON j.job_id = a.job_id
                WHERE j.employer_id = ?
                GROUP BY j.job_id, j.job_title, j.job_type, j.deadline, c.category_name
                ORDER BY j.job_id DESC
                LIMIT " . $limit;

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $employerId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getRecentApplications($employerId, $limit = 5)
    {
        $limit = (int)$limit;
        if ($limit < 1) {
            $limit = 5;
        }

        $sql = "SELECT a.application_id, a.status, a.applied_at,
                       u.name AS student_name,
                       j.job_title
                FROM applications a
                INNER JOIN jobs j ON a.job_id = j.job_id
                INNER JOIN users u ON a.student_id = u.user_id
                WHERE j.employer_id = ?
                ORDER BY a.application_id DESC
                LIMIT " . $limit;

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $employerId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getCategories()
    {
        $result = $this->conn->query(
            "SELECT category_id, category_name
             FROM categories
             ORDER BY category_name ASC"
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getJobs($employerId)
    {
        $sql = "SELECT j.job_id, j.job_title, j.job_type, j.location, j.salary,
                       j.deadline, j.description, j.created_at,
                       c.category_name,
                       COUNT(a.application_id) AS applicant_count
                FROM jobs j
                INNER JOIN categories c ON j.category_id = c.category_id
                LEFT JOIN applications a ON j.job_id = a.job_id
                WHERE j.employer_id = ?
                GROUP BY j.job_id, j.job_title, j.job_type, j.location, j.salary,
                         j.deadline, j.description, j.created_at, c.category_name
                ORDER BY j.job_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $employerId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getJobById($jobId, $employerId)
    {
        $sql = "SELECT job_id, employer_id, category_id, job_title, job_type,
                       location, salary, deadline, description, created_at
                FROM jobs
                WHERE job_id = ? AND employer_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $jobId, $employerId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function createJob($employerId, $categoryId, $jobTitle, $jobType, $location, $salary, $deadline, $description)
    {
        $sql = "INSERT INTO jobs
                (employer_id, category_id, job_title, job_type, location, salary, deadline, description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "iissssss",
            $employerId,
            $categoryId,
            $jobTitle,
            $jobType,
            $location,
            $salary,
            $deadline,
            $description
        );

        return $stmt->execute();
    }

    public function updateJob($jobId, $employerId, $categoryId, $jobTitle, $jobType, $location, $salary, $deadline, $description)
    {
        $sql = "UPDATE jobs
                SET category_id = ?, job_title = ?, job_type = ?, location = ?,
                    salary = ?, deadline = ?, description = ?
                WHERE job_id = ? AND employer_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "issssssii",
            $categoryId,
            $jobTitle,
            $jobType,
            $location,
            $salary,
            $deadline,
            $description,
            $jobId,
            $employerId
        );

        return $stmt->execute();
    }

    public function deleteJob($jobId, $employerId)
    {
        $sql = "DELETE FROM jobs WHERE job_id = ? AND employer_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $jobId, $employerId);

        return $stmt->execute();
    }

    public function getJobsWithApplicantCount($employerId)
    {
        $sql = "SELECT j.job_id, j.job_title, j.deadline,
                       COUNT(a.application_id) AS applicant_count
                FROM jobs j
                LEFT JOIN applications a ON j.job_id = a.job_id
                WHERE j.employer_id = ?
                GROUP BY j.job_id, j.job_title, j.deadline
                ORDER BY j.job_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $employerId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getApplicantsForJob($jobId, $employerId)
    {
        $sql = "SELECT a.application_id, a.student_id, a.expected_salary,
                       a.cover_note, a.status, a.applied_at, a.updated_at,
                       u.name AS student_name, u.email, u.phone, u.cv_file,
                       j.job_title
                FROM applications a
                INNER JOIN jobs j ON a.job_id = j.job_id
                INNER JOIN users u ON a.student_id = u.user_id
                WHERE a.job_id = ? AND j.employer_id = ?
                ORDER BY a.application_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $jobId, $employerId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getApplicationForEmployer($applicationId, $employerId)
    {
        $sql = "SELECT a.application_id, a.status, a.student_id,
                       u.cv_file, u.name AS student_name,
                       j.job_id, j.job_title
                FROM applications a
                INNER JOIN jobs j ON a.job_id = j.job_id
                INNER JOIN users u ON a.student_id = u.user_id
                WHERE a.application_id = ? AND j.employer_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $applicationId, $employerId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function updateApplicationStatus($applicationId, $employerId, $status)
    {
        $sql = "UPDATE applications a
                INNER JOIN jobs j ON a.job_id = j.job_id
                SET a.status = ?, a.updated_at = NOW()
                WHERE a.application_id = ? AND j.employer_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $status, $applicationId, $employerId);

        return $stmt->execute();
    }

    public function getProfile($userId)
    {
        $sql = "SELECT user_id, name, email, phone, role, status,
                       company_name, industry
                FROM users
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
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

    public function updateProfile($userId, $name, $email, $phone, $companyName, $industry)
    {
        $sql = "UPDATE users
                SET name = ?, email = ?, phone = ?, company_name = ?, industry = ?
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $phone, $companyName, $industry, $userId);

        return $stmt->execute();
    }

    public function getPasswordHash($userId)
    {
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        return $row ? $row["password"] : null;
    }

    public function updatePassword($userId, $passwordHash)
    {
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $passwordHash, $userId);

        return $stmt->execute();
    }
}
