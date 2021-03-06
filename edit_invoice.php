<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 16.01.18
 * Time: 18:18
 */

require_once 'includes/database/Database.php';
require_once 'includes/html/utils.php';
require_once 'includes/view/StyledFields.php';
require_once 'includes/view/DropDownStyle.php';

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
                $db->insert_lineitem(
                        $_POST['id'],
                        $lineitem['description'],
                        $lineitem['price']
                );
            }
        }
    }
}

// ## fetching from db has to be placed after saving (post) instructions
/** @var InvoiceRecord $invoice */
if (array_key_exists('invoice_id', $_GET)) {
    $invoice = $db->get_invoice_by_id($_GET['invoice_id']);
    $invoice_id = $invoice->id;
    $lineitems = $db->get_lineitem_by_invoice_id($invoice_id);
}
else {
    $invoice = new InvoiceRecord();
    $invoice_id = $db->get_last_invoice_id() + 1;
}

// ## html ##
$invoice_number = $invoice->invoice_number ?? $db->get_last_invoice_number() + 1;
$extract_customer_id = function (CustomerRecord $customer) {
    return $customer->id;
};
$extract_customer_name = function (CustomerRecord $customer) {
    return "$customer->forename $customer->surname";
};

$invoice_date = $invoice->invoice_date ?? date('Y-m-d');
$reference = $invoice->reference ?? '';

$customer_options = new HtmlFormOptions(
    $customers,
    $extract_customer_id,
    $extract_customer_name
);
$drop_down_customers = new DropDownStyle(
    'customer_id',
    '',
    $customer_options
);

$styled_record = new StyledFields($invoice);
$styled_record->set_field_value('id', $invoice_id);
$styled_record->set_field_value('invoice_number', $invoice_number);
$styled_record->set_field_value('invoice_date', $invoice_date);
$styled_record->field_style('customer_id', $drop_down_customers);
$styled_record->set_field_value('reference', $reference);


require 'includes/html/head.php';
?>
<h1>Rechnung Editieren</h1>
<form action="" method="POST">
    <?php print $styled_record->generate_html() ?>

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
