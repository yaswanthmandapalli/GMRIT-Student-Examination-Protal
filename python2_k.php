<?php
$conn = mysqli_connect("localhost","root","","student_db");
if(!$conn){
    die("❌ Database Connection Failed");
}

// Redirect to page only if experiment exists in table
if(isset($_POST['experiment_no'])){
    $exp_num = (int)$_POST['experiment_no'];

    $check = mysqli_query($conn,"SELECT * FROM experiment_clicks WHERE experiment_no='$exp_num'");
    if(mysqli_num_rows($check) > 0){
        if($exp_num == 0){
            header("Location: basic_k.php");
        } else {
            header("Location: exp-".$exp_num."_k.php");
        }
        exit;
    } else {
        echo "<script>alert('❌ You do not have access for experiment number $exp_num');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Student Experiment List</title>
<style>
body{background:#e9eaec;font-family:Arial;padding:30px;}
h2{text-align:center;font-size:32px;margin-bottom:25px;font-weight:bold;}
.bar{
    background:#333;
    color:white;
    padding:15px;
    margin:10px 0;
    border-radius:7px;
    font-size:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.btn{
    border:none;
    padding:10px 22px;
    border-radius:6px;
    font-size:15px;
    cursor:pointer;
    font-weight:bold;
    color:white;
}
.green{background:#2ecc71;}
.red{background:#e74c3c;}
.green:hover{background:#27ae60;}
.red:hover{background:#c0392b;}
</style>
</head>
<body>

<h2>STUDENT EXPERIMENT LIST</h2>

<!-- BASIC WEB LAB -->
<form method="POST">
    <div class="bar">
        Basic Experiment of Web Technology
        <input type="hidden" name="experiment_no" value="0">
        <?php 
        $check = mysqli_query($conn,"SELECT * FROM experiment_clicks WHERE experiment_no='0'");
        if(mysqli_num_rows($check) > 0){
            // Green button with experiment number
            echo '<button class="btn green" type="submit">Access to experiment Basic codes</button>';
        } else {
            // Red button
            echo '<button class="btn red" type="submit">No Access</button>';
        }
        ?>
    </div>
</form>

<!-- EXPERIMENT 1 TO 16 -->
<?php for($i=1; $i<=16; $i++){ ?>
<form method="POST">
    <div class="bar">
        Experiment - <?php echo $i; ?>
        <input type="hidden" name="experiment_no" value="<?php echo $i; ?>">

        <?php 
        $check = mysqli_query($conn,"SELECT * FROM experiment_clicks WHERE experiment_no='$i'");
        if(mysqli_num_rows($check) > 0){
            // Green button with experiment number
            echo '<button class="btn green" type="submit">Access to experiment '.$i.'</button>';
        } else {
            // Red button
            echo '<button class="btn red" type="submit">No Access</button>';
        }
        ?>
    </div>
</form>
<?php } ?>

</body>
</html>
