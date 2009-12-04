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
		$exprArray = array();
		foreach ($authorizedMedia as $mediaType){
			$mediaListTemp=array();
			$expr="/http:\/\/[^<'\" ]+.".$mediaType."/i";//TODO: to be optimized by only searching tags that could contain media.
			preg_match_all($expr,$xml,$mediaListTemp);
			$mediaList = array_merge($mediaList,$mediaListTemp);
			//$exprArray[]=$expr;//for debug
		}
		// print_r($exprArray);
		// print_r($mediaList);
		foreach($mediaList[0] as $mediaUrl){
			$mediaPath = $this->downloadFile($mediaUrl,$directory);
			$xml = str_replace($mediaUrl,$mediaPath,$xml);
		}
		return $xml;
	}
	
	
	
} /* end of class taoDelivery_helpers_Precompilator */

?>