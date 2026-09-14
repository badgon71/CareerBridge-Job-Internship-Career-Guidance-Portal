<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("employer");
require_once __DIR__ . "/../Model/EmployerModel.php";

$model = new EmployerModel();
$employerId = (int)$_SESSION["user_id"];

$categories = $model->getCategories();

$job = null;
$editId = (int)($_GET["edit"] ?? 0);

if ($editId > 0) {
    $job = $model->getJobById($editId, $employerId);

    if (!$job) {
        $_SESSION["error"] = "Job post not found.";
        header("Location: jobs.php");
        exit;
    }
}

$pageTitle = $job ? "Edit Job" : "Post New Job";
$role = "employer";
$activeMenu = "jobs";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1><?php echo $job ? "Edit Job Post" : "Post a New Job"; ?></h1>
        <p>Enter the job information below.</p>
    </div>

    <a class="btn btn-secondary" href="jobs.php">Back to Jobs</a>
</div>

<?php if (!empty($_SESSION["error"])): ?>
    <div class="message error-message">
        <?php echo htmlspecialchars($_SESSION["error"]); unset($_SESSION["error"]); ?>
    </div>
<?php endif; ?>

<?php if (empty($categories)): ?>
    <div class="notice">
        No job category is available. An Admin must create a category before a job can be posted.
    </div>
<?php endif; ?>

<div class="card">
    <form method="post"
          action="../Controller/EmployerController.php?action=<?php echo $job ? "updateJob" : "createJob"; ?>"
          data-validate="job">

        <?php if ($job): ?>
            <input type="hidden" name="job_id" value="<?php echo (int)$job["job_id"]; ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-group">
                <label for="job_title">Job Title</label>
                <input type="text"
                       id="job_title"
                       name="job_title"
                       value="<?php echo htmlspecialchars($job["job_title"] ?? ""); ?>"
                       placeholder="Example: Junior Web Developer">
                <small class="error" data-error-for="job_title"></small>
            </div>

            <div class="form-group">
                <label for="category_id">Job Category</label>
                <select id="category_id" name="category_id">
                    <option value="">Select category</option>

                    <?php foreach ($categories as $category): ?>
                        <?php
                        $selected = ((int)($job["category_id"] ?? 0) === (int)$category["category_id"])
                            ? "selected"
                            : "";
                        ?>
                        <option value="<?php echo (int)$category["category_id"]; ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($category["category_name"]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="error" data-error-for="category_id"></small>
            </div>

            <div class="form-group">
                <label for="job_type">Job Type</label>
                <select id="job_type" name="job_type">
                    <option value="">Select type</option>

                    <?php
                    $types = ["Full-time", "Part-time", "Internship", "Contract"];
                    foreach ($types as $type):
                        $selected = (($job["job_type"] ?? "") === $type) ? "selected" : "";
                    ?>
                        <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($type); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="error" data-error-for="job_type"></small>
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input type="text"
                       id="location"
                       name="location"
                       value="<?php echo htmlspecialchars($job["location"] ?? ""); ?>"
                       placeholder="Example: Dhaka">
                <small class="error" data-error-for="location"></small>
            </div>

            <div class="form-group">
                <label for="salary">Salary</label>
                <input type="text"
                       id="salary"
                       name="salary"
                       value="<?php echo htmlspecialchars($job["salary"] ?? ""); ?>"
                       placeholder="Example: 25000 - 35000 BDT">
                <small class="error" data-error-for="salary"></small>
            </div>

            <div class="form-group">
                <label for="deadline">Application Deadline</label>
                <input type="date"
                       id="deadline"
                       name="deadline"
                       min="<?php echo date("Y-m-d"); ?>"
                       value="<?php echo htmlspecialchars($job["deadline"] ?? ""); ?>">
                <small class="error" data-error-for="deadline"></small>
            </div>

            <div class="form-group full">
                <label for="description">Job Description</label>
                <textarea id="description"
                          name="description"
                          placeholder="Write the responsibilities and requirements..."><?php echo htmlspecialchars($job["description"] ?? ""); ?></textarea>
                <small class="error" data-error-for="description"></small>
            </div>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary"
                    type="submit"
                    <?php echo empty($categories) ? "disabled" : ""; ?>>
                <?php echo $job ? "Update Job" : "Post Job"; ?>
            </button>

            <a class="btn btn-secondary" href="jobs.php">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
