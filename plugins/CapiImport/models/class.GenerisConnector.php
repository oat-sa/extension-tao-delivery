<?php


class GenerisConnector 
extends common_Object
{
	public $importError = '';
	protected $logger;
	protected $generisApi;

	public function __construct($debug = '') {
		$this->debug = $debug;
		// $this->logger = new Logger('GenerisCapiCreation', Logger::debug_level);
		// core_control_FrontController::connect(GENERIS_LOGIN, md5(GENERIS_PASS),GENERIS_MODULE);	
		// core_kernel_classes_Session::singleton()->setLg("EN");	
		$this->generisApi = core_kernel_impl_ApiModelOO::singleton();
	}

	public function importCapi($capiDescriptor) {
		return $capiDescriptor->import();
	}
}
?>
