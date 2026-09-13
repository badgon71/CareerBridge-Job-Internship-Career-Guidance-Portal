<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("mentor");
require_once __DIR__ . "/../Model/MentorModel.php";

$model = new MentorModel();
$action = $_GET["action"] ?? "";
$mentorId = (int)$_SESSION["user_id"];

function redirectMentor($page, $type, $message)
{
    $_SESSION[$type] = $message;
    header("Location: ../View/" . $page);
    exit;
}

function validMentorName($name)
{
    return preg_match("/^[A-Za-z .'-]{3,50}$/", $name);
}

function validMentorPhone($phone)
{
    return preg_match("/^01[3-9][0-9]{8}$/", $phone);
}

function validMentorPassword($password)
{
    return strlen($password) >= 8
        && preg_match("/[A-Z]/", $password)
        && preg_match("/[a-z]/", $password)
        && preg_match("/[0-9]/", $password)
        && preg_match("/[^A-Za-z0-9]/", $password);
}

if ($action === "createTip" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $content = trim($_POST["content"] ?? "");

    if ($title === "" || strlen($title) < 5 || strlen($title) > 150) {
        redirectMentor("tipForm.php", "error", "Title must be between 5 and 150 characters.");
    }
    if ($category === "") {
        redirectMentor("tipForm.php", "error", "Category is required.");
    }
    if ($content === "" || strlen($content) < 30) {
        redirectMentor("tipForm.php", "error", "Content must be at least 30 characters.");
    }

    $model->createTip($mentorId, $title, $category, $content)
        ? redirectMentor("tips.php", "success", "Career tip added successfully.")
        : redirectMentor("tipForm.php", "error", "Career tip could not be added.");
}

if ($action === "updateTip" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $tipId = (int)($_POST["tip_id"] ?? 0);
    $title = trim($_POST["title"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $content = trim($_POST["content"] ?? "");

    if ($tipId <= 0 || !$model->getTipById($tipId, $mentorId)) {
        redirectMentor("tips.php", "error", "Invalid career tip.");
    }
    if ($title === "" || strlen($title) < 5 || strlen($title) > 150) {
        redirectMentor("tipForm.php?edit=" . $tipId, "error", "Title must be between 5 and 150 characters.");
    }
    if ($category === "") {
        redirectMentor("tipForm.php?edit=" . $tipId, "error", "Category is required.");
    }
    if ($content === "" || strlen($content) < 30) {
        redirectMentor("tipForm.php?edit=" . $tipId, "error", "Content must be at least 30 characters.");
    }

    $model->updateTip($tipId, $mentorId, $title, $category, $content)
        ? redirectMentor("tips.php", "success", "Career tip updated successfully.")
        : redirectMentor("tipForm.php?edit=" . $tipId, "error", "Career tip could not be updated.");
}

if ($action === "deleteTip" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $tipId = (int)($_POST["tip_id"] ?? 0);

    if ($tipId <= 0 || !$model->getTipById($tipId, $mentorId)) {
        redirectMentor("tips.php", "error", "Invalid career tip.");
    }

    $model->deleteTip($tipId, $mentorId)
        ? redirectMentor("tips.php", "success", "Career tip deleted successfully.")
        : redirectMentor("tips.php", "error", "Career tip could not be deleted.");
}

if ($action === "updateProfile" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $expertise = trim($_POST["expertise"] ?? "");

    if (!validMentorName($name)) {
        redirectMentor("profile.php", "error", "Enter a valid name.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirectMentor("profile.php", "error", "Enter a valid email address.");
    }
    if (!validMentorPhone($phone)) {
        redirectMentor("profile.php", "error", "Enter a valid 11-digit Bangladesh phone number.");
    }
    if ($expertise === "" || strlen($expertise) > 150) {
        redirectMentor("profile.php", "error", "Enter a valid area of expertise.");
    }
    if ($model->emailExistsForAnotherUser($email, $mentorId)) {
        redirectMentor("profile.php", "error", "This email is already used by another account.");
    }

    if ($model->updateProfile($mentorId, $name, $email, $phone, $expertise)) {
        $_SESSION["name"] = $name;
        $_SESSION["email"] = $email;
        redirectMentor("profile.php", "success", "Profile updated successfully.");
    }

    redirectMentor("profile.php", "error", "Profile could not be updated.");
}

if ($action === "changePassword" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $current = $_POST["current_password"] ?? "";
    $new = $_POST["new_password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    if ($current === "" || $new === "" || $confirm === "") {
        redirectMentor("changePassword.php", "error", "All password fields are required.");
    }

    $hash = $model->getPasswordHash($mentorId);

    if (!$hash || !password_verify($current, $hash)) {
        redirectMentor("changePassword.php", "error", "Current password is incorrect.");
    }

    if (!validMentorPassword($new)) {
        redirectMentor("changePassword.php", "error", "New password must have 8+ characters, uppercase, lowercase, number and special character.");
    }

    if ($current === $new) {
        redirectMentor("changePassword.php", "error", "New password must be different from current password.");
    }

    if ($new !== $confirm) {
        redirectMentor("changePassword.php", "error", "New password and confirm password do not match.");
    }

    $newHash = password_hash($new, PASSWORD_DEFAULT);

    $model->updatePassword($mentorId, $newHash)
        ? redirectMentor("changePassword.php", "success", "Password changed successfully.")
        : redirectMentor("changePassword.php", "error", "Password could not be changed.");
}

header("Location: ../View/dashboard.php");
exit;
