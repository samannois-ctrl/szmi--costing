<link rel="stylesheet" href="<?php echo asset_url('assets/css/argon-custom.css')?>">
<style>
/* Report-specific styles */
.report-container {
    padding: 20px;
}

.report-header {
    background: var(--glass-white-strong);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 16px;
    padding: 25px 30px;
    margin-bottom: 25px;
    box-shadow: var(--shadow-glass);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.report-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--scmi-blue);
    margin: 0;
}

.report-subtitle {
    color: var(--macos-text-secondary);
    font-size: 14px;
    margin-top: 5px;
}

/* KPI Cards */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.kpi-card {
    background: var(--glass-white-strong);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 16px;
    padding: 20px;
    box-shadow: var(--shadow-glass);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-glass-lg);
    background: rgba(255, 255, 255, 0.9);
}

.kpi-icon {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, var(--scmi-blue) 0%, var(--scmi-blue-light) 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 12px;
    box-shadow: 0 4px 15px rgba(5, 51, 181, 0.3);
}

.kpi-label {
    font-size: 13px;
    color: var(--macos-text-secondary);
    margin-bottom: 6px;
    font-weight: 500;
}

.kpi-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--macos-text);
    margin-bottom: 6px;
}

.kpi-change {
    font-size: 12px;
    font-weight: 500;
}

.kpi-change.positive {
    color: #34c759;
}

.kpi-change.neutral {
    color: var(--macos-text-secondary);
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.chart-card {
    background: var(--glass-white-strong);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 16px;
    padding: 20px;
    box-shadow: var(--shadow-glass);
}

.chart-card h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--macos-text);
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.full-width {
    grid-column: 1 / -1;
}

/* Distribution bars */
.distribution-bar {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.distribution-label {
    width: 70px;
    font-size: 13px;
    font-weight: 500;
    color: var(--macos-text);
}

.distribution-bar-container {
    flex: 1;
    height: 28px;
    background: var(--macos-gray-100);
    border-radius: 8px;
    overflow: hidden;
    margin: 0 12px;
    position: relative;
}

.distribution-bar-fill {
    height: 100%;
    border-radius: 8px;
    display: flex;
    align-items: center;
    padding-left: 8px;
    color: white;
    font-weight: 600;
    font-size: 12px;
    transition: width 1s ease;
}

.distribution-count {
    width: 80px;
    text-align: right;
    font-size: 13px;
    font-weight: 600;
    color: var(--macos-text-secondary);
}

.bar-red { background: linear-gradient(90deg, #ff3b30 0%, #ff6b6b 100%); }
.bar-orange { background: linear-gradient(90deg, #ff9500 0%, #ffb84d 100%); }
.bar-yellow { background: linear-gradient(90deg, #ffcc00 0%, #ffd633 100%); }
.bar-light-green { background: linear-gradient(90deg, #34c759 0%, #5dd87f 100%); }
.bar-green { background: linear-gradient(90deg, #30b0c7 0%, #52c9dd 100%); }

/* Chart Container */
.chart-container {
    position: relative;
    height: 280px;
}

.chart-container-small {
    position: relative;
    height: 240px;
}

/* Table Styles */
.report-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
}

.report-table th {
    background: var(--macos-gray-50);
    padding: 10px 12px;
    text-align: left;
    font-weight: 600;
    font-size: 12px;
    color: var(--macos-text-secondary);
    border-bottom: 2px solid var(--macos-gray-200);
}

.report-table td {
    padding: 10px 12px;
    border-bottom: 1px solid var(--macos-gray-200);
    font-size: 13px;
}

.report-table tr:hover {
    background: var(--macos-gray-50);
}

.badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 5px;
    font-size: 11px;
    font-weight: 600;
}

.badge-success {
    background: #d1f4e0;
    color: #0d894f;
}

.badge-warning {
    background: #fff3cd;
    color: #856404;
}

.badge-danger {
    background: #f8d7da;
    color: #721c24;
}

.no-data-message {
    text-align: center;
    padding: 40px 20px;
    color: var(--macos-text-secondary);
}

.no-data-message p {
    margin: 10px 0;
}

/* Responsive */
@media (max-width: 1200px) {
    .kpi-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .content-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .kpi-grid {
        grid-template-columns: 1fr;
    }
    .report-header {
        flex-direction: column;
        gap: 15px;
    }
}

/* Fix for year/month selector visibility */
.report-header .form-control-sm {
    padding: 0.25rem 0.5rem !important;
    height: auto !important;
    min-height: 31px;
}

.report-header .row {
    align-items: center;
}
</style>

<div class="content-wrapper">

<section class="content">
    <div class="container-fluid report-container">
        
        <!-- Header -->
        <div class="report-header">
            <div>
                <h1 class="report-title">📊 รายงานวิเคราะห์กำไร</h1>
                <p class="report-subtitle">Margin Analysis Report</p>
            </div>
            <div>
                <?php $this->load->view('element/select_year_month') ?>
            </div>
        </div>

        <?php if (!$is_file_completed) { ?>
        <!-- No files uploaded message -->
        <div class="chart-card">
            <div class="no-data-message">
                <p class="text-muted">ยังไม่มีการอัพโหลดไฟล์สำหรับเดือนนี้</p>
                <p>กรุณาไปที่หน้า <a href="<?php echo base_url('cost/list?year='.$select_year.'&month='.$select_month) ?>">อัพโหลดไฟล์</a> เพื่ออัพโหลดไฟล์ก่อน</p>
            </div>
        </div>
        
        <?php } elseif (!$is_calc_fc) { ?>
        <!-- Files uploaded but not calculated -->
        <div class="chart-card">
            <div class="no-data-message">
                <p class="text-muted">ระบบยังไม่ได้คำนวณต้นทุนสำหรับเดือนนี้</p>
                <p>กรุณาไปที่หน้า <a href="<?php echo base_url('cost/calc?year='.$select_year.'&month='.$select_month) ?>">คำนวณต้นทุน</a> เพื่อทำการคำนวณ</p>
            </div>
        </div>
        
        <?php } else { ?>
        <!-- Report Content -->
        
        <!-- KPI Cards -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon">📈</div>
                <div class="kpi-label">ยอดขายรวม</div>
                <div class="kpi-value">฿<?php echo number_format($kpis['total_sale'] / 1000000, 2) ?>M</div>
                <div class="kpi-change <?php echo ($kpis['sale_change_percent'] >= 0) ? 'positive' : 'negative' ?>">
                    <?php echo ($kpis['sale_change_percent'] >= 0) ? '↑' : '↓' ?> 
                    <?php echo number_format(abs($kpis['sale_change_percent']), 1) ?>% จากเดือนที่แล้ว
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon">🧮</div>
                <div class="kpi-label">ต้นทุนรวม</div>
                <div class="kpi-value">฿<?php echo number_format($kpis['total_cost'] / 1000000, 2) ?>M</div>
                <div class="kpi-change neutral"><?php echo number_format($kpis['product_count']) ?> รายการ</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon">💰</div>
                <div class="kpi-label">กำไรรวม</div>
                <div class="kpi-value">฿<?php echo number_format($kpis['total_profit'] / 1000000, 2) ?>M</div>
                <div class="kpi-change <?php echo ($kpis['profit_change_percent'] >= 0) ? 'positive' : 'negative' ?>">
                    <?php echo ($kpis['profit_change_percent'] >= 0) ? '↑' : '↓' ?> 
                    <?php echo number_format(abs($kpis['profit_change_percent']), 1) ?>% จากเดือนที่แล้ว
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon">📊</div>
                <div class="kpi-label">Margin เฉลี่ย</div>
                <div class="kpi-value"><?php echo number_format($kpis['avg_margin'], 1) ?>%</div>
                <div class="kpi-change positive">เป้าหมาย 18%</div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-grid">
            <!-- Left Column: Distribution -->
            <div class="chart-card">
                <h3>📊 การกระจายของ Margin</h3>
                <div style="margin-top: 15px;">
                    <?php 
                    $total_products = array_sum($distribution);
                    $max_count = max($distribution);
                    foreach ($distribution as $range => $count) {
                        $percentage = ($total_products > 0) ? ($count / $total_products) * 100 : 0;
                        $width = ($max_count > 0) ? ($count / $max_count) * 100 : 0;
                        
                        $bar_class = '';
                        if ($range == '0-5') $bar_class = 'bar-red';
                        elseif ($range == '5-10') $bar_class = 'bar-orange';
                        elseif ($range == '10-15') $bar_class = 'bar-yellow';
                        elseif ($range == '15-20') $bar_class = 'bar-light-green';
                        else $bar_class = 'bar-green';
                    ?>
                    <div class="distribution-bar">
                        <div class="distribution-label"><?php echo $range ?>%</div>
                        <div class="distribution-bar-container">
                            <div class="distribution-bar-fill <?php echo $bar_class ?>" style="width: <?php echo $width ?>%;">
                                <?php echo number_format($percentage, 0) ?>%
                            </div>
                        </div>
                        <div class="distribution-count"><?php echo number_format($count) ?> รายการ</div>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Right Column: Trend -->
            <div class="chart-card">
                <h3>📈 Margin Trend (6 เดือน)</h3>
                <div class="chart-container-small">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Second Row -->
        <div class="content-grid">
            <!-- Top Products -->
            <div class="chart-card">
                <h3>🏆 Top 5 สินค้ากำไรสูงสุด</h3>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>รหัสสินค้า</th>
                            <th>ลูกค้า</th>
                            <th>Margin</th>
                            <th>กำไร</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($top_products)) { 
                            foreach ($top_products as $product) { ?>
                        <tr>
                            <td><strong><?php echo html_show($product['product_code']) ?></strong></td>
                            <td><?php echo html_show($product['customer']) ?></td>
                            <td><span class="badge badge-success"><?php echo number_format($product['margin_percent'], 1) ?>%</span></td>
                            <td><strong>฿<?php echo number_format($product['profit'], 0) ?></strong></td>
                        </tr>
                        <?php } 
                        } else { ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">ไม่มีข้อมูล</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Low Margin Products -->
            <div class="chart-card">
                <h3>⚠️ สินค้า Low Margin (&lt;6%)</h3>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>รหัสสินค้า</th>
                            <th>ลูกค้า</th>
                            <th>Margin</th>
                            <th>แนะนำ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($low_margin)) { 
                            foreach ($low_margin as $product) { 
                                $badge_class = ($product['margin_percent'] < 3) ? 'badge-danger' : 'badge-warning';
                        ?>
                        <tr>
                            <td><strong><?php echo html_show($product['product_code']) ?></strong></td>
                            <td><?php echo html_show($product['customer']) ?></td>
                            <td><span class="badge <?php echo $badge_class ?>"><?php echo number_format($product['margin_percent'], 1) ?>%</span></td>
                            <td>ปรับราคา +<?php echo number_format($product['price_increase_percent'], 0) ?>%</td>
                        </tr>
                        <?php } 
                        } else { ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">ไม่มีสินค้า Low Margin</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bottom Section: Machine Chart -->
        <div class="chart-card full-width">
            <h3>🏭 Margin แยกตามเครื่องจักร</h3>
            <div class="chart-container">
                <canvas id="machineChart"></canvas>
            </div>
        </div>

        <?php } ?>

    </div>
</section>

</div>
