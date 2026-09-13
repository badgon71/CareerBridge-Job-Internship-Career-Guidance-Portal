<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("mentor");

$pageTitle = "Change Password";
$role = "mentor";
$activeMenu = "";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>Change Password</h1>
        <p>Enter your current password and a new strong password.</p>
    </div>
</div>

<?php if (!empty($_SESSION["error"])): ?>
    <div class="message error-message">
        <?php echo htmlspecialchars($_SESSION["error"]); unset($_SESSION["error"]); ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION["success"])): ?>
    <div class="message success-message">
        <?php echo htmlspecialchars($_SESSION["success"]); unset($_SESSION["success"]); ?>
    </div>
<?php endif; ?>

<div class="card">
    <form method="post"
          action="../Controller/MentorController.php?action=changePassword"
          data-validate="password">

        <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password">
            <small class="error" data-error-for="current_password"></small>
        </div>

        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password">
            <p class="help-text">
                Minimum 8 characters with uppercase, lowercase, number and special character.
            </p>
            <small class="error" data-error-for="new_password"></small>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password">
            <small class="error" data-error-for="confirm_password"></small>
        </div>

        <button class="btn btn-primary" type="submit">Change Password</button>
    </form>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
