<?php

error_reporting(E_ALL);

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

class tao_helpers_Precompilator
{
    // --- ASSOCIATIONS ---

    // --- ATTRIBUTES ---
	protected $completed = array();
	
	protected $error = array();
	
	
    // --- OPERATIONS ---
	
	public function __construct(){
		$this->completed=array(
					"copiedFiles"=>array(),
					"createdFiles"=>array(),
					"replace"=>array()
					);
		$this->error=array(
					"copiedFiles"=>array(),
					"createdFiles"=>array(),
					"replace"=>array()
					);			
	}
	
	//return the name of the downloaded file or an empty string 
	public function copyFile($url,$directory,$affectedObject){
	
		$returnValue = "";
		
		$fileContent = file_get_contents($url);
		if ($fileContent === false){
			$this->error["copiedFiles"][$affectedObject]=$url;
			//throw new exception("could not open the remote file $url");
			return $returnValue;
		};
		
		//use of reverseUrl to get the last position of "/" and thus the fileName
		$reverseUrl = strrev($url);
		$reverseUrl = substr($reverseUrl,0,strpos($reverseUrl,"/"));
		$fileName = strrev($reverseUrl);
		
		$finalFilePath = $directory."/".$fileName;
		
		//check whether the file has been already downloaded: applicable for case when an item existing in several languages share the same multimedia file
		if(!in_array($url, $this->completed["copiedFiles"])){
			$handle = fopen($finalFilePath,"wb");
			$fileContent = fwrite($handle,$fileContent);
			fclose($handle);
			
			//record in the property "completed" that the file has been successfullly downloaded 
			$this->completed["copiedFiles"][$affectedObject]=$url;//serait bien de faire: $this->completed["file"][$itemUri]=$url; pour connaitre la l'item impacté (par contre, definir la langue pas prévu)
		}
				
		return $returnValue = $fileName;
	}
    
	public function itemParser($xml, $directory, $itemName, $authorizedMedia=array()){
		
		if(!file_exists($directory)){
			throw new exception("the specified directory does not exist");
		}
		
		$defaultMedia = array("jpg","jpeg","png","gif","mp3","swf");
		
		$authorizedMedia = array_merge($defaultMedia,$authorizedMedia);
		$authorizedMedia = array_unique($authorizedMedia);//eliminate duplicate
		
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
					
		$uniqueMediaList = 	array_unique($mediaList[0]);	
		foreach($uniqueMediaList as $mediaUrl){
			$mediaPath = $this->copyFile($mediaUrl, $directory, $itemName);
			$xml = str_replace($mediaUrl,$mediaPath,$xml);
		}
		return $xml;
	}
	
	public function testParser(){
	
	}
	
	public function stringToFile($content, $directory, $fileName){
		if(!is_dir($directory)){
			$created=mkdir($directory);
			if($created===false){
				die ("the folder $directory does not exist and can not be created");
			}
		}
		$handle = fopen("$directory/$fileName","wb");
		$content = fwrite($handle,$content);
		fclose($handle);
		$this->completed["createdFiles"][]=$fileName;
	}
	
	public function result(){
		$returnValue=array("completed"=>$this->completed, "error"=>$this->error);
		return $returnValue;
	}
	
} /* end of class taoDelivery_helpers_Precompilator */

?>