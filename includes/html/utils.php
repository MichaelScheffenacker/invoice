<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 16.01.18
 * Time: 17:49
 */

require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../database/LineItemRecord.php';
require_once __DIR__ . '/generic_html_generators.php';

function print_table(array $matrix, callable $row_edit, int $tabs=0) {
    $header_row = array_keys(call_user_func($row_edit, $matrix[0]));
    $header_transformation = function ($header) : string {
        $db = new Database();
        // adding "functional" columns can lead to arbitrary numeric keys.
        $header = is_numeric($header) ? '' : $header;
        $header = $db->lookup_name_by_identifier($header);
        return "<th>$header</th>";
    };

    print_wtabs("<table>\n", $tabs);
    print_tr($header_row, $header_transformation, $tabs + 1);
    foreach ($matrix as $record) {
        $row = call_user_func($row_edit, $record);
        print_tr($row, function ($cell) {return "<td>$cell</td> ";}, $tabs + 1);
    }
    print_wtabs("</table>", $tabs);
}


function print_tr(array $row, callable $cell_transformation, int $tabs=0) {
    print_wtabs('<tr> ', $tabs);
    foreach ($row as $cell) {
        print call_user_func($cell_transformation, $cell);
    }
    print "</tr>\n";
}

function print_wtabs(string $printee, int $tabs) {
    print str_repeat("\t", $tabs) . $printee;
}

function print_form(Record $record) {
    $properties = $record::get_property_names();
    print "<form action='' method='POST'>";
    foreach ($properties as $property) {
        print generate_form_input($property, $property, $record->$property ?? '');
    }
    print "<div> <input type='submit' value='save'></div> </form>\n";
}

function generate_lineitem_input_element(int $number, string $class, $value) {
    $attributes = array(
        'class' => $class,
        'aria-label' => "item $number $class",
        'type' => 'text',
        'name' => "lineitems[$number][$class]",
        'value' => "$value"
    );
    return generate_html_void_element('input', $attributes);
}

function print_lineitem_row(int $number, LineItemRecord $lineitem) {
    $description = $lineitem->description;
    $price = $lineitem->price;
    $description_element =
        generate_lineitem_input_element($number, 'description', $description);
    $price_element = generate_lineitem_input_element($number, 'price', $price);
    $content = $description_element . $price_element;
    $attributes = ['class' => 'lineitem', 'data-number' => $number];
    print generate_html_element('div', $content, $attributes);
}

function print_lineitems(array $lineitems) {
    $row_number = 1;
    if (sizeof($lineitems) > 0) {
        /** @var LineItemRecord $lineitem */
        foreach ($lineitems as $lineitem) {
            print_lineitem_row($row_number, $lineitem);
            $row_number += 1;
        }
    }
    else {
        $lineitem = new LineItemRecord;
        $lineitem->description = '';
        $lineitem->price = 0;
        print_lineitem_row(1, $lineitem);
    }
}