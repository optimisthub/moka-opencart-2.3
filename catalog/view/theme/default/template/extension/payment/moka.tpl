<form method="POST" id="moka_card_details_form">
    <div class="row" style="margin-top: 40px;">
        <div class="col-md-6 col-md-offset-3">
            <div class="form-group required">
                <label class="control-label" for="input-card-holder-fullname"><?php echo $entry_card_holder_full_name; ?></label>
                <input type="text" name="card_holder_full_name" placeholder="<?php echo $entry_card_holder_full_name; ?>" id="input-card-holder-fullname" class="form-control">
            </div>
            <div class="form-group required">
                <label class="control-label" for="input-card-number"><?php echo $entry_card_number; ?></label>
                <input type="text" name="card_number" placeholder="••••  ••••  ••••  ••••" id="input-card-number" class="form-control">
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group required">
                        <label class="control-label" for="input-card-expiry-month"><?php echo $entry_card_expiry_month; ?></label>
                        <select name="card_expiry_month" id="input-card-expiry-month" class="form-control">
                            <?php foreach ($months as $month) { ?>
                                <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group required">
                        <label class="control-label" for="input-card-expiry-year"><?php echo $entry_card_expiry_year; ?></label>
                        <select name="card_expiry_year" id="input-card-expiry-year" class="form-control">
                            <?php foreach ($years as $year) { ?>
                                <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group required">
                        <label class="control-label" for="input-cvc-number"><?php echo $entry_card_cvc_number; ?></label>
                        <input type="text" name="card_cvc_number" placeholder="<?php echo $entry_card_cvc_number; ?>" id="input-cvc-number" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 40px;">
        <div class="col-md-6 col-md-offset-3">
            <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" />
        </div>
    </div>
</form>
<script type="text/javascript">
    var form = '#moka_card_details_form';

    $('#button-confirm').on('click', function() {
        $.ajax({
            url: '<?php echo $checkout; ?>',
            method: 'POST',
            data : $(form).serialize(),
            dataType: 'json',
            beforeSend: function() {
                $('.moka_message').remove();
                $('#button-confirm').button('loading');
            },
            complete: function() {
                $('#button-confirm').button('reset');
            },
            success: function(data) {
                if (data['error_warning']) {
                    $(form).before('<div class="alert alert-danger moka_message" style="display:none"><i class="fa fa-exclamation-circle"></i> ' + data['error_warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    
                    $('.moka_message').fadeIn();
                }

                if (data['redirect']) {
                    window.location = data['redirect'];	
                }
            }
        });
    });
</script>