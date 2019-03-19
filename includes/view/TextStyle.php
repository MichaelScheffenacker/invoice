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

    public function generate_html(): string {
        $id = $this->name;
        $label = $this->name;
        $value = $this->value ?? '';
        $readonly = $this->readonly;
        return generate_text_input($id, $label, $value, $readonly);
    }
}
