<?php
session_start();

// 🔒 Ensure student is logged in (email set in exam_k.php / login_k.php)
if (!isset($_SESSION['email'])) {
    header("Location: login_k.php");
    exit();
}

// 🔹 Get student details from session (set in exam_k.php)
$name = $_SESSION['name']       ?? 'Unknown';
$roll = $_SESSION['rollno']     ?? 'Unknown';
$student_dept = $_SESSION['student_department'] ?? 'Unknown';

// --- Database configuration (edit these for your environment) ---
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';      // set your DB password
$db_name = 'student_db';

// Create DB connection (used to store submissions)
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_errno) {
    // If DB not available, we'll still allow local grading; just skip saving.
    $db_error = $mysqli->connect_error;
} else {
    $db_error = null;
    // ensure table exists (simple create-if-not-exists)
    $create_sql = "CREATE TABLE IF NOT EXISTS submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        roll VARCHAR(100),
        name VARCHAR(255),
        topic VARCHAR(255),
        earned_marks FLOAT,
        total_marks FLOAT,
        feedback TEXT,
        code LONGTEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $mysqli->query($create_sql);
}

// === Load problem from JSON if provided (optional) ===
// Provide a file 'problem.json' in same folder to override default question structure.
$problem_file = __DIR__ . '/problem.json';
if (file_exists($problem_file)) {
    $raw = file_get_contents($problem_file);
    $decoded = json_decode($raw, true);
    if (is_array($decoded) && isset($decoded['question'])) {
        $question = $decoded;
    } else {
        // fallback if JSON malformed
        // --- Updated question for: File Handling and Simple File Explorer Implementation ---
        // NOTE: this in-browser grader (Skulpt) may not support real filesystem access.
        // Therefore tasks are specified as plain-text input operations that students should
        // implement using normal Python I/O (stdin/stdout) to emulate file behavior.
        $question = [
            "topic" => "File Handling and Simple File Explorer Implementation",
            "question" => "Write a Python program that reads input from stdin. The first line is an operation token (one of: 'create', 'read', 'append', 'list'). For each token follow the input format below and produce exact output (single spaces and newlines matter). These tasks emulate common file operations so the in-browser grader (without a filesystem) can still evaluate solutions.\n\nOperations:\n\n1) 'create'\n   - Next line: filename (single token, no spaces)\n   - Next line: content (single line string)\n   - Requirement: emulate creating a file with the given content and print exactly: Created <filename>\\n\n   - Example: input: 'create\\nnotes.txt\\nHello World\\n' -> output: 'Created notes.txt\\n'\n\n2) 'read'\n   - Next line: filename\n   - Next line: content (the file's content to be read)\n   - Requirement: emulate reading the file and print the exact content line followed by a newline.\n   - Example: input: 'read\\nnotes.txt\\nHello World\\n' -> output: 'Hello World\\n'\n\n3) 'append'\n   - Next line: filename\n   - Next line: original_content (single line)\n   - Next line: append_content (single line)\n   - Requirement: emulate appending append_content to original_content and print the resulting single-line content followed by a newline. (Concatenate exactly: original_content + append_content)\n   - Example: input: 'append\\nlog.txt\\nLine1\\nLine2\\n' -> output: 'Line1Line2\\n'\n\n4) 'list'\n   - Next line: integer n (number of filenames)\n   - Next n lines: filenames (one per line)\n   - Requirement: emulate listing directory contents; print filenames sorted lexicographically (ascending), one per line, then a final newline.\n   - Example: input: 'list\\n3\\nb.txt\\na.txt\\nc.txt\\n' -> output: 'a.txt\\nb.txt\\nc.txt\\n'\n\nBe exact with spacing and trailing newlines. Students may implement these behaviors using plain Python (no real filesystem required).",
            "explanation" => "Emulates simple file operations (create/read/append/list) using deterministic input/output so the in-browser auto-grader can evaluate student solutions.",
            "test_cases" => [
                [
                    "input"  => "create\nnotes.txt\nHello World\n",
                    "output" => "Created notes.txt\n",
                    "desc"   => "Create a file named notes.txt with content 'Hello World' -> Created notes.txt",
                    "marks"  => 2.5
                ],
                [
                    "input"  => "read\nnotes.txt\nHello World\n",
                    "output" => "Hello World\n",
                    "desc"   => "Read filename notes.txt whose content is 'Hello World' -> outputs the content.",
                    "marks"  => 2.5
                ],
                [
                    "input"  => "append\nlog.txt\nLine1\nLine2\n",
                    "output" => "Line1Line2\n",
                    "desc"   => "Append 'Line2' to original 'Line1' -> output concatenated content.",
                    "marks"  => 2.5
                ],
                [
                    "input"  => "list\n3\nb.txt\na.txt\nc.txt\n",
                    "output" => "a.txt\nb.txt\nc.txt\n",
                    "desc"   => "List three files; output sorted lexicographically.",
                    "marks"  => 2.5
                ]
            ],
            "marks" => 10
        ];
    }
} else {
    // fallback same as above
    $question = [
        "topic" => "File Handling and Simple File Explorer Implementation",
        "question" => "Write a Python program that reads input from stdin. The first line is an operation token (one of: 'create', 'read', 'append', 'list'). For each token follow the input format below and produce exact output (single spaces and newlines matter). These tasks emulate common file operations so the in-browser grader (without a filesystem) can still evaluate solutions.\n\nOperations:\n\n1) 'create'\n   - Next line: filename (single token, no spaces)\n   - Next line: content (single line string)\n   - Requirement: emulate creating a file with the given content and print exactly: Created <filename>\\n\n   - Example: input: 'create\\nnotes.txt\\nHello World\\n' -> output: 'Created notes.txt\\n'\n\n2) 'read'\n   - Next line: filename\n   - Next line: content (the file's content to be read)\n   - Requirement: emulate reading the file and print the exact content line followed by a newline.\n   - Example: input: 'read\\nnotes.txt\\nHello World\\n' -> output: 'Hello World\\n'\n\n3) 'append'\n   - Next line: filename\n   - Next line: original_content (single line)\n   - Next line: append_content (single line)\n   - Requirement: emulate appending append_content to original_content and print the resulting single-line content followed by a newline. (Concatenate exactly: original_content + append_content)\n   - Example: input: 'append\\nlog.txt\\nLine1\\nLine2\\n' -> output: 'Line1Line2\\n'\n\n4) 'list'\n   - Next line: integer n (number of filenames)\n   - Next n lines: filenames (one per line)\n   - Requirement: emulate listing directory contents; print filenames sorted lexicographically (ascending), one per line, then a final newline.\n   - Example: input: 'list\\n3\\nb.txt\\na.txt\\nc.txt\\n' -> output: 'a.txt\\nb.txt\\nc.txt\\n'\n\nBe exact with spacing and trailing newlines. Students may implement these behaviors using plain Python (no real filesystem required).",
        "explanation" => "Emulates simple file operations (create/read/append/list) using deterministic input/output so the in-browser auto-grader can evaluate student solutions.",
        "test_cases" => [
            [
                "input"  => "create\nnotes.txt\nHello World\n",
                "output" => "Created notes.txt\n",
                "desc"   => "Create a file named notes.txt with content 'Hello World' -> Created notes.txt",
                "marks"  => 2.5
            ],
            [
                "input"  => "read\nnotes.txt\nHello World\n",
                "output" => "Hello World\n",
                "desc"   => "Read filename notes.txt whose content is 'Hello World' -> outputs the content.",
                "marks"  => 2.5
            ],
            [
                "input"  => "append\nlog.txt\nLine1\nLine2\n",
                "output" => "Line1Line2\n",
                "desc"   => "Append 'Line2' to original 'Line1' -> output concatenated content.",
                "marks"  => 2.5
            ],
            [
                "input"  => "list\n3\nb.txt\na.txt\nc.txt\n",
                "output" => "a.txt\nb.txt\nc.txt\n",
                "desc"   => "List three files; output sorted lexicographically.",
                "marks"  => 2.5
            ]
        ],
        "marks" => 10
    ];
}

// Ensure each test case has marks; if not, distribute evenly
$total_cases = count($question['test_cases']);
if ($total_cases > 0) {
    $sum_marks = 0;
    foreach ($question['test_cases'] as $tc) {
        if (isset($tc['marks'])) $sum_marks += $tc['marks'];
    }
    if ($sum_marks == 0) {
        $per = $question['marks'] / $total_cases;
        for ($i = 0; $i < $total_cases; $i++) {
            $question['test_cases'][$i]['marks'] = $per;
        }
    }
}

// === Handle server POST (after client-side grading) ===
$server_feedback = [];
$earned = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Expected fields populated by client: code, earned, feedback_json
    $code_post = $_POST['answer'] ?? '';
    // If earned was calculated client-side, accept it; otherwise fallback to keyword grading
    if (isset($_POST['earned'])) {
        $earned = floatval($_POST['earned']);
        $feedback_json = $_POST['feedback_json'] ?? '[]';
        $server_feedback = json_decode($feedback_json, true);
        if (!is_array($server_feedback)) $server_feedback = [];
    } else {
        // Fallback: naive server-side keyword checks (old behavior)
        $answer_raw = $code_post;
        $answer = strtolower(trim($answer_raw));
        $marks_per_case = $question['marks'] / max(1,$total_cases);
        $earned = 0;
        $results = [];
        foreach ($question['test_cases'] as $case) {
            $desc = $case['desc'] ?? '';
            $passed = false;
            // loose heuristics to avoid zero-credit if client grading unavailable
            if (strpos($desc, "create") !== false) {
                if (strpos($answer, 'create') !== false || strpos($answer, 'created') !== false) $passed = true;
            } elseif (strpos($desc, "read") !== false) {
                if (strpos($answer, 'read') !== false || strpos($answer, 'print') !== false) $passed = true;
            } elseif (strpos($desc, "append") !== false) {
                if (strpos($answer, 'append') !== false || strpos($answer, '+') !== false || strpos($answer, 'line') !== false) $passed = true;
            } elseif (strpos($desc, "list") !== false) {
                if (strpos($answer, 'sort') !== false || strpos($answer, 'list') !== false) $passed = true;
            } else {
                if (strpos($answer, 'print') !== false) $passed = true;
            }
            if ($passed) {
                $earned += $marks_per_case;
                $results[] = ["desc"=>$desc, "ok"=>true, "marks"=>$marks_per_case];
            } else {
                $results[] = ["desc"=>$desc, "ok"=>false, "marks"=>0];
            }
        }
        $server_feedback = $results;
    }

    // Save submission to DB if DB available
    if ($mysqli && !$db_error) {
        $stmt = $mysqli->prepare("INSERT INTO submissions (roll, name, topic, earned_marks, total_marks, feedback, code) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $feedback_serialized = json_encode($server_feedback);
            $topic = $question['topic'] ?? 'Unknown';
            $total_marks = $question['marks'] ?? 0;
            $stmt->bind_param('sssiiss', $roll, $name, $topic, $earned, $total_marks, $feedback_serialized, $code_post);
            $stmt->execute();
            $stmt->close();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Python Single Question Practice</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Skulpt -->
<script src="https://cdn.jsdelivr.net/npm/skulpt@1.2.0/dist/skulpt.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/skulpt@1.2.0/dist/skulpt-stdlib.js"></script>

<style>
body{
    font-family:"Segoe UI",sans-serif;
    background:#f0f2f5;
    margin:0;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;

}
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
.header-right span{font-weight:bold;color:#ffd700;}
.container{padding:30px;}
.card{padding:20px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.08);background:#fff}
textarea{width:100%;height:220px;font-family:monospace;font-size:14px;padding:10px;border:1px solid #ccc;border-radius:8px}
button{margin-top:10px;padding:10px 18px;border:none;border-radius:8px;background:#007bff;color:white;cursor:pointer;font-size:15px}
button:hover{background:#0056b3}
.run-btn{background:#28a745}
.run-btn:hover{background:#218838}
.output-box{margin-top:10px;background:#111;color:#0f0;padding:10px;border-radius:8px;height:220px;overflow:auto;white-space:pre-wrap}
.result{margin-top:15px;background:#eef;padding:10px;border-radius:8px;font-size:15px}
.score{font-weight:bold;margin-top:10px}
.test-case-pass{color:green}
.test-case-fail{color:red}
@media (max-width: 900px){body .container{padding:15px}}
</style>

<script>
document.addEventListener("paste", function(e) {
    e.preventDefault();
});
// --- Pass PHP test cases into JS ---
const TEST_CASES = <?php echo json_encode(array_values($question['test_cases'])); ?>;
const TOTAL_MARKS = <?php echo json_encode($question['marks']); ?>;

// helper to run student code with Skulpt, supplying input and capturing output
function runSkulpt(code, inputText, timeoutMs = 2000) {
    return new Promise((resolve) => {
        let output = '';
        // create input lines
        const inputLines = inputText === null || inputText === undefined ? [] : String(inputText).split(/\r?\n/);
        let inputPtr = 0;
        function outf(text){
            output += text;
        }
        function builtinRead(x){
            if (Sk.builtinFiles === undefined || Sk.builtinFiles["files"][x] === undefined)
                throw "File not found: '"+x+"'";
            return Sk.builtinFiles["files"][x];
        }
        // input function for Python's input()
        function inputfun(prompt) {
            // ignore prompt (we're not interactive)
            if (inputPtr < inputLines.length) {
                const line = inputLines[inputPtr] + "\n";
                inputPtr++;
                return line;
            } else {
                // when no more input, return empty string
                return "";
            }
        }
        // configure Skulpt
        Sk.configure({output:outf, read:builtinRead, inputfun: inputfun, inputfunTakesPrompt: false});
        let finished = false;
        // run with timeout guard
        const timer = setTimeout(() => {
            if (!finished) {
                finished = true;
                resolve({ok:false, output: output, error: "Timeout (exceeded " + timeoutMs + "ms)"} );
            }
        }, timeoutMs);

        Sk.misceval.asyncToPromise(function() {
            return Sk.importMainWithBody("<stdin>", false, code, true);
        }).then(function(mod) {
            if (!finished) {
                finished = true;
                clearTimeout(timer);
                resolve({ok:true, output: output, error: null});
            }
        }).catch(function(err) {
            if (!finished) {
                finished = true;
                clearTimeout(timer);
                // err.toString() often contains stack; show trimmed
                resolve({ok:false, output: output, error: err.toString()});
            }
        });
    });
}

async function runSingleTest(code, tc) {
    const input = tc.input ?? "";
    const expected = tc.output ?? "";
    // run
    const res = await runSkulpt(code, input, 3000);
    let actual = res.output ?? "";
    // Normalize outputs: trim trailing spaces/newlines
    const normActual = actual.trim().replace(/\r\n/g, "\n");
    const normExpected = expected.trim().replace(/\r\n/g, "\n");
    const passed = (normActual === normExpected);
    return {
        passed: passed,
        actual: actual,
        expected: expected,
        error: res.error,
        desc: tc.desc ?? '',
        marks: (tc.marks ?? 0)
    };
}

async function runAllTestsAndShow() {
    const code = document.getElementById('code').value;
    const outBox = document.getElementById('output');
    outBox.innerText = "Running tests...\n";
    const results = [];
    let earned = 0;
    for (let i=0;i<TEST_CASES.length;i++) {
        const tc = TEST_CASES[i];
        outBox.innerText += `\nRunning Test ${i+1}: ${tc.desc ?? ''}\n`;
        const r = await runSingleTest(code, tc);
        if (r.passed) {
            outBox.innerText += `✅ Passed — +${r.marks} marks\n`;
            earned += r.marks;
        } else {
            outBox.innerText += `❌ Failed — +0 marks\n`;
            if (r.error) {
                outBox.innerText += `Error: ${r.error}\n`;
            } else {
                outBox.innerText += `Expected:\n${r.expected}\nActual:\n${r.actual}\n`;
            }
        }
        results.push(r);
    }
    // Round earned to 2 decimals
    earned = Math.round(earned * 100) / 100;
    outBox.innerText += `\nTotal Score: ${earned} / ${TOTAL_MARKS}\n`;
    return {earned, results};
}

// Called when user presses Submit (grade on client then send to server)
async function gradeAndSubmit(form) {
    // run tests
    const grader = await runAllTestsAndShow();
    const earned = grader.earned;
    const results = grader.results;

    // Build feedback JSON to send to server
    const feedback_json = JSON.stringify(results);

    // set hidden fields and submit
    // ensure hidden elements exist
    document.getElementById('earned_field').value = earned;
    document.getElementById('feedback_json_field').value = feedback_json;

    // submit the form (POST)
    form.submit();
}

// Quick run button: just run code and show stdout/errors (no grading)
function runCodeQuick() {
    const code = document.getElementById('code').value;
    const output = document.getElementById('output');
    output.innerText = "";
    // use runSkulpt for single run
    runSkulpt(code, "").then(res => {
        if (res.error) {
            output.innerText = "Error:\n" + res.error + "\n\nStdout:\n" + res.output;
        } else {
            output.innerText = res.output;
        }
    });
}
</script>
</head>
<body>

<header>
    <div class="header-left">🐍 Python Practice — Single Question</div>
    <div class="header-right">
        Name: <span><?php echo htmlspecialchars($name); ?></span><br>
        Roll No: <span><?php echo htmlspecialchars($roll); ?></span><br>
        Department: <span><?php echo htmlspecialchars($student_dept); ?></span>
    </div>
</header>

<div class="container">
    <div class="card mb-3">
        <h3><?php echo htmlspecialchars($question['topic']); ?> (<?php echo $question['marks']; ?> Marks)</h3>
        <p><b>Problem:</b> <?php echo nl2br(htmlspecialchars($question['question'])); ?></p>
        <h5>Explanation:</h5>
        <p><?php echo nl2br(htmlspecialchars($question['explanation'] ?? '')); ?></p>

        <h5>Test Cases (hidden from students; used for auto-grading)</h5>
        <ul>
            <?php foreach ($question['test_cases'] as $i => $case): ?>
                <li>Test <?php echo $i+1; ?> — <i><?php echo htmlspecialchars($case['desc'] ?? ''); ?></i> — <?php echo htmlspecialchars($case['marks']); ?> marks</li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="card">
        <!-- When user clicks submit, the JS will run tests then post fields earned & feedback_json -->
        <form method="post" onsubmit="event.preventDefault(); gradeAndSubmit(this);">
            <label><b>Write your Python code below:</b></label>
            <textarea id="code" name="answer" placeholder="Type your Python code here..." required><?php
                // keep posted answer in box after submit
                if (isset($_POST['answer'])) echo htmlspecialchars($_POST['answer']);
            ?></textarea><br>

            <button type="button" class="run-btn" onclick="runCodeQuick()">Run Code (quick)</button>
            <button type="submit" name="submit">Submit Code (grade & save)</button>
            <button type="button" onclick="window.location=window.location.pathname" style="background:#6c757d">Clear</button>

            <div id="output" class="output-box"></div>

            <!-- hidden fields populated by JS before submit -->
            <input type="hidden" id="earned_field" name="earned" value="">
            <input type="hidden" id="feedback_json_field" name="feedback_json" value="">

            <?php if (!empty($server_feedback)): ?>
                <div class="result">
                    <b>Auto-grader Results (server recorded):</b><br>
                    <?php
                        foreach ($server_feedback as $i => $f) {
                            // f may be associative array from client tests or the fallback array above
                            if (is_array($f)) {
                                $desc = $f['desc'] ?? ($f['desc'] ?? "Test ".($i+1));
                                $ok = $f['passed'] ?? ($f['ok'] ?? false);
                                $marks = $f['marks'] ?? 0;
                                if ($ok) {
                                    echo "<div class='test-case-pass'>✅ ".htmlspecialchars($desc)." (+".$marks." marks)</div>";
                                } else {
                                    echo "<div class='test-case-fail'>❌ ".htmlspecialchars($desc)." (+0 marks)</div>";
                                }
                            } else {
                                echo htmlspecialchars(json_encode($f)) . "<br>";
                            }
                        }
                    ?>
                    <p class="score">Score recorded: <?php echo htmlspecialchars($earned); ?> / <?php echo htmlspecialchars($question['marks']); ?></p>
                    <?php if ($db_error): ?>
                        <div style="color:darkorange">Note: submission not saved to DB (DB error: <?php echo htmlspecialchars($db_error); ?>)</div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </form>
    </div>
</div>

</body>
</html>
