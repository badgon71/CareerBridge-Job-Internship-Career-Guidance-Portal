<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | CareerBridge</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <h1>Register</h1>
    <p>Create a Student, Employer, or Mentor account.</p>
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
    <form method="post" action="../Controller/AuthController.php?action=register" data-validate="register">
      <div class="field"><label>Name</label><input class="input" name="name"><small class="error" data-error-for="name"></small></div>
      <div class="field"><label>Email</label><input class="input" type="email" name="email"><small class="error" data-error-for="email"></small></div>
      <div class="field"><label>Phone</label><input class="input" name="phone" maxlength="11" placeholder="01XXXXXXXXX"><small class="error" data-error-for="phone"></small></div>
      <div class="field">
        <label>Role</label>
        <select class="select" name="role">
          <option value="">Select role</option>
          <option value="student">Student</option>
          <option value="employer">Employer</option>
          <option value="mentor">Mentor</option>
        </select>
        <small class="error" data-error-for="role"></small>
      </div>
      <div class="field">
        <label>Password</label>
        <input class="input" type="password" name="password">
        <div class="hint">8+ chars, uppercase, lowercase, number and special character.</div>
        <small class="error" data-error-for="password"></small>
      </div>
      <div class="field"><label>Confirm Password</label><input class="input" type="password" name="confirm_password"><small class="error" data-error-for="confirm_password"></small></div>
      <button class="btn btn-primary" type="submit">Register</button>
    </form>
    <p style="margin-top:14px">Already registered? <a href="login.php" style="color:#2563eb">Login</a></p>
  </div>
</div>
<div id="toast" class="toast"></div>
<script src="../js/validation.js"></script>
</body>
</html>
