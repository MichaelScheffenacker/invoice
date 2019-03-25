<?php
/**
 * Created by PhpStorm.
 * User: michaelscheffenacker
 * Date: 2019-03-16
 * Time: 01:31
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../includes/html/generic_html_generators.php';
require_once __DIR__ . '/../includes/view/StyledFields.php';
require_once __DIR__ . '/../includes/view/TextStyle.php';
require_once __DIR__ . '/../includes/view/DropDownStyle.php';
require_once __DIR__ . '/TestRecord.php';
require_once __DIR__ . '/TestIdRecord.php';

class StyleTests extends TestCase {
    public function test_default_form_html() {
        $expected_html =
            generate_text_input('one', 'one')
            . generate_text_input('two', 'two');
        $styled_record = new StyledFields(new TestRecord());
        $this->assertSame($expected_html, $styled_record->generate_html());
    }

    public function test_readonly_form_html() {
        $expected_html =
            generate_text_input('one', 'one', '', True)
            . generate_text_input('two', 'two');
        $styled_record = new StyledFields(new TestRecord());
        $styled_record->set_field_readonly('one');
        $this->assertSame($expected_html, $styled_record->generate_html());
    }

    public function test_id_default_readonly_form_html() {
        $expected_html =
            generate_text_input('id', 'id', '', True)
            . generate_text_input('two', 'two');
        $styled_record = new StyledFields(new TestIdRecord());
        // in contrary to test_readonly_form_html() no manual setting of
        // readonly is required.
        $this->assertSame($expected_html, $styled_record->generate_html());
    }

    public function test_drop_down_style() {
        $options = new HtmlFormOptions(
            [['id' => 1, 'content' => 'aa'], ['id' => 2, 'content' => 'bb']],
            function ($option) { return $option['id']; },
            function ($option) { return $option['content']; }
        );
        $expected_html =
            generate_form_select('one', 'one', $options)
            . generate_text_input('two', 'two');
        $styled_record = new StyledFields(new TestRecord());
        $one_value = $styled_record->get_value_of_field('one');
        $drop_down_one = new DropDownStyle('one', $one_value, $options);
        $styled_record->field_style('one', $drop_down_one);
        $this->assertSame($expected_html, $styled_record->generate_html());
    }
}
