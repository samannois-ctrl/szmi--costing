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
              ข้อมูล 
              <span style="color: #0f4fcd;"> <?php echo $file_display ?>  </span>
              <span style="color: #0f4fcd;"> ( เดือน <?php echo monthYearShowFull($select_year,$select_month); ?> )</span>
            </span>
          </div> 
         
				</div>
			
				<div class="col text-right">
          <a class="btn btn-sm btn-primary" href="<?php echo base_url('cost/list?year='.$select_year.'&month='.$select_month); ?>"> <i class="fa  fa-arrow-left"></i> กลับหน้าคำนวณต้นทุน </a>
				</div>



			</div>

            <div class="upload_status text-center" style="width:100%;"></div>
            <div id="preview_upload_file_content" > <div class="text-center text-lg" style="margin:20px auto;width:300px;font-size: 2rem;font-weight:bold;"></div> </div>


<!-- filter start-->


<!-- filter end-->



		</div>
		<!-- File uploaded List =========================================== -->
 

		<!-- File uploaded List =========================================== -->



</section>

</div>










