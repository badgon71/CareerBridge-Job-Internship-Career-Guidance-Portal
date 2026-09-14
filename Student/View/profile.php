<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("student");
require_once __DIR__ . "/../Model/StudentModel.php";

$model = new StudentModel();
$student = $model->getProfile((int)$_SESSION["user_id"]);

$cvExists = !empty($student["cv_file"])
    && is_file(__DIR__ . "/../../Common/uploads/cv/" . basename($student["cv_file"]));

$pageTitle = "Student Profile";
$role = "student";
$activeMenu = "";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>Student Profile</h1>
        <p>Keep your contact information and CV up to date.</p>
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
        <h3>Current CV</h3>

        <?php if ($cvExists): ?>
            <p>Your CV is uploaded and ready for job applications.</p>

            <a class="btn btn-secondary"
               target="_blank"
               href="../../Common/uploads/cv/<?php echo rawurlencode(basename($student["cv_file"])); ?>">
                View Current CV
            </a>
        <?php else: ?>
            <p>No CV uploaded yet.</p>
        <?php endif; ?>

        <p class="help-text">
            PDF only. Maximum file size: 2 MB.
        </p>
    </div>

    <div class="card">
        <h3>Profile Information</h3>

        <form method="post"
              enctype="multipart/form-data"
              action="../Controller/StudentController.php?action=updateProfile"
              data-validate="profile">

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text"
                       id="name"
                       name="name"
                       value="<?php echo htmlspecialchars($student["name"] ?? ""); ?>">
                <small class="error" data-error-for="name"></small>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="<?php echo htmlspecialchars($student["email"] ?? ""); ?>">
                <small class="error" data-error-for="email"></small>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text"
                       id="phone"
                       name="phone"
                       maxlength="11"
                       value="<?php echo htmlspecialchars($student["phone"] ?? ""); ?>">
                <small class="error" data-error-for="phone"></small>
            </div>

            <div class="form-group">
                <label for="cv_file">
                    <?php echo $cvExists ? "Replace CV" : "Upload CV"; ?>
                </label>

                <input type="file"
                       id="cv_file"
                       name="cv_file"
                       accept=".pdf,application/pdf">

                <small class="error" data-error-for="cv_file"></small>
            </div>

            <button class="btn btn-primary" type="submit">
                Save Profile
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
