<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 14.01.18
 * Time: 00:00
 */

class RogueInvoiceRecord {
    protected const _table_name = 'invoices';
    public $id;
    public $invoice_number;
    public $invoice_date;
    public $customer_id;
    public $reference;
    public $rogue_field;

    public function __construct() {
        $this->reference = 3;
        $this->rogue_field = 4;
    }
}
