<?php
session_start();

/*
|--------------------------------------------------------------------------
| SchoolERP Login Page
|--------------------------------------------------------------------------
| Frontend only.
| Authentication logic is handled by authenticate.php
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errorMessage = '';

if (isset($_SESSION['login_error'])) {
    $errorMessage = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SchoolERP | Login</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
  :root{
    --navy:#0F2A4A;
    --blue:#1E56A0;
    --sky:#4C8FE0;
    --sky-light:#EAF2FD;
    --paper:#FFFFFF;
    --mist:#F4F7FB;
    --ink-soft:#5B6B82;
    --danger:#D64550;
  }

  html,body{height:100%;}
  body{
    font-family:'Inter',sans-serif;
    background:var(--mist);
    color:var(--navy);
  }

  .stage{
    min-height:100vh;
    display:flex;
    align-items:stretch;
  }

  /* ---------- Left brand panel ---------- */
  .brand-panel{
    position:relative;
    background:linear-gradient(160deg,var(--navy) 0%, var(--blue) 62%, var(--sky) 130%);
    color:#fff;
    overflow:hidden;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    padding:3rem 3rem;
  }

  /* Signature element: a "campus ledger" grid of faint horizontal rules + a
     roll-call style dotted timeline, evoking an attendance register / class schedule */
  .ledger-lines{
    position:absolute;
    inset:0;
    background-image:repeating-linear-gradient(
      to bottom,
      rgba(255,255,255,0.06) 0px,
      rgba(255,255,255,0.06) 1px,
      transparent 1px,
      transparent 64px
    );
    pointer-events:none;
  }

  .brand-panel::after{
    content:"";
    position:absolute;
    width:520px;
    height:520px;
    border-radius:50%;
    background:radial-gradient(circle at 30% 30%, rgba(255,255,255,0.14), transparent 70%);
    top:-140px;
    right:-160px;
  }

  .brand-mark{
    display:flex;
    align-items:center;
    gap:.9rem;
    position:relative;
    z-index:2;
  }

  .logo-placeholder{
    width:52px;
    height:52px;
    border-radius:14px;
    background:rgba(255,255,255,0.14);
    border:1.5px solid rgba(255,255,255,0.35);
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:'Poppins',sans-serif;
    font-weight:700;
    font-size:1.05rem;
    letter-spacing:.02em;
    flex-shrink:0;
  }

  .brand-name{
    font-family:'Poppins',sans-serif;
    font-weight:600;
    font-size:1.15rem;
    line-height:1.2;
  }
  .brand-name small{
    display:block;
    font-family:'Inter',sans-serif;
    font-weight:400;
    font-size:.75rem;
    color:rgba(255,255,255,0.7);
    letter-spacing:.04em;
    text-transform:uppercase;
    margin-top:2px;
  }

  .brand-copy{
    position:relative;
    z-index:2;
    max-width:380px;
  }
  .brand-copy h1{
    font-family:'Poppins',sans-serif;
    font-weight:600;
    font-size:1.9rem;
    line-height:1.3;
    margin-bottom:.9rem;
  }
  .brand-copy p{
    color:rgba(255,255,255,0.78);
    font-size:.95rem;
    line-height:1.6;
  }

  .roll-call{
    position:relative;
    z-index:2;
    display:flex;
    gap:1.5rem;
    font-size:.8rem;
    color:rgba(255,255,255,0.65);
    border-top:1px solid rgba(255,255,255,0.18);
    padding-top:1.1rem;
  }
  .roll-call span b{
    display:block;
    font-family:'Poppins',sans-serif;
    font-size:1.25rem;
    color:#fff;
    font-weight:600;
  }

  /* ---------- Right form panel ---------- */
  .form-panel{
    background:var(--paper);
    display:flex;
    align-items:center;
    justify-content:center;
    padding:2.5rem 1.5rem;
  }

  .form-card{
    width:100%;
    max-width:400px;
  }

  .form-eyebrow{
    font-size:.78rem;
    font-weight:600;
    letter-spacing:.08em;
    text-transform:uppercase;
    color:var(--blue);
    margin-bottom:.4rem;
  }

  .form-card h2{
    font-family:'Poppins',sans-serif;
    font-weight:600;
    font-size:1.6rem;
    color:var(--navy);
    margin-bottom:1.6rem;
  }

  .form-label{
    font-size:.85rem;
    font-weight:600;
    color:var(--navy);
  }

  .form-control{
    border:1.5px solid #DCE4F0;
    border-radius:10px;
    padding:.65rem .9rem;
    font-size:.95rem;
    background:var(--mist);
  }
  .form-control:focus{
    border-color:var(--sky);
    box-shadow:0 0 0 .2rem rgba(76,143,224,0.18);
    background:#fff;
  }
  .form-control.is-invalid{
    border-color:var(--danger);
    background-image:none;
  }

  .input-group .form-control{
    border-right:none;
  }
  .input-group .btn-toggle-pass{
    border:1.5px solid #DCE4F0;
    border-left:none;
    background:var(--mist);
    color:var(--ink-soft);
    border-radius:0 10px 10px 0;
  }
  .input-group:focus-within .form-control,
  .input-group:focus-within .btn-toggle-pass{
    border-color:var(--sky);
    background:#fff;
  }
  .input-group:focus-within .form-control{
    box-shadow:0 0 0 .2rem rgba(76,143,224,0.18);
  }

  .invalid-feedback{
    font-size:.8rem;
  }

  .form-check-label{
    font-size:.88rem;
    color:var(--ink-soft);
  }

  .link-forgot{
    font-size:.85rem;
    font-weight:500;
    color:var(--blue);
    text-decoration:none;
  }
  .link-forgot:hover{
    color:var(--navy);
    text-decoration:underline;
  }

  .btn-login{
    background:var(--blue);
    border:none;
    color:#fff;
    font-weight:600;
    padding:.7rem 1rem;
    border-radius:10px;
    font-size:.95rem;
    transition:background .15s ease, transform .1s ease;
  }
  .btn-login:hover{ background:var(--navy); color:#fff; }
  .btn-login:active{ transform:translateY(1px); }
  .btn-login:disabled{ opacity:.7; }

  .divider-note{
    font-size:.8rem;
    color:var(--ink-soft);
    text-align:center;
    margin-top:1.6rem;
  }

  .alert-banner{
    font-size:.88rem;
    border-radius:10px;
    border:1px solid #F3C4C8;
    background:#FDEEEF;
    color:var(--danger);
  }

  @media (max-width: 991.98px){
    .brand-panel{ display:none; }
    .form-panel{ background:var(--mist); }
    .form-card{
      background:#fff;
      padding:2rem 1.75rem;
      border-radius:16px;
      box-shadow:0 10px 30px rgba(15,42,74,0.08);
    }
  }
</style>
</head>
<body>

<div class="stage">

  <!-- Brand / left panel -->
  <div class="brand-panel col-lg-6 d-none d-lg-flex">
    <div class="ledger-lines"></div>

    <div class="brand-mark">
      <div class="logo-placeholder">
    <img
        src="../assets/images/logo.png"
        alt="SchoolERP Logo"
        class="img-fluid"
        style="max-width:40px;">
</div>
      <div class="brand-name">
        SchoolERP
        <small>School Management System</small>
      </div>
    </div>

    <div class="brand-copy">
      <h1>One portal for the whole school day.</h1>
      <p>Attendance, grades, timetables, and messages — sign in to pick up right where you left off.</p>
    </div>

    <div class="roll-call">
      <span><b>12,400+</b>Students managed</span>
      <span><b>340</b>Staff accounts</span>
      <span><b>99.9%</b>Uptime</span>
    </div>
  </div>

  <!-- Form / right panel -->
  <div class="form-panel col-lg-6 col-12">
    <div class="form-card">

      <!-- Mobile-only brand mark -->
      <div class="d-lg-none d-flex align-items-center gap-2 mb-4">
        <div class="logo-placeholder">
          <img
            src="../assets/images/logo.png"
            alt="SchoolERP Logo"
            class="img-fluid"
            style="max-width:40px;">
        </div>
        <div class="brand-name" style="color:var(--navy);">
          SchoolERP
          <small style="color:var(--ink-soft);">School Management System</small>
        </div>
      </div>

      <div class="form-eyebrow">Welcome back</div>
      <h2>Sign in to your account</h2>

      <?php if ($errorMessage !== ''): ?>
      <div class="alert alert-banner py-2 px-3 mb-3" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i><?php echo $errorMessage; ?>
      </div>
      <?php endif; ?>

      <form id="loginForm" action="authenticate.php" method="POST" novalidate>
        <input
    type="hidden"
    name="csrf_token"
    value="<?= $_SESSION['csrf_token']; ?>">

        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input
            type="email"
            autofocus
            class="form-control"
            id="email"
            name="email"
            placeholder="Enter your email"
            autocomplete="username"
            required
          >
          <div class="invalid-feedback" id="emailError">Enter a valid email address.</div>
        </div>

        <div class="mb-2">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <input
              type="password"
              class="form-control"
              id="password"
              name="password"
              placeholder="Password"
              autocomplete="current-password"
              minlength="6"
              required
            >
            <button class="btn btn-toggle-pass" type="button" id="togglePassword" aria-label="Show password" aria-pressed="false">
              <i class="bi bi-eye" id="toggleIcon"></i>
            </button>
            <div class="invalid-feedback" id="passwordError">Password must be at least 6 characters.</div>
          </div> 

           <!-- Caps Lock Warning -->
  <div id="capsWarning"
       class="text-warning small mt-2"
       style="display: none;">
      <i class="bi bi-exclamation-triangle-fill"></i>
      Caps Lock is ON
  </div>
        </div>

        <div class="d-flex justify-content-between align-items-center my-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember_me">
            <label class="form-check-label" for="rememberMe">Remember me</label>
          </div>
          <a href="forgot-password.php" class="link-forgot">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-login w-100" id="submitBtn">
          Sign In
        </button>

        <p class="divider-note">Trouble signing in? Contact your school administrator.</p>

        <div class="text-center mt-4">

    <small class="text-muted">

        © <?php echo date('Y'); ?>

        JoeyTech SchoolERP

    </small>

</div>
      </form>

    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
  'use strict';

  const form = document.getElementById('loginForm');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const togglePassword = document.getElementById('togglePassword');
  const toggleIcon = document.getElementById('toggleIcon');
  const submitBtn = document.getElementById('submitBtn');

  // Show / hide password
  togglePassword.addEventListener('click', function () {
    const isHidden = passwordInput.type === 'password';
    passwordInput.type = isHidden ? 'text' : 'password';
    toggleIcon.classList.toggle('bi-eye');
    toggleIcon.classList.toggle('bi-eye-slash');
    togglePassword.setAttribute('aria-pressed', String(isHidden));
    togglePassword.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
  });

  function validateEmail () {
    const value = emailInput.value.trim();
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const valid = pattern.test(value);
    emailInput.classList.toggle('is-invalid', !valid);
    return valid;
  }

  function validatePassword () {
    const value = passwordInput.value;
    const valid = value.length >= 6;
    passwordInput.classList.toggle('is-invalid', !valid);
    return valid;
  }

  emailInput.addEventListener('input', function () {
    if (emailInput.classList.contains('is-invalid')) validateEmail();
  });
  passwordInput.addEventListener('input', function () {
    if (passwordInput.classList.contains('is-invalid')) validatePassword();
  });

  form.addEventListener('submit', function (event) {
    const emailValid = validateEmail();
    const passwordValid = validatePassword();

    if (!emailValid || !passwordValid) {
      event.preventDefault();
      const firstInvalid = !emailValid ? emailInput : passwordInput;
      firstInvalid.focus();
      return;
    }

    // Prevent duplicate submissions while the request is in flight
    submitBtn.disabled = true;
submitBtn.classList.add("disabled");

submitBtn.innerHTML =
'<span class="spinner-border spinner-border-sm me-2"></span>Signing in...';
  });

    // Detect Caps Lock
function checkCapsLock(event) {

    const warning = document.getElementById("capsWarning");

    if (event.getModifierState("CapsLock")) {
        warning.style.display = "block";
    } else {
        warning.style.display = "none";
    }

}

passwordInput.addEventListener("keydown", checkCapsLock);
passwordInput.addEventListener("keyup", checkCapsLock);

})();
</script>

</body>
</html>
