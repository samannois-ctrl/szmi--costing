<?php
//cal total width
$totalWidth=0;
	foreach ($this->Pos_model->map_excel_col_pos as $col => $attr) { 
		// code...
		$totalWidth += $attr['width'];
	}



?>




<table class="table   " id="tbl-pos-main" style="width:<?php echo ($totalWidth+230); ?>px;margin-right:20px;">

	<thead>
		<tr>
			<th class="notexport" style="width:50px;background: #f3f7ff;color:#363636;text-align: 	center;vertical-align: 	middle;">
				<i class="bi bi-trash"></i>
			</th>

			<th class="notexport" style="width:60px;background: #f3f7ff;color:#363636;text-align: 	center;vertical-align: 	middle;">
				<i class="bi bi-pencil-sqaure"></i>
			</th>


			<th class="notexport" style="width:60px;background: #f3f7ff;color:#363636;text-align: 	center;vertical-align: 	middle;">
				รอแต่งตั้ง
			</th>
			<th class="notexport" style="width:60px;background: #f3f7ff;color:#363636;text-align: 	center;vertical-align: 	middle;">
				แต่งตั้ง
			</th>

		<?php 


		foreach ($this->Pos_model->map_excel_col_pos as $col => $attr) { ?>


			<th style="width:<?php echo $attr['width'] ?>px;background: #f3f7ff;color:#363636;text-align: 	center;vertical-align: 	middle;">
				<?php echo $attr['excel_col'] ?>
			</th>
			 
		<?php }?>

		</tr>
	</thead>


	<tbody>



<?php if(empty($pos_list)) { ?>


		<tr>
			<td colspan="<?php echo count($this->Pos_model->map_excel_col_pos)+4 ?>" align="center">
				------ ไม่มีข้อมูล ------
			</td>
		</tr>



<?php }else{  ?>	
		
		<?php 

			$ord = 0;
			$row_idx = -1;
			foreach ($pos_list as $k => $row) { 

				$ord++;
				$row_idx++;
				 

				$can_delete = 1;

				if( $row['is_appoint_used']){
					$can_delete=0;
				}

				$can_edit = 1;

				if( $row['is_appoint_used']){
					$can_edit=0;
				}
		?>



			<tr class="item-row <?php echo($row['is_appoint_wait'])?('item-row-in-appoint-wait'):('');?> " data-row-idx="<?php echo $row_idx;?>">

				<td style="width:50px" align="center">


					<?php if($can_delete==1) { ?>
					<button class="btn btn-sm btn-danger btn-delete-row" onclick="confirmTodelete(this)"

					data-id = "<?php echo $row['id']?>"	
					data-fullname = "<?php echo html_show($row['fullname'])?>"
				

					>ลบ</button>
					<?php } ?>

				</td>

				<td style="width:60px" align="center">
	

					<?php if($can_edit	==1) { ?>
					<button class="btn btn-sm btn-info btn-edit-row" onclick="openEditPos('<?php echo $row['id'] ?>','<?php echo $row_idx;?>')"

					data-id = "<?php echo $row['id']?>"	
					
				

					>แก้ไข</button>
					<?php } ?>

					 

				</td>

				<td style="width:40px" align="center">

					<?php if($row['fullname']!='ว่าง') {?> 


						<?php if($is_editable) {?>

								<?php if(  $row['is_appoint_wait']==1 && $row['is_appoint_used']==1  ) { ?>

									<i class="bi bi-check-lg"   ></i>

								<?php }else{ ?>
							 
									<input type="checkbox" style="margin-top:8px;" class=" chk-row-is-appoint-wait" 
									data-pos-id="<?php echo $row['id'] ?>" 
									onclick="updateIsAppointWait(this)"

									<?php echo($row['is_appoint_wait']==1)?('checked'):(''); ?>
									>

								<?php } ?>

						<?php }else{ //if($is_editable) 


								echo ($row['is_appoint_wait']==1)?('<i class="bi bi-check-lg"   ></i>'):('');

						} //if($is_editable) ?>
					 
					 
					<?php } ?>
				</td>
				<td style="width:40px" align="center">

					<?php if($row['fullname']=='ว่าง') {?>
					<button class="btn btn-sm btn-success btn-appoint-row" style="font-size: 0.97em;padding-left:3px ;padding-right:3px;" 


					<?php if($row['data_src']=='newvac' && isset($row['appoint_loc_id'])) { ?>

						onclick="goToAppointLocReal('<?php echo $row['appoint_loc_id'] ?>')"

					<?php }else{ ?>
						onclick="goToAppointLocFromPos('<?php echo $row['id'] ?>')"

					<?php } ?>

					data-id = "<?php echo $row['id']?>"	
				

					>แต่งตั้ง</button>
					<?php } ?>
				</td>

				<?php foreach ($this->Pos_model->map_excel_col_pos as $col => $attr) { ?>

					<td style="width:<?php echo $attr['width'] ?>px">
						
						<?php 
							if($col=='seq'){

								echo $ord;


							}else if($attr['type']=='date'){

								echo dateTHShort($row[$col]);

							}else{


								echo $row[$col];

							} 
						?>

					</td>

				<?php }?>



			</tr>

		<?php }?>




<?php }//if(empty($pos_list))  ?>	

	</tbody>









</table>



<div class="m-4" style="text-align: center;">
	<a href="#" onclick="$(window).scrollTop(0);return false;" > กลับสู่ด้านบน </a>
</div>



<script type="text/javascript">
	




var tblmain;
$( document ).ready(function() {

<?php if(!empty($pos_list)) { ?>

	tblmain = $('#tbl-pos-main').DataTable({


						'responsive'  : false,
			            'paging'      : false,
			            'lengthChange': false,
			            'searching'   : false,
			            'ordering'    : false,
			            'info'        : true,
			            'autoWidth'   : false,

			            <?php if(!$is_editable) { ?>
			             'columnDefs': [{ visible: false, targets: [0,1] }],
			            <?php } ?>

			            'layout'	  : 
								        {
										    topEnd: {buttons:[

										    			{
																extend: 'excel',
													            text: '<i class="bi bi-download"></i> ดาวน์โหลด excel ',
													            className: 'excel-btn-dt btn-sm',
													            exportOptions: {
														            columns: ':not(.notexport)'
														        },
														        createEmptyCells: true,
														        title: ('รายการทำเนียบ ปี ' + <?php echo $year_th ?>) ,


														    excelStyles: [   
															    {
															    cells: "st",
															    style:{
															    	alignment: {
												                        vertical: "center",
												                        horizontal: "left",

												                    },
												                    font:{
												                        b: true,
												                        //font: "tahoma",
												                        //size: "12",
                    												},



															    }



															    },



														    	{          // Add an excelStyles definition
												                cells: "2",                     // to row 2
												                style: {                        // The style block
												                    font: {                     // Style the font
												                        // name: "Arial",          // Font name
												                        // size: "14",             // Font size
												                        color: "000000",        // Font Color
												                        b: true,               // Remove bolding from header row
												                    },
												                    fill: {                     // Style the cell fill (background)
												                        pattern: {              // Type of fill (pattern or gradient)
												                            color: "f3f7ff",    // Fill color
												                        }
												                    },
												                    border:{
												                    	top : "hair",
												                    	bottom : "hair",
												                    	left : "hair",
												                    	right : "hair",
												                    }
												                }},


														    	{          // Add an excelStyles definition
												                cells: "3:",                     // to row 2
												                style: {                        // The style block
												                    font: {                     // Style the font
												                        // name: "Arial",          // Font name
												                        // size: "14",             // Font size
												                        color: "000000",        // Font Color
												                        //b: false,               // Remove bolding from header row
												                    },
												                    fill: {                     // Style the cell fill (background)
												                        pattern: {              // Type of fill (pattern or gradient)
												                            color: "ffffff",    // Fill color
												                        }
												                    },
												                    border:{
												                    	top : "hair",
												                    	bottom : "hair",
												                    	left : "hair",
												                    	right : "hair",
												                    }
												                }}


												                ]
												                ,
												            

											        	}

										    				  ]},
										    topStart: 'info',
										    //topEnd: 'search',
										    bottomStart: null,
										    bottomEnd: null,
										},





			            "language": {
									    "search": "ค้นหา :",
									    "zeroRecords": "ไม่พบข้อมูล",
									    "info": "แถว _START_ to _END_ จากทั้งหมด _TOTAL_ รายการ"
									  },


						




	});


			            
<?php } ?>






// 	$('#tbl-pos-main').on('click', '.btn-delete-row', function () {
//     tblmain
//         .row($(this).parents('tr'))
//         .remove();
        


//     tblmain.draw();
// });



		

});


function confirmTodelete(el){

	var fullname = $(el).data('fullname')
	var id = $(el).data('id')
    var del_select_html = 'ลบรายการของ ' + fullname + '<br>'
    						+ getDeleteNoteHTML();

	Swal.fire({
	  title: "ยืนยันการลบรายการ ?",
	  html: del_select_html,
	  showDenyButton: false,
	  showCancelButton: true,
	  confirmButtonText: "ยืนยันลบ",
	  denyButtonText: "ไม่",
	  cancelButtonText: "ไม่",
	  preConfirm: (s)=>{

	  	 if(isEmptyString($('.input_delete_note').val()) ){
	    	alert('กรุณาระบุหมายเหตุในการลบรายการนี้');
	    	return false;
	    }else{
	    	make_remove_row(id,el)
	    }
	    
	  },
	}).then((result) => {

	  if (result.isConfirmed) {
	     
	  } else if (result.isDenied) {
	     
	  }
	});


}




</script>