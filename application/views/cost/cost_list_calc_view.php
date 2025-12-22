<style>
    /* macOS-style Tabs */
    .macos-tabs {
        background: var(--macos-white);
        border-bottom: 1px solid var(--macos-gray-200);
        padding: 0;
        margin-bottom: 2rem;
    }
    
    .macos-tab-item {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        color: var(--macos-text-secondary);
        font-weight: 500;
        font-size: 0.95rem;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .macos-tab-item:hover {
        color: var(--macos-text);
        background-color: var(--macos-gray-50);
        text-decoration: none;
    }
    
    .macos-tab-item.active {
        color: var(--scmi-blue);
        border-bottom-color: var(--scmi-blue);
        font-weight: 600;
    }
    
    /* Page Header */
    .page-header {
        margin-bottom: 1.5rem;
    }
    
    .page-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--macos-text);
        margin: 0;
    }
    
    /* Content Card */
    .content-card {
        background: var(--macos-white);
        border: 1px solid var(--macos-gray-200);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        margin-bottom: 1.5rem;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--macos-text);
        margin-bottom: 1rem;
    }

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

<div class="content-wrapper" style="background: var(--macos-gray-100);">

<section class="content" style="padding-top: 20px;">

    <div class="container-fluid">
    
        <!-- Page Header -->
        <div class="row page-header align-items-center mb-3">
            <div class="col-auto">
                <h1 class="page-title">คำนวณต้นทุน</h1>
            </div>
            <div class="col-auto ms-auto">
                <?php $this->load->view('element/select_year_month') ?>
            </div>
        </div>

        <!-- Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="macos-tabs">
                    <a href="<?php echo base_url('cost/list?year='.$select_year.'&month='.$select_month) ?>" 
                       class="macos-tab-item">
                        <i class="fas fa-upload me-2"></i>อัพโหลดไฟล์
                    </a>
                    <a href="<?php echo base_url('cost/calc?year='.$select_year.'&month='.$select_month) ?>" 
                       class="macos-tab-item active">
                        <i class="fas fa-calculator me-2"></i>คำนวณต้นทุน
                    </a>
                </div>
            </div>
        </div>

    </div>


	<!-- Calc List =========================================== -->
	<div class="row">
		<div class="col-12">
			<div class="content-card">
				<div class="row align-items-center mb-3">
					<div class="col-auto">
						<h2 class="section-title mb-0">
							<i class="fas fa-calculator me-2 text-primary"></i>
							การคำนวณต้นทุน - <?php echo monthYearShowFull($select_year,$select_month); ?>
						</h2>
					</div>

					<?php if(!$is_calc_fc){ ?>
					<div class="col-auto">
						<div class="upload_status"></div>
					</div>			
					<div class="col-12 text-center mt-3">
						<div class="alert alert-info">
							<i class="fas fa-info-circle me-2"></i>
							ระบบยังไม่ได้คำนวณต้นทุนสำหรับเดือนนี้ กรุณาคลิกปุ่ม "คำนวณต้นทุน" เพื่อคำนวณต้นทุนครั้งแรก
						</div>
						<button type="button" class="btn btn-primary btn-lg" onclick="start_calc_cost(1);">
							<i class="bi bi-calculator me-2"></i>คำนวณต้นทุน
						</button>
						<br><br><input type="checkbox" class="form-control form-control-sm collapse" value="1" id="chk_compare"> 
					</div>
					<?php }else{ ?>

					<div class="col-auto ms-auto">
						<button type="button" class="btn btn-primary" onclick="start_calc_cost(0);">
							<i class="bi bi-arrow-clockwise me-2"></i>คำนวณต้นทุนทั้งหมดอีกครั้ง
						</button>
					</div>
					<div class="col-auto">
						<button type="button" class="btn btn-success" onclick="exportCalcResultToExcel();">
							<i class="bi bi-file-earmark-spreadsheet me-2"></i>Export Excel
						</button>
					</div>
					
					<div class="col-auto">
						<div class="upload_status"></div>
					</div>	
					<?php } ?>

				</div>
			</div>
		</div>
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
 