<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>กรุณาลงชื่อเข้าระบบ</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta content="Cost allocation system" name="description" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo asset_url('assets/images/favicon/favicon.ico')?>">

     


        <script src="<?php echo asset_url('assets/js/jquery/jquery-3.7.1.js')?>"></script>
        <script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/dist/js/adminlte.min.js')?>"></script>

        <link rel="stylesheet" href="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/dist/css/adminlte.min.css')?>">


        <link rel="stylesheet" href="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css')?>">
        <link rel="stylesheet" href="<?php echo asset_url('assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css')?>">
        <link rel="stylesheet" href="<?php echo asset_url('assets/css/argon-custom.css')?>">
        <link rel="stylesheet" href="<?php echo asset_url('assets/css/login-glass.css')?>">
        <link rel="stylesheet" href="<?php echo asset_url('assets/css/login-glass-toggle.css')?>">

    </head>

    <body class="login-page">

<div class="container-fluid">
    	<div class="row">


    		<div class="col" style="padding-top: 100px;">
    		

    	

				<div class="card login-card" style="margin: auto;width: 550px;">
					<div class="card-header bg-web-main" style="text-align: center;">
					<h3 class="card-title" style="float: none !important ;">กรุณาลงชื่อเข้าสู่ระบบ</h3>
					</div>


					<form class="form-horizontal" id="form-login">
						<div class="card-body">
							<div class="form-group row">
							<label for="input-user-login" class="col-sm-4 col-form-label">ชื่อผู้ใช้</label>
							<div class="col-sm-8">
							<input type="text" class="form-control" id="input-user-login" autocomplete="on"  >
							</div>
							</div>



							<div class="form-group row">
								<label for="input-user-pass" class="col-sm-4 col-form-label">รหัสผ่าน</label>
								<div class="col-sm-8">


									


									<div class="" style="position: relative;">
										<input type="password" class="form-control" id="input-user-pass" autocomplete="on">
										<a class="btn"   onclick="togglepassword(this)" style="position: absolute;right: 0px; top: -4px;">
											<i class="bi bi-eye-slash" id="togglePassword" data-show="0" style="font-size: 1.2em;"  ></i>
										</a>
										
									</div>


								</div>
							</div>
							<div class="row" >
								
								<div class="col-9" style="height: 20px;">
									<span id="warning-msg" class="text-danger" style="visibility: hidden;"></span>
								</div>
								<div class="col-3" style="text-align:right;">
									<span class="badge badge-danger" id="cap-warning" style="line-height: 1.5em;visibility: hidden;">Caps Lock On</span>
								</div>

							</div>


						</div>

						<div class="card-footer">
							<button type="submit" class="btn btn-primary" style="display: block; margin:0 auto;">เข้าระบบ</button>
						</div>

					</form>
				</div>


			</div>

		</div>


</div>


	</body>


	</html>



	<script type="text/javascript">



		var input1 = document.getElementById("input-user-login");
		var input2 = document.getElementById("input-user-pass");
		var text = document.getElementById("cap-warning");
		




		function togglepassword(el){

			var i = $('#togglePassword');

			

			if($(i).data('show') == '0'){

				$(i).data('show','1');
				$(i).addClass('bi-eye').removeClass('bi-eye-slash');
				$('#input-user-pass').attr('type','text');


			}else if($(i).data('show') == '1'){

				$(i).data('show','0');
				$(i).addClass('bi-eye-slash').removeClass('bi-eye');
				$('#input-user-pass').attr('type','password');

			}



		}


		input2.addEventListener("keyup", function(event) {

			//console.log("input2 keyup event");


			if(  typeof event.getModifierState === 'function') {
				//console.log("input2 keyup event is function");
				  // If the Caps Lock is on, display the warning text
				  if (event.getModifierState("CapsLock")) {
						text.style.visibility = "visible";
					} else {
						text.style.visibility = "hidden"
					}
			}else{
				//console.log("input2 keyup event is not function");
			}

		

	 
		});

		$('#form-login').submit(function(e){


					$('#warning-msg').text('');
					$('#warning-msg').css('visibility', 'hidden');


			//alert('auth'); 
			e.preventDefault(); //prevent reload page


			//check valid form
			if(    String($('#input-user-login').val()).trim().length <= 0  ){

					$('#warning-msg').text('กรุณากรอกชื่อผู้ใช้');
					$('#warning-msg').css('visibility', 'visible');
					return;

			}



			var username = $('#input-user-login').val();
			var password = $('#input-user-pass').val();


			$.post('<?php echo base_url('login/auth')?>',{username, password},function(data){ 

				

				if(data.result == 'granted'){

					window.location.href = data.redirect_to;

				}else if(data.error_msg!=''){

				console.log(data);



					$('#warning-msg').text(data.error_msg);
					$('#warning-msg').css('visibility', 'visible');


				}

			});


			return false;//prevent reload page

		}




		);


		function checkDBConnection(){










		}


	</script>