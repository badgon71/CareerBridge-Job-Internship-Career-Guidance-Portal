<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("mentor");
require_once __DIR__ . "/../Model/MentorModel.php";

$model = new MentorModel();
$mentorId = (int)$_SESSION["user_id"];
$counts = $model->getDashboardCounts($mentorId);
$recentTips = $model->getRecentTips($mentorId);

$pageTitle = "Mentor Dashboard";
$role = "mentor";
$activeMenu = "dashboard";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>Mentor Dashboard</h1>
        <p>Overview of your career guidance posts.</p>
    </div>
    <a class="btn btn-primary" href="tipForm.php">Add Career Tip</a>
</div>

<div class="grid grid-2">
    <div class="card">
        <div class="stat-label">Total Career Tips</div>
        <div class="stat-value"><?php echo $counts["tips"]; ?></div>
    </div>

    <div class="card">
        <div class="stat-label">Added This Month</div>
        <div class="stat-value"><?php echo $counts["this_month"]; ?></div>
    </div>
</div>

<div class="card">
    <h3>Recent Career Tips</h3>

    <?php if (empty($recentTips)): ?>
        <p>No career tips added yet.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recentTips as $tip): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tip["title"]); ?></td>
                        <td><?php echo htmlspecialchars($tip["category"]); ?></td>
                        <td><?php echo htmlspecialchars(date("d M Y", strtotime($tip["created_at"]))); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
