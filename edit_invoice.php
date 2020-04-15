<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 16.01.18
 * Time: 18:18
 */

require_once 'includes/database/CustomerRecord.php';
require_once 'includes/database/InvoiceRecord.php';
require_once 'includes/database/LineItemRecord.php';
require_once 'includes/html/utils.php';
require_once 'includes/view/StyledFields.php';
require_once 'includes/view/DropDown.php';

$customer = new CustomerRecord();
$customers = $customer->select_all();
$db = new Database();

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
                $lineitem = LineItemRecord::construct_from_alien_array($lineitem_array);
                $lineitem->set_new_id();
                $lineitem->invoice_id = $invoice_id;
                $lineitem->insert();
                $lineitems[] = $lineitem;
            }
        }
    }
    return $lineitems;
}

/** @var InvoiceRecord $invoice */

if (array_key_exists('id', $_POST)) {
    $invoice = InvoiceRecord::construct_from_alien_array($_POST);
    $invoice->upsert();
    $lineitems = create_lineitems($db);
} else {
    if (array_key_exists('invoice_id', $_GET)) {
        $invoice = InvoiceRecord::construct_from_id($_GET['invoice_id']);
        $lineitems = $db->get_lineitems_by_invoice_id($invoice->id);
    }
    else {
        $invoice = InvoiceRecord::construct_new();
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
$drop_down_customers = new DropDown(
        'customer_id',
        $invoice->customer_id,
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
