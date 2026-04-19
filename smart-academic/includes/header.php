<?php
// includes/header.php
// Pass $pageTitle and $role ('admin' or 'student') before including
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?? 'Bit Brains' ?> | Smart Academic System</title>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root {
    --bg: #050b18;
    --surface: #0d1b2e;
    --card: #0f2240;
    --card2: #0a1a35;
    --border: rgba(0,200,255,0.12);
    --accent: #00c8ff;
    --accent2: #7c3aed;
    --green: #10b981;
    --yellow: #f59e0b;
    --red: #ef4444;
    --text: #e2eaf8;
    --muted: #6b8aad;
    --sidebar-w: 260px;
  }
  * { margin:0; padding:0; box-sizing:border-box; }
  body { background: var(--bg); font-family: 'Space Grotesk', sans-serif; color: var(--text); display: flex; min-height: 100vh; }

  /* Sidebar */
  .sidebar {
    width: var(--sidebar-w);
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex; flex-direction: column;
    position: fixed; top: 0; left: 0; height: 100vh;
    z-index: 100; overflow-y: auto;
    transition: transform 0.3s;
  }
  .sidebar-brand {
    padding: 24px 20px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 12px;
  }
  .brand-logo {
    width: 40px; height: 40px;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: #fff; flex-shrink: 0;
    box-shadow: 0 0 20px rgba(0,200,255,0.25);
  }
  .brand-text .name { font-family: 'Syne', sans-serif; font-size: 18px; font-weight: 800; color: #fff; line-height: 1; }
  .brand-text .name span { color: var(--accent); }
  .brand-text .sub { font-size: 11px; color: var(--muted); }

  .sidebar-user {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 10px;
  }
  .user-avatar {
    width: 38px; height: 38px;
    background: linear-gradient(135deg, rgba(0,200,255,0.3), rgba(124,58,237,0.3));
    border: 1px solid var(--border);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; color: var(--accent); flex-shrink: 0;
  }
  .user-info .u-name { font-size: 13px; font-weight: 600; color: var(--text); }
  .user-info .u-role {
    font-size: 11px; color: var(--muted);
    text-transform: uppercase; letter-spacing: 0.5px;
  }

  .sidebar-nav { flex: 1; padding: 16px 12px; }
  .nav-section-label {
    font-size: 10px; color: var(--muted);
    text-transform: uppercase; letter-spacing: 1.5px;
    padding: 8px 8px 6px;
    margin-top: 8px;
  }
  .nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border-radius: 10px;
    color: var(--muted); text-decoration: none;
    font-size: 14px; font-weight: 500;
    transition: all 0.2s; margin-bottom: 2px;
  }
  .nav-item:hover { background: rgba(0,200,255,0.07); color: var(--text); }
  .nav-item.active { background: rgba(0,200,255,0.12); color: var(--accent); }
  .nav-item i { width: 18px; text-align: center; font-size: 14px; }
  .nav-badge {
    margin-left: auto; background: var(--accent); color: #050b18;
    font-size: 10px; font-weight: 700;
    padding: 2px 7px; border-radius: 20px;
  }

  .sidebar-footer {
    padding: 16px 12px;
    border-top: 1px solid var(--border);
  }
  .btn-logout {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border-radius: 10px;
    color: var(--red); text-decoration: none;
    font-size: 14px; font-weight: 500;
    transition: all 0.2s; width: 100%;
    background: none; border: none; cursor: pointer;
    font-family: 'Space Grotesk', sans-serif;
  }
  .btn-logout:hover { background: rgba(239,68,68,0.1); }

  /* Main content */
  .main-content {
    margin-left: var(--sidebar-w);
    flex: 1; display: flex; flex-direction: column; min-height: 100vh;
  }
  .topbar {
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    padding: 16px 28px;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; z-index: 50;
  }
  .page-title { font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 800; color: #fff; }
  .page-breadcrumb { font-size: 12px; color: var(--muted); margin-top: 2px; }

  .topbar-right { display: flex; align-items: center; gap: 12px; }
  .notif-btn {
    width: 38px; height: 38px;
    background: var(--card); border: 1px solid var(--border);
    border-radius: 10px; display: flex; align-items: center;
    justify-content: center; color: var(--muted); cursor: pointer;
    transition: all 0.2s; text-decoration: none;
  }
  .notif-btn:hover { color: var(--accent); border-color: var(--accent); }

  .page-content { padding: 28px; flex: 1; }

  /* Cards */
  .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 24px; }
  .stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px; padding: 20px;
    position: relative; overflow: hidden;
    transition: transform 0.2s;
  }
  .stat-card:hover { transform: translateY(-2px); }
  .stat-card::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 2px;
  }
  .stat-card.cyan::before { background: linear-gradient(90deg, var(--accent), transparent); }
  .stat-card.purple::before { background: linear-gradient(90deg, var(--accent2), transparent); }
  .stat-card.green::before { background: linear-gradient(90deg, var(--green), transparent); }
  .stat-card.yellow::before { background: linear-gradient(90deg, var(--yellow), transparent); }

  .stat-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; margin-bottom: 14px;
  }
  .stat-icon.cyan { background: rgba(0,200,255,0.12); color: var(--accent); }
  .stat-icon.purple { background: rgba(124,58,237,0.12); color: var(--accent2); }
  .stat-icon.green { background: rgba(16,185,129,0.12); color: var(--green); }
  .stat-icon.yellow { background: rgba(245,158,11,0.12); color: var(--yellow); }

  .stat-value { font-family: 'Syne', sans-serif; font-size: 28px; font-weight: 800; color: #fff; }
  .stat-label { font-size: 13px; color: var(--muted); margin-top: 4px; }

  /* Table */
  .table-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 16px; overflow: hidden;
  }
  .table-header {
    padding: 18px 22px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
  }
  .table-title { font-weight: 600; font-size: 15px; color: #fff; }
  .table-subtitle { font-size: 12px; color: var(--muted); margin-top: 2px; }

  table { width: 100%; border-collapse: collapse; }
  th { padding: 12px 20px; text-align: left; font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; background: rgba(0,200,255,0.04); border-bottom: 1px solid var(--border); }
  td { padding: 14px 20px; font-size: 14px; border-bottom: 1px solid rgba(0,200,255,0.05); }
  tr:last-child td { border-bottom: none; }
  tr:hover td { background: rgba(0,200,255,0.03); }

  .badge {
    padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block;
  }
  .badge-green { background: rgba(16,185,129,0.15); color: var(--green); }
  .badge-yellow { background: rgba(245,158,11,0.15); color: var(--yellow); }
  .badge-red { background: rgba(239,68,68,0.15); color: var(--red); }
  .badge-cyan { background: rgba(0,200,255,0.15); color: var(--accent); }

  .btn {
    padding: 8px 16px; border-radius: 9px; font-size: 13px; font-weight: 600;
    cursor: pointer; border: none; font-family: 'Space Grotesk', sans-serif;
    display: inline-flex; align-items: center; gap: 6px; text-decoration: none;
    transition: all 0.2s;
  }
  .btn-primary { background: linear-gradient(135deg, var(--accent), #0099cc); color: #050b18; box-shadow: 0 2px 12px rgba(0,200,255,0.25); }
  .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 20px rgba(0,200,255,0.35); }
  .btn-secondary { background: var(--card); border: 1px solid var(--border); color: var(--text); }
  .btn-secondary:hover { border-color: var(--accent); color: var(--accent); }
  .btn-danger { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: var(--red); }
  .btn-danger:hover { background: rgba(239,68,68,0.25); }

  .section-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
  .grid-3 { grid-template-columns: 1fr 1fr 1fr; }

  /* Form styles */
  .form-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 16px; padding: 24px;
  }
  .form-group { margin-bottom: 18px; }
  .form-label { font-size: 13px; color: var(--muted); margin-bottom: 8px; display: block; font-weight: 500; }
  .form-control {
    width: 100%; padding: 10px 14px;
    background: var(--card); border: 1px solid var(--border);
    border-radius: 10px; color: var(--text); font-size: 14px;
    font-family: 'Space Grotesk', sans-serif; outline: none; transition: all 0.2s;
  }
  .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(0,200,255,0.1); }
  .form-control option { background: var(--card); }

  .gpa-display {
    background: linear-gradient(135deg, rgba(0,200,255,0.1), rgba(124,58,237,0.1));
    border: 1px solid var(--border); border-radius: 14px; padding: 20px; text-align: center;
  }
  .gpa-value { font-family: 'Syne', sans-serif; font-size: 48px; font-weight: 800; color: var(--accent); }
  .gpa-label { font-size: 13px; color: var(--muted); }

  @media (max-width: 1024px) {
    .stat-grid { grid-template-columns: repeat(2, 1fr); }
    .section-grid { grid-template-columns: 1fr; }
  }
  @media (max-width: 768px) {
    .sidebar { transform: translateX(-100%); }
    .main-content { margin-left: 0; }
  }
</style>
</head>
<body>

<?php
// Determine nav items
$isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';
$basePath = $isAdmin ? '../' : '../';
?>

<!-- Sidebar -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo"><i class="fas fa-brain"></i></div>
    <div class="brand-text">
      <div class="name">Bit<span>Brains</span></div>
      <div class="sub">Academic System</div>
    </div>
  </div>

  <div class="sidebar-user">
    <div class="user-avatar"><i class="fas fa-user"></i></div>
    <div class="user-info">
      <div class="u-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></div>
      <div class="u-role"><?= ucfirst($_SESSION['user_role'] ?? 'user') ?></div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <?php if ($isAdmin): ?>
    <div class="nav-section-label">Main</div>
    <a href="<?= $basePath ?>admin/dashboard.php" class="nav-item <?= ($pageTitle === 'Dashboard') ? 'active' : '' ?>">
      <i class="fas fa-gauge-high"></i> Dashboard
    </a>
    <div class="nav-section-label">Management</div>
    <a href="<?= $basePath ?>admin/students.php" class="nav-item <?= ($pageTitle === 'Students') ? 'active' : '' ?>">
      <i class="fas fa-users"></i> Students
    </a>
    <a href="<?= $basePath ?>admin/courses.php" class="nav-item <?= ($pageTitle === 'Courses') ? 'active' : '' ?>">
      <i class="fas fa-book-open"></i> Courses
    </a>
    <a href="<?= $basePath ?>admin/semesters.php" class="nav-item <?= ($pageTitle === 'Semesters') ? 'active' : '' ?>">
      <i class="fas fa-calendar-days"></i> Semesters
    </a>
    <a href="<?= $basePath ?>admin/marks.php" class="nav-item <?= ($pageTitle === 'Marks Entry') ? 'active' : '' ?>">
      <i class="fas fa-pen-to-square"></i> Enter Marks
    </a>
    <div class="nav-section-label">Reports</div>
    <a href="<?= $basePath ?>admin/reports.php" class="nav-item <?= ($pageTitle === 'Reports') ? 'active' : '' ?>">
      <i class="fas fa-chart-bar"></i> Reports
    </a>
    <?php else: ?>
    <div class="nav-section-label">Main</div>
    <a href="<?= $basePath ?>student/dashboard.php" class="nav-item <?= ($pageTitle === 'Dashboard') ? 'active' : '' ?>">
      <i class="fas fa-gauge-high"></i> Dashboard
    </a>
    <div class="nav-section-label">Academics</div>
    <a href="<?= $basePath ?>student/results.php" class="nav-item <?= ($pageTitle === 'My Results') ? 'active' : '' ?>">
      <i class="fas fa-award"></i> My Results
    </a>
    <a href="<?= $basePath ?>student/transcript.php" class="nav-item <?= ($pageTitle === 'Transcript') ? 'active' : '' ?>">
      <i class="fas fa-file-arrow-down"></i> Transcript
    </a>
    <a href="<?= $basePath ?>student/profile.php" class="nav-item <?= ($pageTitle === 'Profile') ? 'active' : '' ?>">
      <i class="fas fa-id-card"></i> Profile
    </a>
    <?php endif; ?>
  </nav>

  <div class="sidebar-footer">
    <a href="<?= $basePath ?>auth/logout.php" class="btn-logout">
      <i class="fas fa-right-from-bracket"></i> Sign Out
    </a>
  </div>
</aside>

<!-- Main -->
<div class="main-content">
  <div class="topbar">
    <div>
      <div class="page-title"><?= $pageTitle ?? 'Dashboard' ?></div>
      <div class="page-breadcrumb">BitBrains / <?= $pageTitle ?? 'Dashboard' ?></div>
    </div>
    <div class="topbar-right">
      <span style="font-size:13px; color:var(--muted);"><?= date('D, d M Y') ?></span>
    </div>
  </div>
  <div class="page-content">
