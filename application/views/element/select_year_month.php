			
<?php

			$arr_year_list = getYearList();
			$arr_month_list = getMonthList();

			

			if(!isset($select_year)){
				$select_year = date('Y'); //2025
			}
			if(!isset($select_month)){
				$select_month = date('n'); //1-12
			}
?>


			<div class="row" style="width: 260px;" style="margin-left: 0px;">
				<div class="col-auto" style="padding-left: 0px;">

					<?php echo (isset($label_year_title))?($label_year_title):('ปี ค.ศ.') ?>
					
				</div>
				<div class="col" style="padding-left: 0px;">
					<select class="form-control form-control-sm" id="select-year" name="select-year" onchange="change_select_year_month(this);">

						<?php foreach ($arr_year_list as $k => $v) { ?>
							<option value="<?php echo $v?>" <?php echo ($v==$select_year)?("selected"):('')  ?> >  <?php echo $v;?> </option>
						<?php } ?>
						


					</select>
				</div>
				<div class="col" style="padding-left: 0px;">
					<select class="form-control form-control-sm" id="select-month" name="select-month" onchange="change_select_year_month(this);">

						<?php foreach ($arr_month_list as $k => $v) { ?>
							<option value="<?php echo $k?>" <?php echo ($k==$select_month)?("selected"):('')  ?> >  <?php echo $v;?> </option>
						<?php } ?>
						


					</select>
				</div>

			</div>