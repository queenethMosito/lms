<style>
.divider{
	min-width:1px !important;
	margin:0px;
	padding:0px;
}
</style>
<?php
	require 'menu-setup.inc.php';
	
	
	
?>

	<?php				
		$first = true;
		foreach($menu as $item) {
			
			$attr = array();
			if(isset($item['href']) && $item['href']) $attr[] = "href=\"{$item['href']}\"";
			if(isset($item['target']) && $item['target']) $attr[] = "target=\"{$item['target']}\"";
			if(isset($item['onclick']) && $item['onclick']) $attr[] = "onclick=\"{$item['onclick']}\"";
			if(isset($item['title']) && $item['title']) $attr[] = "title=\"{$item['title']}\"";
			?>
			<?php if(!empty($item['children'])):?>
			<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle <?=$item['selected'] ? "active" : "";?>" data-toggle="dropdown" <?=sizeof($attr) ? " ".implode(" ", $attr) : ""?>><?=$item['label']?></a>
    <div class="dropdown-menu">
    <?php foreach($item['children'] as $key=>$value):?>
    <a class="dropdown-item" href="<?=$value['href']?>"><?=$value['label']?></a>
    <?php endforeach;?>
      
      
    </div>
  </li>
          <?php else:?>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger <?=$item['selected'] ? "active" : "";?>" <?=sizeof($attr) ? " ".implode(" ", $attr) : ""?>><?=$item['label']?></a>
          </li>
			<?php endif;?>
			<?php
		}
	?>

