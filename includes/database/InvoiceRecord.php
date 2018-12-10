<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 14.01.18
 * Time: 00:00
 */

require_once 'Record.php';
require_once 'includes/html/utils.php';

class InvoiceRecord extends  Record {
    public $id;
    public $invoice_number;
    public $invoice_date;
    public $customer_id;
    public $reference;
}
