<script>
var Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });
$( document ).ready(function() {

 

 
});


function change_select_year_month(ele){
	var select_year =$('#select-year').val();
	var select_month = $('#select-month').val();
	var url = '<?php echo base_url('cost/list') ?>' + '?year=' + select_year + '&month=' + select_month;
	//console.log(url);
	window.location.href = url;
}


</script>