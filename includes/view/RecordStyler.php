<?php

/**
 * Created by PhpStorm.
 * User: michaelscheffenacker
 * Date: 25.02.19
 * Time: 18:47
 */

require_once __DIR__ . '../database/Record.php';
require_once __DIR__ . 'Style.php';
require_once __DIR__ . 'TextStyle.php';

class Field {
    public $value;
    public $style;

    public function __construct($value, Style $style) {
        $this->value = $value;
        $this->style = $style;
    }
}


class RecordStyler {
    public $record;

    private $fields = array();

    public function __construct(Record $record) {
        $this->record = $record;
        foreach ($this->record->get_fields() as $field_name => $field) {
            $styledField = new Field($field, RecordStyler::default_style());
            $this->fields[$field_name] = $styledField;
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
    
    static public function default_style() : Style {
        return new TextStyle();
    }

}