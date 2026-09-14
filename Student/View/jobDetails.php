<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("student");
require_once __DIR__ . "/../Model/StudentModel.php";

$model = new StudentModel();
$studentId = (int)$_SESSION["user_id"];
$jobId = (int)($_GET["job_id"] ?? 0);

$job = $model->getJobById($jobId, $studentId);

if (!$job) {
    $_SESSION["error"] = "Job post not found.";
    header("Location: jobs.php");
    exit;
}

$profile = $model->getProfile($studentId);
$hasCv = !empty($profile["cv_file"])
    && is_file(__DIR__ . "/../../Common/uploads/cv/" . basename($profile["cv_file"]));

$isExpired = $job["deadline"] < date("Y-m-d");
$hasApplied = !empty($job["application_id"]);

$pageTitle = "Job Details";
$role = "student";
$activeMenu = "jobs";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1><?php echo htmlspecialchars($job["job_title"]); ?></h1>
        <p>
            <?php
            $company = $job["company_name"] ?: $job["employer_name"];
            echo htmlspecialchars($company);
            ?>
        </p>
    </div>

    <a class="btn btn-secondary" href="jobs.php">Back to Jobs</a>
</div>

<?php if (!empty($_SESSION["error"])): ?>
    <div class="message error-message">
        <?php echo htmlspecialchars($_SESSION["error"]); unset($_SESSION["error"]); ?>
    </div>
<?php endif; ?>

<div class="grid grid-2">
    <div class="card">
        <h3>Job Information</h3>

        <p><strong>Category:</strong> <?php echo htmlspecialchars($job["category_name"]); ?></p>
        <p><strong>Type:</strong> <?php echo htmlspecialchars($job["job_type"]); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($job["location"]); ?></p>
        <p><strong>Salary:</strong> <?php echo htmlspecialchars($job["salary"]); ?></p>
        <p>
            <strong>Deadline:</strong>
            <?php echo htmlspecialchars(date("d M Y", strtotime($job["deadline"]))); ?>
        </p>
    </div>

    <div class="card">
        <h3>Application</h3>

        <?php if ($hasApplied): ?>
            <p>
                You already applied for this job.
                Status:
                <span class="badge">
                    <?php echo htmlspecialchars(ucfirst($job["application_status"])); ?>
                </span>
            </p>

            <a class="btn btn-secondary" href="applications.php">My Applications</a>

        <?php elseif ($isExpired): ?>
            <div class="notice">The application deadline has passed.</div>

        <?php elseif (!$hasCv): ?>
            <div class="notice">
                Upload your CV before applying.
            </div>

            <a class="btn btn-primary" href="profile.php">Upload CV</a>

        <?php else: ?>
            <p>Your CV is ready. You can submit an application for this job.</p>

            <a class="btn btn-primary"
               href="applicationForm.php?job_id=<?php echo (int)$job["job_id"]; ?>">
                Apply Now
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card section-gap">
    <h3>Job Description</h3>
    <p><?php echo nl2br(htmlspecialchars($job["description"])); ?></p>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
