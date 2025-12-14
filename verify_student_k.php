<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "student_db");
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

$message = "";

// When form is submitted
if (isset($_POST['verify'])) {
    $rollno = mysqli_real_escape_string($conn, $_POST['rollno']);

    // Query to check if student exists
    $sql = "SELECT * FROM students WHERE rollno = '$rollno'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Fetch student data
        $student = mysqli_fetch_assoc($result);

        // Store info in session
        $_SESSION['student_email'] = $student['email'];
        $_SESSION['student_name'] = $student['name'];
        $_SESSION['student_rollno'] = $student['rollno'];
        $_SESSION['student_department'] = $student['department'];


        // Redirect to payment page
        header("Location: access_k.php");
        exit();
    } else {
        $message = "<div class='alert alert-danger'>❌ Roll number not found. Please register first.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Student | GMRIT Examination</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #0072ff, #00c6ff);
      font-family: 'Poppins', sans-serif;
    }
    .verify-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      padding: 30px;
      margin-top: 100px;
    }
    .btn-verify {
      background-color: #002970;
      color: #fff;
      border-radius: 30px;
      transition: 0.3s;
    }
    .btn-verify:hover {
      background-color: #0040ff;
    }
    .logo {
      width: 100px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="col-md-5 verify-card text-center">
      <img src="logo_k.jpg" class="logo" alt="GMRIT Logo">
      <h3 class="text-primary mb-3">Access for GMRIT Examination</h3>
      <p class="text-muted">Enter your roll number to Access the Student</p>

      <?php echo $message; ?>

      <form method="POST" action="">
        <div class="mb-3 text-start">
          <label class="form-label">Roll Number</label>
          <input type="text" name="rollno" class="form-control" required>
        </div>

        <button type="submit" name="verify" class="btn btn-verify w-100 py-2">Verify & Proceed</button>
      </form>
    </div>
  </div>
</body>
</html>
