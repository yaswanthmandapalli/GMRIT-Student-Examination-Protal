<?php
$conn = mysqli_connect("localhost", "root", "", "student_db");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

$success = "";
$error = "";

if (isset($_POST['register'])) {
  $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
  $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
  $password = trim(mysqli_real_escape_string($conn, $_POST['password']));
  $confirm_password = trim(mysqli_real_escape_string($conn, $_POST['confirm_password']));

  $password_pattern = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/";

  if ($username == "" || $email == "" || $password == "" || $confirm_password == "") {
    $error = "⚠️ All fields are required!";
  } elseif ($password !== $confirm_password) {
    $error = "❌ Passwords do not match!";
  } elseif (!preg_match($password_pattern, $password)) {
    $error = "⚠️ Password must have 8+ chars including 1 uppercase, 1 lowercase, 1 number & 1 special symbol!";
  } else {
    $check = "SELECT * FROM admin WHERE email='$email'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
      $error = "⚠️ Email already exists!";
    } else {
      $hashed_password = password_hash($password, PASSWORD_BCRYPT);
      $query = "INSERT INTO admin (username, email, password) VALUES ('$username','$email','$hashed_password')";

      if (mysqli_query($conn, $query)) {
        $success = "✅ Admin registered successfully! Redirecting...";
        header("refresh:2; url=admin_login_k.php");
      } else {
        $error = "Database Error!";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Registration</title>

<style>
body{background:linear-gradient(135deg,#1e3c72,#2a5298);font-family:Arial,sans-serif;height:100vh;display:flex;justify-content:center;align-items:center;margin:0;}
.register-container{background:#fff;padding:30px 40px;border-radius:15px;width:420px;box-shadow:0 4px 20px rgba(0,0,0,0.3);text-align:center;}
.register-container img{width:80px;margin-bottom:15px;}
h2{color:#1e3c72;margin-bottom:20px;}
input[type="text"],input[type="email"],input[type="password"]{width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:8px;font-size:15px;}
button{width:100%;background:#1e3c72;color:#fff;padding:10px;border:none;border-radius:8px;cursor:pointer;font-size:16px;transition:.3s;}
button:hover{background:#2a5298;}
.message{margin-bottom:10px;font-size:14px;}
.error{color:red;}
.success{color:green;}
a{text-decoration:none;color:#1e3c72;font-size:14px;}
a:hover{text-decoration:underline;}
.hint{font-size:12px;color:#555;text-align:left;margin-top:-5px;margin-bottom:10px;}
@media(max-width:480px){.register-container{width:90%;padding:25px;}h2{font-size:20px;}}
</style>
</head>

<body>
<div class="register-container">
<img src="logo_k.jpg">
<h2>Admin Registration</h2>

<?php
if ($error!="") echo "<div class='message error'>$error</div>";
if ($success!="") echo "<div class='message success'>$success</div>";
?>

<form method="POST" action="">
  <input type="text" name="username" placeholder="Enter Full Name" required>
  <input type="email" name="email" placeholder="Enter Email" required>
  <input type="password" name="password" placeholder="Enter Password" required>
  <input type="password" name="confirm_password" placeholder="Confirm Password" required>
  <button type="submit" name="register">Register</button>
</form>

<p style="margin-top:10px;">Already registered? <a href="admin_login_k.php">Login</a></p>
</div>
</body>
</html>
