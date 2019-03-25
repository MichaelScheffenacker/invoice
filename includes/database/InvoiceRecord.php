<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 14.01.18
 * Time: 00:00
 */

require_once __DIR__ . '/Record.php';
require_once __DIR__ . '/../../includes/html/utils.php';

class InvoiceRecord extends  Record {
    public $id;
    public $invoice_number;
    public $invoice_date;
    public $customer_id;
    public $reference;
}
