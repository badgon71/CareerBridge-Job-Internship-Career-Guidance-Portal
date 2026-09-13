<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("admin");

require_once __DIR__ . "/../Model/AdminModel.php";

$model = new AdminModel();
$counts = $model->getDashboardCounts();
$recentCategories = $model->getRecentCategories(5);

$pageTitle = "Admin Dashboard";
$role = "admin";
$activeMenu = "dashboard";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>Admin Dashboard</h1>
        <p>Simple overview of the CareerBridge system.</p>
    </div>
</div>

<div class="grid grid-3">
    <div class="card">
        <div class="stat-label">Total Users</div>
        <div class="stat-value"><?php echo $counts["users"]; ?></div>
    </div>

    <div class="card">
        <div class="stat-label">Job Categories</div>
        <div class="stat-value"><?php echo $counts["categories"]; ?></div>
    </div>

    <div class="card">
        <div class="stat-label">Total Jobs</div>
        <div class="stat-value"><?php echo $counts["jobs"]; ?></div>
    </div>
</div>

<div class="card">
    <h3>Recent Categories</h3>

    <?php if (empty($recentCategories)): ?>
        <p>No categories found.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recentCategories as $category): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category["category_name"]); ?></td>
                        <td><?php echo htmlspecialchars(date("d M Y", strtotime($category["created_at"]))); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
