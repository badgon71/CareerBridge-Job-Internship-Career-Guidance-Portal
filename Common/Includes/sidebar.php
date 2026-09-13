<?php
$menus = [
  "student" => [
    ["dashboard.php","Dashboard","dashboard"],
    ["jobs.php","Browse Jobs","jobs"],
    ["applications.php","Applications","applications"],
    ["careerTips.php","Career Tips","tips"]
  ],
  "employer" => [
    ["dashboard.php","Dashboard","dashboard"],
    ["jobs.php","Job Posts","jobs"],
    ["jobForm.php","Add Job","jobform"],
    ["applicants.php","Applicants","applicants"]
  ],
  "mentor" => [
    ["dashboard.php","Dashboard","dashboard"],
    ["tips.php","Career Tips","tips"],
    ["tipForm.php","Add Tip","tipform"]
  ],
  "admin" => [
    ["dashboard.php","Dashboard","dashboard"],
    ["categories.php","Categories","categories"],
    ["users.php","Users","users"]
  ]
];
?>
<aside class="sidebar">
  <div class="brand">CareerBridge</div>
  <div class="role-name"><?php echo htmlspecialchars(ucfirst($role)); ?> Panel</div>
  <nav class="nav">
    <?php foreach (($menus[$role] ?? []) as $item): ?>
      <a class="<?php echo $activeMenu === $item[2] ? "active" : ""; ?>" href="<?php echo htmlspecialchars($item[0]); ?>">
        <?php echo htmlspecialchars($item[1]); ?>
      </a>
    <?php endforeach; ?>
    <a href="profile.php">Profile</a>
    <a href="changePassword.php">Change Password</a>
    <a href="../../Common/View/logout.php">Logout</a>
  </nav>
</aside>
<main class="main">
  <div class="topbar">
    <strong><?php echo htmlspecialchars($pageTitle); ?></strong>
    <span><?php echo htmlspecialchars($userName); ?></span>
  </div>
  <div class="content">
