<?php
session_start();

// Restrict access if not logged in
if (!isset($_SESSION['admin_email'])) {
  header("Location: admin_login.php");
  exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "student_db");
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$message = "";

if (isset($_POST['submit'])) {
  $question = mysqli_real_escape_string($conn, $_POST['question']);
  $option_a = mysqli_real_escape_string($conn, $_POST['option_a']);
  $option_b = mysqli_real_escape_string($conn, $_POST['option_b']);
  $option_c = mysqli_real_escape_string($conn, $_POST['option_c']);
  $option_d = mysqli_real_escape_string($conn, $_POST['option_d']);
  $correct_answer = mysqli_real_escape_string($conn, $_POST['correct_answer']);

  // Insert question into table
  $sql = "INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer)
          VALUES ('$question', '$option_a', '$option_b', '$option_c', '$option_d', '$correct_answer')";
  
  if (mysqli_query($conn, $sql)) {
    // Fetch all student emails
    $students = mysqli_query($conn, "SELECT email FROM students");
    
    $subject = "📢 New Examination Question Added - GMRIT Examination Portal";
    $body = "Dear Student,\n\nA new question has been added to the GMRIT Examination Portal.\nPlease log in to your account to review or participate in the next test.\n\nBest regards,\nGMRIT Admin Team";
    $headers = "From: gmrit@college.com";

    // Send email to each student
    while ($row = mysqli_fetch_assoc($students)) {
      mail($row['email'], $subject, $body, $headers);
    }

    $message = "✅ Question added successfully and email sent to all students!";
  } else {
    $message = "❌ Error inserting question: " . mysqli_error($conn);
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Question | GMRIT Examination Portal</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      background: #ffffff;
      color: #000;
      padding: 30px;
      border-radius: 15px;
      width: 500px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
      text-align: left;
    }

    h2 {
      text-align: center;
      color: #1e3c72;
    }

    label {
      font-weight: bold;
    }

    input[type="text"], textarea, select {
      width: 100%;
      padding: 10px;
      margin: 5px 0 15px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    button {
      width: 100%;
      background-color: #1e3c72;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
    }

    button:hover {
      background-color: #2a5298;
    }

    .msg {
      text-align: center;
      color: green;
      font-weight: bold;
      margin-bottom: 15px;
    }

    .logout {
      text-align: center;
      margin-top: 10px;
    }

    .logout a {
      color: #1e3c72;
      text-decoration: none;
      font-weight: bold;
    }

    .logout a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Add New Question</h2>

    <?php if (!empty($message)) echo "<div class='msg'>$message</div>"; ?>

    <form method="POST" action="">
      <label>Question:</label>
      <textarea name="question" required></textarea>

      <label>Option A:</label>
      <input type="text" name="option_a" required>

      <label>Option B:</label>
      <input type="text" name="option_b" required>

      <label>Option C:</label>
      <input type="text" name="option_c" required>

      <label>Option D:</label>
      <input type="text" name="option_d" required>

      <label>Correct Answer:</label>
      <select name="correct_answer" required>
        <option value="">-- Select Correct Answer --</option>
        <option value="A">Option A</option>
        <option value="B">Option B</option>
        <option value="C">Option C</option>
        <option value="D">Option D</option>
      </select>

      <button type="submit" name="submit">Add Question</button>
    </form>

    <div class="logout">
      <p><a href="admin_logout.php">Logout</a></p>
    </div>
  </div>

</body>
</html>
