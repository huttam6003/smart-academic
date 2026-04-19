<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bit Brains | Smart Academic System</title>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root {
    --bg: #050b18;
    --surface: #0d1b2e;
    --card: #0f2240;
    --border: rgba(0,200,255,0.15);
    --accent: #00c8ff;
    --accent2: #7c3aed;
    --text: #e2eaf8;
    --muted: #6b8aad;
    --success: #10b981;
    --danger: #ef4444;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    background: var(--bg);
    font-family: 'Space Grotesk', sans-serif;
    color: var(--text);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
  }
  /* Animated background */
  body::before {
    content: '';
    position: fixed;
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(0,200,255,0.08) 0%, transparent 70%);
    top: -100px; left: -100px;
    animation: drift 8s ease-in-out infinite alternate;
    pointer-events: none;
  }
  body::after {
    content: '';
    position: fixed;
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(124,58,237,0.08) 0%, transparent 70%);
    bottom: -100px; right: -100px;
    animation: drift 10s ease-in-out infinite alternate-reverse;
    pointer-events: none;
  }
  @keyframes drift { from { transform: translate(0,0); } to { transform: translate(40px, 30px); } }

  /* Grid overlay */
  .grid-bg {
    position: fixed; inset: 0; pointer-events: none;
    background-image:
      linear-gradient(rgba(0,200,255,0.03) 1px, transparent 1px),
      linear-gradient(90deg, rgba(0,200,255,0.03) 1px, transparent 1px);
    background-size: 50px 50px;
    z-index: 0;
  }

  .login-wrapper {
    position: relative; z-index: 10;
    display: flex; gap: 0;
    width: 900px; max-width: 95vw;
    min-height: 560px;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 0 80px rgba(0,200,255,0.1), 0 0 0 1px var(--border);
    animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
  }
  @keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* Left panel */
  .left-panel {
    flex: 1;
    background: linear-gradient(135deg, #0a1628 0%, #0d2240 50%, #0a1628 100%);
    padding: 50px 40px;
    display: flex; flex-direction: column;
    justify-content: space-between;
    position: relative; overflow: hidden;
    border-right: 1px solid var(--border);
  }
  .left-panel::before {
    content: '';
    position: absolute;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(0,200,255,0.12) 0%, transparent 70%);
    top: -50px; right: -50px;
    pointer-events: none;
  }
  .brand { position: relative; z-index: 1; }
  .brand-logo {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; margin-bottom: 20px;
    box-shadow: 0 0 30px rgba(0,200,255,0.3);
  }
  .brand-name {
    font-family: 'Syne', sans-serif;
    font-size: 28px; font-weight: 800;
    color: #fff; line-height: 1;
    margin-bottom: 6px;
  }
  .brand-name span { color: var(--accent); }
  .brand-subtitle { color: var(--muted); font-size: 13px; }

  .features { position: relative; z-index: 1; }
  .feature-item {
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 20px; color: var(--muted); font-size: 14px;
    animation: fadeIn 0.5s ease both;
  }
  .feature-item:nth-child(1) { animation-delay: 0.2s; }
  .feature-item:nth-child(2) { animation-delay: 0.3s; }
  .feature-item:nth-child(3) { animation-delay: 0.4s; }
  .feature-item:nth-child(4) { animation-delay: 0.5s; }
  @keyframes fadeIn { from { opacity:0; transform: translateX(-10px); } to { opacity:1; transform: translateX(0); } }

  .feature-icon {
    width: 36px; height: 36px;
    background: rgba(0,200,255,0.1);
    border: 1px solid rgba(0,200,255,0.2);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: var(--accent); font-size: 14px; flex-shrink: 0;
  }

  .cse-badge {
    position: relative; z-index: 1;
    font-size: 12px; color: var(--muted);
    border-top: 1px solid var(--border);
    padding-top: 20px;
  }
  .cse-badge strong { color: var(--accent); }

  /* Right panel - Login form */
  .right-panel {
    width: 380px;
    background: var(--surface);
    padding: 50px 40px;
    display: flex; flex-direction: column; justify-content: center;
  }
  .form-title {
    font-family: 'Syne', sans-serif;
    font-size: 26px; font-weight: 800;
    color: #fff; margin-bottom: 6px;
  }
  .form-subtitle { color: var(--muted); font-size: 14px; margin-bottom: 36px; }

  .demo-badge {
    background: rgba(0,200,255,0.08);
    border: 1px solid rgba(0,200,255,0.2);
    border-radius: 10px; padding: 12px 16px;
    margin-bottom: 28px; font-size: 12px; color: var(--muted);
    line-height: 1.8;
  }
  .demo-badge strong { color: var(--accent); }

  .alert-error {
    background: rgba(239,68,68,0.1);
    border: 1px solid rgba(239,68,68,0.3);
    border-radius: 10px; padding: 12px 16px;
    margin-bottom: 20px; font-size: 13px;
    color: #fca5a5;
    display: flex; align-items: center; gap: 8px;
  }

  .form-group { margin-bottom: 20px; }
  .form-label { font-size: 13px; color: var(--muted); margin-bottom: 8px; display: block; font-weight: 500; }
  .input-wrapper { position: relative; }
  .input-icon {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
    color: var(--muted); font-size: 14px; pointer-events: none;
  }
  .form-input {
    width: 100%; padding: 12px 14px 12px 40px;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    color: var(--text); font-size: 14px;
    font-family: 'Space Grotesk', sans-serif;
    transition: all 0.2s;
    outline: none;
  }
  .form-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(0,200,255,0.1);
  }
  .form-input::placeholder { color: #3a4f6a; }

  .role-tabs {
    display: flex; gap: 8px; margin-bottom: 24px;
  }
  .role-tab {
    flex: 1; padding: 10px;
    border: 1px solid var(--border);
    border-radius: 10px; background: var(--card);
    color: var(--muted); font-size: 13px; font-weight: 500;
    cursor: pointer; text-align: center;
    transition: all 0.2s; font-family: 'Space Grotesk', sans-serif;
  }
  .role-tab.active {
    background: rgba(0,200,255,0.1);
    border-color: var(--accent); color: var(--accent);
  }

  .btn-login {
    width: 100%; padding: 14px;
    background: linear-gradient(135deg, var(--accent), #0099cc);
    border: none; border-radius: 12px;
    color: #050b18; font-size: 15px; font-weight: 700;
    font-family: 'Space Grotesk', sans-serif;
    cursor: pointer; transition: all 0.2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    box-shadow: 0 4px 20px rgba(0,200,255,0.3);
  }
  .btn-login:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 30px rgba(0,200,255,0.4);
  }
  .btn-login:active { transform: translateY(0); }

  @media (max-width: 768px) {
    .login-wrapper { flex-direction: column; width: 95vw; }
    .left-panel { padding: 30px 24px; }
    .right-panel { width: 100%; padding: 30px 24px; }
    .features { display: none; }
  }
</style>
</head>
<body>
<div class="grid-bg"></div>

<?php
session_start();
$error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
unset($_SESSION['login_error']);
?>

<div class="login-wrapper">
  <!-- Left Panel -->
  <div class="left-panel">
    <div class="brand">
      <div class="brand-logo"><i class="fas fa-brain"></i></div>
      <div class="brand-name">Bit<span>Brains</span></div>
      <div class="brand-subtitle">Smart Academic Management System</div>
    </div>

    <div class="features">
      <div class="feature-item">
        <div class="feature-icon"><i class="fas fa-shield-halved"></i></div>
        <div>Role-based secure authentication</div>
      </div>
      <div class="feature-item">
        <div class="feature-icon"><i class="fas fa-calculator"></i></div>
        <div>Automated GPA & CGPA calculation</div>
      </div>
      <div class="feature-item">
        <div class="feature-icon"><i class="fas fa-file-arrow-down"></i></div>
        <div>Downloadable academic transcripts</div>
      </div>
      <div class="feature-item">
        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
        <div>Real-time performance analytics</div>
      </div>
    </div>

    <div class="cse-badge">
      <strong>CSE 2291</strong> — Software Development II<br>
      Department of Computer Science & Engineering
    </div>
  </div>

  <!-- Right Panel -->
  <div class="right-panel">
    <div class="form-title">Welcome Back</div>
    <div class="form-subtitle">Sign in to your account to continue</div>

    <div class="demo-badge">
      <strong>Admin:</strong> admin@bitbrains.edu<br>
      <strong>Student:</strong> uttam@bitbrains.edu<br>
      <strong>Password:</strong> password
    </div>

    <?php if($error): ?>
    <div class="alert-error">
      <i class="fas fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="auth/login.php">
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <div class="input-wrapper">
          <i class="fas fa-envelope input-icon"></i>
          <input type="email" name="email" class="form-input" placeholder="Enter your email" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-wrapper">
          <i class="fas fa-lock input-icon"></i>
          <input type="password" name="password" class="form-input" placeholder="Enter your password" required>
        </div>
      </div>

      <button type="submit" class="btn-login">
        <i class="fas fa-right-to-bracket"></i> Sign In
      </button>
    </form>
  </div>
</div>
</body>
</html>
