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
        $value_part = $value === Null ? '' : '="' . $value . '"';
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


// ##### html form generators #####

function generate_form_label(string $id, string $label, array $attributes=[]) {
    $attributes['for'] = $id;
    return generate_html_element('label', $label, $attributes);
}

function generate_form_input(
    string $id,
    string $label,
    string $value='',
    string $type='text',
    bool $readonly=False
) {
    $attributes = [
        'id' => $id,
        'type' => $type,
        'name' => $id,
        'value' => $value
    ];
    if ($readonly) {
        $attributes['readonly'] = Null;
    }
    $str =
        "<div>" .
        generate_form_label($id, $label) .
        generate_html_void_element('input', $attributes) .
        "</div>\n";
    return $str;
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
        $this->value_callback = $value_callback;
        $this->content_callback = $content_callback;
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
            $attributes['selected'] = Null;
        }
        $str .= generate_html_element('option', $content, $attributes);
    }
    return $str;
}

function generate_form_select(
    string $id,
    string $label,
    HtmlFormOptions $options,
    int $selected=-1
) {
    $attributes = ['id' => $id, 'name' => $id];
    $options_string = generate_form_options($options, $selected);
    $str = "<div>";
    $str .= generate_form_label($id, $label);
    $str .= generate_html_element('select', $options_string, $attributes);
    $str .= "</div>";
    return $str;
}

function generate_form(Record $record) {
    $fields = $record::get_property_names();
    $str = '<form action="" method="POST">' . "\n";
    foreach ($fields as $field) {
        $str .= generate_form_input(
            $field, $field,
            $record->$field ?? ''
        );
    }
    $str .= '<div><input type="submit" value="save"></div></form>' . "\n";
    return $str;
}
