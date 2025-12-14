<?php
session_start();

// Must be logged in
if (!isset($_SESSION['email'])) {
    header("Location: login_k.php");
    exit();
}

// Student details from session (set in exam_k.php)
$student_name = $_SESSION['student_name']       ?? 'Unknown';
$student_roll = $_SESSION['student_rollno']     ?? 'Unknown';
$student_dept = $_SESSION['student_department'] ?? 'Unknown';
$student_email = $_SESSION['email'];

// === Question Bank ===
$questions = [
    // --- HTML/CSS/JS QUESTIONS ---
    [
        "topic" => "HTML, CSS & JavaScript",
        "question" => "Create a web page with a button labeled 'Click Me'. When clicked, the background color of the page should change to lightblue.",
        "explanation" => "This tests your basic understanding of HTML, CSS, and JavaScript. You must create a button that, when clicked, changes the background color using DOM manipulation.",
        "test_cases" => [
            "button tag exists",
            "onclick event handler",
            "changes background color",
            "uses lightblue color"
        ],
        "marks" => 10
    ],
    [
        "topic" => "HTML, CSS & JavaScript",
        "question" => "Create an input box and a button. When the user clicks the button, display the entered text below it dynamically.",
        "explanation" => "Tests DOM interaction, getting user input, and dynamically updating HTML using JavaScript.",
        "test_cases" => [
            "input tag exists",
            "button tag exists",
            "onclick event handler",
            "updates output text dynamically"
        ],
        "marks" => 10
    ],
    [
        "topic" => "HTML, CSS & JavaScript",
        "question" => "Design a web page with a paragraph that changes its text color to red when hovered using CSS.",
        "explanation" => "Tests your understanding of CSS hover selectors and inline or external stylesheet usage.",
        "test_cases" => [
            "uses paragraph tag",
            "uses CSS hover selector",
            "changes color to red",
            "uses stylesheet or style tag"
        ],
        "marks" => 10
    ],

    // --- BOOTSTRAP QUESTIONS ---
    [
        "topic" => "Bootstrap",
        "question" => "Using Bootstrap, create a responsive layout with a navbar and a card containing 'Welcome to Bootstrap!'.",
        "explanation" => "Use Bootstrap classes such as 'navbar', 'card', 'container', and 'row'. Ensure the layout adapts responsively.",
        "test_cases" => [
            "uses 'navbar' class",
            "uses 'card' class",
            "contains 'Welcome to Bootstrap!' text",
            "responsive layout (container or row-col)"
        ],
        "marks" => 10
    ],
    [
        "topic" => "Bootstrap",
        "question" => "Create a Bootstrap form with 'Username' and 'Password' fields and a submit button centered on the page.",
        "explanation" => "Tests understanding of Bootstrap form classes and layout utilities like 'form-group', 'btn', and alignment using 'd-flex' or 'justify-content-center'.",
        "test_cases" => [
            "uses 'form-control' class",
            "has 'Username' and 'Password' fields",
            "uses 'btn' class for submit",
            "centered layout"
        ],
        "marks" => 10
    ]
];

// === Initialize exam session variables ===
if (!isset($_SESSION['index'])) {
    $_SESSION['index'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['web_result_saved'] = false; // to avoid duplicate inserts
}

// === Handle Submission (Evaluate code) ===
if (isset($_POST['submit'])) {
    $answer = strtolower(trim($_POST['answer']));
    $current = $questions[$_SESSION['index']];
    $marks_per_case = $current['marks'] / count($current['test_cases']);
    $earned = 0;
    $results = [];

    foreach ($current['test_cases'] as $case) {
        $keyword = explode(" ", $case)[0]; // very simple keyword match
        if (strpos($answer, $keyword) !== false) {
            $earned += $marks_per_case;
            $results[] = "✅ Passed: $case (+$marks_per_case marks)";
        } else {
            $results[] = "❌ Failed: $case (0 marks)";
        }
    }

    $_SESSION['score'] += $earned;
    $feedback = $results;
}

// === Next Question / Final Submit ===
if (isset($_POST['next'])) {
    $_SESSION['index']++;

    if ($_SESSION['index'] >= count($questions)) {
        // Exam finished – store marks in DB only once
        if (!$_SESSION['web_result_saved']) {
            $conn = mysqli_connect("localhost", "root", "", "student_db");
            if ($conn) {
                $marks = (int)$_SESSION['score'];
                $email = mysqli_real_escape_string($conn, $student_email);
                $name  = mysqli_real_escape_string($conn, $student_name);
                $roll  = mysqli_real_escape_string($conn, $student_roll);
                $dept  = mysqli_real_escape_string($conn, $student_dept);

                $insert = "
                    INSERT INTO web_results (email, name, rollno, department, marks)
                    VALUES ('$email', '$name', '$roll', '$dept', $marks)
                ";
                mysqli_query($conn, $insert);
                mysqli_close($conn);
            }
            $_SESSION['web_result_saved'] = true;
        }

        $finished = true;
    }
}

// === Restart Exam (but keep login + student details) ===
if (isset($_POST['restart'])) {
    $_SESSION['index'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['web_result_saved'] = false;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Web Technology – Coding Practice Portal</title>
<style>
    body {
        font-family: "Segoe UI", sans-serif;
        background: #f0f2f5;
        margin: 0;
    }

    /* Navbar with student details on right */
    header {
        background-color: #1f1f1f;
        color: #fff;
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 20px;
        font-weight: 600;
    }

    .header-left img {
        height: 40px;
        width: 40px;
        border-radius: 50%;
        border: 2px solid white;
    }

    .header-right {
        text-align: right;
        font-size: 13px;
        line-height: 1.4;
    }

    .header-right span {
        font-weight: bold;
        color: #ffd700;
    }

    .main {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 30px;
        gap: 20px;
    }
    .question-box, .compiler-box {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .question-box { width: 50%; min-width: 320px; }
    .compiler-box { width: 45%; min-width: 320px; }
    h2 { margin-bottom: 10px; color: #333; }
    h3 { margin-top: 15px; }
    ul { margin: 10px 0 0 20px; }
    li { margin-bottom: 5px; }
    textarea {
        width: 100%;
        height: 220px;
        font-family: monospace;
        font-size: 14px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
    }
    button {
        margin-top: 10px;
        padding: 10px 18px;
        border: none;
        border-radius: 8px;
        background: #007bff;
        color: white;
        cursor: pointer;
        font-size: 15px;
    }
    button:hover { background: #0056b3; }
    .run-btn { background: #28a745; }
    .run-btn:hover { background: #218838; }
    .result {
        margin-top: 15px;
        background: #eef;
        padding: 10px;
        border-radius: 8px;
        font-size: 15px;
    }
    iframe {
        width: 100%;
        height: 220px;
        margin-top: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
    }
    .score {
        font-weight: bold;
        margin-top: 10px;
    }

    @media (max-width: 900px) {
        .main {
            flex-direction: column;
        }
        .question-box, .compiler-box {
            width: 90%;
            margin: 0 auto;
        }
    }
</style>
<script>
function runCode() {
    const code = document.getElementById("code").value;
    const iframe = document.getElementById("outputFrame");
    iframe.srcdoc = code;
}
</script>
</head>
<body>

<header>
    <div class="header-left">
        <img src="logo_k.jpg" alt="Logo">
        <div>Web Technology – Coding Exam</div>
    </div>
    <div class="header-right">
        Name: <span><?php echo htmlspecialchars($student_name); ?></span><br>
        Roll No: <span><?php echo htmlspecialchars($student_roll); ?></span><br>
        Department: <span><?php echo htmlspecialchars($student_dept); ?></span>
    </div>
</header>

<div class="main">
    <div class="question-box">
        <?php if (isset($finished) && $finished): ?>
            <h2>🎯 Exam Completed!</h2>
            <p class="score">Your Total Score: <b><?php echo $_SESSION['score']; ?> / 50</b></p>
            <p>Your result has been saved.</p>
            <form method="post">
                <button type="submit" name="restart">Re-Attempt (Reset Exam)</button>
            </form>
        <?php else: ?>
            <?php $q = $questions[$_SESSION['index']]; ?>
            <h2><?php echo $q['topic']; ?> (<?php echo $q['marks']; ?> Marks)</h2>
            <p><b>Problem:</b> <?php echo $q['question']; ?></p>
            <h3>Explanation:</h3>
            <p><?php echo $q['explanation']; ?></p>
            <h3>Test Cases (<?php echo $q['marks']; ?> marks total)</h3>
            <ul>
                <?php $marks_per_case = $q['marks'] / count($q['test_cases']);
                foreach ($q['test_cases'] as $case): ?>
                    <li><?php echo $case; ?> — <i><?php echo $marks_per_case; ?> marks</i></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <?php if (!isset($finished)): ?>
    <div class="compiler-box">
        <form method="post">
            <label><b>Write your code below:</b></label>
            <textarea id="code" name="answer" placeholder="Type your code here..." required></textarea><br>
            <button type="button" class="run-btn" onclick="runCode()">Run Code</button>
            <button type="submit" name="submit">Submit Code</button>
            <?php if (isset($feedback)): ?>
                <div class="result">
                    <b>Results:</b><br>
                    <?php foreach ($feedback as $f) echo $f . "<br>"; ?>
                    <p class="score">Score for this question: <?php echo $earned; ?> / <?php echo $current['marks']; ?></p>
                    <p>Total Score: <?php echo $_SESSION['score']; ?> / 50</p>
                    <button type="submit" name="next">
                        <?php echo ($_SESSION['index'] + 1 == count($questions)) ? 'Finish Exam ✅' : 'Next Question ➡'; ?>
                    </button>
                </div>
            <?php endif; ?>
        </form>
        <iframe id="outputFrame"></iframe>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
