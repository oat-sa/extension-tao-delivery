<?php
use oat\tao\helpers\Template;
Template::inc('form_context.tpl', 'tao');
?>
<?= tao_helpers_Scriptloader::render() ?>

<header class="section-header flex-container-full">
    <h2><?=get_data('formTitle')?></h2>
</header>
<div class="main-container flex-container-main-form">
    <div class="form-content web-browser-form">
        <?=get_data('myForm')?>
        <div class="browser-version-tooltip-content tooltip-content">
            The version from which delivery execution will be available.<br>
            <hr style="margin:5px 0;"/>
            <strong>Examples:</strong><br>
            <i>10</i><br>
            <i>10.1</i><br>
            <i>57.0.1</i><br>
        </div>
    </div>
</div>
<div class="data-container-wrapper flex-container-remaining"></div>

<?php Template::inc('footer.tpl', 'tao'); ?>
