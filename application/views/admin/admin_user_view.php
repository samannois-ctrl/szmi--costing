<link rel="stylesheet" href="<?php echo base_url('assets/css/cost-list-glass.css'); ?>">

<div class="content-wrapper">



<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6 text-web-main">
				<h6><b>ผู้ใช้ระบบ</b></h6>
			</div>
		
			<div class="col-sm-6">


			</div>
		</div>
	</div>

</section>



<section class="content">


		<div class="container-fluid"  >

		



			<div class="row">

				<div class="col">
					
					<div id="showdata">



					</div>


				</div>

			</div>


		</div>

</section>

</div>




<!-- Modal -->
<div class="modal fade" id="modal-add" data-backdrop="static" tabindex="-1" role="dialog"  >
  <div class="modal-dialog modal-lg " role="document" >
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h5 class="modal-title" >เพิ่มผู้ใช้</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

      	<form autocomplete="off">
      	<table class="tbl-form-user">
		  <tr>
		    <td style="vertical-align: top;"><label for="input-login-name">ชื่อผู้ใช้ (สำหรับเข้าระบบ)</label></td>
		    <td style="vertical-align: top;"><input type="text" class="form-control form-control-sm" id="input-login-name" name="input-login-name"  style="width:300px;display: inline;margin-left:10px;"  onchange="checkcode(this);" data-self-code="">
		    <small><span id="code-error" class="text-danger pl-1 "></span></small>
			</td>
		  </tr>

		  <tr>
		    <td style="vertical-align: top;"><label for="input-user-name">ชื่อ-นามสกุล</label></td>
		     <td style="vertical-align: top;"><input type="text" class="form-control form-control-sm" id="input-user-name" name="input-user-name"  style="width:300px;display: inline;margin-left:10px;" >
		     </td>
		  </tr>

		  <tr>
		    <td style="vertical-align: top;"><label for="input-user-pass" id="label-password">เปลี่ยนรหัสผ่านใหม่ <br><small>(ถ้าไม่ต้องการเปลี่ยนรหัสผ่าน ไม่ต้องกรอก)</small></label></td>
		     <td style="vertical-align: top;">

		     						<div class="" style="position: relative;width:315px">
										
										<input type="password" class="form-control form-control-sm" id="input-user-pass" name="input-user-pass" style="width:300px;display: inline;margin-left:10px;" >
										<a class="btn"   onclick="togglepassword(this)" style="position: absolute;right: 0px; top: -4px;">
											<i class="bi bi-eye-slash" id="togglePassword" data-show="0" style="font-size: 1.2em;"  ></i>
										</a>
										   
									</div>
		     </td>
		  </tr>
		 </table>
				</form>

		  <input type="hidden" id="modify-mode" name="modify-mode" value="add">
		  <input type="hidden" id="hidden-id" name="hidden-id" value="">


        
      </div>
      <div class="modal-footer" style="text-align: center;display: block;">
      	
        <button type="button" class="btn btn-sm btn-success btn-confirm" onclick="modifyData();">บันทึก</button>

        <button type="button" class="btn btn-sm btn-secondary"  data-dismiss="modal">ยกเลิก</button>
        
      </div>
    </div>
  </div>
</div>