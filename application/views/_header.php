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
    
    <!-- Custom CSS -->
    <link href="<?php echo asset_url('assets/css/argon-custom.css')?>" rel="stylesheet" />
    
    <style>
        /* Top Menu Specific Styles */
        .top-navbar {
            background: linear-gradient(310deg, #0533b5 0%, #014cc1 100%);
            box-shadow: 0 2px 12px 0 rgba(0,0,0,.16);
        }
        
        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #ffffff !important;
            background: rgba(255,255,255,0.1);
            border-radius: 0.5rem;
        }
        
        .navbar-nav .dropdown-menu {
            border: none;
            box-shadow: 0 8px 26px -4px rgba(20,20,20,0.15);
            border-radius: 0.5rem;
        }
        
        .navbar-nav .dropdown-item {
            padding: 0.5rem 1.5rem;
            transition: all 0.2s;
        }
        
        .navbar-nav .dropdown-item:hover,
        .navbar-nav .dropdown-item.active {
            background: linear-gradient(310deg, #0533b5 0%, #014cc1 100%);
            color: #ffffff;
        }
        
        .main-content-wrapper {
            min-height: calc(100vh - 80px);
            padding-top: 2rem;
        }
    </style>
</head>

<body class="bg-gray-100">
    
<?php
    $part_menu = strtolower(uri_string());
?>

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg top-navbar sticky-top">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="<?php echo base_url('main')?>">
            <img src="<?php echo base_url('/assets/images/logo-small.png')?>" height="40" alt="SCMI Logo" class="me-2">
            <span class="text-white font-weight-bold">SCMI Costing</span>
        </a>
        
        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Menu Items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php echo $menu_item_html;?>
            </ul>
            
            <!-- Right Side Menu -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                        <i class="fa fa-user-circle me-1"></i>
                        <span><?php echo $this->session->userdata('user_name'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo base_url('user/editmyprofile')?>">
                            <i class="fa fa-user me-2"></i>แก้ไขข้อมูลผู้ใช้
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo base_url('login/out')?>">
                            <i class="fa fa-sign-out-alt me-2"></i>ออกจากระบบ
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End Top Navbar -->

<!-- Main Content Wrapper -->
<div class="main-content-wrapper">
    <div class="container-fluid">
    
    <!-- Loading Overlay -->
    <div class="waitloader-overlay" style="display: none;">
        <div class="waitloader-container">
            <div class="waitloader-spinner"></div>
            <div class="waitloader-text"></div>
        </div>
    </div>