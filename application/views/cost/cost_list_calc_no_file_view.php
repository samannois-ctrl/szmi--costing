<style>
	 
.tbl-pos-data{
	table-layout: fixed;
	border-collapse: collapse;
	border-spacing: 0;


}

.tbl-pos-data td, .tbl-pos-data th{
	font-size: 0.85em;
	padding-left: 3px;
	padding-right: 3px;
	padding-top: 5px;
	padding-bottom: 5px;
	border: 1px solid #dee2e6;
}

.tbl-pos-data th{

	text-align: center;
}

#tbl-pos-main{
	table-layout: fixed;
	border-collapse: collapse;
	border-spacing: 0;
	position: relative;;


}

#tbl-pos-main td, #tbl-pos-main th{
	font-size: 0.85em;
	padding-left: 3px;
	padding-right: 3px;
	padding-top: 5px;
	padding-bottom: 5px;
	
	border: 1px solid #cfcfcf;
}

#tbl-pos-main th{

	position: sticky;
	top:0;

}


.row-appform-used td{
	background: #e2f9fb !important;
}

.filter-row .col-auto{
	padding-top:10px;
}

.tbl-form-pos td{
	padding-top: 10px;
}



</style>

<div class="content-wrapper" style="background: #ffffff;">





<section class="content " style="padding-top: 20px;">

		<!-- Cost List =========================================== -->
		<div class="container-fluid">

		
			<div class="row mb-2" >

			<div class="col-sm-6  row">
				<div class="col-auto"><span class="menu-header-first-line">คำนวณต้นทุน</span></div> <div class="col ml-5" ><?php $this->load->view('element/select_year_month') ?></div>
			</div>

			<div class="col-auto">
				
			</div>

			<div class="col-auto">

							
			</div>


			<div class="col-auto">



			</div>

			</div>


<!-- sub tab menu start-->
		<div class="row main-submenu-container" style="padding: 0;">
            

		<div class="col-auto main-submenu " style="">
					<a href="<?php echo base_url('cost/list?year='.$select_year.'&month='.$select_month) ?>"  >อัพโหลดไฟล์</a>
			</div>

			<div class="col-auto main-submenu active" style="">
					<a href="<?php echo base_url('cost/calc?year='.$select_year.'&month='.$select_month) ?>"  >คำนวณต้นทุน</a>
			</div>

			<div class="col main-submenu " style="">
					 &nbsp;
			</div>
        </div>
<!-- sub tab menu end-->


<!-- filter start-->





<!-- filter end-->
		</div>



		<!-- Calc List =========================================== -->

		<div class="row">
			<div class="col-12 text-center mt-5 mb-5">
            <p class="p-0  minor-title-content2 text-danger" style="font-size:1.3rem;">ไฟล์อัพโหลดยังไม่ครบ - <?php echo monthYearShowFull($select_year,$select_month); ?> </p>
			</div>
		</div>

		<div class="row">
			<div class="col-12 text-center">
            
            <a class="btn btn-primary" href="<?php echo base_url('cost/list?year='.$select_year.'&month='.$select_month); ?>">  <i class="fa  fa-arrow-left"></i> กลับไปหน้าอัพโหลด </a>
            
			</div>

		</div>		


		
		

 
		<div class="compare-table">


		</div>

</section>

</div>
 

<script>

function change_select_year_month(ele){
	var select_year =$('#select-year').val();
	var select_month = $('#select-month').val();
	var url = '<?php echo base_url('cost/calc') ?>' + '?year=' + select_year + '&month=' + select_month;
	//console.log(url);
	window.location.href = url;
}



</script>