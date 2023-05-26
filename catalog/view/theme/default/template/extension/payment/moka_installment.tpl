<div class="moka-checkout-card" id="moka-checkout-installment">
    <div class="moka-checkout-card-header"><?php echo $text_installement_options; ?></div>
    <div class="moka-checkout-card-body">
        <div class="moka-checkout-installment-list">
            <?php foreach ($data->BankPaymentInstallmentInfoList[0]->PaymentInstallmentInfoList as $paymentInstallmentInfo) { ?>
                <?php if ($paymentInstallmentInfo->InstallmentNumber == 1 ) { ?>
                    <label for="moka-checkout-installment-<?php echo $paymentInstallmentInfo->InstallmentNumber; ?>" class="moka-checkout-installment-item">
                        <div class="moka-checkout-installment-text">
                            <div class="moka-checkout-installment-radio-item">
                                <input type="radio" name="moka-checkout-installment" class="moka-checkout-installment-radio-input" id="moka-checkout-installment-<?php echo $paymentInstallmentInfo->InstallmentNumber; ?>" value="<?php echo $paymentInstallmentInfo->InstallmentNumber ; ?>" checked>
                                <div class="moka-checkout-installment-check">
                                    <svg width="12" height="12" viewBox="0 0 8 8" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="white" fill-rule="nonzero" d="M1.829 6.825a.832.832 0 0 0 1.162.02l4.733-4.46a.833.833 0 1 0-1.142-1.211L2.44 5.076 1.434 4.06A.833.833 0 1 0 .25 5.232l1.578 1.593z"></path>
                                    </svg>
                                </div>
                            </div>
                            <span class="moka-checkout-installment-number"><?php echo $text_single_payment; ?></span>
                        </div>
                        <div class="moka-checkout-installment-total"><?php echo $paymentInstallmentInfo->AmountFormatted; ?></div>
                    </label>
                <?php } ?>
                <?php if ($paymentInstallmentInfo->InstallmentNumber > 1 ) { ?>
                    <label for="moka-checkout-installment-<?php echo $paymentInstallmentInfo->InstallmentNumber; ?>" class="moka-checkout-installment-item">
                        <div class="moka-checkout-installment-text">
                            <div class="moka-checkout-installment-radio-item">
                                <input type="radio" name="moka-checkout-installment" class="moka-checkout-installment-radio-input" id="moka-checkout-installment-<?php echo $paymentInstallmentInfo->InstallmentNumber; ?>" value="<?php echo $paymentInstallmentInfo->InstallmentNumber ; ?>">
                                <div class="moka-checkout-installment-check">
                                    <svg width="12" height="12" viewBox="0 0 8 8" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="white" fill-rule="nonzero" d="M1.829 6.825a.832.832 0 0 0 1.162.02l4.733-4.46a.833.833 0 1 0-1.142-1.211L2.44 5.076 1.434 4.06A.833.833 0 1 0 .25 5.232l1.578 1.593z"></path>
                                    </svg>
                                </div>
                            </div>
                            <span class="moka-checkout-installment-number"><?php echo $paymentInstallmentInfo->InstallmentNumber; ?></span>
                            <span class="moka-checkout-installment-cross">x</span>
                            <span class="moka-checkout-installment-price"><?php echo $paymentInstallmentInfo->PerInstallmentAmountFormatted; ?></span>
                        </div>
                        <div class="moka-checkout-installment-total"><?php echo $paymentInstallmentInfo->AmountFormatted; ?></div>
                    </label>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>