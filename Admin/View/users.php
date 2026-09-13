<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("admin");

require_once __DIR__ . "/../Model/AdminModel.php";

$model = new AdminModel();
$users = $model->getUsers((int)$_SESSION["user_id"]);

$pageTitle = "User Management";
$role = "admin";
$activeMenu = "users";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>User Management</h1>
        <p>View users and change their basic account status.</p>
    </div>
</div>

<?php if (!empty($_SESSION["error"])): ?>
    <div class="message error-message">
        <?php
        echo htmlspecialchars($_SESSION["error"]);
        unset($_SESSION["error"]);
        ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION["success"])): ?>
    <div class="message success-message">
        <?php
        echo htmlspecialchars($_SESSION["success"]);
        unset($_SESSION["success"]);
        ?>
    </div>
<?php endif; ?>

<div class="card">
    <?php if (empty($users)): ?>
        <p>No users found.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user["name"]); ?></td>
                        <td><?php echo htmlspecialchars($user["email"]); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($user["role"])); ?></td>
                        <td>
                            <span class="badge <?php echo $user["status"] === "active" ? "active" : "blocked"; ?>">
                                <?php echo htmlspecialchars(ucfirst($user["status"])); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user["status"] === "active"): ?>
                                <form method="post"
                                      action="../Controller/AdminController.php?action=blockUser"
                                      onsubmit="return confirm('Are you sure you want to block this user?');">
                                    <input type="hidden"
                                           name="user_id"
                                           value="<?php echo (int)$user["user_id"]; ?>">
                                    <button class="btn btn-danger btn-sm" type="submit">
                                        Block
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="post"
                                      action="../Controller/AdminController.php?action=activateUser"
                                      onsubmit="return confirm('Are you sure you want to activate this user?');">
                                    <input type="hidden"
                                           name="user_id"
                                           value="<?php echo (int)$user["user_id"]; ?>">
                                    <button class="btn btn-success btn-sm" type="submit">
                                        Activate
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
