<?php
class ProcessBrowser extends DeliveryServerModule
{
	
	public function index($processUri)
	{

		// if (!isset($_SESSION['taoqual.authenticated'])){
			// $this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
		// }

		$_SESSION["processUri"]= $processUri;


		$processUri 		= urldecode($processUri); // parameters clean-up.
		$this->setData('processUri',$processUri);
		
		$user 		= $_SESSION['taoqual.userId']; // user data for browser view.
		$this->setData('user',$user);
		$browserViewData 	= array(); // general data for browser view.

		$process 			= new ProcessExecution($processUri);
		if(empty($process->currentActivity)) {
			die('No current activity found in the process : ' . $processUri);
		}
		$activity 			= $process->currentActivity[0];
		$this->setData('activity',$activity);
		$activityPerf 		= new Activity($activity->uri, false); // Performance WA
		$activityExecution 	= new ActivityExecution($process, $activity);

		$browserViewData['activityContentLanguages'] = array();

		// If paused, resume it.
		if ($process->status == 'Paused'){
			$process->resume();
		}
		// Browser view main data.
		$browserViewData['isInteractiveService']	= false;

		$browserViewData['processLabel'] 			= $process->process->label;
		$browserViewData['processExecutionLabel']	= $process->label;
		$browserViewData['activityLabel'] 			= $activity->label;
		$browserViewData['isBackable']				= (FlowHelper::isProcessBackable($process));
		$browserViewData['uiLanguage']				= $GLOBALS['lang'];
		$browserViewData['contentlanguage']			= $_SESSION['taoqual.serviceContentLang'];
		$browserViewData['processUri']				= $processUri ;

		$browserViewData['uiLanguages']				= I18nUtil::getAvailableLanguages();
		$browserViewData['activityContentLanguages'] = I18nUtil::getAvailableServiceContentLanguages();

		$browserViewData['showCalendar']			= $activityPerf->showCalendar;

		// process variables data.
		$variablesViewData = array();
		$variables = $process->getVariables();

		foreach ($process->getVariables() as $var)
		{
			$variablesViewData[$var->code] = array('uri' 	=> $var->uri,
												   'value' 	=> $var->value);
		}

		$this->setData('variablesViewData',$variablesViewData);
		// consistency data.
		$consistencyViewData = array();
		if (isset($_SESSION['taoqual.flashvar.consistency']))
		{
			$consistencyException 		= $_SESSION['taoqual.flashvar.consistency'];
			$involvedActivities 		= $consistencyException['involvedActivities'];

			$consistencyViewData['isConsistent']		= false;
			$consistencyViewData['suppressable']		= $consistencyException['suppressable'];
			$consistencyViewData['notification']		= str_replace(array("\r", "\n"), '', $consistencyException['notification']);
			$consistencyViewData['processExecutionUri'] = urlencode($processUri);
			$consistencyViewData['activityUri']			= urlencode($activity->uri);
			$consistencyViewData['source']				= $consistencyException['source'];

			$consistencyViewData['involvedActivities']	= array();

			foreach ($involvedActivities as $involvedActivity)
			{
				$consistencyViewData['involvedActivities'][] = array('uri' => $involvedActivity['uri'],
																	 'label' => $involvedActivity['label'],
																	 'processUri' => $processUri);
			}

			// Clean flash variables.
			$_SESSION['taoqual.flashvar.consistency'] = null;
		}
		else
		{
			// Everything is allright with data consistency for this process.
			$consistencyViewData['isConsistent'] = true;

			$_SESSION['taoqual.flashvar.consistency'] = null;
		}

		$this->setData('consistencyViewData',$consistencyViewData);

		//The following takes about 0.2 seconds -->cache

		//retrieve activities

		if (!($qSortedActivities = common_Cache::getCache("aprocess_activities")))
		{

			$processDefinition = new core_kernel_classes_resource($process->process->uri);
			$activities = $processDefinition->getPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES));

			//sort the activities
			$qSortedActivities =array();
			foreach ($activities as $key=>$val)
			{
				$activity_res = new core_kernel_classes_resource($val);
				$label = $activity_res->label;
				$qSortedActivities[$label] = $val;

			}
			ksort($qSortedActivities);
			common_Cache::setCache($qSortedActivities,"aprocess_activities");
		}

		$browserViewData['annotationsResourcesJsArray'] = array();
		foreach ($qSortedActivities as $key=>$val)
		{
			$browserViewData['annotationsResourcesJsArray'][]= array($val,$key);
		}

		$browserViewData['active_Resource']="'".$activity->uri."'" ;
		$browserViewData['isInteractiveService'] 	= true;

		$servicesViewData 	= array();

		$services = $activityExecution->getInteractiveServices();

		$this->setData('services',$services);

		$this->setData('browserViewData', $browserViewData);
		$this->setView('process_browser.tpl');
	}

	public function back($processUri)
	{
		//UsersHelper::checkAuthentication();

		$processUri 	= urldecode($processUri);
		$processExecution = new ProcessExecution($processUri);
		$activity = $processExecution->currentActivity[0];
		$processExecution->performBackwardTransition($activity);
		$processUri 	 = urlencode($processUri);

		if (!ENABLE_HTTP_REDIRECT_PROCESS_BROWSER)
		{
			$this->index($processUri);
		}
		else
		{
			$this->redirect(_url('index', 'processBrowser', null, array('processUri' => urlencode($processUri))));
		}
	}

	public function next($processUri, $ignoreConsistency = 'false')
	{
		//UsersHelper::checkAuthentication();
	
	
	
		$processUri 	= urldecode($processUri);
		$processExecution = new ProcessExecution($processUri);
	
		try
		{
			$processExecution->performTransition(($ignoreConsistency == 'true') ? true : false);
	
			if (!$processExecution->isFinished())
			{
				$processUri = urlencode($processUri);
	
				if (!ENABLE_HTTP_REDIRECT_PROCESS_BROWSER) {
					$this->index($processUri);
				}
				else
				{
					$param = array( 'processUri' => urlencode($processUri));
					$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $param));
				}
			}
			else
			{

				$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
				
			}
		}
		catch (ConsistencyException $consistencyException)
		{
			// A consistency error occured when trying to go
			// forward in the process. Let's try to get useful
			// information from the exception.
		
			// We need to tell the "index" action of the "ProcessBrowser" controller
			// that a consistency exception occured. To do so, we will use the concept
			// of flash variable. This kind of variable survives during one and only one
			// HTTP request lifecycle. So that in the "index" action, the session variable
			// depicting the error will be systematically erased after each processing.
			//$_SESSION['taoqual.flashvar.consistency'] = $consistencyException;
			$consistency = ConsistencyHelper::BuildConsistencyStructure($consistencyException);
			$_SESSION['taoqual.flashvar.consistency'] = $consistency;
		
			$this->redirect(_url('index', 'processBrowser', null, array('processUri' => urlencode($processUri))));
		}
	}

	public function pause($processUri)
	{
		//UsersHelper::checkAuthentication();

		$processUri 	= urldecode($processUri);
		$processExecution = new ProcessExecution($processUri);

		$processExecution->pause();
		$_SESSION["processUri"]= null;
		
		$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
	}

}
?>
