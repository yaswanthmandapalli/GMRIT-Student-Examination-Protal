<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Portal|GMRIT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    
    body {
      font-family: 'Poppins', sans-serif;
      color: #fff;
      background: url('gmrit_k.jpg') no-repeat center center/cover;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      position: relative;
      user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    }
    .overlay {
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.6);
      z-index: 0;
    }
    .navbar {
      background: rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(10px);
      z-index: 2;
    }
    .navbar-brand {
      font-weight: 700;
      font-size: 1.6rem;
      color: #fff !important;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .navbar-brand img {
      height: 40px; width: 40px; border-radius: 50%;
    }
    .nav-link {
      color: #f8f9fa !important;
      font-weight: 500;
      margin: 0 10px;
      transition: 0.3s;
    }
    .nav-link:hover {color: #ffc107 !important; transform: translateY(-2px);}
    .hero {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      text-align: center;
      padding: 80px 20px;
      z-index: 1;
      position: relative;
    }
    .hero h1 {
      font-size: 2.8rem;
      font-weight: 700;
      background: linear-gradient(to right, #ffffff, #ffd700);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .hero p {
      font-size: 1.2rem;
      margin-top: 10px;
      color: #f0f0f0;
    }
    .typing-text {color: #ffc107; font-weight: 600;}
    .btn-main {
      background-color: #ffc107;
      color: #000;
      border-radius: 50px;
      padding: 12px 30px;
      font-weight: 600;
      margin: 15px 10px;
      transition: all 0.3s ease;
      border: none;
    }
    .btn-main:hover {
      background-color: #ffca2c;
      transform: translateY(-4px);
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    footer {
      text-align: center;
      padding: 10px 0;
      background: rgba(0, 0, 0, 0.6);
      color: #f8f9fa;
      font-size: 0.9rem;
      z-index: 2;
    }
    @media (max-width: 768px) {
      .hero h1 { font-size: 2rem; }
      .hero p { font-size: 1rem; }
      .btn-main { display: block; width: 80%; margin: 10px auto; }
    }
  </style>
</head>
<body>
  <div class="overlay"></div>
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img src="logo_k.jpg" alt="Logo"> GMRIT Student Portal
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a href="admin_registration_k.php" class="nav-link active">Admin Login</a></li>
          <li class="nav-item"><a href="registration_k.php" class="nav-link">Register</a></li>
          <li class="nav-item"><a href="login_k.php" class="nav-link">Login</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <section class="hero">  
    <h1>Welcome to the Student Portal of GMRIT</h1>
    <p>Start strong your future with GMRIT through <span class="typing-text" id="typing"></span></p>
    <div>
      <a href="registration_k.php" class="btn btn-main">Register</a>
      <a href="login_k.php" class="btn btn-main">Login</a>
      <a href="conditions_k.php" class="btn btn-main">Conditions</a>
    </div>
  </section>
  <footer>
  <div class="d-flex flex-column align-items-center justify-content-center">
    
    <p style="margin:0; color:#f8f9fa;">
      <img src="logo_k.jpg" alt="Logo" style="height:50px; width:50px; border-radius:50%; margin-bottom:8px;">
      <strong>GMRIT 2025 Student Portal</strong> | Designed by <b>YASWANTH</b>
    </p>
  </div>
</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const words = ["Innovation", "Learning", "Technology", "Excellence"];
    let i = 0;
    function typingEffect() {
      const typingElement = document.getElementById("typing");
      let word = words[i].split("");
      typingElement.innerHTML = "";
      function loopTyping() {
        if (word.length > 0) {
          typingElement.innerHTML += word.shift();
          setTimeout(loopTyping, 150);
        } else setTimeout(erasingEffect, 1200);
      }
      loopTyping();
    }
    function erasingEffect() {
      const typingElement = document.getElementById("typing");
      let word = typingElement.innerHTML;
      if (word.length > 0) {
        typingElement.innerHTML = word.slice(0, -1);
        setTimeout(erasingEffect, 100);
      } else {
        i = (i + 1) % words.length;
        typingEffect();
      }
    }

    typingEffect();
  </script>
</body>
</html>
