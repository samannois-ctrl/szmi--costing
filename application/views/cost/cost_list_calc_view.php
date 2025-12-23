<link rel="stylesheet" href="<?php echo asset_url('assets/css/cost-list-glass.css')?>">
<style>
	 
.tbl-pos-data{
	table-layout: fixed;
	border-collapse: collapse;
	border-spacing: 0;


}

.tbl-pos-data td, .tbl-pos-data th{
	font-size: 0.85em;
	padding-left: 3px;
	padding-right: 3px;
	padding-top: 5px;
	padding-bottom: 5px;
	border: 1px solid #dee2e6;
}

.tbl-pos-data th{

	text-align: center;
}

#tbl-pos-main{
	table-layout: fixed;
	border-collapse: collapse;
	border-spacing: 0;
	position: relative;;


}

#tbl-pos-main td, #tbl-pos-main th{
	font-size: 0.85em;
	padding-left: 3px;
	padding-right: 3px;
	padding-top: 5px;
	padding-bottom: 5px;
	
	border: 1px solid #cfcfcf;
}

#tbl-pos-main th{

	position: sticky;
	top:0;

}


.row-appform-used td{
	background: #e2f9fb !important;
}

.filter-row .col-auto{
	padding-top:10px;
}

.tbl-form-pos td{
	padding-top: 10px;
}



</style>

<div class="content-wrapper">





<section class="content " style="padding-top: 20px;">

		<!-- Cost List =========================================== -->
		<div class="container-fluid">

		
			<div class="row mb-2" >

			<div class="col-sm-6  row">
				<div class="col-auto"><span class="menu-header-first-line">คำนวณต้นทุน</span></div> 
				<div class="col ml-5" ><?php $this->load->view('element/select_year_month') ?></div>
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
            

		<div class="col-auto main-submenu " style="">
					<a href="<?php echo base_url('cost/list?year='.$select_year.'&month='.$select_month) ?>"  >อัพโหลดไฟล์</a>
			</div>

			<div class="col-auto main-submenu active" style="">
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



		<!-- Calc List =========================================== -->
		

		<div class="row">
			<div class="col-auto">
			<p class="p-0  minor-title-content2">การคำนวณต้นทุน - <?php echo monthYearShowFull($select_year,$select_month); ?> </p>
			</div>

			<?php if(!$is_calc_fc){ ?>
			<div class="col-auto text-center">
				<div class="upload_status" ></div>
			</div>			
			<div class="col-12 text-center mb-3">
				<p>	ระบบยังไม่ได้คำนวณต้นทุนสำหรับเดือนนี้ กรุณาคลิกปุ่ม "คำนวณต้นทุน" เพื่อคำนวณต้นทุนครั้งแรก </p>
				<button type="button" class="btn bg-indigo" onclick="start_calc_cost(1);"><i class="bi bi-calculator"></i> คำนวณต้นทุน </button>

				<br><br><input type="checkbox" class="form-control form-control-sm collapse" value="1" id="chk_compare"> 
			</div>
			<?php }else{ ?>

			
			<div class="col-auto text-center mb-1">
				<button type="button" class="btn btn-sm bg-indigo" onclick="start_calc_cost(0);"><i class="bi bi-arrow-clockwise"></i> คำนวณต้นทุนทั้งหมดอีกครั้ง </button>
			</div>
			<div class="col-auto text-center mb-1">
				<button type="button" class="btn btn-sm " style="background:#8c0000;color:#ffffff;" onclick="exportCalcResultToExcel();"><i class="bi bi-file-earmark-spreadsheet"></i> Export Excel </button>
			</div>
			
			<div class="col-auto text-center">
				<div class="upload_status" ></div>
			</div>	
			<?php } ?>


		</div>

		<!-- <input type="date" id="calc-date-input"  /> -->
		<section class="content" style="padding:0;<?php if(!$is_calc_fc){ ?> display:none; <?php } ?>">

			<!-- Sheet tab head start -->

			<!-- Sheet tab head end -->


			<div class="row div-sheet-head-wrapper"  style="background-color: #055CFFFF;">

				<?php 
				if(!empty($list_sheet)){
					$active_sheet_name = $list_sheet[0]['real_sheet_name_on_excel'];
				}else{
					$active_sheet_name = '';
				}
				foreach ($list_sheet as $idx => $sheet) { 
					
						
				?>
					<div class="col-auto pr-3 row div-sheet-item div-sheet-<?php echo $sheet['sheet']?> <?php echo ($idx==0)?('active'):('');?>	" 
					data-sheet="<?php echo $sheet['sheet']?>"
					data-real_sheet_name_on_excel="<?php echo html_show($sheet['real_sheet_name_on_excel'])?>"
					
					>
						<div class="col-auto">
							<a class="btn-sheet-link" onclick="clickBtnSheet(this)" data-sheet="<?php echo $sheet['sheet']?>"> 
								<?php echo html_show($sheet['real_sheet_name_on_excel']) ?> 
							</a>
						</div>
						<!-- <div class="col-auto ">
							<div class="sheet-spin czollapse loading-spinner mt-2 collapse"></div>
							
						</div>
						<div class="col-auto ">
							
							<button class="sheet-btn-calc btn btn-sm btn-light collapse"><i class="bi bi-calculator-fill"></i></button>
						</div>
						
						<span class="calc-btn-action">
							
						</span> -->
					</div>
				<?php } ?>

			</div>
			
			<div class="sheet-active-header text-bold collapse" >
				<?php echo $active_sheet_name ?>
			</div>


			<div class="row mt-2 mb-2">
				<div class="col">
					<button class="btn btn-sm btn-success btn-save-sheet" onclick="saveSheetRecalc()" title="บันทึก"><i class="bi bi-save"></i> บันทึกชีทนี้ </button>
				</div>
			
				<div class="col text-right">
					<div class="filter-header " style="width: 300px;display: inline-block;">
						<span>ค้นหา:</span>
						<input class="form-control form-control-sm" style="display: inline;width:auto;" type="text" id="filter-text-box" placeholder="Filter..." oninput="onFilterTextBoxChanged()" />
					</div>
				</div>

			</div>
			



			<div id="sheet-calc-content" style="height: calc(100vh - 300px);">


			</div>
		
		</section>

 
		<div class="compare-table">


		</div>

</section>

</div>
 