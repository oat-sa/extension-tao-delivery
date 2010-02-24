<?include('header.tpl')?>

<?include('subjects.tpl')?>

<?include('delivery_campaign.tpl')?>

<div class="main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>

<script type="text/javascript">
$("div.main-container").css("height",550);
</script>

<?include('footer.tpl');?>
