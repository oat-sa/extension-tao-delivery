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
class taoDelivery_helpers_Compilator
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
	protected $pluginPath = '';
	
	/**
     * The attribute "compiledPath" define the directory where all compiled files for the test will be stored
     *
     * @access public
     * @var string
     */
	protected $compiledPath = '';
	
	/**
     * The attribute "testUri" define the uri of the test that is being compiled
     *
     * @access protected
     * @var string
     */
	protected $testUri = "";
	
	/**
     * The attribute "deliveryUri" define the uri of the delivery that is being compiled
     *
     * @access protected
     * @var string
     */
	protected $deliveryUri = "";
	
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
	public function __construct($deliveryUri, $testUri, $itemUri, $compiledPath='', $pluginPath=''){
		
		//TODO: change testUri to deliveryUri
		
		$this->completed = array(
			"copiedFiles"=>array(),
			"createdFiles"=>array()
		);
					
		$this->failed = array(
			"copiedFiles"=>array(),
			"createdFiles"=>array(),
			"untranslatedItems"=>array(),
			"errorMsg"=>array()
		);
		
		
		
		if(!empty($pluginPath)){
			$this->pluginPath = $pluginPath;
		}else{
			$this->pluginPath = BASE_PATH."/lib/";
		}
		if(!is_dir($this->pluginPath)){
			throw new Exception("The plugin directory '{$this->pluginPath}' does not exist");
		}
		
		if(!empty($compiledPath)){
			$this->compiledPath = $compiledPath;
		}else{
			$this->compiledPath = BASE_PATH."/compiled/";
		}
		if(!is_writable($this->compiledPath)){
			throw new Exception("The compiled directory '{$this->compiledPath}' is not writable");
		}
			
		if(!is_dir($this->compiledPath)){
			$this->failed["createdFiles"]["compiled_test_folder"] = $directory;
			throw new Exception("The main compiled test directory '{$this->compiledPath}' does not exist");
		}
	}
	
	/**
     * Returns the compilation path of the compilator
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return string
     */	
	public function getCompiledPath(){
		return $this->compiledPath;
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
	public function copyFile($url, $directory="", $affectedObject="", $rename=false){
	
		$returnValue = "";
		
		if (empty($directory)){
			$directory=$this->compiledPath;
		}
		
		if (empty($affectedObject)){
			$affectedObject="undefinedObject";
		}
		
		$fileName = basename($url);
			
		//check whether the file has been already downloaded: e.g. in the case an item existing in several languages share the same multimedia file
		$isCopied=false;
		foreach ($this->completed["copiedFiles"] as $copiedFiles){
			//Check if it has not been copied yet
			if(in_array($url, $copiedFiles)) {
				$isCopied=true;
				return $fileName;
			}
		}
		
		if($isCopied === false){
			
			//since the file has not been downloaded yet, start downloading it usin cUrl
			
			if(preg_match("/^http/", $url)){
				
				$curlHandler = curl_init();
				curl_setopt($curlHandler, CURLOPT_URL, $url);
				
				//if there is an http auth on the local domain, it's mandatory to auth with curl
				if(USE_HTTP_AUTH){	
					$addAuth = false;
					$domains = array('localhost', '127.0.0.1', ROOT_URL);
					foreach($domains as $domain){
						if(preg_match("/".preg_quote($domain, '/')."/", $url)){
							$addAuth = true;
						}
					}
					if($addAuth){
						curl_setopt($curlHandler, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                                                curl_setopt($curlHandler, CURLOPT_USERPWD, USE_HTTP_USER.":".USE_HTTP_PASS);
					}
				}
				curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
				
				$fileContent = curl_exec($curlHandler);
				/* TO DEBUG
				 if ($fileContent === false){
					$error = curl_error($curlHandler);
					curl_close($curlHandler);  
					throw new Exception($error);
				}
				*/
				curl_close($curlHandler);  
			}
			else{
			
				$fileContent = @file_get_contents($url);
			}
			
			if ($fileContent === false){
				
				$this->failed["copiedFiles"][$affectedObject][]=$url;
				return $returnValue;
			};
						
			//check file name compatibility: 
			//e.g. if a file with a common name (e.g. car.jpg, house.png, sound.mp3) already exists in the destination folder
			while(file_exists($directory.$fileName) && $rename===true){
				$reverseFileName = strrev($fileName); 
				$reverseExt = substr($reverseFileName, 0, strpos($reverseFileName,"."));
				$reverseName = substr($reverseFileName, strpos($reverseFileName,".")+1);
				
				//add an underscore so it becomes unique
				$fileName = strrev($reverseName)."_.".strrev($reverseExt);
			}
			
			if(file_put_contents($directory.$fileName, $fileContent) === false){
				throw new Exception("the file $directory.$fileName cannot be written");
			}
			
			//record in the property "completed" that the file has been successfullly downloaded 
			$this->completed["copiedFiles"][$affectedObject][]=$url;
			
			$returnValue = $fileName;
		}
				
		return $returnValue;
	}
    
	public function getPlugins($type = ''){
		if(empty($type)){
			return array_merge(
				$this->getPlugins('JS'),
				$this->getPlugins('CSS'),
				$this->getPlugins('IMG'),
				$this->getPlugins('SWF')
			);
		}
		else{
			if(strtoupper($type) == 'JS'){
				$files = array();
				foreach(scandir($this->pluginPath."js/") as $file){
					if(is_file($this->pluginPath."js/".$file)){
						if(preg_match("/\.js$/", $file)){
							$files[] = $file;
						} 
					}
				}
				return $files;
			}
			if(strtoupper($type) == 'CSS'){
				$files = array();
				foreach(scandir($this->pluginPath."css/") as $file){
					if(is_file($this->pluginPath."css/".$file)){
						if(preg_match("/\.css$/", $file)){
							$files[] = $file;
						} 
					}
				}
				return $files;
			}
			if(strtoupper($type) == 'IMG'){
				$files = array();
				foreach(scandir($this->pluginPath."img/") as $file){
					if(is_file($this->pluginPath."img/".$file) && !is_dir($this->pluginPath."img/".$file)){
						$files[] = $file;
					}
				}
				return $files;
			}
			if(strtoupper($type) == 'SWF'){
				$files = array();
				foreach(scandir($this->pluginPath."swf/") as $file){
					if(is_file($this->pluginPath."swf/".$file) && !is_dir($this->pluginPath."swf/".$file)){
						$files[] = $file;
					}
				}
				return $files;
			}
		}
	}
	
	/**
     * The method copyFile firstly defines the runtime files to be included in each compiled test folder
	 * Then it calls the copyFile method to accomplish its task
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param array $plugins to be copied. If not set all are copied
     * @return void
     */
	public function copyPlugins($plugins = array()){
		foreach($plugins as $plugin){
			$plugin = strtolower($plugin);
			if(!is_dir("{$this->compiledPath}/{$plugin}/")){
				mkdir("{$this->compiledPath}/{$plugin}/");
			}	
			foreach($this->getPlugins($plugin) as $file){
				tao_helpers_File::copy("{$this->pluginPath}/{$plugin}/{$file}", "{$this->compiledPath}/{$plugin}/{$file}", true);
			}
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
		
		$defaultMedia = array("jpg","jpeg","png","gif","mp3",'swf','wma','wav', 'css', 'js');
		
		$authorizedMedia = array_merge($defaultMedia, $authorizedMedia);
		$authorizedMedia = array_unique($authorizedMedia);//eliminate duplicate
		
		$mediaList = array();
		$expr="/http[s]?:\/\/[^<'\"&?]+\.(".implode('|',$authorizedMedia).")/mi";
		preg_match_all($expr, $xml, $mediaList, PREG_PATTERN_ORDER);

		
		$plugins = $this->getPlugins();

		$uniqueMediaList = 	array_unique($mediaList[0]);
                
		$compiledUrl = str_replace(ROOT_PATH, ROOT_URL, $this->compiledPath);
		foreach($uniqueMediaList as $mediaUrl){
			if(in_array(basename($mediaUrl), $plugins)){
                                //if it is only a (valid) plugin file, don't try to download it but simply change the link:
                                //if the user upload an OWI with the exact same name and path, consider it as the same as the TAO version
                                if(preg_match_all('/\.(js|css|swf)$/i', basename($mediaUrl), $matches)){
                                        $xml = str_replace($mediaUrl, $compiledUrl.'/'.$matches[1][0].'/'.basename($mediaUrl), $xml, $replaced);
                                }
			}
			else{
				$mediaPath = $this->copyFile($mediaUrl, $directory.'/', $itemName, true);
				if(!empty($mediaPath)){
					$xml = str_replace($mediaUrl, $compiledUrl.'/'.basename($mediaUrl), $xml, $replaced);//replace only when copyFile is successful
				}
			}
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
		$returnValue = array("completed"=>$this->completed, "failed"=>$this->failed);
		return $returnValue;
	}
	
	/**
	 * The method getUniqueId provide an unique id for the ressource, which is a substring of the resource uri
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param  string uriResource
     * @return string
     */	
	public static function getUniqueId($uriResource){
		$returnValue='';
		
		//TODO check format of the uri, preg_match()
		if(stripos($uriResource,"#")>0){
			$returnValue = substr($uriResource, stripos($uriResource,"#")+1);
		}
		
		return $returnValue;
	}
	
	/**
	* retrieve the test uri from the test compiled folder 
	*/
	public static function getTestUri($url){
	
		$returnValue = '';
		$urlPart = explode('/', strip_tags($url));
		$lastPart = array_pop($urlPart);
		// $paramStartIndex = strpos($lastPart,'?');throw new Exception($paramStartIndex);
		$lastPart = substr($lastPart,0,strpos($lastPart,'?'));
		
		if($lastPart == 'theTest.php'){
			$uri = array_pop($urlPart);
			$session = core_kernel_classes_Session::singleton();
			$returnValue =  $session->getNameSpace().'#'.$uri;
		}
		
		return $returnValue;
	}
	
	/** 
	* Get the default absolute path to the compiled folder of a test 
	*/
	public static function getCompiledTestUrl($deliveryUri, $testUri, $itemUri){
		$testUrl ='';
		
		$testUniqueId = self::getUniqueId($testUri);
		if(!empty($testUniqueId)){
			$testUrl = self::getUniqueId($deliveryUri).'/'.self::getUniqueId($testUri).'/'.self::getUniqueId($itemUri).'/index.html';
		}
		
		return $testUrl;
	}
	
	/**
	 * The method clear the compiled folder
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return boolean
     */	
	public function clearCompiledFolder(){
		$returnValue=false;
		
		$path = $this->compiledPath;
		
		//security check: detect directory traversal (deny the ../)
		if(preg_match("/\.\.\//", $path)){
			throw new Exception("forbidden path format");
			return $returnValue;
		}
		
		//security check:  detect the null byte poison by finding the null char injection
		for($i = 0; $i < strlen($path); $i++){
			if(ord($path[$i]) === 0){
				throw new Exception("forbidden path format");
				return $returnValue;
			}
		}
		
		$returnValue = $this->recursiveDelete($this->compiledPath, false);
		
		return $returnValue;
	}
	
	/**
	 * Delete a file or recursively delete a directory
	 *
	 * @access protected
	 * @param string $toDelete
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return boolean
     */	
    protected function recursiveDelete($toDelete, $empty=true){
		$returnValue=false;
		
        if(is_file($toDelete)){
            if(@unlink($toDelete)){
				$returnValue=true;
			}else{
				throw new Exception("the file $toDelete cannot be deleted, please check the access permission");
			}
        }
        elseif(is_dir($toDelete)){
            $scan = glob(rtrim($toDelete,'/').'/*');
            foreach($scan as $index=>$path){
                $returnValue = $this->recursiveDelete($path, false);//delete entirely the subfolders (currently /css and /js)
            }
			if($empty === true){
				if (@rmdir($toDelete)) $returnValue=true;
				else throw new Exception("the folder $toDelete cannot be deleted, please check the access permission");
			}
        }
		
		return $returnValue;
    }
	
	/**
	* record the items that have translation issues in the "failed" array
	* @access protected
	* @param string $name
	* @param string $language
	* @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	* @return void
	*/
	public function setUntranslatedItem($name, $language){
		$this->failed["untranslatedItems"][$language][] = $name;
	}
	
	/**
	* record an error message in the "errorMsg" array
	* @access protected
	* @param string $message
	* @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	* @return void
	*/
	public function setErrorMsg($message){
		$this->failed["errorMsg"][] = $message; 
	}
	
} /* end of class taoDelivery_helpers_Compilator */

?>