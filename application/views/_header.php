<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ระบบคำนวณต้นทุน SCMI</title>
    <meta name="description" content="Cost allocation system">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo asset_url('assets/images/favicon/favicon.ico')?>">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    
    <!-- Nucleo Icons -->
    <link href="<?php echo asset_url('assets/templates/argon-dashboard/css/nucleo-icons.css')?>" rel="stylesheet" />
    <link href="<?php echo asset_url('assets/templates/argon-dashboard/css/nucleo-svg.css')?>" rel="stylesheet" />
    
    <!-- Font Awesome Icons -->
    <link href="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css')?>" rel="stylesheet">
    <link href="<?php echo asset_url('assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css')?>" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link href="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')?>" rel="stylesheet">
    <!-- Toastr -->
    <link href="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/toastr/toastr.min.css')?>" rel="stylesheet">
    
    <!-- jsGrid -->
    <link href="<?php echo asset_url('assets/js/jsgrid-1.5.3/dist/jsgrid.css')?>" rel="stylesheet">
    <link href="<?php echo asset_url('assets/js/jsgrid-1.5.3/dist/jsgrid-theme.css')?>" rel="stylesheet">
    
    <!-- Datatable -->
    <link href="<?php echo asset_url('assets/js/Datatable-2.1.8/datatables.css')?>" rel="stylesheet">
    
    <!-- JS multi-select -->
    <link href="<?php echo asset_url('assets/js/multiple-select-1.7.0/dist/multiple-select.min.css')?>" rel="stylesheet">
    
    <!-- Bootstrap Datepicker -->
    <link href="<?php echo asset_url('assets/js/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')?>" rel="stylesheet">
    
    <!-- Argon Dashboard CSS -->
    <link href="<?php echo asset_url('assets/templates/argon-dashboard/css/argon-dashboard.min.css')?>" rel="stylesheet" />
    
    <!-- Custom macOS CSS -->
    <link href="<?php echo asset_url('assets/css/argon-custom.css')?>" rel="stylesheet" />
    
    <style>
        /* macOS-style Top Navbar - Darker Version */
        .macos-navbar {
            background: rgba(29, 29, 31, 0.85);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.15);
            padding: 0.5rem 0;
        }
        
        .navbar-brand {
            font-weight: 600;
            color: #ffffff !important;
            font-size: 1.1rem;
        }
        
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff !important;
        }
        
        .navbar-nav .nav-link.active {
            background-color: rgba(5, 51, 181, 0.3);
            color: #ffffff !important;
        }
        
        .navbar-nav .dropdown-menu {
            background: rgba(45, 45, 48, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 24px 0 rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 8px;
            margin-top: 8px;
        }
        
        .navbar-nav .dropdown-item {
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.15s ease;
            color: rgba(255, 255, 255, 0.85);
        }
        
        .navbar-nav .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }
        
        .navbar-nav .dropdown-item.active {
            background-color: #0533b5;
            color: #ffffff;
        }
        
        .main-content-wrapper {
            min-height: calc(100vh - 80px);
            padding: 2rem 0;
        }
        
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.85%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
    </style>
</head>

<body class="bg-gray-100">
    
<?php
    $part_menu = strtolower(uri_string());
?>

<!-- macOS-style Top Navbar -->
<nav class="navbar navbar-expand-lg macos-navbar sticky-top">
    <div class="container-fluid px-4">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="<?php echo base_url('main')?>">
            <img src="<?php echo base_url('/assets/images/logo-small.png')?>" height="32" alt="SCMI Logo" class="me-2">
            <span>SCMI Costing</span>
        </a>
        
        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Menu Items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto ms-4">
                <?php echo $menu_item_html;?>
            </ul>
            
            <!-- Right Side Menu -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                        <i class="fa fa-user-circle me-2" style="font-size: 1.2rem;"></i>
                        <span><?php echo $this->session->userdata('user_name'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item d-flex align-items-center" href="<?php echo base_url('user/editmyprofile')?>">
                            <i class="fa fa-user me-2"></i>แก้ไขข้อมูลผู้ใช้
                        </a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item d-flex align-items-center" href="<?php echo base_url('login/out')?>">
                            <i class="fa fa-sign-out-alt me-2"></i>ออกจากระบบ
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End macOS Navbar -->

<!-- Main Content Wrapper -->
<div class="main-content-wrapper">
    <div class="container-fluid px-4">
    
    <!-- Loading Overlay -->
    <div class="waitloader-overlay" style="display: none;">
        <div class="waitloader-container">
            <div class="waitloader-spinner"></div>
            <div class="waitloader-text"></div>
        </div>
    </div>