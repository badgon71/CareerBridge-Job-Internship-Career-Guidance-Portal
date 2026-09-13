<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("admin");

require_once __DIR__ . "/../Model/AdminModel.php";

$model = new AdminModel();
$categories = $model->getCategories();

$editCategory = null;
$editId = (int)($_GET["edit"] ?? 0);

if ($editId > 0) {
    $editCategory = $model->getCategoryById($editId);
}

$pageTitle = "Job Categories";
$role = "admin";
$activeMenu = "categories";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1>Job Categories</h1>
        <p>Add, view, edit and delete job categories.</p>
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

<div class="grid grid-2">
    <div class="card">
        <h3>Category List</h3>

        <?php if (empty($categories)): ?>
            <p>No categories available.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category["category_name"]); ?></td>
                            <td><?php echo htmlspecialchars($category["description"] ?? ""); ?></td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-secondary btn-sm"
                                       href="categories.php?edit=<?php echo (int)$category["category_id"]; ?>">
                                        Edit
                                    </a>

                                    <form method="post"
                                          action="../Controller/AdminController.php?action=deleteCategory"
                                          style="display:inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        <input type="hidden"
                                               name="category_id"
                                               value="<?php echo (int)$category["category_id"]; ?>">
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

    <div class="card">
        <h3><?php echo $editCategory ? "Edit Category" : "Add Category"; ?></h3>

        <form method="post"
              action="../Controller/AdminController.php?action=<?php echo $editCategory ? "updateCategory" : "createCategory"; ?>"
              data-validate="category">

            <?php if ($editCategory): ?>
                <input type="hidden"
                       name="category_id"
                       value="<?php echo (int)$editCategory["category_id"]; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="category_name">Category Name</label>
                <input type="text"
                       id="category_name"
                       name="category_name"
                       value="<?php echo htmlspecialchars($editCategory["category_name"] ?? ""); ?>"
                       placeholder="Example: Web Development">

                <small class="error" data-error-for="category_name"></small>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description"
                          name="description"
                          placeholder="Short description"><?php echo htmlspecialchars($editCategory["description"] ?? ""); ?></textarea>
            </div>

            <button class="btn btn-primary" type="submit">
                <?php echo $editCategory ? "Update Category" : "Add Category"; ?>
            </button>

            <?php if ($editCategory): ?>
                <a class="btn btn-secondary" href="categories.php">Cancel</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
