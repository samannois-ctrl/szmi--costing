<?php //$view_data_select_year_month = array('select_year'=>$select_year, 'select_month'=>$select_month)
// 
?>
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
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--macos-text);
        margin-bottom: 1rem;
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
                       class="macos-tab-item active">
                        <i class="fas fa-upload me-2"></i>อัพโหลดไฟล์
                    </a>
                    <a href="<?php echo base_url('cost/calc?year='.$select_year.'&month='.$select_month) ?>" 
                       class="macos-tab-item">
                        <i class="fas fa-calculator me-2"></i>คำนวณต้นทุน
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Card -->
        <div class="row">
            <div class="col-12">
                <div class="content-card">
                    <h2 class="section-title">
                        <i class="fas fa-file-upload me-2 text-primary"></i>
                        รายการอัพโหลดไฟล์ - <?php echo monthYearShowFull($select_year,$select_month); ?>
                    </h2>
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="tbl-gen-small">
                            <thead>
                                <tr>
                                    <th style="width: 60px">ลำดับ</th>
                                    <th>ไฟล์</th>
                                    <th>เข้าระบบแล้ว</th>
                                    <th style="width: 120px">อัพโหลด</th>
                                    <th style="width: 120px">ดูข้อมูล</th>
                                </tr>
                            </thead>
                            <tbody>	

                            <?php 
                            $ord=0;
                            foreach ($list_all_upload_files as $k => $f) {
                                $ord++;
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $ord ?></td>
                                    <td><strong><?php echo $f['file_display']  ?></strong></td>
                                    <td>
                                        <?php
                                        if($f['is_uploaded']==1){	?>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <div>
                                                    <a href="<?php echo base_url($f['uploaded_data']['file_path']) ?>" 
                                                       download="<?php echo htmlspecialchars($f['uploaded_data']['file_name']) ?>">
                                                        <?php echo $f['uploaded_data']['file_name'] ?>
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo 'วันที่: '.dateENShort($f['uploaded_data']['upload_dtm'])?>
                                                    </small>
                                                </div>
                                            </div>
                                        <?php }else{?>
                                            <span class="text-muted">-</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo base_url('cost/upload_file_year_month?fc='.$f['file_category'].'&y='.$select_year.'&m='.$select_month) ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-upload me-1"></i>อัพโหลด
                                        </a>
                                    </td>
                                    <td>
                                        <?php if($f['is_uploaded']==1){ ?>
                                            <a href="<?php echo base_url('cost/detail_upload_file_year_month?fc='.$f['file_category'].'&y='.$select_year.'&m='.$select_month) ?>" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye me-1"></i>ดูข้อมูล
                                            </a>
                                        <?php }else{?>
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-eye me-1"></i>ดูข้อมูล
                                            </button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</section>

</div>
