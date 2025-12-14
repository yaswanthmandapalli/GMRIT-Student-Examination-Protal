<?php
$host="localhost";$username="root";$password="";$dbname="student_db";
$conn=new mysqli($host,$username,$password,$dbname);
if($conn->connect_error){die("DB Connection Failed");}
$message="";
if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["experiment_no"])){
    $experiment_no=(int)$_POST["experiment_no"];
    $check=$conn->query("SELECT * FROM experiment_clicks WHERE experiment_no=$experiment_no");
    if($check->num_rows>0){
        $conn->query("DELETE FROM experiment_clicks WHERE experiment_no=$experiment_no");
        $message="🗑 Experiment $experiment_no Removed!";
    }else{
        $conn->query("INSERT INTO experiment_clicks (experiment_no) VALUES ($experiment_no)");
        $message="✔ Experiment $experiment_no Added!";
    }
}
$saved=[];
$result=$conn->query("SELECT experiment_no FROM experiment_clicks");
while($row=$result->fetch_assoc()){$saved[]=$row["experiment_no"];}
?>
<!DOCTYPE html>
<html>
<head>
<title>Practical Experiment List</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
:root{--green:#1b5e20;--accent:#43a047;--light:#e8f5e9;--white:#ffffff;--danger:#c62828}
*{box-sizing:border-box}body{font-family:Arial,Helvetica,sans-serif;background:linear-gradient(90deg,#c8e6c9,#a5d6a7);margin:0;padding:0;min-height:100vh;animation:pageFade .6s ease both}
@keyframes pageFade{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
.navbar{width:100%;background:var(--green);padding:14px 20px;display:flex;justify-content:space-between;align-items:center;color:var(--white);font-weight:700;position:sticky;top:0;z-index:10;box-shadow:0 2px 0 rgba(0,0,0,.06);transform:translateY(-8px);opacity:0;animation:navIn .6s .05s ease forwards}
@keyframes navIn{to{transform:none;opacity:1}}
.navbar h1{margin:0;font-size:20px;letter-spacing:.2px}
.nav-btn{background:var(--white);color:var(--green);padding:9px 14px;border:none;border-radius:8px;font-size:14px;cursor:pointer;font-weight:700;transition:transform .18s ease,box-shadow .18s ease}
.nav-btn:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.12);color:var(--white);background:transparent;border:2px solid var(--white)}
.container{padding:22px 12px 60px}
h2{text-align:center;font-size:28px;margin:16px 0;color:var(--green);font-weight:800;letter-spacing:.4px}
.msg{max-width:720px;margin:12px auto 18px;padding:10px 14px;border-radius:10px;background:var(--white);font-size:16px;font-weight:700;text-align:center;opacity:0;transform:translateY(-6px);animation:msgIn .45s .15s ease forwards}
@keyframes msgIn{to{opacity:1;transform:none}}
@keyframes msgOut{to{opacity:0;transform:translateY(-8px)}}
.bar{width:90%;max-width:900px;background:var(--accent);color:var(--white);padding:16px;margin:12px auto;border-radius:12px;display:flex;justify-content:space-between;align-items:center;font-size:18px;font-weight:700;opacity:0;transform:translateY(12px);box-shadow:0 6px 18px rgba(0,0,0,.08)}
.bar.show{animation:barIn .48s cubic-bezier(.2,.85,.32,1) forwards}
@keyframes barIn{to{opacity:1;transform:none}}
button{padding:10px 18px;font-size:15px;border-radius:8px;border:none;cursor:pointer;font-weight:800;transition:transform .16s ease,box-shadow .16s ease}
.btn-green{background:var(--white);color:var(--green)}
.btn-green:hover{transform:translateY(-4px);box-shadow:0 10px 30px rgba(27,94,32,.18);color:var(--white);background:transparent;border:2px solid var(--white)}
.btn-red{background:var(--danger);color:var(--white)}
.btn-red:hover{transform:translateY(-4px);box-shadow:0 10px 30px rgba(198,40,40,.18)}
@keyframes pulse{0%{transform:scale(1)}50%{transform:scale(1.04)}100%{transform:scale(1)}}
.bar.added{animation:barPulse 800ms ease 1}
@keyframes barPulse{0%{box-shadow:0 6px 18px rgba(0,0,0,.08)}50%{transform:translateY(-6px);box-shadow:0 20px 40px rgba(27,94,32,.12)}100%{transform:none}}
@media (max-width:640px){.navbar h1{font-size:16px}.nav-btn{padding:8px 12px;font-size:13px}.bar{padding:12px;font-size:16px}}
</style>
</head>
<body>
<div class="navbar">
    <h1>Practical Experiment List</h1>
    <a href="verify_student_k.php"><button class="nav-btn">Access Verification of Student</button></a>
</div>
<div class="container">
<h2>Practical Experiment List</h2>
<?php if(!empty($message)){echo"<div class='msg' id='msg'>".$message."</div>";} ?>
<div class="bar" data-index="0">
  Basic Experiment
  <form method="POST">
    <input type="hidden" name="experiment_no" value="0">
    <button class="<?php echo in_array(0,$saved)?'btn-red':'btn-green'; ?>" type="submit">
      <?php echo in_array(0,$saved)?"Remove Access of Basic codes":"Access to Basic Experiments"; ?>
    </button>
  </form>
</div>
<?php for($i=1;$i<=16;$i++){ ?>
<div class="bar" data-index="<?php echo $i;?>">
  Experiment - <?php echo $i; ?>
  <form method="POST">
    <input type="hidden" name="experiment_no" value="<?php echo $i; ?>">
    <button class="<?php echo in_array($i,$saved)?'btn-red':'btn-green'; ?>" type="submit">
      <?php echo in_array($i,$saved)?"Remove Access of Experiment $i":"Access to Experiment $i"; ?>
    </button>
  </form>
</div>
<?php } ?>
</div>
<script>
document.addEventListener("DOMContentLoaded",function(){
  var bars=document.querySelectorAll(".bar");
  bars.forEach(function(b,i){setTimeout(function(){b.classList.add("show")},80*i+120)});
  var msg=document.getElementById("msg");
  if(msg){setTimeout(function(){msg.style.animation="msgOut .45s ease forwards";setTimeout(function(){msg.remove()},500)},3000)}
});
</script>
</body>
</html>
