<?php //$view_data_select_year_month = array('select_year'=>$select_year, 'select_month'=>$select_month)
// 
?>
<link rel="stylesheet" href="<?php echo asset_url('assets/css/cost-list-glass.css')?>">
<style>
	 




</style>

<div class="content-wrapper">





<section class="content " style="padding-top: 20px;">

		<!-- Cost List =========================================== -->
		<div class="container-fluid">

		
			<div class="row mb-2" >

				<div class="col-sm-6  row">
					<div class="col-auto"><span class="menu-header-first-line">คำนวณต้นทุน</span></div> <div class="col ml-5" ><?php $this->load->view('element/select_year_month') ?></div>
				</div>
			
				<div class="col-auto">

				</div>

				<div class="col-auto">

					               
				</div>


				<div class="col-auto">
				


				</div>

			</div>


<!-- sub tab menu start-->
		<div class="row main-submenu-container" style="padding: 0;">
            

			<div class="col-auto main-submenu active" style="">
					<a href="<?php echo base_url('cost/list?year='.$select_year.'&month='.$select_month) ?>"  >อัพโหลดไฟล์</a>
			</div>

			<div class="col-auto main-submenu" style="">
					<a href="<?php echo base_url('cost/calc?year='.$select_year.'&month='.$select_month) ?>"  >คำนวณต้นทุน</a>
			</div>

			<div class="col main-submenu " style="">
					 &nbsp;
			</div>
        </div>
<!-- sub tab menu end-->


<!-- filter start-->





<!-- filter end-->



		</div>
		<!-- File uploaded List =========================================== -->
 
			 <p class="p-0  minor-title-content1">รายการอัพโหลดไฟล์ - <?php echo monthYearShowFull($select_year,$select_month); ?> </p>
              <div class="p-0">
                <table class="table dataTable" id="tbl-gen-small" style="width: unset;background: #ffffff;">
                  <thead>
                    <tr>
                      <th style="width: 10px">ลำดับ</th>
                      <th>ไฟล์</th>
                      <th>เข้าระบบแล้ว</th>
                      <th>อัพโหลด</th>
                      <th>ดูข้อมูล</th>
                    </tr>
                  </thead>
                  <tbody>	


				<?php 
				$ord=0;
				foreach ($list_all_upload_files as $k => $f) {
					$ord++;
				?>
                    <tr>
                      <td><?php echo $ord ?></td>
                      <!-- <td><?php /*echo strReplaceYMDPattern($f['file_name'],$select_year,$select_month)  */?></td> -->
                      <td><?php echo $f['file_display']  ?></td>
                      <td>
					  <?php
						if($f['is_uploaded']==1){	?>

							<a href="<?php echo base_url($f['uploaded_data']['file_path']) ?>"  download="<?php echo htmlspecialchars($f['uploaded_data']['file_name']) ?>" >	<?php echo $f['uploaded_data']['file_name'] ?> </a>
							<br>
							<?php echo 'วันที่ : '.dateENShort($f['uploaded_data']['upload_dtm'])?>

							
						<?php }else{?>

							- 
						<?php } ?>
                      </td>
                      <td><a href="<?php echo base_url('cost/upload_file_year_month?fc='.$f['file_category'].'&y='.$select_year.'&m='.$select_month) ?>" class="btn btn-sm btn-success" >อัพโหลด</a></td>
                      <td>

					  <?php if($f['is_uploaded']==1){ ?>
						
					  <a href="<?php echo base_url('cost/detail_upload_file_year_month?fc='.$f['file_category'].'&y='.$select_year.'&m='.$select_month) ?>" class="btn btn-sm btn-info" >ดูข้อมูล</a>
					  <?php }else{?>

					  <?php } ?>
					
						</td>
					</tr>
				<?php } ?>
                    
                    
                     
                  </tbody>
                </table>
              </div>
      


		
		<!-- File uploaded List =========================================== -->






	






</section>

</div>










