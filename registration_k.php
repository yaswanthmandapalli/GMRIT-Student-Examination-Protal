<?php
// ================== PHPMailer Include ==================
require './PHPMailer-master/src/PHPMailer.php';
require './PHPMailer-master/src/SMTP.php';
require './PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GMRIT | Student Registration</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: url('registration_k.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Poppins', sans-serif;
    }

    .card {
      border-radius: 15px;
      background-color: rgba(255, 255, 255, 0.9);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }

    .navbar-brand img {
      height: 40px;
      width: 40px;
      margin-right: 10px;
      border-radius: 50%;
    }

    .navbar {
      background: rgba(0, 102, 204, 0.9);
      backdrop-filter: blur(5px);
    }

    .card-header {
      border-radius: 15px 15px 0 0;
    }

    .btn-primary {
      background-color: #0066cc;
      border: none;
      transition: 0.3s;
    }

    .btn-primary:hover {
      background-color: #004c99;
    }

    .top-btn {
      text-align: center;
      margin-top: 30px;
    }

    .top-btn .btn {
      font-size: 18px;
      padding: 10px 25px;
      border-radius: 30px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .password-toggle {
      position: relative;
    }

    .password-toggle i {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #555;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="logo_k.jpg" alt="GMRIT Logo">
      <span>GMRIT | Student Registration</span>
    </a>
    <a href="login_k.php" class="btn btn-danger ms-auto">Go to Login Page</a>
  </div>
</nav>

<div class="container mt-5" id="registrationForm">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-lg">
        <div class="card-header text-center bg-primary text-white">
          <h3>Register Student</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="">
            <div class="mb-3">
              <label class="form-label">Roll Number</label>
              <input type="text" name="rollno" class="form-control" placeholder="Enter Roll Number" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-control" placeholder="Enter Full Name" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Mobile Number</label>
              <input type="tel" name="mobile" class="form-control" placeholder="Enter Mobile Number" pattern="[0-9]{10}" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Department</label>
              <select name="department" class="form-select" required>
                <option value="">Select Department</option>
                <option value="CSE">CSE</option>
                <option value="CSE(AI&DS)">CSE(AI&DS)</option>
                <option value="CSE(AI&ML)">CSE(AI&ML)</option>
                <option value="ECE">ECE</option>
                <option value="EEE">EEE</option>
                <option value="MECH">MECH</option>
                <option value="CIVIL">CIVIL</option>
                <option value="IT">IT</option>
              </select>
            </div>

            <div class="mb-3 password-toggle">
              <label class="form-label">Password</label>
              <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password" required>
              <i class="bi bi-eye-slash" id="togglePassword"></i>
            </div>

            <div class="d-grid">
              <button type="submit" name="submit" class="btn btn-primary">Register</button>
            </div>
          </form>
        </div>
        <div class="card-footer text-center text-muted">
          2025 GMRIT Student Registration System
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS & Icons -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<script>
  const togglePassword = document.querySelector('#togglePassword');
  const password = document.querySelector('#password');

  togglePassword.addEventListener('click', function () {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);

    // Toggle the eye / eye-slash icon
    this.classList.toggle('bi-eye');
    this.classList.toggle('bi-eye-slash');
  });
</script>

<?php
if(isset($_POST['submit'])){
    $rollno = $_POST['rollno'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $department = $_POST['department'];
    $password = $_POST['password'];

    // ✅ Password strength validation
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8){
        echo "<div class='alert alert-danger text-center mt-3'>
                Password must be at least 8 characters long and include 
                at least one uppercase letter, one lowercase letter, 
                one number, and one special character!
              </div>";
    } else {
        // ✅ Hash password if strong
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $conn = mysqli_connect("localhost", "root", "", "student_db");
        if(!$conn){
            die("<div class='alert alert-danger text-center mt-3'>Connection failed: " . mysqli_connect_error() . "</div>");
        }

        $checkQuery = "SELECT * FROM students WHERE rollno='$rollno' OR email='$email'";
        $result = mysqli_query($conn, $checkQuery);

        if(mysqli_num_rows($result) > 0){
            echo "<div class='alert alert-warning text-center mt-3'>
                    Roll Number or Email already exists!
                  </div>";
        } else {
            $sql = "INSERT INTO students (rollno, name, email, mobile, department, password)
                    VALUES ('$rollno', '$name', '$email', '$mobile', '$department', '$hashed_password')";
            
            if(mysqli_query($conn, $sql)){
                // ========== SEND EMAIL TO REGISTERED MAIL ==========
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'gcoloab@gmail.com';     // your Gmail
                    $mail->Password   = 'dnimfjezbecxcjjk';      // your Gmail App Password
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    // Recipients
                    $mail->setFrom('gcoloab@gmail.com', 'GMRIT Portal');
                    $mail->addAddress($email, $name); // send to registered email

                    // Email content
                    $mail->isHTML(true);
                    $mail->Subject = 'GMRIT Portal Registration';
                    $mail->Body    = 'You are registered in the GMRIT Portal';
                    $mail->AltBody = 'You are registered in the GMRIT Portal';

                    $mail->send();

                    echo "<div class='alert alert-success text-center mt-3'>
                            Registration successful!<br>
                            Email sent to <b>$email</b> with message: <i>You are registered in the GMRIT Portal</i>.<br>
                            Redirecting to login page...
                          </div>";
                } catch (Exception $e) {
                    echo "<div class='alert alert-warning text-center mt-3'>
                            Registration successful, but email could not be sent.<br>
                            Mailer Error: {$mail->ErrorInfo}
                          </div>";
                }

                echo "<script>
                        setTimeout(function(){
                            window.location.href = 'login_k.php';
                        }, 3000);
                      </script>";
            } else {
                echo "<div class='alert alert-danger text-center mt-3'>
                        Error: " . mysqli_error($conn) . "
                      </div>";
            }
        }

        mysqli_close($conn);
    }
}
?>
</body>
</html>
  