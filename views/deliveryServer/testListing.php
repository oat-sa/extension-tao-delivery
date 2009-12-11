<?php
//TODO: ajouter les styles pour les class de balises dans le cas ou c'est read ou non, replied ou forwarded
session_start();

//recupererle type de msg box
// $url= $_POST["data"];
// $tb_url=explode("/",$url,2);
// $option=$tb_url[0];


//get the current page
if(isset($_POST["page"])) $currentPage=intval($_POST["page"]);
else $currentPage=1;

//define the number of tests per page
if (!isset($tests_per_page)) $tests_per_page=10;

//define the start and the end index of tests
$start_number = ($currentPage-1)*$tests_per_page;
$end_number = $start_number+$tests_per_page; 

//check whether an user is logged
if(!isset($_SESSION["subject"]["uri"])){
	die("no user session defined, please login again");
}

//if a subject is loggued in, get available tests:
$tests=array();
$tests[]="45645645645";
$tests[]="5645645645";
$tests[]="33645645645";
$tests[]="456907286945645";
$tests[]="45645645645";
$tests[]="45645668875";
$tests[]="45645645645";
$tests[]="45645645645";
$tests[]="5645645645";
$tests[]="33645645645";
$tests[]="456907286945645";
$tests[]="45645645645";
$tests[]="45645668875";
$tests[]="45645645645";

$total_number=count($tests);
// echo "** $total_number **";
$end_number=min($total_number-1, $end_number);//re-adjust the end_number, in case $end_number > $total_number (for the last page)
		
$totalPage=ceil($total_number/$tests_per_page);
$pager_data=array(
	"current"=>$currentPage, 
	"total"=>$totalPage,
	"start"=>$start_number,
	"end"=>$end_number
	);
		
$tests_data=array();
for($i=$start_number; $i<=$end_number; $i++){
	$tests_data[$i]["uri"]=$tests[$i];
	$tests_data[$i]["label"]="test $i";
	$tests_data[$i]["comment"]="comment $i";
}
$result=array();
$result["tests"]=$tests_data;
$result["pager"]=$pager_data;

echo json_encode($result);
// echo 'result={"tests":' . json_encode($tests_data) . ', "pager":'. json_encode($pager_data) .'}';

/*
		$pager='';
		if($nb_pages>1){
			$pager .= '<p align="center">';
			//page precedente
			if ($page > 1) {
				$url= "#".$option."/p".strval($page-1);
		    	$pager .= '<a class="nav" href="'.$url.'">prev.</a>&nbsp;&nbsp;';
		  	}
		  	// --- listing des pages
		  	$imax = min($nb_pages, 10);
		  	for ($i=1; $i<=$imax; $i++) {
				$url= "#".$option."/p".strval($i);
		    	$pager .= '<a class="nav" href="'.$url.'">['.$i.']</a>';
		  	}
		  	// --- page suivante
		  	if ($page < $nb_pages) {
		 		$url= "#".$option."/p".strval($page+1);
		    	$pager .= '&nbsp;&nbsp;<a class="nav" href="'.$url.'">next</a>';
		  	}
		  	$pager .= '</p>';
		}
		
		//initialisation du message à imprimer
		$msg_box='<table><thead>
					<tr>
						<td width="36"></td>
						<td width="100">Sender</td>
						<td width="342">Title</td>
						<td width="133">Date</td>
					</tr>
				  </thead><tbody>';
		$new_msg=0;
		$i=0;
		foreach ($tb_msg as $msg){
			$i++;
			($i%2==0)?($class="even"):($class="odd");
			//$sender=array();// il n'y a plus besoin de ça grace à la requete optimisée de msg_box2()
			//$sender=$hlp->user_info($msg['sender'],array('fields'=>'username, photoURL'));
			if($option=='inbox'){
				if($msg['read']=='0'){//utile de compter les nouveaux messages que pour l'inbox
				$new_msg ++;
				}
			}
			//creer une ligne de tableau:
			$msg_box .='
						<tr class="message '.$class.'" name="testo">
							<td colspan="4"><input id="ck'.$msg["msg_id"].'" name="chosen" type="checkbox" class="l" />
							<img src="/img/msg-reply.png" class="l"/>
							<a title="read message" name="/msg_reader.php?msg='.$msg["msg_id"].'&box='.$option.'"
							class="jqmtrigger">
							
							<div class="m1">'.$msg["username"].'</div> 
							<div class="m2">'.$msg["subject"].'</div>
							<div class="m3">'.$hlp_temp->date_GMT_to_local($msg['msg_date']).'</div>
							</a>
							</td>
						</tr>
					';	
		}
		$msg_box.="<tbody></table>";
		
		if($option=='inbox'){
			echo "<p>you have $new_msg new messages</p>";
		}
		echo $msg_box;
		echo $pager;
		echo "<br/><br/>";
	}
*/


?>