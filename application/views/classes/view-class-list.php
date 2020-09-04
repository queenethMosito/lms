
<?php foreach($classes as $centre): ?>
			<li id="li_<?=$centre->class_id?>" data-starget="<?=$centre->class_description?>" data-json='<?=json_encode($centre)?>' onclick="preview(this)" class="list-group-item">
				<?=$centre->class_name?>
			</li>
		<?php endforeach; ?>