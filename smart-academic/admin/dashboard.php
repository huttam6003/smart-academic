<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php"); exit;
}
require_once '../config/db.php';

// Stats
$totalStudents = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'];
$totalCourses  = $conn->query("SELECT COUNT(*) as c FROM courses")->fetch_assoc()['c'];
$totalResults  = $conn->query("SELECT COUNT(*) as c FROM results WHERE is_published=1")->fetch_assoc()['c'];
$avgGpa        = $conn->query("SELECT AVG(grade_point) as g FROM results WHERE is_published=1")->fetch_assoc()['g'];

// Recent students
$recentStudents = $conn->query("
    SELECT s.student_id, u.name, u.email, s.department, s.batch, u.created_at
    FROM students s JOIN users u ON s.user_id = u.id
    ORDER BY u.created_at DESC LIMIT 6
");

// Recent results
$recentResults = $conn->query("
    SELECT u.name, r.grade_letter, r.grade_point, r.total_marks, c.course_name, r.is_published
    FROM results r
    JOIN students s ON r.student_id = s.id
    JOIN users u ON s.user_id = u.id
    JOIN courses c ON r.course_id = c.id
    ORDER BY r.created_at DESC LIMIT 5
");

$pageTitle = 'Dashboard';
include '../includes/header.php';
?>

<!-- Stats -->
<div class="stat-grid">
  <div class="stat-card cyan">
    <div class="stat-icon cyan"><i class="fas fa-users"></i></div>
    <div class="stat-value"><?= $totalStudents ?></div>
    <div class="stat-label">Total Students</div>
  </div>
  <div class="stat-card purple">
    <div class="stat-icon purple"><i class="fas fa-book-open"></i></div>
    <div class="stat-value"><?= $totalCourses ?></div>
    <div class="stat-label">Total Courses</div>
  </div>
  <div class="stat-card green">
    <div class="stat-icon green"><i class="fas fa-circle-check"></i></div>
    <div class="stat-value"><?= $totalResults ?></div>
    <div class="stat-label">Published Results</div>
  </div>
  <div class="stat-card yellow">
    <div class="stat-icon yellow"><i class="fas fa-star"></i></div>
    <div class="stat-value"><?= number_format($avgGpa ?? 0, 2) ?></div>
    <div class="stat-label">Average GPA</div>
  </div>
</div>

<!-- Quick Actions -->
<div style="display:flex; gap:10px; margin-bottom:24px; flex-wrap:wrap;">
  <a href="students.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add Student</a>
  <a href="marks.php" class="btn btn-secondary"><i class="fas fa-pen-to-square"></i> Enter Marks</a>
  <a href="courses.php" class="btn btn-secondary"><i class="fas fa-book-open"></i> Manage Courses</a>
  <a href="reports.php" class="btn btn-secondary"><i class="fas fa-chart-bar"></i> View Reports</a>
</div>

<div class="section-grid">
  <!-- Recent Students -->
  <div class="table-card">
    <div class="table-header">
      <div>
        <div class="table-title">Recent Students</div>
        <div class="table-subtitle">Latest registered students</div>
      </div>
      <a href="students.php" class="btn btn-secondary" style="font-size:12px; padding:6px 12px;">View All</a>
    </div>
    <table>
      <thead>
        <tr><th>Student</th><th>ID</th><th>Dept</th></tr>
      </thead>
      <tbody>
        <?php while($s = $recentStudents->fetch_assoc()): ?>
        <tr>
          <td>
            <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($s['name']) ?></div>
            <div style="font-size:11px; color:var(--muted);"><?= htmlspecialchars($s['email']) ?></div>
          </td>
          <td><span class="badge badge-cyan"><?= $s['student_id'] ?></span></td>
          <td style="color:var(--muted); font-size:13px;"><?= $s['department'] ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Recent Results -->
  <div class="table-card">
    <div class="table-header">
      <div>
        <div class="table-title">Recent Results</div>
        <div class="table-subtitle">Latest result entries</div>
      </div>
      <a href="marks.php" class="btn btn-secondary" style="font-size:12px; padding:6px 12px;">Manage</a>
    </div>
    <table>
      <thead>
        <tr><th>Student</th><th>Course</th><th>Grade</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php while($r = $recentResults->fetch_assoc()): ?>
        <tr>
          <td style="font-size:13px; font-weight:600;"><?= htmlspecialchars($r['name']) ?></td>
          <td style="font-size:12px; color:var(--muted);"><?= substr($r['course_name'], 0, 18) ?>...</td>
          <td>
            <span class="badge <?= $r['grade_letter'] === 'A+' ? 'badge-cyan' : ($r['grade_letter'] === 'F' ? 'badge-red' : 'badge-green') ?>">
              <?= $r['grade_letter'] ?> (<?= $r['grade_point'] ?>)
            </span>
          </td>
          <td>
            <span class="badge <?= $r['is_published'] ? 'badge-green' : 'badge-yellow' ?>">
              <?= $r['is_published'] ? 'Published' : 'Pending' ?>
            </span>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
