<?php
define('API_LOGIN','generis');
define('API_PASSWORD','g3n3r1s');
define('TAODELIVERY_PATH','../../');


require_once(TAODELIVERY_PATH .'../generis/common/inc.extension.php');
require_once(TAODELIVERY_PATH.'includes/common.php');
require_once(TAODELIVERY_PATH.'includes/constants.php');
require_once(TAODELIVERY_PATH.'includes/config.php');
require_once(TAODELIVERY_PATH.'models/classes/class.DeliveryService.php');

core_control_FrontController::connect(API_LOGIN, API_PASSWORD, DATABASE_NAME);

// require_once('../../../generis/common/config.php');
// require_once('../../models/classes/class.DeliveryServerService.php');
// require_once('../../../generis/core/kernel/classes/class.DbWrapper.php');
?>