<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("employer");
require_once __DIR__ . "/../Model/EmployerModel.php";

$model = new EmployerModel();
$employer = $model->getProfile((int)$_SESSION["user_id"]);

$pageTitle = "Employer Profile";
$role = "employer";
$activeMenu = "";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>Employer Profile</h1>
        <p>Update your contact and company information.</p>
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

<div class="grid grid-2">
    <div class="card">
        <h3>Contact Information</h3>

        <form method="post"
              action="../Controller/EmployerController.php?action=updateProfile"
              data-validate="profile">

            <div class="form-group">
                <label for="name">Contact Person</label>
                <input type="text"
                       id="name"
                       name="name"
                       value="<?php echo htmlspecialchars($employer["name"] ?? ""); ?>">
                <small class="error" data-error-for="name"></small>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="<?php echo htmlspecialchars($employer["email"] ?? ""); ?>">
                <small class="error" data-error-for="email"></small>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text"
                       id="phone"
                       name="phone"
                       maxlength="11"
                       value="<?php echo htmlspecialchars($employer["phone"] ?? ""); ?>">
                <small class="error" data-error-for="phone"></small>
            </div>
    </div>

    <div class="card">
        <h3>Company Information</h3>

            <div class="form-group">
                <label for="company_name">Company Name</label>
                <input type="text"
                       id="company_name"
                       name="company_name"
                       value="<?php echo htmlspecialchars($employer["company_name"] ?? ""); ?>"
                       placeholder="Example: CareerBridge Ltd.">
            </div>

            <div class="form-group">
                <label for="industry">Industry</label>
                <input type="text"
                       id="industry"
                       name="industry"
                       value="<?php echo htmlspecialchars($employer["industry"] ?? ""); ?>"
                       placeholder="Example: Information Technology">
            </div>

            <button class="btn btn-primary" type="submit">
                Save Profile
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
