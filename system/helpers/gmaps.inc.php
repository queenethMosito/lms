<?php
if(!defined("HELPER_GOOGLE_APPS_API_HELPER"))
{
	define("HELPER_GOOGLE_APPS_API_HELPER", 1);
	
	class GMaps_Helper
	{
		private $index = 0;
		
		const OFFSET = 268435456;
		const RADIUS = 85445659.4471; /* $offset / pi() */
		
		const MAP_TYPE_SATELLITE = "google.maps.MapTypeId.SATELLITE";
		const MAP_TYPE_ROADMAP = "google.maps.MapTypeId.ROADMAP ";
		const MAP_TYPE_HYBRID = "google.maps.MapTypeId.HYBRID";
		const MAP_TYPE_TERRAIN  = "google.maps.MapTypeId.TERRAIN";
		
		public function CalculateDistance($lat1, $lon1, $lat2, $lon2)
		{
		    $latd = deg2rad($lat2 - $lat1);
		    $lond = deg2rad($lon2 - $lon1);
		    $a = sin($latd / 2) * sin($latd / 2) +
		         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
		         sin($lond / 2) * sin($lond / 2);
		         $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		    return 6371.0 * $c;
		}
		
		private function _LonToX($lon) {
		    return round(self::OFFSET + self::RADIUS * $lon * pi() / 180);        
		}
		
		private function _LatToY($lat) {
		    return round(self::OFFSET - self::RADIUS * 
                log((1 + sin($lat * pi() / 180)) / 
                (1 - sin($lat * pi() / 180))) / 2);
		}
		
		public function CalculatePixelDistance($lat1, $lon1, $lat2, $lon2, $zoom) {
		    $x1 = $this->_LonToX($lon1);
		    $y1 = $this->_LatToY($lat1);
		
		    $x2 = $this->_LonToX($lon2);
		    $y2 = $this->_LatToY($lat2);
		        
		    return sqrt(pow(($x1-$x2),2) + pow(($y1-$y2),2)) >> (21 - $zoom);
		}
		
		public function CalculateZoom($lat1, $lon1, $lat2, $lon2, $pixelDiameter)
		{
		    $x1 = $this->_LonToX($lon1);
		    $y1 = $this->_LatToY($lat1);
		
		    $x2 = $this->_LonToX($lon2);
		    $y2 = $this->_LatToY($lat2);
		    
		    $distanceReal = sqrt(pow(($x1-$x2),2) + pow(($y1-$y2),2));
		    $divisor = $distanceReal / $pixelDiameter;
		    $x = log($divisor, 2);
		    
		    return round(21 - $x);
		}
		
		public function RenderJSInclude($apiKey, $sensor = false)
		{
			$sensor = $sensor ? "true" : "false";
			return "
				<script type=\"text/javascript\" src=\"http://maps.googleapis.com/maps/api/js?key={$apiKey}&sensor={$sensor}\"></script>
			";
		}
		
		public function RenderJSMap($elementID, $lat, $lon, $zoom, $mapType = self::MAP_TYPE_ROADMAP)
		{
			return "
				var gmapCentre = new google.maps.LatLng({$lat}, {$lon});
				var gmapOptions = {
				  zoom: {$zoom},
				  center: gmapCentre,
				  mapTypeId: {$mapType}
				};
				var gmap = new google.maps.Map(document.getElementById(\"{$elementID}\"), gmapOptions);
			";
		}
		
		public function RenderJSMarker($lat, $lon, $title, $colour)
		{
			$index = $this->index++;
			return "
		        var pinImage_{$index} = new google.maps.MarkerImage(\"http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|\" + \"{$colour}\",
		            new google.maps.Size(21, 34),
		            new google.maps.Point(0,0),
		            new google.maps.Point(10, 34));
		        var pinShadow_{$index} = new google.maps.MarkerImage(\"http://chart.apis.google.com/chart?chst=d_map_pin_shadow\",
		            new google.maps.Size(40, 37),
		            new google.maps.Point(0, 0),
		            new google.maps.Point(12, 35));
	        
				var marker_{$index} = new google.maps.Marker({
				      position: new google.maps.LatLng({$lat}, {$lon}),
				      map: gmap,
				      title:\"Location of event\",
		                icon: pinImage_{$index},
		                shadow: pinShadow_{$index}
		         });
			";
		}
	}
}