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

.filter-row .col-auto{
	padding-top:10px;
}

.tbl-form-pos td{
	padding-top: 10px;
}

/* Sheet Tabs Styling */
.sheet-tabs-wrapper {
	scrollbar-width: thin;
	scrollbar-color: rgba(255,255,255,0.3) transparent;
}

.sheet-tabs-wrapper::-webkit-scrollbar {
	height: 6px;
}

.sheet-tabs-wrapper::-webkit-scrollbar-track {
	background: transparent;
}

.sheet-tabs-wrapper::-webkit-scrollbar-thumb {
	background: rgba(255,255,255,0.3);
	border-radius: 3px;
}

.sheet-tab-item {
	flex-shrink: 0;
}

.sheet-tab-item .btn-sheet-link {
	display: block;
	padding: 0.5rem 1rem;
	color: rgba(255,255,255,0.7);
	background: transparent;
	border: none;
	border-radius: 6px;
	font-size: 0.9rem;
	font-weight: 500;
	cursor: pointer;
	transition: all 0.2s ease;
	white-space: nowrap;
	text-decoration: none;
}

.sheet-tab-item .btn-sheet-link:hover {
	background: rgba(255,255,255,0.1);
	color: rgba(255,255,255,0.9);
}

.sheet-tab-item.active .btn-sheet-link {
	background: var(--macos-white);
	color: var(--macos-text);
	box-shadow: var(--shadow-sm);
}

/* Calc Toolbar */
.calc-toolbar .form-control:focus {
	border-color: var(--scmi-blue);
	box-shadow: 0 0 0 3px rgba(5, 51, 181, 0.1);
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

	<!-- Sheet Tabs and Grid -->
	<section class="content" style="padding:0;<?php if(!$is_calc_fc){ ?> display:none; <?php } ?>">

		<div class="row">
			<div class="col-12">
				<div class="content-card" style="padding: 0; overflow: hidden;">
					<!-- Sheet Tab Header -->
					<div class="sheet-tabs-wrapper" style="background: linear-gradient(135deg, #1d1d1f 0%, #2d2d30 100%); padding: 0.75rem 1.5rem; display: flex; gap: 0.5rem; overflow-x: auto;">
						<?php 
						if(!empty($list_sheet)){
							$active_sheet_name = $list_sheet[0]['real_sheet_name_on_excel'];
						}else{
							$active_sheet_name = '';
						}
						foreach ($list_sheet as $idx => $sheet) { 
						?>
							<div class="sheet-tab-item div-sheet-<?php echo $sheet['sheet']?> <?php echo ($idx==0)?('active'):('');?>" 
							data-sheet="<?php echo $sheet['sheet']?>"
							data-real_sheet_name_on_excel="<?php echo html_show($sheet['real_sheet_name_on_excel'])?>">
								<a class="btn-sheet-link" onclick="clickBtnSheet(this)" data-sheet="<?php echo $sheet['sheet']?>">
									<?php echo html_show($sheet['real_sheet_name_on_excel']) ?>
								</a>
							</div>
						<?php } ?>
					</div>
					
					<!-- Toolbar -->
					<div class="calc-toolbar" style="background: var(--macos-gray-50); padding: 1rem 1.5rem; border-bottom: 1px solid var(--macos-gray-200);">
						<div class="row align-items-center">
							<div class="col-auto">
								<button class="btn btn-success btn-sm" onclick="saveSheetRecalc()" title="บันทึก">
									<i class="bi bi-save me-1"></i>บันทึกชีทนี้
								</button>
							</div>
							
							<div class="col-auto ms-auto">
								<div class="d-flex align-items-center gap-2">
									<label class="mb-0 text-muted" style="font-size: 0.9rem;">ค้นหา:</label>
									<input class="form-control form-control-sm" style="width: 250px;" type="text" id="filter-text-box" placeholder="Filter..." oninput="onFilterTextBoxChanged()" />
								</div>
							</div>
						</div>
					</div>

					<!-- Grid Content -->
					<div id="sheet-calc-content" style="height: calc(100vh - 400px); background: var(--macos-white);"></div>
				</div>
			</div>
		</div>
	
	</section>

	<div class="compare-table"></div>

</section>

</div>