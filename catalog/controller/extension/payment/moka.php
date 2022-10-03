<?php

class ControllerExtensionPaymentMoka extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/moka');
        $this->load->model('extension/payment/moka');

        $data = array();

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_title'] = $this->language->get('text_title');
        $data['text_loading'] = $this->language->get('text_loading');
        $data['payment_field_desc'] = $this->language->get('payment_field_desc');
        $data['installement_field_desc'] = $this->language->get('installement_field_desc');

        $data['entry_card_holder_full_name'] = $this->language->get('entry_card_holder_full_name');
        $data['entry_card_number'] = $this->language->get('entry_card_number');
        $data['entry_card_expiry_month'] = $this->language->get('entry_card_expiry_month');
        $data['entry_card_expiry_year'] = $this->language->get('entry_card_expiry_year');
        $data['entry_card_cvc_number'] = $this->language->get('entry_card_cvc_number');

        $data['error_card_holder_full_name'] = $this->language->get('error_card_holder_full_name');
        $data['error_card_number'] = $this->language->get('error_card_number');
        $data['error_card_cvc_number'] = $this->language->get('error_card_cvc_number');

        $data['PaymentDealer.CheckPaymentDealerAuthentication.InvalidRequest'] = $this->language->get('PaymentDealer.CheckPaymentDealerAuthentication.InvalidRequest');
        $data['PaymentDealer.CheckPaymentDealerAuthentication.InvalidAccount'] = $this->language->get('PaymentDealer.CheckPaymentDealerAuthentication.InvalidAccount');
        $data['PaymentDealer.CheckPaymentDealerAuthentication.VirtualPosNotFound'] = $this->language->get('PaymentDealer.CheckPaymentDealerAuthentication.VirtualPosNotFound');
        $data['PaymentDealer.CheckDealerPaymentLimits.DailyDealerLimitExceeded'] = $this->language->get('PaymentDealer.CheckDealerPaymentLimits.DailyDealerLimitExceeded');
        $data['PaymentDealer.CheckDealerPaymentLimits.DailyCardLimitExceeded'] = $this->language->get('PaymentDealer.CheckDealerPaymentLimits.DailyCardLimitExceeded');
        $data['PaymentDealer.CheckCardInfo.InvalidCardInfo'] = $this->language->get('PaymentDealer.CheckCardInfo.InvalidCardInfo');
        $data['PaymentDealer.DoDirectPayment3dRequest.InstallmentNotAvailableForForeignCurrencyTransaction'] = $this->language->get('PaymentDealer.DoDirectPayment3dRequest.InstallmentNotAvailableForForeignCurrencyTransaction');
        $data['PaymentDealer.DoDirectPayment3dRequest.ThisInstallmentNumberNotAvailableForDealer'] = $this->language->get('PaymentDealer.DoDirectPayment3dRequest.ThisInstallmentNumberNotAvailableForDealer');
        $data['PaymentDealer.DoDirectPayment3dRequest.InvalidInstallmentNumber'] = $this->language->get('PaymentDealer.DoDirectPayment3dRequest.InvalidInstallmentNumber');
        $data['PaymentDealer.DoDirectPayment3dRequest.ThisInstallmentNumberNotAvailableForVirtualPos'] = $this->language->get('PaymentDealer.DoDirectPayment3dRequest.ThisInstallmentNumberNotAvailableForVirtualPos');
        $data['EX'] = $this->language->get('EX');

        $data['months'] = $this->model_extension_payment_moka->getMonths();
        $data['years'] = $this->model_extension_payment_moka->getYears();

        $data['button_confirm'] = $this->language->get('button_confirm');

        $data['checkout'] = html_entity_decode($this->url->link('extension/payment/moka/checkout', '', true), ENT_COMPAT, 'UTF-8');

        return $this->load->view('extension/payment/moka', $data);
    }

    public function checkout()
    {
        $this->load->model('checkout/order');
        $this->load->model('setting/setting');
        $this->load->library('moka');

        $cookies = array('PHPSESSID', 'OCSESSID', 'default');

        foreach ($cookies as $cookie) {
            if (isset($_COOKIE[$cookie])) {
                $this->setcookieSameSite($cookie, $_COOKIE[$cookie], time() + 86400, "/", $_SERVER['SERVER_NAME'], true, true);
            }
        }

        $data = array();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $order_id = $this->session->data['order_id'];
            $order_info = $this->model_checkout_order->getOrder($order_id);

            $total_formatted = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);

            $card_holder_full_name = $this->request->post['card_holder_full_name'];
            $card_number = str_replace(' ', '', $this->request->post['card_number']);
            $exp_month = $this->request->post['card_expiry_month'];
            $exp_year = $this->request->post['card_expiry_year'];
            $cvc_number = $this->request->post['card_cvc_number'];
            $currency = $order_info['currency_code'] == 'TRY' ? 'TL' : $order_info['currency_code'];
            $callback_url = html_entity_decode($this->url->link('extension/payment/moka/callback', '', true), ENT_COMPAT, "UTF-8");

            $options = [
                'dealerCode' => $this->config->get('moka_dealer_code'),
                'username' => $this->config->get('moka_username'),
                'password' => $this->config->get('moka_password'),
            ];

            if ($this->config->get('moka_api_environment') == 'test') {
                $options['baseUrl'] = 'https://service.refmoka.com';
            }

            $moka = new \Moka\MokaClient($options);

            $request = new Moka\Model\CreatePaymentRequest();

            $request->setCardHolderFullName($card_holder_full_name);
            $request->setCardNumber($card_number);
            $request->setExpMonth($exp_month);
            $request->setExpYear($exp_year);
            $request->setCvcNumber($cvc_number);

            $request->setAmount($total_formatted);
            $request->setCurrency($currency);
            $request->setClientIp($order_info['ip']);
            $request->setOtherTrxCode($order_id);
            $request->setSoftware('OPENCART');
            $request->setReturnHash(1);
            $request->setRedirectUrl($callback_url);

            $payment = $moka->payments()->createThreeds($request);

            $payment_result_code = $payment->getResultCode();
            $payment_data = $payment->getData();

            if ($payment_result_code == 'Success') {
                if (isset($payment_data->Url)) {
                    $data['redirect'] = $payment_data->Url;
                }
                if (isset($payment_data->CodeForHash)) {
                    $this->session->data['codeForHash'] = $payment_data->CodeForHash;
                }
            }

            if ($payment_result_code !== 'Success') {
                $data['error_warning'] = $this->language->get($payment_result_code);
            }
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }

    public function callback()
    {
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/moka');
        $this->load->library('moka');

        if (isset($this->request->post['hashValue'])) {
            $code_for_hash = hash('sha256', $this->session->data['codeForHash'] . 'T');
            $hashValue = $this->request->post['hashValue'];
            $order_id = $this->session->data['order_id'];

            if ($code_for_hash == $hashValue) {
                $options = [
                    'dealerCode' => $this->config->get('moka_dealer_code'),
                    'username' => $this->config->get('moka_username'),
                    'password' => $this->config->get('moka_password'),
                ];

                if ($this->config->get('moka_api_environment') == 'test') {
                    $options['baseUrl'] = 'https://service.refmoka.com';
                }

                $moka = new \Moka\MokaClient($options);

                $paymentDetailRequest = new \Moka\Model\RetrievePaymentDetailRequest();
                $paymentDetailRequest->setOtherTrxCode($order_id);

                $paymentDetail = $moka->payments()->retrieve($paymentDetailRequest);

                $paymentDetail_result_code = $paymentDetail->getResultCode();
                $paymentDetail_data = $paymentDetail->getData();

                if ($paymentDetail_result_code == 'Success') {
                    $this->model_extension_payment_moka->addTransaciton($paymentDetail_data->PaymentDetail, $order_id);
                }

                $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('moka_order_status'));

                $this->response->redirect($this->url->link('checkout/success'));
            } else {
                $this->response->redirect($this->url->link('checkout/failure'));
            }
        }
    }

    private function validate()
    {
        $this->load->language('extension/payment/moka');
        $this->load->model('extension/payment/moka');

        if (!isset($this->request->post['card_holder_full_name']) || utf8_strlen($this->request->post['card_holder_full_name']) < 1 || utf8_strlen($this->request->post['card_holder_full_name']) > 80) {
            $this->error['warning'] = $this->language->get('error_card_holder_full_name');
        }

        if (!isset($this->request->post['card_number']) || utf8_strlen($this->request->post['card_number']) < 1 || utf8_strlen($this->request->post['card_number']) > 19) {
            $this->error['warning'] = $this->language->get('error_card_number');
        }

        if (!isset($this->request->post['card_cvc_number']) || utf8_strlen($this->request->post['card_cvc_number']) < 1 || utf8_strlen($this->request->post['card_cvc_number']) > 4) {
            $this->error['warning'] = $this->language->get('error_card_cvc_number');
        }

        return !$this->error;
    }   

    private function setCookieSameSite($name, $value, $expire, $path, $domain, $secure, $httponly)
    {
        if (PHP_VERSION_ID < 70300) {
            setcookie($name, $value, $expire, "$path; samesite=None", $domain, $secure, $httponly);
        } else {
            setcookie($name, $value, [
                'expires' => $expire,
                'path' => $path,
                'domain' => $domain,
                'samesite' => 'None',
                'secure' => $secure,
                'httponly' => $httponly
            ]);
        }
    }
}
