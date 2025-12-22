<?php
if(!function_exists('set_select_year'))
{
  function set_select_year($y=null)
  {
      if($y === null){
        $y = date('Y');
      }

        $_SESSION['select_year'] = $y;
      



  }
}


if(!function_exists('get_select_year'))
{
  function get_select_year()
  {
   
      if(!isset($_SESSION['select_year'])){
        set_select_year();
        return $_SESSION['select_year'];
      }else{
        return $_SESSION['select_year'];
      }



  }
}

if(!function_exists('get_select_year_from_input'))
{
  function get_select_year_from_input()
  {
   
      if(!isset($_GET['year'])){
        return date('Y');
      }else{
        return $_GET['year'];
      }

  }
}

if(!function_exists('get_select_month_from_input'))
{
  function get_select_month_from_input()
  {
   
      if(!isset($_GET['month'])){
        return date('n');
      }else{
        return $_GET['month'];
      }

  }
}


if(!function_exists('getYearList'))
{
  function getYearList()
  {
   
     $y = [];
     $start_year = 2025;
     $end_year = date('Y')+1;

     for ($i=$start_year; $i <= $end_year ; $i++) { 
       $y[] = $i;
     }



     return $y;
  }
}


if(!function_exists('getMonthList'))
{
  function getMonthList($lang='th')
  {
   
    $m = [];
    if($lang=='th'){
      $m = Array( 1=>"มกราคม",2=>"กุมภาพันธ์",3=>"มีนาคม",4=>"เมษายน",5=>"พฤษภาคม",6=>"มิถุนายน",7=>"กรกฏาคม",8=>"สิงหาคม",9=>"กันยายน",10=>"ตุลาคม",11=>"พฤศจิกายน",12=>"ธันวาคม");
    }else if($lang=='en'){
      $m = Array( 1=>"January",2=>"February",3=>"March",4=>"April",5=>"May",6=>"June",7=>"July",8=>"August",9=>"September",10=>"October",11=>"November",12=>"December");  

    }else if($lang=='abbr-en'){
      $m = Array( 1=>"JAN",2=>"FEB",3=>"MAR",4=>"APR",5=>"MAY",6=>"JUN",7=>"JUL",8=>"AUG",9=>"SEP",10=>"OCT",11=>"NOV",12=>"DEC");  

    }else if($lang=='abbr-th'){
      $m = Array( 1=>"ม.ค.",2=>"ก.พ.",3=>"มี.ค.",4=>"เม.ษ.",5=>"พ.ค.",6=>"มิ.ย.",7=>"ก.ค.",8=>"ส.ค.",9=>"ก.ย.",10=>"ต.ค.",11=>"พ.ย.",12=>"ธ.ค.");  

    }
    return $m;
  }
}

 


if(!function_exists('response_json'))
{
  function response_json($arr)
  {
   

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($arr);
    exit();



  }
}

if(!function_exists('response_json_compress'))
{
  function response_json_compress($arr)
  {
   
       
      $json_data = json_encode($arr);
      
      if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
        // Compress the JSON data using gzencode
        $gzipped_data = gzencode($json_data, 9);
    
        // Set the Content-Encoding header to 'gzip'
        header('Content-Encoding: gzip');
        // Set the Content-Type header to 'application/json'
        header('Content-Type: application/json');
    
        // 6. Output the gzipped data
        echo $gzipped_data;
      } else {
          // If the client does not accept gzip, send uncompressed JSON
          header('Content-Type: application/json; charset=utf-8');
          echo $json_data;
      }

      exit();





  }
}

if(!function_exists('setNullEmpty'))
{
  function setNullEmpty($str)
  {
      if($str === null) return null;
      if (strlen(strval($str)) == 0) {
          return null;
      } else {
          return $str;
      }

  }
}

if(!function_exists('setNullNumber'))
{
  function setEmptyNumberNull($str)
  {
      if($str === null) return null;
      if (strlen(strval($str)) == 0) {
          return null;
      } else {
          return floatval(str_replace(',','',$str));
      }

  }
}



if(!function_exists('setEmptyIsNull'))
{
  function setEmptyIsNull($str)
  {
      if($str === null) return '';
  
      if (strlen(strval($str)) == 0) {
          return '';
      } else {
          return $str;
      }
  
  } 
}



if(!function_exists('setZeroIsEmpty'))
{
  function setZeroIsEmpty($str)
  {
      if($str === null) return 0;
  
      if (strlen(strval($str)) == 0) {
          return 0;
      } else {
          return $str;
      }
  
  } 
}


if(!function_exists('setDateDBValue'))
{
  function setDateDBValue($str)
  {
      if($str === null) return null;

      if (strlen(strval($str)) == 0) {
          return null;
      } else {
          return date('Y-m-d',$str);
      }
  
  } 
}

if(!function_exists('has_val'))
{
  function has_val($str)
  {
      if($str === null) return 0;

      if (strlen(trim(strval($str))) == 0) {
          return 0;
      } else {
          return 1;
      }
  
  } 
}


if(!function_exists('in_arr_has_val_all'))
{
  function in_arr_has_val_all($arr,$str_keys)
  {
      $keys = explode(',',$str_keys);

      if(empty($keys)) return 0;

      foreach ($keys as $n => $key) {
        $key = trim($key);
        if(!(isset($arr[$key]) && has_val($arr[$key]) )){

          return 0;

        }
        
      }

      return 1;
  
  } 
}

if(!function_exists('is_arr_all_not_has_val'))
{
  function is_arr_all_not_has_val($arr)
  {

      if(empty($arr)) return 1;

      foreach ($arr as $k => $v) {
        
        if(has_val(trim(strval($v)))){
          return 0;
        }
        
      }

      return 1;
  
  } 
}



if(!function_exists('dateTHShort'))
{
  function dateTHShort($str)
  {
      if(empty($str)) return '';
      if(trim(strval($str)) == '0000-00-00') return '';
      if(trim(strval($str)) == '0000-00-00 00:00:00') return '';

      $t = strtotime($str);

      $y = floatval(date('Y',$t)) + 543;
      $m = date('n',$t);
      $d = date('j',$t);


      return $d.'/'.$m.'/'.$y;
  
  } 
}


if(!function_exists('dateENShort'))
{
  function dateENShort($str)
  {
      if(empty($str)) return '';
      if(trim(strval($str)) == '0000-00-00') return '';
      if(trim(strval($str)) == '0000-00-00 00:00:00') return '';

      $t = strtotime($str);

      $y = floatval(date('Y',$t));
      $m = date('n',$t);
      $d = date('j',$t);


      return $d.'/'.$m.'/'.$y;
  
  } 
}

if(!function_exists('dateTHShortShort'))
{
  function dateTHShortShort($str)
  {
      if(empty($str)) return '';
      if(trim(strval($str)) == '0000-00-00') return '';
      if(trim(strval($str)) == '0000-00-00 00:00:00') return '';

      $t = strtotime($str);

      $y =  substr(floatval(date('Y',$t)) + 543, 2,2);
      $m = date('n',$t);
      $d = date('j',$t);


      return $d.'/'.$m.'/'.$y;
  
  } 
}

if(!function_exists('datetimeTHShortShort'))
{
  function datetimeTHShortShort($str)
  {
      if(empty($str)) return '';

      $t = strtotime($str);

      $y =  substr(floatval(date('Y',$t)) + 543, 2,2);
      $m = date('n',$t);
      $d = date('j',$t);

      $time = date('H:i',$t);


      return $d.'/'.$m.'/'.$y.' '.$time;
  
  } 
}


if(!function_exists('monthYearShowFull'))
{
  function monthYearShowFull($y,$m)
  {
      
 
      $mon = getTHmonth($m);


      return $mon.' '.$y;
  
  } 
}

if(!function_exists('getDisplayFromExcelFormatEnYear'))
{
  function getDisplayFromExcelFormatEnYear($str,$format)
  {
      
    if(empty($str)) return '';

    $t = strtotime($str);

      if($format=='dd mmm yy'){
        $m_list = getMonthList('abbr-th');

        $y_abbr =  substr(floatval(date('Y',$t)) , 2,2);
        $m = date('n',$t);
        $d = date('j',$t);

        $str_m = (isset($m_list[$m]))?($m_list[$m]):('');
        
        return "$d $str_m $y_abbr";

      }

      if($format=='d/m/Y'){
 
        $y =  date('Y');
        $m = date('n',$t);
        $d = date('j',$t);

       
        return "$d/$m/$y";

      }



    //default


    return date('Y-m-d',$t); 
  
  } 
}




if(!function_exists('strReplaceYMDPattern'))
{
  function strReplaceYMDPattern($pattern,$y,$m,$d=null)
  {
      $res = $pattern;
      if( (strstr($pattern, '[MM-1]') || strstr($pattern, '[MMM-1]'))  &&   intval($m)==1 ){
        $m = 13;
        $y = $y - 1;
       
      }



      $res = str_replace('[YYYY]', $y, $res);
      $res = str_replace('[YY]', substr(strval($y),2,2), $res);
      $res = str_replace('[MM-1]', str_pad(strval(($m-1)), 2, "0", STR_PAD_LEFT ), $res);
      $res = str_replace('[MM]', str_pad(strval($m), 2, "0", STR_PAD_LEFT ), $res);
      $res = str_replace('[MMM-1]', getENmonth(($m-1)), $res);
      $res = str_replace('[MMM]', getENmonth($m), $res);
 
      return $res;
  
  } 
}


if(!function_exists('format_mny'))
{
  function format_mny($str)
  {
      if($str===null) return '';

      if(floatval($str)==0){return '0';}else{return number_format($str,2);}
  
  } 
}

if(!function_exists('format_int'))
{
  function format_int($str)
  {
      if($str===null) return '';


      $str = floatval($str);
      $str = round($str);
      if($str == -0 ) $str=0;
      return number_format($str,0);


  
  } 
}

if(!function_exists('format_num_general'))
{
  function format_num_general($str)
  {
    if($str===null) return '';
    if(trim(strval($str))==='') return '';

      if(is_int($str)) {return number_format($str,0);}

      if(floatval($str)==0){
        return '0';
      }else if(is_int($str)){
        return number_format($str,0);
      }else{
        $str = floatval(str_replace(',','',$str));
        
        //seperate decimal point
        $arr_n = explode('.',strval($str));
        if(count($arr_n) > 1){
          //has decimal point
          $dec = rtrim($arr_n[1],'0');
          if($dec===''){
            //no decimal point
            return number_format($arr_n[0],0);
          }else{
            return number_format($arr_n[0],0).'.'.$dec;
          }

        }else{
          return number_format($str,0);
        }
      }

 
  } 
}



if(!function_exists('format_num_general_no_comma'))
{
  function format_num_general_no_comma($str)
  {
    if($str===null) return '';
    if(trim(strval($str))==='') return '';

    $str = floatval(str_replace(',','',$str));
        
    //seperate decimal point
    $arr_n = explode('.',strval($str));
    if(count($arr_n) > 1){
      //has decimal point
      $dec = rtrim($arr_n[1],'0');
      if($dec===''){
        //no decimal point
        return $arr_n[0];
      }else{
        return $arr_n[0].'.'.$dec;
      }

    }else{
      return $str;
    }

 
  } 
}


if(!function_exists('has_keys_in_array'))
{
  function has_keys_in_array($keys,$arr)
  { 
      if(empty($arr)) return 0;


      $res = 1;
      foreach ($keys as $i => $key) {
        if(!array_key_exists($key,$arr)){
          return 0;
        }
      }
      return 1;


  
  } 
}


if(!function_exists('html_show'))
{
  function html_show($str)
  { 
      return nl2br(htmlspecialchars(strval($str)));


  
  } 
}



if(!function_exists('refineRemoveNoneList'))
{
  function refineRemoveNoneList($is_arr=0,$str1=null,$str2=null,$str3=null,$str4=null)
  { 
    $res = [];
    if(has_val($str1)){
      $res[]=$str1;
    }
    if(has_val($str2)){
      $res[]=$str2;
    }
    if(has_val($str3)){
      $res[]=$str3;
    }
    if(has_val($str4)){
      $res[]=$str4;
    }


    if(empty($res)) $res=null;

    return ($is_arr)?($res):(implode(',', strval($res)));


  }

}



if(!function_exists('removeDuplicateMultiList'))
{
  function removeDuplicateMultiList($is_arr=0,$l1=null,$l2=null,$l3=null,$l4=null)
  { 
     
      $arr1 = (has_val($l1))?(explode(',',strval($l1))):([]);
      $arr2 = (has_val($l2))?(explode(',',strval($l2))):([]);
      $arr3 = (has_val($l3))?(explode(',',strval($l3))):([]);
      $arr4 = (has_val($l4))?(explode(',',strval($l4))):([]);


      

      $res = [];

      $arr_do =  $arr1;
      if(!empty($arr_do)){ 
        foreach ($arr_do as $k => $v) {
          $v = trim(strval($v));
          if(!in_array($v, $res)){
            $res[] = $v;
          }
        }
      }

      $arr_do =  $arr2;
      if(!empty($arr_do)){ 
        foreach ($arr_do as $k => $v) {
          $v = trim(strval($v));
          if(!in_array($v, $res)){
            $res[] = $v;
          }
        }
      }

      $arr_do =  $arr3;
      if(!empty($arr_do)){ 
        foreach ($arr_do as $k => $v) {
          $v = trim(strval($v));
          if(!in_array($v, $res)){
            $res[] = $v;
          }
        }
      }

      $arr_do =  $arr4;
      if(!empty($arr_do)){ 
        foreach ($arr_do as $k => $v) {
          $v = trim(strval($v));
          if(!in_array($v, $res)){
            $res[] = $v;
          }
        }
      }
      

      return ($is_arr)?($res):(implode(',', $res));


  
  } 
}

if(!function_exists('convertArrayKeyId'))
{
  function convertArrayKeyId($arr,$key='id')
  { 
    $res = [];
    foreach ($arr as $k => $value) {
      $res[$value[$key]] = $value;
    }
    
    return $res;


  }

}

if(!function_exists('getValueFromIdxArrayByKey'))
{
  function getValueFromIdxArrayByKey($arr,$key_attr,$key_match,$val_arr)
  { 
    $res = null;

      foreach ($arr as $k => $v) {
        if(isset($v[$key_attr]) && $v[$key_attr]==$key_match){
          if(isset($v[$val_arr])){
            $res = $v[$val_arr];
            return $res;
            
          }
        }
      }
    
    
    return $res;


  }

}




if(!function_exists('getTHmonth'))
{
  function getTHmonth($num=null)
  { 
    $m = Array( 1=>"มกราคม",2=>"กุมภาพันธ์",3=>"มีนาคม",4=>"เมษายน",5=>"พฤษภาคม",6=>"มิถุนายน",7=>"กรกฏาคม",8=>"สิงหาคม",9=>"กันยายน",10=>"ตุลาคม",11=>"พฤศจิกายน",12=>"ธันวาคม");

    if(is_null($num)) return $m;
    if(!has_val($num)) return '';



     if(isset($m[floatval(strval($num))])){
      return $m[floatval(strval($num))];
     }else{
      return '';
     }



  }

}

if(!function_exists('getENmonth'))
{
  function getENmonth($num=null)
  { 
    //$m = Array( 1=>"มกราคม",2=>"กุมภาพันธ์",3=>"มีนาคม",4=>"เมษายน",5=>"พฤษภาคม",6=>"มิถุนายน",7=>"กรกฏาคม",8=>"สิงหาคม",9=>"กันยายน",10=>"ตุลาคม",11=>"พฤศจิกายน",12=>"ธันวาคม");
    $m = getMonthList('abbr-en');
    if(is_null($num)) return $m;
    if(!has_val($num)) return '';



     if(isset($m[floatval(strval($num))])){
      return $m[floatval(strval($num))];
     }else{
      return '';
     }



  }

}

if(!function_exists('getTHAnnualmonth'))
{
  function getTHAnnualmonth($num=null)
  { 
    $m = Array( 10=>"ตุลาคม",11=>"พฤศจิกายน",12=>"ธันวาคม",1=>"มกราคม",2=>"กุมภาพันธ์",3=>"มีนาคม",4=>"เมษายน",5=>"พฤษภาคม",6=>"มิถุนายน",7=>"กรกฏาคม",8=>"สิงหาคม",9=>"กันยายน");

    return $m;

  }

}

if(!function_exists('getMonthListAnnualYear'))
{
  function getMonthListAnnualYear($annual_year)
  { 
    
             //build array month of annual year
            $arr_month_data = [];
            $start_annual_date = strtotime(($annual_year-1)."-10-01");
            $each_start_date = $start_annual_date;
            $timestampnextmonth = 86400*32;

            for ($i=1; $i <= 12 ; $i++) {

                $month_num = date('n',$each_start_date);
                $month_numzerolead = date('m',$each_start_date);
                $each_month = [];
                $each_month['month_num'] = date('n',$each_start_date);
                $each_month['start_date'] = date('Y',$each_start_date)."-".$month_numzerolead."-01";
                $each_month['end_date'] = date('Y',$each_start_date)."-".$month_numzerolead."-".date('t',$each_start_date);
                $each_month['month_name'] = getTHmonth($month_num);
                $each_month['year_th'] =  date('Y',$each_start_date)+543;
                $each_month['year_num'] =  date('Y',$each_start_date);

                $arr_month_data[] = $each_month;
                // echo ">>".($each_start_date) ."-";
                // echo ($timestampnextmonth) ."-";
                // echo ($each_start_date + $timestampnextmonth) ."\n";
                $next_month_draft_ts = $each_start_date + $timestampnextmonth;
                $each_start_date = strtotime(date('Y-m-1',$next_month_draft_ts));


            }

            return  $arr_month_data ;

  }

}

           

if(!function_exists('reset_array_zero'))
{
  function reset_array_zero($arr)
  { 
    $res = [];
    foreach ($arr as $k => $v) {
      $res[$k]=0;
    }

    return $res;

  }

}  


//for print array pretty on web browser
if(!function_exists('print_r_html'))
{
  function print_r_html($arr)
  { 
   
   echo "<pre>";
   print_r($arr);
   echo "</pre>";

  }

}  


// if(!function_exists('strip_sheetname'))
// {
//   function strip_sheetname($name)
//   { 
   
//      // $name = str_replace(' ','',$name);
//      // $name = str_replace('(','',$name);
//      // $name = str_replace(')','',$name);

//      return $name;

//   }

// }  

if(!function_exists('gen_uuid'))
{
  function gen_uuid()
  { 
   
     return uniqid(rand(100,999));

  }

}  

 

if(!function_exists('getCurrentPermissionOfMenuPath'))
{
  function getCurrentPermissionOfMenuPath($path)
  { 
   
     $path = strtolower($path);


     if(isset($_SESSION['permission_menu_path'][$path])){
      return $_SESSION['permission_menu_path'][$path];
     }


     return 0;

  }

}  



if(!function_exists('getCurrentAccessMenuPath'))
{
  function getCurrentAccessMenuPath($path)
  { 
   
     $path = strtolower($path);

     
     if(isset($_SESSION['permission_info'] ['permission_menu_path'][$path])){
      $p = $_SESSION['permission_info']['permission_menu_path'][$path];
      if($p=='1' || $p=='2'){
        return 1;
      }
     }


     return 0;

  }

}  



if(!function_exists('checkCurrentIsWriteMenu'))
{
  function checkCurrentIsWriteMenu($path)
  { 
   
     $path = strtolower($path);


     if(isset($_SESSION['permission_info']['permission_menu_path'][$path])){
      $p = $_SESSION['permission_info']['permission_menu_path'][$path];
      if($p=='2'){
        return 1;
      }
     }


     return 0;

  }

} 



if(!function_exists('verifyWriteMenuApi'))
{
  function verifyWriteMenuApi($path,$fail_msg='not_write')
  { 
    
      if(! checkCurrentIsWriteMenu($path) ) { 
            $r = [];
            $r['result'] = $fail_msg;
          
            response_json($r);
            exit();
        }
 
  }

} 



if(!function_exists('verifyAccessMenu'))
{
  function verifyAccessMenu($path)
  { 
    
      if( !getCurrentAccessMenuPath($path) ) { 

       
            $CI =& get_instance();
            echo $CI->load->view('_error_notaccess',null,true);
            exit;
        }
 
  }

} 


if(!function_exists('showNotEditable'))
{
  function showNotEditable($is_editable=0)
  { 
    if(!$is_editable){
      $CI =& get_instance();
      echo $CI->load->view('_error_notaccess',null,true);
      exit;
    }
 
  }

} 

if(!function_exists('getDistinctValueFromArray'))
{
  function getDistinctValueFromArray($arr,$key)
  { 
    
    $d = [];
    foreach ($arr as $a => $v) {
      
        if(isset($v[$key]) && has_val($v[$key]) ){
          if(!in_array($v[$key], $d)){
            $d[]=$v[$key];
          }
        }





    }

    sort($d);

    return $d;
  }

} 
  
if(!function_exists('getEmptyArrayIfNull'))
{
  function getEmptyArrayIfNull($arr)
  { 
    
    if($arr===null){
      return [];
    }

    return $arr;
  }

} 





if(!function_exists('strTHDateToDate'))
{
  function strTHDateToDBDate($str)
  { 
      
      $arr = explode('/',$str);

      if(count($arr) < 3 ) return null;


      $d = $arr[0];
      $m = $arr[1];
      $th_y = $arr[2];

      $y =  $th_y - 543;


      return $y."-".$m."-".$d;


    
  }

} 






if(!function_exists('asset_url'))
{
  function asset_url($str)
  { 

    //return  base_url($str);
      
     if(1){// strpos('.css',$str) !== false || strpos('.js',$str) !== false ){

      $url = str_replace('/index.php', '', base_url());
      return $url.$str;
     }else{
        return  base_url($str);
     }

     
    
  }

} 

if(!function_exists('msg_array_to_html'))
{
  function msg_array_to_html($arr)
  { 

    
    $html = '<ul><li>'.implode('</li><li>', $arr).'</li></ul>';

    return $html;   

     
    
  }

} 

if(!function_exists('checkStringHasAnyWord'))
{

  function checkStringHasAnyWord($str, $arr_search)
  {
      foreach ($arr_search as $word) {
          //echo "check ($word) in ($str)";echo "=>".(stripos($str, $word)).'<br>';
          if (stripos($str, $word) !== false) {
            //echo "found***";echo '<br>';
              return true;
          }
      }
      return false;
  }

}

if(!function_exists('in_array_ci'))
{

function in_array_ci($needle, $haystack) {
  return in_array(strtolower($needle), array_map('strtolower', $haystack));
}
}


if(!function_exists('str_my_compress'))
{
function str_my_compress($str) {
  return  gzcompress($str);
}
}

if(!function_exists('str_my_decompress'))
{
function str_my_decompress($str) {
  return  gzuncompress($str);
}
}



if(!function_exists('copy_waiting_to_file_uploaded'))
{

function copy_waiting_to_file_uploaded($waiting_file_path)
{
    
		//move waiting file to upload file
		$upload_file_path = "uploads/file_uploaded/".date('Ym');
		$upload_file_path_file = $upload_file_path."/". pathinfo($waiting_file_path)['basename'];
		//build directory month year
		// Check if the directory already exists
		if (!file_exists($upload_file_path)) {
			if (mkdir($upload_file_path, 0755)) {				 
			} else {
        return '';
      }
		}  




		if(copy(FCPATH.$waiting_file_path,FCPATH.$upload_file_path_file	)){
      return $upload_file_path_file;
    }

    return '';


}
}


if(!function_exists('delete_oldfile_in_dir'))
{


function delete_oldfile_in_dir($dir, $max_sec) {

  $allow_dirs = [];
  $allow_dirs[]=FCPATH."uploads/waiting";

  if( ! in_array($dir,$allow_dirs)){
    return 0;
  }

  $list = array();
  
  $limit = time() - $max_sec;
  
  $dir = realpath($dir);
  
  if (!is_dir($dir)) {
    return 0;
  }
  
  $dh = opendir($dir);
  if ($dh === false) {
    return 0;
  }
  
  while (($file = readdir($dh)) !== false) {
    $file = $dir . '/' . $file;
    if (!is_file($file)) {
      continue;
    }
    
    if (filemtime($file) < $limit) {
      $list[] = $file;
      unlink($file);
    }
    
  }
  closedir($dh);
  return $list;

}

}



if(!function_exists('compare_cell_header'))
{


function compare_cell_header($str1, $str2) {


  $s1 = preg_replace('/\s+/', '', strtolower($str1));
  $s2 = preg_replace('/\s+/', '', strtolower($str2));

  return $s1 == $s2;


}

}



function display_memory(){
  echo "Current memory usage: " . memory_get_usage() . " bytes\n";
  echo "Current real memory usage: " . memory_get_usage(true) . " bytes\n";
  exit;
}







if(!function_exists('convertExcelNumToTimestamp'))
{


function convertExcelNumToTimestamp($excel_date_num) {

  if(!has_val($excel_date_num)){
    return null;
  }

  //$excelDateNumber = 44186; // Example Excel date number (e.g., for December 25, 2020)
  $unixEpochOffset = 25569; // Number of days from Jan 1, 1900 to Jan 1, 1970
  
  $daysSinceUnixEpoch = $excel_date_num - $unixEpochOffset;
  
  return $daysSinceUnixEpoch;


}

}


if(!function_exists('convertExcelNumToDBDate'))
{

function convertExcelNumToDBDate($excel_date_num) {

  if(!has_val($excel_date_num)){
    return null;
  }

  return date('Y-m-d',convertExcelNumToTimestamp($excel_date_num));

}

}


if(!function_exists('convertExcelNumToDBDateTime'))
{

function convertExcelNumToDBDateTime($excel_date_num) {

  if(!has_val($excel_date_num)){
    return null;
  }

  return date('Y-m-d H:i:s',convertExcelNumToTimestamp($excel_date_num));

}

}

if(!function_exists('excel_to_phpdate'))
{


function excel_to_phpdate($date_value = 0, $format = null)
{
    /**
     * Number of days between the beginning of serial date-time (1900-Jan-0)
     * used by Excel and the beginning of UNIX Epoch time (1970-Jan-1).
     */
    $days_since_1900 = 25569;

    if ($date_value < 60) {
        --$days_since_1900;
    }

    /**
     * Values greater than 1 contain both a date and time while values lesser
     * than 1 contain only a fraction of a 24-hour day, and thus only time.
     */
    if ($date_value >= 1) {
        $utc_days = $date_value - $days_since_1900;
        $timestamp = round($utc_days * 86400);

        if (($timestamp <= PHP_INT_MAX) && ($timestamp >= -PHP_INT_MAX)) {
            $timestamp = (integer) $timestamp;
        }
    } else {
        $hours = round($date_value * 24);
        $mins = round($date_value * 1440) - round($hours * 60);
        $secs = round($date_value * 86400) - round($hours * 3600) - round($mins * 60);
        $timestamp = (integer) gmmktime($hours, $mins, $secs);
    }

    return $format ? date($format, $timestamp) : $timestamp;
}

}




if(!function_exists('downloadfiletmp'))
{


function downloadfiletmp($filenamepath,$saveasfilename)
{

$filename = $filenamepath; // Replace with the actual path to your temporary file
$download_name = $saveasfilename; // The name the user will see for the downloaded file

// Check if the file exists
if (!file_exists($filename)) {
    die('Error: File not found.');
}

// Set HTTP headers for download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream'); // Or the correct MIME type for your file
header('Content-Disposition: attachment; filename="' . basename($download_name) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filename));

// Clear output buffer if necessary
if (ob_get_level()) {
    ob_end_clean();
}

// Read the file and output its content
readfile($filename);

// Exit to prevent further output
exit;

}

}

if(!function_exists('roundByCustomNum'))
{

function roundByCustomNum($number,$decimal_mark=0.5) {

  if(!is_numeric($number)){
    return null;
  }
  if(is_null($number)){
    return null;
  }


  $floor = floor($number);
  $decimal = $number - $floor;
  if ($decimal >= $decimal_mark) {
      return ceil($number);
  } else {
      return $floor;
  }
}


}


if(!function_exists('remove_after_space'))
{

function remove_after_space($str) {

  // Find the position of the first space
  $str = trim(strval($str));
  $position = strpos($str, ' ');

  // Extract the part of the string before the space
  if ($position !== false) {
      $result = substr($str, 0, $position);
  } else {
      // If no space is found, the original string is returned
      $result = $str;
  }
  return $result;

}

}


if(!function_exists('strip_sheetname'))
{
  function strip_sheetname($name,$invalidCharacters)
  { 
          
     $name = str_replace($invalidCharacters, '', $name);

     return mb_substr($name,0,25,'UTF-8');

  }

}  

?>