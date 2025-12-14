<?php
session_start();

// 🔒 Ensure student is logged in (email set in exam_k.php / login_k.php)
if (!isset($_SESSION['email'])) {
    header("Location: login_k.php");
    exit();
}

// 🔹 Get student details from session (set in exam_k.php)
$student_name = $_SESSION['student_name']       ?? 'Unknown';
$student_roll = $_SESSION['student_rollno']     ?? 'Unknown';
$student_dept = $_SESSION['student_department'] ?? 'Unknown';

// === PYTHON QUESTION BANK ===
$questions = [
    [
        "topic" => "Python Basics",
        "question" => "Write a Python program to print 'Hello, World!'.",
        "explanation" => "Tests understanding of print statement syntax and Python indentation.",
        "test_cases" => [
            "uses print",
            "contains 'Hello, World!'",
            "correct syntax",
            "no runtime errors"
        ],
        "marks" => 10
    ],
    [
        "topic" => "Python Loops",
        "question" => "Write a Python program to print the first 10 natural numbers using a for loop.",
        "explanation" => "Tests knowledge of loops, range(), and printing values in Python.",
        "test_cases" => [
            "uses for",
            "uses range",
            "prints 1 to 10",
            "no syntax errors"
        ],
        "marks" => 10
    ],
    [
        "topic" => "Python Functions",
        "question" => "Write a Python function that takes two numbers as input and returns their sum.",
        "explanation" => "Tests your understanding of defining functions and returning values.",
        "test_cases" => [
            "uses def",
            "takes two parameters",
            "uses return",
            "returns correct sum"
        ],
        "marks" => 10
    ],
    [
        "topic" => "Python Conditionals",
        "question" => "Write a Python program that checks if a number is even or odd.",
        "explanation" => "Tests conditional statements and modulo operator usage.",
        "test_cases" => [
            "uses if",
            "uses %",
            "prints even or odd correctly",
            "no syntax errors"
        ],
        "marks" => 10
    ],
    [
        "topic" => "Python Lists",
        "question" => "Write a Python program to find the largest number in a list.",
        "explanation" => "Tests use of loops, conditionals, or built-in functions like max().",
        "test_cases" => [
            "uses list",
            "uses max or loop",
            "prints largest number",
            "no syntax errors"
        ],
        "marks" => 10
    ]
];

// === Initialize ===
if (!isset($_SESSION['py_index'])) {      // use separate keys for python exam
    $_SESSION['py_index'] = 0;
    $_SESSION['py_score'] = 0;
}

// === Handle Submission ===
if (isset($_POST['submit'])) {
    $answer = strtolower(trim($_POST['answer']));
    $current = $questions[$_SESSION['py_index']];
    $marks_per_case = $current['marks'] / count($current['test_cases']);
    $earned = 0;
    $results = [];

    // improved keyword-based test detection
    foreach ($current['test_cases'] as $case) {
        $keywords = [];

        if (strpos($case, "print") !== false) $keywords[] = "print";
        if (strpos($case, "for") !== false) $keywords[] = "for";
        if (strpos($case, "range") !== false) $keywords[] = "range";
        if (strpos($case, "def") !== false) $keywords[] = "def";
        if (strpos($case, "return") !== false) $keywords[] = "return";
        if (strpos($case, "%") !== false) $keywords[] = "%";
        if (strpos($case, "list") !== false) $keywords[] = "[";
        if (strpos($case, "max") !== false) $keywords[] = "max";
        if (strpos($case, "if") !== false) $keywords[] = "if";

        $passed = false;
        foreach ($keywords as $kw) {
            if (strpos($answer, $kw) !== false) {
                $passed = true;
                break;
            }
        }

        if ($passed) {
            $earned += $marks_per_case;
            $results[] = "✅ Passed: $case (+$marks_per_case marks)";
        } else {
            $results[] = "❌ Failed: $case (0 marks)";
        }
    }

    $_SESSION['py_score'] += $earned;
    $feedback = $results;
}

if (isset($_POST['next'])) {
    $_SESSION['py_index']++;
    if ($_SESSION['py_index'] >= count($questions)) {
        $finished = true;
    }
}

if (isset($_POST['restart'])) {
    // only reset python exam variables, not login
    $_SESSION['py_index'] = 0;
    $_SESSION['py_score'] = 0;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Python Coding Practice Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/skulpt@1.2.0/dist/skulpt.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/skulpt@1.2.0/dist/skulpt-stdlib.js"></script>
<style>
body{
    font-family:"Segoe UI",sans-serif;
    background:#f0f2f5;
    margin:0
}

/* 🔹 Navbar with student details on the right side */
header{
    background:#1f1f1f;
    color:#fff;
    padding:12px 24px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 10px rgba(0,0,0,0.3);
}

.header-left{
    font-size:20px;
    font-weight:bold;
    display:flex;
    align-items:center;
    gap:8px;
}

.header-right{
    text-align:right;
    font-size:13px;
    line-height:1.4;
}

.header-right span{
    font-weight:bold;
    color:#ffd700;
}

.main{
    display:flex;
    justify-content:center;
    align-items:flex-start;
    padding:30px;
    gap:20px
}
.question-box,.compiler-box{
    background:#fff;
    padding:25px;
    border-radius:10px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1)
}
.question-box{width:50%}
.compiler-box{width:45%}
textarea{
    width:100%;
    height:220px;
    font-family:monospace;
    font-size:14px;
    padding:10px;
    border:1px solid #ccc;
    border-radius:8px
}
button{
    margin-top:10px;
    padding:10px 18px;
    border:none;
    border-radius:8px;
    background:#007bff;
    color:white;
    cursor:pointer;
    font-size:15px
}
button:hover{background:#0056b3}
.run-btn{background:#28a745}
.run-btn:hover{background:#218838}
.result{
    margin-top:15px;
    background:#eef;
    padding:10px;
    border-radius:8px;
    font-size:15px
}
.output-box{
    margin-top:10px;
    background:#111;
    color:#0f0;
    padding:10px;
    border-radius:8px;
    height:220px;
    overflow:auto
}
.score{font-weight:bold;margin-top:10px}

@media (max-width: 900px){
    .main{flex-direction:column}
    .question-box,.compiler-box{width:100%}
}
</style>
<script>
function outf(text){
    document.getElementById("output").innerHTML += text;
}
function runCode(){
    let code = document.getElementById("code").value;
    let output = document.getElementById("output");
    output.innerHTML = "";
    Sk.configure({
        output: outf,
        read: function(x){
            if (Sk.builtinFiles === undefined || Sk.builtinFiles["files"][x] === undefined)
                throw "File not found: '"+x+"'";
            return Sk.builtinFiles["files"][x];
        }
    });
    Sk.misceval.asyncToPromise(function(){
        return Sk.importMainWithBody("<stdin>", false, code, true);
    }).catch(function(err){
        output.innerHTML = err.toString();
    });
}
</script>
</head>
<body>

<header>
    <div class="header-left">
        🐍 Python Coding Practice Portal
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
            <h2>🎯 Quiz Completed!</h2>
            <p class="score">Your Total Score: <b><?php echo $_SESSION['py_score']; ?> / 50</b></p>
            <form method="post">
                <button type="submit" name="restart">Restart Quiz</button>
            </form>
        <?php else: ?>
            <?php $q = $questions[$_SESSION['py_index']]; ?>
            <h2><?php echo $q['topic']; ?> (<?php echo $q['marks']; ?> Marks)</h2>
            <p><b>Problem:</b> <?php echo $q['question']; ?></p>
            <h3>Explanation:</h3>
            <p><?php echo $q['explanation']; ?></p>
            <h3>Test Cases (<?php echo $q['marks']; ?> marks total)</h3>
            <ul>
                <?php $marks_per_case=$q['marks']/count($q['test_cases']);
                foreach($q['test_cases'] as $case): ?>
                    <li><?php echo $case; ?> — <i><?php echo $marks_per_case; ?> marks</i></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <?php if (!isset($finished)): ?>
    <div class="compiler-box">
        <form method="post">
            <label><b>Write your Python code below:</b></label>
            <textarea id="code" name="answer" placeholder="Type your Python code here..." required></textarea><br>
            <button type="button" class="run-btn" onclick="runCode()">Run Code</button>
            <button type="submit" name="submit">Submit Code</button>
            <div id="output" class="output-box"></div>
            <?php if (isset($feedback)): ?>
                <div class="result">
                    <b>Results:</b><br>
                    <?php foreach($feedback as $f) echo $f."<br>"; ?>
                    <p class="score">Score for this question: <?php echo $earned; ?> / <?php echo $current['marks']; ?></p>
                    <p class="score">Total Score: <?php echo $_SESSION['py_score']; ?> / 50</p>
                    <button type="submit" name="next">Next Question ➡️</button>
                </div>
            <?php endif; ?>
        </form>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
