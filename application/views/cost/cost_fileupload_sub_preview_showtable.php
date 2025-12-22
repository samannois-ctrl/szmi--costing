<div style="width: 100%;height:10px;"></div>
<div class="table-responsive " style="overflow: auto;max-height: calc(100vh - 300px);">
                                <table class="tbl-show-excel " >
                                        <thead class="thead-light" style="position: sticky; top: 0; z-index: 1;">
                                            <tr style="background-color: #e1e1e1;">
                                                <th></th>
                                                <?php 
                                                    $ord=1;
                                                    foreach($def_col as $col_data) { ?>
                                                    <th><?php echo $col_data['excel_col'] ?></th>
                                                <?php } ?>
                                            </tr>
                                            <tr style="background-color: #eaf3ff;">
                                                <th>ลำดับ</th>
                                                <?php foreach($def_col as $col_data) { ?>
                                                    <th><?php echo $col_data['excel_header'] ?></th>
                                                <?php } ?>
                                            </tr>                                        
                                        </thead>
                                        <tbody>
                                        <?php   foreach($each_table as $row) {?>
                                            <tr>
                                                <td><?php echo $ord++; ?></td>
                                                <?php foreach($row as $col_value) { 
                                                    
                                   
                                                ?>
                                                    <td <?php  echo ($col_value['type']=='number')?(' class="text-right" '):('');  ?> ><?php echo html_show($col_value['display_val']); ?></td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                </table>
                                </div>