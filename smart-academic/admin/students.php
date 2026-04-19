<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php"); exit;
}
require_once '../config/db.php';

$msg = '';

// Add student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $name       = trim($_POST['name']);
    $email      = trim($_POST['email']);
    $sid        = trim($_POST['student_id']);
    $dept       = trim($_POST['department']);
    $batch      = trim($_POST['batch']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
    $stmt->bind_param("sss", $name, $email, $password);
    if ($stmt->execute()) {
        $uid  = $conn->insert_id;
        $stmt2 = $conn->prepare("INSERT INTO students (user_id, student_id, department, batch) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("isss", $uid, $sid, $dept, $batch);
        $stmt2->execute();
        $msg = "success";
    } else {
        $msg = "error";
    }
}

// Delete student
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $conn->query("DELETE FROM students WHERE id=$did");
    header("Location: students.php"); exit;
}

$students = $conn->query("
    SELECT s.id, s.student_id, u.name, u.email, s.department, s.batch, s.session, u.created_at
    FROM students s JOIN users u ON s.user_id = u.id
    ORDER BY u.created_at DESC
");

$pageTitle = 'Students';
include '../includes/header.php';
?>

<?php if($msg === 'success'): ?>
<div style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); border-radius:12px; padding:14px 18px; margin-bottom:20px; color:#6ee7b7; display:flex; align-items:center; gap:8px;">
  <i class="fas fa-circle-check"></i> Student added successfully!
</div>
<?php endif; ?>

<div class="section-grid" style="grid-template-columns: 1fr 360px; align-items: start;">
  <!-- Students Table -->
  <div class="table-card">
    <div class="table-header">
      <div>
        <div class="table-title">All Students</div>
        <div class="table-subtitle">Manage registered students</div>
      </div>
    </div>
    <table>
      <thead>
        <tr><th>#</th><th>Student</th><th>ID</th><th>Department</th><th>Batch</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php $i=1; while($s = $students->fetch_assoc()): ?>
        <tr>
          <td style="color:var(--muted); font-size:13px;"><?= $i++ ?></td>
          <td>
            <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($s['name']) ?></div>
            <div style="font-size:11px; color:var(--muted);"><?= htmlspecialchars($s['email']) ?></div>
          </td>
          <td><span class="badge badge-cyan"><?= $s['student_id'] ?></span></td>
          <td style="font-size:13px; color:var(--muted);"><?= $s['department'] ?></td>
          <td style="font-size:13px; color:var(--muted);"><?= $s['batch'] ?></td>
          <td>
            <a href="?delete=<?= $s['id'] ?>" class="btn btn-danger"
               onclick="return confirm('Delete this student?')"
               style="font-size:12px; padding:5px 10px;">
              <i class="fas fa-trash"></i>
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Add Student Form -->
  <div class="form-card">
    <div style="font-weight:700; font-size:16px; color:#fff; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
      <i class="fas fa-user-plus" style="color:var(--accent);"></i> Add New Student
    </div>
    <form method="POST">
      <input type="hidden" name="add_student" value="1">
      <div class="form-group">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" placeholder="e.g. Uttam Halder" required>
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
      </div>
      <div class="form-group">
        <label class="form-label">Student ID</label>
        <input type="text" name="student_id" class="form-control" placeholder="e.g. 42250102317" required>
      </div>
      <div class="form-group">
        <label class="form-label">Department</label>
        <select name="department" class="form-control">
          <option>CSE</option><option>EEE</option><option>BBA</option><option>LAW</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Batch</label>
        <input type="text" name="batch" class="form-control" placeholder="e.g. 2025">
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Set password" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
        <i class="fas fa-plus"></i> Add Student
      </button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
