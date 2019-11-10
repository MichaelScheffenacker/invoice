<?php

/**
 * Created by PhpStorm.
 * User: michaelscheffenacker
 * Date: 25.02.19
 * Time: 19:13
 */
abstract class Style {
    public $name;
    public $value;
    public $readonly;

    public function __construct(
        string $name,
        $value,
        bool $readonly=False
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->readonly = $readonly;
    }

    abstract public function generate_html() : string;
}
