<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: ../index.php"); exit;
}
require_once '../config/db.php';

$uid     = $_SESSION['user_id'];
$student = $conn->query("SELECT s.*, u.name, u.email FROM students s JOIN users u ON s.user_id=u.id WHERE u.id=$uid")->fetch_assoc();
$sid     = $student['id'];

// Filter by semester
$semFilter = isset($_GET['semester']) ? (int)$_GET['semester'] : 0;
$where = "r.student_id=$sid AND r.is_published=1" . ($semFilter ? " AND r.semester_id=$semFilter" : "");

$results = $conn->query("
    SELECT r.*, c.course_name, c.course_code, c.credit_hours, sem.semester_name, sem.year
    FROM results r
    JOIN courses c ON r.course_id=c.id
    JOIN semesters sem ON r.semester_id=sem.id
    WHERE $where
    ORDER BY sem.semester_number, c.course_name
");

$semesters = $conn->query("
    SELECT DISTINCT sem.id, sem.semester_name, sem.year
    FROM results r JOIN semesters sem ON r.semester_id=sem.id
    WHERE r.student_id=$sid AND r.is_published=1
");

// Compute CGPA
$cgpaQ = $conn->query("SELECT SUM(grade_point*credit_hours) as gp_sum, SUM(credit_hours) as cr_sum FROM results r JOIN courses c ON r.course_id=c.id WHERE r.student_id=$sid AND r.is_published=1");
$cgpaR = $cgpaQ->fetch_assoc();
$cgpa  = $cgpaR['cr_sum'] > 0 ? round($cgpaR['gp_sum']/$cgpaR['cr_sum'], 2) : 0;

$pageTitle = 'My Results';
include '../includes/header.php';
?>

<!-- Semester Filter -->
<div style="display:flex; align-items:center; gap:10px; margin-bottom:20px; flex-wrap:wrap;">
  <a href="results.php" class="btn <?= !$semFilter ? 'btn-primary' : 'btn-secondary' ?>">All Semesters</a>
  <?php while($sem=$semesters->fetch_assoc()): ?>
  <a href="?semester=<?=$sem['id']?>" class="btn <?= $semFilter==$sem['id'] ? 'btn-primary' : 'btn-secondary' ?>">
    <?=$sem['semester_name']?> (<?=$sem['year']?>)
  </a>
  <?php endwhile; ?>
</div>

<!-- CGPA card -->
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">
  <div class="gpa-display">
    <div class="gpa-value"><?=$cgpa?></div>
    <div class="gpa-label">Overall CGPA</div>
  </div>
  <div class="gpa-display">
    <div class="gpa-value" style="font-size:36px;"><?=$cgpaR['cr_sum']??0?></div>
    <div class="gpa-label">Total Credit Hours</div>
  </div>
  <div class="gpa-display">
    <?php $best=$conn->query("SELECT grade_letter FROM results WHERE student_id=$sid AND is_published=1 ORDER BY grade_point DESC LIMIT 1")->fetch_assoc(); ?>
    <div class="gpa-value" style="font-size:36px;"><?=$best['grade_letter']??'N/A'?></div>
    <div class="gpa-label">Best Grade</div>
  </div>
</div>

<!-- Results Table -->
<div class="table-card">
  <div class="table-header">
    <div>
      <div class="table-title">Semester-wise Results</div>
      <div class="table-subtitle">All published results</div>
    </div>
    <a href="transcript.php" class="btn btn-primary" style="font-size:12px; padding:8px 14px;">
      <i class="fas fa-file-arrow-down"></i> Download Transcript
    </a>
  </div>
  <table>
    <thead>
      <tr><th>Course</th><th>Semester</th><th>Attendance</th><th>Class Test</th><th>Midterm</th><th>Final</th><th>Total</th><th>GP</th><th>Grade</th></tr>
    </thead>
    <tbody>
      <?php while($r=$results->fetch_assoc()): ?>
      <tr>
        <td>
          <div style="font-weight:600; font-size:13px;"><?=htmlspecialchars($r['course_name'])?></div>
          <div style="font-size:11px; color:var(--muted);"><?=$r['course_code']?> | <?=$r['credit_hours']?> Cr</div>
        </td>
        <td style="font-size:12px; color:var(--muted);"><?=$r['semester_name']?><br><?=$r['year']?></td>
        <td><?=$r['attendance_marks']?>/10</td>
        <td><?=$r['class_test_marks']?>/15</td>
        <td><?=$r['midterm_marks']?>/30</td>
        <td><?=$r['final_marks']?>/45</td>
        <td style="font-weight:700; font-size:15px; color:var(--accent);"><?=$r['total_marks']?></td>
        <td style="font-weight:700;"><?=$r['grade_point']?></td>
        <td>
          <span class="badge <?= in_array($r['grade_letter'],['A+','A']) ? 'badge-cyan' : ($r['grade_letter']==='F' ? 'badge-red' : 'badge-green') ?>">
            <?=$r['grade_letter']?>
          </span>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Grade Scale Reference -->
<div class="table-card" style="margin-top:20px;">
  <div class="table-header"><div class="table-title">Grading Scale Reference</div></div>
  <div style="padding:20px; display:grid; grid-template-columns:repeat(5,1fr); gap:10px; text-align:center;">
    <?php
    $scale=[['A+',80,100,4.00],['A',75,79,3.75],['A-',70,74,3.50],['B+',65,69,3.25],['B',60,64,3.00],['B-',55,59,2.75],['C+',50,54,2.50],['C',45,49,2.25],['D',40,44,2.00],['F',0,39,0.00]];
    foreach($scale as $g):
    ?>
    <div style="background:var(--card); border:1px solid var(--border); border-radius:10px; padding:10px;">
      <div style="font-family:'Syne',sans-serif; font-size:18px; font-weight:800; color:var(--accent);"><?=$g[0]?></div>
      <div style="font-size:11px; color:var(--muted);"><?=$g[1]?>–<?=$g[2]?> marks</div>
      <div style="font-size:13px; font-weight:700; margin-top:4px;"><?=$g[3]?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
