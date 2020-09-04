<?php
	if(!defined("__PROVIDER_ENGINE_PHP__"))
	{
		define("__PROVIDER_ENGINE_PHP__", 1);
		
		class SearchResult
		{
			public $content1;
			public $content2;
			public $date;
			public $relevence;
			public $group;
			public $link;
		}
		
		abstract class Provider
		{
			abstract function performSearch($data, $connection);
		}
		
		class NewsProvider extends Provider
		{
			/**
			 * @param mixed $data
			 * @param MySQL $connection
			 */
			function performSearch($data, $connection)
			{
				global $config;
				$data = array();
				$sql = "
					SELECT * FROM `news` ORDER BY `date`
				";
				$results = $connection->query($sql);
				foreach($results->rows as $row)
				{
					$r = new SearchResult();
					$r->content1 = "????";
					$r->content2 = $row->title;
					$r->date = $row->date;
					$r->relevence = 100;
					$r->group = "News";
					$r->link = "{$config['root_web']}client/news/?&amp;={$row->id}";
					$data[] = $r;
				}
				return $data;
			}
		}
		
		class SearchEngine
		{
			protected $providers;
			protected $connection;
			
			public function __construct($connection)
			{
				$this->providers = array();
				$this->connection = $connection;
			}
			
			public function addProvider($provider)
			{
				$this->providers[] = $provider;
			}
			
			public function performSearch($data)
			{
				$results = array();
				foreach($this->providers as $provider)
					$results += $provider->performSearch($data, $this->connection);
				return $results;
			}
		}
	}
?>