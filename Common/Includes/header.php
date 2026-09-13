<?php
$pageTitle = $pageTitle ?? "CareerBridge";
$role = strtolower($role ?? "student");
$activeMenu = $activeMenu ?? "";
$userName = $_SESSION["name"] ?? ($userName ?? "User");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle); ?> | CareerBridge</title>
  <link rel="stylesheet" href="../../Common/css/style.css">
</head>
<body>
<div class="layout">
