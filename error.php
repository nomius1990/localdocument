<?php
	
	function customerError($errno,$errstr,$errfile,$errline){
		echo "<b>错误代码：<b/>{$errno} {$errstr} </br>";
		echo "错误所在行 {$errline} 错误文件 {$errfile}";
	}

	set_error_handler('customerError',E_ALL | E_STRICT);

	$a = array('o'=>2,4,6,8);
	echo $a[o];