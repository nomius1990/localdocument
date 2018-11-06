<?php
	class person{
		private $name;
		public $gender;
		public function say(){
			echo $this->name.'--'.$this->gender;
		}

		public function __set($name,$value){
			echo "setting $name to $value\n";
		}

		public function __get($name){
			if(!isset($this->$name)){
				echo 'not setted';
				$this->$name = 'default setted defalut_value';
			}
		}

	}

	$student = new person();
	$student->gender = 'female';
	$student->name = 'wangmazi';
	$reflection = new ReflectionObject($student);
	// var_dump($reflection);exit;
	$class = $reflection->getName(); //获取类名
	// echo $class;exit;
	$data = $reflection->getProperties();  //获取类的属性
	foreach ($data as $key => $value) {
		// var_dump($value);exit;
		echo $value->isPublic(); //是否是public
		// echo $value->getName()."</br>";
	}

	$methods = $reflection->getMethods(); //获取类的风法
	// foreach ($methods as $key => $value) {
	// 	echo $value->getName()."</br>";
	// }

	// var_dump(get_object_vars($student)); //获取对象的属性 
	// var_dump(get_class_vars(get_class($student))); //获取类的属性
	// var_dump(get_class_methods(get_class($student)));//获取类的方法

	// echo get_class($student); //获取对象所属的类