<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("student");
require_once __DIR__ . "/../Model/StudentModel.php";

$model = new StudentModel();
$tips = $model->getCareerTips();

$pageTitle = "Career Tips";
$role = "student";
$activeMenu = "tips";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>Career Tips</h1>
        <p>Read practical guidance shared by CareerBridge mentors.</p>
    </div>
</div>

<?php if (empty($tips)): ?>
    <div class="card">
        <p>No career tips are available yet.</p>
    </div>
<?php else: ?>
    <div class="grid grid-2">
        <?php foreach ($tips as $tip): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($tip["title"]); ?></h3>

                <p>
                    <strong><?php echo htmlspecialchars($tip["category"]); ?></strong>
                </p>

                <p><?php echo nl2br(htmlspecialchars($tip["content"])); ?></p>

                <p class="sub">
                    Mentor: <?php echo htmlspecialchars($tip["mentor_name"]); ?>
                    <?php if (!empty($tip["expertise"])): ?>
                        | <?php echo htmlspecialchars($tip["expertise"]); ?>
                    <?php endif; ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
