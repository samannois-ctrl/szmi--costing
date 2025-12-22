<?php //$view_data_select_year_month = array('select_year'=>$select_year, 'select_month'=>$select_month)
// 
?>
<style>
	 




</style>

<div class="content-wrapper" style="background: #ffffff;">





<section class="content " style="padding-top: 5px;padding-left: 0px;padding-right: 0px;">  

		<!-- Cost List =========================================== -->
		<div class="container-fluid" style="padding-left: 3px;">

		
			<div class="row mb-0" >

				<div class="col-sm-6  row">
					<div class="col-auto pl-4" >
            <span class="menu-header-first-line-upload text-bold">
              อัพโหลดเดือน <span style="color: #0f4fcd;">  <?php echo monthYearShowFull($select_year,$select_month); ?> </span>
              ไฟล์ <span style="color: #0f4fcd;"> <?php echo $file_display ?>  </span>
              
            </span>
          </div> 
         
				</div>
			
				<div class="col text-right">
          <a class="btn btn-sm btn-primary" href="<?php echo base_url('cost/list?year='.$select_year.'&month='.$select_month); ?>"> <i class="fa  fa-arrow-left"></i> กลับหน้าคำนวณต้นทุน </a>
				</div>



			</div>


<!-- sub tab menu start-->
<div class="card card-primary card-outline card-outline-tabs">
              <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs tab-condense" id="tabhead-upload-step" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="link-upload-step1" data-toggle="pill" href="#content-upload-step1" role="tab" aria-controls="content-upload-step1" aria-selected="true"><span class="badge badge-success" > 1 </span> เลือกไฟล์</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="link-upload-step2" data-toggle="pill" href="#content-upload-step2" role="tab" aria-controls="content-upload-step2" aria-selected="true"><span class="badge badge-success" > 2 </span> ตรวจสอบข้อมูล</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="link-upload-step3" data-toggle="pill" href="#content-upload-step3" role="tab" aria-controls="content-upload-step3" aria-selected="true"><span class="badge badge-success" > 3 </span> ผลการบันทึก</a>
                  </li>
                 
                </ul>
              </div>
              <div class="card-body pl-0 pr-0 pt-1 pb-1" >
                <div class="tab-content" id="content-upload-step-cover">
                  <div class="tab-pane fade show active" style="min-height: 600px;" id="content-upload-step1" role="tabpanel" aria-labelledby="content-upload-step1">
                    
                    <div class="p-5 text-center">
               
                      <span> กรุณากดปุ่มเลือกไฟล์ เพื่ออัพโหลดเข้าระบบ และตรวจสอบข้อมูลของไฟล์ให้ตรงกับปีและเดือนที่เลือกไว้</span><br><br> 
                      <span>
                      <?php 

                          echo $suggestion;

                       ?>
                      </span>
                      <br>
                      <br>
                      <form id="form-excel-upload">
					                <input type="file" name="file_excel" id="file_excel" style="display: none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                          <button id="btn_file_excel" type="button" class="btn btn bg-excel" onclick="$('#file_excel').click();" style=""> <i class="bi bi-file-earmark-excel"></i> เลือกไฟล์ </button>

                          <input type="hidden" name="excel_year_upload" id="excel_year_upload" value="<?php echo $select_year ?>">
                          <input type="hidden" name="excel_month_upload" id="excel_month_upload" value="<?php echo $select_month ?>">
                          <input type="hidden" name="file_category" id="file_category" value="<?php echo $file_category ?>">
                        
                      </form>


                      <div class="upload_status text-center m-1">
					                        
                      </div>


                    </div>

                     



                  </div>
                  <div class="tab-pane fade show" style="min-height: 600px;" id="content-upload-step2" role="tabpanel" aria-labelledby="content-upload-step2">
                  <div class="waiting_render_table text-center mt-3" style="display:none;"  >รอการแสดงผลข้อมูล...<i class="bi bi-hourglass-split"></i></div>
                  <div id="preview_upload_file_content" > <div class="text-center text-lg" style="margin:20px auto;width:300px;font-size: 2rem;font-weight:bold;">ยังไม่ได้อัพโหลดไฟล์</div> </div>
                  </div>                 
                  <div class="tab-pane fade show" style="min-height: 600px;" id="content-upload-step3" role="tabpanel" aria-labelledby="content-upload-step3">
                  <div class="confirm_save_upload_result text-lg text-center" style="margin:20px auto;width:300px;font-size: 2rem;font-weight:bold;"><span>ยังไม่ได้กดยืนยันบันทึก</span></div>
                  <div class="confirm_save_upload_detail" style="margin:auto;width:600px;"><table class="table table-sm table-borderless  confirm_save_upload_list"  ></table></div>
                  <div class="btn-back-to-upload-list text-center p-4">
                    <a class="btn btn-sm btn-primary" href="<?php echo base_url('cost/list?year='.$select_year.'&month='.$select_month); ?>">  <i class="fa  fa-arrow-left"></i> กลับหน้าคำนวณต้นทุน </a>
                  </div>
                  </div>                 
                 
                </div>
              </div>
              <!-- /.card -->
            </div>
<!-- sub tab menu end-->


<!-- filter start-->





<!-- filter end-->



		</div>
		<!-- File uploaded List =========================================== -->
 

		
		<!-- File uploaded List =========================================== -->






	






</section>

</div>










