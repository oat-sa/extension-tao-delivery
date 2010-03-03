<?include('header.tpl')?>

<?include('delivery_tests.tpl')?>

<div class="main-container" style="height:500px;">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>
<div></div>

<?include('subjects.tpl')?>

<?include('delivery_campaign.tpl')?>

<?include('footer.tpl');?>