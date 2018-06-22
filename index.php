<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 10.01.18
 * Time: 23:24
 */

require_once 'includes/database/Database.php';
require_once 'includes/html/utils.php';

require 'includes/html/head.php';

echo "<h1>Rechnungen</h1>";
echo "<p><a href='edit_invoice.php' class='text-button'> [new] </a></p>\n";

$db = new Database();
$invoices = $db->get_invoices();

$row_edit = function (InvoiceRecord $invoice) : array {
    $row = $invoice->get_properties();
    $href_create = "create_invoice.php?invoice_id=$invoice->invoice_id";
    $href_edit = "edit_invoice.php?invoice_id=$invoice->invoice_id";
    $row[] = "<a href='$href_create' class='text-button'> [pdf] </a>";
    $row[] = "<a href='$href_edit' class='text-button'> [edit] </a>";
    return $row;
};

print_table($invoices, $row_edit, 2);

require 'includes/html/tail.php';
