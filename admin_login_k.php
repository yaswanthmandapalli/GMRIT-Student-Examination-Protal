<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "student_db");
if(!$conn){die("Connection Failed");}

$error="";

if(isset($_POST['login'])){
  $email=trim(mysqli_real_escape_string($conn,$_POST['email']));
  $password=trim(mysqli_real_escape_string($conn,$_POST['password']));

  $pattern="/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/";

  if(!preg_match($pattern,$password)){
    $error="⚠ Password must include 1 uppercase, 1 lowercase, 1 number, 1 symbol & min 8 characters!";
  }else{
    $sql="SELECT * FROM admin WHERE email='$email'";
    $result=mysqli_query($conn,$sql);

    if($result && mysqli_num_rows($result)==1){
      $row=mysqli_fetch_assoc($result);

      if(password_verify($password,$row['password'])){
        $_SESSION['admin_email']=$row['email'];
        $_SESSION['admin_username']=$row['username'];
        header("Location:experiment-no_k.php");
        exit;
      }else $error="❌ Incorrect Password!";
    }else $error="⚠ No admin found with this email!";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | GMRIT Examination Portal</title>

<style>
body{background:linear-gradient(135deg,#1e3c72,#2a5298);font-family:Arial;height:100vh;display:flex;justify-content:center;align-items:center;margin:0;}
.login-container{background:#fff;padding:30px 40px;border-radius:15px;width:400px;box-shadow:0 4px 20px rgba(0,0,0,.3);text-align:center;}
.login-container img{width:80px;margin-bottom:10px;}
h2{color:#1e3c72;margin-bottom:20px;}
input[type="email"],input[type="password"]{width:100%;padding:10px;margin-top:10px;border:1px solid #ccc;border-radius:8px;font-size:15px;}
button{width:100%;background:#1e3c72;color:#fff;padding:10px;border:none;border-radius:8px;font-size:16px;cursor:pointer;}
button:hover{background:#2a5298;}
.error{color:red;margin-bottom:10px;font-size:14px;}
a{text-decoration:none;color:#1e3c72;font-size:14px;}
a:hover{text-decoration:underline;}
</style>
</head>

<body>
<div class="login-container">
<img src="logo_k.jpg">
<h2>Admin Login</h2>

<?php if($error!="") echo "<div class='error'>$error</div>"; ?>

<form method="POST">
<input type="email" name="email" placeholder="Enter Email" required>
<input type="password" name="password" placeholder="Enter Password" required><br><br>
<button type="submit" name="login">Login</button>
</form>

<p style="margin-top:10px;">Don’t have an account? <a href="admin_registration_k.php">Register here</a></p>
</div>
</body>
</html>
