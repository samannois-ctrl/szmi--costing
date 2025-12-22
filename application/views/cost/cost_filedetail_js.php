<script>
var Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });
$( document ).ready(function() {

 
    loadData();
 
});


 

var current_json_sheets = {};
var current_json_info = {};


 function loadData(){
 
   $('.upload_status').html('<span>รอการแสดงผล ... </span>');
   $('.waitloader-text').text('รอการแสดงผล ...');
   $('.waitloader-overlay').show();
    var year = <?php echo json_encode($select_year);?>;
    var month = <?php echo json_encode($select_month);?>;
    var file_category = <?php echo json_encode($file_category);?>;
    var json_data = {year,month,file_category}

//    console.log(json_data);

     $.ajax({
         url: "<?php echo base_url('cost/getFileCategoryDetail'); ?>",
         type: "post",
         data: json_data,
         processData: true, //Not to process data
         //contentType: false, //Not to set contentType
         dataType:'json',
         success: function (data,textStatus) {
          
           if(data.result=='success'){
             
            current_json_sheets = JSON.parse(data.str_json_sheets);
 
            // build_tab_sheet_result();
            build_tab_sheet_result_fastload();
            $('.upload_status').html('');
             

           }else if( data.result == 'logout'){
           apiLogout();
         }else if(data.result=='failed'){
             $('.upload_status').html('');
             if(typeof data.errormsg != 'undefined'){

               $('.upload_status').html('<span class="text-danger">'+data.errormsg+'</span>');
             }else{
                $('.upload_status').html('<span class="text-danger">ไม่สามารถอ่านข้อมูลได้</span>');
            }

              
           }

             
         },
         error: function(jqXHR, textStatus, errorThrown) {
           //alert("Error: " + errorThrown);
           $('.upload_status').html('<span class="text-danger">พบข้อผิดพลาด '+errorThrown+'</span>');
           $('#preview_upload_file_content').html('');
       },

     }).always(  function (){

           
           $('.waitloader-overlay').hide();



      });



}

 
 
//-----Upload File----end


function build_tab_sheet_result(){


  let html_header = '<div class="pl-3 pt-1 pb-2 text-center">';

 

      html_header += '</div>';
                   

      $('#preview_upload_file_content').html(html_header);   


      let html_sheet_tab = build_tab_sheet_from_json(current_json_sheets,'#1e6d96','200px');


      $('#preview_upload_file_content').append(html_sheet_tab);


}



function build_tab_sheet_result_fastload(){


let html_header = '<div class="pl-3 pt-1 pb-2 text-center">';
     html_header += '</div>';
    
     $('#preview_upload_file_content').html(html_header);

    

    let html_sheet_tab = build_tab_sheet_from_json_fastload('#1e6d96','200px');


    $('#preview_upload_file_content').append(html_sheet_tab);

    setTimeout(() => {
      $('#sheet_preview1-tab').click();
    }, 200);
    
 }



</script>