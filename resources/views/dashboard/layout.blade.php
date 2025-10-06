<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EDUTILAS+ - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    {{-- Tempat untuk CSS tambahan per halaman --}}
    @stack('styles')

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            height: 100%;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #1e293b;
            overflow-x: hidden;
        }
        .app { display: flex; min-height: 100vh; position: relative; }
        
        /* Enhanced Sidebar */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            display: flex; 
            flex-direction: column;
            position: fixed; 
            height: 100vh; 
            left: 0; 
            top: 0;
            z-index: 1000; 
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
            border-right: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        /* Enhanced Brand Section */
        .brand {
            padding: 2.5rem 1.5rem;
            background: linear-gradient(135deg,rgb(195, 199, 217) 0%,rgb(72, 70, 217) 100%);
            color: white; 
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .brand::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        .brand h1 { 
            font-size: 1.75rem; 
            font-weight: 800; 
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .brand p { 
            font-size: 0.9rem; 
            opacity: 0.95;
            position: relative;
            z-index: 1;
            letter-spacing: 0.5px;
        }
        
        /* Enhanced Menu */
        .menu { 
            flex: 1; 
            padding: 2rem 1rem; 
            overflow-y: auto;
        }
        .menu-item {
            display: flex; 
            align-items: center; 
            gap: 1rem;
            color: #64748b; 
            text-decoration: none;
            padding: 1rem 1.25rem; 
            border-radius: 14px;
            margin-bottom: 0.75rem; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative; 
            font-weight: 500;
            overflow: hidden;
        }
        .menu-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            transform: translateX(-4px);
            transition: transform 0.3s ease;
        }
        .menu-item:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08), rgba(118, 75, 162, 0.08));
            color: #667eea;
        }
        .menu-item:hover::before {
            transform: translateX(0);
        }
        .menu-item.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white; 
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35);
        }
        .menu-item.active::before {
            transform: translateX(0);
        }
        .menu-item i { 
            font-size: 1.25rem; 
            width: 24px; 
            text-align: center;
        }
        
        /* Enhanced Content Area */
        .content { 
            flex: 1; 
            margin-left: 280px; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh;
        }
        
        /* Enhanced Topbar - IMPROVED */
        .topbar {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(255, 255, 255, 0.95));
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(102, 126, 234, 0.15);
            padding: 1rem 2rem; 
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(102, 126, 234, 0.1);
            position: sticky; 
            top: 0; 
            z-index: 100;
        }
        .topbar::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #667eea 100%);
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .topbar-left { 
            display: flex; 
            align-items: center; 
            gap: 1.25rem;
        }
        .topbar-left h2 { 
            font-size: 1.75rem; 
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #667eea 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
            animation: gradient-shift 3s ease infinite;
            position: relative;
        }
        .topbar-left h2::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% center; }
            50% { background-position: 100% center; }
        }
        .topbar-right { 
            display: flex; 
            align-items: center; 
            gap: 1rem;
        }
        
        /* Enhanced User Info - IMPROVED */
        .user-info {
            display: flex; 
            align-items: center; 
            gap: 1rem;
            padding: 0.75rem 1.5rem; 
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-radius: 20px; 
            border: 2px solid rgba(102, 126, 234, 0.25);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .user-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }
        .user-info:hover::before {
            left: 100%;
        }
        .user-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.25);
            border-color: rgba(102, 126, 234, 0.4);
        }
        .user-avatar {
            width: 48px; 
            height: 48px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            color: white; 
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            border: 3px solid rgba(255, 255, 255, 0.9);
            position: relative;
        }
        .user-avatar::after {
            content: '';
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 12px;
            height: 12px;
            background: #10b981;
            border: 2px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.4);
        }
        .user-details {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }
        .user-name {
            font-weight: 700;
            font-size: 1rem;
            color: #1e293b;
            letter-spacing: -0.01em;
        }
        .user-role {
            font-size: 0.8rem;
            color: #667eea;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Enhanced Logout Button - IMPROVED */
        .logout-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white; 
            border: none; 
            padding: 0.875rem 1.75rem;
            border-radius: 14px; 
            font-weight: 700; 
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            text-decoration: none;
            display: flex; 
            align-items: center; 
            gap: 0.625rem;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.35);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.3px;
        }
        .logout-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.5s, height 0.5s;
        }
        .logout-btn:hover::before {
            width: 300px;
            height: 300px;
        }
        .logout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(239, 68, 68, 0.5);
        }
        .logout-btn:active {
            transform: translateY(-1px);
        }
        .logout-btn i {
            position: relative;
            z-index: 1;
        }
        .logout-btn span {
            position: relative;
            z-index: 1;
        }
        
        /* Enhanced Page Content */
        .page { 
            flex: 1; 
            padding: 2.5rem;
            background: transparent;
        }
        .page-header { 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .page-title { 
            font-size: 2.5rem; 
            font-weight: 800;
            color: white;
            margin: 0;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        
        /* Enhanced Badge */
        .badge {
            display: inline-flex; 
            align-items: center; 
            gap: 0.625rem;
            background: rgba(255, 255, 255, 0.95);
            color: #667eea;
            border-radius: 25px;
            padding: 0.75rem 1.5rem; 
            font-size: 0.9rem; 
            font-weight: 700;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Enhanced Cards Grid */
        .cards {
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem; 
            margin-bottom: 2.5rem;
        }
        
        /* Enhanced Cards */
        .card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 20px; 
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        .card:hover { 
            transform: translateY(-6px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.18);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
        }
        .btn-export {
            background: rgba(255, 255, 255, 0.95);
            color: #667eea;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            background: white;
        }
        .btn-refresh {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .btn-refresh:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        /* Data Section */
        .data-section {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin-top: 2rem;
        }
        .data-section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #f1f5f9;
        }
        .data-section-header i {
            font-size: 1.5rem;
            color: white;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .data-section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e2e8f0;
        }
        .tab {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 1.5rem;
            border: none;
            background: transparent;
            color: #64748b;
            font-weight: 600;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            margin-bottom: -2px;
        }
        .tab:hover {
            color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }
        .tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        /* Search and Filters */
        .search-filter-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }
        .search-box input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
        }
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
        }
        .filter-select {
            padding: 1rem 1.5rem;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 1rem;
            background: #f8fafc;
            color: #1e293b;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 180px;
        }
        .filter-select:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
        }

        /* Table Styles */
        .table-responsive {
            overflow-x: auto;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table thead {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .data-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
        }
        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .data-table tbody tr {
            transition: all 0.2s ease;
        }
        .data-table tbody tr:hover {
            background: rgba(102, 126, 234, 0.04);
        }

        /* Mobile Menu Button - IMPROVED */
        .mobile-menu-btn {
            display: none; 
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15), rgba(118, 75, 162, 0.15));
            border: 2px solid rgba(102, 126, 234, 0.3);
            font-size: 1.5rem; 
            color: #667eea;
            cursor: pointer;
            padding: 0.75rem; 
            border-radius: 14px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .mobile-menu-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(102, 126, 234, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
        }
        .mobile-menu-btn:hover::before {
            width: 120px;
            height: 120px;
        }
        .mobile-menu-btn:hover { 
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.25), rgba(118, 75, 162, 0.25));
            border-color: rgba(102, 126, 234, 0.5);
        }
        .mobile-menu-btn i {
            position: relative;
            z-index: 1;
        }

        /* Toast Notification Styles */
        .toast {
            position: fixed;
            top: 30px;
            right: 30px;
            background: white;
            border-radius: 16px;
            padding: 1.25rem 1.75rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 320px;
            transform: translateX(500px);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 5px solid;
        }
        .toast.show { transform: translateX(0); }
        .toast.success {
            border-left-color: #10b981;
            background: linear-gradient(135deg, #ecfdf5, #f0fdf4);
        }
        .toast.error {
            border-left-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2, #fef1f1);
        }
        .toast.info {
            border-left-color: #3b82f6;
            background: linear-gradient(135deg, #eff6ff, #f0f9ff);
        }
        .toast-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .toast.success .toast-icon { background: #10b981; }
        .toast.error .toast-icon { background: #ef4444; }
        .toast.info .toast-icon { background: #3b82f6; }
        .toast-content { flex: 1; }
        .toast-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.375rem;
        }
        .toast-message {
            font-size: 0.9rem;
            color: #64748b;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .content { margin-left: 0; }
            .mobile-menu-btn { display: block; }
            .cards { 
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
                gap: 1.5rem; 
            }
            .action-buttons { flex-wrap: wrap; }
        }
        @media (max-width: 768px) {
            .topbar { padding: 1rem; }
            .topbar-left h2 { font-size: 1.25rem; }
            .page { padding: 1.5rem; }
            .cards { grid-template-columns: 1fr; }
            .page-title { font-size: 2rem; }
            .user-details { display: none; }
            .search-filter-row { flex-direction: column; }
            .search-box { min-width: 100%; }
            .toast {
                right: 15px;
                left: 15px;
                min-width: auto;
                transform: translateY(-150px);
            }
            .toast.show { transform: translateY(0); }
            .logout-btn span { display: none; }
            .logout-btn { padding: 0.875rem; }
        }
        
        .overlay {
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            right: 0; 
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 999;
            backdrop-filter: blur(4px);
        }
        .overlay.active { display: block; }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <h1>EDUTILAS<span style="color: #fbbf24;">+</span></h1>
            <p>Sistem Perpustakaan</p>
        </div>
        <nav class="menu">
            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('dashboard.buku') }}" class="menu-item {{ request()->routeIs('dashboard.buku*') ? 'active' : '' }}">
                <i class="fas fa-book"></i> <span>Data Buku</span>
            </a>
            <a href="{{ route('dashboard.peminjaman') }}" class="menu-item {{ request()->routeIs('dashboard.peminjaman*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding"></i> <span>Data Peminjaman</span>
            </a>
            <a href="{{ route('dashboard.data-warga-sekolah') }}" class="menu-item {{ request()->routeIs('dashboard.data-warga-sekolah*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> <span>Data Warga Sekolah</span>
            </a>
        </nav>
    </aside>

    <div class="overlay" id="overlay"></div>

    <main class="content">
        <div class="topbar">
            <div class="topbar-left">
                <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-bars"></i></button>
                <h2>Dashboard Admin</h2>
            </div>
            <div class="topbar-right">
                <div class="user-info">
                    <div class="user-avatar"><i class="fas fa-user"></i></div>
                    <div class="user-details">
                        <div class="user-name">Admin</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
        <div class="page">
            @yield('content')
        </div>
    </main>
</div>

{{-- Toast Container --}}
<div id="toastContainer"></div>

<script>
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    
    mobileMenuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    });
    
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    });
    
    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        }
    });

    // Toast Notification System
    function showToast(title, message, type = 'success') {
        const toastContainer = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        
        const icons = {
            success: 'fas fa-check',
            error: 'fas fa-times',
            info: 'fas fa-info'
        };
        
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="${icons[type]}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Show toast with animation
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        // Remove toast after 4 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 400);
        }, 4000);
    }

    // Make showToast available globally
    window.showToast = showToast;
</script>

{{-- Tempat untuk JavaScript tambahan per halaman --}}
@stack('scripts')

</body>
</html>