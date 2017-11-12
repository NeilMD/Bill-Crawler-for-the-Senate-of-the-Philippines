<?php
	
	ini_set('display_errors', 1); error_reporting(E_ALL);  
	require 'db.php';

	$base = 'http://senate.gov.ph/lis/leg_sys.aspx?congress=17&type=bill&p=';
	$break = false;
	$senator = array();
	for($ctrx = 1;$ctrx < 250; $ctrx++){
		$bills = array();
		
		//PHP 
		$doc = new DOMDocument();
		$sctr = $ctrx.'';
		//GET. 
		echo "<h5>PAGE :".$sctr."</h5> <br/>";
		$page = file_get_contents($base.$sctr);
		echo $base.$sctr."<br/>";
		// MAKE A DOMDOCUMENT Class
		$doc->loadHTML($page);
		$bCtr = 0;
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
						$congressNumber = clean($td->childNodes->item(0)->wholeText);
						

						$congressNumber = str_replace("\r\n            ", "", $congressNumber);
						$bill->congressNumber = $congressNumber;
						echo $congressNumber."<br/>";
						// Senate Bill Number
						$billNumber = ($td->childNodes->item(2)->wholeText);
						$number = str_replace("Senate Bill No. ", "", $billNumber);
					
						$bill->billNumber = $billNumber;

						echo $billNumber."<br/>";
						// Bill Title
						$billTitle = ($td->childNodes->item(3)->childNodes->item(0)->childNodes->item(0)->wholeText);
						$bill->billTitle = $billTitle;
						// echo ($td->childNodes->item(3)->childNodes->item(0)->textContent);
						echo $billTitle."<br/>";
						// Filed By and Date
						$filed = ($td->childNodes->item(4)->wholeText);
						echo $filed."<br/>";
							// FIXED DATE FORMAT
							$filed = str_replace("Filed on ", "", $filed);
							$dateAndNames = explode(" by ", $filed);

							//DATE FILED
							$date = $dateAndNames[0];	
							$date = date('Y-m-d',strtotime($date));
							$bill->dateFiled = $date;	

							$names = $dateAndNames[1];
							$namesArr = explode(",", $names);
							$senatorBill = array();					
							var_dump($namesArr);
							// //INSERT TO DB PER NAME
							for($ctrz = 0 ; $ctrz < sizeof($namesArr); $ctrz+=2){
								$completeName = rtrim(ltrim($namesArr[$ctrz+1])).' '.rtrim(ltrim($namesArr[$ctrz]));			

								array_push($senatorBill, $completeName);

							}
							$bill->senatorProposed = $senatorBill;



						//SCOPE
						$scope = ($td->childNodes->item(10)->textContent);
						$bill->scope = $scope;			
						echo $scope."<br/>";	
						//Status
						$status = ($td->childNodes->item(12)->textContent);
						$bill->status = $status;			
						echo $status."<br/><hr/>";			
						//INSERT TO DB
						// insert($billNumber, $billTitle, $filed, $db);

					}

				}


				// TAGS, SCOPE, COMMITTEE
				$inputs = $doc->getElementsByTagName("input");
				$data = array('__EVENTTARGET' => 'lbCtte', '__EVENTARGUMENT' => '',
							  '__VIEWSTATE'=>'','__VIEWSTATEGENERATOR'=>'','__EVENTVALIDATION'=>'');

				for($ctr2 = 0; $ctr2 < $inputs->length; $ctr2++){
					$input = $inputs->item($ctr2)->getAttribute("value");
				
					if($ctr2 == 0){
						$data["__VIEWSTATE"] = $input;

					}
					if($ctr2 == 1){
						$data["__VIEWSTATEGENERATOR"] = $input;	
					}		
					if($ctr2 == 2){
						
						$data["__EVENTVALIDATION"] = $input;	
					}					
				}
				// $article = "http://senate.gov.ph/lis/".$i->getAttribute("href");
				$options = array(
				    'http' => array(
				        'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
										"Cookie: ASPSESSIONIDSASDSQBB=EBCIPCKDODPDDFFKPKNPIDBJ\r\n",
				        'method'  => 'POST',
				        'content' => http_build_query($data),
				        
				    ),
				 
				);
				$context  = stream_context_create($options);
				// echo $article;
				$result = file_get_contents($article, false, $context);

				
				
				if ($result === FALSE) {
					echo "DID NOT GET FILE";
				}else{
					$doc2 = new DOMDOCUMENT();
					$doc2->loadHTML($result);
					$tsc = $doc2->getElementsByTagName("td");

					for($ctr = 0; $ctr < $tsc->length; $ctr++){
						$td2 = $tsc->item($ctr);	

						if($td2->getAttribute("id") == 'content'){
							$td2 = clean($td2);
							$sub = ($td2->childNodes->item(7)->childNodes);
							echo "<br>";
							echo "Subjects:";
							//GET SUBJECTS
							$subjects = array();
							foreach($sub as $t){
								
								if($t instanceof DOMTEXT){
									array_push($subjects, $t->wholeText);
									echo $t->wholeText;
									echo "<br>";
								}
							}
							$bill->subjects = $subjects;			
							// array_push($bill->subjects,$subjects);			

							echo "Primary Committee:";
							//GET PRIMARY COMMITTEE
							$pCommittee = ($td2->childNodes->item(9)->childNodes);
							$pComm = array();
							foreach($pCommittee as $t){
								
								if($t instanceof DOMTEXT){
									array_push($pComm, $t->wholeText);
									echo $t->wholeText;
									echo "<br>";
								}
							}

							$bill->primaryCommittee = $pComm;			
							

							echo "Secondary Committee:";
							$sComm = array();
							// var_dump($td2->childNodes->item(10));
							//GET SECONDARY COMMITTEE
							if($td2->childNodes->item(10)->textContent == 'Secondary committee'){
								$sCommittee = ($td2->childNodes->item(11)->childNodes);
								 $subjects = array();
								foreach($sCommittee as $t){
									
									if($t instanceof DOMTEXT){
										array_push($sComm, $t->wholeText);
										echo $t->wholeText;
										echo "<br>";
									}
								}

							}
							echo "<hr/>";
							$bill->secondaryCommittee = $sComm;			
							
							
							
						}
					}
				}
				
				array_push($bills, $bill);
				// var_dump($bills);
				$senateInfo = json_encode($bill, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
				$senateFolder = "/srv/http/PHP/SenateJSON/{$bCtr}-{$bill->billNumber}_".$ctrx.'.json';		

				$lfile3 = fopen($senateFolder,'w+');
			    fwrite($lfile3, $senateInfo);
			    fclose($lfile3);

			}	

			//CHECK FOR NEXT PAGE
			else if(strpos($i->getAttribute("href"),"leg_sys.aspx?congress=17&type=bill&p") !== false){
				
				if(trim($i->childNodes->item(0)->wholeText) == "Next"){
					// echo "PUMASOK";
					$break = false;
				}
			}						
			$bCtr++;
		}
		// var_dump($bills);
		// SAVE BILL OBJECT

		// $senateInfo = json_encode($bills, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		

		// SAVE LAST PAGE

		$lastfileName = 'lastPageSenate.txt';
		$linfo = $ctrx;
		$lfile = '/srv/http/PHP/'.$lastfileName;		

		$lfile2 = fopen($lfile,'w+');
	    fwrite($lfile2, $linfo);
	    fclose($lfile2);
		



	}

	$db->close();
?>