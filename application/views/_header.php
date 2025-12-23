<!DOCTYPE html>
<html lang="th">
    <head>
        <meta charset="utf-8" >
        <title>ระบบคำนวณต้นทุน SCMI </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" >
        <meta content="Cost allocation system" name="description" >

        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo asset_url('assets/images/favicon/favicon.ico')?>">

     






        <link rel="stylesheet" href="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/dist/css/adminlte.min.css')?>">
        <link rel="stylesheet" href="<?php echo asset_url('assets/js/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')?>">



        <link rel="stylesheet" href="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css')?>">
        <link rel="stylesheet" href="<?php echo asset_url('assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css')?>">


  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')?>">
  <!-- Toastr -->
  <link rel="stylesheet" href="<?php echo asset_url('assets/templates/AdminLTE-3.2.0/plugins/toastr/toastr.min.css')?>">


  <!-- jsGrid -->

  <link rel="stylesheet" href="<?php echo asset_url('assets/js/jsgrid-1.5.3/dist/jsgrid.css')?>">
  <link rel="stylesheet" href="<?php echo asset_url('assets/js/jsgrid-1.5.3/dist/jsgrid-theme.css')?>">

  <!-- Datatable -->
  <link rel="stylesheet" href="<?php echo asset_url('assets/js/Datatable-2.1.8/datatables.css')?>">




 <!-- JS multi-select -->
  <link rel="stylesheet" href="<?php echo asset_url('assets/js/multiple-select-1.7.0/dist/multiple-select.min.css')?>" >
  
  <!-- Custom Glass UI Theme -->
  <link rel="stylesheet" href="<?php echo asset_url('assets/css/argon-custom.css')?>" >
  <link rel="stylesheet" href="<?php echo asset_url('assets/css/dashboard-glass.css')?>" >

        <style>
            
            body {
                /* margin: 0; */
                font-family: "tahoma", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
                font-size: 16px; /* 11pt; */
                font-weight: 400;
                line-height: 1.5;
                /* color: #212529; */
                /* text-align: left; */
            }

            /* Chrome, Safari, Edge, Opera */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
            }

            /* Firefox */
            input[type=number] {
            -moz-appearance: textfield;
            }
    .bg-web-main{
        background: #0533b5;
        color: #ffffff;
    }.bg-excel{
        background: #34a853;
        color: #ffffff;
    }




    .text-web-main{
        color: #003cab;
    }

    .text-web-secondary{
        color: #014cc1;
    }

    .main-submenu-container{
        /* border-bottom: 1px solid #717171; */
        /* margin-bottom: 10px; */
    }
    .main-submenu{
        border-bottom: 1px solid #dee2e6;
        padding: 5px 20px 3px 20px;
        margin-bottom: 10px;
    }

    .main-submenu.active{
         border-bottom: 2px solid #003cab;
         /* padding-left: 0px;
         padding-right: 0px; */
         font-weight: bold;
     }
 
     .main-submenu a{
        color: #888888;
    }  
    
    .main-submenu a:hover{
        color: #003cab;
    } 
    .main-submenu.active a{
        color: #003cab;
    }


    .tr_top_border td{
        border-top: 1px solid #d8d8d8;
    }

    .waitloader-overlay{

        position: fixed;
        left: 0px;
        top: 0px;
        right: 0px;
        bottom: 0px;
        z-index: 20000;
        background: #0000008c;
        /*padding-top: 25%;*/
        text-align: center;

    }


    .waitloader-container {
        
        position: absolute;
        left: 50%;
        top: 40%;

    }
    .waitloader-text {
        
        color:#fff; 
        margin:0px auto;
        margin-top: 20px;

    }

    .waitloader-spinner {
       
       height:60px;
       width:60px;
       margin:0px auto;
       -webkit-animation: rotation .6s infinite linear;
       -moz-animation: rotation .6s infinite linear;
       -o-animation: rotation .6s infinite linear;
       animation: rotation .6s infinite linear;
       border-left:6px solid rgba(0,174,239,.15);
       border-right:6px solid rgba(0,174,239,.15);
       border-bottom:6px solid rgba(0,174,239,.15);
       border-top:6px solid rgba(0,174,239,.8);
       border-radius:100%;
    }

    @-webkit-keyframes rotation {
       from {-webkit-transform: rotate(0deg);}
       to {-webkit-transform: rotate(359deg);}
    }
    @-moz-keyframes rotation {
       from {-moz-transform: rotate(0deg);}
       to {-moz-transform: rotate(359deg);}
    }
    @-o-keyframes rotation {
       from {-o-transform: rotate(0deg);}
       to {-o-transform: rotate(359deg);}
    }
    @keyframes rotation {
       from {transform: rotate(0deg);}
       to {transform: rotate(359deg);}
    }


    .showDataLoader-spinner {
       
       height:40px;
       width:40px;
       margin:0px auto;
       -webkit-animation: rotation .6s infinite linear;
       -moz-animation: rotation .6s infinite linear;
       -o-animation: rotation .6s infinite linear;
       animation: rotation .6s infinite linear;
       border-left:4px solid rgba(0,174,239,.15);
       border-right:4px solid rgba(0,174,239,.15);
       border-bottom:4px solid rgba(0,174,239,.15);
       border-top:4px solid rgba(0,174,239,.8);
       border-radius:100%;
    }



    .link-top{
        z-index: 1000;
        position: absolute;
        top: 0;
        left: 0;

    }





    .input-error-warn {

        border: 1px solid #ff0000;
    }



    .has-error {

        border: 1px solid #ff0000;
    }

    .pre-wrap{

        white-space: pre-wrap;
        
    }

  
 
    .text-loading:after {
      overflow: hidden;
      display: inline-block;
      vertical-align: bottom;
      -webkit-animation: ellipsis steps(4,end) 900ms infinite;      
      animation: ellipsis steps(4,end) 900ms infinite;
      content: "\2026"; /* ascii code for the ellipsis character */
      width: 0px;
    }

  

    @-webkit-keyframes ellipsis {
      to {
        width: 1.25em;    
      }
    }

      @keyframes ellipsis {
          to {
            width: 1.25em;    
          }
        }



    .tbl-form-user td{

        padding-top: 10px;
        padding-bottom: 10px;

    }


    .tbl-form-vac td{

        padding-top: 10px;
        padding-bottom: 10px;

    }

    .excel-btn-dt{
/*        display: none;*/
        background: #5225da;
    }


    .btn-custom-add-pos{

        background: #13b39d;
        color:#ffffff;
    }

    .btn-custom-add-pos:hover{

        background: #0d8b79;
        color:#ffffff;  
        
    }
    

    .has_error{
        border: 1px solid red;
    }





    .just-updated{
     
      animation: transcolorupdate 2s 1;
    }


    .item-row-in-appoint-wait td{
        background: #eeffeb;
    }

    .item-row-in-dup td{
        background: #55ffcc;
    }
   

    @keyframes transcolorupdate {
      /*from {background-color: #77f7e4;}
      to */{background-color: transparent;}
      from {color: #a7a7a7;}
      to {color: unset;}
    }



    .tbl-gen-data{
        table-layout: fixed;
        border-collapse: collapse;
        border-spacing: 0;


    }

    .tbl-gen-data td, .tbl-gen-data th{
        font-size: 0.85em;
        padding-left: 3px;
        padding-right: 3px;
        padding-top: 5px;
        padding-bottom: 5px;
        border: 1px solid #dee2e6;
    }

    .tbl-gen-data th{

        text-align: center;
    }

    #tbl-gen-main{
        table-layout: fixed;
        border-collapse: collapse;
        border-spacing: 0;
        position: relative;;


    }

    #tbl-gen-main td, #tbl-gen-main th{
        font-size: 0.85em;
        padding-left: 3px;
        padding-right: 3px;
        padding-top: 5px;
        padding-bottom: 5px;
        
        border: 1px solid #cfcfcf;
    }

    #tbl-gen-main th{

        position: sticky;
        top:0;

    }

    #tbl-gen-small{
        
        border-collapse: collapse;
        border-spacing: 0;
          
    }

    #tbl-gen-small td, #tbl-gen-small th{
        font-size: 0.9em;
        padding-left: 10px;
        padding-right: 10px;
        padding-top: 10px;
        padding-bottom: 10px;
        
        border: 1px solid #cfcfcf;
    }

    #tbl-gen-small th{

        background-color: #f0f5ff;
        text-align: center;
        

    }



.tbl-show-excel{
	
	border-collapse: collapse;
	border-spacing: 0;
	position: relative;;


}

.tbl-show-excel td, .tbl-show-excel th{
	font-size: 0.8em;
	padding-left: 3px;
	padding-right: 3px;
	padding-top: 5px;
	padding-bottom: 5px;
	
	border: 1px solid #cfcfcf;
}

.tbl-show-excel td {

    /* white-space: nowrap;
    overflow-x: auto; */
    
}

.tbl-show-excel th{

	position: sticky;
    text-align: center;
	top:0;

}


.nav-link.tab-sub-table.active{
    color:#383838;
    border-top: 1px solid #098704;
}

a.nav-link.tab-sub-table{
    padding: 5px 10px;
 color:   #3b7749
}



    .menu-header-first-line{
        font-size: 1.2rem;
        font-weight: bold;
        color: #4f4f4f;
    }

    .minor-title-content1{
        font-size: 1rem;
        font-weight: bold;
        color: #05748d;
        /* border-left: 10px solid #ed2227; */
        padding-left: 10px;
        /* margin-top: 20px; */
        margin-bottom: 10px;
    }
    .minor-title-content2{
        font-size: 1rem;
        font-weight: bold;
        color: #0C2CBDFF;
        /* border-left: 10px solid #ed2227; */
        padding-left: 10px;
       /* margin-top: 20px;*/
        margin-bottom: 10px;
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


    .nav-tabs.tab-condense .nav-item .nav-link {
    
        padding: .2rem 0.5rem;
        font-size: 0.9rem;
    }

    /* short-class-name */
    .al-r{
        text-align: right;
    }

    .al-l{
        text-align: left;
    }
    .al-c{
        text-align: center;
    }



/*-----ag-table-custom start */
.ag-head-row1{

    background-color: #f5f5f5;
    color: #383838;


}

.ag-head-row2{


    background-color: #def0fb;
    color: #383838;
    
}
.ag-header-cell-comp-wrapper {
    justify-content: center;
}
.ag-header-cell-label {
  justify-content: center; /* Centers the content horizontally */
}

.cell-num-ag-table{
    text-align: right;
}
.manual-lock-cell{
    background-color:#ffb2001c !important;
    color: #721c24 !important;
    /* border:1px solid   #098704 ; */
}
.edit-lock-float{
    border: none;
    background: transparent;
    color: transparent;
    padding: 0px;
    position: absolute;
    z-index: 500;
}
.custom-edit-manual-lock-wrapper:hover .edit-lock-float.is-lock {
    color: orange;
}

.custom-edit-manual-lock-wrapper:hover .edit-lock-float.is-unlock {
    color: #bdbdbd;
}
.cell-display-value{
    margin-left: 22px;
}


/* .manual-lock-cell::before {
    content: '\F47B';  
 
    color: orange;
    display: inline-block;
    font-family: bootstrap-icons !important;
    font-style: normal;
    font-weight: 400 !important;

} */

/* .ag-theme-balham .ag-header {
     
}
.ag-theme-balham .ag-header-group-cell {
    background-color: #003cab;
    color:black;
}
.ag-theme-balham .ag-header-cell {
    background-color:rgb(66, 241, 13);
    color:black;
} */
/*-----ag-table-custom end */




    #tbl-compare-small{
        
        border-collapse: collapse;
        border-spacing: 0;
          
    }

    #tbl-compare-small td, #tbl-compare-small th{
        font-size: 0.7em;
        padding-left: 2px;
        padding-right: 2px;
        padding-top: 0px;
        padding-bottom: 0px;
        
        border: 1px solid #cfcfcf;
    }

    #tbl-compare-small th{

        background-color: #f0f5ff;
        text-align: center;
        position: sticky;
        top:0;
        

    }
/* First header row */
#tbl-compare-small thead tr.head-row-1 th {
  position: sticky;
  top: 0;
  z-index: 3; /* Ensure it stays above other headers */
  
  height: 20px;
}

/* Second header row */
#tbl-compare-small thead tr.head-row-2 th {
  position: sticky;
  top: 20px; /* Adjust based on the height of the first row */
  z-index: 3;
  
  height: 20px;
}

/* Third header row */
#tbl-compare-small thead tr.head-row-3 th {
  position: sticky;
  top: 40px; /* Adjust based on the height of the first two rows */
  z-index: 3;
  
  height: 20px;
}

/* Mini Loading icon ========================================*/
.loading-spinner {
    width: 15px;
    height: 15px;
    border: 3px solid #ccc;
    border-top-color: #06e4b4;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
/* Mini Loading icon ========================================*/


.div-sheet-head-wrapper{
    padding-top: 6px;
    border-top-right-radius: 5px;
    border-top-left-radius: 5px;
}
.div-sheet-item{
    border: none;

    padding: 0px 2px;
    margin-right: 10px;
    background-color: transparent;
    color: #ffffff;
    cursor: pointer;
    user-select: none;
    font-size: 0.9rem;
}

.div-sheet-item.active .btn-sheet-link {

    color: #000000;
    background-color: #ffffff;

}

.div-sheet-item .btn-sheet-link{
    border: none;
    border-top-right-radius: 5px;
    border-top-left-radius: 5px;
    padding: 5px 5px;
    color: #ffffff;
}


        </style>


    </head>

<body class="layout-top-nav">
        




    <div class="wrapper">


<?php

    $part_menu = strtolower(uri_string());

    // echo $part_menu;
    // print_r($_SESSION);exit;

?>
        

        <nav class="main-header navbar navbar-expand  bg-web-main navbar-dark" style="">

            <ul class="navbar-nav">


 

                <div href="#" style="padding: 10px 2px 2px 2px">
                <img src=<?php echo(base_url("/assets/images/logo-small.png"))?> alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
               
                </div>


                <?php echo $menu_item_html;?>

 
 
            
            
            

                
            </ul>


            <ul class="navbar-nav ml-auto">


                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle  " href="#" id="menu-profile" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fas fa-user-circle"></i>&nbsp;&nbsp;<span class="nav-bar-user-name"><?php echo $this->session->userdata('user_name'); ?></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="menu-profile">
                      <a class="dropdown-item "  href="<?php echo base_url('user/editmyprofile') ?>">แก้ไขข้อมูลผู้ใช้</a>
                      
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('login/out') ?>" title="ออกจากระบบ">
                      <i class="fas fa-sign-out-alt"></i>
                      
                    </a>
                   
                </li>




            </ul>




        </nav>



<div class="waitloader-overlay" style="display: none;">
    <div class="waitloader-container">
        <div class="waitloader-spinner"></div>
        <div class="waitloader-text"></div>
    </div>

</div>

 