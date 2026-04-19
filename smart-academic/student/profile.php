<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: ../index.php"); exit;
}
require_once '../config/db.php';

$uid     = $_SESSION['user_id'];
$student = $conn->query("SELECT s.*, u.name, u.email FROM students s JOIN users u ON s.user_id=u.id WHERE u.id=$uid")->fetch_assoc();
$sid     = $student['id'];

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone   = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $conn->prepare("UPDATE students SET phone=?, address=? WHERE id=?")->execute() ||
    $stmt = $conn->prepare("UPDATE students SET phone=?, address=? WHERE id=?");
    $stmt->bind_param("ssi", $phone, $address, $sid);
    $stmt->execute();

    if (!empty($_POST['new_password'])) {
        $newpw = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $conn->prepare("UPDATE users SET password=? WHERE id=?")->execute();
        $stmt2 = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt2->bind_param("si", $newpw, $uid);
        $stmt2->execute();
    }
    $msg = 'saved';
    $student = $conn->query("SELECT s.*, u.name, u.email FROM students s JOIN users u ON s.user_id=u.id WHERE u.id=$uid")->fetch_assoc();
}

$pageTitle = 'Profile';
include '../includes/header.php';
?>

<?php if($msg==='saved'): ?>
<div style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); border-radius:12px; padding:14px 18px; margin-bottom:20px; color:#6ee7b7; display:flex; align-items:center; gap:8px;">
  <i class="fas fa-circle-check"></i> Profile updated!
</div>
<?php endif; ?>

<div class="section-grid" style="grid-template-columns:300px 1fr; align-items:start;">
  <!-- Profile Card -->
  <div class="form-card" style="text-align:center;">
    <div style="width:80px; height:80px; background:linear-gradient(135deg, rgba(0,200,255,0.3), rgba(124,58,237,0.3)); border:2px solid var(--border); border-radius:20px; display:flex; align-items:center; justify-content:center; font-size:32px; color:var(--accent); margin:0 auto 16px;">
      <i class="fas fa-user-graduate"></i>
    </div>
    <div style="font-family:'Syne',sans-serif; font-size:20px; font-weight:800; color:#fff;"><?=htmlspecialchars($student['name'])?></div>
    <div style="color:var(--muted); font-size:13px; margin-top:4px;"><?=htmlspecialchars($student['email'])?></div>
    <div style="margin-top:16px;">
      <span class="badge badge-cyan"><?=$student['student_id']?></span>
    </div>
    <div style="margin-top:20px; border-top:1px solid var(--border); padding-top:16px;">
      <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:13px;">
        <span style="color:var(--muted);">Department</span>
        <span><?=$student['department']?></span>
      </div>
      <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:13px;">
        <span style="color:var(--muted);">Batch</span>
        <span><?=$student['batch']?></span>
      </div>
      <div style="display:flex; justify-content:space-between; font-size:13px;">
        <span style="color:var(--muted);">Phone</span>
        <span><?=$student['phone'] ?: 'Not set'?></span>
      </div>
    </div>
  </div>

  <!-- Edit Form -->
  <div class="form-card">
    <div style="font-weight:700; font-size:16px; color:#fff; margin-bottom:20px;"><i class="fas fa-pen-to-square" style="color:var(--accent); margin-right:8px;"></i>Edit Profile</div>
    <form method="POST">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group">
          <label class="form-label">Full Name</label>
          <input type="text" class="form-control" value="<?=htmlspecialchars($student['name'])?>" disabled style="opacity:0.6;">
        </div>
        <div class="form-group">
          <label class="form-label">Student ID</label>
          <input type="text" class="form-control" value="<?=$student['student_id']?>" disabled style="opacity:0.6;">
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" value="<?=htmlspecialchars($student['email'])?>" disabled style="opacity:0.6;">
        </div>
        <div class="form-group">
          <label class="form-label">Phone</label>
          <input type="text" name="phone" class="form-control" value="<?=htmlspecialchars($student['phone']??'')?>" placeholder="+880...">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" rows="3" placeholder="Your address..."><?=htmlspecialchars($student['address']??'')?></textarea>
      </div>
      <div style="border-top:1px solid var(--border); padding-top:20px; margin-top:8px;">
        <div style="font-size:14px; font-weight:600; color:#fff; margin-bottom:14px;">Change Password</div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
          <div class="form-group">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current">
          </div>
          <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" class="form-control" placeholder="Confirm new password">
          </div>
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="margin-top:6px;">
        <i class="fas fa-floppy-disk"></i> Save Changes
      </button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
