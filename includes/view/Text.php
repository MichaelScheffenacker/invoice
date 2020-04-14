<?php

/**
 * Created by PhpStorm.
 * User: michaelscheffenacker
 * Date: 25.02.19
 * Time: 19:14
 */

require_once __DIR__ . '/Representation.php';
require_once __DIR__ . '/../html/utils.php';

class Text extends Representation {

    public function generate_html(): string {
        $id = $this->name;
        $label = $this->name;
        $value = $this->value ?? '';
        $readonly = $this->readonly;
        return generate_text_input($id, $label, $value, $readonly);
    }
}
