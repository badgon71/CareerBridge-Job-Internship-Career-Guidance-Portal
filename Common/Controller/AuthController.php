<?php

require_once __DIR__ . "/../Model/AuthModel.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$model = new AuthModel();
$action = $_GET["action"] ?? "";

function validPassword($password)
{
    return strlen($password) >= 8
        && preg_match("/[A-Z]/", $password)
        && preg_match("/[a-z]/", $password)
        && preg_match("/[0-9]/", $password)
        && preg_match("/[^A-Za-z0-9]/", $password);
}

function redirectDashboard($role)
{
    $routes = [
        "student" => "../../Student/View/dashboard.php",
        "employer" => "../../Employer/View/dashboard.php",
        "mentor" => "../../Mentor/View/dashboard.php",
        "admin" => "../../Admin/View/dashboard.php"
    ];

    header("Location: " . ($routes[$role] ?? "../View/login.php"));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../View/login.php");
    exit;
}

if ($action === "register") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $role = strtolower(trim($_POST["role"] ?? ""));
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    $allowedRoles = ["student", "employer", "mentor"];

    if ($name === "" || !preg_match("/^[A-Za-z .'-]{3,50}$/", $name)) {
        header("Location: ../View/register.php?error=" . urlencode("Enter a valid name."));
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../View/register.php?error=" . urlencode("Enter a valid email address."));
        exit;
    }

    if (!preg_match("/^01[3-9][0-9]{8}$/", $phone)) {
        header("Location: ../View/register.php?error=" . urlencode("Enter a valid Bangladesh phone number."));
        exit;
    }

    if (!in_array($role, $allowedRoles, true)) {
        header("Location: ../View/register.php?error=" . urlencode("Select a valid role."));
        exit;
    }

    if (!validPassword($password)) {
        header("Location: ../View/register.php?error=" . urlencode("Password does not meet the requirements."));
        exit;
    }

    if ($password !== $confirmPassword) {
        header("Location: ../View/register.php?error=" . urlencode("Passwords do not match."));
        exit;
    }

    if ($model->emailExists($email)) {
        header("Location: ../View/register.php?error=" . urlencode("Email is already registered."));
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    if ($model->register($name, $email, $phone, $hash, $role)) {
        header("Location: ../View/login.php?success=" . urlencode("Registration successful. Please login."));
        exit;
    }

    header("Location: ../View/register.php?error=" . urlencode("Registration failed."));
    exit;
}

if ($action === "login") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === "") {
        header("Location: ../View/login.php?error=" . urlencode("Enter valid login information."));
        exit;
    }

    $user = $model->findByEmail($email);

    if (!$user || !password_verify($password, $user["password"])) {
        header("Location: ../View/login.php?error=" . urlencode("Invalid email or password."));
        exit;
    }

    if ($user["status"] !== "active") {
        header("Location: ../View/login.php?error=" . urlencode("This account is blocked."));
        exit;
    }

    session_regenerate_id(true);
    $_SESSION["user_id"] = (int)$user["user_id"];
    $_SESSION["name"] = $user["name"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["role"] = $user["role"];

    redirectDashboard($user["role"]);
}

header("Location: ../View/login.php");
exit;
