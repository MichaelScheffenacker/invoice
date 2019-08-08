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

/**
 * @param Database $db
 * @return array $lineitems
 */
function create_lineitems(Database $db): array {
    // All lineitems are deleted and inserted again on every "save"
    $invoice_id = $_POST['id'];
    $db->delete_lineitem_by_invoice_id($invoice_id);
    $lineitems_array = $_POST['lineitems'];
    $lineitems = [];
    if (isset($lineitems_array)) {
        foreach ($lineitems_array as $lineitem_array) {
            if ($lineitem_array['description'] or $lineitem_array['price']) {
                /** @var LineItemRecord $lineitem */
                $lineitem = LineItemRecord::construct_by_alien_array($lineitem_array);
                $lineitem->id = $db->select_last_record_id($db->lineitem_table) + 1;
                $lineitem->invoice_id = $invoice_id;
                $db->insert_lineitem($lineitem);
                $lineitems[] = $lineitem;
            }
        }
    }
    return $lineitems;
}

/** @var InvoiceRecord $invoice */
/**
 * @param Database $db
 * @return InvoiceRecord
 */
function new_invoice(Database $db): InvoiceRecord {
    $invoice = new InvoiceRecord();
    $invoice->id = $db->get_last_invoice_id() + 1;
    $invoice->invoice_number = $db->get_last_invoice_number() + 1;
    $invoice->invoice_date = date('Y-m-d');
    return $invoice;
}

// ## saving to db gets solely triggered by a post request with an invoice_id
if (array_key_exists('id', $_POST)) {
    $invoice = InvoiceRecord::construct_by_alien_array($_POST);
    $db->upsert_invoice($invoice);
    $lineitems = create_lineitems($db);
} else {

    if (array_key_exists('invoice_id', $_GET)) {
        $invoice_id = $_GET['invoice_id'];
        $invoice = $db->get_invoice_by_id($invoice_id);
        $lineitems = $db->get_lineitem_by_invoice_id($invoice_id);
    }
    else {
        $invoice = new_invoice($db);
        $lineitems = [];
    }
}


// ## html ##
$invoice_number = $invoice->invoice_number ?? $db->get_last_invoice_number() + 1;
$extract_customer_id = function (CustomerRecord $customer) {
    return $customer->id;
};
$extract_customer_name = function (CustomerRecord $customer) {
    return "$customer->forename $customer->surname";
};
$customer_options = new HtmlFormOptions(
    $customers,
    $extract_customer_id,
    $extract_customer_name
);

$styled_record = new StyledFields($invoice);
$drop_down_customers = new DropDownStyle(
        'customer_id',
        '',
        $customer_options
);
$styled_record->field_style('customer_id', $drop_down_customers);


require 'includes/html/head.php';
?>
<h1>Rechnung Editieren</h1>
<form action="" method="POST">
    <?php print $styled_record->generate_html() ?>

    <div>
        <h2>Leistungen: </h2>
        <p id="add_lineitem_button" class="text-button">[add]</p>
        <?php print_lineitems($lineitems) ?>
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
