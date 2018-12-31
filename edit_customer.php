<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 01.02.18
 * Time: 18:07
 */

require_once 'includes/database/Database.php';
require_once 'includes/html/utils.php';
$db = new Database();


require 'includes/html/head.php';

if (array_key_exists('id', $_POST)) {
    /* @var $customer CustomerRecord */
    $customer = CustomerRecord::construct_by_alien_array($_POST);
    $db->upsert_customer($customer);
}

if (array_key_exists('id', $_GET)) {
    $customer = $db->get_customer_by_id($_GET['id']);
}
else {
    $customer = new CustomerRecord();
    $customer->id = $db->get_last_customer_id() + 1;
}
?>

<h1>Kunde Editieren</h1>
<?php print generate_form($customer); ?>

<details class="post-array">
    <summary>Post Array <code>$POST</code></summary>
    <pre>
        <?php print_r($customer); ?>
    </pre>
</details>

<?php
require 'includes/html/tail.php';
