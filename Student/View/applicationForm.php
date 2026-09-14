<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("student");
require_once __DIR__ . "/../Model/StudentModel.php";

$model = new StudentModel();
$studentId = (int)$_SESSION["user_id"];

$application = null;
$job = null;

$editId = (int)($_GET["edit"] ?? 0);
$jobId = (int)($_GET["job_id"] ?? 0);

if ($editId > 0) {
    $application = $model->getApplicationById($editId, $studentId);

    if (!$application) {
        $_SESSION["error"] = "Application not found.";
        header("Location: applications.php");
        exit;
    }

    if ($application["status"] !== "pending") {
        $_SESSION["error"] = "Only pending applications can be edited.";
        header("Location: applications.php");
        exit;
    }
} else {
    $job = $model->getJobById($jobId, $studentId);

    if (!$job) {
        $_SESSION["error"] = "Job post not found.";
        header("Location: jobs.php");
        exit;
    }

    if (!empty($job["application_id"])) {
        $_SESSION["error"] = "You have already applied for this job.";
        header("Location: applications.php");
        exit;
    }
}

$pageTitle = $application ? "Edit Application" : "Apply for Job";
$role = "student";
$activeMenu = "applications";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1><?php echo $application ? "Edit Application" : "Job Application"; ?></h1>
        <p>
            <?php
            if ($application) {
                echo htmlspecialchars($application["job_title"]);
            } else {
                echo htmlspecialchars($job["job_title"]);
            }
            ?>
        </p>
    </div>

    <a class="btn btn-secondary"
       href="<?php echo $application ? "applications.php" : "jobDetails.php?job_id=" . (int)$jobId; ?>">
        Back
    </a>
</div>

<?php if (!empty($_SESSION["error"])): ?>
    <div class="message error-message">
        <?php echo htmlspecialchars($_SESSION["error"]); unset($_SESSION["error"]); ?>
    </div>
<?php endif; ?>

<div class="card">
    <form method="post"
          action="../Controller/StudentController.php?action=<?php echo $application ? "updateApplication" : "createApplication"; ?>"
          data-validate="application">

        <?php if ($application): ?>
            <input type="hidden"
                   name="application_id"
                   value="<?php echo (int)$application["application_id"]; ?>">
        <?php else: ?>
            <input type="hidden"
                   name="job_id"
                   value="<?php echo (int)$job["job_id"]; ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="expected_salary">Expected Salary</label>
            <input type="number"
                   id="expected_salary"
                   name="expected_salary"
                   min="0"
                   step="0.01"
                   value="<?php echo htmlspecialchars($application["expected_salary"] ?? ""); ?>"
                   placeholder="Optional">
            <small class="error" data-error-for="expected_salary"></small>
        </div>

        <div class="form-group">
            <label for="cover_note">Cover Note</label>
            <textarea id="cover_note"
                      name="cover_note"
                      placeholder="Write a short note about why you are interested in this job..."><?php echo htmlspecialchars($application["cover_note"] ?? ""); ?></textarea>
            <small class="error" data-error-for="cover_note"></small>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">
                <?php echo $application ? "Update Application" : "Submit Application"; ?>
            </button>

            <a class="btn btn-secondary"
               href="<?php echo $application ? "applications.php" : "jobDetails.php?job_id=" . (int)$job["job_id"]; ?>">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
