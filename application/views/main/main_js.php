<script>
var Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

var is_calc_fc = <?php echo ($is_calc_fc)?('true'):('false'); ?>;

$( document ).ready(function() {

if(is_calc_fc){
  loadSheetActive();
}
 
});

let current_json_calc_data={};
let calc_col_front_dbcd = [];
let calc_col_edit_dbcd = [];
let calc_col_all_dbcd = [];
let dbcd_member_sheet = ["dbcd4",
    "dbcd6",
    "dbcd5",
    "dbcd6lx",
    "dbk6"];
let current_sheet = '';
let current_def_col = {};
let sheet_data_table_name = '';
let current_year = null;
let current_month = null;

function change_select_year_month(ele){
	var select_year =$('#select-year').val();
	var select_month = $('#select-month').val();
	var url = '<?php echo base_url('main') ?>' + '?year=' + select_year + '&month=' + select_month;
	window.location.href = url;
}

function loadSheetActive(){

  let  real_sheet_name_on_excel = '';
  let  sheetname = '';

  //find sheet active
  $('.div-sheet-item').each(function(i,el){

    if($(el).hasClass('active')){
         real_sheet_name_on_excel = $(el).data('real_sheet_name_on_excel');
         sheetname = $(el).data('sheet');
    }

  });

  $('.sheet-active-header').html(real_sheet_name_on_excel);
  getSheetCalc(sheetname);

}

function clickBtnSheet(el){

  let active_sheet = $(el).data('sheet');
  console.log(active_sheet);
  $('.div-sheet-item').each(function(i,el){
    $(el).removeClass('active');
  });
   
  $(el).parents('.div-sheet-item').addClass('active');

  loadSheetActive();

}

function getSheetCalc(sheetname){

	var select_year =$('#select-year').val();
	var select_month = $('#select-month').val();
  var fc = 'datacost'

  $('.waitloader-text').text('Loading ...');
  $('.waitloader-overlay').show();
  var json = {select_year,select_month,sheetname,fc}

  $.post('<?php echo base_url('cost/getCalcSheetObject'); ?>',json,function(data, textStatus, jqXHR){

    if(data.result=='success'){
            
      
            $('#sheet-calc-content').empty();
 
            current_json_calc_data = JSON.parse(data.str_json_sheets_calc_data);
            current_sheet = data.sheet;

            calc_col_front_dbcd = JSON.parse(data.calc_col_front_dbcd);
            calc_col_edit_dbcd = JSON.parse(data.calc_col_edit_dbcd);
            calc_col_all_dbcd = JSON.parse(data.calc_col_all_dbcd);
            dbcd_member_sheet = JSON.parse(data.dbcd_member_sheet);
            current_year = data.year;
            current_month = data.month;

            //build def_col and data_table_name
            current_def_col = {};
            $.each(current_json_calc_data.def_tables['main'].def_col,function(idx,coldef){
                current_def_col[coldef.excel_col] = coldef;
            });
            sheet_data_table_name = current_json_calc_data.def_tables['main'].data_table_name;

            $('#sheet-calc-content').html();
            build_calc_ag_table();

        }else if( data.result == 'logout'){
           apiLogout();
        }else if(data.result=='failed'){
             if(data.errormsg != 'undefined'){
               console.log('Error:', data.errormsg);
             }

             $('#sheet-calc-content').empty();

        }
          

  }).fail(function(jqXHR, textStatus, errorThrown) {
           console.log('Error:', errorThrown);
           $('#sheet-calc-content').empty();
  }).always( function (){
          $('.waitloader-overlay').hide();  

  });

}

</script>

<script>
  //------ CALC SHEET VIEW-ONLY (Dashboard) ------//
// Grid API: Access to Grid API methods
let gridApi;

function is_dbcd_sheet(){
  return dbcd_member_sheet.includes(current_sheet);
}
 
function build_calc_ag_table(){
  
  let table_data = current_json_calc_data.calc_result.tables['main'];
  let def_col = current_json_calc_data.def_tables['main'].def_col;
 
  let element_content_id  = '#sheet-calc-content';
  let col_def_by_letter = {};

  //convert col def to col_def_by_letter
  col_def_by_letter = {}
  $.each(def_col,function(idx,coldef){
    col_def_by_letter[coldef.excel_col] = coldef;
  });

  const myTheme = agGrid.themeQuartz.withParams({
    spacing: 2,
    headerColumnBorder: true,
    headerColumnResizeHandleHeight: '100%',
    headerColumnResizeHandleWidth: 1,
    headerColumnResizeHandleColor: 'transparent',
    columnBorder: { style: "solid", width: 1 },
    rowBorder: { style: "solid", width: 1 },

});

let row_data = [];
let seq= 1;
$.each(table_data.content,function(row_idx,row){
  let each_row = {}
  each_row['seq'] = seq++;
  each_row['id'] = row['id'];
  $.each(def_col,function(col_idx,col_data){
    
    if(typeof row[col_idx] !== 'undefined'){

      //cast type
      if( col_data.col_type=='DECIMAL' || col_data.col_type=='DOUBLE' ){
        each_row[col_data.excel_col] = (row[col_idx]!==null) ? parseFloat(row[col_idx]) : null;
      }else if( col_data.col_type=='INT' ){
        each_row[col_data.excel_col] = (row[col_idx]!==null) ? parseInt(row[col_idx]): null;
      }else{
        each_row[col_data.excel_col] = row[col_idx];
      }
     
    }else{
      each_row[col_data.excel_col] = '';
    }
    
  });

  row_data.push(each_row);

});

let pinned_idx_0=null;
if(is_dbcd_sheet()){
   pinned_idx_0='left';
}

let col_conf= [{'headerName':'', headerClass: 'ag-head-row1','children': [{ headerName: "ลำดับ",pinned:pinned_idx_0, field: "seq",headerClass: 'ag-head-row2' }]}];

  $.each(def_col,function(col_idx,col_data){

    //check col for dbcd sheet
    if(is_dbcd_sheet() && !(calc_col_all_dbcd.includes(col_data.excel_col))) return;

    let real_excel_head_col = '';
    if( typeof col_data.real_excel_header !== 'undefined' && !isEmptyString(col_data.real_excel_header) ){
      real_excel_head_col = col_data.real_excel_header;
    }else{
      real_excel_head_col = col_data.excel_header;
    }

    let clsCell1 = '';
    if( col_data.col_type=='DECIMAL' || col_data.col_type=='DOUBLE' || col_data.col_type=='INT' ){
      clsCell1 = 'cell-num-ag-table';
    }
    let pinned_val = null;

    let data_type = 'text';
    if( col_data.col_type=='DECIMAL' || col_data.col_type=='DOUBLE' || col_data.col_type=='INT' ){
      data_type = 'number';  
    }else if( col_data.col_type=='DATE' ){
      data_type = 'dateString';  
    }

    let each_col_data = {
      headerName: col_data.excel_col, 
      headerClass: 'ag-head-row1',
      pinned: pinned_val,
      children: [{ headerName: real_excel_head_col, 
                   field: col_data.excel_col, 
                   pinned: pinned_val,
                   headerClass: 'ag-head-row2',
                   valueFormatter: function(params) {
                                 return convertValToDisplay(col_data.col_type,params.value,col_data.format_cell,1);
                               },
                   cellClass: clsCell1,
                   editable: false, // View-only
                   cellDataType: data_type,
                  }]};
     
    col_conf.push(each_col_data);
  });

 let myAutoSizeStrategy = {
    type: "fitCellContents",    
    defaultMaxWidth: 150,
    defaultMinWidth: 40,
  }

// Grid Options: Contains all of the grid configurations
const gridOptions = {
  theme: myTheme,
  defaultColDef: {
        width: 80,
    },
  autoSizeStrategy: myAutoSizeStrategy,
  rowData: row_data,
  columnDefs: col_conf,
};

gridApi = new agGrid.createGrid(document.querySelector(element_content_id), gridOptions);

}

function onFilterTextBoxChanged() {
  gridApi.setGridOption(
    "quickFilterText",
    document.getElementById("filter-text-box").value,
  );
}

</script>