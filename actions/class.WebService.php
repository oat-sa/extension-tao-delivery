<?php
class taoDelivery_actions_WebService extends tao_actions_CommonModule{
	
	public function index(){
		$width = $this->hasRequestParameter('width')?$this->getRequestParameter('width'):'100%';
		$height = $this->hasRequestParameter('height')?$this->getRequestParameter('height'):'100%';
		$url = urldecode($this->getRequestParameter('url'));
		echo '<iframe id="webWerviceFrame" src ="'.$url.'" width="'.$width.'" height="'.$height.'"/>';
			
	}
}
