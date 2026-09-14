<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("employer");

require_once __DIR__ . "/../Model/EmployerModel.php";

$model = new EmployerModel();

$action = $_GET["action"] ?? "";
$employerId = (int)$_SESSION["user_id"];

function redirectEmployer($page, $type, $message)
{
    $_SESSION[$type] = $message;
    header("Location: ../View/" . $page);
    exit;
}

function validEmployerName($name)
{
    return preg_match("/^[A-Za-z .'-]{3,50}$/", $name);
}

function validEmployerPhone($phone)
{
    return preg_match("/^01[3-9][0-9]{8}$/", $phone);
}

function validEmployerPassword($password)
{
    return strlen($password) >= 8
        && preg_match("/[A-Z]/", $password)
        && preg_match("/[a-z]/", $password)
        && preg_match("/[0-9]/", $password)
        && preg_match("/[^A-Za-z0-9]/", $password);
}

function validateJobInput($categoryId, $jobTitle, $jobType, $location, $salary, $deadline, $description)
{
    $allowedTypes = ["Full-time", "Part-time", "Internship", "Contract"];

    if ($categoryId <= 0) {
        return "Select a valid job category.";
    }

    if (strlen($jobTitle) < 3 || strlen($jobTitle) > 150) {
        return "Job title must be between 3 and 150 characters.";
    }

    if (!in_array($jobType, $allowedTypes, true)) {
        return "Select a valid job type.";
    }

    if (strlen($location) < 2 || strlen($location) > 120) {
        return "Enter a valid job location.";
    }

    if ($salary === "" || strlen($salary) > 100) {
        return "Enter a valid salary.";
    }

    if ($deadline === "" || strtotime($deadline) === false) {
        return "Select a valid application deadline.";
    }

    if ($deadline < date("Y-m-d")) {
        return "Application deadline cannot be in the past.";
    }

    if (strlen($description) < 30) {
        return "Job description must be at least 30 characters.";
    }

    return "";
}

/* ---------------- Create Job ---------------- */

if ($action === "createJob" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $categoryId = (int)($_POST["category_id"] ?? 0);
    $jobTitle = trim($_POST["job_title"] ?? "");
    $jobType = trim($_POST["job_type"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $salary = trim($_POST["salary"] ?? "");
    $deadline = trim($_POST["deadline"] ?? "");
    $description = trim($_POST["description"] ?? "");

    $error = validateJobInput(
        $categoryId,
        $jobTitle,
        $jobType,
        $location,
        $salary,
        $deadline,
        $description
    );

    if ($error !== "") {
        redirectEmployer("jobForm.php", "error", $error);
    }

    if ($model->createJob(
        $employerId,
        $categoryId,
        $jobTitle,
        $jobType,
        $location,
        $salary,
        $deadline,
        $description
    )) {
        redirectEmployer("jobs.php", "success", "Job posted successfully.");
    }

    redirectEmployer("jobForm.php", "error", "Job could not be posted.");
}

/* ---------------- Update Job ---------------- */

if ($action === "updateJob" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $jobId = (int)($_POST["job_id"] ?? 0);
    $categoryId = (int)($_POST["category_id"] ?? 0);
    $jobTitle = trim($_POST["job_title"] ?? "");
    $jobType = trim($_POST["job_type"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $salary = trim($_POST["salary"] ?? "");
    $deadline = trim($_POST["deadline"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($jobId <= 0 || !$model->getJobById($jobId, $employerId)) {
        redirectEmployer("jobs.php", "error", "Invalid job post.");
    }

    $error = validateJobInput(
        $categoryId,
        $jobTitle,
        $jobType,
        $location,
        $salary,
        $deadline,
        $description
    );

    if ($error !== "") {
        redirectEmployer("jobForm.php?edit=" . $jobId, "error", $error);
    }

    if ($model->updateJob(
        $jobId,
        $employerId,
        $categoryId,
        $jobTitle,
        $jobType,
        $location,
        $salary,
        $deadline,
        $description
    )) {
        redirectEmployer("jobs.php", "success", "Job updated successfully.");
    }

    redirectEmployer("jobForm.php?edit=" . $jobId, "error", "Job could not be updated.");
}

/* ---------------- Delete Job ---------------- */

if ($action === "deleteJob" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $jobId = (int)($_POST["job_id"] ?? 0);

    if ($jobId <= 0 || !$model->getJobById($jobId, $employerId)) {
        redirectEmployer("jobs.php", "error", "Invalid job post.");
    }

    try {
        if ($model->deleteJob($jobId, $employerId)) {
            redirectEmployer("jobs.php", "success", "Job deleted successfully.");
        }
    } catch (mysqli_sql_exception $e) {
        redirectEmployer(
            "jobs.php",
            "error",
            "This job has applications and cannot be deleted."
        );
    }

    redirectEmployer("jobs.php", "error", "Job could not be deleted.");
}

/* ---------------- Application Status ---------------- */

if (($action === "acceptApplication" || $action === "rejectApplication")
    && $_SERVER["REQUEST_METHOD"] === "POST") {

    $applicationId = (int)($_POST["application_id"] ?? 0);
    $jobId = (int)($_POST["job_id"] ?? 0);

    $application = $model->getApplicationForEmployer($applicationId, $employerId);

    if (!$application || (int)$application["job_id"] !== $jobId) {
        redirectEmployer("applicants.php", "error", "Invalid application.");
    }

    $status = $action === "acceptApplication" ? "accepted" : "rejected";

    if ($model->updateApplicationStatus($applicationId, $employerId, $status)) {
        redirectEmployer(
            "applicants.php?job_id=" . $jobId,
            "success",
            "Application status updated successfully."
        );
    }

    redirectEmployer(
        "applicants.php?job_id=" . $jobId,
        "error",
        "Application status could not be updated."
    );
}

/* ---------------- Download CV ---------------- */

if ($action === "downloadCv") {
    $applicationId = (int)($_GET["application_id"] ?? 0);
    $application = $model->getApplicationForEmployer($applicationId, $employerId);

    if (!$application || empty($application["cv_file"])) {
        redirectEmployer("applicants.php", "error", "CV is not available.");
    }

    $fileName = basename($application["cv_file"]);
    $filePath = __DIR__ . "/../../Common/uploads/cv/" . $fileName;

    if (!is_file($filePath)) {
        redirectEmployer("applicants.php", "error", "CV file was not found.");
    }

    header("Content-Type: application/pdf");
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header("Content-Length: " . filesize($filePath));
    readfile($filePath);
    exit;
}

/* ---------------- Profile Update ---------------- */

if ($action === "updateProfile" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $companyName = trim($_POST["company_name"] ?? "");
    $industry = trim($_POST["industry"] ?? "");

    if (!validEmployerName($name)) {
        redirectEmployer("profile.php", "error", "Enter a valid name.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirectEmployer("profile.php", "error", "Enter a valid email address.");
    }

    if (!validEmployerPhone($phone)) {
        redirectEmployer("profile.php", "error", "Enter a valid 11-digit Bangladesh phone number.");
    }

    if (strlen($companyName) < 2 || strlen($companyName) > 150) {
        redirectEmployer("profile.php", "error", "Enter a valid company name.");
    }

    if (strlen($industry) < 2 || strlen($industry) > 120) {
        redirectEmployer("profile.php", "error", "Enter a valid industry.");
    }

    if ($model->emailExistsForAnotherUser($email, $employerId)) {
        redirectEmployer("profile.php", "error", "This email is already used by another account.");
    }

    if ($model->updateProfile(
        $employerId,
        $name,
        $email,
        $phone,
        $companyName,
        $industry
    )) {
        $_SESSION["name"] = $name;
        $_SESSION["email"] = $email;

        redirectEmployer("profile.php", "success", "Profile updated successfully.");
    }

    redirectEmployer("profile.php", "error", "Profile could not be updated.");
}

/* ---------------- Change Password ---------------- */

if ($action === "changePassword" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $currentPassword = $_POST["current_password"] ?? "";
    $newPassword = $_POST["new_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($currentPassword === "" || $newPassword === "" || $confirmPassword === "") {
        redirectEmployer("changePassword.php", "error", "All password fields are required.");
    }

    $storedHash = $model->getPasswordHash($employerId);

    if (!$storedHash || !password_verify($currentPassword, $storedHash)) {
        redirectEmployer("changePassword.php", "error", "Current password is incorrect.");
    }

    if (!validEmployerPassword($newPassword)) {
        redirectEmployer(
            "changePassword.php",
            "error",
            "New password must have 8+ characters, uppercase, lowercase, number and special character."
        );
    }

    if ($currentPassword === $newPassword) {
        redirectEmployer(
            "changePassword.php",
            "error",
            "New password must be different from current password."
        );
    }

    if ($newPassword !== $confirmPassword) {
        redirectEmployer(
            "changePassword.php",
            "error",
            "New password and confirm password do not match."
        );
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

    if ($model->updatePassword($employerId, $newHash)) {
        redirectEmployer("changePassword.php", "success", "Password changed successfully.");
    }

    redirectEmployer("changePassword.php", "error", "Password could not be changed.");
}

header("Location: ../View/dashboard.php");
exit;
