<?php
	
	//定义项目和对应的项目目录  一定要加后面的/
	$project = [
			'mt'=>'/Users/boli/Documents/mt/',
			'designa'=>'/Users/boli/Documents/designa/'
		];
	
	//拓展  可能会添加其它的东西
	$config = [
			'php_command'=>'php'
		];

	//命令列表 自行拓展
	$cmd = [
			['重置目录权限','chmod  -R  777 {dir}app/etc/ {dir}var/ {dir}pub/ {dir}generated/'],
			['显示插件列表','{php_command} {dir}bin/magento module:status'],
			['刷新索引','{php_command} {dir}bin/magento indexer:reindex'],
			['刷新缓存','{php_command} {dir}bin/magento cache:clean && {php_command} {dir}bin/magento cache:flush'],
			['更新模块','{php_command} {dir}bin/magento setup:upgrade'],
			['生成静态文件','{dir}bin/magento setup:static-content:deploy -f'],
			['代码编译','{dir}bin/magento setup:di:compile'],
			['切换到开发模式','{php_command} {dir}bin/magento deploy:mode:set developer'],
			['切换到生产模式','{php_command} {dir}bin/magento deploy:mode:set production'],
			['卸载模块','{php_command} {dir}bin/magento module:uninstall --clear-static-content {module}','show'],
			['禁用模块插件','{php_command} {dir}bin/magento module:disable --clear-static-content {module}','show'],
			['启用模块插件','{php_command} {dir}bin/magento module:enable --clear-static-content {module}','show'],
			['显示后台管理员url','{php_command} {dir}bin/magento info:adminuri'],
			['启用维护模式','{php_command} {dir}bin/magento maintenance:enable'],
			['禁用维护模式','{php_command} {dir}bin/magento maintenance:disable'],
			['备份文件系统和数据库','{php_command} {dir}bin/magento setup:backup --code --db'],
		];

	if( isset($_POST['project']) && $_POST['project']){

		$project_dir = $project[$_POST['project']];

		$command = str_replace('{php_command}', $config['php_command'], $cmd[$_POST['cmd']]['1']);
		$command = str_replace('{dir}', $project_dir, $command);
		if(!isset($cmd[$_POST['cmd']]['2'])){
			$file = '/Users/boli/Documents/test/m2.sh';
			file_put_contents($file, $command);
			echo '执行中...';
			while(file_exists($file)){
			}
			exit('执行完毕<a href=\'mt.php\'>返回</a>');
		}else{
			echo $command;
		}
	}


?>

<html>
	<header>
		<title>m2管理工具</title>
	</header>

	<body>
		<form method='POST'>
			
			<select name="project">
			  <?php 
			  	foreach ($project as $key => $value) {
			  		echo '<option value ="'.$key.'">'.$key.'</option>';
			  	}
			  ?>
			</select>
			<select name="cmd">
			   <?php 
			  	foreach ($cmd as $key => $value) {
			  		echo '<option value ="'.$key.'">'.$value['0'].'</option>';
			  	}
			  	?>
			</select>

			<input type="submit" value="提交" />
		</form>
	</body>
</html>






