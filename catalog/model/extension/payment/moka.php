<?php

class ModelExtensionPaymentMoka extends Model
{
    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/moka');

        $payment_moka_geo_zone_id   = $this->config->get('payment_moka_geo_zone_id');
        $payment_moka_geo_zone_id   = $this->db->escape($payment_moka_geo_zone_id);
        $address_country_id         = $this->db->escape($address['country_id']);
        $address_zone_id             = $this->db->escape($address['zone_id']);

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . $payment_moka_geo_zone_id . "' AND `country_id` = '" . $address_country_id . "' AND (`zone_id` = '" . $address_zone_id . "' OR `zone_id` = '0')");

        if ($this->config->get('payment_moka_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('payment_moka_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code'       => 'moka',
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' => $this->config->get('payment_moka_sort_order')
            );
        }

        return $method_data;
    }

    public function addTransaciton($transaciton, $order_id)
    {
        $addTransaciton = $this->db->query("INSERT INTO `" . DB_PREFIX . "moka_transaction` SET 
			`payment_id` = '" . $this->db->escape($transaciton->DealerPaymentId) . "',
			`order_id` = '" . $this->db->escape($order_id) . "', 
			`amount` = '" . $this->db->escape($transaciton->Amount) . "',
			`currency_code` = '" . $this->db->escape($transaciton->CurrencyCode) . "',
			`installment_number` = '" . $this->db->escape($transaciton->InstallmentNumber) . "',
			`commission_amount` = '" . $this->db->escape($transaciton->DealerCommissionAmount) . "',
			`payment_status` = '" . $this->db->escape($transaciton->PaymentStatus) . "',
			`transaction_status` = '" . $this->db->escape($transaciton->TrxStatus) . "'");

        return $addTransaciton;
    }

    public function getMonths()
    {
        $months = array();

        for ($i = 1; $i <= 12; $i++) {
            $months[] = array(
                'text'  => sprintf('%02d', $i),
                'value' => sprintf('%02d', $i)
            );
        }

        return $months;
    }

    public function getYears()
    {
        $years = array();

        $today = getdate();

        for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
            $years[] = array(
                'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
                'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
            );
        }

        return $years;
    }
}
