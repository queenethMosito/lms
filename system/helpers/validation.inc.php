<?php	
	class Validation_Helper {
		protected $data;
		
		public function setData($data) {
			$this->data = $data;
		}
		
		public function validateData($data = null) {
			if(!$data) {
				$data = $this->data;
			}
			
			$errors = array();
			foreach($data as $key => $element) {
				$rules = isset($element['rules']) ? explode('|', $element['rules']) : array();
				$value = isset($element['value']) ? $element['value'] : '';
				$label = isset($element['label']) ? $element['label'] : $key;
				$labelNice = strtoupper(substr($label, 0, 1)).substr($label, 1);
				
				foreach($rules as $rule) {
					$rule = trim($rule);
					$param = '';
					if(!$rule) {
						continue;	
					}
					if(strstr($rule, '-') !== false) {
						list($rule, $param) = explode('-', $rule, 2);	
					}
					$rule = strtolower($rule);
					switch($rule) {
						case 'required':
							if($param != 'select' && $value == '0') break; 
							if(!trim($value)) $errors[] = "{$labelNice} is required";
							break;
						case "email":
							if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $value)) break;
							if($value != "0" && !trim($value)) break;
							$errors[] = "{$labelNice} appears to be invalid";
							break;
						case "match":
							$other = $data[$param];
							$value2 = isset($other['value']) ? $other['value'] : "";
							$otherLabel = isset($other['label']) ? $other['label'] : $param;
							if($value2 != $value) $errors[] = "{$labelNice} does not match {$otherLabel}";
							break;
						case "int":
							$result = preg_match('/^\d*$/', $value) == 1;
							if(trim($value) && !$result) $errors[] = "{$labelNice} must only contain digits";
							break;
						case "gt":
							$result = preg_match('/^\d*$/', $value) == 1;
							$number = (int) $value;
							if(strlen($value) > 0 && $result && $number <= (int) $param) $errors[] = "{$labelNice} must be greater than 0";
							break;
						case "len":
							$len = (int) $param;
							$result = preg_match('/^\d*$/', $value) == 1;
							$number = (int) $value;
							$s = $len == 1 ? "" : "s";
							if(strlen($value) > 0 && $result && strlen(trim($value)) != $len) $errors[] = "{$labelNice} must be exactly {$len} digit{$s}";
							break;
						case "start":
							$check = substr($value, 0, strlen($param));
							if(trim($value) && $check != $param) $errors[] = "{$labelNice} must start with {$param}";
							break;
					}
				}
			}
			return $errors;
		}
	}
