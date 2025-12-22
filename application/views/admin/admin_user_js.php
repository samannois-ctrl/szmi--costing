<script type="text/javascript">
var Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });
  

$( document ).ready(function() {

    loadTable();
    
});


function loadTable(){

    
   $.post('<?php echo base_url('admin/getUserTable')?>',null,function(data){
     

        if(data.result = 'success'){

            $('#showdata').html(data.html);


        }else if( data.result == 'logout'){
          apiLogout();
        }



   })

}




 function openAddDataForm(){

    resetForm();
    $('#modal-add').modal('show');
    $('#modify-mode').val('add');
    $('#btn-confirm').text('เพิ่มข้อมูล');
    $('.modal-header').addClass('bg-success').removeClass('bg-info');
    $('.modal-title').text('เพิ่มผู้ใช้');


    $('#label-password').html('ตั้งรหัสผ่าน');



  }

function openEditDataForm(id){

   resetForm();
      var login_name = $('.row-data[data-id="'+id+'"]').data('login-name');
      var user_name = $('.row-data[data-id="'+id+'"]').data('user-name');

    // console.log({accsub_code, accsub_name});

              $('#modal-add').modal('show');
              $('#modify-mode').val('edit');
              $('#btn-confirm').text('บันทึก')
              $('.modal-header').addClass('bg-info').removeClass('bg-success');
              $('.modal-title').text('แก้ไขข้อมูลผู้ใช้');

              $('#label-password').html('เปลี่ยนรหัสผ่านใหม่ <br><small>(ถ้าไม่ต้องการเปลี่ยนรหัสผ่าน ไม่ต้องกรอก)</small>');


              fillForm({id,login_name, user_name});


        


}

function fillForm(data){

    $('#input-user-name').val(data.user_name);
    $('#input-login-name').val(data.login_name);
    $('#hidden-id').val(data.id);
    $('#input-login-name').data('self-code',data.login_name);

    clearInputError();

  }

function resetForm(){

    clearInputError();
    
    $('#input-user-name').val('');
    $('#input-login-name').val('');
    $('#input-user-pass').val('');
    $('#hidden-id').val('');

    $('#input-login-name').data('self-code','');
   


  }



function clearInputError(){

    $('.input-error-warn').each(function(){

      $(this).removeClass('input-error-warn');

    });

    $('#code-error').text('');

}

function modifyData(){

    if($('#modify-mode').val()=='add'){
      submitAddData();
    }else if($('#modify-mode').val()=='edit'){
      submitEditData();

    }

}


function checkcode(el){

 
  //ถ้ายังไม่ popup ก็ไม่ต้องเช็ค เพราะบางกรณี chrome เติมช่อง user login โดยอัตโนมัติเอง ถ้าไม่ดักไว้ พอเรา popup ทีหลัง มันจะ checkcode จากที่ chrome ใส่ไว้อัตโนมัติ
  if( !($("#modal-add").data('bs.modal') || {})._isShown ) return;


    var new_code = $(el).val();
    if($('#modify-mode').val()=='add'){
            
      if( String(new_code).trim().length == 0){

        return true;

      }
      
    }else if($('#modify-mode').val()=='edit'){
      
      var old_code = $(el).data('self-code');
      console.log('old_code',old_code);
      console.log('new_code',new_code);
      // console.log(String(new_ccode).trim().length);
      // console.log(String(new_ccode).trim());
      // console.log(String(old_ccode).trim());
      if( String(new_code).trim().length == 0 || String(new_code).trim() == String(old_code).trim()){
        
        clearInputError();
        return true;

      }

    }


    //check

       $.post('<?php echo base_url('admin/checkExistedCode')?>',{new_code},function(data){ 

    //console.log('call getDebtTable')

      if(data.existed == '1'){

           Toast.fire({
              icon: 'error',
              title: 'ชื่อนี้มีการใช้งานแล้ว'
            });

           $('#code-error').text('ชื่อนี้มีการใช้งานแล้ว');

           return

      }else if(data.existed == '0'){
          clearInputError();
         
      }else{
         
        
      }





    });





  }

  function submitAddData(){

    var user_name = $('#input-user-name').val();
    var login_name = $('#input-login-name').val();
    var user_password = $('#input-user-pass').val();
   


    if(String(user_name).trim().length == 0){ 

      alert('กรุณากรอกชื่อนามสกุล');
      $('#input-user-name').addClass('input-error-warn');
      return;
    }


    if(String(login_name).trim().length == 0){ 

      alert('กรุณากรอกชื่อผ้ใช้');
      $('#input-login-name').addClass('input-error-warn');
      return;
    }



    if(String(user_password).trim().length == 0){ 

      alert('กรุณากรอกรหัสผ่าน');
      $('#input-user-pass').addClass('input-error-warn');
      return;
    }

    var json = {login_name,user_password,user_name};

    // console.log(json);


    $.post('<?php echo base_url('admin/addNewUser')?>',json,function(data){ 

    //console.log('call getDebtTable')

      if(data.result == 'success'){

          $('#modal-add').modal('hide');

          Toast.fire({
                    icon: 'success',
                    title: 'บันทึกสำเร็จ',
                    timer:800,
                  })

          loadTable();



      }else if(data.result == 'code_duplicate'){
         
          Toast.fire({
              icon: 'error',
              title: 'ชื่อผู้ใช้นี้มีอยู่ในระบบแล้ว'
            })
      }else if(data.result == 'admin_fail'){
         
          Toast.fire({
              icon: 'error',
              title: 'ไม่มีสิทธิ์แก้ข้อมูลผู้ใช้'
            })



      }else if( data.result == 'logout'){
          apiLogout();
      }else{
          //$('#modal-add').modal('hide');

            Toast.fire({
              icon: 'error',
              title: 'ไม่สำเร็จ พบข้อผิดพลาด'
            })
        
      }





    });



  }


function submitEditData(){

    var user_name = $('#input-user-name').val();
    var login_name = $('#input-login-name').val();
    var new_user_password = $('#input-user-pass').val();
    var id = $('#hidden-id').val();
   


    if(String(user_name).trim().length == 0){ 

      alert('กรุณากรอกชื่อนามสกุล');
      $('#input-user-name').addClass('input-error-warn');
      return;
    }


    if(String(login_name).trim().length == 0){ 

      alert('กรุณากรอกชื่อผ้ใช้');
      $('#input-login-name').addClass('input-error-warn');
      return;
    }



    

    var json = {id,login_name,new_user_password,user_name};

    // console.log(json);


    $.post('<?php echo base_url('admin/editUser')?>',json,function(data){ 

    //console.log('call getDebtTable')

      if(data.result == 'success'){

          $('#modal-add').modal('hide');

          Toast.fire({
                    icon: 'success',
                    title: 'บันทึกสำเร็จ',
                    timer:800,
                  })

          loadTable();



      }else if(data.result == 'code_duplicate'){
         
          Toast.fire({
              icon: 'error',
              title: 'ชื่อผู้ใช้นี้มีอยู่ในระบบแล้ว'
            })
      }else if(data.result == 'admin_fail'){
         
          Toast.fire({
              icon: 'error',
              title: 'ไม่มีสิทธิ์แก้ข้อมูลผู้ใช้'
            })



      }else if( data.result == 'logout'){
          apiLogout();
      }else{
          //$('#modal-add').modal('hide');

            Toast.fire({
              icon: 'error',
              title: 'ไม่สำเร็จ พบข้อผิดพลาด'
            })
        
      }





    });



  }

  

function deleteData(id){


      var login_name = $('.row-data[data-id="'+id+'"]').data('login-name');
      var user_name = $('.row-data[data-id="'+id+'"]').data('user-name');

      if(confirm("ยืนยันลบข้อมูลผู้ใช้ " + login_name + " ( "+ user_name +" ) ")){

        submitDeleteData(id);

      }else{


        return false;

      }

        

}


function submitDeleteData(id){



    var json = {id};

    // console.log(json);


    $.post('<?php echo base_url('admin/updateDeleteUser')?>',json,function(data){ 

    //console.log('call getDebtTable')

      if(data.result == 'success'){

          $('#modal-add').modal('hide');

          Toast.fire({
                    icon: 'success',
                    title: 'ลบข้อมูลสำเร็จ',
                    timer:800,
                  })

          loadTable();



      }else if(data.result == 'admin_fail'){
         
          Toast.fire({
              icon: 'error',
              title: 'ไม่มีสิทธิ์ลบข้อมูลผู้ใช้'
            })



      }else if( data.result == 'logout'){
          apiLogout();
      }else{
          //$('#modal-add').modal('hide');

            Toast.fire({
              icon: 'error',
              title: 'ไม่สำเร็จ พบข้อผิดพลาด'
            })
        
      }





    });



  }

         




function updateIsActive(el){

    var value = $(el).prop('checked')?('1'):('0');

    var id = $(el).parents('.row-data').data('id');

    var json = {id,value};

    $.post('<?php echo base_url('admin/updateIsActiveUser')?>',json,function(data){ 

    //console.log('call getDebtTable')

      if(data.result == 'success'){

          $('#modal-add').modal('hide');

          Toast.fire({
                    icon: 'success',
                    title: 'บันทึกข้อมูลสำเร็จ',
                    timer:800,
                  })

          loadTable();



      }else if(data.result == 'admin_fail'){
         
          Toast.fire({
              icon: 'error',
              title: 'ไม่มีสิทธิ์บันทึกข้อมูลผู้ใช้'
            })



      }else if( data.result == 'logout'){
          apiLogout();
      }else{
          //$('#modal-add').modal('hide');

            Toast.fire({
              icon: 'error',
              title: 'ไม่สำเร็จ พบข้อผิดพลาด'
            })
        
      }





    });



  }
//-----------------------
function togglepassword(el){

    var i = $('#togglePassword');

    

    if($(i).data('show') == '0'){

        $(i).data('show','1');
        $(i).addClass('bi-eye').removeClass('bi-eye-slash');
        $('#input-user-pass').attr('type','text');


    }else if($(i).data('show') == '1'){

        $(i).data('show','0');
        $(i).addClass('bi-eye-slash').removeClass('bi-eye');
        $('#input-user-pass').attr('type','password');

    }



}

</script>