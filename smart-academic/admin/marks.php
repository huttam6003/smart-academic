<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php"); exit;
}
require_once '../config/db.php';

$msg = '';

// Save marks
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_marks'])) {
    $student_id  = (int)$_POST['student_id'];
    $course_id   = (int)$_POST['course_id'];
    $semester_id = (int)$_POST['semester_id'];
    $att  = (float)$_POST['attendance'];
    $ct   = (float)$_POST['class_test'];
    $mid  = (float)$_POST['midterm'];
    $fin  = (float)$_POST['final'];
    $tot  = $att + $ct + $mid + $fin;
    $gp   = calculateGPA($tot);

    $check = $conn->prepare("SELECT id FROM results WHERE student_id=? AND course_id=? AND semester_id=?");
    $check->bind_param("iii", $student_id, $course_id, $semester_id);
    $check->execute();
    $existing = $check->get_result()->fetch_assoc();

    if ($existing) {
        $stmt = $conn->prepare("UPDATE results SET attendance_marks=?, class_test_marks=?, midterm_marks=?, final_marks=?, total_marks=?, grade_point=?, grade_letter=? WHERE id=?");
        $stmt->bind_param("ddddddsi", $att, $ct, $mid, $fin, $tot, $gp['point'], $gp['letter'], $existing['id']);
    } else {
        $stmt = $conn->prepare("INSERT INTO results (student_id, course_id, semester_id, attendance_marks, class_test_marks, midterm_marks, final_marks, total_marks, grade_point, grade_letter) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("iiiddddds", $student_id, $course_id, $semester_id, $att, $ct, $mid, $fin, $tot, $gp['point'], $gp['letter']);
    }
    $stmt->execute();

    // Publish
    if (isset($_POST['publish'])) {
        $conn->query("UPDATE results SET is_published=1 WHERE student_id=$student_id AND course_id=$course_id AND semester_id=$semester_id");
    }
    $msg = "saved";
}

$students  = $conn->query("SELECT s.id, s.student_id, u.name FROM students s JOIN users u ON s.user_id=u.id ORDER BY u.name");
$courses   = $conn->query("SELECT * FROM courses ORDER BY course_name");
$semesters = $conn->query("SELECT * FROM semesters ORDER BY semester_number");

// All results
$results = $conn->query("
    SELECT r.*, u.name as sname, c.course_name, sem.semester_name
    FROM results r
    JOIN students s ON r.student_id=s.id
    JOIN users u ON s.user_id=u.id
    JOIN courses c ON r.course_id=c.id
    JOIN semesters sem ON r.semester_id=sem.id
    ORDER BY r.created_at DESC
");

$pageTitle = 'Marks Entry';
include '../includes/header.php';
?>

<?php if($msg === 'saved'): ?>
<div style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); border-radius:12px; padding:14px 18px; margin-bottom:20px; color:#6ee7b7; display:flex; align-items:center; gap:8px;">
  <i class="fas fa-circle-check"></i> Marks saved successfully!
</div>
<?php endif; ?>

<div class="section-grid" style="grid-template-columns: 380px 1fr; align-items:start;">
  <!-- Entry Form -->
  <div class="form-card">
    <div style="font-weight:700; font-size:16px; color:#fff; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
      <i class="fas fa-pen-to-square" style="color:var(--accent);"></i> Enter Marks
    </div>
    <form method="POST">
      <input type="hidden" name="save_marks" value="1">
      <div class="form-group">
        <label class="form-label">Select Student</label>
        <select name="student_id" class="form-control" required>
          <option value="">— Select Student —</option>
          <?php while($s=$students->fetch_assoc()): ?>
          <option value="<?=$s['id']?>"><?= htmlspecialchars($s['name']) ?> (<?=$s['student_id']?>)</option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Select Course</label>
        <select name="course_id" class="form-control" required>
          <option value="">— Select Course —</option>
          <?php while($c=$courses->fetch_assoc()): ?>
          <option value="<?=$c['id']?>"><?= htmlspecialchars($c['course_name']) ?> (<?=$c['course_code']?>)</option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Semester</label>
        <select name="semester_id" class="form-control" required>
          <option value="">— Select Semester —</option>
          <?php while($sem=$semesters->fetch_assoc()): ?>
          <option value="<?=$sem['id']?>"><?=$sem['semester_name']?> (<?=$sem['year']?>)</option>
          <?php endwhile; ?>
        </select>
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
          <label class="form-label">Attendance (10)</label>
          <input type="number" name="attendance" min="0" max="10" step="0.5" class="form-control" placeholder="0-10" required>
        </div>
        <div class="form-group">
          <label class="form-label">Class Test (15)</label>
          <input type="number" name="class_test" min="0" max="15" step="0.5" class="form-control" placeholder="0-15" required>
        </div>
        <div class="form-group">
          <label class="form-label">Midterm (30)</label>
          <input type="number" name="midterm" min="0" max="30" step="0.5" class="form-control" placeholder="0-30" required>
        </div>
        <div class="form-group">
          <label class="form-label">Final (45)</label>
          <input type="number" name="final" min="0" max="45" step="0.5" class="form-control" placeholder="0-45" required>
        </div>
      </div>
      <div style="display:flex; align-items:center; gap:10px; margin-bottom:18px;">
        <input type="checkbox" name="publish" id="pub" style="accent-color:var(--accent); width:16px; height:16px;">
        <label for="pub" style="font-size:13px; color:var(--muted); cursor:pointer;">Publish result immediately</label>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
        <i class="fas fa-floppy-disk"></i> Save Marks
      </button>
    </form>
  </div>

  <!-- Results Table -->
  <div class="table-card">
    <div class="table-header">
      <div>
        <div class="table-title">All Results</div>
        <div class="table-subtitle">Entered marks & grades</div>
      </div>
    </div>
    <table>
      <thead>
        <tr><th>Student</th><th>Course</th><th>Semester</th><th>Total</th><th>Grade</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php while($r = $results->fetch_assoc()): ?>
        <tr>
          <td style="font-size:13px; font-weight:600;"><?= htmlspecialchars($r['sname']) ?></td>
          <td style="font-size:12px; color:var(--muted);"><?= substr($r['course_name'],0,20) ?>...</td>
          <td style="font-size:12px; color:var(--muted);"><?= $r['semester_name'] ?></td>
          <td style="font-weight:600;"><?= $r['total_marks'] ?></td>
          <td>
            <span class="badge <?= $r['grade_letter']==='A+' || $r['grade_letter']==='A' ? 'badge-cyan' : ($r['grade_letter']==='F' ? 'badge-red' : 'badge-green') ?>">
              <?= $r['grade_letter'] ?> | <?= $r['grade_point'] ?>
            </span>
          </td>
          <td>
            <span class="badge <?= $r['is_published'] ? 'badge-green' : 'badge-yellow' ?>">
              <?= $r['is_published'] ? 'Published' : 'Draft' ?>
            </span>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
