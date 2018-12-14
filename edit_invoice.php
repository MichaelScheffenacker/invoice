<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 16.01.18
 * Time: 18:18
 */

require_once 'includes/database/Database.php';
require_once 'includes/html/utils.php';
$db = new Database();
$customers = $db->get_customers();

// ## saving to db gets solely triggered by a post request with an invoice_id
if (array_key_exists('id', $_POST)) {
    $db->upsert_invoice(
            $_POST['id'] ?? '',
            $_POST['invoice_number'] ?? '',
            $_POST['invoice_date'] ?? '',
            $_POST['customer_id'] ?? '',
            $_POST['reference'] ?? ''
    );

    // lineitem are all getting deleted and inserted again on every "save"
    $db->delete_lineitem_by_invoice_id($_POST['id']);
    if (isset($_POST['lineitems'])) {
        foreach ($_POST['lineitems'] as $lineitem) {
            if ($lineitem['description'] or $lineitem['price']) {
                $db->insert_lineitem($_POST['id'], $lineitem['description'], $lineitem['price']);
            }
        }
    }
}

// ## fetching from db has to be placed after saving (post) instructions
/* @var $invoice InvoiceRecord */
if (array_key_exists('invoice_id', $_GET)) {
    $invoice = $db->get_invoice_by_id($_GET['invoice_id']);
    $invoice_id = $invoice->id;
    $lineitems = $db->get_lineitem_by_invoice_id($invoice_id);
}
else {
    $invoice_id = $db->get_last_invoice_id() + 1;
}

// ## html ##
require 'includes/html/head.php';
?>
<h1>Rechnung Editieren</h1>
<form action="" method="POST">
    <?php
    $invoice_number = $invoice->invoice_number ?? $db->get_last_invoice_number() + 1;
    print_form_input('id', 'Database ID', $invoice_id, 'text',true);
    print_form_input('invoice_date', 'Rechnungsdatum', $invoice->invoice_date ?? '');
    print_form_input('invoice_number', 'Rechnungsnummer', $invoice_number);
    print_form_select('customer_id', 'Kunde', $customers, $invoice->customer_id ?? -1);
    print_form_input('reference', 'Referenz/Zweck', $invoice->reference ?? '');
    ?>

    <div><h2>Leistungen: </h2><p id="add_lineitem_button" class="text-button">[add]</p>
        <?php print_lineitems($lineitems ?? []) ?>
    </div>

    <div> <input type="submit" value="save"> </div>
</form>

<details class="post-array">
    <summary><code>post array $POST</code></summary>
    <pre>
        <?php print_r($_POST) ?>
    </pre>
</details>


<?php
require 'includes/html/tail.php';
