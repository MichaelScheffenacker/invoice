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
if (array_key_exists('invoice_id', $_POST)) {
    $db->upsert_invoice(
            $_POST['invoice_id'] ?? '',
            $_POST['invoice_num'] ?? '',
            $_POST['date'] ?? '',
            $_POST['customer_id'] ?? '',
            $_POST['purpose'] ?? ''
    );

    // task are all getting deleted and inserted again on every "save"
    $db->delete_tasks_by_invoice_id($_POST['invoice_id']);
    if (isset($_POST['tasks'])) {
        foreach ($_POST['tasks'] as $task) {
            if ($task['title'] or $task['price']) {
                $db->insert_task($_POST['invoice_id'], $task['title'], $task['price']);
            }
        }
    }
}

// ## fetching from db has to be placed after saving (post) instructions
if (array_key_exists('invoice_id', $_GET)) {
    $invoice = $db->get_invoice_by_id($_GET['invoice_id']);
    $invoice_id = $invoice->invoice_id;
    $tasks = $db->get_tasks_by_invoice_id($invoice_id);
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
    print_form_input('invoice_id', 'Database ID', $invoice_id, 'text',true);
    print_form_input('date', 'Rechnungsdatum', $invoice->date ?? '');
    print_form_input('invoice_num', 'Rechnungsnummer', $invoice->invoice_num ?? '');
    print_form_select('customer_id', 'Kunde', $customers, $invoice->customer_id ?? -1);
    print_form_input('purpose', 'Zweck', $invoice->purpose ?? '');
    ?>

    <div><h2>Leistungen: </h2><p id="add_task_button" class="text-button">[add]</p>
        <?php
        $row_number = 1;
        if (isset($tasks) and sizeof($tasks) > 0) {
            /* @var $task TaskRecord */
            foreach ($tasks as $task) {
                echo task_row($row_number, $task->title, $task->amount);
                $row_number += 1;
            }
        }
        else {
            echo task_row(1);
        }
        ?>
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
