<?php

error_reporting(E_ALL);

/**
 * The precompilator helper provides methods for the delivery compilation action
 * such as file copy, error management or file parser
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage helpers
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The precompilator helper provides methods for the delivery compilation action
 * such as file copy, error management or file parser
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage helpers
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class tao_helpers_Precompilator
{
    // --- ASSOCIATIONS ---

    // --- ATTRIBUTES ---
	/**
     * The attribute "completed" contains the array of completed actions performed during the delivery compilation
	 * (e.g. file copy, file or folder creation) 
     *
     * @access protected
     * @var array
     */
	protected $completed = array();
	
	/**
     * The attribute "failed" contains the array of failed actions performed during the delivery compilation
	 * (e.g. file copy, file or folder creation) 
     *
     * @access protected
     * @var array
     */
	protected $failed = array();
	
	/**
     * The attribute "pluginPath" define the directory where all required runtime plugins are stored
     *
     * @access protected
     * @var string
     */
	protected $pluginPath = "";
	
	/**
     * The attribute "compiledPath" define the directory where all compiled files for the test will be stored
     *
     * @access public
     * @var string
     */
	public $compiledPath= "";
	
	/**
     * The attribute "testUri" define the uri of the test that is being compiled
     *
     * @access protected
     * @var string
     */
	protected $testUri = "";
	
    // --- OPERATIONS ---
	
	/**
     * The method __construct intiates the Precompilator class by setting the initial values to the attributes 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param  string testUri
	 * @param  string compiledPath
	 * @param  string pluginPath
     * @return mixed
     */	
	public function __construct($testUri, $compiledPath, $pluginPath){
	
		$this->completed=array(
					"copiedFiles"=>array(),
					"createdFiles"=>array()
					);
					
		$this->failed=array(
					"copiedFiles"=>array(),
					"createdFiles"=>array()
					);
		
		//create a directory where all files related to this test(i.e media files and item xml files) will be copied:
		$testId=self::getUniqueId($testUri);//get the an unique id for the test to be compiled
		$directory="$compiledPath$testId/";		
		if(!is_dir($compiledPath)){
			$this->failed["createdFiles"]["compiled_test_folder"]=$directory;
			throw new Exception("The main compiled test directory '$compiledPath' does not exist");
		}else{
			if(!is_dir($directory)){
				$created=mkdir($directory);
				if($created===false){
					$this->failed["createdFiles"]["compiled_test_folder"]=$directory;
					throw new Exception("The compiled test directory '$directory' does not exist and can not be created");
				}else{
					$this->completed["createdFiles"][]=$directory;
				}
			}
		}
		
		if(!is_dir($pluginPath)){
			throw new Exception("The plugin directory $pluginPath does not exist");
		}
		
		$this->compiledPath = $directory;
		$this->pluginPath = $pluginPath;
	}
	
	/**
     * The method copyFile enable a precompilator instance to copy a file
	 * Depending on the success or the failure of the operation, it records the result either in the class attribute "completed" or "failed"
     * If the copy succeeds, it returns the name and the extension of the copied file, with the format "name.extension". 
     * It returns an empty string otherwise.
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param  string url
	 * @param  string directory
	 * @param  string affectedObject
     * @return string
     */		
	public function copyFile($url, $directory, $affectedObject){
	
		$returnValue = "";
		
		$fileContent = @file_get_contents($url);
		if ($fileContent === false){
			$this->failed["copiedFiles"][$affectedObject][]=$url;
			return $returnValue;
		};
		
		//use of reverseUrl to get the last position of "/" and thus the fileName
		$reverseUrl = strrev($url);
		$reverseUrl = substr($reverseUrl,0,strpos($reverseUrl,"/"));
		$fileName = strrev($reverseUrl);
		
		$finalFilePath = $directory."/".$fileName;
		
		//check whether the file has been already downloaded: applicable for case when an item existing in several languages share the same multimedia file
		$isDownloaded=false;
		foreach ($this->completed["copiedFiles"] as $copiedFiles){
			//Check if it has not been copied yet
			if(in_array($url, $copiedFiles)) {
				$isDownloaded=true;
				break;
			}
		}
		if($isDownloaded===false){
			$handle = fopen($finalFilePath,"wb");
			$fileContent = fwrite($handle,$fileContent);
			fclose($handle);
			
			//record in the property "completed" that the file has been successfullly downloaded 
			$this->completed["copiedFiles"][$affectedObject][]=$url;
		}
				
		return $returnValue = $fileName;
	}
    
	/**
     * The method copyFile firstly defines the runtime files to be included in each compiled test folder
	 * Then it calls the copyFile method to accomplish its task
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return void
     */
	public function copyPlugins(){
		$affectedObject='';
		$plugins=array(
			'bar.swf',
			'CLLPlugin.swf',
			'countdown.swf',
			'ctest_item.swf',
			'kohs_passation.swf',
			'listen.swf',
			'tao_item.swf',
			'taotab.swf',
			'Test.swf',
			'upload_result.swf',
			'start.html',
			'theTest.php',
			'uploadItem.xml'
			);
		
		$jsFiles=array(
			'elements.js',
			'init.js',
			'jquery.js',
			'swfobject.js'
			);
			
		foreach($plugins as $plugin){
			$this->copyFile($this->pluginPath.$plugin, $this->compiledPath, 'delivery_runtime');
		}
		
		if(!is_dir($this->compiledPath."js/")){
			mkdir($this->compiledPath."js/");
		}	
		foreach($jsFiles as $jsFile){
			$this->copyFile($this->pluginPath."js/".$jsFile, $this->compiledPath."js/", 'delivery_runtime/js');
		}

		$cssFiles=array(
			'test_layout.css'
			);
		if(!is_dir($this->compiledPath."css/")){
			mkdir($this->compiledPath."css/");
		}	
		foreach($cssFiles as $cssFile){
			$this->copyFile($this->pluginPath."css/".$cssFile, $this->compiledPath."css/", 'delivery_runtime/css');
		}
	}
	
	/**
     * The method itemParser parses the ItemContent xml file and executes fileCOpy with media to be downloaded.
	 * It also replaces the old link to the media file with the new ones in the ItemContent XML file and returns it as a string.
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param  string xml
	 * @param  string directory
	 * @param  string itemName
	 * @param  array authorizedMedia
     * @return string
     */	
	public function itemParser($xml, $directory, $itemName, $authorizedMedia=array()){
		
		if(!file_exists($directory)){
			throw new Exception("the specified directory does not exist");
		}
		
		$defaultMedia = array("jpg","jpeg","png","gif","mp3","swf");
		
		$authorizedMedia = array_merge($defaultMedia,$authorizedMedia);
		$authorizedMedia = array_unique($authorizedMedia);//eliminate duplicate
		
		$mediaList = array();
		$exprArray = array();
		foreach ($authorizedMedia as $mediaType){
			$mediaListTemp=array();
			$expr="/http:\/\/[^<'\" ]+.".$mediaType."/i";//TODO: could be optimized by only searching tags that could contain media.
			preg_match_all($expr,$xml,$mediaListTemp);
			$mediaList = array_merge($mediaList,$mediaListTemp);
		}
					
		$uniqueMediaList = 	array_unique($mediaList[0]);	
		foreach($uniqueMediaList as $mediaUrl){
			$mediaPath = $this->copyFile($mediaUrl, $directory, $itemName);
			$xml = str_replace($mediaUrl,$mediaPath,$xml);
		}
		return $xml;
	}
	
	/**
	 * The method stringToFile is used to write the required test and item XML files in the local disk.
	 * It also manages errors and exceptions of the operation by recording the result in the class attributes "completed" or "failed"
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param  string content
	 * @param  string directory
	 * @param  string fileName
     * @return void
     */	
	public function stringToFile($content, $directory, $fileName){
		if(!is_dir($directory)){
			$created=mkdir($directory);
			if($created===false){
				$this->failed["createdFiles"][$directory]=$fileName;
				throw new Exception("The folder $directory does not exist and can not be created");
			}
		}
		$handle = fopen("$directory/$fileName","wb");
		$content = fwrite($handle,$content);
		fclose($handle);
		$this->completed["createdFiles"][]=$fileName;
	}
	
	/**
	 * The method result returns the protected attributes "completed" and "failed" 
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return array
     */	
	public function result(){
		$returnValue=array("completed"=>$this->completed, "failed"=>$this->failed);
		return $returnValue;
	}
	
	/**
	 * The method getUniqueId provide an unique id for the ressource, which is a substring of the resource uri
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param  string uriRessource
     * @return string
     */	
	public static function getUniqueId($uriRessource){
		$returnValue='';
		//TODO check format of the uri, preg_match()
		
		$returnValue=substr($uriRessource,stripos($uriRessource,".rdf#")+5);
		
		return $returnValue;
	}
	
} /* end of class taoDelivery_helpers_Precompilator */

?>