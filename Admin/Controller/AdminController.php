<?php

require_once __DIR__ . "/../../Common/session.php";
requireRole("admin");

require_once __DIR__ . "/../Model/AdminModel.php";

$model = new AdminModel();

$action = $_GET["action"] ?? "";
$adminId = (int)$_SESSION["user_id"];

function redirectAdmin($page, $type, $message)
{
    $_SESSION[$type] = $message;

    header("Location: ../View/" . $page);
    exit;
}

function validName($name)
{
    return preg_match("/^[A-Za-z .'-]{3,50}$/", $name);
}

function validPhone($phone)
{
    return preg_match("/^01[3-9][0-9]{8}$/", $phone);
}

function validPassword($password)
{
    return strlen($password) >= 8
        && preg_match("/[A-Z]/", $password)
        && preg_match("/[a-z]/", $password)
        && preg_match("/[0-9]/", $password)
        && preg_match("/[^A-Za-z0-9]/", $password);
}

/* ---------------- Category Create ---------------- */

if ($action === "createCategory" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $categoryName = trim($_POST["category_name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($categoryName === "") {
        redirectAdmin("categories.php", "error", "Category name is required.");
    }

    if (strlen($categoryName) < 3 || strlen($categoryName) > 100) {
        redirectAdmin("categories.php", "error", "Category name must be between 3 and 100 characters.");
    }

    if ($model->categoryNameExists($categoryName)) {
        redirectAdmin("categories.php", "error", "This category already exists.");
    }

    if ($model->createCategory($categoryName, $description, $adminId)) {
        redirectAdmin("categories.php", "success", "Category added successfully.");
    }

    redirectAdmin("categories.php", "error", "Category could not be added.");
}

/* ---------------- Category Update ---------------- */

if ($action === "updateCategory" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $categoryId = (int)($_POST["category_id"] ?? 0);
    $categoryName = trim($_POST["category_name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($categoryId <= 0 || !$model->getCategoryById($categoryId)) {
        redirectAdmin("categories.php", "error", "Invalid category.");
    }

    if ($categoryName === "" || strlen($categoryName) < 3 || strlen($categoryName) > 100) {
        redirectAdmin("categories.php?edit=" . $categoryId, "error", "Enter a valid category name.");
    }

    if ($model->categoryNameExists($categoryName, $categoryId)) {
        redirectAdmin("categories.php?edit=" . $categoryId, "error", "Another category already uses this name.");
    }

    if ($model->updateCategory($categoryId, $categoryName, $description)) {
        redirectAdmin("categories.php", "success", "Category updated successfully.");
    }

    redirectAdmin("categories.php?edit=" . $categoryId, "error", "Category could not be updated.");
}

/* ---------------- Category Delete ---------------- */

if ($action === "deleteCategory" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $categoryId = (int)($_POST["category_id"] ?? 0);

    if ($categoryId <= 0 || !$model->getCategoryById($categoryId)) {
        redirectAdmin("categories.php", "error", "Invalid category.");
    }

    try {
        if ($model->deleteCategory($categoryId)) {
            redirectAdmin("categories.php", "success", "Category deleted successfully.");
        }
    } catch (mysqli_sql_exception $e) {
        redirectAdmin("categories.php", "error", "This category is being used by a job and cannot be deleted.");
    }

    redirectAdmin("categories.php", "error", "Category could not be deleted.");
}

/* ---------------- User Block / Activate ---------------- */

if (($action === "blockUser" || $action === "activateUser")
    && $_SERVER["REQUEST_METHOD"] === "POST") {

    $userId = (int)($_POST["user_id"] ?? 0);

    if ($userId <= 0 || $userId === $adminId) {
        redirectAdmin("users.php", "error", "Invalid user.");
    }

    $user = $model->getUserById($userId);

    if (!$user) {
        redirectAdmin("users.php", "error", "User not found.");
    }

    $newStatus = $action === "blockUser" ? "blocked" : "active";

    if ($model->updateUserStatus($userId, $newStatus)) {
        $message = $newStatus === "blocked"
            ? "User blocked successfully."
            : "User activated successfully.";

        redirectAdmin("users.php", "success", $message);
    }

    redirectAdmin("users.php", "error", "User status could not be updated.");
}

/* ---------------- Profile Update ---------------- */

if ($action === "updateProfile" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if (!validName($name)) {
        redirectAdmin("profile.php", "error", "Enter a valid name.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirectAdmin("profile.php", "error", "Enter a valid email address.");
    }

    if (!validPhone($phone)) {
        redirectAdmin("profile.php", "error", "Enter a valid 11-digit Bangladesh phone number.");
    }

    if ($model->emailExistsForAnotherUser($email, $adminId)) {
        redirectAdmin("profile.php", "error", "This email is already used by another account.");
    }

    if ($model->updateProfile($adminId, $name, $email, $phone)) {
        $_SESSION["name"] = $name;
        $_SESSION["email"] = $email;

        redirectAdmin("profile.php", "success", "Profile updated successfully.");
    }

    redirectAdmin("profile.php", "error", "Profile could not be updated.");
}

/* ---------------- Change Password ---------------- */

if ($action === "changePassword" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $currentPassword = $_POST["current_password"] ?? "";
    $newPassword = $_POST["new_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($currentPassword === "" || $newPassword === "" || $confirmPassword === "") {
        redirectAdmin("changePassword.php", "error", "All password fields are required.");
    }

    $storedHash = $model->getPasswordHash($adminId);

    if (!$storedHash || !password_verify($currentPassword, $storedHash)) {
        redirectAdmin("changePassword.php", "error", "Current password is incorrect.");
    }

    if (!validPassword($newPassword)) {
        redirectAdmin(
            "changePassword.php",
            "error",
            "New password must have 8+ characters, uppercase, lowercase, number and special character."
        );
    }

    if ($currentPassword === $newPassword) {
        redirectAdmin("changePassword.php", "error", "New password must be different from current password.");
    }

    if ($newPassword !== $confirmPassword) {
        redirectAdmin("changePassword.php", "error", "New password and confirm password do not match.");
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

    if ($model->updatePassword($adminId, $newHash)) {
        redirectAdmin("changePassword.php", "success", "Password changed successfully.");
    }

    redirectAdmin("changePassword.php", "error", "Password could not be changed.");
}

header("Location: ../View/dashboard.php");
exit;
