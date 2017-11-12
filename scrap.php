<?php
	
	require 'hquery.php';
	// hQuery::$cache_path = "~/Hquery";

	$base = 'http://senate.gov.ph/lis/leg_sys.aspx?congress=17&type=bill&p=';
	
	for ($i=0; $i < 1; $i++) { 
		$temp = $i + 1;
		$billPages = hQuery::fromUrl($base . $temp);
		$ctr = 0;
		foreach ($billPages->find("div.alight p") as $element) {
			//HREF
			$site =  $element->children()->attr("href",true);
			echo $site. "<br/>";
			
			$ctr++;

			$getSite = hQuery::fromUrl($site);
			

			// echo $billItself->html();
		}
	}

	
	

?>