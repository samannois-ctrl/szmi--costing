<script>
var Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });
$( document ).ready(function() {

 

 
});





//-----Upload File----start

var confirm_upload = {};
confirm_upload['str_json_sheets'] = {};
confirm_upload['str_json_sheets_compact'] = {};
confirm_upload['str_json_info'] = {};
confirm_upload['file_info'] = {};

var current_json_sheets = {};
var current_json_sheets2 = {};
var current_json_info = {};


$(document).ready(function () {


$('#file_excel').change(function(){


   var formData = new FormData($('#form-excel-upload')[0]);
   $('#btn_file_excel').hide();
   $('.upload_status').html('<span>กำลังอัพโหลดไฟล์ ... </span>');
   $('.waitloader-text').text('กำลังอัพโหลดไฟล์ ...');
   $('.waitloader-overlay').show();

   $('.confirm_save_upload_result').html('ยังไม่ได้กดยืนยันบันทึก');
   $('.confirm_save_upload_list').empty();

     $.ajax({
      url: "<?php echo base_url('cost/uploadFileCategoryRecieveObjectFastExcel'); ?>",
      // url: "<?php echo base_url('cost/uploadFileCategoryRecieveObject'); ?>",

         
         type: "post",
         data: formData,
         processData: false, //Not to process data
         contentType: false, //Not to set contentType
         //dataType:'json',
         success: function (data,textStatus) {


         // $('#preview_upload_file_content').html(data);return;

           if(data.result=='success'){
            
            $('#link-upload-step2').tab('show');
            $('#preview_upload_file_content').empty();
            
            
            //confirm_upload['str_json_sheets'] =  data.str_json_sheets;
            confirm_upload['str_json_sheets_compact'] =  data.str_json_sheets_compact_data;
            confirm_upload['str_json_info'] =  data.str_json_info;
 
            confirm_upload['file_info'] = data.file_info;

            // current_json_sheets = JSON.parse(data.str_json_sheets_compact_display);
            current_json_sheets = JSON.parse(data.str_json_sheets_compact_data);
            current_json_info = JSON.parse(data.str_json_info);

            build_tab_sheet_result_fastload();
            
           
             $('.upload_status').html('');

           }else if( data.result == 'logout'){
           apiLogout();
         }else if(data.result=='failed'){
             $('.upload_status').html('');
             if(data.errormsg != 'undefined'){

               $('.upload_status').html('<span class="text-danger">'+data.errormsg+'</span>');
             }

             $('#preview_upload_file_content').html('');



           }

             
         },
         error: function(jqXHR, textStatus, errorThrown) {
           //alert("Error: " + errorThrown);
           $('.upload_status').html('<span class="text-danger">พบข้อผิดพลาด '+errorThrown+'</span>');
           $('#preview_upload_file_content').html('');
       },

     }).always(  function (){

           $('#form-excel-upload')[0].reset();
           $('#btn_file_excel').show();
           $('.waitloader-overlay').hide();



      });



});





});


function confirm_save_upload_sheet(){

   $('#btn-confirm-save').hide();
   $('.confirm_send_upload_status').html('<span>กำลังอัพโหลดไฟล์ ... </span>');
   $('.waitloader-text').text('กำลังอัพโหลดไฟล์ ...');
   $('.waitloader-overlay').show();
   $('.confirm_save_upload_result').empty();
   $('.confirm_save_upload_list').empty();
   

  // //  $.post('<?php echo base_url('test.html'); ?>',{'data' : confirm_upload['str_json_sheets_compact']},function(data){
  // //     console.log(data);
  // //  },'json');
  //  $.post('<?php echo base_url('test.html'); ?>',{'data' : 'xxx'},function(data){
  //     console.log(data);
  //  },'json');
  $.ajax({
    //url: "<?php echo base_url('test.html'); ?>",
    url: "<?php echo base_url('cost/uploadFileCategoryConfirmSaveObject'); ?>",
    type: "post",
         data: confirm_upload,
        //  data: {'data' : confirm_upload['str_json_sheets_compact']},
         processData: true, //Not to process data
         //contentType: false, //Not to set contentType
         dataType:'json',
         success: function (data,textStatus) {
          $('#link-upload-step3').tab('show');
           if(data.result=='success'){

            //$('#preview_upload_file_content').html('');
           
            
           
            $('.confirm_save_upload_result').html('บันทึกข้อมูลสำเร็จ');
            let li_html = "";
            if(typeof data.res_save_db !== 'undefined'){
              li_html += "<tbody>";
                $.each(data.res_save_db.sheets, function(i, sheet_data) {
                  
                  let tb_row = 1;
                  $.each(sheet_data.tables,function(tb_name, tb_info){
                    
                    let sh_name = "";
                    let cls_top_border = "";
                    if(tb_row==1){
                       sh_name = sheet_data.real_sheet_name_on_excel;
                       cls_top_border = "tr_top_border";
                    }
                    li_html += '<tr class="' + cls_top_border + '">';
                    li_html += "<td>"+   sh_name   +"</td>"
                    li_html += "<td>";
                    li_html += "<span>ตาราง "+ tb_info.table_title +"</span>";
                    li_html += "</td>";
                    li_html += "<td>";
                    li_html += "<span>"+ (tb_info.result=='success')?('<i class="bi bi-check-circle-fill text-success"></i>'):('<i class="bi bi-x-circle-fill text-danger"></i>') +"</span>";
                    li_html += "</td>";
                    li_html += '<td style="text-align:right;" >';
                    li_html += "<span> "+ formatIntegerNumber(tb_info.count_insert) +" แถว</span>";
                    li_html += "</td>";
                    li_html += "</tr>";
                    tb_row++;

                    
                  });

                  
                  
              });

              li_html += "</tbody>";

              
              $('.confirm_save_upload_list').html(li_html);

            }


           }else if( data.result == 'logout'){
           apiLogout();
         }else if(data.result=='failed'){
             $('.confirm_save_upload_result').html('');
             if(data.errormsg != 'undefined'){

               $('.confirm_save_upload_result').html('<span class="text-danger">'+data.errormsg+'</span>');
             }



           }

             
         },
         error: function(jqXHR, textStatus, errorThrown) {
           //alert("Error: " + errorThrown);
           $('.confirm_save_upload_result').html('<span class="text-danger">พบข้อผิดพลาด '+errorThrown+'</span>');
       },

     }).always(  function (){

           
           $('#btn-confirm-save').show();
           $('.confirm_send_upload_status').html('ส่งยืนยันบันทึกข้อมูลแล้ว');
           $('.waitloader-overlay').hide();



      });





}

//-----Upload File----end


function build_tab_sheet_result(){


  let html_header = '<div class="pl-3 pt-1 pb-2 text-center">';

      if(current_json_info.is_all_valid_sheet==1){
        html_header += '<span>*** กรุณาตรวจสอบก่อนกดปุ่มยืนยันบันทึกข้อมูล ***</span>';
        html_header += '<button class="btn btn-primary" id="btn-confirm-save" onclick="confirm_save_upload_sheet()"> <i class="bi bi-floppy2-fill"></i> ยืนยันบันทึกข้อมูล  </button>';
      }else{
        html_header += '<span class="text-danger">*** ข้อมูลในไฟล์นี้ยังไม่ถูกต้องตรงกับโครงสร้างที่กำหนดไว้ โปรดตรวจสอบ ***</span>';
      }

      html_header += '<span class="confirm_send_upload_status"></span>';

      html_header += '</div>';
                   

      $('#preview_upload_file_content').html(html_header);   


      let html_sheet_tab = build_tab_sheet_from_json(current_json_sheets);


      $('#preview_upload_file_content').append(html_sheet_tab);


}



function build_tab_sheet_result_fastload(){


let html_header = '<div class="pl-3 pt-1 pb-2 text-center">';

    if(current_json_info.is_all_valid_sheet==1){
      html_header += '<span>*** กรุณาตรวจสอบก่อนกดปุ่มยืนยันบันทึกข้อมูล ***</span>';
      html_header += '<button class="btn btn-primary" id="btn-confirm-save" onclick="confirm_save_upload_sheet()"> <i class="bi bi-floppy2-fill"></i> ยืนยันบันทึกข้อมูล  </button>';
    }else{
      html_header += '<span class="text-danger">*** ข้อมูลในไฟล์นี้ยังไม่ถูกต้องตรงกับโครงสร้างที่กำหนดไว้ โปรดตรวจสอบ ***</span>';
      $('#preview_upload_file_content').html(html_header);
      return;
    }

    html_header += '<span class="confirm_send_upload_status"></span>';

    html_header += '</div>';
                 

    $('#preview_upload_file_content').html(html_header);   


    let html_sheet_tab = build_tab_sheet_from_json_fastload();


    $('#preview_upload_file_content').append(html_sheet_tab);

    setTimeout(() => {
      $('#sheet_preview1-tab').click();
    }, 200);
    
 }


</script>