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

class Field {
    public $value;
    public $style;

    public function __construct($value, Style $style) {
        $this->value = $value;
        $this->style = $style;
    }
}

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
// be a part general rules and exceptions for primary keys, timestamps, et
// cetera could be managed.


class StyledRecord {
    public $record;

    private $fields = array();

    public function __construct(Record $record) {
        $this->record = $record;
        foreach ($this->record->get_fields() as $field_name => $field_value) {
            $readonly = ($field_name === 'id');
            $style = new TextStyle($field_name, $field_value, $readonly);
            $this->fields[$field_name] = new Field($field_value, $style);
        }
    }

    public function generate_html() : String {
        $str = '';
        /** @var Field $field */
        foreach ($this->fields as $field) {
            $str .= $field->style->generate_html();
        }
        return $str;
    }

    public function set_field_readonly($field_name) {
        $style = $this->fields[$field_name]->style;
        /** @var TextStyle $style */
        $style->readonly = True;
    }

}
