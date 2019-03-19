<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 14.12.18
 * Time: 13:52
 */


// ##### basic html generators #####

function generate_html_attributes(array $attributes) : string {
    $str = '';
    /** @var string $attribute */
    foreach ($attributes as $attribute => $value) {
        $value_part = $value === Null ? '' : '="' . $value . '"';
        $str .= ' ' . $attribute . $value_part;
    }
    return $str;
}

function generate_html_element(
    string $tag,
    string $content,
    array $attributes=[]
) : string
{
    $attributes_string = generate_html_attributes($attributes);
    return "<$tag$attributes_string>$content</$tag>";
}

function generate_html_void_element(string $tag, array $attributes=[]) : string {
    $attributes_string = generate_html_attributes($attributes);
    return "<$tag$attributes_string />";
}


// ##### html form generators #####

function generate_form_label(
    string $id,
    string $label,
    array $attributes=[]
) : string
{
    $attributes['for'] = $id;
    return generate_html_element('label', $label, $attributes);
}

function generate_form_input(
    string $id,
    string $label,
    string $value='',
    string $type='text',
    bool $readonly=False
) : string
{
    $attributes = [
        'id' => $id,
        'type' => $type,
        'name' => $id,
        'value' => $value
    ];
    if ($readonly) {
        $attributes['readonly'] = Null;
    }
    $str = "<div>"
        . generate_form_label($id, $label)
        . generate_html_void_element('input', $attributes)
        . "</div>\n";
    return $str;
}

function generate_text_input(
    string $id,
    string $label,
    string $value='',
    bool $readonly=False
) : string
{
    return generate_form_input($id, $label, $value, 'text', $readonly);
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

function generate_form_options( HtmlFormOptions $options, $selected=-1) : string {
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
) : string
{
    $attributes = ['id' => $id, 'name' => $id];
    $options_string = generate_form_options($options, $selected);
    return "<div>"
        . generate_form_label($id, $label)
        . generate_html_element('select', $options_string, $attributes)
        . "</div>";
}

function generate_form_inputs_from_record(Record $record) : string {
    $fields = $record::get_field_names();
    $str ='';
    foreach ($fields as $field) {
        $str .= generate_form_input($field, $field, $record->$field ?? '');
    }
    return $str;
}


// Since generate_form() takes a Record as a parameter, it does not allow for
// anything else, than a simple reflection of the Record. The function should
// be generalized to allow (1) combinations of different Record types like in
// edit_invoice.php (a plain Invoice plus regarding LineItems as a list) and
// (2) to specify the input type, e.g. dropdowns fed from other Records or
// implicit lists, like the mentioned one.
// (Currently the implicit lists are implemented by deleting all entries
// and reinserting them, on 'save'; this might not be the best solution in
// all cases.)
// todo: Generalize generate_form().

function generate_form(Record $record) : string {
    return '<form action="" method="POST">' . "\n"
        . generate_form_inputs_from_record($record)
        . '<div><input type="submit" value="save"></div></form>' . "\n";
}
