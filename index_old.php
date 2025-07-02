<?php
session_start();
include 'config.php';

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit();
}

$error = '';
if (isset($_GET['error'])) {
    $error = 'Username atau password salah!';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMPELBEND</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #1a2a6c);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .login-header {
            background: linear-gradient(to right, #2c3e50, #1a252f);
            color: white;
            text-align: center;
            padding: 40px 20px;
            position: relative;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%);
        }
        
        .logo {
            font-size: 3.5rem;
            margin-bottom: 15px;
            color: #3498db;
            text-shadow: 0 0 10px rgba(52, 152, 219, 0.5);
        }
        
        .login-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .login-header p {
            opacity: 0.8;
            font-size: 1rem;
            margin-top: 10px;
        }
        
        .login-body {
            padding: 40px 30px 30px;
            position: relative;
        }
        
        .wave-decoration {
            position: absolute;
            top: -60px;
            left: 0;
            width: 100%;
            height: 60px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z' opacity='.25' fill='%232c3e50'/%3E%3Cpath d='M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z' fill='%23ffffff'/%3E%3C/svg%3E");
            background-size: 1200px 60px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
            text-align: center;
            font-size: 0.95rem;
            animation: shake 0.5s;
            position: relative;
            overflow: hidden;
        }
        
        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
        }
        
        .alert-error {
            background: rgba(255, 235, 238, 0.8);
            color: #c62828;
            border: 1px solid rgba(255, 205, 210, 0.5);
        }
        
        .alert-error::before {
            background: #c62828;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            background: rgba(255, 255, 255, 0.8);
        }
        
        .input-with-icon input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            background: white;
        }
        
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(to right, #2c3e50, #1a252f);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(44, 62, 80, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(44, 62, 80, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .login-footer {
            text-align: center;
            padding: 20px;
            background: rgba(248, 249, 250, 0.7);
            color: #7f8c8d;
            font-size: 0.9rem;
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        
        .login-footer a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .login-footer a:hover {
            color: #2980b9;
            text-decoration: underline;
        }
        
        .forgot-password {
            text-align: right;
            margin-top: -10px;
            margin-bottom: 25px;
        }
        
        .forgot-password a {
            color: #7f8c8d;
            font-size: 0.9rem;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .forgot-password a:hover {
            color: #3498db;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #7f8c8d;
            z-index: 2;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
            20%, 40%, 60%, 80% { transform: translateX(10px); }
        }
        
        /* Floating elements */
        .floating {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
            backdrop-filter: blur(4px);
            z-index: -1;
        }
        
        .floating:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation: float 15s linear infinite;
        }
        
        .floating:nth-child(2) {
            width: 120px;
            height: 120px;
            bottom: 30%;
            right: 15%;
            animation: float 18s linear infinite reverse;
        }
        
        .floating:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 60%;
            left: 25%;
            animation: float 12s linear infinite;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(20px, 20px) rotate(90deg); }
            50% { transform: translate(40px, 0) rotate(180deg); }
            75% { transform: translate(20px, -20px) rotate(270deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }
        
        /* Responsiveness */
        @media (max-width: 480px) {
            .login-container {
                max-width: 95%;
            }
            
            .login-body {
                padding: 30px 20px;
            }
            
            .login-header {
                padding: 30px 15px;
            }
            
            .login-header h1 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <!-- Floating background elements -->
    <div class="floating"></div>
    <div class="floating"></div>
    <div class="floating"></div>
    
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-file-contract"></i>
            </div>
            <h1>SIMPELBEND</h1>
            <p>KABUPATEN KOTAWARINGIN BARAT</p>
        </div>
        
        <div class="wave-decoration"></div>
        
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>
            
            <form action="login.php" method="post" id="loginForm">
                <div class="form-group">
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" id="username" placeholder="Username" required autocomplete="username">
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Password" required autocomplete="current-password">
                        <span class="password-toggle" id="passwordToggle">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div class="forgot-password">
                        <a href="#" id="forgotPassword">Lupa Password?</a>
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> LOGIN
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            &copy; <?= date('Y') ?> SIMPELBEND
        </div>
    </div>

    <script>
        // Toggle password visibility
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');
        
        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
        
        // Forgot password modal (placeholder)
        document.getElementById('forgotPassword').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Tampilkan modal sederhana
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.7);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1000;
            `;
            
            modal.innerHTML = `
                <div style="
                    background: white;
                    padding: 30px;
                    border-radius: 15px;
                    text-align: center;
                    width: 90%;
                    max-width: 400px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                    position: relative;
                ">
                    <button style="
                        position: absolute;
                        top: 15px;
                        right: 15px;
                        background: none;
                        border: none;
                        font-size: 1.5rem;
                        cursor: pointer;
                        color: #777;
                    " id="closeModal">×</button>
                    
                    <h2 style="margin-bottom: 20px; color: #2c3e50;">Lupa Password</h2>
                    <p style="margin-bottom: 20px; color: #555;">
                        Silakan hubungi administrator sistem untuk reset password
                    </p>
                    <div style="display: flex; justify-content: center; gap: 10px;">
                        <button style="
                            padding: 10px 20px;
                            background: #3498db;
                            color: white;
                            border: none;
                            border-radius: 5px;
                            cursor: pointer;
                        " id="contactAdmin">
                            <i class="fas fa-user-tie"></i> Hubungi Admin
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Handle close button
            modal.querySelector('#closeModal').addEventListener('click', function() {
                document.body.removeChild(modal);
            });
            
            // Handle contact button
            modal.querySelector('#contactAdmin').addEventListener('click', function() {
                alert('Email admin: admin@perusahaan.com');
                document.body.removeChild(modal);
            });
        });
        
        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Username dan password harus diisi!');
            }
        });
        
        // Add focus effect on load
        window.onload = function() {
            document.getElementById('username').focus();
            
            // Add floating animation to login container
            const container = document.querySelector('.login-container');
            container.style.transform = 'translateY(20px)';
            container.style.opacity = '0';
            
            setTimeout(() => {
                container.style.transition = 'transform 0.8s ease, opacity 0.8s ease';
                container.style.transform = 'translateY(0)';
                container.style.opacity = '1';
            }, 100);
        };
    </script>

        <div class="login-container">
        <!-- ... Bagian header yang sudah ada ... -->
        
        <div class="wave-decoration"></div>
        
        <div class="login-body">
            <!-- ... Form login yang sudah ada ... -->
        </div>
        
        <div class="login-footer">
            &copy; <?= date('Y') ?> SIMPELBEND | 
            <a href="track_document.php" style="margin-left: 10px;">
                <i class="fas fa-search"></i> Lacak Dokumen
            </a>
        </div>
    </div>
    
</body>
</html>