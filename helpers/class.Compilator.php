<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);\n *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
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
     * The attribute "item" define the item that is being compiled
     *
     * @access protected
     * @var core_kernel_classes_Resource
     */
	protected $item = null;
	
	/**
     * The attribute "test" define the test that is being compiled
     *
     * @access protected
     * @var core_kernel_classes_Resource
     */
	protected $test = null;
	
	/**
     * The attribute "delivery" define the delivery that is being compiled
     *
     * @access protected
     * @var core_kernel_classes_Resource
     */
	protected $delivery = null;
	
    // --- OPERATIONS ---
	
	/**
     * The method __construct intiates the Precompilator class by setting the initial values to the attributes 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param  string delivery
	 * @param  string test
	 * @param  string item
	 * @param  string compiledPath
     * @return mixed
     */	
	public function __construct(core_kernel_classes_Resource $delivery, core_kernel_classes_Resource $test, core_kernel_classes_Resource $item, $compiledPath=''){
		
		$this->delivery = $delivery;
		$this->test = $test;
		$this->item = $item;
		
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
		
		
		$deliveryExtension = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
		if(!empty($pluginPath)){
			$this->pluginPath = $pluginPath;
		}else{
			$this->pluginPath = $deliveryExtension->getConstant('BASE_PATH')."/lib/";
		}
		if(!is_dir($this->pluginPath)){
			throw new Exception("The plugin directory '{$this->pluginPath}' does not exist");
		}
		
		if(!empty($compiledPath)){
			$this->compiledPath = $compiledPath;
		}else{
			$this->compiledPath = $deliveryExtension->getConstant('BASE_PATH')."/compiled/";
		}
		if(!is_writable($this->compiledPath)){
			throw new Exception("The compiled directory '{$this->compiledPath}' is not writable");
		}
			
		if(!is_dir($this->compiledPath)){
			$this->failed["createdFiles"]["compiled_test_folder"] = $this->compiledPath;
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
     * The method copyFile enable tje conpilator to copy a file from a remote location
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
			// Since the file has not been downloaded yet, start downloading it using cUrl

			// Only if the resource is external to TAO or in the filemanager of the current instance.
			error_reporting(E_ALL);
			if(!preg_match('@^' . BASE_URL . '@', $url)){
				
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
				
				curl_close($curlHandler);  
			}
			else{
			
				// Duplicated file copy. Not useful.
				//$fileContent = @file_get_contents($url);
				$fileContent = null;
			}
			
			if ($fileContent === false){
				
				$this->failed["copiedFiles"][$affectedObject][]=$url;
				return $returnValue;
			}
						
			//check file name compatibility: 
			//e.g. if a file with a common name (e.g. car.jpg, house.png, sound.mp3) already exists in the destination folder
			while(file_exists($directory.$fileName) && $rename===true){
				$reverseFileName = strrev($fileName); 
				$reverseExt = substr($reverseFileName, 0, strpos($reverseFileName,"."));
				$reverseName = substr($reverseFileName, strpos($reverseFileName,".")+1);
				
				//add an underscore so it becomes unique
				$fileName = strrev($reverseName)."_.".strrev($reverseExt);
			}
			
			if($fileContent !== null && file_put_contents($directory.$fileName, $fileContent) === false){
				throw new Exception("the file $directory.$fileName cannot be written");
			}
			
			//record in the property "completed" that the file has been successfullly downloaded 
			if ($fileContent !== null){
				$this->completed["copiedFiles"][$affectedObject][]=$url;
				$returnValue = $fileName;
			}
			else {
				$returnValue = false;	
			}
			
			
		}
				
		return $returnValue;
	}
    
	/**
	 * @todo : get the plugins to be copied in thte compiled delivery according to the item type
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return array
	 */
	public function getPlugins(){
		
		$returnValue = array();
		
		//@todo : get the plugins to be copied in thte compiled delivery according to the item type
		//@todo : distinguish language dependent and non-dependent resources?
		$itemModel = taoItems_models_classes_ItemsService::singleton()->getItemModel($this->item);
		if($itemModel->getUri() == TAO_ITEM_MODEL_QTI){
			$taoQTIext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQTI');
			$libs = array(
				'QtiImg' => array(
					'path' => $taoQTIext->getConstant('BASE_PATH') . 'views/js/QTI/img/',
					'relativePath' => '../img/',
					'files' => '*'
				),
				'QtiJqueryUIimg' => array(
					'path' => TAOVIEW_PATH . 'css/custom-theme/images/',
					'relativePath' => 'images/',
					'files' => '*'
				)
			);

			foreach ($libs as $libConf) {
				if (isset($libConf['path']) && isset($libConf['relativePath']) && isset($libConf['files'])) {
					$path = $libConf['path'];
					$relativePath = $libConf['relativePath'];
					$files = $libConf['files'];
					if ($files === '*') {
						foreach (scandir($path) as $fileName) {
							if (is_file($path . $fileName)) {
								$returnValue[$path . $fileName] = $relativePath . $fileName;
							}
						}
					} elseif (is_array($files)) {
						foreach ($files as $fileName) {
							if (is_file($path . $fileName)) {
								$returnValue[$path . $fileName] = $relativePath . $fileName;
							}
						}
					}
				}
			}
		}
		
		return $returnValue;
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
		foreach ($this->getPlugins() as $absoluePath => $relativePath){
			if(tao_helpers_File::copy($absoluePath, $this->compiledPath.'/'.$relativePath, true)){
				$this->completed['copiedFiles'][] = $absoluePath;
			}else{
				$this->failed['copiedFiles'][] = $absoluePath;
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
		$expr = "/http[s]?:\/\/[^<'\"&?]+\.(".implode('|',$authorizedMedia).")/mi";
		preg_match_all($expr, $xml, $mediaList, PREG_PATTERN_ORDER);

		$plugins = $this->getPlugins();
		$uniqueMediaList = 	array_unique($mediaList[0]);
		$compiledUrl = tao_helpers_Uri::getUrlForPath($this->compiledPath);
		
		foreach($uniqueMediaList as $mediaUrl){
			if(in_array(basename($mediaUrl), $plugins)){
				//if it is only a (valid) plugin file, don't try to download it but simply change the link:
				//if the user upload an OWI with the exact same name and path, consider it as the same as the TAO version
				if(preg_match_all('/\.(js|css|swf)$/i', basename($mediaUrl), $matches)){
					// This break paths ! to change in further versions.	
					//$xml = str_replace($mediaUrl, $compiledUrl.'/'.$matches[1][0].'/'.basename($mediaUrl), $xml, $replaced);
				}
			}
			else{
				// This is a file that has to be stored in the item compilation folder itself...
				// I do not get why they are all copied. They are all there they were copied from the item module...
				// But I agree that remote resources (somewhere on the Internet) should be copied via curl.
				// So if the URL does not matches a place where the TAO server is, we curl the resource and store it.
				// FileManager files should be considered as remote resources to avoid 404 issues. Indeed, a backoffice
				// user might delete an image in the filemanager during a delivery campain. This is dangerous.
				$mediaPath = $this->copyFile($mediaUrl, $directory.'/', $itemName, true);
				if(!empty($mediaPath) && $mediaPath !== false){
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
		$handle = fopen($directory.'/'.$fileName,'wb');
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