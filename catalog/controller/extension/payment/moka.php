<?php

class ControllerExtensionPaymentMoka extends Controller
{
    private $errors = array();

    public function index()
    {
        $this->load->language('extension/payment/moka');
        $this->load->model('extension/payment/moka');

        $data = array();

        $data['config_moka_api_environment'] = $this->config->get('moka_api_environment');
        $data['checkout'] = html_entity_decode($this->url->link('extension/payment/moka/checkout', '', true), ENT_COMPAT, 'UTF-8');
        $data['installment'] = html_entity_decode($this->url->link('extension/payment/moka/installment', '', true), ENT_COMPAT, 'UTF-8');

        $data['payment_failed'] = $this->language->get('payment_failed');
        $data['text_title'] = $this->language->get('text_title');
        $data['text_test_alert'] = $this->language->get('text_test_alert');
        $data['text_installment_content'] = $this->language->get('text_installment_content');
        $data['text_installement_options'] = $this->language->get('text_installement_options');
        $data['text_pay'] = $this->language->get('text_pay');
        $data['text_single_payment'] = $this->language->get('text_single_payment');
        $data['entry_card_holder_full_name'] = $this->language->get('entry_card_holder_full_name');
        $data['entry_card_number'] = $this->language->get('entry_card_number');
        $data['entry_card_expire_date'] = $this->language->get('entry_card_expire_date');
        $data['entry_card_cvc'] = $this->language->get('entry_card_cvc');

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
            
            $card_holder_full_name = $this->request->post['card_holder_full_name'];
            $card_number = $this->request->post['card_number'];
            $expiry_month = $this->request->post['expiry_month'];
            $expiry_year = $this->request->post['expiry_year'];
            $cvc_number = $this->request->post['cvc_number'];
            $installment = $this->request->post['installment'];
            $currency = $order_info['currency_code'] == 'TRY' ? 'TL' : $order_info['currency_code'];
            $order_amount = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
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
            $request->setExpMonth($expiry_month);
            $request->setExpYear($expiry_year);
            $request->setCvcNumber($cvc_number);

            $request->setAmount($order_amount);
            $request->setCurrency($currency);
            $request->setInstallmentNumber($installment);
            $request->setClientIp($order_info['ip']);
            $request->setOtherTrxCode($order_id);
            $request->setSoftware('OPENCART');
            $request->setReturnHash(1);
            $request->setRedirectUrl($callback_url);

            // Installment
            $retrieveInstallmentInfoRequest = new Moka\Model\RetrieveInstallmentInfoRequest();
            $retrieveInstallmentInfoRequest->setBinNumber(substr($card_number, 0, 6));
            $retrieveInstallmentInfoRequest->setCurrency($currency);
            $retrieveInstallmentInfoRequest->setOrderAmount($order_amount);
            $retrieveInstallmentInfoRequest->setIsThreeD(1);
            
            $retrieveInstallmentInfo = $moka->payments()->retrieveInstallmentInfo($retrieveInstallmentInfoRequest);   

            $retrieveInstallmentInfoData = (object) [
                'BankPaymentInstallmentInfoList' => [
                    (object) [
                        'BankInfoName' => '',
                        'PaymentInstallmentInfoList' => [
                            (object) [
                                'CommissionType' => '',
                                'InstallmentNumber' => 1,
                                'DealerCommissionRate' => 0,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount' => 0,
                                'PerInstallmentAmount' => $order_amount,
                                'Amount' => $order_amount
                            ]
                        ]
                    ]
                ]
            ];

            if ($retrieveInstallmentInfo->getResultCode() == 'Success') {
                $retrieveInstallmentInfoData = $retrieveInstallmentInfo->getData();
            }

            foreach ($retrieveInstallmentInfoData->BankPaymentInstallmentInfoList as $BankPaymentInstallmentInfo) {
                foreach ($BankPaymentInstallmentInfo->PaymentInstallmentInfoList as $paymentInstallmentInfo) {
                    if ($paymentInstallmentInfo->InstallmentNumber == $installment) {
                        $request->setAmount($paymentInstallmentInfo->Amount);
                    }
                }
            }

            // Payment
            $payment = $moka->payments()->createThreeds($request);

            $payment_result_code = $payment->getResultCode();
            $payment_data = $payment->getData();

            if ($payment_result_code == 'Success') {
                $this->session->data['codeForHash'] = $payment_data->CodeForHash;

                $data['redirect'] = $payment_data->Url;
            }

            if ($payment_result_code !== 'Success') {
                $this->errors[$payment_result_code] = $this->language->get($payment_result_code);
            }
        }

        $data['errors'] = $this->errors;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }

    public function installment()
    {
        $this->load->model('checkout/order');
        $this->load->model('setting/setting');
        $this->load->language('extension/payment/moka');
        $this->load->library('moka');

        $data = array();  

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateInstallment()) { 
            $order_id = $this->session->data['order_id'];
            $order_info = $this->model_checkout_order->getOrder($order_id);
    
            $currency = $order_info['currency_code'] == 'TRY' ? 'TL' : $order_info['currency_code'];
            $order_amount = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
    
            $options = [
                'dealerCode' => $this->config->get('moka_dealer_code'),
                'username' => $this->config->get('moka_username'),
                'password' => $this->config->get('moka_password'),
            ];
    
            if ($this->config->get('moka_api_environment') == 'test') {
                $options['baseUrl'] = 'https://service.refmoka.com';
            }
    
            $moka = new \Moka\MokaClient($options);
    
            $retrieveInstallmentInfoRequest = new Moka\Model\RetrieveInstallmentInfoRequest();
            $retrieveInstallmentInfoRequest->setBinNumber($this->request->post['bin_number']);
            $retrieveInstallmentInfoRequest->setCurrency($currency);
            $retrieveInstallmentInfoRequest->setOrderAmount($order_amount);
            $retrieveInstallmentInfoRequest->setIsThreeD(1);
            
            $retrieveInstallmentInfo = $moka->payments()->retrieveInstallmentInfo($retrieveInstallmentInfoRequest);   
    
            $retrieveInstallmentInfoData = (object) [
                'BankPaymentInstallmentInfoList' => [
                    (object) [
                        'BankInfoName' => '',
                        'PaymentInstallmentInfoList' => [
                            (object) [
                                'CommissionType' => '',
                                'InstallmentNumber' => 1,
                                'DealerCommissionRate' => 0,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount' => 0,
                                'PerInstallmentAmount' => $order_amount,
                                'Amount' => $order_amount
                            ]
                        ]
                    ]
                ]
            ];
    
            if ($retrieveInstallmentInfo->getResultCode() == 'Success') {
                $retrieveInstallmentInfoData = $retrieveInstallmentInfo->getData();
            }
    
            foreach ($retrieveInstallmentInfoData->BankPaymentInstallmentInfoList as $bankPaymentInstallmentInfo) {
                foreach ($bankPaymentInstallmentInfo->PaymentInstallmentInfoList as $installment) {
                    $installment->AmountFormatted = $this->currency->format($installment->Amount, $order_info['currency_code'], true, true);
                    $installment->PerInstallmentAmountFormatted = $this->currency->format($installment->PerInstallmentAmount, $order_info['currency_code'], true, true);
                }
            }
    
            $installmentDetails = array(
                'text_single_payment' => $this->language->get('text_single_payment'),
                'text_installement_options' => $this->language->get('text_installement_options'),
                'data' => $retrieveInstallmentInfoData,
                'status' => $retrieveInstallmentInfo->getResultCode(),
                'message' => $retrieveInstallmentInfo->getResultMessage(),
            );
    
            $data['html'] = $this->load->view('extension/payment/moka_installment', $installmentDetails);
            $data['status'] = $retrieveInstallmentInfo->getResultCode();
            $data['message'] = $retrieveInstallmentInfo->getResultMessage();
        }

        $data['errors'] = $this->errors;

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

        function checkExpiryDate($month, $year) {
            $currentYear = date('Y');
            $currentMonth = date('m');
            
            if ($year > $currentYear || ($year == $currentYear && $month >= $currentMonth)) {
                return true;
            }
            
            return false;
        }

        if (!isset($this->session->data['order_id'])) {
            $this->errors['order'] = $this->language->get('error_order_expired');
        }

        if (!isset($this->request->post['card_holder_full_name']) || empty(trim($this->request->post['card_holder_full_name'])) || utf8_strlen($this->request->post['card_holder_full_name']) > 80) {
            $this->errors['card_holder_full_name'] = $this->language->get('error_card_holder_full_name');
        }
        
        if (!isset($this->request->post['card_number']) || empty(trim($this->request->post['card_number'])) || !preg_match('/^\d{13,19}$/', $this->request->post['card_number'])) {
            $this->errors['card_number'] = $this->language->get('error_card_number');
        }
        
        if (!isset($this->request->post['expiry_month']) || !isset($this->request->post['expiry_year']) || empty(trim($this->request->post['expiry_month'])) || empty(trim($this->request->post['expiry_year'])) || !checkExpiryDate($this->request->post['expiry_month'], $this->request->post['expiry_year'])) {
            $this->errors['expiry_month'] = $this->language->get('error_expiry_date');
        }
        
        if (!isset($this->request->post['cvc_number']) || empty(trim($this->request->post['cvc_number'])) || !preg_match('/^\d{3,4}$/', $this->request->post['cvc_number'])) {
            $this->errors['cvc_number'] = $this->language->get('error_cvc_number');
        }
        
        if (!isset($this->request->post['installment']) || empty(trim($this->request->post['installment'])) || !is_numeric($this->request->post['installment']) || $this->request->post['installment'] < 1) {
            $this->errors['installment'] = $this->language->get('error_installment');
        }

        return !$this->errors;
    }
    
    private function validateInstallment()
    {
        $this->load->language('extension/payment/moka');

        if (!isset($this->session->data['order_id'])) {
            $this->errors['order'] = $this->language->get('error_order_expired');
        }

        if (!isset($this->request->post['bin_number']) || utf8_strlen($this->request->post['bin_number']) < 6) {
            $this->errors['bin_number'] = 'BIN number';
        }

        return !$this->errors;
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
