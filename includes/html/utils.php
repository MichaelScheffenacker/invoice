<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 16.01.18
 * Time: 17:49
 */

require_once 'includes/database/Database.php';

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
        print_form_input($property, $property, $record->$property ?? '');
    }
    print "<div> <input type='submit' value='save'></div> </form>\n";
}

function print_form_input(
    string $id,
    string $label,
    string $value='',
    string $type='text',
    bool $readonly=false
) {
    $dis = $readonly ? 'readonly' : '';
    print
        "<div> " .
        form_label($id, $label) .
        " <input id='$id' type='$type' name='$id' value='$value' $dis> " .
        "</div>\n";
}

function form_label(string $id, string $label) {
    return "<label for='$id'>$label:</label>";
}

function print_form_select(string $id, string $label, array $values, int $selected=-1) {
    print
        "<div>\n" .
        form_label($id, $label) .
        "\n<select id='$id' name='$id'>" .
        form_options($values, $selected) .
        "\n</select>" .
        "\n</div>\n";
}

// todo: This form_options() function has to be generalised: it is using
// todo: explicitly attributes from CustomerRecord class. In that case the applicable
// todo: classes need attributes expressing their display.
// todo: tl;dr > Put rendering information close to the data.
function form_options($values, $selected) {
    $str = '';
    foreach ($values as $value) {
        $sel = ($value->customer_id == $selected) ? 'selected' : '';
        $str .= "<option value='$value->customer_id' $sel> " .
            " $value->name_first $value->name_last</option>\n";
    }
    return $str;
}

function task_row(int $number, string $title='', int $price=0) {
    $str =
        "<div number='$number' class='lineitem'>\n" .
        "    <input class='title' aria-label='task $number title' type='text' name='tasks[$number][title]' value='$title'>\n" .
        "    <input class='price' aria-label='task $number price' type='text' name='tasks[$number][price]' value='$price'>\n" .
        "</div>\n";
    return $str;
}