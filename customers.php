<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 31.01.18
 * Time: 13:40
 */

require_once 'includes/database/Database.php';
require_once 'includes/html/utils.php';
$db = new Database();

require 'includes/html/head.php';

echo "<h1>Kunden</h1>";
echo "<p><a href='edit_customer.php' class='text-button'> [new] </a></p>\n";

$db = new Database();
$customers = $db->select_customers();

/*
 * The reason for implementing the following callback $row_edit is to create
 * a additional column in the table with a link to the editing page for the
 * record.  It is not sufficient to simply pass the columns, because the link
 * needs to know the id of the record to create a proper reference.
 *
 * But this again leads to other difficulties: the table rows are passed as
 * Records, in this case, more specific, as CustomerRecord.  But an Object can
 * obviously (at least in PHP) not be extended like an array.  this leads to
 * a necessary (in a way defining) transformation from the object to an
 * array.  The array is then easily iterated in the html generating loop
 * (interestingly the object can be iterated over its properties with exactly
 * the same loop, which lead to a confusion at some point).  The object-to-
 * array transformation part is actually wet, because it will be repeated
 * in every of those callbacks for each table.  Moving the transformation part
 * out of the callback also isn't an option due to required access to the
 * objects id.  Although the the array created by ->get_properties() is
 * associative and indexed by the property names of the object.  And therefore
 * it would actually be possible to substitute the object by the array from
 * the beginning!? But this leads us to the next problem:
 *
 * Writing the callback requires knowledge of the class: ->customer_id is
 * specific for the CustomerRecord class.  In exotic cases it might not even be
 * clear which property to use.  On the other hand in most cases the required
 * property might be the id of the record.  So it might be possible to find
 * a way to implement a solution to automatically handle (or at least support)
 * the record id cases.  Record ids are all named differently in the various
 * tables, therefore it might be necessary to implement a uniform getter method
 * for the different ids.  This is an example where the use of the name "id"
 * for primary keys simplifies development.
 *
 * Possibly some kind of a callback factory would be better. But how is it done
 * without knowing the id, how to have the id it at hand?
 * ...
 */

$row_edit = function (CustomerRecord $customer) : array {
    $removees = array_flip([
        'street',
        'city',
        'country',
        'phone_office',
        'phone_mobile',
        'mail'
    ]);
    $row = array_diff_key($customer->get_fields(), $removees);
    $href_edit = "edit_customer.php?id=$customer->id";
    $row[] = "<a href='$href_edit' class='text-button'> [edit] </a>";
    return $row;
};

if (!is_null($customers) and !empty($customers)) {
    print_table($customers, $row_edit, 2);
}

require 'includes/html/tail.php';
