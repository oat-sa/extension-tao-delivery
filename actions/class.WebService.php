<?
class taoDelivery_actions_WebService extends tao_actions_CommonModule{
	
	public function index(){
		$width = $this->hasRequestParameter('width')?$this->getRequestParameter('width'):'100%';
		$height = $this->hasRequestParameter('height')?$this->getRequestParameter('height'):'100%';
		$url = urldecode($this->getRequestParameter('url'));
		$content = '<iframe id="webWerviceFrame" src ="'.$url.'" width="'.$width.'" height="'.$height.'"/>';
		$control = '<form>
				<input type="button" VALUE="&lt; &lt; &nbsp; Back &nbsp;"  onClick="history.back()"> 
				<input type="button" VALUE="Forward &nbsp; &gt; &gt;"  onClick="history.forward()"> 
			</form>';
			
		echo $control.$content;
	}
}
?>

