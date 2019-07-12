extension-tao-delivery
======================

Extension to manage delivery

# Configuration options:

## AttemptService.conf.php

### Configuration option "states_to_exclude"

*Description :* when retrieving attempts (executions), those attempts with specified states won't be retrieved

*Possible states :* 
* http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStatusActive : active state
* http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStatusPaused : paused state
* http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStatusFinished : finished state
* http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStatusTerminated : terminated state

One can specify multiple states as an array to exclude.

## authorization.conf.php

### Configuration option "providers"

*Description :* when verifying that a given delivery execution is allowed to be executed, the specified providers are used. For an execution to be rejected, at least one provider should throw an exception, return values are not considered 

*Possible values:* 
* Objects of a class that implements the AuthorizationProvider interface

*Value examples :* 
* [ new oat\taoDelivery\model\authorization\strategy\StateValidation() ]
* [ new oat\taoDelivery\model\authorization\strategy\StateValidation(), oat\taoDelivery\model\authorization\strategy\AuthorizationAggregator() ]


## DeliveryExecutionDelete.conf.php

### Configuration option "deleteDeliveryExecutionDataServices"

*Description:* the list of services to remove a delivery execution

*Possible values:* 
* Objects of a class that implements the DeliveryExecutionDelete interface.


## deliveryFields.conf.php

### Configuration option "http://www.tao.lu/Ontologies/TAODelivery.rdf#CustomLabel"

*Description:* the use roles able to see delivery custom labels

*Possible values:* 
* Any TAO roles

*Value examples:* 
* [ 'http://www.tao.lu/Ontologies/TAO.rdf#DeliveryRole' ]

## DeliveryServer.conf.php
No options

## execution_service.conf.php
No options

## returnUrl.conf.php

### Configuration option "extension"

*Description:* an extension name for composing a return URL

*Possible values:* 
* Any TAO extension name

*Value examples:* 
* taoDelivery

### Configuration option "controller"

*Description:* a controller (module) name for composing a return URL

*Possible values:* 
* Any controller within the extension above

*Value examples:* 
* Main

### Configuration option "method"

*Description:* a method (action) for composing a return URL

*Possible values:* 
* any public method within the controller above

*Value examples:* 
* index

## Runtime.conf.php
No options

## stateService.conf.php
No options