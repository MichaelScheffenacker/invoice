<?php

/**
 * Created by PhpStorm.
 * User: michaelscheffenacker
 * Date: 25.02.19
 * Time: 18:47
 */

require_once __DIR__ . '/../database/Record.php';
require_once __DIR__ . '/Style.php';
require_once __DIR__ . '/TextStyle.php';


// One problem with this implementation (and this problem includes the naming
// of the class) is its rigidity against varying number and order of fields.
// Actually, allowing for a lower number or changing order would not be too
// hard to implement. But what about combining multiple records.
// Nah, as I think about it, multiple Records should not be included here ...
// A aggregation of different records for a single form should be handled by
// a an object one level higher in the composition order (it does not even
// have to be an object; it could be handled directly in the php file itself.

// Currently the name 'field' is used for a record on the database side,
// and also in the front-end as a field of a from. But this ambiguity also
// maps the real world ambiguity between a form field and a database field:
// in our notion they are pretty much the same. But on the other hand,
// this (the naming ambiguity as well as the actual treatment) might break
// the separation of concerns and application boundaries. I should meditate
// about the discrepancy about the rules of clean code and my intent with this
// software.

// The if statement
//
//     if ($field_name === 'id')
//
// is dangerously hardcoded here. It does not harm the current application
// since it expects to have a id field as a primary key for every table. But
// for generalization this should be outsourced to a higher level. There could
// be a part defining general rules and exceptions for primary keys, timestamps,
// et cetera could be managed.


class StyledFields {
    private $record;
    private $fields = array();

    public function __construct(Record $record) {
        $this->record = $record;
        foreach ($this->record->get_fields() as $field_name => $field_value) {
            $readonly = ($field_name === 'id');
            $field_value = $field_value ?? '';
            $styled_field = new TextStyle($field_name, $field_value, $readonly);
            $this->fields[$field_name] = $styled_field;
        }
    }

    public function generate_html() : String {
        $str = '';
        /** @var Style $field */
        foreach ($this->fields as $field) {
            $str .= $field->generate_html();
        }
        return $str;
    }

    public function set_field_readonly(string $field_name) {
        $field = $this->fields[$field_name];
        /** @var TextStyle $field */
        $field->readonly = True;
    }

    public function set_field_value(string $field_name, string $value) {
        $field = $this->fields[$field_name];
        /** @var TextStyle $field */
        $field->value = $value;
    }

    public function field_style(string $field_name, Style $style) {
        $this->fields[$field_name] = $style;
    }

    public function get_field_value(string $field_name) {
        $field = $this->fields[$field_name];
        /** @var Style $field */
        return $field->value;
    }
}
