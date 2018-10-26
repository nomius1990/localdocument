<?php
	while(1){
		$file = '/Users/boli/Documents/test/m2.sh';
		if(file_exists($file)){
			$command = trim(file_get_contents($file));
			echo "正在执行命令".$command."\n";
			passthru($command);
			passthru("rm -f {$file}");
			echo "执行完毕\n";
		}
	}