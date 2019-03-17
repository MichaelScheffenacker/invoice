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
require_once __DIR__ . '/TestRecord.php';
require_once __DIR__ . '/TestIdRecord.php';

class StyleTests extends TestCase {
    public function test_default_form_html() {
        $expected_html = generate_form_input('one', 'one', '');
        $expected_html.= generate_form_input('two', 'two', '');
        $styled_record = new StyledFields(new TestRecord());
        $this->assertEquals($expected_html, $styled_record->generate_html());
    }

    public function test_readonly_form_html() {
        $expected_html = generate_form_input(
            'one',
            'one',
            '',
            'text',
            True
        );
        $expected_html.= generate_form_input('two', 'two', '');
        $styled_record = new StyledFields(new TestRecord());
        $styled_record->set_field_readonly('one');
        $this->assertEquals($expected_html, $styled_record->generate_html());
    }

    public function test_id_default_readonly_form_html() {
        $expected_html = generate_form_input(
            'id',
            'id',
            '',
            'text',
            True
        );
        $expected_html.= generate_form_input('two', 'two', '');
        $styled_record = new StyledFields(new TestIdRecord());
        // in contrary to test_readonly_form_html() no manual setting of readonly is required.
        $this->assertEquals($expected_html, $styled_record->generate_html());
    }
}
