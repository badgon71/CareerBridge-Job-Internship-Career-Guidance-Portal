<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | CareerBridge</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-box register-box">
        <h1>CareerBridge</h1>
        <h2>Create Account</h2>
        <p class="auth-text">Register as a Student, Employer, or Career Mentor.</p>

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
              action="../Controller/AuthController.php?action=register"
              data-validate="register">

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text"
                       id="name"
                       name="name"
                       placeholder="Enter your full name">
                <small class="error" data-error-for="name"></small>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       placeholder="Enter your email">
                <small class="error" data-error-for="email"></small>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text"
                       id="phone"
                       name="phone"
                       maxlength="11"
                       placeholder="01XXXXXXXXX">
                <small class="error" data-error-for="phone"></small>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="">Select your role</option>
                    <option value="student">Student</option>
                    <option value="employer">Employer</option>
                    <option value="mentor">Career Mentor</option>
                </select>
                <small class="error" data-error-for="role"></small>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Enter a password">
                <p class="help-text">
                    Minimum 8 characters with uppercase, lowercase, number and special character.
                </p>
                <small class="error" data-error-for="password"></small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password"
                       id="confirm_password"
                       name="confirm_password"
                       placeholder="Enter password again">
                <small class="error" data-error-for="confirm_password"></small>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Register</button>
        </form>

        <p class="auth-link">
            Already have an account?
            <a href="login.php">Login</a>
        </p>
    </div>
</div>

<div id="toast" class="toast"></div>
<script src="../js/validation.js"></script>
</body>
</html>
