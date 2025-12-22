<style>
	.tbl-user td,th{
		border: 1px solid #dee2e6; 

	}


</style>

<div class="row">
	<div class="col-12"></div>
	
</div>

<table class="table tbl-user  "  style="width:auto;margin: auto;">
	

	<?php if($is_editable) { ?>
	<thead style="border: none;">
		<tr style="border: none;">
			<td colspan="7" align="right" style="border: none;">
				<button type="button" class="btn btn-sm btn-success" onclick="openAddDataForm();">เพิ่มผู้ใช้</button>
			</td>
		</tr>

	</thead>
	<?php } ?>


	 <thead>
		<tr style="background: #e5e5e5;">
			<th>
				ID
			</th>
			 

			<th>
				ชื่อผู้ใช้(สำหรับเข้าระบบ)
			</th>
			<th>
				ชื่อ-นามสกุล
			</th>				
			 		 
			<th>
				เปิดใช้งาน
			</th>	


			<?php if($is_editable) {?>		
			<th>
				 
			</th>
			<th>
				 
			</th>
			<?php } ?>
		</tr>

	</thead> 

	<tbody>

<!-- <pre>Array
(
    [user_list] => Array
        (
            [id] => 1
            [login_name] => admin
            [user_name] => System Admin
            [hash_password] => $2y$10$ypFlfZWW2Di9Q1GbMTbvf.ClsiwstIkk.p1MyvaBxtlQbcTNkdk8m
            [is_admin] => 1
            [is_deleted] => 0
            [is_active] => 1
            [last_login_dtm] => 2024-11-13 14:47:42
            [created] => 2024-11-13 11:35:20
            [updated] => 2024-11-13 14:47:42
        )

)
</pre> -->

		<?php 


		foreach ($user_list as $k => $v) { 


			 

						$can_off = 1;
						if(  $this->session->userdata('user_id')  ==  $v['id'] ){
							$can_off = 0;
						}


						if(!$is_editable){
							$can_off = 0;
						}
						



		?>
			<tr data-id="<?php echo $v['id'] ?>" class="row-data" data-login-name="<?php echo html_show($v['login_name']) ?>" data-user-name="<?php echo html_show($v['user_name']) ?>" >
				
				<td align="right">
					<?php echo $v['id'] ?>
				</td>


				<td align="left"  style="color: #0546df;">

					<?php echo html_show($v['login_name']) ?>

				</td>

				<td align="left">
					<?php echo html_show($v['user_name']) ?>
				</td>

			 


				<td align="center">
					 
				  <?php if($can_off) {?>
					<div class="form-check">
					  <input class="form-check-input" type="checkbox" value="1" id="check-isactive-id-<?php echo $v['id'] ?>"
						  
					  <?php  echo(!$can_off)?('disabled'):('')   ?>   
					  <?php  echo($v['is_active'])?('checked'):('')   ?>  
					  data-id=<?php echo $v['id'] ?> 
					  onclick="updateIsActive(this);"
					  >
					
					</div>
				 <?php } else {?>

				 	  <?php  echo($v['is_active'])?('<i class="bi bi-check-square" style="margin-left:-5px;" ></i>'):('')   ?> 

				 <?php } ?>


				</td>


				<?php if($is_editable) {?>
				<td>
					 
					<button type="button" class="btn btn-sm btn-info" onclick="openEditDataForm('<?php echo $v['id'] ?>');">แก้ไข</button>
					 
				</td>
			
				<td align="right"  >
					<?php if($can_off) {?>
					<button type="button" class="btn btn-sm btn-danger" onclick="deleteData(<?php echo $v['id'] ?>);">ลบผู้ใช้</button>
					<?php } ?>
				</td>


				<?php }//if($is_editable) ?>



		</tr>
		<?php } ?>




	</tbody>





</table>






