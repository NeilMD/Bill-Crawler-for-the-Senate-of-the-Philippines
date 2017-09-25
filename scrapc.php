<?php
	require 'db.php';

	$base = 'http://senate.gov.ph/lis/leg_sys.aspx?congress=17&type=bill&p=';
	$break = false;
	for($ctrx = 1;$ctrx < 3 && !$break; $ctrx++){
		
		//PHP 
		$doc = new DOMDocument();
		$sctr = $ctrx.'';
		//GET. 
		echo "<h5>PAGE :".$sctr."</h5> <br/>";
		$page = file_get_contents($base.$sctr);
		echo $base.$sctr."<br/>";
		// MAKE A DOMDOCUMENT Class
		$doc->loadHTML($page);
		
		$list = $doc->getElementsByTagName("a");
		$break = true;
		foreach ($list as $i) {

			
			if(strpos($i->getAttribute("href"),'bill_re') !== false){
				echo "URL: ".$i->getAttribute("href").'<br/>';				
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
						// Congress Number
						$congressNumber = ($td->childNodes->item(0)->wholeText);
						echo $congressNumber."<br/>";
						// Senate Bill Number
						$billNumber = ($td->childNodes->item(2)->wholeText);
						echo $billNumber."<br/>";
						// Bill Title
						$billTitle = ($td->childNodes->item(3)->childNodes->item(0)->childNodes->item(0)->wholeText);
						// echo ($td->childNodes->item(3)->childNodes->item(0)->textContent);
						echo $billTitle."<br/>";
						// Filed By and Date
						$filed = ($td->childNodes->item(4)->wholeText);
						echo $filed."<br/><hr/>";				
						//INSERT TO DB
						insert($billNumber, $billTitle, $filed, $db);

					}

				}

				
			}

			//CHECK FOR NEXT PAGE
			else if(strpos($i->getAttribute("href"),"leg_sys.aspx?congress=17&type=bill&p") !== false){
				
				if(trim($i->childNodes->item(0)->wholeText) == "Next"){
					// echo "PUMASOK";
					$break = false;
				}
			}						

		}
		



	}

	$db->close();
?>