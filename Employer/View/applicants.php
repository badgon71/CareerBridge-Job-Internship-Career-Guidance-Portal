<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("employer");
require_once __DIR__ . "/../Model/EmployerModel.php";

$model = new EmployerModel();
$employerId = (int)$_SESSION["user_id"];

$jobs = $model->getJobsWithApplicantCount($employerId);
$selectedJobId = (int)($_GET["job_id"] ?? 0);

$selectedJob = null;
$applicants = [];

if ($selectedJobId > 0) {
    $selectedJob = $model->getJobById($selectedJobId, $employerId);

    if (!$selectedJob) {
        $_SESSION["error"] = "Job post not found.";
        header("Location: applicants.php");
        exit;
    }

    $applicants = $model->getApplicantsForJob($selectedJobId, $employerId);
}

$pageTitle = "Applicants";
$role = "employer";
$activeMenu = "applicants";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>Job Applicants</h1>
        <p>Select one of your job posts to review applications.</p>
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
        <h3>Your Job Posts</h3>

        <?php if (empty($jobs)): ?>
            <p>No jobs available.</p>
        <?php else: ?>
            <?php foreach ($jobs as $job): ?>
                <div class="section-gap">
                    <strong><?php echo htmlspecialchars($job["job_title"]); ?></strong>
                    <p>
                        Applicants: <?php echo (int)$job["applicant_count"]; ?>
                        |
                        Deadline:
                        <?php echo htmlspecialchars(date("d M Y", strtotime($job["deadline"]))); ?>
                    </p>

                    <a class="btn btn-secondary btn-sm"
                       href="applicants.php?job_id=<?php echo (int)$job["job_id"]; ?>">
                        View Applicants
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="card">
        <?php if (!$selectedJob): ?>
            <h3>Applicant List</h3>
            <p>Select a job from the left side.</p>

        <?php else: ?>
            <h3><?php echo htmlspecialchars($selectedJob["job_title"]); ?></h3>

            <?php if (empty($applicants)): ?>
                <p>No applications received for this job yet.</p>
            <?php else: ?>
                <?php foreach ($applicants as $applicant): ?>
                    <div class="section-gap">
                        <strong><?php echo htmlspecialchars($applicant["student_name"]); ?></strong>

                        <p>
                            Email: <?php echo htmlspecialchars($applicant["email"]); ?><br>
                            Phone: <?php echo htmlspecialchars($applicant["phone"]); ?><br>
                            Expected Salary:
                            <?php echo htmlspecialchars($applicant["expected_salary"] ?: "Not provided"); ?>
                        </p>

                        <p>
                            <strong>Cover Note:</strong><br>
                            <?php echo nl2br(htmlspecialchars($applicant["cover_note"])); ?>
                        </p>

                        <p>
                            Status:
                            <span class="badge">
                                <?php echo htmlspecialchars(ucfirst($applicant["status"])); ?>
                            </span>
                        </p>

                        <div class="actions">
                            <?php if (!empty($applicant["cv_file"])): ?>
                                <a class="btn btn-secondary btn-sm"
                                   href="../Controller/EmployerController.php?action=downloadCv&application_id=<?php echo (int)$applicant["application_id"]; ?>">
                                    Download CV
                                </a>
                            <?php else: ?>
                                <span class="sub">No CV uploaded</span>
                            <?php endif; ?>

                            <form method="post"
                                  action="../Controller/EmployerController.php?action=acceptApplication"
                                  style="display:inline;"
                                  onsubmit="return confirm('Accept this application?');">
                                <input type="hidden"
                                       name="application_id"
                                       value="<?php echo (int)$applicant["application_id"]; ?>">
                                <input type="hidden"
                                       name="job_id"
                                       value="<?php echo (int)$selectedJobId; ?>">
                                <button class="btn btn-success btn-sm" type="submit">
                                    Accept
                                </button>
                            </form>

                            <form method="post"
                                  action="../Controller/EmployerController.php?action=rejectApplication"
                                  style="display:inline;"
                                  onsubmit="return confirm('Reject this application?');">
                                <input type="hidden"
                                       name="application_id"
                                       value="<?php echo (int)$applicant["application_id"]; ?>">
                                <input type="hidden"
                                       name="job_id"
                                       value="<?php echo (int)$selectedJobId; ?>">
                                <button class="btn btn-danger btn-sm" type="submit">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
