<?php

class ModelExtensionPaymentMoka extends Model
{
    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/moka');

        $moka_geo_zone_id   = $this->config->get('moka_geo_zone_id');
        $moka_geo_zone_id   = $this->db->escape($moka_geo_zone_id);
        $address_country_id = $this->db->escape($address['country_id']);
        $address_zone_id    = $this->db->escape($address['zone_id']);

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . $moka_geo_zone_id . "' AND `country_id` = '" . $address_country_id . "' AND (`zone_id` = '" . $address_zone_id . "' OR `zone_id` = '0')");

        if ($this->config->get('moka_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('moka_geo_zone_id')) {
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
                'sort_order' => $this->config->get('moka_sort_order')
            );
        }

        return $method_data;
    }

    public function addTransaciton($transaciton, $order_id)
    {
        // Tablo yalnizca kurulumda olusturuluyordu; modul guncellenen kurulumlarda
        // install() yeniden calismadigi icin kayit yolu kendini onarir. Aksi halde
        // callback tablo hatasiyla kesilir ve siparis hic odendi olmaz.
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "moka_transaction` (
          `moka_transaction_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `payment_id` INT(11) NOT NULL,
          `order_id` INT(11) NOT NULL,
          `amount` DECIMAL(10, 2) NOT NULL,
          `currency_code` char(3) NOT NULL,
          `installment_number` int(11) NOT NULL,
          `commission_amount` int(11) NOT NULL,
          `payment_status` VARCHAR(20) NOT NULL,
          `transaction_status` VARCHAR(20) NOT NULL,
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`moka_transaction_id`),
          KEY `order_id` (`order_id`),
          KEY `payment_id` (`payment_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

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

    /**
     * Doğrulama kaydını veritabanına yazar (siparişe bağlı, tek kullanımlık).
     * Oturum çerezine bağımlı olmadığı için banka dönüşü çapraz site POST'unda
     * da erişilebilir.
     */
    public function savePendingPayment($order_id, $trx_code, $code_for_hash, $amount, $currency)
    {
        $this->ensurePendingTable();

        $this->db->query("DELETE FROM `" . DB_PREFIX . "moka_pending` WHERE `created_at` < DATE_SUB(NOW(), INTERVAL 7 DAY)");

        // REPLACE yerine INSERT: ayni siparis icin birden fazla deneme (her
        // denemenin kendi koduyla) yan yana durabilir; aksi halde ikinci deneme
        // birincinin kaydini sessizce siler ve geciken banka donusu eslesmez.
        $this->db->query("INSERT INTO `" . DB_PREFIX . "moka_pending` SET
            `order_id` = '" . (int) $order_id . "',
            `trx_code` = '" . $this->db->escape((string) $trx_code) . "',
            `code_for_hash` = '" . $this->db->escape((string) $code_for_hash) . "',
            `amount` = '" . (float) $amount . "',
            `currency` = '" . $this->db->escape((string) $currency) . "',
            `created_at` = NOW()
            ON DUPLICATE KEY UPDATE
                `code_for_hash` = VALUES(`code_for_hash`),
                `amount` = VALUES(`amount`),
                `currency` = VALUES(`currency`),
                `created_at` = NOW()");
    }

    /**
     * `moka_pending` tablosunu hazir eder ve gerekirse eski semadan donusturur.
     *
     * Tablo yalnizca kurulumda olusturuluyor; modul guncellenen kurulumlarda
     * admin install() yeniden calismadigi icin kayit yolu kendini onarir.
     * Ayrica eski surumun kurdugu tabloda `trx_code` sutunu yoktu; CREATE TABLE
     * IF NOT EXISTS tablo zaten varsa hicbir sey yapmadigi icin sema burada
     * denetlenir ve veri kaybi olmadan donusturulur. Donusturme yapilamiyorsa
     * (yetki/kilitleme) tablo yeniden olusturulur; bekleyen kayitlar kisa
     * omurludur ve musteri odemeyi tekrar deneyebilir.
     */
    private function ensurePendingTable()
    {
        // Once tablo olusturulur: var olmayan bir tabloda SHOW COLUMNS sorgusu
        // OpenCart'in DB katmaninda exception firlatir.
        $this->db->query($this->pendingTableSql());

        if ($this->pendingTableIsCurrent()) {
            return;
        }

        try {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "moka_pending` ADD COLUMN `trx_code` varchar(64) NOT NULL DEFAULT ''");
            $this->db->query("UPDATE `" . DB_PREFIX . "moka_pending` SET `trx_code` = CONCAT('legacy-', `order_id`, '-', UNIX_TIMESTAMP()) WHERE `trx_code` = ''");
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "moka_pending` DROP PRIMARY KEY, ADD PRIMARY KEY (`trx_code`), ADD KEY `order_id` (`order_id`)");
        } catch (Exception $e) {
            // donusturulemedi; asagida tablo bastan olusturulur
        }

        if ($this->pendingTableIsCurrent()) {
            return;
        }

        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "moka_pending`");
        $this->db->query($this->pendingTableSql());
    }

    /**
     * Tablo, beklenen semada mi (trx_code sutunu ve birincil anahtar)?
     */
    private function pendingTableIsCurrent()
    {
        try {
            $column = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "moka_pending` LIKE 'trx_code'");

            if (!$column || !is_object($column) || !$column->num_rows) {
                return false;
            }

            $index = $this->db->query("SHOW KEYS FROM `" . DB_PREFIX . "moka_pending` WHERE Key_name = 'PRIMARY' AND Column_name = 'trx_code'");

            return $index && is_object($index) && $index->num_rows;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * `moka_pending` tablosunun guncel tanimi.
     */
    private function pendingTableSql()
    {
        return "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "moka_pending` (
          `trx_code` varchar(64) NOT NULL,
          `order_id` int(11) NOT NULL,
          `code_for_hash` varchar(64) NOT NULL,
          `amount` decimal(10,2) NOT NULL,
          `currency` varchar(8) NOT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`trx_code`),
          KEY `order_id` (`order_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    }

    /**
     * Kayit, Moka'ya gonderilen benzersiz islem koduyla bulunur. Siparis kimligi
     * tekrar kullanilabilen bir degerdir; Moka OtherTrxCode'u bayi bazinda kalici
     * olarak benzersiz ister, aksi halde islem reddedilir veya baska bir islemin
     * kaydi eslesir.
     */
    public function getPendingPaymentByTrxCode($trx_code)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "moka_pending` WHERE `trx_code` = '" . $this->db->escape((string) $trx_code) . "'");

        if (!$query || !is_object($query) || !$query->num_rows) {
            return null;
        }

        return $query->row;
    }

    /**
     * Siparise ait dogrulama kaydini dondurur (geriye donuk uyumluluk yolu).
     */
    public function getPendingPayment($order_id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "moka_pending` WHERE `order_id` = '" . (int) $order_id . "'");

        // Tablo yoksa (henuz hic odeme baslatilmamis kurulum) veya sorgu
        // basarisizsa kayit yokmus gibi davranilir; callback fail-closed kalir.
        if (!$query || !is_object($query) || !$query->num_rows) {
            return null;
        }

        return $query->row;
    }

    /**
     * Doğrulama kaydını siler (tek kullanımlık).
     */
    public function deletePendingPayment($order_id)
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "moka_pending` WHERE `order_id` = '" . (int) $order_id . "'");
    }

}
