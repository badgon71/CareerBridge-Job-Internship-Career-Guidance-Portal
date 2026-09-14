<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("employer");
require_once __DIR__ . "/../Model/EmployerModel.php";

$model = new EmployerModel();
$employerId = (int)$_SESSION["user_id"];

$counts = $model->getDashboardCounts($employerId);
$recentJobs = $model->getRecentJobs($employerId, 4);
$recentApplications = $model->getRecentApplications($employerId, 4);

$pageTitle = "Employer Dashboard";
$role = "employer";
$activeMenu = "dashboard";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>Employer Dashboard</h1>
        <p>Manage job posts and review student applications.</p>
    </div>

    <a class="btn btn-primary" href="jobForm.php">Post New Job</a>
</div>

<div class="grid grid-2">
    <div class="card">
        <div class="stat-label">Total Job Posts</div>
        <div class="stat-value"><?php echo $counts["jobs"]; ?></div>
    </div>

    <div class="card">
        <div class="stat-label">Active Job Posts</div>
        <div class="stat-value"><?php echo $counts["active_jobs"]; ?></div>
    </div>

    <div class="card">
        <div class="stat-label">Total Applications</div>
        <div class="stat-value"><?php echo $counts["applications"]; ?></div>
    </div>

    <div class="card">
        <div class="stat-label">Pending Applications</div>
        <div class="stat-value"><?php echo $counts["pending"]; ?></div>
    </div>
</div>

<div class="grid grid-2 section-gap">
    <div class="card">
        <h3>Recent Job Posts</h3>

        <?php if (empty($recentJobs)): ?>
            <p>No job posts yet.</p>
        <?php else: ?>
            <?php foreach ($recentJobs as $job): ?>
                <div class="section-gap">
                    <strong><?php echo htmlspecialchars($job["job_title"]); ?></strong>
                    <p>
                        <?php echo htmlspecialchars($job["category_name"]); ?>
                        |
                        <?php echo htmlspecialchars($job["job_type"]); ?>
                    </p>
                    <p>
                        Applicants: <?php echo (int)$job["applicant_count"]; ?>
                        |
                        Deadline:
                        <?php echo htmlspecialchars(date("d M Y", strtotime($job["deadline"]))); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Recent Applications</h3>

        <?php if (empty($recentApplications)): ?>
            <p>No applications received yet.</p>
        <?php else: ?>
            <?php foreach ($recentApplications as $application): ?>
                <div class="section-gap">
                    <strong><?php echo htmlspecialchars($application["student_name"]); ?></strong>
                    <p><?php echo htmlspecialchars($application["job_title"]); ?></p>
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
