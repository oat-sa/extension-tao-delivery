<?php

error_reporting(E_ALL);

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

class tao_helpers_Precompilator
{
    // --- ASSOCIATIONS ---

    // --- ATTRIBUTES ---

    // --- OPERATIONS ---
	
	//return the new location of the file or an empty string 
	public function downloadFile($url,$directory,$newName=''){
	
		$returnValue = "";
		
		$fileContent = file_get_contents($url);
		if ($fileContent === false){
			throw new exception("could not open the remote file $url");
			return $returnValue;
		};
		
		//use of reverseUrl to get the last position of "/" and thus the fileName
		$reverseUrl = strrev($url);
		$reverseUrl = substr($reverseUrl,0,strpos($reverseUrl,"/"));
		$fileName = strrev($reverseUrl);
		
		//echo "Dans le xml je remplace ".$oldvalue." par ".$alendroit."<br>";
		//$xml = str_replace($oldvalue,$alendroit,$xml);
		//echo "le fichier ecrit est ".$alendroit."<br>";
		
		$finalFilePath = $directory."/".$fileName;
		$handle = fopen($finalFilePath,"wb");
		$fileContent = fwrite($handle,$fileContent);
		fclose($handle);
				
		return $returnValue = $finalFilePath;
		
	}
    
	public function parser($xml,$directory,$authorizedMedia=array()){
		
		if(!file_exists($directory)){
			throw new exception("the specified directory does not exist");
		}
		
		$defaultMedia = array("jpg","jpeg","png","gif","mp3","swf");
		
		$authorizedMedia = array_merge($defaultMedia,$authorizedMedia);
		
		$mediaList = array();
		foreach ($authorizedMedia as $mediaType){
			$expr="/http:\/\/[^<'\" ]+.".$mediaType."/i";echo $expr;
			preg_match_all($expr,$xml,$mediaList);
		}
		
		print_r($authorizedMedia);
		foreach($mediaList[0] as $mediaUrl){
			$mediaPath = $this->downloadFile($mediaUrl,$directory);echo $mediaPath."<br>";
			$xml = str_replace($mediaUrl,$mediaPath,$xml);
		}
		
		return $xml;
		
		/*
		$content = $xml;
		$occurences =array();
		$occurences2=array();
		//$occurences = split ("<[^>]*>", $content );
		//eregi(">[^<]*jpg|>[^<]*gif|>[^<]*mp3|>[^<]*swf",$content,$occurences) ;
		$expr="/http:\/\/[^<'\" ]+.jpg/i";
		preg_match_all($expr,$content,$occurences);
		$expr="/http:\/\/[^<'\"]+.mp3/i";
		preg_match_all($expr,$content,$occurences2);
		$listing = array_merge($occurences[0],$occurences2[0]);
		
		//eregi('<image src=[^<]*>',$content,$occurences) ;
		error_reporting(0);
		if (is_array($occurences))
		{
			while(list($x,$value)=each($mediaList))
			{		
					$oldvalue = $value;
					$toopen=str_replace(" ","%20",$value);
					$value=str_replace(" ","SPACE",$value);
					//echo "le fichier ouvert est ".$toopen."<br>";
					$temp2 = file_get_contents($toopen);
					
					$alenvers = strrev($value);
					$toujoursalenvers= substr($alenvers,0,strpos($alenvers,"/"));
					$alendroit = strrev($toujoursalenvers);
					//echo "Dans le xml je remplace ".$oldvalue." par ".$alendroit."<br>";
					$xml = str_replace($oldvalue,$alendroit,$xml);
					//$zipfile -> add_file($temp2, "dir/".$alendroit); 
					//echo "le fichier ecrit est ".$alendroit."<br>";
					$handle = fopen($directory."/".$alendroit,"wb");
					$temp2 = fwrite($handle,$temp2);
					fclose($handle);
					//echo "<br><br>";
			}
		}
		return$xml;
		*/
	}
	
	public function subjectCache(){
	
	}
	
} /* end of class taoDelivery_helpers_Precompilator */

?>