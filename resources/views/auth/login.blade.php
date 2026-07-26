<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login · Vidyananda Vidyapeetaya</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {

            min-height: 100vh;

            font-family: 'Segoe UI', sans-serif;

            background:
                radial-gradient(circle at 20% 10%,
                    rgba(34, 211, 238, 0.20),
                    transparent 40%),

                radial-gradient(circle at 80% 20%,
                    rgba(56, 189, 248, 0.18),
                    transparent 35%),

                radial-gradient(circle at 70% 80%,
                    rgba(20, 184, 166, 0.20),
                    transparent 30%),

                radial-gradient(circle at 20% 90%,
                    rgba(52, 211, 153, 0.15),
                    transparent 35%),

                linear-gradient(135deg,
                    #020617,
                    #0a2235,
                    #071b2a);

            display: flex;
            justify-content: center;
            align-items: center;

            overflow: hidden;

            position: relative;
        }

        /* FLOATING LIGHTS - Enhanced */

        .floating-bg {
            position: absolute;
            inset: 0;
            overflow: hidden;
        }

        .floating-circle {

            position: absolute;

            border-radius: 50%;

            background: rgba(255, 255, 255, .04);

            animation: float 12s infinite ease-in-out;

            filter: blur(2px);
        }

        .floating-circle:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -80px;
            left: -80px;
            background: radial-gradient(circle, rgba(34, 211, 238, 0.12), transparent 70%);
        }

        .floating-circle:nth-child(2) {
            width: 220px;
            height: 220px;
            bottom: -60px;
            right: -50px;
            animation-delay: 2s;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.10), transparent 70%);
        }

        .floating-circle:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 55%;
            left: 8%;
            animation-delay: 1s;
            background: radial-gradient(circle, rgba(20, 184, 166, 0.10), transparent 70%);
        }

        .floating-circle:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 15%;
            right: 15%;
            animation-delay: 3s;
            background: radial-gradient(circle, rgba(52, 211, 153, 0.10), transparent 70%);
        }

        .floating-circle:nth-child(5) {
            width: 180px;
            height: 180px;
            top: 75%;
            right: 25%;
            animation-delay: 1.5s;
            background: radial-gradient(circle, rgba(34, 211, 238, 0.08), transparent 70%);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) scale(1);
            }

            50% {
                transform: translateY(-30px) scale(1.05);
            }
        }

        /* LOGIN WRAPPER */

        .login-wrapper {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 5;
            padding: 20px;
        }

        /* LOGIN CARD - Enhanced */

        .login-card {

            position: relative;

            overflow: hidden;

            border-radius: 38px;

            background: rgba(8, 24, 36, 0.72);

            border: 1px solid rgba(255, 255, 255, 0.10);

            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);

            box-shadow:
                0 30px 80px rgba(0, 0, 0, 0.55),
                inset 0 1px 0 rgba(255, 255, 255, 0.06);

            color: white;

            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-3px);
        }

        /* Glassmorphism gradient overlay */
        .login-card::before {

            content: '';

            position: absolute;

            width: 350px;
            height: 350px;

            background: radial-gradient(circle, rgba(34, 211, 238, 0.10), transparent 70%);

            border-radius: 50%;

            top: -150px;
            right: -150px;

            pointer-events: none;
        }

        .login-card::after {

            content: '';

            position: absolute;

            width: 200px;
            height: 200px;

            background: radial-gradient(circle, rgba(20, 184, 166, 0.08), transparent 70%);

            border-radius: 50%;

            bottom: -80px;
            left: -80px;

            pointer-events: none;
        }

        /* HEADER - Enhanced */

        .login-header {

            position: relative;

            padding: 50px 40px 25px;

            text-align: center;
        }

        .logo-box {

            width: 96px;
            height: 96px;

            margin: auto;

            border-radius: 30px;

            background:
                linear-gradient(135deg,
                    rgba(34, 211, 238, 0.95),
                    rgba(56, 189, 248, 0.85));

            display: flex;
            justify-content: center;
            align-items: center;

            box-shadow:
                0 15px 35px rgba(34, 211, 238, 0.30),
                inset 0 1px 0 rgba(255, 255, 255, 0.20);

            margin-bottom: 28px;

            position: relative;
            z-index: 2;

            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .logo-box:hover {
            transform: scale(1.05);
            box-shadow: 0 20px 45px rgba(34, 211, 238, 0.40);
        }

        .logo-box i {

            font-size: 42px;
            color: white;

            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.20));
        }

        .brand-title {

            font-size: 30px;

            font-weight: 800;

            letter-spacing: 0.5px;

            margin-bottom: 12px;
        }

        .brand-title .highlight {
            background: linear-gradient(90deg, #67e8f9 0%, #38bdf8 40%, #14b8a6 70%, #34d399 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {

            color: rgba(255, 255, 255, 0.70);

            font-size: 15px;

            line-height: 1.7;

            font-weight: 400;
        }

        .brand-subtitle i {
            color: var(--emerald, #34d399);
            margin: 0 3px;
        }

        /* BODY */

        .login-body {
            padding: 20px 40px 42px;
            position: relative;
            z-index: 2;
        }

        /* LABELS - Enhanced */

        .login-label {

            font-weight: 600;

            margin-bottom: 10px;

            font-size: 14px;

            color: #e2e8f0;

            letter-spacing: 0.3px;
        }

        .login-label i {
            color: #38bdf8;
            margin-right: 6px;
        }

        /* INPUT GROUP - Enhanced */

        .custom-input-group {

            background: rgba(255, 255, 255, 0.06);

            border: 1px solid rgba(255, 255, 255, 0.10);

            border-radius: 20px;

            overflow: hidden;

            transition: all 0.3s ease;
        }

        .custom-input-group:focus-within {

            border-color: rgba(56, 189, 248, 0.60);

            box-shadow:
                0 0 0 5px rgba(34, 211, 238, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);

            background: rgba(255, 255, 255, 0.08);
        }

        .custom-input-group .input-group-text {

            background: transparent;

            border: none;

            color: #94a3b8;

            padding-left: 20px;

            font-size: 16px;
        }

        .custom-input {

            background: transparent !important;

            border: none !important;

            color: white !important;

            height: 58px;

            font-size: 15px;

            font-weight: 400;
        }

        .custom-input::placeholder {
            color: rgba(255, 255, 255, 0.35);
            font-weight: 300;
        }

        .custom-input:focus {
            box-shadow: none !important;
            background: transparent !important;
        }

        /* PASSWORD BUTTON - Enhanced */

        .toggle-password {

            border: none;

            background: transparent;

            color: #94a3b8;

            padding: 0 20px;

            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: #38bdf8;
        }

        /* REMEMBER - Enhanced */

        .form-check-input {

            background-color: rgba(255, 255, 255, 0.10);

            border: 1px solid rgba(255, 255, 255, 0.25);

            width: 18px;
            height: 18px;
            margin-top: 2px;
            cursor: pointer;
        }

        .form-check-input:checked {

            background-color: #38bdf8;

            border-color: #38bdf8;

            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.20);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
            border-color: rgba(56, 189, 248, 0.50);
        }

        .form-check-label {
            color: #cbd5e1;
            font-size: 14px;
            cursor: pointer;
        }

        /* LOGIN BUTTON - Enhanced */

        .btn-login {

            width: 100%;

            height: 60px;

            border: none;

            border-radius: 20px;

            background:
                linear-gradient(135deg,
                    #38bdf8 0%,
                    #22d3ee 50%,
                    #14b8a6 100%);

            color: white;

            font-size: 16px;

            font-weight: 700;

            transition: all 0.3s ease;

            box-shadow:
                0 12px 35px rgba(34, 211, 238, 0.25);

            position: relative;

            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.10), transparent 50%);
            pointer-events: none;
            border-radius: 20px;
        }

        .btn-login:hover {

            transform: translateY(-3px);

            box-shadow:
                0 18px 45px rgba(34, 211, 238, 0.35);

            background:
                linear-gradient(135deg,
                    #22d3ee 0%,
                    #38bdf8 50%,
                    #14b8a6 100%);
        }

        .btn-login:active {
            transform: translateY(0px);
        }

        /* LINKS - Enhanced */

        .custom-link {

            color: #bfdbfe;

            text-decoration: none;

            transition: all 0.3s ease;

            font-weight: 500;
        }

        .custom-link:hover {
            color: #67e8f9;
            text-decoration: underline;
        }

        /* ALERT - Enhanced */

        .alert {

            border: none;

            border-radius: 20px;

            background: rgba(239, 68, 68, 0.15);

            color: #fca5a5;

            backdrop-filter: blur(10px);

            border: 1px solid rgba(239, 68, 68, 0.20);
        }

        .alert-danger .mb-0 {
            color: #fca5a5;
        }

        .alert li {
            list-style-type: none;
            padding-left: 0;
        }

        .alert li::before {
            content: '• ';
            color: #f87171;
        }

        /* FOOTER - Enhanced */

        .footer-text {

            margin-top: 30px;

            text-align: center;

            color: rgba(255, 255, 255, 0.45);

            font-size: 14px;

            font-weight: 400;
        }

        .footer-text .custom-link {
            font-weight: 500;
        }

        /* RESPONSIVE - Enhanced */

        @media(max-width:576px) {

            .login-card {
                border-radius: 30px;
            }

            .login-header {
                padding: 35px 25px 20px;
            }

            .login-body {
                padding: 16px 25px 30px;
            }

            .brand-title {
                font-size: 24px;
            }

            .logo-box {
                width: 80px;
                height: 80px;
            }

            .logo-box i {
                font-size: 34px;
            }

            .custom-input {
                height: 50px;
                font-size: 14px;
            }

            .btn-login {
                height: 54px;
                font-size: 15px;
            }

            .brand-subtitle {
                font-size: 13px;
            }

            .login-label {
                font-size: 13px;
            }

            .custom-input-group {
                border-radius: 16px;
            }

            .btn-login {
                border-radius: 16px;
            }
        }

        @media(max-height: 700px) {
            .login-header {
                padding: 30px 35px 15px;
            }

            .logo-box {
                width: 72px;
                height: 72px;
                margin-bottom: 18px;
            }

            .logo-box i {
                font-size: 30px;
            }

            .brand-title {
                font-size: 24px;
                margin-bottom: 6px;
            }

            .brand-subtitle {
                font-size: 13px;
            }

            .login-body {
                padding: 12px 35px 28px;
            }

            .custom-input {
                height: 48px;
            }

            .btn-login {
                height: 50px;
            }
        }
    </style>

</head>

<body>

    <!-- FLOATING BG - Enhanced -->
    <div class="floating-bg">

        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>

    </div>

    <!-- LOGIN WRAPPER -->
    <div class="login-wrapper">

        <div class="login-card">

            <!-- HEADER -->
            <div class="login-header">

                <div class="logo-box">
                    <i class="fas fa-graduation-cap"></i>
                </div>

                <h1 class="brand-title">
                    <span class="highlight">Vidyananda</span><br>Vidyapeetaya
                </h1>

                <p class="brand-subtitle">
                    <i class="fas fa-star" style="color: #22d3ee; font-size: 12px;"></i>
                    Wisdom · Virtue · Excellence
                    <i class="fas fa-star" style="color: #22d3ee; font-size: 12px;"></i><br>
                    Secure portal for staff
                </p>

            </div>

            <!-- BODY -->
            <div class="login-body">

                <!-- ERRORS -->
                @if($errors->any())

                    <div class="alert alert-danger mb-4">

                        <ul class="mb-0 ps-3">

                            @foreach($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                @endif

                <!-- FORM -->
                <form method="POST" action="{{ route('login') }}">

                    @csrf

                    <!-- EMAIL -->
                    <div class="mb-4">

                        <label class="login-label">
                            <i class="fas fa-envelope"></i>Email Address
                        </label>

                        <div class="input-group custom-input-group">

                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>

                            <input type="email" name="email" class="form-control custom-input"
                                placeholder="Enter your email" required>

                        </div>

                    </div>

                    <!-- PASSWORD -->
                    <div class="mb-4">

                        <label class="login-label">
                            <i class="fas fa-lock"></i>Password
                        </label>

                        <div class="input-group custom-input-group">

                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>

                            <input type="password" name="password" id="password" class="form-control custom-input"
                                placeholder="Enter your password" required>

                            <button type="button" class="toggle-password" onclick="togglePassword()">

                                <i class="fas fa-eye" id="eyeIcon"></i>

                            </button>

                        </div>

                    </div>

                    <!-- REMEMBER & FORGOT -->
                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div class="form-check">

                            <input type="checkbox" class="form-check-input" id="remember">

                            <label class="form-check-label" for="remember">

                                Remember Me

                            </label>

                        </div>

                        <a href="forgot-password" class="custom-link">

                            Forgot Password?

                        </a>

                    </div>

                    <!-- LOGIN BUTTON -->
                    <button type="submit" class="btn-login">

                        <i class="fas fa-sign-in-alt me-2"></i>

                        Access Dashboard

                    </button>

                </form>

                <!-- FOOTER -->
                <div class="footer-text">

                    Need help?

                    <a href="{{ route('contact_administrator') }}" class="custom-link">

                        Contact Administrator

                    </a>

                </div>

            </div>

        </div>

    </div>

    <!-- PASSWORD TOGGLE -->
    <script>

        function togglePassword() {

            const password =
                document.getElementById('password');

            const eyeIcon =
                document.getElementById('eyeIcon');

            if (password.type === 'password') {

                password.type = 'text';

                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');

            } else {

                password.type = 'password';

                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

    </script>

</body>

</html>