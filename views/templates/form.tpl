<?php
use oat\tao\helpers\Template;
?>
<header class="flex-container-full">
	<h2><?=get_data('formTitle')?></h2>
</header>
<div class="main-container flex-container-main-form">
	<div id="form-container">
		<?=get_data('myForm')?>
	</div>
</div>

<div class="data-container-wrapper flex-container-remainer"></div>

<?php Template::inc('footer.tpl'); ?>
