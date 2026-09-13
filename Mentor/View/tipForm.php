<?php
require_once __DIR__ . "/../../Common/session.php";
requireRole("mentor");
require_once __DIR__ . "/../Model/MentorModel.php";

$model = new MentorModel();
$mentorId = (int)$_SESSION["user_id"];

$tip = null;
$editId = (int)($_GET["edit"] ?? 0);

if ($editId > 0) {
    $tip = $model->getTipById($editId, $mentorId);

    if (!$tip) {
        $_SESSION["error"] = "Career tip not found.";
        header("Location: tips.php");
        exit;
    }
}

$pageTitle = $tip ? "Edit Career Tip" : "Add Career Tip";
$role = "mentor";
$activeMenu = "tipform";

include __DIR__ . "/../../Common/Includes/header.php";
include __DIR__ . "/../../Common/Includes/sidebar.php";
?>

<div class="page-head">
    <div>
        <h1><?php echo $tip ? "Edit Career Tip" : "Add Career Tip"; ?></h1>
        <p>Keep the guidance simple and useful.</p>
    </div>
</div>

<?php if (!empty($_SESSION["error"])): ?>
    <div class="message error-message">
        <?php echo htmlspecialchars($_SESSION["error"]); unset($_SESSION["error"]); ?>
    </div>
<?php endif; ?>

<div class="card">
    <form method="post"
          action="../Controller/MentorController.php?action=<?php echo $tip ? "updateTip" : "createTip"; ?>"
          data-validate="tip">

        <?php if ($tip): ?>
            <input type="hidden" name="tip_id" value="<?php echo (int)$tip["tip_id"]; ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text"
                   id="title"
                   name="title"
                   value="<?php echo htmlspecialchars($tip["title"] ?? ""); ?>"
                   placeholder="Example: How to Prepare for an Interview">
            <small class="error" data-error-for="title"></small>
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category">
                <option value="">Select category</option>
                <?php
                $options = ["Interview", "CV & Resume", "Career Planning"];
                foreach ($options as $option):
                    $selected = (($tip["category"] ?? "") === $option) ? "selected" : "";
                ?>
                    <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($option); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="error" data-error-for="category"></small>
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content"
                      name="content"
                      placeholder="Write the career tip..."><?php echo htmlspecialchars($tip["content"] ?? ""); ?></textarea>
            <small class="error" data-error-for="content"></small>
        </div>

        <button class="btn btn-primary" type="submit">
            <?php echo $tip ? "Update Tip" : "Add Tip"; ?>
        </button>

        <a class="btn btn-secondary" href="tips.php">Cancel</a>
    </form>
</div>

<?php include __DIR__ . "/../../Common/Includes/footer.php"; ?>
