<style>
	.tbl-user td,th{
		border: 1px solid #b7b7b7; 
		border-bottom: 1px solid #b7b7b7 !important;
		border-top: 1px solid #b7b7b7 !important;
		font-size: 0.85em;
		padding-left:3px;
		padding-right:3px;


	}


	.tbl-user th{
		padding-top:3px;
		padding-bottom:3px;
	}
	.tbl-user td{
		background: #ffffff;
	}

	.tbl-user{
		border: 1px solid #b7b7b7; 
	}

	.cell-permission-seperate{
		border-left: 5px solid #b7b7b7	!important;
	}

</style>

<div class="row">
	<div class="col-12"></div>
	
</div>

<table class="table tbl-user  "  style="width:auto;margin: auto;">
	
	


	 <thead>
		<tr >
			<th rowspan="2" style="background: #f1f1f1;vertical-align: middle;">
				ID
			</th>
			 

			<th rowspan="2" style="background: #f1f1f1;vertical-align: middle;">
				ชื่อผู้ใช้
			</th>
			<th rowspan="2" style="background: #f1f1f1;vertical-align: middle;">
				ชื่อ-นามสกุล
			</th>				
			 		 
			
			<?php foreach ($menu_list as $m => $menu) { ?>
				<th colspan="3" style="background: #fff4bf;text-align: center;" class="cell-permission-seperate">
				 	<?php echo $menu['title'];?>
				</th>
			<?php } ?>

			
		</tr>


		<tr style="font-size:0.85em;text-align: center;">

			<?php foreach ($menu_list as $m => $menu) { ?>
				<th style="background: #ffd3d3;padding-left:1px;padding-right: 1px;" class="cell-permission-seperate">
				 	ห้าม
				</th>
				<th style="background: #ade8ff;padding-left:1px;padding-right: 1px;">
				 	ดูข้อมูล
				</th>
				<th style="background: #5cff87;padding-left:1px;padding-right: 1px;">
				 	แก้ไข
				</th>
			<?php } ?>

		</tr>


	</thead> 

	<tbody>

 

		<?php 


		foreach ($user_list as $k => $v) { 


			 
				$user_id = $v['id'];
						$can_off = 1;
						// if(  $this->session->userdata('user_id')  ==  $v['id'] ){
						// 	$can_off = 0;
						// }

		?>
			<tr data-id="<?php echo $v['id'] ?>" class="row-data" data-login-name="<?php echo html_show($v['login_name']) ?>" data-user-name="<?php echo html_show($v['user_name']) ?>" >
				
				<td align="right">
					<?php echo $user_id ?>
				</td>


				<td align="left"  style="color: #0546df;">

					<?php echo html_show($v['login_name']) ?>

				</td>

				<td align="left">
					<?php echo html_show($v['user_name']) ?>
				</td>

			 

				<?php foreach ($menu_list as $m => $menu) {
					 
					$cur_perm = 0;
					$menu_id = $menu['id'];

					if( isset( $user_menu_perm_list[$user_id][$menu['id']] ) ){
						$cur_perm = $user_menu_perm_list[$user_id][$menu['id']];
					}


					

				?>

					<td style="text-align:center;" class="cell-permission-seperate">  
					 
						<?php if($is_editable) { ?>
							<input type="checkbox" id="perm-<?php echo $user_id."-".$menu_id."-0"  ?>" class="check-perm" 
							data-user-id="<?php echo $user_id?>"
							data-menu-id="<?php echo $menu_id?>"
							data-perm="0"

							<?php  echo ($cur_perm=='0')?('checked'):('') ?> 

							>
						<?php }else{
							    echo($cur_perm=='0')?('<i class="bi bi-check-lg" style="margin-left:-5px;" ></i>'):('');    
						} ?>

					</td>




					<td style="text-align:center;">  

						<?php if($is_editable) { ?>

							<input type="checkbox" id="perm-<?php echo $user_id."-".$menu_id."-1"  ?>" class="check-perm" 
							data-user-id="<?php echo $user_id?>"
							data-menu-id="<?php echo $menu_id?>"
							data-perm="1"

							<?php  echo ($cur_perm=='1')?('checked'):('') ?> 

							>
						<?php }else{
							    echo($cur_perm=='1')?('<i class="bi bi-check-lg" style="margin-left:-5px;" ></i>'):('');    
						} ?>

					</td>


					<td style="text-align:center;">  
						<?php if($is_editable) { ?>
							<input type="checkbox" id="perm-<?php echo $user_id."-".$menu_id."-2"  ?>" class="check-perm" 
							data-user-id="<?php echo $user_id?>"
							data-menu-id="<?php echo $menu_id?>"
							data-perm="2"

							<?php  echo ($cur_perm=='2')?('checked'):('') ?> 

							>
						<?php }else{
							    echo($cur_perm=='2')?('<i class="bi bi-check-lg" style="margin-left:-5px;" ></i>'):('');    
						} ?>

					</td>







				<?php } ?>



		</tr>
		<?php } ?>




	</tbody>





</table>


<script type="text/javascript">
	

$(document).ready( function(){	

		$('.check-perm').click(function(){

			var checked = $(this).prop('checked');
			if(!checked){
				return false;
			}


			var user_id = $(this).data('user-id');
			var menu_id = $(this).data('menu-id');
			var permission = $(this).data('perm');
			console.log(user_id, menu_id, permission);



			$('.check-perm[data-user-id="'+user_id+'"][data-menu-id="'+menu_id+'"]').prop('checked',false);

			$(this).prop('checked',true);


			submitCheckPermission(user_id, menu_id, permission);




		});


});



function submitCheckPermission(user_id, menu_id, permission){


   		$.post('<?php echo base_url('admin/setPermissionUserMenu')?>',{user_id, menu_id, permission},function(data){
     

        if(data.result = 'success'){

            
           Toast.fire({
              icon: 'success',
              title: 'บันทึกข้อมูลสำเร็จ'
            });



        }else if( data.result == 'logout'){
          apiLogout();
        }else{


        	Toast.fire({
              icon: 'error',
              title: 'ไม่สำเร็จ'
            });
        }



   })



}


</script>



