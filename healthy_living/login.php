<?php
session_start();
include 'koneksi.php';

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_query($conn,
        "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($query) > 0){

        $user = mysqli_fetch_assoc($query);

        if(password_verify($password, $user['password'])){

            // simpan session
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama'] = $user['nama_pengguna'];
            $_SESSION['role'] = $user['role'];

            // 🔥 REDIRECT BERDASARKAN ROLE
            if($user['role'] == 'admin'){
                header("Location: dashboard_admin.php");
            } else {
                header("Location: dashboard_user.php");
            }
            exit;

        } else {
            $error = "Password salah!";
        }

    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log-in</title>

<style>
    :root{
        --green: #1aa260;
        --green-dark: #128a4f;
        --ink: #1f2430;
        --muted: #8a93a3;
        --line: #e6e8ec;
    }

    *{ box-sizing: border-box; }

    body{
        margin:0;
        font-family: Arial, Helvetica, sans-serif;
        background: #f4f5f7;
        min-height: 100vh;
        display:flex;
        align-items:center;
        justify-content:center;
        padding: 24px;
    }

    .wrap{
        display:flex;
        width: 100%;
        max-width: 980px;
        min-height: 460px;
        background: #fff;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(20,20,30,0.08);
    }

    .visual{
        flex: 1 1 55%;
        position: relative;
        background:
            linear-gradient(180deg, rgba(0,0,0,0.05), rgba(0,0,0,0.15)),
            url('https://images.unsplash.com/photo-1502904550040-7534597429ae?q=80&w=1200&auto=format&fit=crop')
            center/cover no-repeat;
    }

    .panel{
        flex: 1 1 45%;
        padding: 48px 50px;
        display:flex;
        flex-direction:column;
        justify-content:center;
    }

    .logo{
        width: 56px;
        height: 56px;
        margin: 0 auto 18px;
        border: 2px solid var(--green);
        border-radius: 14px;
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .logo svg{
        width: 28px;
        height: 28px;
    }

    h1{
        text-align:center;
        font-size: 22px;
        color: var(--ink);
        margin: 0 0 6px;
    }

    .subtitle{
        text-align:center;
        font-size: 13px;
        color: var(--muted);
        margin: 0 0 26px;
    }

    .error{
        background:#fdecec;
        color:#c0392b;
        border:1px solid #f5c6c6;
        padding:10px 12px;
        border-radius:6px;
        font-size:13px;
        text-align:center;
        margin-bottom:16px;
    }

    label.field{
        display:block;
        margin-bottom: 18px;
    }

    label.field span{
        display:block;
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 6px;
    }

    input{
        width: 100%;
        border: none;
        border-bottom: 1px solid var(--line);
        padding: 6px 2px 10px;
        font-size: 14px;
        color: var(--ink);
        background: transparent;
        outline: none;
        transition: border-color .15s ease;
    }

    input:focus{
        border-bottom-color: var(--green);
    }

    .forgot{
        text-align:right;
        margin: -8px 0 18px;
    }

    .forgot a{
        font-size: 12px;
        color: var(--muted);
        text-decoration:none;
    }

    .forgot a:hover{ color: var(--green); }

    button.login-btn{
        width: 100%;
        padding: 12px;
        background: var(--green);
        border: none;
        border-radius: 5px;
        color: #fff;
        font-size: 14px;
        font-weight: bold;
        letter-spacing: .5px;
        cursor: pointer;
        transition: background .15s ease;
    }

    button.login-btn:hover{
        background: var(--green-dark);
    }

    .divider{
        display:flex;
        align-items:center;
        gap: 12px;
        margin: 20px 0;
        color: var(--muted);
        font-size: 11px;
    }

    .divider::before,
    .divider::after{
        content:"";
        flex:1;
        height:1px;
        background: var(--line);
    }

    .socials{
        display:flex;
        justify-content:center;
        gap: 18px;
        margin-bottom: 22px;
    }

    .socials a{
        width: 34px;
        height: 34px;
        border-radius:50%;
        border:1px solid var(--line);
        display:flex;
        align-items:center;
        justify-content:center;
        color: var(--ink);
        text-decoration:none;
        font-size: 14px;
    }

    .signup{
        text-align:center;
        font-size: 13px;
        color: var(--muted);
    }

    .signup a{
        color: var(--green);
        font-weight:bold;
        text-decoration:none;
    }

    @media (max-width: 720px){
        .visual{ display:none; }
        .wrap{ max-width: 420px; }
    }
</style>
</head>

<body>

<div class="wrap">

    <div class="visual"></div>

    <div class="panel">

        <div class="logo">
            <svg viewBox="0 0 24 24" fill="none" stroke="#1aa260" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="13" cy="4" r="2"></circle>
                <path d="M4 17l4-2 2-4 3 2 2-3 3 1"></path>
                <path d="M10 11l-2 6 4 4"></path>
                <path d="M13 13l3 2-1 5"></path>
                <path d="M9 13l-3 1-2 4"></path>
            </svg>
        </div>
        <h1>Login To Your Account</h1>
        <p class="subtitle">Enter your credentials to access your dashboard.</p>

        <?php if(isset($error)) { ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <form method="POST">

            <label class="field">
                <span>Email or Username</span>
                <input type="email" name="email" required>
            </label>

            <label class="field">
                <span>Password</span>
                <input type="password" name="password" required>
            </label>

            <div class="forgot"><a href="#">Forgot Password?</a></div>

            <button type="submit" name="login" class="login-btn">LOGIN</button>

        </form>

        <div class="divider">OR</div>

        <div class="socials">
            <a href="#" aria-label="Google">
                <svg viewBox="0 0 24 24" width="16" height="16">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
            </a>
            <a href="#" aria-label="Facebook">
                <svg viewBox="0 0 24 24" width="16" height="16">
                    <path fill="#1877F2" d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.69 4.53-4.69 1.31 0 2.68.24 2.68.24v2.97h-1.51c-1.49 0-1.95.93-1.95 1.89v2.25h3.32l-.53 3.49h-2.79V24C19.61 23.1 24 18.1 24 12.07z"/>
                </svg>
            </a>
            <a href="#" aria-label="Apple">
                <svg viewBox="0 0 24 24" width="16" height="16">
                    <path fill="#000000" d="M16.36 1.43c0 1.14-.42 2.21-1.18 3.02-.78.85-2.07 1.5-3.13 1.42-.13-1.1.41-2.25 1.13-3.02.78-.84 2.13-1.46 3.18-1.42zm3.18 16.6c-.55 1.27-.81 1.84-1.52 2.96-.99 1.56-2.39 3.5-4.13 3.51-1.54.02-1.94-1.01-4.03-1-2.09.01-2.53 1.02-4.07 1-1.74-.02-3.06-1.77-4.05-3.32C-1.4 16.4-.5 11.18 2.21 8.39c1.34-1.38 3.07-2.21 4.65-2.21 1.62 0 2.64 1 3.98 1 1.3 0 2.09-1 3.97-1 1.27 0 2.62.69 3.6 1.88-3.16 1.73-2.65 6.27.13 7.97z"/>
                </svg>
            </a>
        </div>

        <div class="signup">
            Don't have an account? <a href="register.php">Sign up</a>
        </div>

    </div>

</div>

</body>
</html>