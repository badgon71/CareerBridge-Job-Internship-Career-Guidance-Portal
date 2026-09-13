<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | CareerBridge</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <h1>Login</h1>
    <p>Enter your account information.</p>
    <?php if (!empty($_GET["error"])): ?>
  <div class="notice" style="border-color:#fecaca;background:#fef2f2;color:#991b1b;margin-bottom:12px;">
    <?php echo htmlspecialchars($_GET["error"]); ?>
  </div>
<?php endif; ?>
<?php if (!empty($_GET["success"])): ?>
  <div class="notice" style="border-color:#bbf7d0;background:#f0fdf4;color:#166534;margin-bottom:12px;">
    <?php echo htmlspecialchars($_GET["success"]); ?>
  </div>
<?php endif; ?>
    <form method="post" action="../Controller/AuthController.php?action=login" data-validate="login">
      <div class="field">
        <label>Email</label>
        <input class="input" type="email" name="email" placeholder="name@example.com">
        <small class="error" data-error-for="email"></small>
      </div>
      <div class="field">
        <label>Password</label>
        <input class="input" type="password" name="password">
        <small class="error" data-error-for="password"></small>
      </div>
      <button class="btn btn-primary" type="submit">Login</button>
    </form>
    <p style="margin-top:14px">No account? <a href="register.php" style="color:#2563eb">Register</a></p>
  </div>
</div>
<div id="toast" class="toast"></div>
<script src="../js/validation.js"></script>
</body>
</html>
