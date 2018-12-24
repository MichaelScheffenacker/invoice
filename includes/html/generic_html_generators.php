<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 14.12.18
 * Time: 13:52
 */


// ##### basic html generators #####

function generate_html_attributes(array $attributes) {
    $str = '';
    /** @var string $attribute */
    foreach ($attributes as $attribute => $value) {
        $value_part = $value === '' ? '' : '="' . $value . '"';
        $str .= ' ' . $attribute . $value_part;
    }
    return $str;
}

function generate_html_element(string $tag, string $content, array $attributes=[]) {
    $attributes_string = generate_html_attributes($attributes);
    return "<$tag$attributes_string>$content</$tag>";
}

function generate_html_void_element(string $tag, array $attributes=[]) {
    $attributes_string = generate_html_attributes($attributes);
    return "<$tag$attributes_string />";
}


// ##### html form functions #####

function generate_form_label(string $id, string $label, array $attributes=[]) {
    $attributes['for'] = $id;
    return generate_html_element('label', $label, $attributes);
}

class HtmlFormOptions {
    public $options_array;
    private $value_callback;
    private $content_callback;

    public function __construct(
        array $options_array,
        callable $value_callback,
        callable $content_callback
    ) {
        $this->options_array = $options_array;
        $default_callback = function ($option) {
            return $option->id;
        };
        $this->value_callback = $value_callback ?? $default_callback;
        $this->content_callback = $content_callback ?? $default_callback;
    }

    public function extract_value($option) {
        return call_user_func($this->value_callback, $option);
    }

    public function extract_content($option) {
        return call_user_func($this->content_callback, $option);
    }
}

function generate_form_options( HtmlFormOptions $options, $selected=-1) {
    $str = '';
    foreach ($options->options_array as $option) {
        $value = $options->extract_value($option);
        $content = $options->extract_content($option);
        $attributes = ['value' => $value];
        if ($value == $selected) {
            $attributes['selected'] = '';
        }
        $str .= generate_html_element('option', $content, $attributes);
//        $str .= "<option value='$value->id' $sel> " .
//            " $value->forename $value->surname</option>\n";
    }
    return $str;
}

function print_form_select(
    string $id,
    string $label,
    HtmlFormOptions $options,
    int $selected=-1
) {
    $attributes = ['id' => $id, 'name' => $id];
    $options_string = generate_form_options($options, $selected);
    print "<div>";
    print generate_form_label($id, $label);
    print generate_html_element('select', $options_string, $attributes);
    print "</div>";
}
