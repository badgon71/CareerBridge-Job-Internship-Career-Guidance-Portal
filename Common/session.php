<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin()
{
    if (!isset($_SESSION["user_id"], $_SESSION["role"])) {
        header("Location: ../../Common/View/login.php?error=" . urlencode("Please login first."));
        exit;
    }
}

function requireRole($role)
{
    requireLogin();

    if ($_SESSION["role"] !== $role) {
        header("Location: ../../Common/View/login.php?error=" . urlencode("Access denied for this user role."));
        exit;
    }
}
