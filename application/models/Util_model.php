<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties] 
class Util_model extends CI_Model {



	public function __construct()

	{
		parent::__construct();
  
	}
 

    public function convertRawColumnToDisplay($col_type,$col_val,$format_cell=''){

        $display_val = $col_val;
        
        if($col_type == 'DECIMAL' || $col_type == 'DOUBLE'){
            if( !empty($format_cell) && $format_cell=='_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)'){
                if($col_val===0){
                    $display_val = '-';
                }else if(empty($col_val)){
                    $display_val = null;
                }else {
                    $display_val = format_mny($col_val);
                }
            }
            if( !empty($format_cell) && $format_cell=='_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)'){
                if($col_val===0){
                    $display_val = '-';
                }else if(empty($col_val)){
                    $display_val = null;
                }else {
                    $display_val = round(floatval($col_val),-1,PHP_ROUND_HALF_UP);
                }
            }
            

            if( !empty($format_cell) && $format_cell=='accounting_2'){
                $display_val = format_mny($col_val);
            }

            if( !empty($format_cell) && $format_cell=='percent_0'){

                if($col_val===null || $col_val===''){
                    $display_val = null;
                }else{
                    $display_val = format_int($col_val).'%';
                }
                
            }


            if( !empty($format_cell) && $format_cell=='general_num'){
                $display_val = format_num_general($col_val);
            }
            if( empty($format_cell)){
                $display_val = format_num_general_no_comma($col_val);
            }

        }else if($col_type == 'INT'){

            $display_val = format_int($col_val);

            if( !empty($format_cell) && $format_cell=='_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)'){
                if($col_val===0){
                    $display_val = '-';
                }else if(empty($col_val)){
                    $display_val = null;
                }else {
                    $display_val = format_mny($col_val);
                }
            }

            if( !empty($format_cell) && $format_cell=='_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)'){
                if($col_val===0){
                    $display_val = '-';
                }else if(empty($col_val)){
                    $display_val = null;
                }else {
                    $display_val = round(floatval($col_val),-1,PHP_ROUND_HALF_UP);
                }
            }
            
            if( !empty($format_cell) && $format_cell=='general_num'){
                $display_val = format_num_general($col_val);
            }

            if( empty($format_cell)){
                $display_val = format_num_general_no_comma($col_val);
            }



        }else if($col_type == 'DATE'){

            if($format_cell=='dd mmm yy'){

                $display_val =  getDisplayFromExcelFormatEnYear($col_val,$format_cell);

            }else if ($format_cell=='d/m/Y'){
                $display_val =  getDisplayFromExcelFormatEnYear($col_val,$format_cell);
            }
            
        }


        return $display_val;

    }


    public function extractWHFromPaperSize($paper_size_str,$type='inch'){

        //$str = '25.00"X30.00" 350 GSM';
        $paper_size_str = strval($paper_size_str);
        $size = [];

        if($type=='mm'){
            $pattern =   '/(\d+(?:\.\d+)?)\s*[xX]\s*(\d+(?:\.\d+)?)(?:\s*(mm|cm|"))?/i';
        }else if($type=='inch'){
            $pattern =   '/(\d+(?:\.\d+)?)"\s*[xX]\s*(\d+(?:\.\d+)?)"/';
        }else{ 
            //default inch
            $pattern =   '/(\d+(?:\.\d+)?)"\s*[xX]\s*(\d+(?:\.\d+)?)"/';
        }  

        if (preg_match($pattern, $paper_size_str, $matches)) {
            $size['1'] = $matches[1];
            $size['2'] = $matches[2];
        } else {
            $size['1'] = null;
            $size['2'] = null;
        }

        return $size;
    }
 
}//end class