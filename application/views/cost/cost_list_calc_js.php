<script>
var Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

var is_calc_fc = <?php echo ($is_calc_fc)?('true'):('false'); ?>;
var auto_save=0;

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
let changed_rows_cells = [];



function change_select_year_month(ele){
	var select_year =$('#select-year').val();
	var select_month = $('#select-month').val();
	var url = '<?php echo base_url('cost/calc') ?>' + '?year=' + select_year + '&month=' + select_month;
	//console.log(url);
	window.location.href = url;
}



function loadSheetActive(){

  let  real_sheet_name_on_excel = '';
  let  sheetname = '';

  //find sheet active
  //set all unactive

  $('.div-sheet-item').each(function(i,el){

    // console.log($(el));

    if($(el).hasClass('active')){
      $(el).find('.btn-sheet-calc').addClass('btn-primary');
      $(el).find('.btn-sheet-calc').removeClass('btn-outline-secondary');
         real_sheet_name_on_excel = $(el).data('real_sheet_name_on_excel');
         sheetname = $(el).data('sheet');


    }else{
      $(el).find('.btn-sheet-calc').removeClass('btn-primary');
      $(el).find('.btn-sheet-calc').addClass('btn-outline-secondary');
    }

  });
  
  if( dbcd_member_sheet.includes(sheetname)  ){
    $('.btn-save-sheet').removeClass('collapse');
  }else{
    $('.btn-save-sheet').addClass('collapse');
  }

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

function getChangedRows() {
    const changedRows = [];
    gridOptions.api.forEachNode(rowNode => {
        if (rowNode.data.isChanged) {
            changedRows.push(rowNode.data);
        }
    });
    return changedRows;
}

function saveSheetRecalc(){

   $('.upload_status').html('<span>กำลังบันทึกข้อมูล ... </span>');
   $('.waitloader-text').text('กำลังบันทึกข้อมูล ...');
   $('.waitloader-overlay').show();

  var select_year =$('#select-year').val();
  var select_month = $('#select-month').val();
  var sheet_name = current_sheet;
  var json_calc_manual_lock_list = JSON.stringify(calc_manual_lock_list);


 
  var fc = 'datacost';
  var table_data_name = sheet_data_table_name;
  var table_of_sheet = 'main';





  var json = {select_year,select_month,sheet_name,json_calc_manual_lock_list,table_data_name,table_of_sheet,fc}
  //var upate_row_data = gridApi.getGridOption('rowData');

  console.log('calc_manual_lock_list',json_calc_manual_lock_list);
  // console.log('upate_row_data',upate_row_data);

  $.post('<?php echo base_url('cost/saveCalcSheetRecalc'); ?>',json,function(data, textStatus, jqXHR){

    if(data.result=='success'){
            
            
            $('.upload_status').html('');

            Toast.fire({
              icon: 'success',
              title: 'บันทึกข้อมูลสำเร็จ'
            });

            loadSheetActive();

        }else if( data.result == 'logout'){
           apiLogout();
        }else if(data.result=='failed'){
             $('.upload_status').html('');
             if(data.errormsg != 'undefined'){

               $('.upload_status').html('<span class="text-danger">'+data.errormsg+'</span>');
             }

        }
          

  }).fail(function(jqXHR, textStatus, errorThrown) {
           //alert("Error: " + errorThrown);
           $('.upload_status').html('<span class="text-danger">พบข้อผิดพลาด '+errorThrown+'</span>');
  }).always( function (){
          $('.waitloader-overlay').hide();  

  });

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
            calc_manual_lock_list = JSON.parse(data.manual_lock_list);
            current_year = data.year;
            current_month = data.month;

            //add manual lock list status
            $.each(calc_manual_lock_list,function(idx,lock_item){
              calc_manual_lock_list[idx]['status'] = 1;
              calc_manual_lock_list[idx]['is_new'] = 0;
              calc_manual_lock_list[idx]['old_auto_value'] = null;
              calc_manual_lock_list[idx]['old_lock_value'] = calc_manual_lock_list[idx]['lock_value'];
            });

            //build def_col and data_table_name
            current_def_col = {};
            $.each(current_json_calc_data.def_tables['main'].def_col,function(idx,coldef){
                current_def_col[coldef.excel_col] = coldef;
            });
            sheet_data_table_name = current_json_calc_data.def_tables['main'].data_table_name;





            $('#sheet-calc-content').html();
            build_calc_ag_table();

            $('.upload_status').html('');

        }else if( data.result == 'logout'){
           apiLogout();
        }else if(data.result=='failed'){
             $('.upload_status').html('');
             if(data.errormsg != 'undefined'){

               $('.upload_status').html('<span class="text-danger">'+data.errormsg+'</span>');
             }

             $('#sheet-calc-content').empty();

        }
          

  }).fail(function(jqXHR, textStatus, errorThrown) {
           //alert("Error: " + errorThrown);
           $('.upload_status').html('<span class="text-danger">พบข้อผิดพลาด '+errorThrown+'</span>');
           $('#sheet-calc-content').empty();
  }).always( function (){
          $('.waitloader-overlay').hide();  

  });


 








}



function start_calc_cost(first_calc=0){

   $('.upload_status').html('<span>รอการคำนวณ ... </span>');
   $('.waitloader-text').text('รอการคำนวณ ...');
   $('.waitloader-overlay').show();

	var select_year =$('#select-year').val();
	var select_month = $('#select-month').val();

  

  var is_compare=0;
  if($('#chk_compare').prop('checked')){
    is_compare=1;
  }
  var json = {select_year,select_month,first_calc,is_compare}


  $.post('<?php echo base_url('cost/startCalcCostBasic');    ?>',json,function(data){
 
  //debug compare
  if(is_compare==1){
    let arr_html_compare_list = JSON.parse(data.str_json_compare_calc_result);

    let all_html = '';
    $.each(arr_html_compare_list,function(shtname,ht){
  
      all_html += '<h4>'+shtname+'</h4>';
      all_html += ht;
    });
    $('.compare-table').html(all_html);

    return;
  }
 
  
  // str_json_compare_calc_result


          if(data.result=='success'){
             
            console.log('success');

            $('.upload_status').html('');

            if(first_calc==1){
              window.location.reload();
              return;
            }


            loadSheetActive();

            }else if( data.result == 'logout'){
            apiLogout();

          }else if(data.result=='failed'){
            
              $('.upload_status').html('');
              if(typeof data.errormsg != 'undefined'){
               $('.upload_status').html('<span class="text-danger">'+data.errormsg+'</span>');
              }else{
               $('.upload_status').html('<span class="text-danger">ไม่สามารถคำนวณได้</span>');
              }
               
            
          }


  }).fail(function(xhr, status, error) {
      
    $('.upload_status').html('<span class="text-danger">พบข้อผิดพลาด '+errorThrown+'</span>');
    // error handling
    
  }).always(  function (){

    $('.waitloader-overlay').hide();
  
  });

 





}




</script>



<script>
  //------ CALC SHEET EDITABLE ------//
// Grid API: Access to Grid API methods
let gridApi;
function is_dbcd_sheet(){
  return dbcd_member_sheet.includes(current_sheet);
}

function chkIsManualLockCell(row_id,excel_col){
  //console.log('chkIsManualLockCell',row_id,excel_col);
  let is_manual_lock = false;
  $.each(calc_manual_lock_list,function(idx,lock_item){
      if( lock_item.row_id==row_id && lock_item.excel_col==excel_col && lock_item.status==1 ){
          is_manual_lock = true
          return false; //break loop
      }
  });

  return is_manual_lock;
}

function setIsManualLockCell(row_id,row_node_id,excel_col,is_lock,val){
  console.log('setIsManualLockCell',row_id,excel_col,is_lock,val);
  let found_idx = -1;
  $.each(calc_manual_lock_list,function(idx,lock_item){
      if( lock_item.row_id==row_id && lock_item.excel_col==excel_col ){
          found_idx = idx;
          return false; //break loop
      }
  });

  if(found_idx>=0){
    //update status
    calc_manual_lock_list[found_idx]['status'] = is_lock ? 1 : 0;
    calc_manual_lock_list[found_idx]['lock_value'] = val;

    //if unlock reset to auto value
    if(!is_lock){
      calc_manual_lock_list[found_idx]['lock_value'] = null;
      gridApi.getRowNode(String(row_node_id)).setDataValue(excel_col, calc_manual_lock_list[found_idx]['old_auto_value'] );
    }
    
  }else{
    //add new
    calc_manual_lock_list.push( {
      id:null,
      row_id: String(row_id),
      excel_col:excel_col,

      col_name: current_def_col[excel_col].col_name,
      table_name: sheet_data_table_name,
      year: current_year,
      month: current_month,
      status: is_lock ? 1 : 0 ,
      lock_value: val,
      is_new: 1,
      old_auto_value: val // first entry keep auto value
    
    } );
  }



  //console.log('setIsManualLockCell',calc_manual_lock_list);
}

 
function build_calc_ag_table(){
  
  let table_data = current_json_calc_data.calc_result.tables['main'];
  let def_col = current_json_calc_data.def_tables['main'].def_col;

  // console.log(table_data);
 
  let element_content_id  = '#sheet-calc-content';
  let col_def_by_letter = {};
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



let row_data = [];
let seq= 1;
$.each(table_data.content,function(row_idx,row){
  //console.log(row)
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


// console.log('row_data',row_data);

let pinned_idx_0=null;
if(is_dbcd_sheet()){
   pinned_idx_0='left';
}




let col_conf= [{'headerName':'', headerClass: 'ag-head-row1','children': [{ headerName: "ลำดับ",pinned:pinned_idx_0, field: "seq",headerClass: 'ag-head-row2' }]}];



  $.each(def_col,function(col_idx,col_data){



    //check col for dbcd sheet
    if(is_dbcd_sheet() && !(calc_col_all_dbcd.includes(col_data.excel_col))) return;


    let real_excel_head_col = '';
    let is_edit_col=false;
    let cell_class_func = null;
    let cell_editable_func = null;
    let cell_renderer_func = null;
    let cell_change_func = null;
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
    //if(is_dbcd_sheet() && ( calc_col_front_dbcd.includes(col_data.excel_col))){ pinned_val='left'};


    let data_type = 'text';
    let displayFormatter = null;
    if( col_data.col_type=='DECIMAL' || col_data.col_type=='DOUBLE' || col_data.col_type=='INT' ){
      data_type = 'number';  
    }else if( col_data.col_type=='DATE' ){
      data_type = 'dateString';  
    }

    //Editable
    if(is_dbcd_sheet() && ( calc_col_edit_dbcd.includes(col_data.excel_col))){
      is_edit_col = true;

      cell_class_func = params => {
                let row_id = params.data.id;
                let is_manual_lock = chkIsManualLockCell(row_id,col_data.excel_col);
                if(is_manual_lock){
                  return 'manual-lock-cell';
                }else{
                  return ''
                }
                
            }
      cell_editable_func       = params => {
                let row_id = params.data.id;
                let is_manual_lock = chkIsManualLockCell(row_id,col_data.excel_col);
                if(is_manual_lock){
                  return true;
                }else{
                  return false;
                }
                



            }
      
      cell_change_func = (event) => {
        // Handle the event here
        const { api, data, colDef, newValue, oldValue, node ,source} = event;
        console.log(event);
        console.log('source=>',source);

        console.log('Cell value changed to:', newValue);
        console.log('Old value:', oldValue);
       // console.log('Row data:', data);

        if(event.source == 'edit'){
          //request update from backend
          let row_id = data.id;
          let excel_col = colDef.field;
          let new_val = newValue;
          let col_name = current_def_col[excel_col].col_name;
          let table_name = sheet_data_table_name; 
          console.log('update calc cell');
        }
    }
      


      cell_renderer_func = CustomEditManualLockAndAutoModeComponent
            // agTextCellEditor: 'TextEditor',
            // agNumberCellEditor: 'NumberEditor',



     
    }else{
      cell_class_func = clsCell1;
    }

    
    // if( col_data.col_type=='DECIMAL' || col_data.col_type=='DOUBLE'){

    //   convertValToDisplay(col_type,col_val,format_cell)
    //   displayFormatter = (params)=> '$' + params.value.toFixed(2);  
    // }else if( col_data.col_type=='INT' ){
    //   displayFormatter = (params)=> params.value;
    // }else if( col_data.col_type=='DATE' ){
    //   data_type = 'dateString';  
    // }

    //valueFormatter: params => '$' + params.value.toFixed(2);

    let each_col_data = {
      headerName: col_data.excel_col, 
      headerClass: 'ag-head-row1',
      pinned: pinned_val,
      children: [{ headerName: real_excel_head_col, 
                   
                   field: col_data.excel_col, 
                   pinned: pinned_val,
                   headerClass: 'ag-head-row2',
                  //valueFormatterxx: displayFormatter,
                   valueFormatter: function(params) {
                                // let cur_val = params.value;
                                // if(params.value===null || typeof params.value==='undefined' || (params.value=='')){
                                //   console.log('null val',col_data.excel_col);
                                //   cur_val==null;
                                // }
                                // if( col_data.excel_col=='OL'){
                                //   console.log('OL',cur_val);
                                //   console.log('valueFormatter',col_data.col_type,cur_val,col_data.format_cell);
                                // }
                               
                                return convertValToDisplay(col_data.col_type,params.value,col_data.format_cell,1);
                              
                              }
                            ,
                   cellClass: cell_class_func,
                   editable: cell_editable_func,
                   cellDataType: data_type,
                   cellRenderer: cell_renderer_func,
                   //onCellValueChanged: cell_change_func,
                   

                  }]};
     
    col_conf.push(each_col_data);
  });


  console.log(col_conf);

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
  singleClickEdit:true, 
  stopEditingWhenCellsLoseFocus: true,

  //place here because need source from event
  onCellValueChanged: (event) => { 
        // Handle the event here
        const { api, data, colDef, newValue, oldValue, node, source } = event;

        console.log('node=>',node);

        if(source == 'edit' &&  calc_col_edit_dbcd.includes(colDef.field) ){

          console.log(event);
          console.log(event.source);

          console.log('Cell value changed to:', newValue);
          console.log('Old value:', oldValue);

          if(auto_save==1){
            getSheetCalcFromCellEdit(data,colDef.field,newValue,current_year,current_month,node.id);
          }else{
            console.log('save change cell');
            //save touched cell to manual-lock-cell

            setIsManualLockCell(data.id,node.id,colDef.field,1,newValue);// set manual lock value


          }

         
      

        }


       // console.log('Row data:', data);

        // You can perform other actions here, e.g., send an API request
    }
  
};
// Create Grid: Create new grid within the #myGrid div, using the Grid Options object

// console.log(col_conf);
gridApi = new agGrid.createGrid(document.querySelector(element_content_id), gridOptions);



// $(selector).html(selector);
}

function onFilterTextBoxChanged() {
  gridApi.setGridOption(
    "quickFilterText",
    document.getElementById("filter-text-box").value,
  );
}






</script>



<script>

// backspace starts the editor on Windows
const KEY_BACKSPACE = 'Backspace';
class CalcNumericCellEditorLock  {
    eInput;
    cancelBeforeStart;

    // gets called once before the renderer is used
    init(params) {
        // create the cell
        this.eInput = document.createElement('input');
        this.eInput.className = 'ag-input-field-input'+( (typeof params.is_lock !== 'undefined' && params.is_lock==1)?('manual-lock-input'):('') );

        const eventKey = params.eventKey;

        if (eventKey === KEY_BACKSPACE) {
            this.eInput.value = '';
        } else if (this.isCharNumeric(eventKey)) {
            this.eInput.value = eventKey;
        } else {
            if (params.value !== undefined && params.value !== null) {
                this.eInput.value = params.value;
            }
        }

        this.eInput.addEventListener('keydown', (event) => {
            if (!event.key || event.key.length !== 1) {
                return;
            }

            if (!this.isNumericKey(event)) {
                this.eInput.focus();
                if (event.preventDefault) event.preventDefault();
            } else if (this.isNavigationKey(event) || this.isBackspace(event)) {
                event.stopPropagation();
            }
        });

        // only start edit if key pressed is a number, not a letter
        const isCharacter = eventKey && eventKey.length === 1;
        const isNotANumber = isCharacter && '1234567890'.indexOf(eventKey) < 0;
        this.cancelBeforeStart = !!isNotANumber;
    }

    isNumericKey(event) {
        const charStr = event.key;
        return this.isCharNumeric(charStr);
    }

    isBackspace(event) {
        return event.key === KEY_BACKSPACE;
    }

    isNavigationKey(event) {
        return event.key === 'ArrowLeft' || event.key === 'ArrowRight';
    }

    isCharNumeric(charStr) {
        return charStr && !!/^\d+$/.test(charStr);
    }

    // gets called once when grid ready to insert the element
    getGui() {
        return this.eInput;
    }

    // focus and select can be done after the gui is attached
    afterGuiAttached() {
        this.eInput.focus();
    }

    // returns the new value after editing
    isCancelBeforeStart() {
        return this.cancelBeforeStart;
    }

    // example - will reject the number if it contains the value 007
    // - not very practical, but demonstrates the method.
    // isCancelAfterEnd() {
    //     const value = this.getValue();
    //     return value.indexOf('007') >= 0;
    // }

    // returns the new value after editing
    getValue() {
        return this.eInput.value;
    }

    // any cleanup we need to be done here
    destroy() {
        // but this example is simple, no cleanup, we could  even leave this method out as it's optional
    }
}



function CustomEditManualLockAndAutoModeComponent(params) {

    // console.log(params);
    const row_id = params.data.id;
    const row_node_id = params.node.id;
    const excel_col = params.colDef.field;
    const is_manual_lock = chkIsManualLockCell(row_id,excel_col);

    const cellDiv = document.createElement('div');
    cellDiv.className = 'custom-edit-manual-lock-wrapper';
    let html = '';
    let val = params.valueFormatted ?? '';
    
    if(is_manual_lock){
      html= '<button type="button" class=" edit-lock-float is-lock" onclick="toggleManualLock(this,0);" data-field="'+excel_col+'" data-row-id="'+row_id+'" data-row-node-id="'+row_node_id+'"><i class="bi bi-pencil"></i></button><span class="cell-display-value">'+val+'</span>';
    }else{
      html = '<button type="button" class=" edit-lock-float is-unlock" onclick="toggleManualLock(this,1);" data-field="'+excel_col+'" data-row-id="'+row_id+'" data-row-node-id="'+row_node_id+'"><i class="bi bi-pencil"></i></button><span class="cell-display-value">'+val+'</span>';
    }
    cellDiv.innerHTML = html;
    return cellDiv;
}

function toggleManualLock(el,is_lock){
    const event = window.event;
    event.stopPropagation();
   // console.log('toggleManualLock');
    const row_id = $(el).data('row-id');
    const row_node_id = $(el).data('row-node-id');
    const excel_col = $(el).data('field');
    const rowNode = gridApi.getRowNode(row_node_id);
    console.log('click toggleManual Lock',row_id,excel_col,rowNode.data[excel_col]);

  console.log('typeof val',typeof rowNode.data[excel_col]);

  //check type of excel_col


    setIsManualLockCell(
      row_id,
      row_node_id,
      excel_col,
      is_lock,
      rowNode.data[excel_col]
      
    );

    
    //refresh cell
    gridApi.refreshCells({  
      force: true,
      columns: [excel_col],
      rowNodes: [ rowNode ]
    });


    if(is_lock){
      
      //start edit
      setTimeout(() => {
        gridApi.startEditingCell({
          rowIndex: rowNode.rowIndex,
          colKey: excel_col
      });
      
      }, 100);

      
    }else{
      //get auto value from calc data 
      //rowNode.setDataValue(excel_col, "un");
    }
    




    
    // setTimeout(() => {
    //   // gridApi.startEditingCell({
    //   //     rowIndex: gridApi.getFocusedCell().rowIndex,
    //   //     colKey: gridApi.getFocusedCell().column.getId()
    //   // });
    //   gridApi.stopEditing();
    // }, 500);
   
}

</script>




<script>
function getSheetCalcFromCellEdit(rowdata,excel_col,new_val,year,month,rowId){   
   console.log('getSheetCalcFromCellEdit',rowdata,excel_col,new_val,year,month);

   let sheet_name = current_sheet;
   let fc = 'datacost';
   let table_data_name = sheet_data_table_name;
   let col_name = current_def_col[excel_col].col_name;
   let json_rowdata = JSON.stringify(rowdata);
   let table_of_sheet = 'main';
  $.post('<?php echo base_url('cost/updateCalcSheetCellAndRecalc'); ?>',
    {json_rowdata,excel_col,col_name,new_val,year,month,fc,sheet_name,table_data_name,table_of_sheet},
    function(data, textStatus, jqXHR){

      if(data.result=='success'){
              
          let new_data = JSON.parse(data.json_new_data);
          console.log('new_data',new_data);
          //update grid data
          
          gridApi.getRowNode(rowId).setData(new_data);
          
      
      }else if( data.result == 'logout'){
          apiLogout();
      }else if(data.result=='failed'){
            
            if(data.errormsg != 'undefined'){

              alert('Error: '+data.errormsg);
            }

      }

});


}

function exportCalcResultToExcel(){

  var year = current_year;
  var month = current_month;
  var filter_data = encodeURIComponent(JSON.stringify({year,month}));

  window.open('<?php echo base_url('Cost/getCalcExport?filter_data=') ?>'+filter_data, '_blank');


}


// Frontend: Making an AJAX request and handling file download
function exportCalcResultToExcel_old2() {

  var year = current_year;
  var month = current_month;
  var filter_data = encodeURIComponent(JSON.stringify({year,month}));
  var url_link = '<?php echo base_url('Cost/getCalcExport?filter_data=') ?>'+filter_data;

   $('.upload_status').html('<span>กำลังประมวล สร้างไฟล์ Excel ส่งออก ... </span>');
   $('.waitloader-text').text('กำลังประมวลผล สร้างไฟล์ Excel ส่งออก ...');
   $('.waitloader-overlay').show();

    fetch('<?php echo base_url('Cost/getCalcExport?filter_data=') ?>'+filter_data, {
        method: 'POST',
    })
    .then(response => {
      console.log(response);
      response.blob();
      $('.upload_status').html('');
      $('.waitloader-overlay').hide();
    })
    .then(blob => {
      console.log(blob);
        const url = window.URL.createObjectURL(blob);

        console.log(url);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'คำนวณต้นทุน_'+year+"_"+month+".xlsx";
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
    })
    .catch(error => {console.error('Download failed:', error);alert('ดาวน์โหลดไม่สำเร็จ')});
}



function exportCalcResultToExcel_ajax(){


  var year = current_year;
  var month = current_month;
  var filter_data = encodeURIComponent(JSON.stringify({year,month}));
  var url_link = '<?php echo base_url('Cost/getCalcExport?filter_data=') ?>'+filter_data;


    // jQuery ajax
    $.ajax({
        type: "POST",
        url: url_link,
        data: null,
        success: function(response, status, xhr) {
            // check for a filename
            var filename = "";
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
            }

            var type = xhr.getResponseHeader('Content-Type');
            var blob = new Blob([response], { type: type });

            if (typeof window.navigator.msSaveBlob !== 'undefined') {
                // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
                window.navigator.msSaveBlob(blob, filename);
            } else {
                var URL = window.URL || window.webkitURL;
                var downloadUrl = URL.createObjectURL(blob);

                if (filename) {
                    // use HTML5 a[download] attribute to specify filename
                    var a = document.createElement("a");
                    // safari doesn't support this yet
                    if (typeof a.download === 'undefined') {
                        window.location = downloadUrl;
                    } else {
                        a.href = downloadUrl;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                    }
                } else {
                    window.location = downloadUrl;
                }

                setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 100); // cleanup
            }
        }
    });





}

</script>  