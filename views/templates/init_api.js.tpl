$(document).ready(function(){

	var <?=get_data('envVarName')?> = <?=get_data('executionEnvironment')?>;
	initManualDataSource(<?=get_data('envVarName')?>);

	<?if(get_data('pushParams')):?>
		initPush(<?=get_data('pushParams')?>, null);
	<?endif?>
	
	<?if(get_data('eventData')):?>
		initEventServices({ type: 'manual', data: <?=get_data('eventData')?>}, <?=get_data('eventParams')?>);
	<?endif?>
	
	<?if(get_data('matchingServer') === true):?>
		var matchingParam = $.extend(<?=get_data('matchingParams')?>, {
		    "format" : "json", 
		    "options" : {
		        "evaluateCallback" : function () {
		        	// Finish the process
		            finish();
		        }
		    }
		});
	<?else:?>
		var matchingParam = {
		    "data" 		: <?=get_data('matchingData')?>, 
		    "format" 	: "json", 
		    "options" 	: {
		        "evaluateCallback" : function () {
		            var outcomes = matchingGetOutcomes();
		            for (var outcomeKey in outcomes){
		            	if(outcomeKey.toUpperCase() == 	'SCORE'){
		            		setScore(outcomes[outcomeKey]['value']);
		            	}
		               else{
		               		setUserVar(outcomeKey, outcomes[outcomeKey]['value']);
		               }
		            }
		            // Finish the process
		            finish();
		        }
		    }
		};
	<?endif?>
	
	matchingInit(matchingParam);
	
	initRecoveryContext(<?=get_data('contextSourceParams')?>, <?=get_data('contextDestinationParams')?>);
});