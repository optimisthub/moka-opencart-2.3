<style>.moka-checkout-input,.moka-checkout-input-container{border-width:initial!important;border-style:none!important;border-color:initial!important;border-image:initial!important}.moka-checkout-card-body,.moka-checkout-input{border-radius:6px!important;width:100%!important}.moka-checkout-card-body,.moka-checkout-input-container{box-shadow:0 0 0 1px #e0e0e0,0 2px 4px 0 rgba(0,0,0,.07),0 1px 1.5px 0 rgba(0,0,0,.05)!important}.moka-checkout{position:relative!important;width:100%!important;max-width:500px!important;min-width:320px!important;background-color:#fff!important;margin:0 auto!important;font-size:14px!important;padding:16px;font-family:-apple-system,blinkmacsystemfont,"segoe ui",helvetica,arial,sans-serif,"apple color emoji","segoe ui emoji","segoe ui symbol"!important;line-height:1.5!important;color:#495057!important;-webkit-font-smoothing:antialiased!important}.moka-checkout *{box-sizing:border-box!important}.moka-checkout-test-alert{text-align:center!important;margin-bottom:16px!important;background:#fce386!important;padding:10px!important;border-radius:6px!important}.moka-checkout-form{display:flex!important;flex-wrap:wrap!important}.moka-checkout-input-container{width:100%!important;position:relative!important;z-index:0!important;background:#fff!important}.moka-checkout-input-icon,.moka-checkout-input-icons{position:absolute!important;top:0!important;display:flex!important}.moka-checkout-input-container--half{width:50%!important}.moka-checkout-border-radius--top-left{border-top-left-radius:6px!important}.moka-checkout-border-radius--top-right{border-top-right-radius:6px!important}.moka-checkout-border-radius--bottom-left{border-bottom-left-radius:6px!important}.moka-checkout-border-radius--bottom-right{border-bottom-right-radius:6px!important}.moka-checkout-input-icon{left:0!important;right:auto!important;width:35px!important;height:100%!important;-webkit-box-pack:center!important;justify-content:center!important;-webkit-box-align:center!important;align-items:center!important}.moka-checkout-input{height:48px!important;color:#212529!important;font-size:14px!important;text-align:left!important;background:0 0!important;padding:0 12px 0 35px!important;outline:0!important}.moka-checkout-input-icons{pointer-events:none!important;right:0!important;height:100%!important;justify-content:flex-end!important;align-items:center!important;margin-right:8px!important}.moka-checkout-input-icons-icon{pointer-events:none!important;margin-left:8px!important}.moka-checkout-message-container{margin-top:8px!important}.moka-checkout-error-label{color:#fa5252!important;font-size:14px!important;line-height:18px!important;font-weight:400!important}.moka-checkout-card{background-color:#fff!important;margin-top:16px!important}.moka-checkout-card-header{margin:8px 0!important;line-height:1.5!important;font-size:14px!important;color:#495057!important}.moka-checkout-installment-content{padding:10px!important;color:#868e96!important;text-align:center!important;line-height:1.2!important;font-size:14px!important}.moka-checkout-installment-item{position:relative!important;display:flex!important;-webkit-box-align:center!important;align-items:center!important;cursor:pointer!important;padding:16px!important;width:100%!important;border-bottom:.5px solid #e0e0e0!important}.moka-checkout-installment-radio-item{margin-right:8px!important}.moka-checkout-installment-text{display:flex!important;-webkit-box-align:center!important;align-items:center!important;width:70%!important;font-size:14px!important}.moka-checkout-installment-cross{margin-left:8px!important;margin-right:8px!important}.moka-checkout-installment-total{text-align:right!important;width:30%!important;font-size:14px!important}.moka-checkout-installment-radio-input{position:absolute!important;top:0!important;left:0!important;opacity:0!important;padding:0!important;margin:0!important}.moka-checkout-installment-check{display:flex!important;-webkit-box-pack:center!important;justify-content:center!important;-webkit-box-align:center!important;align-items:center!important;width:24px!important;height:24px!important;border-radius:50%!important;border:2px solid #ced4da!important;border-image:initial!important}.moka-checkout-installment-radio-input:checked+.moka-checkout-installment-check{background:#37b24d!important;border-color:#37b24d!important}.moka-checkout-installment-label{width:70%!important;font-size:14px!important}.moka-checkout-button-container{margin-top:24px!important}.moka-checkout-button{background-position:center center!important;user-select:none!important;display:flex!important;-webkit-box-pack:center!important;justify-content:center!important;-webkit-box-align:center!important;align-items:center!important;cursor:pointer!important;width:100%!important;height:48px!important;line-height:48px!important;border-radius:6px!important;opacity:1!important;font-size:16px!important;font-weight:600!important;outline:0!important;padding:0!important;transition:.3s linear!important;border:1px #164194!important;background-color:#164194!important;color:#fff!important}.moka-checkout-button:hover{border:1px #0040d1!important;background-color:#0040d1!important}.moka-checkout-credit-cards-container{display:flex!important;margin-top:24px!important;flex-wrap:wrap!important;-webkit-box-pack:center!important;justify-content:center!important;-webkit-box-align:center!important;align-items:center!important}.moka-checkout-credit-card{height:16px!important;margin:8px!important}</style>
<div class="moka-checkout">
    <?php if ($config_moka_api_environment == 'test'): ?>
        <div class="moka-checkout-test-alert"><?php echo $text_test_alert; ?></div>
    <?php endif; ?>
    <div class="moka-checkout-form">
        <div class="moka-checkout-input-container moka-checkout-border-radius--top-left moka-checkout-border-radius--top-right">
            <div class="moka-checkout-input-icon">
                <svg width="16" height="16" viewBox="0 0 16 16">
                    <g fill="#AFAFAF" transform="translate(2.071 .25)">
                        <circle cx="6.415" cy="3.913" r="3.67"></circle>
                        <path d="M6.415 8.584a6.347 6.347 0 00-6.34 6.34c0 .184.15.334.334.334h12.013c.184 0 .333-.15.333-.334a6.347 6.347 0 00-6.34-6.34z"></path>
                    </g>
                </svg>
            </div>
            <input type="text" class="moka-checkout-input" id="moka-checkout-holder" placeholder="<?php echo $entry_card_holder_full_name; ?>">
        </div>
        <div class="moka-checkout-input-container">
            <div class="moka-checkout-input-icon">
                <svg width="16" height="16" viewBox="0 0 16 16">
                    <path fill="#AFAFAF" d="M.325 11.843c0 .737.598 1.335 1.335 1.335h12.68c.737 0 1.335-.598 1.335-1.335V6.17a.167.167 0 00-.167-.166H.492a.167.167 0 00-.167.166v5.673zm11.012-1.502h2.002a.5.5 0 010 1.002h-2.002a.5.5 0 110-1.002zM2.995 7.672h5.339a.5.5 0 010 1.001h-5.34a.5.5 0 110-1.001zm0 2.67h2.002a.5.5 0 010 1H2.995a.5.5 0 110-1zm12.68-6.507c0-.737-.598-1.335-1.335-1.335H1.66C.923 2.5.325 3.098.325 3.835v1c0 .093.075.168.167.168h15.016a.167.167 0 00.167-.167V3.835z"></path>
                </svg>
            </div>
            <input type="text" class="moka-checkout-input" id="moka-checkout-card-number" placeholder="<?php echo $entry_card_number; ?>">
            <div class="moka-checkout-input-icons">
                <img src="catalog/view/javascript/moka/svg/visa.svg" class="moka-checkout-input-icons-icon moka-checkout-visa-icon" alt="visa-logo">
                <img src="catalog/view/javascript/moka/svg/mastercard.svg" class="moka-checkout-input-icons-icon moka-checkout-mastercard-icon" alt="mastercard-logo">
                <img src="catalog/view/javascript/moka/svg/amex.svg" class="moka-checkout-input-icons-icon moka-checkout-amex-icon" alt="amex-logo">
                <img src="catalog/view/javascript/moka/svg/troy.svg" class="moka-checkout-input-icons-icon moka-checkout-troy-icon" alt="troy-logo">
            </div>
        </div>
        <div class="moka-checkout-input-container moka-checkout-input-container--half moka-checkout-border-radius--bottom-left">
            <div class="moka-checkout-input-icon">
                <svg width="16" height="16" viewBox="0 0 16 16">
                    <path fill="#AFAFAF" d="M11.552.25c.357 0 .646.29.646.646v1.13c0 .09.072.162.161.162h1.776c.714 0 1.292.578 1.292 1.291v10.98c0 .713-.578 1.291-1.292 1.291H1.865a1.292 1.292 0 01-1.292-1.292V3.48c0-.713.578-1.292 1.292-1.292h.968c.179 0 .323.145.323.323v1.454a.484.484 0 00.969 0V.896a.646.646 0 111.292 0v1.13c0 .09.072.162.161.162h4.037c.178 0 .322.144.322.322v1.454a.484.484 0 00.97 0V.896c0-.357.288-.646.645-.646zm2.26 5.813H2.188a.323.323 0 00-.323.322v7.75c0 .179.144.323.323.323h11.624a.323.323 0 00.323-.323v-7.75a.323.323 0 00-.322-.322zm-8.718 6.135a.646.646 0 110 1.292h-.646a.646.646 0 110-1.292zm3.229 0a.646.646 0 110 1.292h-.646a.646.646 0 110-1.292zm3.23 0a.646.646 0 110 1.292h-.647a.646.646 0 110-1.292zm-3.23-2.583a.646.646 0 010 1.291h-.646a.646.646 0 110-1.291zm3.23 0a.646.646 0 010 1.291h-.647a.646.646 0 110-1.291zm0-2.584a.646.646 0 010 1.292h-.645a.646.646 0 010-1.292z"></path>
                </svg>
            </div>
            <input type="text" class="moka-checkout-input" id="moka-checkout-expire-date" placeholder="<?php echo $entry_card_expire_date; ?>">
        </div>
        <div class="moka-checkout-input-container moka-checkout-input-container--half moka-checkout-border-radius--bottom-right">
            <div class="moka-checkout-input-icon">
                <svg width="16" height="16" viewBox="0 0 16 16">
                    <path fill="#AFAFAF" d="M12.934 6.333h-.493V4.5C12.44 2.015 10.453 0 8 0S3.56 2.015 3.56 4.5v1.833h-.494c-.727 0-1.316.597-1.316 1.334v7c0 .736.59 1.333 1.316 1.333h9.868c.727 0 1.316-.597 1.316-1.333v-7c0-.737-.59-1.334-1.316-1.334zm-6.25 4a1.329 1.329 0 0 1 1.084-1.318 1.313 1.313 0 0 1 1.468.854 1.342 1.342 0 0 1-.578 1.613V13a.662.662 0 0 1-.658.667.662.662 0 0 1-.658-.667v-1.518a1.333 1.333 0 0 1-.658-1.149zM5.204 4.5c0-1.565 1.252-2.833 2.796-2.833 1.544 0 2.796 1.268 2.796 2.833V6a.331.331 0 0 1-.329.333H5.533A.331.331 0 0 1 5.203 6V4.5z" fill-rule="nonzero"></path>
                </svg>
            </div>
            <input type="text" class="moka-checkout-input" id="moka-checkout-cvc" placeholder="<?php echo $entry_card_cvc; ?>">
            <div class="moka-checkout-input-icons">
                <svg width="24" height="24" viewBox="0 0 24 24">
                    <g fill="none" fill-rule="evenodd">
                        <path d="M0 0h24v24H0z"></path>
                        <g fill-rule="nonzero">
                            <path fill="#ADB5BD" d="M17.75 6.25a.25.25 0 0 1 .25.25v1.75a.251.251 0 0 1-.257.25H17.5a8.515 8.515 0 0 0-8.215 6.314.25.25 0 0 1-.241.186H2.5A2.5 2.5 0 0 1 0 12.5v-6a.25.25 0 0 1 .25-.25zM6.5 10.75h-3a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5zm9-8.75A2.5 2.5 0 0 1 18 4.5a.25.25 0 0 1-.25.25H.25A.25.25 0 0 1 0 4.5 2.5 2.5 0 0 1 2.5 2z"></path>
                            <path fill="#495057" d="M17.5 10.5a6.5 6.5 0 1 1 0 13 6.5 6.5 0 0 1 0-13zm3.16 3.918c-.842 0-1.401.329-1.79.774l.689.77a1.35 1.35 0 0 1 1.048-.485c.422 0 .73.179.73.514 0 .347-.348.53-.796.53h-.593v1.04h.585c.493 0 .9.186.9.544 0 .372-.36.547-.804.547a1.52 1.52 0 0 1-1.096-.46l-.685.77c.407.479 1.052.749 1.84.749 1.027 0 1.993-.486 1.993-1.508 0-.536-.318-.941-.859-1.157.474-.222.752-.62.752-1.153 0-.89-.789-1.475-1.915-1.475zm-6.204.011-2.36.902v1.164l1.16-.434v3.558h1.2v-5.19zm2.35-.01c-.993 0-1.57.51-1.893.897l.711.803.06-.063c.263-.273.559-.506.996-.506.392 0 .678.255.678.639 0 .277-.13.529-.382.77l-1.796 1.733v.927h3.54v-1.098h-1.833l.941-.927c.537-.53.778-.916.778-1.526 0-.993-.708-1.65-1.8-1.65z"></path>
                        </g>
                    </g>
                </svg>
            </div>
        </div>
    </div>
    <div class="moka-checkout-card" id="moka-checkout-installment-content">
        <div class="moka-checkout-card-body">
            <div class="moka-checkout-installment-content"><?php echo $text_installment_content; ?></div>
        </div>
    </div>
    <div class="moka-checkout-button-container">
        <button type="submit" class="moka-checkout-button" id="button-confirm"><?php echo $text_pay; ?></button>
    </div>
    <div class="moka-checkout-credit-cards-container">
        <div class="moka-checkout-credit-card">
            <img src="catalog/view/javascript/moka/svg/advantage.svg" alt="Advantage" class="moka-checkout-credit-card__logo">
        </div>
        <div class="moka-checkout-credit-card">
            <img src="catalog/view/javascript/moka/svg/axess.svg" alt="Axess" class="moka-checkout-credit-card__logo">
        </div>
        <div class="moka-checkout-credit-card">
            <img src="catalog/view/javascript/moka/svg/bankkart-combo.svg" alt="BankkartCombo" class="moka-checkout-credit-card__logo">
        </div>
        <div class="moka-checkout-credit-card">
            <img src="catalog/view/javascript/moka/svg/bonus.svg" alt="Bonus" class="moka-checkout-credit-card__logo">
        </div>
        <div class="moka-checkout-credit-card">
            <img src="catalog/view/javascript/moka/svg/cardfinans.svg" alt="Cardfinans" class="moka-checkout-credit-card__logo">
        </div>
        <div class="moka-checkout-credit-card">
            <img src="catalog/view/javascript/moka/svg/maximum.svg" alt="Maximum" class="moka-checkout-credit-card__logo">
        </div>
        <div class="moka-checkout-credit-card">
            <img src="catalog/view/javascript/moka/svg/paraf.svg" alt="Paraf" class="moka-checkout-credit-card__logo">
        </div>
        <div class="moka-checkout-credit-card">
            <img src="catalog/view/javascript/moka/svg/saglam-kart.svg" alt="SaglamKart" class="moka-checkout-credit-card__logo">
        </div>
        <div class="moka-checkout-credit-card">
            <img src="catalog/view/javascript/moka/svg/world.svg" alt="World" class="moka-checkout-credit-card__logo">
        </div>
    </div>
</div>
<script type="text/javascript">!function(){var e=document.getElementById("moka-checkout-card-number"),t=document.getElementById("moka-checkout-expire-date"),n=document.getElementById("moka-checkout-cvc"),a=document.getElementsByClassName("moka-checkout-visa-icon"),r=document.getElementsByClassName("moka-checkout-mastercard-icon"),l=document.getElementsByClassName("moka-checkout-amex-icon"),s=document.getElementsByClassName("moka-checkout-troy-icon"),i=[{name:"Visa",pattern:/^4/,icon:a},{name:"Mastercard",pattern:/^(51|52|53|54|55|58)/,icon:r},{name:"American Express",pattern:/^(34|37)/,icon:l},{name:"Troy",pattern:/^(?:9|65|36)/,icon:s},];e.addEventListener("input",function(e){(function e(t){for(var n=t.value.replace(/\D/g,"").substring(0,16),a="",r=0;r<n.length;r++)r>0&&r%4==0&&(a+=" "),a+=n.charAt(r);t.value=a})(e.target),function e(t){for(var n=t.value.replace(/\D/g,""),u=function e(t){for(var n=0;n<i.length;n++){var a=i[n];if(a.pattern.test(t))return a}return null}(n),c=0;c<a.length;c++)a[c].style.display="none";for(var c=0;c<r.length;c++)r[c].style.display="none";for(var c=0;c<l.length;c++)l[c].style.display="none";for(var c=0;c<s.length;c++)s[c].style.display="none";if(u)for(var c=0;c<u.icon.length;c++)u.icon[c].style.display="block";if(""===n)for(var c=0;c<i.length;c++)for(var g=i[c].icon,o=0;o<g.length;o++)g[o].style.display="block"}(e.target)}),t.addEventListener("input",function(e){var t,n,a,r;a=(n=(t=e.target).value.replace(/\D/g,"").substring(0,6)).substring(0,2),r=n.substring(2,6),n.length>2?t.value=a+"/"+r:t.value=n}),t.addEventListener("keyup",function(e){if("Backspace"===e.key||"Delete"===e.key){var t,n,a,r,l,s,i,u,c=e.target.value.replace(/\D/g,"");2===c.length||3===c.length?(a=(n=(t=e.target).value.replace(/\D/g,"").substring(0,6)).substring(0,2),r=n.substring(2,6),2===n.length?t.value=a:3===n.length?t.value=a+"/"+r.substring(0,1):n.length>3?t.value=a+"/"+r:t.value=n):4===c.length&&(i=(s=(l=e.target).value.replace(/\D/g,"").substring(0,6)).substring(0,2),u=s.substring(2,6),s.length>=2?l.value=i+"/"+u:l.value=s)}}),n.addEventListener("input",function(e){var t,n;n=(t=e.target).value.replace(/\D/g,"").substring(0,4),t.value=n})}(window);</script>
<script type="text/javascript">
    var previousCardNumber = "";

    $("#moka-checkout-card-number").on("input", function() {
        var cardNumber = $(this).val().replace(/\s/g, '');
        var firstSixDigits = cardNumber.substring(0, 6);

        if (firstSixDigits.length === 6) {
            if (firstSixDigits !== previousCardNumber) {
                previousCardNumber = firstSixDigits;

                $("#moka-checkout-installment-content").show();
                $("#moka-checkout-installment").remove();

                $.ajax({
                    url: '<?php echo $installment; ?>',
                    method: 'POST',
                    data: { bin_number: previousCardNumber.slice(0, 6) },
                    dataType: 'json',
                    success: function(data) {
                        $("#moka-checkout-installment-content").hide();
                        $("#moka-checkout-installment-content").after(data.html);
                    }
                });
            }
        } else {
            previousCardNumber = "";

            $("#moka-checkout-installment").remove();
            $("#moka-checkout-installment-content").show();
        }
    });

    $("#button-confirm").on("click", function() {
        var cardHolder = $("#moka-checkout-holder").val();
        var cardNumber = $("#moka-checkout-card-number").val().replace(/\s/g, '');
        var expireDate = $("#moka-checkout-expire-date").val();
        var cvc = $("#moka-checkout-cvc").val();
        var installment = $('input[name="moka-checkout-installment"]:checked').val();
        var month = '';
        var year = '';

        if (expireDate) {
            var splitExpireDate = expireDate.split('/');
            
            if (splitExpireDate.length === 2) {
                month = splitExpireDate[0].trim();
                year = splitExpireDate[1].trim();
                
                if (year.length === 2) {
                    var currentCentury = new Date().getFullYear().toString().slice(0, 2);
                    year = currentCentury + year;
                } else if (year.length === 1 || year.length === 3) {
                    year = '';
                }
            }
        }

        $.ajax({
            url: '<?php echo $checkout; ?>',
            method: 'POST',
            dataType: 'json',
            data: {
                card_holder_full_name: cardHolder,
                card_number: cardNumber,
                expiry_month: month,
                expiry_year: year,
                cvc_number: cvc,
                installment: installment,
            },
            beforeSend: function() {
                $(".moka-checkout-message-container").remove();
                $("#button-confirm").button("loading");
            },
            complete: function() {
                $("#button-confirm").button("reset");
            },
            success: function(data) {
                var errors = data.errors;
                var firstErrorKey = Object.keys(errors)[0];
                var firstErrorMessage = errors[firstErrorKey];

                if (firstErrorMessage) {
                    var errorMessageContainer = $('<div class="moka-checkout-message-container"></div>');
                    var errorLabel = $('<div class="moka-checkout-error-label">' + firstErrorMessage + '</div>');

                    $(".moka-checkout-form").append(errorMessageContainer.append(errorLabel));
                }

                if (data['redirect']) {
                    window.location = data['redirect'];	
                }
            }
        });
    });
</script>