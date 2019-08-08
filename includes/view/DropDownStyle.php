<?php

/**
 * Created by PhpStorm.
 * User: michaelscheffenacker
 * Date: 25.02.19
 * Time: 19:22
 */

require_once __DIR__ . '/Style.php';
require_once __DIR__ . '/../html/utils.php';

class DropDownStyle extends Style {
    public $options;

    public function __construct(
        string $name,
        string $value,
        HtmlFormOptions $options,
        bool $readonly=False
    ) {
        parent::__construct($name, $value, $readonly);
        $this->options = $options;
    }

    public function generate_html(): String {
        $id = $this->name;
        $label = $this->name;
        $value = $this->value ?? -1;
        $options = $this->options;
        $readonly = $this->readonly;
        return generate_form_select(
            $id,
            $label,
            $options,
            $value,
            $readonly
        );
    }

}