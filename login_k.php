<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Examination Login</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background: url('gate_k.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Poppins', sans-serif;
    }

    .card {
      border-radius: 15px;
      overflow: hidden;
    }

    .input-group-text {
      background: white;
      border-left: none;
      cursor: pointer;
    }

    .form-control:focus {
      box-shadow: none;
      border-color: #0d6efd;
    }

    @media (max-width: 768px) {
      .card {
        width: 90%;
        margin: auto;
      }
    }

    @media (max-width: 480px) {
      .navbar-brand {
        font-size: 16px;
      }
      .btn {
        font-size: 14px;
        padding: 6px 10px;
      }
    }
  </style>
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Student Pratical Section</a>
    <div class="ms-auto">
      <a href="index_k.php" class="btn btn-danger me-2">Home Page</a>
      <a href="registration_k.php" class="btn btn-danger">Registration Page</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-lg">
        <div class="card-header text-center bg-primary text-white">
          <h3>Student Login</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="">
            <div class="mb-3">
              <label class="form-label">Email or Mobile:</label>
              <input type="text" name="login_id" class="form-control" placeholder="Enter email or mobile number" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Password:</label>
              <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
                <span class="input-group-text" onclick="togglePassword()">
                  <i class="fa-solid fa-eye" id="eyeIcon"></i>
                </span>
              </div>
            </div>

            <div class="d-grid">
              <button type="submit" name="login" class="btn btn-primary">Login</button>
            </div>
          </form>
        </div>
        <div class="card-footer text-center text-muted">
           2025 Student Login Section
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function togglePassword() {
  const password = document.getElementById("password");
  const eyeIcon = document.getElementById("eyeIcon");
  if (password.type === "password") {
    password.type = "text";
    eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
  } else {
    password.type = "password";
    eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
  }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php
session_start();

if (isset($_POST['login'])) {
  $login_id = $_POST['login_id'];
  $password = $_POST['password'];

  $conn = mysqli_connect("localhost", "root", "", "student_db");
  if (!$conn) {
    die("<div class='alert alert-danger text-center mt-3'>Connection failed: " . mysqli_connect_error() . "</div>");
  }

  $sql = "SELECT * FROM students WHERE email='$login_id' OR mobile='$login_id'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    if (password_verify($password, $row['password'])) {
      $_SESSION['email'] = $row['email'];
      $_SESSION['rollno'] = $row['rollno'];
      $rollno = $row['rollno'];

      $payCheck = mysqli_query($conn, "SELECT * FROM access WHERE email='{$row['email']}' AND rollno='$rollno'");

      if (mysqli_num_rows($payCheck) > 0) {
        echo "<div class='alert alert-success text-center mt-3'>
                Login successful! Redirecting to Examination Section...
              </div>";
        echo "<script>
                setTimeout(function(){
                  window.location.href = 'exam_k.php';
                }, 1500);
              </script>";
      } else {
        echo "<div class='alert alert-danger text-center mt-3'>
                The Student not had the access for the Praticals.Please Contact your college Administration.
              </div>";
      }
    } else {
      echo "<div class='alert alert-danger text-center mt-3'>Invalid password!</div>";
    }
  } else {
    echo "<div class='alert alert-danger text-center mt-3'>No account found with that email or mobile number!</div>";
  }

  mysqli_close($conn);
}
?>

</body>
</html>
