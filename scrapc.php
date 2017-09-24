<?php
	//Linux. Dont Matter
	// ini_set('allow_url_fopen', 1); 
// ini_set("implicit_flush", "0");
	// BASE URL
	$base = 'http://senate.gov.ph/lis/leg_sys.aspx?congress=17&type=bill&p=';
	
	//PHP 
	$doc = new DOMDocument();
	//GET. 
	$page = file_get_contents($base);

	// MAKE A DOMDOCUMENT Class
	$doc->loadHTML($page);
	
	$list = $doc->getElementsByTagName("a");

	foreach ($list as $i) {
		if(strpos($i->getAttribute("href"),'bill_re') !== false){
			echo $i->getAttribute("href").'<br/>';

			$article = "http://senate.gov.ph/lis/".$i->getAttribute("href");

			//REPEAT
			$doc = new DOMDOCUMENT();
				
			$page = file_get_contents($article);

			$doc->loadHTML($page);

		

			$tds = $doc->getElementsByTagName("td");
			
			
			
			for($ctr = 0; $ctr < $tds->length; $ctr++){
				$td = $tds->item($ctr);
				

				if($td->getAttribute("id") == 'content'){
					$td = clean($td);
					echo($td->childNodes->length);
					echo "<br/>";

					echo ($td->childNodes->item(0)->wholeText)."<br/>";
					echo ($td->childNodes->item(2)->wholeText)."<br/>";
					echo ($td->childNodes->item(3)->childNodes->item(0)->childNodes->item(0)->wholeText)."<br/>";
					echo ($td->childNodes->item(4)->wholeText)."<br/> <hr/>";


					
					// echo $td->nodeValue."<br/>";
					
					

					
					
					
				
					// print_r($td->childNodes->item(3)->nodeValue)."<br/>";
					// echo $divs->item($ctr+1)->nodeValue;
				}

			}

			// break;
		}
		

	}

	function clean($nodes){
		$new = $nodes;

		for($ctr = 0; $ctr < $nodes->childNodes->length; $ctr++){
			$node = $nodes->childNodes->item($ctr);
			
			if($node->nodeType == 8 || ($node->nodeType == 3 && trim($node->wholeText) == '' ) ){
				// print_r($node);
				$new->removeChild($node);
			}
		}
	 
		return $new;

	}



?>