<?php

/*
* xml files may contain several results this splits the xml base don the tao:TEST nodes found in the xml
*/
function splitXML($xml)
{
	
	// $hdl = fopen("log.xml","wb");
	// fwrite($hdl,$xml);
	// fclose($hdl);
	$header = substr($xml,0,strpos($xml,"<tao:TEST "));
	$content=array();
	
	//echo strpos($xml,"<tao:TEST ");
	while (strpos($xml,"<tao:TEST "))
	{
		
		$begin =stristr($xml,"<tao:TEST ");
		$begin = $header.substr($begin,0,strpos($begin,"</tao:TEST>")+11)."</tao:Result>
</rdf:RDF>";
		
		$contents[]= $begin;
		$xml = stristr($xml,"<tao:TEST ");
		$xml = substr($xml,10);

	}
	
	return $contents;
}
/*
$hdl = fopen("Received April 4, 2005, 10 15 am1112602511.xml","r");
$xml = fread($hdl,500000);
fclose($hdl);
print_r(splitXML($xml));
*/
?>