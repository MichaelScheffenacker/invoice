<?php

/**
 * Created by PhpStorm.
 * User: michaelscheffenacker
 * Date: 25.02.19
 * Time: 19:14
 */

require_once __DIR__ . '/Style.php';
require_once __DIR__ . '/../html/utils.php';

class TextStyle extends Style {

    public $name;
    public $value;
    public $readonly;

    public function __construct(string $name, $value, bool $readonly=False) {
        $this->name = $name;
        $this->value = $value;
        $this->readonly = $readonly;
    }

    public function generate_html(): String {
        $id = $this->name;
        $label = $this->name;
        $value = $this->value ?? '';
        $type = 'text';
        $readonly = $this->readonly;
        $str = generate_form_input($id, $label, $value, $type, $readonly);
        return $str;
    }
}