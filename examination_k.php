<?php
session_start();

// ✅ Use session if available, else fallback
$rollno = isset($_SESSION['rollno']) ? $_SESSION['rollno'] : "";

// ✅ Connect to Database
$conn = mysqli_connect("localhost", "root", "", "student_db");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ✅ Fetch student details from students table
$student = ['name' => 'Unknown', 'rollno' => $rollno, 'branch' => 'Not Assigned'];
if (!empty($rollno)) {
    $student_query = "SELECT name, rollno, department AS branch FROM students WHERE rollno = ?";
    $stmt = $conn->prepare($student_query);
    $stmt->bind_param("s", $rollno);
    $stmt->execute();
    $student_result = $stmt->get_result();
    if ($student_result->num_rows > 0) {
        $student = $student_result->fetch_assoc();
    }
}

// ✅ Fetch 10 random questions
$query = "SELECT id, question_text, option_a, option_b, option_c, option_d FROM questions ORDER BY RAND() LIMIT 10";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>GMRIT Examination Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #74ABE2, #5563DE);
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      padding-top: 40px;
    }
    .card { border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden; }
    .card-header { background: linear-gradient(90deg, #2C3E50, #4CA1AF); color: white; }
    .student-info { background: #f8f9fa; border-radius: 10px; padding: 10px 20px; margin-bottom: 20px; border-left: 5px solid #4CA1AF; }
    .question-block { padding: 15px; background: #fff; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .btn-submit { background: linear-gradient(90deg, #667eea, #764ba2); color: white; border: none; border-radius: 50px; padding: 10px 25px; transition: 0.3s; }
    .btn-submit:hover { transform: scale(1.05); background: linear-gradient(90deg, #764ba2, #667eea); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
    header { background: #fff; padding: 10px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
    header img { height: 60px; }
    header h2 { margin: 0; font-weight: bold; color: #2C3E50; }
  </style>
</head>
<body>
<header>
  <div class="d-flex align-items-center">
    <img src="logo_k.jpg" alt="Logo">
    <h2 class="ms-3">GMRIT EXAMINATION PORTAL</h2>
  </div>
</header>

<div class="container mb-5">
  <div class="card shadow-lg">
    <div class="card-header text-center py-3">
      <h3>🎓 Online Examination</h3>
    </div>
    <div class="card-body">
      <div class="student-info">
        <p><strong>Name:</strong> <?= htmlspecialchars($student['name']) ?></p>
        <p><strong>Roll No:</strong> <?= htmlspecialchars($student['rollno']) ?></p>
        <p><strong>Branch:</strong> <?= htmlspecialchars($student['branch']) ?></p>
      </div>

      <form action="result_k.php" method="POST">
        <?php
        $qnum = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            echo "
            <div class='question-block'>
              <label><strong>Q$qnum. " . htmlspecialchars($row['question_text']) . "</strong></label><br>
              <div class='form-check'><input class='form-check-input' type='radio' name='q{$row['id']}' value='A' required> " . htmlspecialchars($row['option_a']) . "</div>
              <div class='form-check'><input class='form-check-input' type='radio' name='q{$row['id']}' value='B'> " . htmlspecialchars($row['option_b']) . "</div>
              <div class='form-check'><input class='form-check-input' type='radio' name='q{$row['id']}' value='C'> " . htmlspecialchars($row['option_c']) . "</div>
              <div class='form-check'><input class='form-check-input' type='radio' name='q{$row['id']}' value='D'> " . htmlspecialchars($row['option_d']) . "</div>
            </div>";
            $qnum++;
        }
        ?>
        <div class="text-center mt-4">
          <button type="submit" class="btn btn-submit">Submit Exam</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
