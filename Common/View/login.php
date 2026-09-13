<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CareerBridge</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-box">
        <h1>CareerBridge</h1>
        <h2>Login</h2>
        <p class="auth-text">Login to continue to your account.</p>

        <?php if (!empty($_GET["error"])): ?>
            <div class="message error-message">
                <?php echo htmlspecialchars($_GET["error"]); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET["success"])): ?>
            <div class="message success-message">
                <?php echo htmlspecialchars($_GET["success"]); ?>
            </div>
        <?php endif; ?>

        <form method="post"
              action="../Controller/AuthController.php?action=login"
              data-validate="login">

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       placeholder="Enter your email">
                <small class="error" data-error-for="email"></small>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Enter your password">
                <small class="error" data-error-for="password"></small>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Login</button>
        </form>

        <p class="auth-link">
            Don't have an account?
            <a href="register.php">Register</a>
        </p>
    </div>
</div>

<div id="toast" class="toast"></div>
<script src="../js/validation.js"></script>
</body>
</html>
