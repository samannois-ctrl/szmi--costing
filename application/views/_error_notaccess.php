<?php

$header_data = [];
$header_data['menu_item_html'] = $this->Menu_model->get_html_menu_header('');  
		$this->load->view('_header',$header_data);
?>


<div style="width:100;text-align:center;" class="p-5">

<p><h4> <i class="bi bi-ban text-danger" ></i> ไม่มีสิทธิ์เข้าถึงหน้านี้ </h4></p><br><br><br>


<p> <a href="<?php  echo base_url();?>"> &gt;&gt; กลับไปหน้าแรก &lt;&lt; </a> </p>

</div>

<?php

		$this->load->view('_script_header');

		$this->load->view('_footer');


?>