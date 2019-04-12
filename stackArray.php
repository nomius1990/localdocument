<?php 
	
	class stackArray{

		protected $data;

		public function __construct(){
			$this->data = [];
		}

		public function push($value){
			array_push($this->data, $value);
		}

		public function get(){
			return array_pop($this->data);
		}

		public function getlen(){
			return count($this->data);
		}
	}


	class compute{

		protected $numbers;

		protected $symbols;


		public function __construct($str){
			$this->numbers = new stackArray();
			$this->symbols = new stackArray();
			$this->store($str);
		}

		private function store($str){

			$num = '';
			$newNum = false;
			$guard = 'e';
			$str=$guard.$str;
			// $str=$str.$guard;
			$len = strlen($str);
			$i = $len-1;
			while($i>=0){
				$s = $str[$i--];
				if(in_array($s,['+','-','*','/',$guard])){
					if($s != $guard) $this->symbols->push($s);
					$this->numbers->push(strrev($num));
					$num = '';
				}else{
					$num .= $s;
				}
			}
		}

		public function compute(){

			while($this->symbols->getlen() >=2){

				$firstSymbol = $this->symbols->get();
				$secondSymbol = $this->symbols->get();

				$num1 = $this->numbers->get();
				$num2 = $this->numbers->get();
				$num3 = $this->numbers->get();
				print_r("$num1 $firstSymbol $num2 $secondSymbol $num3 \n");
				if(in_array($firstSymbol, ['*','/'])){
					$this->symbols->push($secondSymbol);
					$this->numbers->push($num3);
					$this->computeSingle($firstSymbol,$num1,$num2);
				}elseif(in_array($secondSymbol, ['*','/'])){
					$this->computeSingle($secondSymbol,$num2,$num3);
					$this->symbols->push($firstSymbol);
					$this->numbers->push($num1);
				}else{
					$this->numbers->push($num3);
					$this->symbols->push($secondSymbol);
					$this->computeSingle($firstSymbol,$num1,$num2);
				}
			}

			$firstNum = $this->numbers->get();
			$secondNum = $this->numbers->get();
			$this->computeSingle($this->symbols->get(),$firstNum,$secondNum);
		}

		private function computeSingle($symbol,$num1,$num2){

			switch ($symbol) {
				case '+':
					$this->numbers->push($num1 + $num2);
					break;
				case '-':
					$this->numbers->push($num1 - $num2);
					break;
				case '*':
					$this->numbers->push($num1 * $num2);
					break;
				case '/':
					$this->numbers->push($num1 / $num2);
					break;
				default:
					# code...
					break;
			}

			return;
		}

		public function printData(){

			while($str = $this->symbols->get()){
				echo '('.$str.')';
			}

			echo "\n";
			while($str = $this->numbers->get()){
				echo '('.$str.')';
			}
		}

	}
	

$compute = new compute('21/7*4/2+8*9+8/4*2+9');
$compute->compute();
$compute->printData();
