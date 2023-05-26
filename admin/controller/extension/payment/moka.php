<?php

class ControllerExtensionPaymentMoka extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/moka');
        $this->load->model('setting/setting');
        $this->load->model('extension/payment/moka');
        $this->load->model('localisation/order_status');

        $this->document->setTitle($this->language->get('heading_title'));

        $data = array();

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/moka', 'token=' . $this->session->data['token'], true)
        );

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('moka', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');
        }

        $data['heading_title'] = $this->language->get('heading_title');

		$data['tab_setting'] = $this->language->get('tab_setting');
		$data['tab_transaction'] = $this->language->get('tab_transaction');

		$data['text_status'] = $this->language->get('text_status');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_extension'] = $this->language->get('text_extension');
		$data['text_extension_status_enabled'] = $this->language->get('text_extension_status_enabled');
		$data['text_extension_status_disabled'] = $this->language->get('text_extension_status_disabled');
		$data['text_success'] = $this->language->get('text_success');
		$data['text_live'] = $this->language->get('text_live');
		$data['text_sandbox'] = $this->language->get('text_sandbox');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_sort_order'] = $this->language->get('text_sort_order');
		$data['text_no_transactions'] = $this->language->get('text_no_transactions');
		
        $data['entry_api_environment'] = $this->language->get('entry_api_environment');
		$data['entry_dealer_code'] = $this->language->get('entry_dealer_code');
		$data['entry_username'] = $this->language->get('entry_username');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_cancel_order_status'] = $this->language->get('entry_cancel_order_status');
		
        $data['help_order_status'] = $this->language->get('help_order_status');
		$data['help_cancel_order_status'] = $this->language->get('help_cancel_order_status');
		
        $data['column_transaction_id'] = $this->language->get('column_transaction_id');
        $data['column_payment_id'] = $this->language->get('column_payment_id');
        $data['column_order_id'] = $this->language->get('column_order_id');
        $data['column_customer'] = $this->language->get('column_customer');
        $data['column_amount'] = $this->language->get('column_amount');
        $data['column_installment_number'] = $this->language->get('column_installment_number');
        $data['column_commission_amount'] = $this->language->get('column_commission_amount');
        $data['column_payment_status'] = $this->language->get('column_payment_status');
        $data['column_transaction_status'] = $this->language->get('column_transaction_status');
        $data['column_created_at'] = $this->language->get('column_created_at');

        $data['error_api_environment'] = '';
        $data['error_dealer_code'] = '';
        $data['error_username'] = '';
        $data['error_password'] = '';
        $data['error_order_status'] = '';
        $data['error_cancel_order_status'] = '';

        $data['moka_api_environment'] = $this->config->get('moka_api_environment');
        $data['moka_dealer_code'] = $this->config->get('moka_dealer_code');
        $data['moka_username'] = $this->config->get('moka_username');
        $data['moka_password'] = $this->config->get('moka_password');
        $data['moka_order_status'] = $this->config->get('moka_order_status');
        $data['moka_cancel_order_status'] = $this->config->get('moka_cancel_order_status');
        $data['moka_status'] = $this->config->get('moka_status');
        $data['moka_sort_order'] = $this->config->get('moka_sort_order');

        if (isset($this->error['error_api_environment'])) {
            $data['error_api_environment'] = $this->error['error_api_environment'];
        }

        if (isset($this->error['error_dealer_code'])) {
            $data['error_dealer_code'] = $this->error['error_dealer_code'];
        }

        if (isset($this->error['error_username'])) {
            $data['error_username'] = $this->error['error_username'];
        }

        if (isset($this->error['error_password'])) {
            $data['error_password'] = $this->error['error_password'];
        }

        if (isset($this->error['error_order_status'])) {
            $data['error_order_status'] = $this->error['error_order_status'];
        }

        if (isset($this->error['error_cancel_order_status'])) {
            $data['error_cancel_order_status'] = $this->error['error_cancel_order_status'];
        }

        if (isset($this->request->post['moka_api_environment'])) {
            $data['moka_api_environment'] = $this->request->post['moka_api_environment'];
        }

        if (isset($this->request->post['moka_dealer_code'])) {
            $data['moka_dealer_code'] = $this->request->post['moka_dealer_code'];
        }

        if (isset($this->request->post['moka_username'])) {
            $data['moka_username'] = $this->request->post['moka_username'];
        }

        if (isset($this->request->post['moka_password'])) {
            $data['moka_password'] = $this->request->post['moka_password'];
        }

        if (isset($this->request->post['moka_order_status'])) {
            $data['moka_order_status'] = $this->request->post['moka_order_status'];
        }

        if (isset($this->request->post['moka_cancel_order_status'])) {
            $data['moka_cancel_order_status'] = $this->request->post['moka_cancel_order_status'];
        }

        if (isset($this->request->post['moka_status'])) {
            $data['moka_status'] = $this->request->post['moka_status'];
        }

        if (isset($this->request->post['moka_sort_order'])) {
            $data['moka_sort_order'] = $this->request->post['moka_sort_order'];
        }

        if (isset($this->error['warning'])) {
            $this->pushAlert(array(
                'type' => 'danger',
                'icon' => 'exclamation-circle',
                'text' => $this->error['warning']
            ));
        }

        if (isset($this->session->data['success'])) {
            $this->pushAlert(array(
                'type' => 'success',
                'icon' => 'exclamation-circle',
                'text' => $this->session->data['success']
            ));

            unset($this->session->data['success']);
        }

        $data['alerts'] = $this->pullAlerts();

        $this->clearAlerts();

        $data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
        $data['action'] = $this->url->link('extension/payment/moka', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);
        $data['token'] = $this->session->data['token'];
        $data['url_list_transactions'] = html_entity_decode($this->url->link('extension/payment/moka/transactions', 'token=' . $this->session->data['token'] . '&page={PAGE}', true));

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/moka', $data));
    }

    public function transactions()
    {
        $this->load->language('extension/payment/moka');
        $this->load->model('extension/payment/moka');

        $page = 1;

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        }

        $result = array(
            'transactions' => array(),
            'pagination' => ''
        );

        $filter_data = array(
            'start' => ($page - 1) * (int)$this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        if (isset($this->request->get['order_id'])) {
            $filter_data['order_id'] = $this->request->get['order_id'];
        }

        $transactions_total = $this->model_extension_payment_moka->getTotalTransactions($filter_data);
        $transactions = $this->model_extension_payment_moka->getTransactions($filter_data);

        $this->load->model('sale/order');

        foreach ($transactions as $transaction) {
            $amount = $transaction['amount'] . ' ' . $transaction['currency_code'];
            $commission_amount = $transaction['commission_amount'] . ' ' . $transaction['currency_code'];

            $order_info = $this->model_sale_order->getOrder($transaction['order_id']);

            $customer = '-';

            if (isset($order_info)) {
                $customer = $order_info['firstname'] . ' ' . $order_info['lastname'];
            }

            $result['transactions'][] = array(
                'moka_transaction_id' => $transaction['moka_transaction_id'],
                'payment_id' => $transaction['payment_id'],
                'order_id' => $transaction['order_id'],
                'url_order' => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $transaction['order_id'], true),
                'customer' => $customer,
                'amount' => $amount,
                'installment_number' => $transaction['installment_number'],
                'commission_amount' => $commission_amount,
                'payment_status' => $transaction['payment_status'],
                'transaction_status' => $transaction['transaction_status'],
                'created_at' => date($this->language->get('datetime_format'), strtotime($transaction['created_at'])),
            );
        }

        $pagination = new Pagination();
        $pagination->total = $transactions_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = '{page}';

        $result['pagination'] = $pagination->render();

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));
    }

    public function install()
    {
        $this->load->model('extension/payment/moka');

        $this->model_extension_payment_moka->createTables();
    }

    public function uninstall()
    {
        $this->load->model('extension/payment/moka');

        $this->model_extension_payment_moka->dropTables();
    }

    protected function pushAlert($alert)
    {
        $this->session->data['moka_alerts'][] = $alert;
    }

    protected function pullAlerts()
    {
        if (isset($this->session->data['moka_alerts'])) {
            return $this->session->data['moka_alerts'];
        }

        return array();
    }

    protected function clearAlerts()
    {
        unset($this->session->data['moka_alerts']);
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/moka')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['moka_api_environment']) {
            $this->error['error_api_environment'] = $this->language->get('error_api_environment');
        }

        if (!$this->request->post['moka_dealer_code']) {
            $this->error['error_dealer_code'] = $this->language->get('error_dealer_code');
        }

        if (!$this->request->post['moka_username']) {
            $this->error['error_username'] = $this->language->get('error_username');
        }

        if (!$this->request->post['moka_password']) {
            $this->error['error_password'] = $this->language->get('error_password');
        }

        if (!$this->request->post['moka_order_status']) {
            $this->error['error_order_status'] = $this->language->get('error_order_status');
        }

        if (!$this->request->post['moka_cancel_order_status']) {
            $this->error['error_cancel_order_status'] = $this->language->get('error_cancel_order_status');
        }

        return !$this->error;
    }
}
