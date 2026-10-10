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
            $clientInfo = \Moka\ClientInfo::resolve();
            $clientIp = $clientInfo['ip'];

            // Prefer the public address from the live connection, but fall back
            // to the stored order IP when the request itself carries only a
            // private address (for example an unconfigured reverse proxy).
            if (!\Moka\ClientInfo::isPublicIp($clientIp) && \Moka\ClientInfo::isPublicIp($order_info['ip'])) {
                $clientIp = $order_info['ip'];
            }

            $request->setClientIp($clientIp);
            $request->setClientPort($clientInfo['port']);
            // Moka OtherTrxCode'u bayi bazinda KALICI olarak benzersiz ister; siparis
            // kimligi tekrar kullanilabilir bir deger oldugu icin ayni siparisle
            // ikinci deneme reddedilir veya baska bir islemin kaydi eslesir.
            $moka_trx_code = $order_id . '-' . date('YmdHis') . '-' . mt_rand(1000, 9999);

            $request->setOtherTrxCode($moka_trx_code);
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
                // Doğrulama kodu siparişe bağlı ve tek kullanımlık olarak saklanır;
                // böylece aynı hash başka bir sipariş için tekrar kullanılamaz.
                $this->load->model('extension/payment/moka');
                $this->model_extension_payment_moka->savePendingPayment($order_id, $moka_trx_code, $payment_data->CodeForHash, $request->getAmount(), $request->getCurrency());

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

        $hashValue = (isset($this->request->post['hashValue']) && is_string($this->request->post['hashValue'])) ? $this->request->post['hashValue'] : '';

        // Doğrulama kaydı, ödeme başlatılırken Moka'ya gönderilen benzersiz işlem
        // koduyla bulunur. Oturum çerezine bağımlı değildir; bu yüzden banka
        // dönüşündeki çapraz site POST'ta çerez gelmese bile çalışır.
        $posted_trx_code = (isset($this->request->post['OtherTrxCode']) && is_scalar($this->request->post['OtherTrxCode'])) ? (string) $this->request->post['OtherTrxCode'] : '';
        $pending = null;
        $order_id = 0;

        if ($posted_trx_code !== '') {
            $pending = $this->model_extension_payment_moka->getPendingPaymentByTrxCode($posted_trx_code);
        }

        if (is_array($pending)) {
            $order_id = (int) $pending['order_id'];
        } elseif ($posted_trx_code !== '' && ctype_digit($posted_trx_code)) {
            // Geriye dönük uyumluluk: eski sürümlerde OtherTrxCode sipariş kimliğiydi.
            $order_id = (int) $posted_trx_code;
            $pending = $this->model_extension_payment_moka->getPendingPayment($order_id);
        }

        if ($order_id < 1 && isset($this->session->data['order_id'])) {
            $order_id = (int) $this->session->data['order_id'];

            if (!is_array($pending)) {
                $pending = $this->model_extension_payment_moka->getPendingPayment($order_id);
            }
        }

        if ($hashValue === '' || $order_id < 1) {
            return $this->paymentFailed();
        }
        $stored_code = (is_array($pending) && isset($pending['code_for_hash']) && is_string($pending['code_for_hash'])) ? $pending['code_for_hash'] : '';

        // Kayıtlı doğrulama kodu yoksa hiçbir hash kabul edilmez; aksi halde
        // beklenen değer herkesin hesaplayabildiği sha256("T") sabitine düşer.
        if ($stored_code === '') {
            $order_info = $this->model_checkout_order->getOrder($order_id);

            // Aynı callback tekrar geldiyse ve sipariş zaten tamamlandıysa başarı sayfasına dön.
            if ($order_info && (int) $order_info['order_status_id'] === (int) $this->config->get('moka_order_status')) {
                return $this->response->redirect($this->url->link('checkout/success'));
            }

            return $this->paymentFailed();
        }

        $expected = hash('sha256', $stored_code . 'T');

        // Hash eşleşse bile ödeme Moka tarafında doğrulanamazsa sipariş ödendi sayılmaz.
        if (!$this->hashEquals($expected, $hashValue) || !$this->verifyPayment($order_id, $pending)) {
            return $this->paymentFailed();
        }

        // Kod tek kullanımlıktır: aynı hash ile tekrar oynatılamaz.
        $this->model_extension_payment_moka->deletePendingPayment($order_id);

        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('moka_order_status'));

        return $this->response->redirect($this->url->link('checkout/success'));
    }

    /**
     * Ödemeyi Moka tarafında sunucu-sunucuya doğrular.
     *
     * Hash doğru olsa bile callback istemciden gelir; bu yüzden işlemin Moka'da
     * gerçekten başarılı olduğu ve tutarın ödeme başlatılırken gönderilen
     * tutarla eşleştiği doğrulanmadan sipariş ödendi olarak işaretlenmez.
     */
    private function verifyPayment($order_id, array $pending)
    {
        $options = array(
            'dealerCode' => $this->config->get('moka_dealer_code'),
            'username'   => $this->config->get('moka_username'),
            'password'   => $this->config->get('moka_password'),
        );

        if ($this->config->get('moka_api_environment') == 'test') {
            $options['baseUrl'] = 'https://service.refmoka.com';
        }

        $moka = new \Moka\MokaClient($options);

        $paymentDetailRequest = new \Moka\Model\RetrievePaymentDetailRequest();
        // Moka sorgusu, ödeme başlatılırken gönderilen benzersiz kodla yapılmalı.
        $query_trx_code = (is_array($pending) && !empty($pending['trx_code'])) ? (string) $pending['trx_code'] : (string) $order_id;

        $paymentDetailRequest->setOtherTrxCode($query_trx_code);

        $paymentDetail = $moka->payments()->retrieve($paymentDetailRequest);

        if ($paymentDetail->getResultCode() != 'Success') {
            return false;
        }

        $paymentDetail_data = $paymentDetail->getData();

        if (!isset($paymentDetail_data->PaymentDetail)) {
            return false;
        }

        $detail = $paymentDetail_data->PaymentDetail;

        // Moka durum tablosu: PaymentStatus 2 (Ödeme) + TrxStatus 1 (Başarılı)
        // birlikte "ödeme başarılı" demektir. TrxStatus tek başına 1; iptal
        // (PaymentStatus 3) ve ön provizyon (1) için de dönebilir.
        $payment_status = (isset($detail->PaymentStatus) && is_numeric($detail->PaymentStatus)) ? (int) $detail->PaymentStatus : 0;
        $trx_status = (isset($detail->TrxStatus) && is_numeric($detail->TrxStatus)) ? (int) $detail->TrxStatus : 0;

        if ($payment_status !== 2 || $trx_status !== 1) {
            return false;
        }

        // Tutar, ödeme başlatılırken Moka'ya gönderilen tutarla eşleşmeli.
        $moka_amount = (isset($detail->Amount) && is_numeric($detail->Amount)) ? (float) $detail->Amount : 0.0;
        $expected_amount = isset($pending['amount']) ? (float) $pending['amount'] : 0.0;

        if (abs($moka_amount - $expected_amount) >= 0.01) {
            return false;
        }

        // Para birimi: TRY ve TL eşdeğerdir, alan boşsa bu adım atlanır.
        $moka_currency = isset($detail->CurrencyCode) ? strtoupper((string) $detail->CurrencyCode) : '';
        $expected_currency = isset($pending['currency']) ? strtoupper((string) $pending['currency']) : '';

        if ($moka_currency === 'TRY') {
            $moka_currency = 'TL';
        }

        if ($expected_currency === 'TRY') {
            $expected_currency = 'TL';
        }

        if ($moka_currency !== '' && $expected_currency !== '' && $moka_currency !== $expected_currency) {
            return false;
        }

        $this->model_extension_payment_moka->addTransaciton($detail, $order_id);

        return true;
    }

    /**
     * Sabit zamanlı hash karşılaştırması.
     */
    private function hashEquals($known, $given)
    {
        if (function_exists('hash_equals')) {
            return hash_equals($known, $given);
        }

        // PHP 5.6 öncesi için sabit zamanlı yedek.
        if (!is_string($known) || !is_string($given) || strlen($known) !== strlen($given)) {
            return false;
        }

        $diff = 0;

        for ($i = 0; $i < strlen($known); $i++) {
            $diff |= ord($known[$i]) ^ ord($given[$i]);
        }

        return $diff === 0;
    }

    /**
     * Doğrulanamayan callback için başarısızlık yönlendirmesi.
     */
    private function paymentFailed()
    {
        return $this->response->redirect($this->url->link('checkout/failure'));
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
