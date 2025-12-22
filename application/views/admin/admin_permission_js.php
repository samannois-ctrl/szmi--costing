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

    
   $.post('<?php echo base_url('admin/getPermissionTable')?>',null,function(data){
     

        if(data.result = 'success'){

            $('#showdata').html(data.html);


        }else if( data.result == 'logout'){
          apiLogout();
        }



   })

}



</script>