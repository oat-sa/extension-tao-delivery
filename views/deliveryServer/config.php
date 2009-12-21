<?php
define('API_LOGIN','generis');
define('API_PASSWORD',md5('g3n3r1s'));
define('TAODELIVERY_PATH','../../');


require_once(TAODELIVERY_PATH.'../generis/common/inc.extension.php');
require_once(TAODELIVERY_PATH.'includes/common.php');
require_once(TAODELIVERY_PATH.'includes/constants.php');
require_once(TAODELIVERY_PATH.'includes/config.php');
require_once(TAODELIVERY_PATH.'models/classes/class.DeliveryService.php');

core_control_FrontController::connect(API_LOGIN, API_PASSWORD, DATABASE_NAME);

?>