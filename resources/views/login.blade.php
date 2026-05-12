<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mitta Excellence - Login</title>

    @include('includes.header_links')

    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            font-family: "Poppins", sans-serif;

            background: linear-gradient(135deg, #1F62A6, #96cce7ff);
        }

        .background-pattern {
            position: absolute;
            inset: 0;
            background: url('https://cdn.dribbble.com/users/1162077/screenshots/17047720/media/0b78c5081c198d357c1530b18f6f4b87.png') center/cover no-repeat;
            opacity: 0.12;
            z-index: 0;
        }

        .login-container {
            position: relative;
            z-index: 2;
            width: 900px;
            max-width: 95%;
            display: flex;
            border-radius: 26px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            overflow: hidden;
            box-shadow: 0 18px 60px rgba(0, 0, 0, 0.25);
        }

        /* LEFT PANEL */
        .login-left {
            width: 50%;
            padding: 40px;
            background: rgba(255, 255, 255, 0.08);
            position: relative;
            color: #fff;

            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-left::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(31, 98, 166, 0.35);
            z-index: 1;
        }

        .left-content {
            position: relative;
            z-index: 2;
        }

        .left-content h1 {
            font-size: 34px;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .left-content p {
            font-size: 15px;
            line-height: 1.6;
            color: #e7f3ff;
            max-width: 90%;
            margin-bottom: 20px;
        }

        .feature-points {
            margin-top: 10px;
            padding-left: 18px;
        }

        .feature-points li {
            margin-bottom: 10px;
            font-size: 14px;
            color: #e9f4ff;
            line-height: 1.6;
        }

        /* RIGHT SIDE */
        .login-right {
            width: 50%;
            padding: 60px 50px;
            text-align: center;
            color: white;
        }

        /* LOGO ANIMATION */
        .logo-wrapper {
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 22px;
            margin: 0 auto 30px auto;
            display: flex;
            justify-content: center;
            align-items: center;

            animation: glowPulse 3s infinite ease-in-out,
                floatLogo 4s infinite ease-in-out;
        }

        @keyframes glowPulse {
            0% {
                box-shadow: 0 0 18px rgba(255, 255, 255, 0.5);
            }

            50% {
                box-shadow: 0 0 40px rgba(255, 255, 255, 0.85);
            }

            100% {
                box-shadow: 0 0 18px rgba(255, 255, 255, 0.5);
            }
        }

        @keyframes floatLogo {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0);
            }
        }

        .logo {
            width: 150px;
            height: auto;
            filter: drop-shadow(0px 2px 4px rgba(0, 0, 0, 0.35));
        }

        .login-right h3 {
            font-size: 24px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .login-right p {
            font-size: 14px;
            margin-bottom: 25px;
            color: #e6f3ff;
        }


        /* ==========================================
           CORPORATE INPUT FIELDS
        ========================================== */
        .form-control {
            height: 46px;
            border-radius: 10px;
            background: #ffffff !important;
            border: 1px solid #d5ddeb !important;
            color: #1a1a1a !important;
            padding-left: 14px;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
            transition: 0.2s ease;
        }

        .form-control:focus {
            border-color: #1F62A6 !important;
            box-shadow: 0 0 0 3px rgba(31, 98, 166, 0.25) !important;
        }

        .form-control::placeholder {
            color: #7b8a9f !important;
        }

        /* CORPORATE LOGIN BUTTON */
        .btn-login {
            width: 100%;
            height: 46px;
            border-radius: 10px;
            background: #1F62A6;
            color: #fff;
            font-weight: 600;
            border: none;
            margin-top: 12px;
            transition: 0.3s ease;
        }

        .btn-login:hover {
            background: #174d84;
            transform: translateY(-2px);
        }


        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .login-left,
            .login-right {
                width: 100%;
                padding: 35px 25px;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <div class="background-pattern"></div>

    <div class="login-container">

        <!-- LEFT SIDE -->
        <div class="login-left">
            <div class="left-content">
                <h1>Mitta Excellence<br>Medical CRM</h1>
                <p>Powerful tools designed for hospitals, labs, and medical centers to manage daily workflow smoothly.
                </p>

                <ul class="feature-points">
                    <li>Fast OP Registrations</li>
                    <li>Smart Test Management & Tracking</li>
                    <li>Instant Report Generation</li>
                    <li>Branch & Team Coordination</li>
                    <li>Secure Medical Data Storage</li>
                </ul>
            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="login-right">

            <div class="logo-wrapper">
                <img src="{{ asset('public/img/logo.webp') }}" alt="Mitta Excellence Logo" class="logo">
            </div>

            <h3>Login to Your Account</h3>
            <p>Enter your credentials to continue</p>

            <!-- CORPORATE INPUT FIELDS -->
            <!-- <form>
                <div class="mb-3">
                    <input type="email" class="form-control" placeholder="Enter Email" required>
                </div>

                <div class="mb-3">
                    <input type="password" class="form-control" placeholder="Enter Password" required>
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form> -->


            <form action="{{ route('login.submit') }}" method="POST">
                @csrf

                <!-- Show Validation Errors -->
                @if ($errors->any())
                    <div style="background:#ff4d4d; padding:10px; border-radius:8px; color:white; margin-bottom:20px;">
                        <ul style="margin:0; padding-left:18px;">
                            @foreach ($errors->all() as $error)
                                <li style="font-size:14px;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Show Custom Error Messages -->
                @if(session('error'))
                    <div style="background:#ff4d4d; padding:10px; border-radius:8px; color:white; margin-bottom:20px;">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Enter Email"
                        value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form>


        </div>

    </div>

    @include('includes.footer_links')


</body>

</html>