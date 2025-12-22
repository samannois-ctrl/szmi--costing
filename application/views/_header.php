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
</head>

<body class="g-sidenav-show bg-gray-100">
    
<?php
    $part_menu = strtolower(uri_string());
?>

<!-- Sidebar -->
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-primary" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="<?php echo base_url('main')?>">
            <img src="<?php echo base_url('/assets/images/logo-small.png')?>" class="navbar-brand-img h-100" alt="SCMI Logo">
            <span class="ms-1 font-weight-bold text-white">SCMI Costing</span>
        </a>
    </div>
    
    <hr class="horizontal light mt-0 mb-2">
    
    <div class="collapse navbar-collapse w-auto h-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <?php echo $menu_item_html;?>
        </ul>
    </div>
</aside>
<!-- End Sidebar -->

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">หน้าหลัก</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">ระบบคำนวณต้นทุน</h6>
            </nav>
            
            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                    <!-- Search or other elements can go here -->
                </div>
                
                <ul class="navbar-nav justify-content-end">
                    <li class="nav-item d-flex align-items-center">
                        <a href="<?php echo base_url('user/editmyprofile')?>" class="nav-link text-body font-weight-bold px-0">
                            <i class="fa fa-user me-sm-1"></i>
                            <span class="d-sm-inline d-none"><?php echo $this->session->userdata('user_name'); ?></span>
                        </a>
                    </li>
                    
                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </li>
                    
                    <li class="nav-item px-3 d-flex align-items-center">
                        <a href="<?php echo base_url('login/out')?>" class="nav-link text-body p-0" title="ออกจากระบบ">
                            <i class="fa fa-sign-out-alt cursor-pointer"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->
    
    <!-- Loading Overlay -->
    <div class="waitloader-overlay" style="display: none;">
        <div class="waitloader-container">
            <div class="waitloader-spinner"></div>
            <div class="waitloader-text"></div>
        </div>
    </div>