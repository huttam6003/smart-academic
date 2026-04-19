<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: ../index.php"); exit;
}
require_once '../config/db.php';

$uid     = $_SESSION['user_id'];
$student = $conn->query("SELECT s.*, u.name, u.email FROM students s JOIN users u ON s.user_id=u.id WHERE u.id=$uid")->fetch_assoc();
$sid     = $student['id'];

// All results grouped by semester
$results = $conn->query("
    SELECT r.*, c.course_name, c.course_code, c.credit_hours, sem.semester_name, sem.year, sem.semester_number
    FROM results r
    JOIN courses c ON r.course_id=c.id
    JOIN semesters sem ON r.semester_id=sem.id
    WHERE r.student_id=$sid AND r.is_published=1
    ORDER BY sem.semester_number, c.course_name
");

// CGPA
$cgpaQ = $conn->query("SELECT SUM(r.grade_point*c.credit_hours) as gp_sum, SUM(c.credit_hours) as cr_sum FROM results r JOIN courses c ON r.course_id=c.id WHERE r.student_id=$sid AND r.is_published=1");
$cgpaR = $cgpaQ->fetch_assoc();
$cgpa  = $cgpaR['cr_sum'] > 0 ? round($cgpaR['gp_sum']/$cgpaR['cr_sum'], 2) : 0;

// Group results by semester
$grouped = [];
$allResults = [];
while($r=$results->fetch_assoc()) {
    $grouped[$r['semester_name'].' '.$r['year']][] = $r;
    $allResults[] = $r;
}

$pageTitle = 'Transcript';
include '../includes/header.php';
?>

<div style="display:flex; gap:10px; margin-bottom:20px;">
  <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Transcript</button>
  <a href="results.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Results</a>
</div>

<!-- Transcript Card -->
<div id="transcript" style="background:var(--surface); border:1px solid var(--border); border-radius:18px; padding:40px; max-width:900px;">

  <!-- Header -->
  <div style="text-align:center; border-bottom:1px solid var(--border); padding-bottom:24px; margin-bottom:28px;">
    <div style="font-family:'Syne',sans-serif; font-size:30px; font-weight:800; color:#fff;">
      Bit<span style="color:var(--accent)">Brains</span> University
    </div>
    <div style="color:var(--muted); font-size:13px; margin-top:4px;">Department of Computer Science & Engineering</div>
    <div style="font-family:'Syne',sans-serif; font-size:16px; font-weight:700; color:var(--accent); margin-top:14px; letter-spacing:2px;">
      OFFICIAL ACADEMIC TRANSCRIPT
    </div>
  </div>

  <!-- Student Info -->
  <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:28px; background:var(--card); border-radius:12px; padding:20px;">
    <div>
      <div style="font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Student Name</div>
      <div style="font-weight:700; color:#fff;"><?=htmlspecialchars($student['name'])?></div>
    </div>
    <div>
      <div style="font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Student ID</div>
      <div style="font-weight:700; color:var(--accent);"><?=$student['student_id']?></div>
    </div>
    <div>
      <div style="font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Department</div>
      <div style="font-weight:600; color:#fff;"><?=$student['department']?></div>
    </div>
    <div>
      <div style="font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Batch</div>
      <div style="font-weight:600; color:#fff;"><?=$student['batch']?></div>
    </div>
    <div>
      <div style="font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Email</div>
      <div style="font-weight:600; color:#fff;"><?=htmlspecialchars($student['email'])?></div>
    </div>
    <div>
      <div style="font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Issued Date</div>
      <div style="font-weight:600; color:#fff;"><?=date('d F Y')?></div>
    </div>
  </div>

  <!-- Semester-wise Results -->
  <?php foreach($grouped as $semName => $courses): ?>
  <?php
    $semGP = 0; $semCR = 0;
    foreach($courses as $c) { $semGP += $c['grade_point']*$c['credit_hours']; $semCR += $c['credit_hours']; }
    $semGPA = $semCR > 0 ? round($semGP/$semCR, 2) : 0;
  ?>
  <div style="margin-bottom:24px;">
    <div style="font-weight:700; color:var(--accent); margin-bottom:10px; display:flex; align-items:center; justify-content:space-between;">
      <span><?=$semName?></span>
      <span style="font-size:13px; color:var(--muted);">GPA: <strong style="color:#fff;"><?=$semGPA?></strong></span>
    </div>
    <table style="width:100%; border-collapse:collapse;">
      <thead>
        <tr style="background:rgba(0,200,255,0.06);">
          <th style="padding:10px 14px; text-align:left; font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid var(--border);">Course</th>
          <th style="padding:10px 14px; text-align:center; font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid var(--border);">Credits</th>
          <th style="padding:10px 14px; text-align:center; font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid var(--border);">Total</th>
          <th style="padding:10px 14px; text-align:center; font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid var(--border);">Grade</th>
          <th style="padding:10px 14px; text-align:center; font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid var(--border);">GP</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($courses as $c): ?>
        <tr>
          <td style="padding:11px 14px; border-bottom:1px solid rgba(0,200,255,0.04);">
            <div style="font-weight:600; font-size:13px;"><?=htmlspecialchars($c['course_name'])?></div>
            <div style="font-size:11px; color:var(--muted);"><?=$c['course_code']?></div>
          </td>
          <td style="padding:11px 14px; text-align:center; color:var(--muted); font-size:13px; border-bottom:1px solid rgba(0,200,255,0.04);"><?=$c['credit_hours']?></td>
          <td style="padding:11px 14px; text-align:center; font-weight:700; border-bottom:1px solid rgba(0,200,255,0.04);"><?=$c['total_marks']?></td>
          <td style="padding:11px 14px; text-align:center; border-bottom:1px solid rgba(0,200,255,0.04);">
            <span style="background:rgba(0,200,255,0.12); color:var(--accent); padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700;"><?=$c['grade_letter']?></span>
          </td>
          <td style="padding:11px 14px; text-align:center; font-weight:700; color:var(--accent); border-bottom:1px solid rgba(0,200,255,0.04);"><?=$c['grade_point']?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endforeach; ?>

  <!-- CGPA Summary -->
  <div style="background:linear-gradient(135deg, rgba(0,200,255,0.1), rgba(124,58,237,0.1)); border:1px solid var(--border); border-radius:14px; padding:20px; display:flex; align-items:center; justify-content:space-between; margin-top:10px;">
    <div>
      <div style="font-size:13px; color:var(--muted);">Total Credit Hours Earned</div>
      <div style="font-family:'Syne',sans-serif; font-size:28px; font-weight:800; color:#fff;"><?=$cgpaR['cr_sum']?></div>
    </div>
    <div style="text-align:center;">
      <div style="font-size:13px; color:var(--muted);">Cumulative GPA (CGPA)</div>
      <div style="font-family:'Syne',sans-serif; font-size:48px; font-weight:800; color:var(--accent);"><?=$cgpa?></div>
    </div>
    <div style="text-align:right;">
      <div style="font-size:13px; color:var(--muted);">Standing</div>
      <div style="font-family:'Syne',sans-serif; font-size:20px; font-weight:800; color:#fff;">
        <?= $cgpa >= 3.75 ? 'Distinction' : ($cgpa >= 3.25 ? 'Merit' : ($cgpa >= 2.50 ? 'Pass' : 'Fail')) ?>
      </div>
    </div>
  </div>

  <!-- Signature area -->
  <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-top:40px; text-align:center;">
    <div>
      <div style="border-top:1px solid var(--border); padding-top:10px; font-size:12px; color:var(--muted);">Examiner's Signature</div>
    </div>
    <div>
      <div style="border-top:1px solid var(--border); padding-top:10px; font-size:12px; color:var(--muted);">Controller of Examinations</div>
    </div>
    <div>
      <div style="border-top:1px solid var(--border); padding-top:10px; font-size:12px; color:var(--muted);">Vice Chancellor</div>
    </div>
  </div>
</div>

<style>
@media print {
  .sidebar, .topbar, .btn, .main-content > div:first-child { display: none !important; }
  .main-content { margin-left: 0 !important; }
  body { background: white !important; }
  #transcript { border: 1px solid #ddd !important; color: #111 !important; background: white !important; }
}
</style>

<?php include '../includes/footer.php'; ?>
