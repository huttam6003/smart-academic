<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php"); exit;
}
require_once '../config/db.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code   = trim($_POST['course_code']);
    $name   = trim($_POST['course_name']);
    $credit = (float)$_POST['credit_hours'];
    $dept   = trim($_POST['department']);
    $stmt = $conn->prepare("INSERT INTO courses (course_code, course_name, credit_hours, department) VALUES (?,?,?,?)");
    $stmt->bind_param("ssds", $code, $name, $credit, $dept);
    $stmt->execute() ? $msg = 'success' : $msg = 'error';
}
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM courses WHERE id=" . (int)$_GET['delete']);
    header("Location: courses.php"); exit;
}

$courses = $conn->query("SELECT * FROM courses ORDER BY course_name");
$pageTitle = 'Courses';
include '../includes/header.php';
?>

<?php if($msg==='success'): ?>
<div style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); border-radius:12px; padding:14px 18px; margin-bottom:20px; color:#6ee7b7; display:flex; align-items:center; gap:8px;">
  <i class="fas fa-circle-check"></i> Course added!
</div>
<?php endif; ?>

<div class="section-grid" style="grid-template-columns:1fr 340px; align-items:start;">
  <div class="table-card">
    <div class="table-header">
      <div class="table-title">All Courses</div>
    </div>
    <table>
      <thead><tr><th>Code</th><th>Course Name</th><th>Credits</th><th>Dept</th><th>Action</th></tr></thead>
      <tbody>
        <?php while($c=$courses->fetch_assoc()): ?>
        <tr>
          <td><span class="badge badge-cyan"><?=$c['course_code']?></span></td>
          <td style="font-size:13px;"><?=htmlspecialchars($c['course_name'])?></td>
          <td style="font-size:13px; color:var(--muted);"><?=$c['credit_hours']?></td>
          <td style="font-size:13px; color:var(--muted);"><?=$c['department']?></td>
          <td><a href="?delete=<?=$c['id']?>" class="btn btn-danger" onclick="return confirm('Delete?')" style="font-size:12px;padding:5px 10px;"><i class="fas fa-trash"></i></a></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div class="form-card">
    <div style="font-weight:700; font-size:16px; color:#fff; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
      <i class="fas fa-book-open" style="color:var(--accent);"></i> Add Course
    </div>
    <form method="POST">
      <div class="form-group"><label class="form-label">Course Code</label>
        <input type="text" name="course_code" class="form-control" placeholder="e.g. CSE2291" required>
      </div>
      <div class="form-group"><label class="form-label">Course Name</label>
        <input type="text" name="course_name" class="form-control" placeholder="e.g. Software Development II" required>
      </div>
      <div class="form-group"><label class="form-label">Credit Hours</label>
        <input type="number" name="credit_hours" class="form-control" step="0.5" placeholder="3.0" required>
      </div>
      <div class="form-group"><label class="form-label">Department</label>
        <select name="department" class="form-control">
          <option>CSE</option><option>EEE</option><option>BBA</option><option>LAW</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
        <i class="fas fa-plus"></i> Add Course
      </button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
