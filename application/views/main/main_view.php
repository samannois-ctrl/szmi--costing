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
	position: relative;
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
</style>

<div class="content-wrapper">

<section class="content" style="padding-top: 20px;">

	<!-- Cost Calculation Dashboard -->
	<div class="container-fluid">
	
		<div class="row mb-2">
			<div class="col-sm-6 row">
				<div class="col-auto"><span class="menu-header-first-line">การคำนวณต้นทุน</span></div> 
				<div class="col ml-5"><?php $this->load->view('element/select_year_month') ?></div>
			</div>
		</div>

		<?php if (!$is_file_completed) { ?>
		<!-- No files uploaded message -->
		<div class="row">
			<div class="col-12 text-center mb-3">
				<p class="text-muted">ยังไม่มีการอัพโหลดไฟล์สำหรับเดือนนี้</p>
				<p>กรุณาไปที่หน้า <a href="<?php echo base_url('cost/list?year='.$select_year.'&month='.$select_month) ?>">อัพโหลดไฟล์</a> เพื่ออัพโหลดไฟล์ก่อน</p>
			</div>
		</div>
		<?php } elseif (!$is_calc_fc) { ?>
		<!-- Files uploaded but not calculated -->
		<div class="row">
			<div class="col-12 text-center mb-3">
				<p class="text-muted">ระบบยังไม่ได้คำนวณต้นทุนสำหรับเดือนนี้</p>
				<p>กรุณาไปที่หน้า <a href="<?php echo base_url('cost/calc?year='.$select_year.'&month='.$select_month) ?>">คำนวณต้นทุน</a> เพื่อทำการคำนวณ</p>
			</div>
		</div>
		<?php } else { ?>
		<!-- Calculation data exists - show grid -->
		<div class="row">
			<div class="col-auto">
				<p class="p-0 minor-title-content2">การคำนวณต้นทุน - <?php echo monthYearShowFull($select_year,$select_month); ?></p>
			</div>
		</div>

		<section class="content" style="padding:0;">
			<!-- Sheet tab head -->
			<div class="row div-sheet-head-wrapper" style="background-color: #055CFFFF;">
				<?php 
				if(!empty($list_sheet)){
					$active_sheet_name = $list_sheet[0]['real_sheet_name_on_excel'];
				}else{
					$active_sheet_name = '';
				}
				foreach ($list_sheet as $idx => $sheet) { 
				?>
					<div class="col-auto pr-3 row div-sheet-item div-sheet-<?php echo $sheet['sheet']?> <?php echo ($idx==0)?('active'):('');?>" 
					data-sheet="<?php echo $sheet['sheet']?>"
					data-real_sheet_name_on_excel="<?php echo html_show($sheet['real_sheet_name_on_excel'])?>"
					>
						<div class="col-auto">
							<a class="btn-sheet-link" onclick="clickBtnSheet(this)" data-sheet="<?php echo $sheet['sheet']?>"> 
								<?php echo html_show($sheet['real_sheet_name_on_excel']) ?> 
							</a>
						</div>
					</div>
				<?php } ?>
			</div>
			
			<div class="sheet-active-header text-bold collapse">
				<?php echo $active_sheet_name ?>
			</div>

			<div class="row mt-2 mb-2">
				<div class="col text-right">
					<div class="filter-header" style="width: 300px;display: inline-block;">
						<span>ค้นหา:</span>
						<input class="form-control form-control-sm" style="display: inline;width:auto;" type="text" id="filter-text-box" placeholder="Filter..." oninput="onFilterTextBoxChanged()" />
					</div>
				</div>
			</div>

			<div id="sheet-calc-content" style="height: calc(100vh - 300px);"></div>
		
		</section>
		<?php } ?>

	</div>

</section>

</div>
