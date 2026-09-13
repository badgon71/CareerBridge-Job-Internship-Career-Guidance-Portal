<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("admin");

require_once __DIR__ . "/../Model/AdminModel.php";

$model = new AdminModel();
$admin = $model->getUserById((int)$_SESSION["user_id"]);

$pageTitle = "Profile";
$role = "admin";
$activeMenu = "";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>Admin Profile</h1>
        <p>View and update your basic information.</p>
    </div>
</div>

<?php if (!empty($_SESSION["error"])): ?>
    <div class="message error-message">
        <?php
        echo htmlspecialchars($_SESSION["error"]);
        unset($_SESSION["error"]);
        ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION["success"])): ?>
    <div class="message success-message">
        <?php
        echo htmlspecialchars($_SESSION["success"]);
        unset($_SESSION["success"]);
        ?>
    </div>
<?php endif; ?>

<div class="card">
    <form method="post"
          action="../Controller/AdminController.php?action=updateProfile"
          data-validate="profile">

        <div class="form-grid">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text"
                       id="name"
                       name="name"
                       value="<?php echo htmlspecialchars($admin["name"] ?? ""); ?>">
                <small class="error" data-error-for="name"></small>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="<?php echo htmlspecialchars($admin["email"] ?? ""); ?>">
                <small class="error" data-error-for="email"></small>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text"
                       id="phone"
                       name="phone"
                       maxlength="11"
                       value="<?php echo htmlspecialchars($admin["phone"] ?? ""); ?>">
                <small class="error" data-error-for="phone"></small>
            </div>

            <div class="form-group">
                <label>Role</label>
                <input type="text" value="Admin" readonly>
            </div>
        </div>

        <button class="btn btn-primary" type="submit">Update Profile</button>
    </form>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
