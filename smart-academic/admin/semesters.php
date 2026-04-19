<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php"); exit;
}
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['semester_name']);
    $num  = (int)$_POST['semester_number'];
    $year = trim($_POST['year']);
    $stmt = $conn->prepare("INSERT INTO semesters (semester_name, semester_number, year) VALUES (?,?,?)");
    $stmt->bind_param("sis", $name, $num, $year);
    $stmt->execute();
    header("Location: semesters.php"); exit;
}
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM semesters WHERE id=" . (int)$_GET['delete']);
    header("Location: semesters.php"); exit;
}

$semesters = $conn->query("SELECT * FROM semesters ORDER BY year DESC, semester_number");
$pageTitle = 'Semesters';
include '../includes/header.php';
?>

<div class="section-grid" style="grid-template-columns:1fr 320px; align-items:start;">
  <div class="table-card">
    <div class="table-header"><div class="table-title">All Semesters</div></div>
    <table>
      <thead><tr><th>Semester</th><th>Number</th><th>Year</th><th>Status</th><th>Action</th></tr></thead>
      <tbody>
        <?php while($s=$semesters->fetch_assoc()): ?>
        <tr>
          <td style="font-size:13px; font-weight:600;"><?=$s['semester_name']?></td>
          <td style="color:var(--muted); font-size:13px;"><?=$s['semester_number']?></td>
          <td style="color:var(--muted); font-size:13px;"><?=$s['year']?></td>
          <td><span class="badge <?=$s['is_active'] ? 'badge-green' : 'badge-yellow'?>"><?=$s['is_active'] ? 'Active' : 'Closed'?></span></td>
          <td><a href="?delete=<?=$s['id']?>" class="btn btn-danger" onclick="return confirm('Delete?')" style="font-size:12px;padding:5px 10px;"><i class="fas fa-trash"></i></a></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div class="form-card">
    <div style="font-weight:700; font-size:16px; color:#fff; margin-bottom:20px;"><i class="fas fa-calendar-days" style="color:var(--accent); margin-right:8px;"></i>Add Semester</div>
    <form method="POST">
      <div class="form-group"><label class="form-label">Semester Name</label>
        <input type="text" name="semester_name" class="form-control" placeholder="e.g. 1st Semester" required>
      </div>
      <div class="form-group"><label class="form-label">Semester Number</label>
        <input type="number" name="semester_number" class="form-control" placeholder="1" required>
      </div>
      <div class="form-group"><label class="form-label">Year</label>
        <input type="text" name="year" class="form-control" placeholder="2025" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
        <i class="fas fa-plus"></i> Add Semester
      </button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
