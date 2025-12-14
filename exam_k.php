<?php
session_start();

if (!isset($_SESSION['email'])) {
  header("Location: login_k.php");
  exit();
}

$conn = mysqli_connect("localhost", "root", "", "student_db");
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$email = $_SESSION['email'];
$query = "SELECT name, rollno, department FROM students WHERE email='$email'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);

// Store in session
$_SESSION['student_name']       = $student['name'];
$_SESSION['student_rollno']     = $student['rollno'];
$_SESSION['student_department'] = $student['department'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GMRIT EXAMINATION PORTAL</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: linear-gradient(135deg, #89f7fe, #66a6ff);
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 0;
    }

    header {
      width: 100%;
      background: #1f1f1f;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 15px 0;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    header img {
      height: 50px;
      width: 50px;
      margin-right: 15px;
      border-radius: 50%;
      border: 2px solid white;
    }

    header h1 {
      font-size: 24px;
      letter-spacing: 1px;
      font-weight: 600;
    }

    .student-info {
      margin-top: 20px;
      background: #ffffffa8;
      padding: 15px 30px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      text-align: center;
      font-size: 18px;
    }

    .student-info span {
      font-weight: bold;
      color: #333;
    }

    .card-container {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 40px;
      flex-wrap: wrap;
      width: 100%;
      max-width: 900px;
      padding: 40px 20px;
    }

    .card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      width: 260px;
      text-align: center;
      padding: 25px;
      transition: transform 0.4s ease, box-shadow 0.4s ease;
      animation: fadeIn 1s ease;
      position: relative;
    }

    .card:hover {
      transform: translateY(-10px) scale(1.05);
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
      animation: glow 2s infinite alternate;
    }

    .card h3 {
      margin-bottom: 20px;
      color: #333;
      font-size: 1.2rem;
    }

    .btn {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 25px;
      cursor: pointer;
      font-size: 16px;
      transition: all 0.3s ease;
      outline: none;
      box-shadow: 0 4px 10px rgba(37, 117, 252, 0.3);
      position: relative;
      overflow: hidden;
    }

    .btn:hover {
      transform: scale(1.1);
      box-shadow: 0 6px 20px rgba(37, 117, 252, 0.5);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes glow {
      from { box-shadow: 0 0 15px rgba(37, 117, 252, 0.2); }
      to { box-shadow: 0 0 25px rgba(37, 117, 252, 0.5); }
    }

    @media (max-width: 768px) {
      .card-container { flex-direction: column; gap: 25px; }
      .card { width: 90%; max-width: 320px; }
    }
  </style>
</head>

<body>

<header>
    <img src="logo_k.jpg" alt="Logo">
    <h1>GMRIT EXAMINATION PORTAL</h1>
</header>

<div class="student-info">
    <p>
      Name: <span><?php echo $student['name']; ?></span> |
      Roll No: <span><?php echo $student['rollno']; ?></span> |
      Department: <span><?php echo $student['department']; ?></span>
    </p>
</div>

<div class="card-container">

    <!-- Web Tech Exam -->
    <div class="card">
      <h3>Web Technology</h3>
      <button class="btn" onclick="window.location.href='web_k.php'">Attempt Exam</button>
    </div>

    <!-- ⭐ NEW EXTRA WEB CARD -->
    <div class="card">
      <h3>2nd-year Python Praticals Expriments</h3>
      <button class="btn" onclick="window.location.href='python2_k.php'">Attempt Exam</button>
    </div>

    <!-- Python Exam -->
    <div class="card">
      <h3>Python</h3>
      <button class="btn" onclick="window.location.href='python_k.php'">Attempt Exam</button>
    </div>

    <!-- Objective Exam -->
    <div class="card">
      <h3>Objective Exam</h3>
      <button class="btn" onclick="window.location.href='examination_k.php'">Attempt Exam</button>
    </div>

</div>

</body>
</html>
