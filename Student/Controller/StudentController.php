<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("student");

require_once __DIR__ . "/../Model/StudentModel.php";

$model = new StudentModel();

$action = $_GET["action"] ?? "";
$studentId = (int)$_SESSION["user_id"];

function redirectStudent($page, $type, $message)
{
    $_SESSION[$type] = $message;
    header("Location: ../View/" . $page);
    exit;
}

function validStudentName($name)
{
    return preg_match("/^[A-Za-z .'-]{3,50}$/", $name);
}

function validStudentPhone($phone)
{
    return preg_match("/^01[3-9][0-9]{8}$/", $phone);
}

function validStudentPassword($password)
{
    return strlen($password) >= 8
        && preg_match("/[A-Z]/", $password)
        && preg_match("/[a-z]/", $password)
        && preg_match("/[0-9]/", $password)
        && preg_match("/[^A-Za-z0-9]/", $password);
}

function validateApplicationInput($expectedSalaryRaw, $coverNote)
{
    if ($expectedSalaryRaw !== "") {
        if (!is_numeric($expectedSalaryRaw) || (float)$expectedSalaryRaw < 0) {
            return "Expected salary must be a valid non-negative number.";
        }
    }

    if (strlen($coverNote) < 20) {
        return "Cover note must be at least 20 characters.";
    }

    return "";
}

/* ---------------- Create Application ---------------- */

if ($action === "createApplication" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $jobId = (int)($_POST["job_id"] ?? 0);
    $expectedSalaryRaw = trim($_POST["expected_salary"] ?? "");
    $coverNote = trim($_POST["cover_note"] ?? "");

    $job = $model->getJobById($jobId, $studentId);

    if (!$job) {
        redirectStudent("jobs.php", "error", "Job post not found.");
    }

    if ($job["deadline"] < date("Y-m-d")) {
        redirectStudent("jobDetails.php?job_id=" . $jobId, "error", "The application deadline has passed.");
    }

    if ($model->applicationExists($studentId, $jobId)) {
        redirectStudent("applications.php", "error", "You have already applied for this job.");
    }

    $profile = $model->getProfile($studentId);

    if (empty($profile["cv_file"])) {
        redirectStudent(
            "profile.php",
            "error",
            "Upload your CV before applying for a job."
        );
    }

    $cvPath = __DIR__ . "/../../Common/uploads/cv/" . basename($profile["cv_file"]);

    if (!is_file($cvPath)) {
        redirectStudent(
            "profile.php",
            "error",
            "Your saved CV file is missing. Please upload it again."
        );
    }

    $error = validateApplicationInput($expectedSalaryRaw, $coverNote);

    if ($error !== "") {
        redirectStudent("applicationForm.php?job_id=" . $jobId, "error", $error);
    }

    $expectedSalary = $expectedSalaryRaw === "" ? null : (float)$expectedSalaryRaw;

    try {
        if ($model->createApplication($jobId, $studentId, $expectedSalary, $coverNote)) {
            redirectStudent("applications.php", "success", "Application submitted successfully.");
        }
    } catch (mysqli_sql_exception $e) {
        redirectStudent("applications.php", "error", "You have already applied for this job.");
    }

    redirectStudent(
        "applicationForm.php?job_id=" . $jobId,
        "error",
        "Application could not be submitted."
    );
}

/* ---------------- Update Application ---------------- */

if ($action === "updateApplication" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $applicationId = (int)($_POST["application_id"] ?? 0);
    $expectedSalaryRaw = trim($_POST["expected_salary"] ?? "");
    $coverNote = trim($_POST["cover_note"] ?? "");

    $application = $model->getApplicationById($applicationId, $studentId);

    if (!$application) {
        redirectStudent("applications.php", "error", "Application not found.");
    }

    if ($application["status"] !== "pending") {
        redirectStudent(
            "applications.php",
            "error",
            "Only pending applications can be edited."
        );
    }

    $error = validateApplicationInput($expectedSalaryRaw, $coverNote);

    if ($error !== "") {
        redirectStudent(
            "applicationForm.php?edit=" . $applicationId,
            "error",
            $error
        );
    }

    $expectedSalary = $expectedSalaryRaw === "" ? null : (float)$expectedSalaryRaw;

    if ($model->updateApplication(
        $applicationId,
        $studentId,
        $expectedSalary,
        $coverNote
    )) {
        redirectStudent("applications.php", "success", "Application updated successfully.");
    }

    redirectStudent(
        "applicationForm.php?edit=" . $applicationId,
        "error",
        "Application could not be updated."
    );
}

/* ---------------- Withdraw Application ---------------- */

if ($action === "deleteApplication" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $applicationId = (int)($_POST["application_id"] ?? 0);
    $application = $model->getApplicationById($applicationId, $studentId);

    if (!$application) {
        redirectStudent("applications.php", "error", "Application not found.");
    }

    if ($application["status"] !== "pending") {
        redirectStudent(
            "applications.php",
            "error",
            "Only pending applications can be withdrawn."
        );
    }

    if ($model->deleteApplication($applicationId, $studentId)) {
        redirectStudent("applications.php", "success", "Application withdrawn successfully.");
    }

    redirectStudent("applications.php", "error", "Application could not be withdrawn.");
}

/* ---------------- Profile Update + CV Upload ---------------- */

if ($action === "updateProfile" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if (!validStudentName($name)) {
        redirectStudent("profile.php", "error", "Enter a valid name.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirectStudent("profile.php", "error", "Enter a valid email address.");
    }

    if (!validStudentPhone($phone)) {
        redirectStudent(
            "profile.php",
            "error",
            "Enter a valid 11-digit Bangladesh phone number."
        );
    }

    if ($model->emailExistsForAnotherUser($email, $studentId)) {
        redirectStudent(
            "profile.php",
            "error",
            "This email is already used by another account."
        );
    }

    $profile = $model->getProfile($studentId);
    $oldCv = $profile["cv_file"] ?? null;
    $cvFile = $oldCv;
    $newCvPath = null;

    if (isset($_FILES["cv_file"])
        && $_FILES["cv_file"]["error"] !== UPLOAD_ERR_NO_FILE) {

        if ($_FILES["cv_file"]["error"] !== UPLOAD_ERR_OK) {
            redirectStudent("profile.php", "error", "CV upload failed.");
        }

        if (!is_uploaded_file($_FILES["cv_file"]["tmp_name"])) {
            redirectStudent("profile.php", "error", "Invalid CV upload.");
        }

        if ((int)$_FILES["cv_file"]["size"] > 2 * 1024 * 1024) {
            redirectStudent("profile.php", "error", "CV must be 2 MB or smaller.");
        }

        $extension = strtolower(
            pathinfo($_FILES["cv_file"]["name"], PATHINFO_EXTENSION)
        );

        if ($extension !== "pdf") {
            redirectStudent("profile.php", "error", "CV must be a PDF file.");
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES["cv_file"]["tmp_name"]);

        if (!in_array($mime, ["application/pdf", "application/x-pdf"], true)) {
            redirectStudent("profile.php", "error", "CV must be a valid PDF file.");
        }

        $uploadDir = __DIR__ . "/../../Common/uploads/cv/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $cvFile = "cv_" . $studentId . "_" . time() . "_" . mt_rand(1000, 9999) . ".pdf";
        $newCvPath = $uploadDir . $cvFile;

        if (!move_uploaded_file($_FILES["cv_file"]["tmp_name"], $newCvPath)) {
            redirectStudent("profile.php", "error", "CV could not be saved.");
        }
    }

    if ($model->updateProfile($studentId, $name, $email, $phone, $cvFile)) {
        $_SESSION["name"] = $name;
        $_SESSION["email"] = $email;

        if ($newCvPath !== null && !empty($oldCv) && $oldCv !== $cvFile) {
            $oldCvPath = __DIR__ . "/../../Common/uploads/cv/" . basename($oldCv);

            if (is_file($oldCvPath)) {
                unlink($oldCvPath);
            }
        }

        redirectStudent("profile.php", "success", "Profile updated successfully.");
    }

    if ($newCvPath !== null && is_file($newCvPath)) {
        unlink($newCvPath);
    }

    redirectStudent("profile.php", "error", "Profile could not be updated.");
}

/* ---------------- Change Password ---------------- */

if ($action === "changePassword" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $currentPassword = $_POST["current_password"] ?? "";
    $newPassword = $_POST["new_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($currentPassword === "" || $newPassword === "" || $confirmPassword === "") {
        redirectStudent(
            "changePassword.php",
            "error",
            "All password fields are required."
        );
    }

    $storedHash = $model->getPasswordHash($studentId);

    if (!$storedHash || !password_verify($currentPassword, $storedHash)) {
        redirectStudent(
            "changePassword.php",
            "error",
            "Current password is incorrect."
        );
    }

    if (!validStudentPassword($newPassword)) {
        redirectStudent(
            "changePassword.php",
            "error",
            "New password must have 8+ characters, uppercase, lowercase, number and special character."
        );
    }

    if ($currentPassword === $newPassword) {
        redirectStudent(
            "changePassword.php",
            "error",
            "New password must be different from current password."
        );
    }

    if ($newPassword !== $confirmPassword) {
        redirectStudent(
            "changePassword.php",
            "error",
            "New password and confirm password do not match."
        );
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

    if ($model->updatePassword($studentId, $newHash)) {
        redirectStudent(
            "changePassword.php",
            "success",
            "Password changed successfully."
        );
    }

    redirectStudent("changePassword.php", "error", "Password could not be changed.");
}

header("Location: ../View/dashboard.php");
exit;
