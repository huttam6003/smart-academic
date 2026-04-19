<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php"); exit;
}
require_once '../config/db.php';

// Overall stats
$totalStudents = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'];
$totalCourses  = $conn->query("SELECT COUNT(*) as c FROM courses")->fetch_assoc()['c'];
$avgGpa        = $conn->query("SELECT AVG(grade_point) as g FROM results WHERE is_published=1")->fetch_assoc()['g'];
$topStudents   = $conn->query("
    SELECT u.name, s.student_id, s.department,
           AVG(r.grade_point) as cgpa, COUNT(r.id) as courses
    FROM results r
    JOIN students s ON r.student_id=s.id
    JOIN users u ON s.user_id=u.id
    WHERE r.is_published=1
    GROUP BY s.id ORDER BY cgpa DESC LIMIT 10
");

$gradeDistrib = $conn->query("
    SELECT grade_letter, COUNT(*) as count
    FROM results WHERE is_published=1
    GROUP BY grade_letter ORDER BY grade_point DESC
");

$pageTitle = 'Reports';
include '../includes/header.php';
?>

<div class="stat-grid">
  <div class="stat-card cyan">
    <div class="stat-icon cyan"><i class="fas fa-users"></i></div>
    <div class="stat-value"><?=$totalStudents?></div>
    <div class="stat-label">Total Students</div>
  </div>
  <div class="stat-card purple">
    <div class="stat-icon purple"><i class="fas fa-book-open"></i></div>
    <div class="stat-value"><?=$totalCourses?></div>
    <div class="stat-label">Total Courses</div>
  </div>
  <div class="stat-card green">
    <div class="stat-icon green"><i class="fas fa-star"></i></div>
    <div class="stat-value"><?=number_format($avgGpa??0,2)?></div>
    <div class="stat-label">Average CGPA</div>
  </div>
  <div class="stat-card yellow">
    <div class="stat-icon yellow"><i class="fas fa-trophy"></i></div>
    <div class="stat-value">A+</div>
    <div class="stat-label">Top Grade</div>
  </div>
</div>

<div class="section-grid">
  <!-- Top Students -->
  <div class="table-card">
    <div class="table-header">
      <div>
        <div class="table-title">Top Performing Students</div>
        <div class="table-subtitle">Ranked by CGPA</div>
      </div>
    </div>
    <table>
      <thead><tr><th>Rank</th><th>Student</th><th>ID</th><th>CGPA</th><th>Courses</th></tr></thead>
      <tbody>
        <?php $rank=1; while($s=$topStudents->fetch_assoc()): ?>
        <tr>
          <td>
            <?php if($rank===1): ?>
              <span style="color:#fbbf24; font-size:16px;">🥇</span>
            <?php elseif($rank===2): ?>
              <span style="color:#9ca3af; font-size:16px;">🥈</span>
            <?php elseif($rank===3): ?>
              <span style="color:#d97706; font-size:16px;">🥉</span>
            <?php else: ?>
              <span style="color:var(--muted);"><?=$rank?></span>
            <?php endif; ?>
          </td>
          <td style="font-weight:600; font-size:13px;"><?=htmlspecialchars($s['name'])?></td>
          <td><span class="badge badge-cyan"><?=$s['student_id']?></span></td>
          <td>
            <span style="font-family:'Syne',sans-serif; font-size:18px; font-weight:800; color:var(--accent);">
              <?=number_format($s['cgpa'],2)?>
            </span>
          </td>
          <td style="color:var(--muted);"><?=$s['courses']?></td>
        </tr>
        <?php $rank++; endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Grade Distribution -->
  <div class="table-card">
    <div class="table-header">
      <div class="table-title">Grade Distribution</div>
    </div>
    <div style="padding:20px;">
      <?php
      $grades = $conn->query("SELECT grade_letter, COUNT(*) as count FROM results WHERE is_published=1 GROUP BY grade_letter ORDER BY count DESC");
      $total  = $conn->query("SELECT COUNT(*) as c FROM results WHERE is_published=1")->fetch_assoc()['c'];
      $colors = ['A+'=>'var(--accent)','A'=>'#6ee7b7','A-'=>'#10b981','B+'=>'var(--yellow)','B'=>'#f97316','F'=>'var(--red)'];
      while($g=$grades->fetch_assoc()):
        $pct = $total > 0 ? round(($g['count']/$total)*100) : 0;
        $col = $colors[$g['grade_letter']] ?? 'var(--muted)';
      ?>
      <div style="margin-bottom:16px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:13px;">
          <span style="font-weight:600; color:<?=$col?>"><?=$g['grade_letter']?></span>
          <span style="color:var(--muted);"><?=$g['count']?> students (<?=$pct?>%)</span>
        </div>
        <div style="background:var(--card); border-radius:20px; height:8px; overflow:hidden;">
          <div style="width:<?=$pct?>%; height:100%; background:<?=$col?>; border-radius:20px; transition:width 1s;"></div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
