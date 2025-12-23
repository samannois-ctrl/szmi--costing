
<!-- jQuery -->
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/jquery/jquery.min.js')?>"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/jquery-ui/jquery-ui.min.js')?>"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<!-- <script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.min.js')?>"></script> -->
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js')?>"></script>
<!-- ChartJS -->
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/chart.js/Chart.min.js')?>"></script>
<!-- Sparkline -->
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/sparklines/sparkline.js')?>"></script>
<!-- JQVMap -->
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/jqvmap/jquery.vmap.min.js')?>"></script>
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/jqvmap/maps/jquery.vmap.usa.js')?>"></script>
<!-- jQuery Knob Chart -->
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/jquery-knob/jquery.knob.min.js')?>"></script>
<!-- daterangepicker -->
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/moment/moment.min.js')?>"></script>

<!-- <script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/daterangepicker/daterangepicker.js')?>"></script> -->
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')?>"></script>
<!-- Summernote -->
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/summernote/summernote-bs4.min.js')?>"></script>
<!-- overlayScrollbars -->
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/dist/js/adminlte.js')?>"></script>


<!-- SweetAlert2 -->
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/sweetalert2/sweetalert2.all.min.js')?>"></script>
<!-- Toastr -->
<script src="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/toastr/toastr.min.js')?>"></script>


<!-- jsGrid -->
<script src="<?php echo asset_url('assets/js/jsgrid-1.5.3/dist/jsgrid.js?v=23');?>"></script>



<!-- Datatable -->
<script src="<?php echo asset_url('assets/js/Datatable-2.1.8/datatables.min.js');?>"></script>


<script src="<?php echo asset_url('assets/js/bootstrap-datepicker/dist/js/bootstrap-datepicker-custom-thyear.js')?>"></script>
<script src="<?php echo asset_url('assets/js/bootstrap-datepicker/dist/js/jquery-dateformat.min.js')?>"></script>
<script src="<?php echo asset_url('assets/js/bootstrap-datepicker/dist/locales/bootstrap-datepicker.th.min.js')?>"></script>

<!-- multi-select-js -->
<script src="<?php echo asset_url('assets/js/multiple-select-1.7.0/dist/multiple-select.min.js?v=1')?>"></script>


<!-- AutoNumeric -->
<script src="<?php echo asset_url('assets/js/autoNumeric-4.10.7/autoNumeric.min.js?v=1')?>"></script>


<!-- datatables-buttons-excel-styles -->
<script src="<?php echo asset_url('assets/js/datatables-buttons-excel-styles/js/buttons.html5.styles.js')?>"></script>
<script src="<?php echo asset_url('assets/js/datatables-buttons-excel-styles/js/buttons.html5.styles.templates.js')?>"></script>

<!-- AG Grid -->
<!-- <script src="https://cdn.jsdelivr.net/npm/ag-grid-community@34.2.0/dist/ag-grid-community.min.js"></script> -->
<script src="<?php echo asset_url('assets/js/ag-grid/dist/ag-grid-community.js?v=2')?>"></script>
 

<script>

//set add extra header api
$.ajaxSetup({
    headers: {
        'AppReqType': 'api',
        
    }
});


var excel_col =  ['','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ'];



function apiLogout(){
  window.location.href = "<?php echo base_url('login/out') ?>";
}

  $(function () {
  $('[data-toggle="tooltip"]').tooltip()
})



function getMonthList(lang='th')
  {
   
    m = [];
    if(lang=='th'){
      m = { 1:"มกราคม",2:"กุมภาพันธ์",3:"มีนาคม",4:"เมษายน",5:"พฤษภาคม",6:"มิถุนายน",7:"กรกฏาคม",8:"สิงหาคม",9:"กันยายน",10:"ตุลาคม",11:"พฤศจิกายน",12:"ธันวาคม"};
    }else if(lang=='en'){
      m = { 1:"January",2:"February",3:"March",4:"April",5:"May",6:"June",7:"July",8:"August",9:"September",10:"October",11:"November",12:"December"};  

    }else if(lang=='abbr-en'){
      m = { 1:"Jan",2:"Feb",3:"Mar",4:"Apr",5:"May",6:"Jun",7:"Jul",8:"Aug",9:"Sep",10:"Oct",11:"Nov",12:"Dec"};  

    }else if(lang=='abbr-th'){
      m = { 1:"ม.ค.",2:"ก.พ.",3:"มี.ค.",4:"เม.ษ.",5:"พ.ค.",6:"มิ.ย.",7:"ก.ค.",8:"ส.ค.",9:"ก.ย.",10:"ต.ค.",11:"พ.ย.",12:"ธ.ค."};  

    }
    return m;
  }

function dateToTHDateShort(value){
     if(  value === null) return '';

     if(isEmptyString(value)) return '';

      var yth = String(parseInt($.format.date(new Date(value),'yyyy')) + 543);
			
			
      return $.format.date(new Date(value),'dd/MM/') + yth;


}

function dateToTHDateShortShort(value){

if(  value === null) return '';

if(isEmptyString(value)) return '';

// console.log(value);

 var yth = String(parseInt($.format.date(new Date(value),'yyyy')) + 543).substr(2, 2);
 
 
 return $.format.date(new Date(value),'d/M/') + yth;


}

function formatMoney(n){


  if(  n === null) return '';
  if(isEmptyString(n)) return '';


  n = parseFloat(n).toFixed(2);
  
  return Number(n).toLocaleString('en-US', {style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2});


  


}


function formatIntegerNumber(n){


  if(  n === null) return '';
  if(isEmptyString(n)) return '';


  n = parseFloat(n).toFixed(0);
  if(n==-0) n=0;
  
  return Number(n).toLocaleString('en-US', {style: 'decimal', minimumFractionDigits: 0, maximumFractionDigits: 0});


  


}


function  isEmptyString(str){

  if(str===null) return true;
  
  if(String(str).trim().length == 0){
    return true;
  }else{
    return false;
  }
}



function nl2br (str, is_xhtml) {   
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

function HtmlEncode(s)
{
  var el = document.createElement("div");
  el.innerText = el.textContent = s;
  s = el.innerHTML;
  return s;
}


function exceed_limit_date(s,e,max_day){


  var diff  = (new Date(e) - new Date(s)) / 86400000; //days
  console.log(diff,'days');

  if(diff > max_day){
    return 1;
  }else{
    return 0;
  }



}


function formatBytes(bytes, decimals = 2) {
    if (!+bytes) return '0 Bytes'

    const k = 1024
    const dm = decimals < 0 ? 0 : decimals
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']

    const i = Math.floor(Math.log(bytes) / Math.log(k))

    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`
}


function genCurrentTransactionUser($user_id){



  return String($user_id) + String(Date.now());
}

function msToPrettyTime(mst){


  var  ms = mst % 1000;
  var  ss = Math.floor(mst / 1000) % 60;
  var  mm = Math.floor(mst / 1000 / 60) % 60;
  var  hh = Math.floor(mst / 1000 / 60 / 60);

  //console.log(`${diff}ms = ${hh}hr, ${mm}min, ${ss}sec, ${ms}ms`);
  var text = '';
  if(hh > 0){ text += hh + ' ชม.'}
  if(mm > 0){ text += mm + ' นาที '}
  if(ss > 0){ text += ss + ' วินาที '}
  if(hh == 0 && mm==0 && ss == 0){ text += ms + ' มิลลิวินาที '}


    return text;
}


function strtonum(str) {
  var str = String(str).trim();
  if(str.length == 0) return 0;
  // var num = Number(str);

  var num = Number(String(str).replace(/,/g, ''));
// console.log(output)



  return isNaN(num)?(0):(num);
}

function getPath($el) {
  var node = $el;
  var path;

  while (node.length) {
    var realNode = node.get(0);
    var name = realNode.localName;

    // Qualify as much as possible
    if(realNode.id) {
      name += '#' + realNode.id;
    } else if(realNode.className) {
      name += '.' + realNode.className;
    }

    if(!name) break;
    name = name.toLowerCase();

    // Make the selector unique at all costs
    siblings = node.siblings(name)
    if(siblings.length > 1) {
      var index = node.index() + 1;
      if(index > 1) {
        name += ':nth-child(' + index + ')';
      }
    }

    path = name + (path ? '>' + path : '');
    node = node.parent();
  }

  return path;
}




function updateDisplayProfileName(){


  


    $.post('<?php echo base_url('user/getMyUserName')?>',null,function(data){ 

    //console.log('call getDebtTable')

      if(data.result == 'success'){

          $('.nav-bar-user-name').text(data.user_name);

      }else if( data.result == 'logout'){
          apiLogout();
      }

    });

}


function set_year_session(el){

    var y = $(el).val();

    $.post('<?php echo base_url('util/setyearsession')?>',{'year':y},function(data){ 

    //console.log('call getDebtTable')

      if(data.result == 'success'){

          $('.nav-bar-user-name').text(data.user_name);

       



      }

    });

  

}


function getFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

 
function convertToNumber(str,allownull=0){

  //console.log('convert' + str);
  str = String(str);
  if(isEmptyString(str)){
    if(allownull==1)
      return null;
    else
      return 0;
  }else{
    return parseFloat(str.replace(/\,/g,''));
  }


}

function setEmpty(el){

  $(el).val('');


}

function nltobr(str){
  return str.replace(/(?:\r\n|\r|\n)/g, '<br>');
}


//generate random string for unique data in page
function getNewRandomId(){

    return Math.random().toString(36).slice(2) + Math.random().toString(36).slice(2);


}

function roundBeforeDecimal(num, digit) {
  const numStr = String(num);
  const integerPart = numStr.split('.')[0];
   
  const precision = Math.pow(10, digit);
  return Math.round(num / precision) * precision;
}

function formatNumGeneral(str,comma=1){
  let num = parseFloat(str);
  if(str===null) return '';
  if(isEmptyString(str)) return '';
  if(isNaN(num)) return '';

  let arrNum = String(num).split('.');
  let section1 = arrNum[0];
  if(comma==0){
     section1 = arrNum[0];
  }else{
     section1 = Number(num).toLocaleString('en-US', {style: 'decimal', minimumFractionDigits: 0, maximumFractionDigits: 0})
  }


  if(arrNum.length>1){
    return section1 + '.' + arrNum[1];
  }else{
    return section1;
  }
  
}

//SHARE FUNCTION ===============

function build_tab_sheet_from_json(json_sheets,tab_color='#3b7749',bottom_offset='300px'){

  let html = "";
  // console.log(json_sheets);

  //card div start ----
  //tab-head-start----
  html += '<div class="card card-success card-tabs tab-all-sheets" style="box-shadow:unset;">' +
              '<div class="card-header p-0 pt-1" style="background-color: '+tab_color +';">'+
                '<ul class="nav nav-tabs tab-condense" id="preview-upload-tab-head" role="tablist">';
  let count_tab = 1;
  $.each(json_sheets, function(sheet_name,sheet_data){

    if(typeof sheet_data.is_found_in_db !== 'undefined' && sheet_data.is_found_in_db==0){
      return;
    }

    html += '<li class="nav-item" >' +
            '<a class="nav-link ' +  ((count_tab==1)?(' active '):('')) + 'text-center" id="sheet_preview'+count_tab+'-tab" data-toggle="pill" href="#sheet_preview'+count_tab+'" role="tab" aria-controls="sheet_preview'+count_tab+'" aria-selected="true">' + 

            ((sheet_data.is_found_in_excel==1)?(sheet_data.real_sheet_name_on_excel):(sheet_data.excel_sheet_name))

            // <!-- display error -->

             if(sheet_data.is_found_in_excel==0){

              html +='<span class="badge badge-pill" style="background:#ffffff;color:#ff0000;">ไม่พบใน Excel</span>';

             }else if( typeof sheet_data.compact_result.is_valid_sheet !== 'undefined' && sheet_data.compact_result.is_valid_sheet==0){
              html +='<span class="badge badge-pill" style="background:#ffffff;color:#ff0000;">อ่านข้อมูลไม่ได้</span>';
             
             }
            
        
    html +='</a>' +
           '</li>';
 
          count_tab++;       
  });

  html +='</ul>' +
        '</div>';
 //tab-head-end----


  //tab content start ----            
  html +='<div class="card-body pl-0 pr-0 pt-1 pb-1">';
  html +=  '<div class="tab-content" id="preview-upload-tab-content">';


  count_tab = 1;
  $.each(json_sheets, function(sheet_name,sheet_data){

    let is_active = (count_tab==1)?(1):(0);

    
    if(typeof sheet_data.is_found_in_db !== 'undefined' && sheet_data.is_found_in_db==0){
      return;
    }


    //tab-centent-sheet
    html +='<div class="tab-pane  fade ' +  ((count_tab==1)?(' show active '):('')) + '" id="sheet_preview'+count_tab+'" role="tabpanel" aria-labelledby="sheet_preview'+count_tab+'-tab">';
                    
                     if(sheet_data.is_found_in_excel==1) {  
                         if(sheet_data.compact_result.is_valid_sheet==1) {  

                            html += build_tab_table_from_sheet_data(sheet_name,sheet_data,bottom_offset);
                            //html += '<b>'+sheet_name+'</b>';

                           }else{ 
                            
                            html +=' <span class="text-danger">' + sheet_data.compact_result.msg +'</span>';
  
                         } 
                     }else{

                        html += '<span class="text-danger">ไม่พบ Sheet ใน Excel ที่อัพโหลดมา </span>';
                    } 


    count_tab++;

    html +='</div>';

  });

  html +=  '</div>';
  html +='</div>';
  //tab content end ----    
 
  return html;
}



function build_tab_table_from_sheet_data(sheet_name,sheet_data,bottom_offset){
  // <!-- TAB TABLE PANEL START-->

  //start - nav head ===
  let html = '<nav>' + 
              '<ul class="nav nav-tabs tab-condense" id="nav-'+sheet_name+'-tab" role="tablist">';

  let count_tbl_tab = 1;
  // console.log(sheet_data.def_tables);

      $.each(sheet_data.compact_result.tables,function(tbl_name,each_table){

        // console.log(each_table);

        let table_of_sheet_id = sheet_name+"_"+tbl_name;
        let suffix_table_msg = '';
        if(each_table.is_valid_table==0){
          suffix_table_msg = "ตารางไม่ถูกต้อง - " + each_table.msg;
        }


        html += '<li class="nav-item">'+      
                  '<a class="nav-link tab-sub-table ' +  ((count_tbl_tab==1)?(' active '):('')) + '" id="tbl-'+table_of_sheet_id+'-tab" data-toggle="tab" data-target="#tbl-'+table_of_sheet_id+'-content" type="button" role="tab" aria-controls="tbl-'+table_of_sheet_id+'-tab" aria-selected="true">'+
                  
                  ((tbl_name=='main')?('ตาราง'):('ตาราง '+each_table.table_title)) + suffix_table_msg ;
                  
                  
        html += '</a>' +
                '</li>';



        count_tbl_tab++;
      });



       html += ' </ul>'+
              '</nav>';
  //end - nav head ===


  //start - tab content ===

      html += '<div class="tab-content" id="nav-'+sheet_name+'-tabContent">';

      count_tbl_tab=1;

      $.each(sheet_data.compact_result.tables,function(tbl_name,each_table){

        let table_of_sheet_id = sheet_name+"_"+tbl_name;

        html += '<div class="tab-pane fade ' +  ((count_tbl_tab==1)?(' show active '):('')) + '" id="tbl-'+table_of_sheet_id+'-content" role="tabpanel" aria-labelledby="tbl-'+table_of_sheet_id+'-tab">';



          if(each_table.is_valid_table==1) {   

          let each_def_col = sheet_data.def_tables[tbl_name].def_col; 
        
          html += build_table_from_table_data(each_def_col,each_table,bottom_offset);
           
          }

        html += '</div>';

 
        count_tbl_tab++;
      });

      html +='</div>';



  //end - tab content ===
                      

  return html;

}


function build_table_from_table_data(def_col,table_data,bottom_offset='300px'){


 

  let html = '';
  // console.log(def_col);
  // console.log(table_data);


  html += '<div class="table-responsive " style="overflow: auto;max-height: calc(100vh - '+bottom_offset+');">';
      html += '<table class="tbl-show-excel " >';
        html += '<thead class="thead-light" style="position: sticky; top: 0; z-index: 1;">';
            html += '<tr style="background-color: #e1e1e1;">';
                html += '<th></th>';
                
                    let ord=1;
                    $.each(def_col,function(col_idx,col_data){
                      html += '<th>'+col_data.excel_col+'</th>';
                    })


            html += '</tr>';

            html += '<tr style="background-color: #eaf3ff;">';
              html += '<th>ลำดับ</th>';

                     $.each(def_col,function(col_idx,col_data){


                      if( typeof col_data.real_excel_header !== 'undefined' && !isEmptyString(col_data.real_excel_header) ){
                        html += '<th>'+col_data.real_excel_header+'</th>';
                      }else{
                        html += '<th>'+col_data.excel_header+'</th>';
                      }
                    })

             
            html += '</tr>';                                        
        html += '</thead>';


        html += '<tbody>';


        $.each(table_data.content,function(row_idx,row){
          html += '<tr>';
          html += '<td>'+ord+'</td>';

          $.each(def_col,function(col_idx,col_data){
                     
            if( typeof row[col_idx] !== 'undefined'){
              //html += '<td> '+ (row[col_idx]) +' </td>';

              let showval = row[col_idx];


              let cls_align = '';
              if( col_data.col_type=='INT' || col_data.col_type=='DECIMAL' || col_data.col_type=='DOUBLE'){
                cls_align = ' class="al-r" ';
              }

              //pattern format cell

              // #####################
              showval = convertValToDisplay(col_data.col_type,showval,col_data.format_cell);


              html += '<td ' + cls_align +' > '+ HtmlEncode(showval) +' </td>';
            }else{
              html += '<td> NODATA </td>';
            }
            
          
          });

          html += '</tr>';

          ord++;
        
          });

  html += '</tbody>'+
                  '</table>'+
                '</div>';


    return html;


}


//function to mirror to Util_model->convertRawColumnToDisplay($col_type,$col_val,$format_cell);
function convertValToDisplay(col_type,col_val,format_cell,edit_mode=0){

   if(typeof col_val === 'undefined'){
    col_val = null;
   }
   let display_val = col_val;
        
        if(col_type == 'DECIMAL' || col_type == 'DOUBLE' || col_type == 'INT'){


          //display_val = format_int(col_val);

            if(  format_cell=='_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)'){
                if(col_val===0){
                    display_val = (edit_mode)?('0'):('-');
                }else if(isEmptyString(col_val)){
                    display_val = null;
                }else {
                    display_val = formatMoney(col_val);
                }
            }
            if(  format_cell=='_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)'){
                if(col_val===0){
                    display_val = (edit_mode)?('0'):('-');
                }else if(isEmptyString(col_val)){
                    display_val = null;
                }else {
                    display_val = formatIntegerNumber(col_val);
                }
            }
            

            if(  format_cell=='accounting_2'){
                display_val = formatMoney(col_val);
            }

            if(  format_cell=='percent_0'){

              
              if(col_val===null || col_val===''){
                display_val = '';
              }else{
                display_val = formatIntegerNumber(col_val)+'%';
              }
            }


            if(  format_cell=='general_num'){
                display_val = formatNumGeneral(col_val);
            }
            if( isEmptyString(format_cell)){
                display_val = formatNumGeneral(col_val,0);
            }



        }else if(col_type == 'DATE'){

            if(format_cell=='dd mmm yy'){

                display_val =  getDisplayFromExcelFormatEnYearJS(col_val,format_cell);

            }else if (format_cell=='d/m/Y'){
                display_val =  getDisplayFromExcelFormatEnYearJS(col_val,format_cell);
            }
            
        }

        //console.log('convertValToDisplay',col_type,col_val,format_cell,'=>',display_val);
        return display_val;



}


function getDisplayFromExcelFormatEnYearJS(str,format)
  {
      
    if(isEmptyString(str)) return '';

     
   // $.format.date(new Date(value),'yyyy'))

      if(format=='dd mmm yy'){
        m_list = getMonthList('abbr-en');

        y_abbr =  $.format.date(new Date(str),'yy') // 24, 25
        m = $.format.date(new Date(str),'M'); // 1, 2
        d = $.format.date(new Date(str),'d'); // 1, 2

        str_m = (typeof (m_list[m]) !== "undefined")?(m_list[m]):('');
        
        return d + " " + str_m + " " + y_abbr;

      }

      if(format=='d/m/Y'){
 
        y =   $.format.date(new Date(str),'yyyy');//2025
        m = $.format.date(new Date(str),'M'); // 1, 2
        d = $.format.date(new Date(str),'d'); // 1, 2

       
        return  d + "/" + m + "/" + y;

      }

      if(format=='d-mmm'){
       m_list = getMonthList('abbr-en');
       m = $.format.date(new Date(str),'M'); // 1, 2
       d = $.format.date(new Date(str),'d'); // 1, 2

       str_m = (typeof (m_list[m]) !== "undefined")?(m_list[m]):('');
       return  d + "-" + str_m  ;

      }

    //default


    return str; 
  
  } 

</script>


<script>
//------ Script for fast table load ------//
var sheet_table_bottom_offset = '300px';




function build_tab_sheet_from_json_fastload(tab_color='#3b7749',bottom_offset='300px'){
  sheet_table_bottom_offset = bottom_offset;
let html = "";
// console.log(json_sheets);

//card div start ----
//tab-head-start----
html += '<div class="card card-success card-tabs tab-all-sheets" style="box-shadow:unset;">' +
            '<div class="card-header p-0 pt-1" style="background-color: '+tab_color +';">'+
              '<ul class="nav nav-tabs tab-condense" id="preview-upload-tab-head" role="tablist">';
let count_tab = 1;
$.each(current_json_sheets, function(sheet_name,sheet_data){

  if(typeof sheet_data.is_found_in_db !== 'undefined' && sheet_data.is_found_in_db==0){
    return;
  }

  html += '<li class="nav-item" >' +
          '<a class="nav-link ' +  ((count_tab==1)?(' active '):('')) + 'text-center" id="sheet_preview'+count_tab+'-tab" data-toggle="pill" href="#" data-sheet-name="'+sheet_name+'"  onclick="opentab_sheet_data(this)">' + 

          ((sheet_data.is_found_in_excel==1)?(sheet_data.real_sheet_name_on_excel):(sheet_data.excel_sheet_name))

          // <!-- display error -->

           if(sheet_data.is_found_in_excel==0){

            html +='<span class="badge badge-pill" style="background:#ffffff;color:#ff0000;">ไม่พบใน Excel</span>';

           }else if( typeof sheet_data.compact_result.is_valid_sheet !== 'undefined' && sheet_data.compact_result.is_valid_sheet==0){
            html +='<span class="badge badge-pill" style="background:#ffffff;color:#ff0000;">อ่านข้อมูลไม่ได้</span>';
           
           }
          
      
  html +='</a>' +
         '</li>';

        count_tab++;       
});

html +='</ul>' +
      '</div>';
//tab-head-end----


//tab content start ----            
html +='<div class="card-body pl-0 pr-0 pt-1 pb-1">';
html +=  '<div class="tab-content" id="preview-upload-tab-content">';





html +='<div class="current-tab-content" id="sheet_preview" > Loading...';

// $.each(json_sheets, function(sheet_name,sheet_data){

//   let is_active = (count_tab==1)?(1):(0);

  
//   if(typeof sheet_data.is_found_in_db !== 'undefined' && sheet_data.is_found_in_db==0){
//     return;
//   }


//   //tab-centent-sheet
//   html +='<div class="tab-pane  fade ' +  ((count_tab==1)?(' show active '):('')) + '" id="sheet_preview'+count_tab+'" role="tabpanel" aria-labelledby="sheet_preview'+count_tab+'-tab">';
                  
//                    if(sheet_data.is_found_in_excel==1) {  
//                        if(sheet_data.compact_result.is_valid_sheet==1) {  

//                           html += build_tab_table_from_sheet_data(sheet_name,sheet_data,bottom_offset);
//                           //html += '<b>'+sheet_name+'</b>';

//                          }else{ 
                          
//                           html +=' <span class="text-danger">' + sheet_data.compact_result.msg +'</span>';

//                        } 
//                    }else{

//                       html += '<span class="text-danger">ไม่พบ Sheet ใน Excel ที่อัพโหลดมา </span>';
//                   } 


//   count_tab++;

//   html +='</div>';

// });

html +='</div>';
html +='</div>';
html +='</div>';
html +='</div>';
//tab content end ----    

return html;
}


function opentab_sheet_data_fastload(el){
  let sheet_name = $(el).data('sheet-name');
  let html = '';
  console.log(sheet_name);
  if(typeof current_json_sheets[sheet_name]==='undefined'){
    console.log('not found sheet data');
    return;
  }
  sheet_data = current_json_sheets[sheet_name];


  

  if(sheet_data.is_found_in_excel==1) {  
    if(sheet_data.compact_result.is_valid_sheet==1) {  

      html += build_tab_table_from_sheet_data_fastload(sheet_name,sheet_data);
      //html += '<b>'+sheet_name+'</b>';

      }else{ 
      
      html +=' <span class="text-danger">' + sheet_data.compact_result.msg +'</span>';

    } 
  }else{

    html += '<span class="text-danger">ไม่พบ Sheet ใน Excel ที่อัพโหลดมา </span>';
  } 

  //console.log(html);

  $('.current-tab-content').html(html);
}


function opentab_sheet_data(el){
  let sheet_name = $(el).data('sheet-name');
  let html = '';
  console.log(sheet_name);
  if(typeof current_json_sheets[sheet_name]==='undefined'){
    console.log('not found sheet data');
    return;
  }
  sheet_data = current_json_sheets[sheet_name];


  

  if(sheet_data.is_found_in_excel==1) {  
    if(sheet_data.compact_result.is_valid_sheet==1) {  

      build_tab_table_from_sheet_data_ag_table(sheet_name,sheet_data);
      //$('.current-tab-content').html(html);

      }else{ 
      
      html =' <span class="text-danger">' + sheet_data.compact_result.msg +'</span>';
      $('.current-tab-content').html(html);
      return;

    } 
  }else{

    html = '<span class="text-danger">ไม่พบ Sheet ใน Excel ที่อัพโหลดมา </span>';
    $('.current-tab-content').html(html);
    return;
  } 

  //console.log(html);

  //$('.current-tab-content').html(html);
}

function build_tab_table_from_sheet_data_fastload(sheet_name,sheet_data){
// <!-- TAB TABLE PANEL START-->

//start - nav head ===
let html = '<nav>' + 
            '<ul class="nav nav-tabs tab-condense" id="nav-'+sheet_name+'-tab" role="tablist">';

let count_tbl_tab = 1;
// console.log(sheet_data.def_tables);

    $.each(sheet_data.compact_result.tables,function(tbl_name,each_table){

      // console.log(each_table);

      let table_of_sheet_id = sheet_name+"_"+tbl_name;
      let suffix_table_msg = '';
      if(each_table.is_valid_table==0){
        suffix_table_msg = "ตารางไม่ถูกต้อง - " + each_table.msg;
      }


      html += '<li class="nav-item">'+      
                '<a class="nav-link tab-sub-table ' +  ((count_tbl_tab==1)?(' active '):('')) + '" id="tbl-'+table_of_sheet_id+'-tab" data-toggle="tab" data-target="#tbl-'+table_of_sheet_id+'-content" type="button" role="tab" aria-controls="tbl-'+table_of_sheet_id+'-tab" aria-selected="true">'+
                
                ((tbl_name=='main')?('ตาราง'):('ตาราง '+each_table.table_title)) + suffix_table_msg ;
                
                
      html += '</a>' +
              '</li>';



      count_tbl_tab++;
    });



     html += ' </ul>'+
            '</nav>';
//end - nav head ===


//start - tab content ===

    html += '<div class="tab-content" id="nav-'+sheet_name+'-tabContent">';

    count_tbl_tab=1;

    $.each(sheet_data.compact_result.tables,function(tbl_name,each_table){

      let table_of_sheet_id = sheet_name+"_"+tbl_name;

      html += '<div class="tab-pane fade ' +  ((count_tbl_tab==1)?(' show active '):('')) + '" id="tbl-'+table_of_sheet_id+'-content" role="tabpanel" aria-labelledby="tbl-'+table_of_sheet_id+'-tab">';



        if(each_table.is_valid_table==1) {   

        let each_def_col = sheet_data.def_tables[tbl_name].def_col; 
      
        html += build_table_from_table_data_fastload(each_def_col,each_table);
         
        }

      html += '</div>';


      count_tbl_tab++;
    });

    html +='</div>';



//end - tab content ===
                    

return html;

}


function build_tab_table_from_sheet_data_ag_table(sheet_name,sheet_data){
// <!-- TAB TABLE PANEL START-->
$('.current-tab-content').html();
//start - nav head ===
let html = '<nav>' + 
            '<ul class="nav nav-tabs tab-condense" id="nav-'+sheet_name+'-tab" role="tablist">';

let count_tbl_tab = 1;
// console.log(sheet_data.def_tables);

    $.each(sheet_data.compact_result.tables,function(tbl_name,each_table){

      // console.log(each_table);

      let table_of_sheet_id = sheet_name+"_"+tbl_name;
      let suffix_table_msg = '';
      if(each_table.is_valid_table==0){
        suffix_table_msg = "ตารางไม่ถูกต้อง - " + each_table.msg;
      }


      html += '<li class="nav-item">'+      
                '<a class="nav-link tab-sub-table ' +  ((count_tbl_tab==1)?(' active '):('')) + '" id="tbl-'+table_of_sheet_id+'-tab" data-toggle="tab"  type="button" role="tab" aria-controls="tbl-'+table_of_sheet_id+'-tab" aria-selected="true" onclick="opentab_table(this)" data-sheet-name="'+sheet_name+'" data-table-name="'+tbl_name+'" data-table-idx="'+count_tbl_tab+'">'+
                
                ((tbl_name=='main')?('ตาราง'):('ตาราง '+each_table.table_title)) + suffix_table_msg ;
                
                
      html += '</a>' +
              '</li>';

             

      count_tbl_tab++;
    });



     html += ' </ul>'+
            '</nav>';
//end - nav head ===
$('.current-tab-content').html(html);
     html = "";

//start - tab content ===

    html += '<div class="tab-content" id="nav-'+sheet_name+'-tabContent">';

    count_tbl_tab=1;

    // $.each(sheet_data.compact_result.tables,function(tbl_name,each_table){

    //   let table_of_sheet_id = sheet_name+"_"+tbl_name;

    //   //html += '<div class="tab-pane fade ' +  ((count_tbl_tab==1)?(' show active '):('')) + '" id="tbl-'+table_of_sheet_id+'-content" role="tabpanel" aria-labelledby="tbl-'+table_of_sheet_id+'-tab" style="height: calc(100vh - '+sheet_table_bottom_offset+');">';
    //   html += '<div id="tbl-'+table_of_sheet_id+'-content" style="height: calc(100vh - '+sheet_table_bottom_offset+');">';


    //   html += '</div>';


    //   count_tbl_tab++;
    // });

    html += '<div id="tbl-ag-grid-content" style="height: calc(100vh - '+sheet_table_bottom_offset+');">';


    html += '</div>';

    html +='</div>';


    $('.current-tab-content').append(html);


    setTimeout(() => {

      $('.tab-sub-table[data-table-idx="1"]').click();
      
    }, 300);

    return;

    //build ag table
    let tbl_idx=0;
    $.each(sheet_data.compact_result.tables,function(tbl_name,each_table){

      let table_of_sheet_id = sheet_name+"_"+tbl_name;

       
        if(each_table.is_valid_table==1) {   

        let each_def_col = sheet_data.def_tables[tbl_name].def_col; 
        let element_table_id  = '#tbl-'+table_of_sheet_id+'-content';


        if(tbl_idx>0){
          setTimeout(() => {
            build_table_from_table_data_ag_table(tbl_idx,each_def_col,each_table,element_table_id);
          }, 1000);
        }else{
          build_table_from_table_data_ag_table(tbl_idx,each_def_col,each_table,element_table_id);
        }

        
        tbl_idx++;
        }
 
      });



//end - tab content ===
                    

//return html;

}


function opentab_table(el){

  let sheet_name = $(el).data('sheet-name');
  let table_name = $(el).data('table-name');
  let html = '';
  console.log(sheet_name);
  if(typeof current_json_sheets[sheet_name]==='undefined'){
    console.log('not found sheet data');
    return;
  }
  if(typeof current_json_sheets[sheet_name]['compact_result']['tables'][table_name]==='undefined'){
    console.log('not found table data');
    return;
  }
  let sheet_data = current_json_sheets[sheet_name];
  let each_table = current_json_sheets[sheet_name]['compact_result']['tables'][table_name];

  let table_of_sheet_id = sheet_name+"_"+table_name;

       
if(each_table.is_valid_table==1) {   

  let each_def_col = sheet_data.def_tables[table_name].def_col; 
  let element_table_id  = '#tbl-ag-grid-content';

  $(element_table_id).empty();
  
    build_table_from_table_data_ag_table(0,each_def_col,each_table,element_table_id);
 

}




}

function build_table_from_table_data_fastload(def_col,table_data,bottom_offset='300px'){




let html = '';
// console.log(def_col);
// console.log(table_data);


html += '<div class=" " style="overflow: auto;max-height: calc(100vh - '+bottom_offset+');">';
    html += '<table class="tbl-show-excel " >';
      html += '<thead class="thead-light" style="position: sticky; top: 0; z-index: 1;">';
          html += '<tr style="background-color: #e1e1e1;">';
              html += '<th></th>';
              
                  let ord=1;
                  $.each(def_col,function(col_idx,col_data){
                    html += '<th>'+col_data.excel_col+'</th>';
                  })


          html += '</tr>';

          html += '<tr style="background-color: #eaf3ff;">';
            html += '<th>ลำดับ</th>';

                   $.each(def_col,function(col_idx,col_data){


                    if( typeof col_data.real_excel_header !== 'undefined' && !isEmptyString(col_data.real_excel_header) ){
                      html += '<th>'+col_data.real_excel_header+'</th>';
                    }else{
                      html += '<th>'+col_data.excel_header+'</th>';
                    }
                  })

           
          html += '</tr>';                                        
      html += '</thead>';


      html += '<tbody>';


      $.each(table_data.content,function(row_idx,row){
        html += '<tr>';
        html += '<td>'+ord+'</td>';

        $.each(def_col,function(col_idx,col_data){
                   
          if( typeof row[col_idx] !== 'undefined'){
            //html += '<td> '+ (row[col_idx]) +' </td>';

            let showval = row[col_idx];


            let cls_align = '';
            if( col_data.col_type=='INT' || col_data.col_type=='DECIMAL' || col_data.col_type=='DOUBLE'){
              cls_align = ' class="al-r" ';
            }

            //pattern format cell

            // #####################
            showval = convertValToDisplay(col_data.col_type,showval,col_data.format_cell);


            html += '<td ' + cls_align +' > '+ HtmlEncode(showval) +' </td>';
          }else{
            html += '<td> NODATA </td>';
          }
          
        
        });

        html += '</tr>';

        ord++;
      
        });

html += '</tbody>'+
                '</table>'+
              '</div>';


  return html;


}



var aggrid = [];
var col_def_by_letter = {};
function build_table_from_table_data_ag_table(tbl_idx,def_col,table_data,selector){

  // console.log(def_col);return;
  //convert col def to col_def_by_letter
  col_def_by_letter = {}
  $.each(def_col,function(idx,coldef){
    col_def_by_letter[coldef.excel_col] = coldef;
  });

// const myTheme = agGrid.themeBalham.withParams({
  const myTheme = agGrid.themeQuartz.withParams({
    spacing: 2,
    //borderColor: 'black',
    headerColumnBorder: true,


    headerColumnResizeHandleHeight: '100%',
    headerColumnResizeHandleWidth: 1,
    headerColumnResizeHandleColor: 'transparent',
    columnBorder: { style: "solid", width: 1 },
    rowBorder: { style: "solid", width: 1 },

});
// Row Data Interface

// Grid API: Access to Grid API methods
let gridApi;

let row_data = [];
let seq= 1;
$.each(table_data.content,function(row_idx,row){
  // console.log(row)
  let each_row = {}
  each_row['seq'] = seq++;
  $.each(def_col,function(col_idx,col_data){

    if(typeof row[col_idx] !== 'undefined'){
      each_row[col_data.excel_col] = row[col_idx];
    }else{
      each_row[col_data.excel_col] = '';
    }
    
  });

  row_data.push(each_row);

});


let col_conf= [{'headerName':'', headerClass: 'ag-head-row1','children': [{ headerName: "ลำดับ", field: "seq",headerClass: 'ag-head-row2' }]}];
  $.each(def_col,function(col_idx,col_data){

    let real_excel_head_col = '';
    if( typeof col_data.real_excel_header !== 'undefined' && !isEmptyString(col_data.real_excel_header) ){
      real_excel_head_col = col_data.real_excel_header;
    }else{
      real_excel_head_col = col_data.excel_header;
    }

    let clsCell = '';
    if( col_data.col_type=='DECIMAL' || col_data.col_type=='DOUBLE' || col_data.col_type=='INT' ){
      clsCell = 'cell-num-ag-table';
    }
    let each_col_data = {
      headerName: col_data.excel_col, 
      headerClass: 'ag-head-row1',
      children: [{ headerName: real_excel_head_col, 
                   field: col_data.excel_col, 
                   headerClass: 'ag-head-row2',
                   valueFormatter: displayFormatter,
                   cellClass: clsCell
                  }]};
     
    col_conf.push(each_col_data);
  });

 let myAutoSizeStrategy = {}
//  if(tbl_idx==0){

  myAutoSizeStrategy = {
    type: "fitCellContents",    
    defaultMaxWidth: 150,
    defaultMinWidth: 40,
  }
//  }

// Grid Options: Contains all of the grid configurations
const gridOptions = {
  theme: myTheme,
  defaultColDef: {
        width: 80,
    },
  autoSizeStrategy: myAutoSizeStrategy,
  //suppressColumnVirtualisation: true,
  // Data to be displayed
  rowData: row_data,
  // Columns to be displayed (Should match rowData properties)
  columnDefs: col_conf,
};
// Create Grid: Create new grid within the #myGrid div, using the Grid Options object

//console.log(selector);
gridApi = new agGrid.createGrid(document.querySelector(selector), gridOptions);



// $(selector).html(selector);
}

function displayFormatter(params) {

 
  let cur_field = params.colDef.field;

  if(typeof col_def_by_letter[cur_field] !== 'undefined'){
    let cur_col_def =  col_def_by_letter[cur_field];
    return convertValToDisplay(cur_col_def.col_type,params.value,cur_col_def.format_cell)
  }else{
    return params.value;
  }


  

  return params.value;
}

</script>
