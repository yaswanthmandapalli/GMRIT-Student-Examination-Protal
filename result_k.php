<?php
session_start();

// ✅ Get student info
$name = $_SESSION['name'] ?? 'N/A';
$rollno = $_SESSION['rollno'] ?? 'N/A';
$branch = $_SESSION['branch'] ?? 'N/A';

// ✅ Connect to Database
$conn = mysqli_connect("localhost", "root", "", "student_db");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ✅ Fetch all questions with correct answers
$query = "SELECT id, correct_answer FROM questions LIMIT 10";
$result = mysqli_query($conn, $query);

$score = 0;
$total = mysqli_num_rows($result);

// ✅ Calculate marks
while ($row = mysqli_fetch_assoc($result)) {
    $qid = $row['id'];
    $correct = strtoupper(trim($row['correct_answer']));
    $user_answer = strtoupper(trim($_POST["q$qid"] ?? ''));
    if ($user_answer === $correct) {
        $score++;
    }
}

// ✅ Store result
$stmt = $conn->prepare("INSERT INTO results (name, rollno, branch, marks) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $name, $rollno, $branch, $score);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Exam Result</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #74ABE2, #5563DE);
      height: 100vh;
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }

    .card {
      width: 400px;
      border: none;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      background: #fff;
      animation: slideUp 1s ease-out;
    }

    @keyframes slideUp {
      from { transform: translateY(50px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    .card h3 {
      font-weight: 600;
      background: linear-gradient(90deg, #2C3E50, #4CA1AF);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 20px;
    }

    .card p {
      font-size: 16px;
      color: #444;
      margin: 8px 0;
    }

    .score-badge {
      background: linear-gradient(90deg, #1D976C, #93F9B9);
      padding: 10px 20px;
      border-radius: 50px;
      color: white;
      font-weight: bold;
      font-size: 18px;
      display: inline-block;
      animation: popIn 0.8s ease-in-out;
    }

    @keyframes popIn {
      0% { transform: scale(0.5); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    .btn-custom {
      background: linear-gradient(90deg, #667eea, #764ba2);
      color: white;
      border: none;
      border-radius: 50px;
      transition: 0.3s;
      font-weight: 500;
    }

    .btn-custom:hover {
      transform: scale(1.05);
      background: linear-gradient(90deg, #764ba2, #667eea);
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    footer {
      position: absolute;
      bottom: 10px;
      font-size: 13px;
      color: rgba(255,255,255,0.8);
    }
  </style>
</head>
<body>
  <div class="card text-center p-4">
    <h3>🎓 Exam Result</h3>
    <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
    <p><strong>Roll No:</strong> <?= htmlspecialchars($rollno) ?></p>
    <p><strong>Branch:</strong> <?= htmlspecialchars($branch) ?></p>
    <p><strong>Marks:</strong> <span class="score-badge"><?= htmlspecialchars($score) ?> / <?= htmlspecialchars($total) ?></span></p>
    
    <?php if ($score >= $total * 0.5): ?>
      <p class="text-success fw-bold mt-2">🎉 Congratulations! You Passed!</p>
    <?php else: ?>
      <p class="text-danger fw-bold mt-2">❌ Better Luck Next Time!</p>
    <?php endif; ?>

    <div class="mt-4">
      <a href="examination_k.php" class="btn btn-custom px-4">Take Again</a>
    </div>
  </div>

  <footer><?= date("Y") ?> GMRIT Examination Portal</footer>
</body>
</html>
