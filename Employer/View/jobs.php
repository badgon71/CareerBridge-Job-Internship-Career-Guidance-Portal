<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("employer");
require_once __DIR__ . "/../Model/EmployerModel.php";

$model = new EmployerModel();
$jobs = $model->getJobs((int)$_SESSION["user_id"]);

$pageTitle = "My Job Posts";
$role = "employer";
$activeMenu = "jobs";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>My Job Posts</h1>
        <p>Create and manage the jobs posted by your company.</p>
    </div>

    <a class="btn btn-primary" href="jobForm.php">Post New Job</a>
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
    <?php if (empty($jobs)): ?>
        <p>No job posts found. Use <strong>Post New Job</strong> to create one.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Job</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Deadline</th>
                        <th>Applicants</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($jobs as $job): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($job["job_title"]); ?></strong><br>
                            <span class="sub"><?php echo htmlspecialchars($job["category_name"]); ?></span>
                        </td>

                        <td><?php echo htmlspecialchars($job["job_type"]); ?></td>
                        <td><?php echo htmlspecialchars($job["location"]); ?></td>
                        <td><?php echo htmlspecialchars(date("d M Y", strtotime($job["deadline"]))); ?></td>
                        <td><?php echo (int)$job["applicant_count"]; ?></td>

                        <td>
                            <div class="actions">
                                <a class="btn btn-secondary btn-sm"
                                   href="jobForm.php?edit=<?php echo (int)$job["job_id"]; ?>">
                                    Edit
                                </a>

                                <a class="btn btn-success btn-sm"
                                   href="applicants.php?job_id=<?php echo (int)$job["job_id"]; ?>">
                                    Applicants
                                </a>

                                <form method="post"
                                      action="../Controller/EmployerController.php?action=deleteJob"
                                      style="display:inline;"
                                      onsubmit="return confirm('Are you sure you want to delete this job post?');">
                                    <input type="hidden"
                                           name="job_id"
                                           value="<?php echo (int)$job["job_id"]; ?>">
                                    <button class="btn btn-danger btn-sm" type="submit">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
