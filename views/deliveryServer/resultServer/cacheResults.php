<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* builds Xml results with parts provided in $xml
* @package plugins.uploadresultserver
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/
/**
* builds Xml results with parts provided in $xml
* @return false if xml incomplete, otherwise it returns xml
* @param $xml an xml part
* @param $IDresult unique Id of result
* @param $seq [0->$length-1] sequence number of this part in the result $IDresult
* @param $length number of parts in the result $IDresult

*/
function getFullXml($xml, $IDresult, $seq, $length)
	{
					$backuphandler = fopen("./partialResults/smBU".$IDresult."--".$seq."--".$length."--".rand(0,2000).".xml","wb");
					fwrite($backuphandler,$xml);
					fclose($backuphandler);


		$filename = "./partialResults/".$IDresult.".xml";
		$exist = file_exists($filename);
		
		if ($exist)

		{
			
			$handle = fopen($filename,"r");
			$content= fread($handle,filesize($filename));
			fclose($handle);
			
			$content = unserialize($content);
			
			$content[$seq]=$xml;
			
			
			if (sizeOf($content)==$length) 
				
				{	
					
					$xx=serialize($content);
					
					$handle = fopen($filename,"wb");
					fwrite($handle,$xx);
					fclose($handle);
					
					
					$i=0;
					$xml="";
					while ($i<=$length-1)
						{
							$xml.=$content[$i];
							$i++;
						}
					return $xml;
				}
			else
				{
					
					$content=serialize($content);
					
					$handle = fopen($filename,"wb");
					fwrite($handle,$content);
					fclose($handle);
					
					return false;
				}


		}

		else
		{
			//if ($length==1) {return $xml;}
			// if ($length==1) { return base64_decode($xml);}
			// else
				// {	
					$content=array();
					$content[$seq]=$xml;
					$content=serialize($content);
					$handle = fopen($filename,"wb");
					fwrite($handle,$content);
					fclose($handle);
					if ($length==1) {return $xml;} //just added
					return false;
				// }
		}



	}
function isFullyOkResult($IDresult,$numberElts)
	{
		$filename = "./partialResults/".$IDresult.".xml";
		$exist = file_exists($filename);

		if (!($exist)) {return "Error;0;".$IDresult;}
		
		$handle = fopen($filename,"r");
		$content= fread($handle,filesize($filename));
		fclose($handle);
			
		$content = unserialize($content);
		if (sizeOf($content)==$numberElts) {
			// return "OK";
			return "OK;".$numberElts.";".$IDresult;
		} 
		else{	
			$missing=$numberElts-sizeOf($content);
			$i=0;
			$indexmissing="";
			while ($i<$numberElts)
			{
				if (!(isset($content[$i]))) {$indexmissing.=$i.";";} 
				$i++;
			}
			
			return "Error;1;".$missing.";".$indexmissing;}

		}

/*
$id = rand(0,200);

echo isFullyOkResult($id,5)."<BR>";

$return = getFullXml("AAAA", $id, "0", "5");
echo isFullyOkResult($id,5)."<BR>";

$return = getFullXml("CCCC", $id, "2", "5");
echo isFullyOkResult($id,5)."<BR>";
$return = getFullXml("BBBB", $id, "1", "5");
echo isFullyOkResult($id,5)."<BR>";
$return = getFullXml("FFFF", $id, "4", "5");
echo isFullyOkResult($id,5)."<BR>";
$return = getFullXml("EEEE", $id, "3", "5");
echo isFullyOkResult($id,5)."<BR>";
*/

	/*
	$return = getFullXml("BBBB", "12", "1", "2");
	echo $return;
	*/

?>