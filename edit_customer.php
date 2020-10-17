<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 01.02.18
 * Time: 18:07
 */

require_once 'includes/html/utils.php';


require 'includes/html/head.php';

if (array_key_exists('id', $_POST)) {
    /* @var $customer CustomerRecord */
    $customer = CustomerRecord::construct_from_alien_array($_POST);
    $customer->upsert();
} else {
    if (array_key_exists('id', $_GET)) {
        $customer = CustomerRecord::construct_from_id($_GET['id']);
    }
    else {
        $customer = CustomerRecord::construct_next();
    }
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
