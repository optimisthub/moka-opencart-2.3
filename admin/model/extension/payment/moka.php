<?php

class ModelExtensionPaymentMoka extends Model
{
    public function getTransactions($data)
    {
        $sql = "SELECT * FROM `" . DB_PREFIX . "moka_transaction`";

        if (isset($data['order_id'])) {
            $sql .= " WHERE order_id='" . (int)$data['order_id'] . "'";
        }

        $sql .= " ORDER BY created_at DESC";

        if (isset($data['start']) && isset($data['limit'])) {
            $sql .= " LIMIT " . $data['start'] . ', ' . $data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalTransactions($data)
    {
        $sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "moka_transaction`";

        if (isset($data['order_id'])) {
            $sql .= " WHERE order_id='" . (int)$data['order_id'] . "'";
        }

        return $this->db->query($sql)->row['total'];
    }

    public function createTables()
    {
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
          `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`moka_transaction_id`),
          KEY `order_id` (`order_id`),
          KEY `payment_id` (`payment_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    public function dropTables()
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "moka_transaction`");
    }
}
