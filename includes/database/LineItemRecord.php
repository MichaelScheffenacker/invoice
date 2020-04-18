<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 14.01.18
 * Time: 00:11
 */

require_once __DIR__ . '/Record.php';

class LineItemRecord extends Record
{
    public const _table = 'lineitems';
    public $id;
    public $invoice_id;
    public $description;
    public $price;
}
