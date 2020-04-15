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
    protected const _table_name = 'invoices';
    public $id;
    public $invoice_number;
    public $invoice_date;
    public $customer_id;
    public $reference;

    public static function construct_new() : Record {
        /** @var InvoiceRecord $invoice */
        $invoice = parent::construct_new();
        $invoice->invoice_number = self::$_db->get_last_invoice_number() + 1;
        $invoice->invoice_date = date('Y-m-d');
        return $invoice;
    }
}
