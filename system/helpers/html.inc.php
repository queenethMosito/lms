<?php
	class Html_Helper {
		public function formStart($method = 'POST', $name = '', $action = '', $enctype = '') {
			$html = "<form method=\"{$method}\"";
			if(trim($name)) $html .= " name=\"{$name}\"";
			if(trim($enctype)) $html .= " enctype=\"{$enctype}\"";
			if(trim($action)) $html .= " action=\"{$action}\"";
			$html .= '>';

			return $html;
		}

		public function formEnd()
		{
			return "</form>";
		}

		public function label($label, $for) {
			return "<label for=\"{$for}\">{$label}</label>";
		}

		public function input($id, $value = '', $class = '', $attributes = array(), $style = array()) {
			$html = "<input id=\"{$id}\" name=\"{$id}\"";
			$html .= " value=\"".htmlspecialchars($value)."\"";
			if($class) $html .= " class=\"{$class}\"";

			foreach($attributes as $key => $attr) $html .= " {$key}=\"{$attr}\"";
			if(sizeof($style))
			{
				$styleHtml = array();
				foreach($style as $key => $val) $styleHtml[] = "{$key}:{$val}";
				$styleHtml = implode(";", $styleHtml);
				$html .= " style=\"{$styleHtml}\"";
			}
			$html .= " />";

			return $html;
		}

		public function Hidden($id, $value = "")
		{
			$html = "<input id=\"{$id}\" name=\"{$id}\" type=\"hidden\"";
			$html .= " value=\"".htmlspecialchars($value)."\"";
			$html .= " />";

			return $html;
		}

		public function RequiredSpan()
		{
			return "<span class=\"required\">*</span>";
		}

		public function select($id, $list, $current = null) {
			$current = htmlspecialchars($current);
			$html = "<select name=\"{$id}\" id=\"{$id}\">";
			foreach($list as $key => $value) {
				$selected = $key == $current ? ' selected="selected"' : '';
				$html .= "<option value=\"{$key}\"{$selected}>{$value}</option>";
			}
			$html .= "</select>";

			return $html;
		}

		public function Checkbox($id, $checked = false, $value = null)
		{
			$html = "<input type=\"checkbox\" id=\"{$id}\" name=\"{$id}\"";
			if($checked) $html .= " checked=\"checked\"";
			if($value) $html .= " value=\"{$value}\"";
			$html .= " />";
			return $html;
		}
		public function selectTitle($id, $list,$title, $current = null) {
			$current = htmlspecialchars($current);
			$html = "<select name=\"{$id}\" id=\"{$id}\">";
			foreach($list as $key => $value) {
				if(is_object($value)) {
					$html .= '<optgroup label="'.$value->name.'">';
					foreach($value->items as $key2 => $value2) {
						$selected = $key2 == $current ? ' selected="selected"' : '';
						$html .= "<option value=\"{$key2}\"{$selected}>{$value2}</option>";
					}
					$html .= '</optgroup>';
				}
				else {
					$selected = $key == $current ? ' selected="selected"' : '';
					$html .= "<option value=\"{$key}\"{$selected}>{$value}</option>";
				}
			}
			$html .= "</select>";

			return $html;
		}
	}
