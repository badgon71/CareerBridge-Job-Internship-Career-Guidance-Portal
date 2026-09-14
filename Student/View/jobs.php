<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("student");
require_once __DIR__ . "/../Model/StudentModel.php";

$model = new StudentModel();
$studentId = (int)$_SESSION["user_id"];
$jobs = $model->getJobs($studentId);

$pageTitle = "Browse Jobs";
$role = "student";
$activeMenu = "jobs";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>Browse Jobs</h1>
        <p>Explore currently available jobs and internships.</p>
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
    <div class="form-group">
        <label for="job_search">Search Jobs</label>
        <input type="text"
               id="job_search"
               data-job-search
               placeholder="Search by title, company, category or location">
    </div>
</div>

<div class="grid grid-2">
    <?php if (empty($jobs)): ?>
        <div class="card">
            <p>No active jobs are available.</p>
        </div>
    <?php else: ?>
        <?php foreach ($jobs as $job): ?>
            <div class="card" data-job-row>
                <h3><?php echo htmlspecialchars($job["job_title"]); ?></h3>

                <p>
                    <strong>
                        <?php
                        $company = $job["company_name"] ?: $job["employer_name"];
                        echo htmlspecialchars($company);
                        ?>
                    </strong>
                </p>

                <p><?php echo htmlspecialchars($job["category_name"]); ?></p>
                <p>
                    <?php echo htmlspecialchars($job["job_type"]); ?>
                    |
                    <?php echo htmlspecialchars($job["location"]); ?>
                </p>

                <p>
                    Salary:
                    <?php echo htmlspecialchars($job["salary"]); ?>
                </p>

                <p>
                    Deadline:
                    <?php echo htmlspecialchars(date("d M Y", strtotime($job["deadline"]))); ?>
                </p>

                <a class="btn btn-primary btn-sm"
                   href="jobDetails.php?job_id=<?php echo (int)$job["job_id"]; ?>">
                    View Details
                </a>

                <?php if ((int)$job["already_applied"] === 1): ?>
                    <span class="badge">
                        <?php echo htmlspecialchars(ucfirst($job["application_status"])); ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
