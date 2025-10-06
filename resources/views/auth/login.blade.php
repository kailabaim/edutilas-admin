<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - EDUTILAS+</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        html, body {
            height: 100%;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #586dcbff 0%, #6e4894ff 100%);
            overflow-x: hidden;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }
        
        .left {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            z-index: 2;
        }
        
        .card {
            max-width: 460px;
            width: 100%;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.8s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #afbbf0ff, #4e3864ff, #667eea);
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .brand {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .brand h1 {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #667eea 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.75rem;
            animation: gradient-shift 3s ease infinite;
            letter-spacing: -0.5px;
        }
        
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% center; }
            50% { background-position: 100% center; }
        }
        
        .brand p {
            color: #64748b;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .title {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            margin-bottom: 1.75rem;
            padding-bottom: 1.25rem;
            border-bottom: 2px solid #f1f5f9;
        }
        
        .title .icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.75rem;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            position: relative;
        }
        
        .title .icon::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 16px;
            padding: 3px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0.3;
        }
        
        .title h2 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.5px;
        }
        
        .subtitle {
            color: #64748b;
            font-size: 1rem;
            margin-bottom: 2.25rem;
            line-height: 1.7;
            font-weight: 500;
        }
        
        .field {
            margin-bottom: 1.75rem;
        }
        
        .label {
            display: block;
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.75rem;
        }
        
        .input-group {
            position: relative;
        }
        
        .input {
            width: 100%;
            height: 56px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 0 1.25rem 0 3.5rem;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #f8fafc;
            color: #1e293b;
        }
        
        .input::placeholder {
            color: #94a3b8;
        }
        
        .input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.12);
            transform: translateY(-2px);
        }
        
        .input-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .input:focus + .input-icon {
            color: #667eea;
        }
        
        .remember {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            margin: 1.75rem 0;
            font-size: 0.95rem;
            color: #64748b;
            font-weight: 600;
            cursor: pointer;
            user-select: none;
        }
        
        .remember input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: #667eea;
            cursor: pointer;
        }
        
        .btn {
            width: 100%;
            height: 56px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 14px;
            color: white;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn:hover::before {
            width: 400px;
            height: 400px;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
        }
        
        .btn:active {
            transform: translateY(-1px);
        }
        
        .btn i {
            position: relative;
            z-index: 1;
        }
        
        .btn span {
            position: relative;
            z-index: 1;
        }
        
        .right {
            flex: 1;
            background: url('https://jurnalposmedia.com/wp-content/uploads/2022/07/2612498771.jpg') center/cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .right::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(177, 188, 237, 0.85), rgba(154, 137, 171, 0.85));
            backdrop-filter: blur(0.5px);
        }
        
        .right::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.08) 0%, transparent 50%);
        }
        
        .right-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            padding: 3rem;
            max-width: 600px;
        }
        
        .right-content h3 {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            line-height: 1.2;
            letter-spacing: -1px;
            animation: fadeInUp 1s ease-out;
        }
        
        .right-content p {
            font-size: 1.3rem;
            opacity: 0.95;
            line-height: 1.7;
            font-weight: 500;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1s ease-out 0.2s backwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            z-index: 1;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
            backdrop-filter: blur(5px);
        }
        
        .shape:nth-child(1) { 
            width: 120px; 
            height: 120px; 
            top: 15%; 
            left: 10%; 
            animation-delay: 0s;
            animation-duration: 7s;
        }
        .shape:nth-child(2) { 
            width: 180px; 
            height: 180px; 
            top: 55%; 
            right: 8%; 
            animation-delay: 2s;
            animation-duration: 9s;
        }
        .shape:nth-child(3) { 
            width: 90px; 
            height: 90px; 
            bottom: 15%; 
            left: 15%; 
            animation-delay: 4s;
            animation-duration: 6s;
        }
        .shape:nth-child(4) { 
            width: 60px; 
            height: 60px; 
            top: 40%; 
            left: 45%; 
            animation-delay: 1s;
            animation-duration: 8s;
        }
        
        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) translateX(0px) rotate(0deg); 
            }
            25% { 
                transform: translateY(-30px) translateX(20px) rotate(90deg); 
            }
            50% { 
                transform: translateY(-50px) translateX(-20px) rotate(180deg); 
            }
            75% { 
                transform: translateY(-30px) translateX(20px) rotate(270deg); 
            }
        }
        
        @media (max-width: 768px) {
            .right { display: none; }
            .left { flex: 1; padding: 1.5rem; }
            .card { 
                padding: 2rem; 
                max-width: 100%;
            }
            .brand h1 { font-size: 2rem; }
            .title h2 { font-size: 1.5rem; }
            .title .icon { 
                width: 50px; 
                height: 50px; 
                font-size: 1.5rem; 
            }
        }
        
        .alert {
            border: 2px solid #f59e0b;
            background: linear-gradient(135deg, #fef3c7, #fef3c7);
            color: #92400e;
            padding: 1.25rem 1.5rem;
            border-radius: 14px;
            margin-bottom: 1.75rem;
            font-size: 0.95rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.875rem;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);
        }
        
        .alert i {
            font-size: 1.25rem;
            color: #f59e0b;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container">
    <div class="left">
        <div class="card">
            <div class="brand">
                <h1>EDUTILAS<span style="color: #fbbf24;">+</span></h1>
                <p>Sistem Manajemen Perpustakaan</p>
            </div>
            
            <div class="title">
                <div class="icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h2>Masuk ke Dashboard</h2>
            </div>
            
            <p class="subtitle">Selamat datang kembali! Masukkan kredensial Anda untuk mengakses sistem perpustakaan.</p>

            @if (session('status'))
                <div class="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                <div class="field">
                    <label class="label" for="username">Username atau Email</label>
                    <div class="input-group">
                        <input class="input" id="username" type="text" name="username" placeholder="Masukkan username atau email" required autofocus>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>
                
                <div class="field">
                    <label class="label" for="password">Password</label>
                    <div class="input-group">
                        <input class="input" id="password" type="password" name="password" placeholder="Masukkan password" required>
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>
                
                <label class="remember">
                    <input type="checkbox" name="remember">
                    <span>Ingat saya</span>
                </label>
                
                <button class="btn" type="submit">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>
        </div>
    </div>
    
    <div class="right">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        <div class="right-content">
            <h3>Selamat Datang di EDUTILAS+</h3>
        </div>
    </div>
</div>
</body>
</html>