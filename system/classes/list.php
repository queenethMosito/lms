<?php
if(!defined("__LIST_PHP__"))
{
	define("__LIST_PHP__", 1);
	define("LIST_COMPARE_BUTTON_NAME", "_compare_button_");
	
	class Listings extends Base
	{
		protected $compare_count;
		protected $table;
		protected $data;
		protected $text;
		
		public function __construct($data, $table, $text, $compare_count = 3)
		{
			parent::__construct();
			$this->compare_count = $compare_count;
			$this->table = $table;
			$this->data = $data;
			$this->text = $text;
		}
		
		public function run()
		{			
			$compare = array();
			if(isset($_POST[LIST_COMPARE_BUTTON_NAME]))
			{
				$compare = $_POST['compare'];
			}
	
			$sel_list = array();
			$connection = Application::GetApplication()->getConnection();
			foreach($compare as $id)
			{
				$temp = $connection->selectSingle($this->table, $id);
				if($temp) $sel_list[] = $temp;
			}
			
			$view_data = array(
				"text" => $this->text,
				"data_list" => $connection->query("
					SELECT `{$this->table}`.*, `companies`.`name` AS `company_name`, {$this->table}.`access`
					FROM `{$this->table}`
					LEFT JOIN `companies` ON `{$this->table}`.`company` = `companies`.`id` 
					ORDER BY `companies`.`name`, `name`
				"),
				"count" => $this->compare_count
			);
			print loadView("list/select", $view_data);
			
			if(sizeof($sel_list) >= 1)
			{
				addLogEntry($this->text['module'], "COMPARE", "INFO", array("selection" => $compare), 0, 
					"User {$_SESSION['user']['fullname']} performed a compare on {$this->text['module']}'s");
				$this->showLists($sel_list);
			}
		}
		
		protected function showLists($sel_list)
		{
			global $config;
	
			$firstHeader = true;
			?>
			<div style="clear: both; float: none"></div>
			<table class="colour extra" width="100%">
			<tr>
				<th>&nbsp;</th>
				<?php foreach($sel_list as $item): ?>
				<th width="<?=(60 / sizeof($sel_list))?>%"><?=$item->name?></th>
				<?php endforeach; ?>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<?php foreach($sel_list as $item): ?>
				<td style="text-align: center" valign="top">
					<img src="<?=WEB_PATH."images/".$item->logo_path?>" height="64" />
					<?php /*<br />
					<p style="text-align: left"><?=$item->description?></p>*/ ?>
				</td>
				<?php endforeach; ?>
			</tr>
			<?php foreach($this->data as $field): ?>
			
			<?php if(sizeof($field) == 1 || sizeof($field) == 2):?>
			<tr class="heading">
				<?php
					$col_count = $firstHeader ? sizeof($sel_list) + 1 : 1; 
				?>
				<td colspan="<?=$col_count?>">
					<?=$field[0]?>
					<?php
						if(sizeof($field) == 2)
						{
							print(" ( <a onclick=\"javascript:window.open('{$config['root_web']}client/dictionary/quick/{$field[1]}/?direct=1', 'quick_dictionary', 'toolbar=0,location=0,status=0,menubar=0,width=300,height=400,directories=0,resizable=0');\">Definition</a> )");
						}
					?>
				</td>
				<?php if(!$firstHeader): ?>
					<?php foreach($sel_list as $item): ?>
					<td style="text-align: center" width="<?=(60 / sizeof($sel_list))?>%"><?=$item->name?></td>
					<?php endforeach; ?>				
				<?php else: $firstHeader = false; ?>
				<?php endif; ?>
			</tr>
			<?php else: ?>
			<tr>
				<td class="label">
					<?php 
						print($field[0]);
						if(sizeof($field) > 3)
						{
							print(" ( <a onclick=\"javascript:window.open('{$config['root_web']}client/dictionary/quick/{$field[3]}/?direct=1', 'quick_dictionary', 'toolbar=0,location=0,status=0,menubar=0,width=300,height=400,directories=0,resizable=0');\">Definition</a> )");
						}
					?>
				</td>
				<?php foreach($sel_list as $item): ?>
				<td valign="top">
					<?php
						$function = "format_{$field[2]}";
						print $this->$function($item->$field[1]);
					?>
				</td>
				<?php endforeach; ?>
			</tr>
			<?php endif; ?>
			
			<?php endforeach; ?>
			<tr class="heading">
				<td colspan="<?=sizeof($sel_list) + 1?>" style="text-align: center">
					<br />
					<input type="button" value="Quote me on these <?=$this->text['module']?>'s" onclick="quoteMe();" class="button" />
					<br />
					<br />
				</td>
			</tr>
			</table>
			<?php
				$temp = array();
				foreach($sel_list as $item) $temp[] = $item->id;
			?>
			<script type="text/javascript">
				function quoteMe()
				{
					var div = $("<div>");
					$.post("<?=$this->text['quote_path']?>", {
						"ids" : "<?=implode(",", $temp)?>",
						"ajax" : 1
					}, function(c) {
						div.html(c).dialog({
							"title" : "Quote me",
							"modal" : true,
							"resizable" : false,
							"draggable" : false,
							"close" : function() {
								div.remove();
							},
							"width" : 600,
							"buttons" : {
								"Send request" : function() {
									var number = $.trim($("#contact_number", div).val());
									var comments = $.trim($("#comments").val());
									var ids = "";
									var id_index = 0;
									$(".helper-checkbox:checked", div).each(function() {
										var id = $(this).next().val();
										if(id_index > 0) ids += ",";
										id_index++;
										ids += id;
									});
									
									// Error checking
									var errors = [];
									var error_index = 0;
									if(number == "") errors[error_index++] = "<li>Please enter your contact number</li>";
									if(id_index == 0) errors[error_index++] = "<li>Please select atleast 1 product</li>";
									if(error_index > 0)
									{
										var error_html = "<p>We could not process your quote due to the following errors:</p><ul>";
										for(var i in errors) error_html += errors[i];
										error_html += "</ul>"; 
										var error_div = $("<div>")
											.html(error_html)
											.dialog({
												"title" : "Error",
												"modal" : true,
												"close" : function() { error_div.remove(); },
												"draggable" : false,
												"resizable" : false,
												"buttons" : { "OK" : function() { error_div.dialog("close"); }}
											});
										returnl
									}
									
									// Send request
									div.html("Procssing your request...").dialog("option", "buttons", "null");
									$.post("<?=$this->text['quote_send_path']?>", {
										"ajax" : 1,
										"number" : number,
										"ids" : ids,
										"comments" : comments
									}, function(c) {
										div.html(c).dialog("option", "buttons", {
											"OK" : function() { div.dialog("close"); }
										});
									});
								},
								"Cancel" : function() {
									div.dialog("close");
								}
							}
						});
					});
				}
			</script>
			<?php
		}
		
		protected function format_website($value)
		{
			return "<a href=\"http://{$value}\" target=\"_blank\">{$value}</a>";
		}
		
		protected function format_text($value)
		{
			return trim($value);
		}
		
		protected function format_number($value)
		{
			return "<p style=\"text-align: center\">".(is_numeric($value) ? $value : "NaN")."</p>";
		}
		
		protected function format_percent($value)
		{
			return "<p style=\"text-align: center\">".(is_numeric($value) ? trim($value)." %" : "NaN")."</p>";
		}
		
		protected function format_bool($value)
		{
			global $config;
			
			$content = $value ? "<img src=\"{$config['root_web']}images/tick.jpg\" alt=\"Yes\" />" : 
				"<img src=\"{$config['root_web']}images/cross.jpg\" alt=\"No\" />";
				
			return "<center>{$content}</center>";
		}
		
		protected function format_paragraph($value)
		{
			$value = nl2br($value);
			return "<p style=\"text-align: center\">{$value}</p>";
		}
	}
}
?>