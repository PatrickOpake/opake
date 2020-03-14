<?php

use \Console\Migration\BaseMigration;

class FloatToDecimal extends BaseMigration
{
    public function change()
    {
	    $this->query('
			ALTER TABLE `billing_fee_schedule` CHANGE `amount` `amount` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_fee_schedule` CHANGE `fc_mod_amount` `fc_mod_amount` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_fee_schedule` CHANGE `fb_mod_amount` `fb_mod_amount` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_fee_schedule` CHANGE `penalty_price` `penalty_price` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_fee_schedule` CHANGE `fc_mod_penalty_price` `fc_mod_penalty_price` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_fee_schedule` CHANGE `fb_mod_penalty_price` `fb_mod_penalty_price` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_fee_schedule` CHANGE `non_covered_charges` `non_covered_charges` DECIMAL(12,2)  NULL  DEFAULT NULL;

			ALTER TABLE `billing_navicure_claim_status_acknowledgment` CHANGE `amount` `amount` DECIMAL(12,2)  NULL  DEFAULT NULL;

			ALTER TABLE `billing_navicure_claim_status_acknowledgment_service` CHANGE `amount` `amount` DECIMAL(12,2)  NULL  DEFAULT NULL;

			ALTER TABLE `billing_navicure_payment_bunch` CHANGE `amount` `amount` DECIMAL(12,2)  NULL  DEFAULT NULL;

			ALTER TABLE `hcpc` CHANGE `price` `price` DECIMAL(12,2)  NULL  DEFAULT NULL;

			ALTER TABLE `inventory` CHANGE `unit_price` `unit_price` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `inventory` CHANGE `charge_amount` `charge_amount` DECIMAL(12,2)  NULL  DEFAULT NULL;

			ALTER TABLE `order_item` CHANGE `price` `price` DECIMAL(12,2)  NULL  DEFAULT NULL;

			ALTER TABLE `orgnization` CHANGE `chargeable` `chargeable` DECIMAL(12,2)  NULL  DEFAULT NULL;

			ALTER TABLE `site` CHANGE `chargeable` `chargeable` DECIMAL(12,2)  NULL  DEFAULT NULL;

			ALTER TABLE `order` CHANGE `shipping_cost` `shipping_cost` DECIMAL(12,2)  NOT NULL;
		');
    }
}
