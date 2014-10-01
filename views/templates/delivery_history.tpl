<?php
use oat\tao\helpers\Template;
?><h2><?=__("History")?></h2>
<div id="form-history" class="form-content">
	<div id="history-link-container" class="ext-home-container">
		<p>
		<?php if(get_data('executionNumber')):?>
			<?=__('There are currently')?>&nbsp;<?=get_data('executionNumber')?>&nbsp;<?=__('delivery executions')?>.
		<?php else:?>
			<?=__('There is currently no delivery execution.')?>
		<?php endif;?>
		</p>
	</div>
	<div>
		<table id="history-list"></table>
		<div id="history-list-pager"></div>
	</div>
</div>

<script type="text/javascript">
	var historyGrid = null;
	var actionUrl = '';

function buildHistoryGrid(selector) {
<?php if(tao_helpers_Context::check('STANDALONE_MODE')):?>
	actionUrl = "<?=_url('historyData', 'Delivery', 'taoDelivery', array('STANDALONE_MODE' => true))?>";
<?php else:?>
	actionUrl = "<?=_url('historyData', 'Delivery', 'taoDelivery')?>";
<?php endif;?>
	require(['require', 'jquery', 'grid/tao.grid'], function(req, $) {
		historyGrid = $(selector).jqGrid({
			url: actionUrl,
			datatype: "json",
			colNames:[ __('Test Taker'), __('Time'), __('Actions')],
			colModel:[
				{name:'subject',index:'subject'},
				{name:'time',index:'time'},
				{name:'actions',index:'actions', align:"center", sortable: false}
			],
			rowNum:20,
			height:300,
			width:parseInt($(selector).width()) - 2,
			pager: '#history-list-pager',
			sortname: 'subject',
			viewrecords: false,
			sortorder: "asc",
			caption: __("Execution History"),
			postData: {'uri': "<?=get_data('uri')?>", 'classUri': "<?=get_data('classUri')?>"},
			gridComplete: function(){
				$.each(historyGrid.getDataIDs(), function(index, elt){
					historyGrid.setRowData(elt, {
						actions: "<a id='history_deletor_"+elt+"' href='#' class='user_deletor nd' ><img class='icon' src='<?=Template::css('delete.png')?>' alt='<?=__('Delete History')?>' /><?=__('Delete')?></a>"
					});
				});
				$(".user_deletor").click(function(e){
					e.preventDefault();
					removeHistory(this.id.replace('history_deletor_', ''));
				});

				$(window).unbind('resize').bind('resize', function(){
					historyGrid.jqGrid('setGridWidth', (parseInt($(selector).width())-2));
				});
			}
		});
		historyGrid.navGrid('#history-list-pager',{edit:false, add:false, del:false});
	});
}

var removeHistory = function(uri){
	if(confirm("<?=__('Please confirm history deletion')?>")){
		$.ajax({
			url: "<?=_url('deleteHistory', 'Delivery', 'taoDelivery')?>",
			type: "POST",
			data: {
				'historyUri': uri,
				'uri': "<?=get_data('uri')?>",
				'classUri': "<?=get_data('classUri')?>"
			},
			dataType: 'json',
			success: function(r){
				if (r.deleted){
					historyGrid.trigger("reloadGrid");
					helpers.createInfoMessage(r.message);
				}else{
					helpers.createErrorMessage(r.message);
				}
			}
		});
	}
}


$(function(){
	$('#historyLink').click(function(e){
		e.preventDefault();
		$('#history-link-container').hide();
		buildHistoryGrid("#history-list");
	});
});
</script>
