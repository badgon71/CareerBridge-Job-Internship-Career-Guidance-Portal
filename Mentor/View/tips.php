<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("mentor");
require_once __DIR__ . "/../Model/MentorModel.php";

$model = new MentorModel();
$tips = $model->getTips((int)$_SESSION["user_id"]);

$pageTitle = "Career Tips";
$role = "mentor";
$activeMenu = "tips";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>Career Tips</h1>
        <p>Add, view, edit and delete your career guidance posts.</p>
    </div>
    <a class="btn btn-primary" href="tipForm.php">Add Career Tip</a>
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
    <?php if (empty($tips)): ?>
        <p>No career tips found.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($tips as $tip): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tip["title"]); ?></td>
                        <td><?php echo htmlspecialchars($tip["category"]); ?></td>
                        <td><?php echo htmlspecialchars(date("d M Y", strtotime($tip["created_at"]))); ?></td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-secondary btn-sm"
                                   href="tipForm.php?edit=<?php echo (int)$tip["tip_id"]; ?>">
                                    Edit
                                </a>

                                <form method="post"
                                      action="../Controller/MentorController.php?action=deleteTip"
                                      style="display:inline;"
                                      onsubmit="return confirm('Are you sure you want to delete this career tip?');">
                                    <input type="hidden" name="tip_id" value="<?php echo (int)$tip["tip_id"]; ?>">
                                    <button class="btn btn-danger btn-sm" type="submit">Delete</button>
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
