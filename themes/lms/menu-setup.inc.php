<?php
	$connection = Application::GetApplication()->GetConnection();
	$page = strtolower(trim($_GET['route']));
	if(substr($page, -1) != '/') $page .= '/';
	
	$menu = array();
	if(!isset($_SESSION['user']) && !isset($_SESSION['user']['id'])) {
	 }
	elseif(isset($_SESSION['user']) && isset($_SESSION['user']['id']))
	{
	   
	    $menu['classes'] = array(
	        'label' => 'Manage Classes',
	        'title' => 'Classes',
	        'href' => '/client/classes',
	        'selected' => substr($page, 0, 24) == '/' ? true : false,
	        
	    );
	    $menu['students'] = array(
	        'label' => 'Manage Learners',
	        'title' => 'Learners',
	        'href' => '/client/attendees',
	        'selected' => substr($page, 0, 24) == '/' ? true : false,
	        
	    );
	}
	

	
		
	
	
		
