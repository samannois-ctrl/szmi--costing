<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>กรุณาลงชื่อเข้าระบบ - SCMI Costing</title>
    <meta name="description" content="Cost allocation system" />
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo asset_url('assets/images/favicon/favicon.ico')?>">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    
    <!-- Nucleo Icons -->
    <link href="<?php echo asset_url('assets/templates/argon-dashboard/css/nucleo-icons.css')?>" rel="stylesheet" />
    
    <!-- Font Awesome Icons -->
    <link href="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css')?>" rel="stylesheet">
    <link href="<?php echo asset_url('assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css')?>" rel="stylesheet">
    
    <!-- Argon Dashboard CSS -->
    <link href="<?php echo asset_url('assets/templates/argon-dashboard/css/argon-dashboard.min.css')?>" rel="stylesheet" />
    
    <!-- Custom macOS CSS -->
    <link href="<?php echo asset_url('assets/css/argon-custom.css')?>" rel="stylesheet" />
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #0533b5 0%, #2563eb 100%);
            padding: 2.5rem 2rem;
            text-align: center;
        }
        
        .login-header h3 {
            color: #ffffff;
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .login-header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 0.5rem 0 0 0;
            font-size: 0.95rem;
        }
        
        .login-body {
            padding: 2.5rem 2rem;
        }
        
        .form-label {
            font-weight: 500;
            color: #1d1d1f;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-control {
            border: 1px solid #d2d2d7;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            border-color: #0533b5;
            box-shadow: 0 0 0 3px rgba(5, 51, 181, 0.1);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #0533b5 0%, #2563eb 100%);
            border: none;
            border-radius: 10px;
            color: #ffffff;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(5, 51, 181, 0.3);
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #86868b;
            z-index: 10;
            transition: color 0.2s ease;
        }
        
        .password-toggle:hover {
            color: #0533b5;
        }
        
        .caps-warning {
            background: #ff3b30;
            color: #ffffff;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .text-danger {
            color: #ff3b30 !important;
            font-size: 0.85rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-lg-5 col-md-7">
                <div class="login-card">
                    <div class="login-header">
                        <h3>ระบบคำนวณต้นทุน SCMI</h3>
                        <p>กรุณาลงชื่อเข้าสู่ระบบ</p>
                    </div>
                    
                    <div class="login-body">
                        <form id="form-login" role="form">
                            <div class="mb-3">
                                <label for="input-user-login" class="form-label">ชื่อผู้ใช้</label>
                                <input type="text" class="form-control" id="input-user-login" placeholder="กรอกชื่อผู้ใช้" autocomplete="on">
                            </div>
                            
                            <div class="mb-3">
                                <label for="input-user-pass" class="form-label">รหัสผ่าน</label>
                                <div style="position: relative;">
                                    <input type="password" class="form-control" id="input-user-pass" placeholder="กรอกรหัสผ่าน" autocomplete="on">
                                    <i class="bi bi-eye-slash password-toggle" id="togglePassword" data-show="0" onclick="togglePassword(this)"></i>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-8">
                                    <span id="warning-msg" class="text-danger" style="visibility: hidden;"></span>
                                </div>
                                <div class="col-4 text-end">
                                    <span class="caps-warning" id="cap-warning" style="visibility: hidden;">Caps Lock On</span>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-login btn-lg w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>เข้าระบบ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Core JS -->
    <script src="<?php echo asset_url('assets/js/jquery/jquery-3.7.1.js')?>"></script>
    <script src="<?php echo asset_url('assets/templates/argon-dashboard/js/core/popper.min.js')?>"></script>
    <script src="<?php echo asset_url('assets/templates/argon-dashboard/js/core/bootstrap.min.js')?>"></script>
    <script src="<?php echo asset_url('assets/templates/argon-dashboard/js/argon-dashboard.min.js')?>"></script>
    
    <script type="text/javascript">
        var input1 = document.getElementById("input-user-login");
        var input2 = document.getElementById("input-user-pass");
        var text = document.getElementById("cap-warning");
        
        function togglePassword(el) {
            var i = $('#togglePassword');
            
            if($(i).data('show') == '0') {
                $(i).data('show','1');
                $(i).addClass('bi-eye').removeClass('bi-eye-slash');
                $('#input-user-pass').attr('type','text');
            } else if($(i).data('show') == '1') {
                $(i).data('show','0');
                $(i).addClass('bi-eye-slash').removeClass('bi-eye');
                $('#input-user-pass').attr('type','password');
            }
        }
        
        input2.addEventListener("keyup", function(event) {
            if(typeof event.getModifierState === 'function') {
                if (event.getModifierState("CapsLock")) {
                    text.style.visibility = "visible";
                } else {
                    text.style.visibility = "hidden";
                }
            }
        });
        
        $('#form-login').submit(function(e) {
            $('#warning-msg').text('');
            $('#warning-msg').css('visibility', 'hidden');
            
            e.preventDefault();
            
            if(String($('#input-user-login').val()).trim().length <= 0) {
                $('#warning-msg').text('กรุณากรอกชื่อผู้ใช้');
                $('#warning-msg').css('visibility', 'visible');
                return;
            }
            
            var username = $('#input-user-login').val();
            var password = $('#input-user-pass').val();
            
            $.post('<?php echo base_url('login/auth')?>',{username, password},function(data) { 
                if(data.result == 'granted') {
                    window.location.href = data.redirect_to;
                } else if(data.error_msg != '') {
                    console.log(data);
                    $('#warning-msg').text(data.error_msg);
                    $('#warning-msg').css('visibility', 'visible');
                }
            });
            
            return false;
        });
    </script>
</body>
</html>