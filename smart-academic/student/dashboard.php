<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: ../index.php"); exit;
}
require_once '../config/db.php';

$uid = $_SESSION['user_id'];
$student = $conn->query("SELECT s.*, u.name, u.email FROM students s JOIN users u ON s.user_id=u.id WHERE u.id=$uid")->fetch_assoc();
$sid = $student['id'];

$totalCourses = $conn->query("SELECT COUNT(*) as c FROM results WHERE student_id=$sid AND is_published=1")->fetch_assoc()['c'];
$cgpaRow      = $conn->query("SELECT AVG(grade_point) as g FROM results WHERE student_id=$sid AND is_published=1")->fetch_assoc();
$cgpa         = round($cgpaRow['g'] ?? 0, 2);
$bestGrade    = $conn->query("SELECT grade_letter, grade_point FROM results WHERE student_id=$sid AND is_published=1 ORDER BY grade_point DESC LIMIT 1")->fetch_assoc();
$semesters    = $conn->query("SELECT COUNT(DISTINCT semester_id) as c FROM results WHERE student_id=$sid")->fetch_assoc()['c'];

// Recent results
$recentResults = $conn->query("
    SELECT r.*, c.course_name, c.course_code, c.credit_hours, sem.semester_name
    FROM results r
    JOIN courses c ON r.course_id=c.id
    JOIN semesters sem ON r.semester_id=sem.id
    WHERE r.student_id=$sid AND r.is_published=1
    ORDER BY r.created_at DESC LIMIT 5
");

$pageTitle = 'Dashboard';
include '../includes/header.php';
?>

<!-- Welcome Banner -->
<div style="background:linear-gradient(135deg, rgba(0,200,255,0.1), rgba(124,58,237,0.1)); border:1px solid var(--border); border-radius:18px; padding:24px 28px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
  <div>
    <div style="font-size:13px; color:var(--muted); margin-bottom:4px;">Welcome back 👋</div>
    <div style="font-family:'Syne',sans-serif; font-size:24px; font-weight:800; color:#fff;"><?= htmlspecialchars($student['name']) ?></div>
    <div style="font-size:13px; color:var(--muted); margin-top:4px;">
      <span class="badge badge-cyan" style="margin-right:8px;"><?=$student['student_id']?></span>
      <?=$student['department']?> | Batch <?=$student['batch']?>
    </div>
  </div>
  <div style="text-align:right;">
    <div style="font-size:12px; color:var(--muted);">Your CGPA</div>
    <div style="font-family:'Syne',sans-serif; font-size:42px; font-weight:800; color:var(--accent); line-height:1;"><?=$cgpa?></div>
  </div>
</div>

<!-- Stats -->
<div class="stat-grid">
  <div class="stat-card cyan">
    <div class="stat-icon cyan"><i class="fas fa-book-open"></i></div>
    <div class="stat-value"><?=$totalCourses?></div>
    <div class="stat-label">Courses Completed</div>
  </div>
  <div class="stat-card purple">
    <div class="stat-icon purple"><i class="fas fa-star"></i></div>
    <div class="stat-value"><?=$cgpa?></div>
    <div class="stat-label">Current CGPA</div>
  </div>
  <div class="stat-card green">
    <div class="stat-icon green"><i class="fas fa-trophy"></i></div>
    <div class="stat-value"><?=$bestGrade['grade_letter'] ?? 'N/A'?></div>
    <div class="stat-label">Best Grade</div>
  </div>
  <div class="stat-card yellow">
    <div class="stat-icon yellow"><i class="fas fa-calendar-days"></i></div>
    <div class="stat-value"><?=$semesters?></div>
    <div class="stat-label">Semesters</div>
  </div>
</div>

<!-- Quick links -->
<div style="display:flex; gap:10px; margin-bottom:24px; flex-wrap:wrap;">
  <a href="results.php" class="btn btn-primary"><i class="fas fa-award"></i> View All Results</a>
  <a href="transcript.php" class="btn btn-secondary"><i class="fas fa-file-arrow-down"></i> Download Transcript</a>
  <a href="profile.php" class="btn btn-secondary"><i class="fas fa-id-card"></i> My Profile</a>
</div>

<!-- Recent Results -->
<div class="table-card">
  <div class="table-header">
    <div>
      <div class="table-title">Recent Results</div>
      <div class="table-subtitle">Your latest published results</div>
    </div>
    <a href="results.php" class="btn btn-secondary" style="font-size:12px; padding:6px 12px;">View All</a>
  </div>
  <table>
    <thead>
      <tr><th>Course</th><th>Semester</th><th>Attendance</th><th>Midterm</th><th>Final</th><th>Total</th><th>Grade</th></tr>
    </thead>
    <tbody>
      <?php while($r=$recentResults->fetch_assoc()): ?>
      <tr>
        <td>
          <div style="font-weight:600; font-size:13px;"><?=htmlspecialchars($r['course_name'])?></div>
          <div style="font-size:11px; color:var(--muted);"><?=$r['course_code']?> | <?=$r['credit_hours']?> Credits</div>
        </td>
        <td style="font-size:13px; color:var(--muted);"><?=$r['semester_name']?></td>
        <td style="font-size:13px;"><?=$r['attendance_marks']?>/10</td>
        <td style="font-size:13px;"><?=$r['midterm_marks']?>/30</td>
        <td style="font-size:13px;"><?=$r['final_marks']?>/45</td>
        <td style="font-weight:700; font-size:15px;"><?=$r['total_marks']?></td>
        <td>
          <span class="badge <?= in_array($r['grade_letter'],['A+','A']) ? 'badge-cyan' : ($r['grade_letter']==='F' ? 'badge-red' : 'badge-green') ?>">
            <?=$r['grade_letter']?> | <?=$r['grade_point']?>
          </span>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include '../includes/footer.php'; ?>
