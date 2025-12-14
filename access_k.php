<?php
session_start();

/*
  Full page: fetch mobile from students table by rollno only,
  show student info and on submit insert into access table.
*/

// Require login
if (!isset($_SESSION['student_email'])) {
  header("Location: verify_student.php");
  exit();
}

// Session values
$name = isset($_SESSION['student_name']) ? $_SESSION['student_name'] : '';
$rollno = isset($_SESSION['student_rollno']) ? $_SESSION['student_rollno'] : '';
$email = isset($_SESSION['student_email']) ? $_SESSION['student_email'] : '';
$department = isset($_SESSION['student_department']) ? $_SESSION['student_department'] : '';

// DB connection
$conn = mysqli_connect("localhost", "root", "", "student_db");
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// --- Retrieve mobile from students table using rollno ONLY ---
$mobile = "";
if (!empty($rollno)) {
    $sql = "SELECT mobile FROM students WHERE rollno = ? LIMIT 1";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $rollno);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $mobile_db);
        if (mysqli_stmt_fetch($stmt)) {
            $mobile = $mobile_db;
        }
        mysqli_stmt_close($stmt);
    }
}

// Save mobile to session (if found or empty - keeps consistency)
$_SESSION['student_mobile'] = $mobile;

// --- Handle form submission (record access) ---
if (isset($_POST['pay_now'])) {
    // Respect readonly mobile: read from session (or from posted value if you later allow editing)
    $mobile_post = isset($_POST['mobile']) ? trim($_POST['mobile']) : $mobile;

    $name_clean = $name;
    $rollno_clean = $rollno;
    $email_clean = $email;
    $mobile_clean = $mobile_post;

    // Check duplicate by rollno + email
    $checkSql = "SELECT id FROM access WHERE rollno = ? AND email = ? LIMIT 1";
    if ($stmt = mysqli_prepare($conn, $checkSql)) {
        mysqli_stmt_bind_param($stmt, "ss", $rollno_clean, $email_clean);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('❌ Database error. Please contact admin.');</script>";
        exit();
    }

    if ($exists) {
        echo "<script>
          alert('✅ Access already recorded for this student.');
          window.location.href = 'verify_student_k.php';
        </script>";
        exit();
    } else {
        $insertSql = "INSERT INTO access (name, rollno, email, mobile, created_at, date) VALUES (?, ?, ?, ?, NOW(), CURDATE())";
        if ($stmt = mysqli_prepare($conn, $insertSql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $name_clean, $rollno_clean, $email_clean, $mobile_clean);
            $exec = mysqli_stmt_execute($stmt);
            if ($exec) {
                mysqli_stmt_close($stmt);
                // Save mobile to session for future pages
                if (!empty($mobile_clean)) {
                    $_SESSION['student_mobile'] = $mobile_clean;
                }
                echo "<script>
                  alert('🎉 Access recorded successfully.');
                  window.location.href = 'verify_student_k.php';
                </script>";
                exit();
            } else {
                $err = htmlspecialchars(mysqli_error($conn));
                mysqli_stmt_close($stmt);
                echo "<script>
                  alert('❌ Failed to record access: {$err}');
                  window.location.href = 'verify_student_k.php';
                </script>";
                exit();
            }
        } else {
            $err = htmlspecialchars(mysqli_error($conn));
            echo "<script>
              alert('❌ Database prepare error: {$err}');
              window.location.href = 'verify_student_k.php';
            </script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Examination Fee Payment | GMRIT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #0072ff, #00c6ff);
      font-family: 'Segoe UI', sans-serif;
    }
    .payment-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      padding: 30px;
      margin-top: 60px;
    }
    .btn-pay {
      background-color: #002970;
      color: #fff;
      border-radius: 30px;
      transition: 0.3s;
    }
    .btn-pay:hover {
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
    <div class="col-md-6 payment-card text-center">
      <img src="logo_k.jpg" class="logo" alt="GMRIT Logo">
      <h3 class="mb-3 text-primary">GMRIT Practical Access to Students</h3>
      <p class="text-muted">Confirm your Student details and proceed to Access</p>

      <form method="POST" action="">
        <div class="mb-3 text-start">
          <label class="form-label">Full Name</label>
          <input type="text" class="form-control" value="<?php echo htmlspecialchars($name); ?>" readonly>
        </div>

        <div class="mb-3 text-start">
          <label class="form-label">Roll Number</label>
          <input type="text" class="form-control" value="<?php echo htmlspecialchars($rollno); ?>" readonly>
        </div>

        <div class="mb-3 text-start">
          <label class="form-label">Department</label>
          <input type="text" class="form-control" value="<?php echo htmlspecialchars($department); ?>" readonly>
        </div>

        <div class="mb-3 text-start">
          <label class="form-label">Email</label>
          <input type="text" class="form-control" value="<?php echo htmlspecialchars($email); ?>" readonly>
        </div>

        <div class="mb-3 text-start">
          <label class="form-label">Mobile</label>
          <!-- Readonly as requested; remove readonly if you want the student to edit mobile -->
          <input type="text" name="mobile" class="form-control" value="<?php echo htmlspecialchars($mobile); ?>" readonly>
        </div>

        <button type="submit" name="pay_now" class="btn btn-pay w-100 py-2">Access to the Students</button>
      </form>
    </div>
  </div>
</body>
</html>
