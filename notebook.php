<?php
	
	class message{
		
		public $name;
		public $email;
		public $content;

		public function __set($name,$value){
			$this->$name = $value;
		}

		public function __get($name){
			if(!isset($this->$name)){
				$this->$name = NULL;
			}
		}
	}

	/**
	留言本模型,负责福安里留言本
	bookpath 留言本属性
	*/
	class gBookModel{
	
		private $bookPath; //文件
		private $data; //数据
	
		public function setBookPath($bookPath){
			$this->bookPath = $bookPath;
		}

		public function getBookPath(){
			return $this->bookPath;
		}

		public function open(){}

		public function close(){}

		public function read(){
			return file_get_contents($this->bookPath);
		}

		public static function safe($data){
			$reflect = new ReflectionObject($data);
			$pro = $reflect->getProperties();
			$messageBox = new stdClass();
			foreach ($pro as  $value) {
				$ivar = $value->getName();
				$messageBox->$ivar = trim($value->getValue($data));
			}
			return $messageBox;
		}


		public function write($data){
			$this->data = self::safe($data)->name.'&'.self::safe($data)->email."&".self::safe($data)->content;
			file_put_contents($this->bookPath, $this->data);
		}

		public function delete(){
			file_put_contents($this->bookPath, 'no content');
		}
	}

	/**
	写日志之类的
	*/
	class leaveModel{
		public function write(gBookModel $gb,$data){
			$book = $gb->getBookPath();
			$gb->write($data);
		}
	}

	class authorControll{
		
		public function message(leaveModel $l,gBookModel $g,message $data){
			$l->write($g,$data);
		}

		public function view(gBookModel $g){
			return $g->read();
		}
		public function delete(gBookModel $g){
			$g->delete();
			echo $this->view($g);
		}
	}

	$message = new message();
	$message->name = 'phper';
	$message->email = 'nomius@126.com';
	$message->content = '这是一个神奇的世界';

	$gb = new authorControll(); //策划
	$pen = new leaveModel(); //拿出笔  写东西的
	$book = new gBookModel(); //翻出笔记本  相当于是载体
	$book->setBookPath('a.txt');
	$gb->message($pen,$book,$message);
	echo $gb->view($book);
	$gb->delete($book);
	// echo 2223;

