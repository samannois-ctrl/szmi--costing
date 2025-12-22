<?php 
   
// print('<pre>'.print_r(Import_model::$def_sheet_structor,true).'</pre>');exit;


?>


<div class="pl-3 pt-1 pb-2 text-center">
  
<?php if(isset(Import_model::$def_sheet_info['is_all_valid_sheet']) && (Import_model::$def_sheet_info['is_all_valid_sheet']==1)) {?>
<span class="">*** กรุณาตรวจสอบก่อนกดปุ่มยืนยันบันทึกข้อมูล ***</span>
<button class="btn btn-primary" id="btn-confirm-save" onclick="confirm_save_upload_sheet()"> <i class="bi bi-floppy2-fill"></i>
   ยืนยันบันทึกข้อมูล  </button>
<?php }else{?>

  <span class="text-danger">*** ข้อมูลในไฟล์นี้ยังไม่ถูกต้องตรงกับโครงสร้างที่กำหนดไว้ โปรดตรวจสอบ ***</span>
<?php }?>


<span class="confirm_send_upload_status"></span>
</div>


<div class="card card-success card-tabs tab-all-sheets">
              <div class="card-header p-0 pt-1" style="background-color: #3b7749;">
                <ul class="nav nav-tabs tab-condense" id="preview-upload-tab-head" role="tablist">

                <?php 
                $count_tab=0;
                foreach(Import_model::$def_sheet_structor as $sheet_name => $sheet_data) {
                    $count_tab++; 
                ?>
                  
                  <li class="nav-item">
                    <a class="nav-link <?php echo ($count_tab==1)?('active'):('');$count_tab++;?> text-center" id="<?php echo 'sheet_preview'.$count_tab; ?>-tab" data-toggle="pill" href="#<?php echo 'sheet_preview'.$count_tab; ?>" role="tab" aria-controls="<?php echo 'sheet_preview'.$count_tab; ?>" aria-selected="true">
                        
                    <!-- [is_found_in_excel] => 1
                    [real_sheet_name_on_excel] => MASTER    
                     -->
                    <?php if($sheet_data['is_found_in_excel']==1) { 
                        echo $sheet_data['real_sheet_name_on_excel'];
                    }else{
                        echo $sheet_data['excel_sheet_name'];
                    }?>

                    <!-- display error -->
                    <?php if($sheet_data['is_found_in_excel']==0){
                    
                      echo '<span class="badge badge-pill" style="background:#ffffff;color:#ff0000;">ไม่พบใน Excel</span>';
                    
                    }else if(isset($sheet_data['import_result']['is_valid_sheet']) && $sheet_data['import_result']['is_valid_sheet']==0){

                      echo '<span class="badge badge-pill" style="background:#ffffff;color:#ff0000;">อ่านข้อมูลไม่ได้</span>';
                        
                    } ?>
                    
                    
                
                
                
                
                </a>
                  </li>

                <?php } ?>
                
                </ul>
              </div>
              <div class="card-body pl-0 pr-0 pt-1 pb-1">
                <div class="tab-content" id="preview-upload-tab-content">
                
                
                <?php $count_tab=0;
                
                foreach(Import_model::$def_sheet_structor as $sheet_name => $sheet_data) {
                    
                    $count_tab++;?>

                  <div class="tab-pane fade show <?php echo ($count_tab==1)?('active'):('');$count_tab++;?>" id="<?php echo 'sheet_preview'.$count_tab; ?>" role="tabpanel" aria-labelledby="<?php echo 'sheet_preview'.$count_tab; ?>-tab">
                    
                    <?php if($sheet_data['is_found_in_excel']==1) { ?>
                        <?php if($sheet_data['import_result']['is_valid_sheet']==1) { ?>


                        <!-- TAB TABLE PANEL START-->
                        <nav>
                          <ul class="nav nav-tabs tab-condense" id="nav-<?php echo $sheet_name;?>-tab" role="tablist">

                            <?php 
                                $count_tbl_tab=0;
                                foreach($sheet_data['import_result']['tables'] as $tbl_name => $each_table) { 
                                  $count_tbl_tab++;
                                  $table_of_sheet_id = $sheet_name."_".$tbl_name;
                            ?>
                               
                                <?php  
                                         $suffix_table_msg = "";
                                        if($each_table['is_valid_table']==0) { 
                                          $suffix_table_msg = "ตารางไม่ถูกต้อง - ".$each_table['msg'];
                                        }
                                ?>




                                <li class="nav-item">      
                                  <a class="nav-link tab-sub-table <?php echo($count_tbl_tab==1)?('active'):('');?>" id="tbl-<?php echo $table_of_sheet_id;?>-tab" data-toggle="tab" data-target="#tbl-<?php echo $table_of_sheet_id;?>-content" type="button" role="tab" aria-controls="<?php echo $table_of_sheet_id;?>" aria-selected="true"><?php echo ($tbl_name=='main')?('ตาราง'):('ตาราง '.$each_table['table_title']);?> <?php echo $suffix_table_msg;?> </a>
                                </li>
                                  <?php } ?>
                            
                          </ul>
                        </nav>
                        <div class="tab-content" id="nav-<?php echo $sheet_name;?>-tabContent">


                        <?php 
                              $count_tbl_tab=0;
                            foreach($sheet_data['import_result']['tables'] as $tbl_name => $each_table) { 
                              $count_tbl_tab++;
                              $table_of_sheet_id = $sheet_name."_".$tbl_name;
                            ?>
                               
                          <div class="tab-pane fade   <?php echo($count_tbl_tab==1)?('show active'):('');?>" id="tbl-<?php echo $table_of_sheet_id;?>-content" role="tabpanel" aria-labelledby="tbl-<?php echo $table_of_sheet_id;?>-tab">
                            
                                <?php  if($each_table['is_valid_table']==1) {   

                                  $sub_table_data = []; 
                                    $sub_table_data['def_col'] = $sheet_data['def_tables'][$tbl_name]['def_col'];
                                    $sub_table_data['each_table'] = $this->Cost_model->refineRawContentToFormatDisplay($sub_table_data['def_col'],$each_table);

                                    // print_r($sub_table_data['each_table']);exit;
                                    

                                    $this->load->view('cost/cost_fileupload_sub_preview_showtable',$sub_table_data)

                                  ?>
                                    
                                
                                
                                <?php } ?>


                          </div>
                        <?php } ?>

                        </div>
                        <!-- TAB TABLE PANEL END-->


                            

                        <?php }else{ ?>
                            
                            <span class="text-danger"><?php echo $sheet_data['import_result']['msg']?></span>


                            <?php //print_r_html($sheet_data['import_result']); ?>
                            
                        <?php } ?>
                    <?php }else{

                        echo 'ไม่พบ Sheet ใน Excel ที่อัพโหลดมา';
                    } ?>





                  </div>

                <?php } ?>
             
                </div>
              </div>
              <!-- /.card -->
  </div> <!-- tab-all-sheets -->


  <script>
    // $('.tab-all-sheets').ready(function(){

      $('.waiting_render_table').hide();
      console.log('waiting_render_table');
    // })
  </script>