<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 10.01.18
 * Time: 23:24
 */

require_once 'includes/database/InvoiceRecord.php';
require_once 'includes/html/utils.php';

require 'includes/html/head.php';

echo "<h1>Rechnungen</h1>";
echo "<p><a href='edit_invoice.php' class='text-button'> [new] </a></p>\n";

$invoice = new InvoiceRecord();
$invoices = $invoice->select_all();

$row_edit = function (InvoiceRecord $invoice) : array {
    $row = $invoice->get_fields();
    $href_create = "create_invoice.php?invoice_id=$invoice->id";
    $href_edit = "edit_invoice.php?invoice_id=$invoice->id";
    $row[] = "<a href='$href_create' class='text-button'> [pdf] </a>";
    $row[] = "<a href='$href_edit' class='text-button'> [edit] </a>";
    return $row;
};

if (!is_null($invoices) and !empty($invoices)) {
    print_table($invoices, $row_edit, 2);
}

require 'includes/html/tail.php';
