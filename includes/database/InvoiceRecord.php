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
    public $invoice_id;
    public $invoice_num;
    public $date;
    public $customer_id;
    public $purpose;
    public $status;
}
