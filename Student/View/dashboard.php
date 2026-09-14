<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("student");
require_once __DIR__ . "/../Model/StudentModel.php";

$model = new StudentModel();
$studentId = (int)$_SESSION["user_id"];

$counts = $model->getDashboardCounts($studentId);
$recentJobs = $model->getRecentJobs($studentId, 4);
$recentApplications = $model->getRecentApplications($studentId, 4);

$pageTitle = "Student Dashboard";
$role = "student";
$activeMenu = "dashboard";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>Student Dashboard</h1>
        <p>Find jobs, manage applications and follow career guidance.</p>
    </div>
    <a class="btn btn-primary" href="jobs.php">Browse Jobs</a>
</div>

<div class="grid grid-2">
    <div class="card">
        <h3>Job Search</h3>
        <p>Available Jobs</p>
        <div class="stat-value"><?php echo $counts["available_jobs"]; ?></div>
        <a class="btn btn-secondary btn-sm" href="jobs.php">View Jobs</a>
    </div>

    <div class="card">
        <h3>My Applications</h3>
        <p>Total: <strong><?php echo $counts["applications"]; ?></strong></p>
        <p>Pending: <strong><?php echo $counts["pending"]; ?></strong></p>
        <p>Accepted: <strong><?php echo $counts["accepted"]; ?></strong></p>
        <a class="btn btn-secondary btn-sm" href="applications.php">View Applications</a>
    </div>
</div>

<div class="grid grid-2 section-gap">
    <div class="card">
        <h3>Latest Jobs</h3>

        <?php if (empty($recentJobs)): ?>
            <p>No active jobs are available right now.</p>
        <?php else: ?>
            <?php foreach ($recentJobs as $job): ?>
                <div class="section-gap">
                    <strong><?php echo htmlspecialchars($job["job_title"]); ?></strong>
                    <p>
                        <?php
                        $company = $job["company_name"] ?: $job["employer_name"];
                        echo htmlspecialchars($company);
                        ?>
                    </p>
                    <p>
                        <?php echo htmlspecialchars($job["category_name"]); ?>
                        |
                        <?php echo htmlspecialchars($job["location"]); ?>
                    </p>

                    <a class="btn btn-secondary btn-sm"
                       href="jobDetails.php?job_id=<?php echo (int)$job["job_id"]; ?>">
                        View Details
                    </a>

                    <?php if ((int)$job["already_applied"] === 1): ?>
                        <span class="sub">Already applied</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Recent Application Status</h3>

        <?php if (empty($recentApplications)): ?>
            <p>You have not submitted any applications yet.</p>
        <?php else: ?>
            <?php foreach ($recentApplications as $application): ?>
                <div class="section-gap">
                    <strong><?php echo htmlspecialchars($application["job_title"]); ?></strong>
                    <p>
                        <?php
                        $company = $application["company_name"] ?: $application["employer_name"];
                        echo htmlspecialchars($company);
                        ?>
                    </p>
                    <p>
                        Status:
                        <span class="badge">
                            <?php echo htmlspecialchars(ucfirst($application["status"])); ?>
                        </span>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
