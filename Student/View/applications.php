<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("student");
require_once __DIR__ . "/../Model/StudentModel.php";

$model = new StudentModel();
$applications = $model->getApplications((int)$_SESSION["user_id"]);

$pageTitle = "My Applications";
$role = "student";
$activeMenu = "applications";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>My Applications</h1>
        <p>Track, edit or withdraw your job applications.</p>
    </div>

    <a class="btn btn-primary" href="jobs.php">Browse More Jobs</a>
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

<?php if (empty($applications)): ?>
    <div class="card">
        <p>You have not submitted any applications yet.</p>
    </div>
<?php else: ?>
    <div class="grid grid-2">
        <?php foreach ($applications as $application): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($application["job_title"]); ?></h3>

                <p>
                    <strong>
                        <?php
                        $company = $application["company_name"] ?: $application["employer_name"];
                        echo htmlspecialchars($company);
                        ?>
                    </strong>
                </p>

                <p>
                    <?php echo htmlspecialchars($application["category_name"]); ?>
                    |
                    <?php echo htmlspecialchars($application["location"]); ?>
                </p>

                <p>
                    Status:
                    <span class="badge">
                        <?php echo htmlspecialchars(ucfirst($application["status"])); ?>
                    </span>
                </p>

                <p>
                    Expected Salary:
                    <?php echo htmlspecialchars(
                        $application["expected_salary"] !== null
                            ? $application["expected_salary"]
                            : "Not provided"
                    ); ?>
                </p>

                <p>
                    Applied:
                    <?php echo htmlspecialchars(date("d M Y", strtotime($application["applied_at"]))); ?>
                </p>

                <a class="btn btn-secondary btn-sm"
                   href="jobDetails.php?job_id=<?php echo (int)$application["job_id"]; ?>">
                    Job Details
                </a>

                <?php if ($application["status"] === "pending"): ?>
                    <a class="btn btn-primary btn-sm"
                       href="applicationForm.php?edit=<?php echo (int)$application["application_id"]; ?>">
                        Edit
                    </a>

                    <form method="post"
                          action="../Controller/StudentController.php?action=deleteApplication"
                          style="display:inline;"
                          onsubmit="return confirm('Withdraw this application?');">
                        <input type="hidden"
                               name="application_id"
                               value="<?php echo (int)$application["application_id"]; ?>">

                        <button class="btn btn-danger btn-sm" type="submit">
                            Withdraw
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
