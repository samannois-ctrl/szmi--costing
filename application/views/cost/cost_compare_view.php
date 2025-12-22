


<table id="tbl-compare-small" class="table">

 <thead>
    <tr class="head-row-1">
    <th></th>
    <th></th>
    <th></th>
    <?php foreach ($calc_excel_col_list as $c_idx => $col) { ?>

        <th colspan="2"> <?php echo $col;?></th>

    <?php } ?>
    </tr>

    <tr class="head-row-2">
    <th>#</th>
    <th>id</th>
    <th>product_code</th>        
    <?php foreach ($calc_fld_list as $c_idx => $col) { ?>

        <th colspan="2"> <?php echo $col;?></th>

    <?php } ?>
    </tr>

    <tr class="head-row-3">
    <th></th>
    <th></th>
    <th></th>       
    <?php foreach ($calc_fld_list as $c_idx => $col) { ?>

        <th> old </th>
        <th> new </th>

    <?php } ?>
    </tr>
<thead>

<?php  
    $ord=0;
     foreach ($arr_compare as $k => $row) { ?>


    <?php if($row['row_is_diff']==1){ ?>
    <tr>
    <td> <?php echo $ord++; ?></td>
    <td> <?php echo $row['id'] ?></td>
    <td> <?php echo $row['product_code'] ?></td>
    <?php foreach ($calc_fld_list as $c_idx => $col) { 
            
            $cls = 'text-blue';
            $old_val = $row[$col]['old'];
            $new_val = $row[$col]['new'];     
            if($old_val!=$new_val){
                if(is_numeric($old_val) && is_numeric($new_val) && floatval($old_val)==floatval($new_val)){
                    
                }else{
                    $cls = 'text-red';
                }
            }


        ?>

        <td> <?php echo $row[$col]['old'] ?></td>
        <td class="<?php echo $cls;?>"> <?php echo $row[$col]['new'] ?></td>

        <?php } ?>


    </tr>

    <?php } ?>

<?php } ?>


</table>