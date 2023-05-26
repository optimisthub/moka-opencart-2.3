<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-payment" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
            </div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
        <?php foreach ($alerts as $alert) { ?>
            <div class="alert alert-<?php echo $alert['type']; ?>"><i class="fa fa-<?php echo $alert['icon']; ?>"></i>&nbsp;<?php echo $alert['text']; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<form method="POST" id="form-payment" class="form-horizontal">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab-setting" data-toggle="tab"><i class="fa fa-gear"></i>&nbsp;<?php echo $tab_setting; ?></a></li>
						<li><a href="#tab-transaction" data-toggle="tab"><i class="fa fa-list"></i>&nbsp;<?php echo $tab_transaction; ?></a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="tab-setting">
							<div class="form-group required">
								<label class="col-sm-2 control-label" for="moka-api-environment"><?php echo $entry_api_environment; ?></label>
								<div class="col-sm-10">
									<select name="moka_api_environment" class="form-control" id="moka-api-environment">
                                        <option value="test" <?php echo $moka_api_environment == 'test' ? 'selected' : ''; ?>><?php echo $text_sandbox; ?></option>
                                        <option value="live" <?php echo $moka_api_environment == 'live' ? 'selected' : ''; ?>><?php echo $text_live; ?></option>
									</select>
									<?php if ($error_api_environment) { ?>
										<div class="text-danger"><?php echo $error_api_environment; ?></div>
									<?php } ?>
								</div>
							</div>
							<div class="form-group required">
								<label class="col-sm-2 control-label" for="moka-dealer-code"><?php echo $entry_dealer_code; ?></label>
								<div class="col-sm-10">
									<input type="text" name="moka_dealer_code" class="form-control" value="<?php echo $moka_dealer_code; ?>" placeholder="<?php echo $entry_dealer_code; ?>" id="moka-dealer-code"/>
									<?php if ($error_dealer_code) { ?>
										<div class="text-danger"><?php echo $error_dealer_code; ?></div>
									<?php } ?>
								</div>
							</div>
							<div class="form-group required">
								<label class="col-sm-2 control-label" for="moka-username"><?php echo $entry_username; ?></label>
								<div class="col-sm-10">
									<input type="text" name="moka_username" class="form-control" value="<?php echo $moka_username; ?>" placeholder="<?php echo $entry_username; ?>" id="moka-username"/>
									<?php if ($error_username) { ?>
										<div class="text-danger"><?php echo $error_username; ?></div>
									<?php } ?>
								</div>
							</div>
							<div class="form-group required">
								<label class="col-sm-2 control-label" for="moka-password"><?php echo $entry_password; ?></label>
								<div class="col-sm-10">
									<input type="text" name="moka_password" class="form-control" value="<?php echo $moka_password; ?>" placeholder="<?php echo $entry_password; ?>" id="moka-password"/>
									<?php if ($error_password) { ?>
										<div class="text-danger"><?php echo $error_password; ?></div>
									<?php } ?>
								</div>
							</div>
							<div class="form-group required">
                                <label class="col-sm-2 control-label">
                                    <span data-toggle="tooltip" title="<?php echo $help_order_status; ?>"><?php echo $entry_order_status; ?></span>
                                </label>
								<div class="col-sm-10">
									<select name="moka_order_status" class="form-control" id="moka-order-status">
										<?php foreach ($order_statuses as $order_status) { ?>
											<?php if ($order_status['order_status_id'] == $moka_order_status) { ?>
												<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
											<?php } else { ?>
												<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
											<?php } ?>
										<?php } ?>
									</select>
									<?php if ($error_order_status) { ?>
										<div class="text-danger"><?php echo $error_order_status; ?></div>
									<?php } ?>
								</div>
							</div>
							<div class="form-group required">
                                <label class="col-sm-2 control-label">
                                    <span data-toggle="tooltip" title="<?php echo $help_cancel_order_status; ?>"><?php echo $entry_cancel_order_status; ?></span>
                                </label>
								<div class="col-sm-10">
									<select name="moka_cancel_order_status" class="form-control" id="moka-cancel-order-status">
										<?php foreach ($order_statuses as $order_status) { ?>
											<?php if ($order_status['order_status_id'] == $moka_cancel_order_status) { ?>
												<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
											<?php } else { ?>
												<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
											<?php } ?>
										<?php } ?>
									</select>
									<?php if ($error_cancel_order_status) { ?>
										<div class="text-danger"><?php echo $error_cancel_order_status; ?></div>
									<?php } ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="moka-status"><?php echo $text_status; ?></label>
								<div class="col-sm-10">
									<select name="moka_status" class="form-control" id="moka-status">
                                        <option value="1" <?php echo $moka_status == 1 ? 'selected' : ''; ?>><?php echo $text_extension_status_enabled; ?></option>
                                        <option value="0" <?php echo $moka_status == 0 ? 'selected' : ''; ?>><?php echo $text_extension_status_disabled; ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="moka-sort-order"><?php echo $text_sort_order; ?></label>
								<div class="col-sm-10">
									<input type="text" name="moka_sort_order" class="form-control" value="<?php echo $moka_sort_order; ?>" placeholder="<?php echo $text_sort_order; ?>" id="moka-sort-order"/>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab-transaction">
							<div id="transaction-alert" data-message="<?php echo $text_loading; ?>"></div>
							<div class="text-right margin-bottom"></div>
							<div class="table-responsive">
								<table class="table table-bordered table-hover">
									<thead>
										<tr>
											<th class="text-left hidden-xs"><?php echo $column_transaction_id; ?></th>
											<th class="text-left hidden-xs"><?php echo $column_payment_id; ?></th>
											<th class="text-left"><?php echo $column_customer; ?></th>
											<th class="text-left hidden-xs"><?php echo $column_order_id; ?></th>
											<th class="text-left hidden-xs"><?php echo $column_amount; ?></th>
											<th class="text-left hidden-xs"><?php echo $column_installment_number; ?></th>
											<th class="text-left hidden-xs"><?php echo $column_commission_amount; ?></th>
											<th class="text-left hidden-xs hidden-sm"><?php echo $column_payment_status; ?></th>
											<th class="text-left hidden-xs hidden-sm"><?php echo $column_transaction_status; ?></th>
											<th class="text-left"><?php echo $column_created_at; ?></th>
										</tr>
									</thead>
									<tbody id="transactions"></tbody>
								</table>
								<div id="transactions_pagination"></div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function () {
    var transactionListPage = 1;

    var listTransactions = function (page) {
        $.ajax({
            url: '<?php echo $url_list_transactions; ?>'.replace('{PAGE}', page ? page : transactionListPage),
            dataType: 'json',
            beforeSend: function () {
                $('#transactions_pagination').empty();
                $('#transactions').html('<tr><td colspan="9" class="text-center"><i class="fa fa-circle-o-notch fa-spin"></i>&nbsp;<?php echo $text_loading; ?></td></tr>');
            },
            success: function (data) {
                var html = '';

                if (data.transactions.length) {
                    for (var i in data.transactions) {
                        var row = data.transactions[i];

                        html += '<tr>';
                        html += '<td class="text-left hidden-xs">' + row.moka_transaction_id + '</td>';
                        html += '<td class="text-left hidden-xs">' + row.payment_id + '</td>';
                        html += '<td class="text-left hidden-xs">' + row.customer + '</td>';
                        html += '<td class="text-left"><a target="_blank" href="' + row.url_order + '">' + row.order_id + '</td>';
                        html += '<td class="text-left hidden-xs">' + row.amount + '</td>';
                        html += '<td class="text-left hidden-xs">' + row.installment_number + '</td>';
                        html += '<td class="text-left hidden-xs">' + row.commission_amount + '</td>';
                        html += '<td class="text-left hidden-xs">' + row.payment_status + '</td>';
                        html += '<td class="text-left hidden-xs">' + row.transaction_status + '</td>';
                        html += '<td class="text-left">' + row.created_at + '</td>';
                        html += '</tr>';
                    }
                } else {
                    html += '<tr>';
                    html += '<td class="text-center" colspan="9"><?php echo $text_no_transactions; ?></td>';
                    html += '</tr>';
                }

                $('#transactions').html(html);

                $('#transactions_pagination').html(data.pagination).find('a[href]').each(function (index, element) {
                    $(this).click(function (e) {
                        e.preventDefault();

                        transactionListPage = isNaN($(this).attr('href')) ? 1 : $(this).attr('href');

                        listTransactions();
                    });
                });
            }
        });
    }

    listTransactions();
});
</script>
<?php echo $footer; ?>
