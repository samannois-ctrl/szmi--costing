<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model {

    // Machine configuration
    private $machine_list;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Def_model');
        
        // Machine list from Calc_model
        $this->machine_list = [
            'dbcd4'   => ['title' => 'CD4'],
            'dbcd6'   => ['title' => 'CD6'],
            'dbcd5'   => ['title' => 'CD5'],
            'dbcd6lx' => ['title' => 'CD6LX'],
            'dbk6'    => ['title' => 'CD6 K6']
        ];
    }

    /**
     * Get KPI summary data (Total Sales, Cost, Profit, Average Margin)
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getMarginKPIs($year, $month)
    {
        $total_sale = 0;
        $total_cost = 0;
        $total_profit = 0;
        $count = 0;
        $sum_margin = 0;

        // Query from all machine tables
        foreach ($this->machine_list as $sheet_name => $sheet_info) {
            $table_name = $this->Def_model->getSheetDataTableName('datacost', $sheet_name, '');
            
            $this->db->select('SUM(sale_price) as total_sale, SUM(fg) as total_cost, COUNT(*) as count, SUM(margin_percent) as sum_margin');
            $this->db->where('year', $year);
            $this->db->where('month', $month);
            $this->db->where('margin_percent IS NOT NULL');
            $this->db->where('sale_price IS NOT NULL');
            $this->db->where('fg IS NOT NULL');
            
            $result = $this->db->get($table_name)->row_array();
            
            if ($result) {
                $total_sale += floatval($result['total_sale']);
                $total_cost += floatval($result['total_cost']);
                $count += intval($result['count']);
                $sum_margin += floatval($result['sum_margin']);
            }
        }

        $total_profit = $total_sale - $total_cost;
        $avg_margin = ($count > 0) ? ($sum_margin / $count) : 0;

        // Get previous month for comparison
        $prev_month = $month - 1;
        $prev_year = $year;
        if ($prev_month < 1) {
            $prev_month = 12;
            $prev_year = $year - 1;
        }

        $prev_kpis = $this->getMarginKPIsSimple($prev_year, $prev_month);
        
        // Calculate percentage changes
        $sale_change = 0;
        $profit_change = 0;
        
        if ($prev_kpis['total_sale'] > 0) {
            $sale_change = (($total_sale - $prev_kpis['total_sale']) / $prev_kpis['total_sale']) * 100;
        }
        
        if ($prev_kpis['total_profit'] > 0) {
            $profit_change = (($total_profit - $prev_kpis['total_profit']) / $prev_kpis['total_profit']) * 100;
        }

        return [
            'total_sale' => $total_sale,
            'total_cost' => $total_cost,
            'total_profit' => $total_profit,
            'avg_margin' => $avg_margin,
            'product_count' => $count,
            'sale_change_percent' => $sale_change,
            'profit_change_percent' => $profit_change
        ];
    }

    /**
     * Get simple KPIs without comparison (helper function)
     */
    private function getMarginKPIsSimple($year, $month)
    {
        $total_sale = 0;
        $total_cost = 0;

        foreach ($this->machine_list as $sheet_name => $sheet_info) {
            $table_name = $this->Def_model->getSheetDataTableName('datacost', $sheet_name, '');
            
            $this->db->select('SUM(sale_price) as total_sale, SUM(fg) as total_cost');
            $this->db->where('year', $year);
            $this->db->where('month', $month);
            $this->db->where('margin_percent IS NOT NULL');
            $this->db->where('sale_price IS NOT NULL');
            $this->db->where('fg IS NOT NULL');
            
            $result = $this->db->get($table_name)->row_array();
            
            if ($result) {
                $total_sale += floatval($result['total_sale']);
                $total_cost += floatval($result['total_cost']);
            }
        }

        return [
            'total_sale' => $total_sale,
            'total_cost' => $total_cost,
            'total_profit' => $total_sale - $total_cost
        ];
    }

    /**
     * Get product count by margin ranges
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getMarginDistribution($year, $month)
    {
        $distribution = [
            '0-5'    => 0,
            '5-10'   => 0,
            '10-15'  => 0,
            '15-20'  => 0,
            '20+'    => 0
        ];

        foreach ($this->machine_list as $sheet_name => $sheet_info) {
            $table_name = $this->Def_model->getSheetDataTableName('datacost', $sheet_name, '');
            
            // Count for each range
            $ranges = [
                '0-5'    => ['min' => 0, 'max' => 5],
                '5-10'   => ['min' => 5, 'max' => 10],
                '10-15'  => ['min' => 10, 'max' => 15],
                '15-20'  => ['min' => 15, 'max' => 20],
                '20+'    => ['min' => 20, 'max' => 999]
            ];

            foreach ($ranges as $range_key => $range) {
                $this->db->where('year', $year);
                $this->db->where('month', $month);
                $this->db->where('margin_percent >=', $range['min']);
                $this->db->where('margin_percent <', $range['max']);
                $this->db->where('margin_percent IS NOT NULL');
                
                $count = $this->db->count_all_results($table_name);
                $distribution[$range_key] += $count;
            }
        }

        return $distribution;
    }

    /**
     * Get margin trend for last N months
     * @param int $year
     * @param int $month
     * @param int $months_back
     * @return array
     */
    public function getMarginTrend($year, $month, $months_back = 6)
    {
        $trend = [];
        
        for ($i = $months_back - 1; $i >= 0; $i--) {
            $target_month = $month - $i;
            $target_year = $year;
            
            // Adjust year if month goes below 1
            while ($target_month < 1) {
                $target_month += 12;
                $target_year--;
            }
            
            $sum_margin = 0;
            $count = 0;
            
            foreach ($this->machine_list as $sheet_name => $sheet_info) {
                $table_name = $this->Def_model->getSheetDataTableName('datacost', $sheet_name, '');
                
                $this->db->select('AVG(margin_percent) as avg_margin, COUNT(*) as count');
                $this->db->where('year', $target_year);
                $this->db->where('month', $target_month);
                $this->db->where('margin_percent IS NOT NULL');
                
                $result = $this->db->get($table_name)->row_array();
                
                if ($result && $result['count'] > 0) {
                    $sum_margin += floatval($result['avg_margin']) * intval($result['count']);
                    $count += intval($result['count']);
                }
            }
            
            $avg_margin = ($count > 0) ? ($sum_margin / $count) : 0;
            
            // Thai month names (short)
            $thai_months = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
                           'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            
            $trend[] = [
                'year' => $target_year,
                'month' => $target_month,
                'month_label' => $thai_months[$target_month],
                'avg_margin' => round($avg_margin, 2)
            ];
        }
        
        return $trend;
    }

    /**
     * Get top profitable products
     * @param int $year
     * @param int $month
     * @param int $limit
     * @return array
     */
    public function getTopProfitProducts($year, $month, $limit = 5)
    {
        $all_products = [];
        
        foreach ($this->machine_list as $sheet_name => $sheet_info) {
            $table_name = $this->Def_model->getSheetDataTableName('datacost', $sheet_name, '');
            
            $this->db->select('product_code, customer, margin_percent, sale_price, fg, (sale_price - fg) as profit');
            $this->db->where('year', $year);
            $this->db->where('month', $month);
            $this->db->where('margin_percent IS NOT NULL');
            $this->db->where('sale_price IS NOT NULL');
            $this->db->where('fg IS NOT NULL');
            $this->db->where('sale_price > 0');
            
            $results = $this->db->get($table_name)->result_array();
            
            foreach ($results as $row) {
                $row['machine'] = $sheet_info['title'];
                $row['profit'] = floatval($row['sale_price']) - floatval($row['fg']);
                $all_products[] = $row;
            }
        }
        
        // Sort by profit descending
        usort($all_products, function($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });
        
        // Return top N
        return array_slice($all_products, 0, $limit);
    }

    /**
     * Get low margin products (margin < threshold)
     * @param int $year
     * @param int $month
     * @param float $threshold
     * @return array
     */
    public function getLowMarginProducts($year, $month, $threshold = 6)
    {
        $low_margin_products = [];
        
        foreach ($this->machine_list as $sheet_name => $sheet_info) {
            $table_name = $this->Def_model->getSheetDataTableName('datacost', $sheet_name, '');
            
            $this->db->select('product_code, customer, margin_percent, sale_price, fg');
            $this->db->where('year', $year);
            $this->db->where('month', $month);
            $this->db->where('margin_percent <', $threshold);
            $this->db->where('margin_percent IS NOT NULL');
            $this->db->where('sale_price > 0');
            $this->db->order_by('margin_percent', 'ASC');
            $this->db->limit(10);
            
            $results = $this->db->get($table_name)->result_array();
            
            foreach ($results as $row) {
                $row['machine'] = $sheet_info['title'];
                
                // Calculate suggested price increase
                $current_margin = floatval($row['margin_percent']);
                $target_margin = 15; // Target 15% margin
                $sale_price = floatval($row['sale_price']);
                $cost = floatval($row['fg']);
                
                // New price = Cost / (1 - Target Margin/100)
                $suggested_price = $cost / (1 - ($target_margin / 100));
                $price_increase_percent = (($suggested_price - $sale_price) / $sale_price) * 100;
                
                $row['suggested_price'] = $suggested_price;
                $row['price_increase_percent'] = round($price_increase_percent, 1);
                
                $low_margin_products[] = $row;
            }
        }
        
        // Sort by margin ascending (worst first)
        usort($low_margin_products, function($a, $b) {
            return floatval($a['margin_percent']) <=> floatval($b['margin_percent']);
        });
        
        return array_slice($low_margin_products, 0, 10);
    }

    /**
     * Get average margin grouped by machine
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getMarginByMachine($year, $month)
    {
        $by_machine = [];
        
        foreach ($this->machine_list as $sheet_name => $sheet_info) {
            $table_name = $this->Def_model->getSheetDataTableName('datacost', $sheet_name, '');
            
            $this->db->select('AVG(margin_percent) as avg_margin, COUNT(*) as product_count');
            $this->db->where('year', $year);
            $this->db->where('month', $month);
            $this->db->where('margin_percent IS NOT NULL');
            
            $result = $this->db->get($table_name)->row_array();
            
            $by_machine[] = [
                'machine_code' => strtoupper($sheet_name),
                'machine_name' => $sheet_info['title'],
                'avg_margin' => $result ? round(floatval($result['avg_margin']), 2) : 0,
                'product_count' => $result ? intval($result['product_count']) : 0
            ];
        }
        
        return $by_machine;
    }
}
