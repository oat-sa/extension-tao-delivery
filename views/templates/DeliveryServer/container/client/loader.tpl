<?php
use oat\tao\helpers\Layout;
use oat\tao\helpers\Template;
?>

<?= Layout::getAmdLoader(
    Template::js('loader/taoQtiTestRunner.min.js', 'taoQtiTest'),
    'taoQtiTest/controller/runner/runner',
    [
        'testDefinition'       => get_data('testDefinition'),
        'testCompilation'      => get_data('testCompilation'),
        'serviceCallId'        => get_data('serviceCallId'),
        'deliveryServerConfig' => get_data('deliveryServerConfig'),
        'bootstrap'            => get_data('bootstrap'),
        'providers'            => get_data('providers'),
        'options'              => [
            'exitUrl' => get_data('returnUrl')
        ]
    ]
); ?>
