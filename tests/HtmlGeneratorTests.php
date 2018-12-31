<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 24.12.18
 * Time: 03:14
 */

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../includes/html/generic_html_generators.php';
require_once 'TestRecord.php';

class HtmlGeneratorTests extends TestCase
{
    public function test_html_attributes() {
        $this->assertSame(
            ' id="element"',
            generate_html_attributes(['id' => 'element'])
        );
        $this->assertSame(
            ' id="element" class="css"',
            generate_html_attributes(['id' => 'element', 'class' => 'css'])
        );
        $this->assertSame(
            ' value="1" selected',
            generate_html_attributes(['value' => 1, 'selected' => Null])
        );
    }

    public function test_html_element() {
        $this->assertSame(
            '<div>content</div>',
            generate_html_element('div', 'content')
        );
        $this->assertSame(
            '<a href="./index.php">content</a>',
            generate_html_element(
                'a',
                'content',
                ['href' => './index.php']
            )
        );
    }

    public function test_html_void_element() {
        $this->assertSame(
            '<br />',
            generate_html_void_element('br')
        );
        $this->assertSame(
            '<img src="./logo.png" alt="logo" />',
            generate_html_void_element(
                'img',
                ['src' => './logo.png', 'alt' => 'logo']
            )
        );
    }

    public function test_html_element_combinations() {
        $inner = generate_html_element('a', 'content');
        $this->assertSame(
            '<div><a>content</a></div>',
            generate_html_element('div', $inner)
        );
        $inner = generate_html_void_element('br');
        $this->assertSame(
            '<div><br /></div>',
            generate_html_element('div', $inner)
        );
    }

    public function test_form_label() {
        $this->assertSame(
            '<label class="test" for="id">content</label>',
            generate_form_label(
                'id',
                'content',
                ['class' => 'test']
            )
        );
    }

    public function test_form_input() {

        $result = '<div><label for="id">label</label>'
            . '<input id="id" type="text" name="id" value="" />'
            . '</div>' . "\n";
        $this->assertSame(
            $result,
            generate_form_input('id', 'label')
        );

        $result = '<div><label for="id">label</label>'
            . '<input id="id" type="text" name="id" value="value" />'
            . '</div>' . "\n";
        $this->assertSame(
            $result,
            generate_form_input('id', 'label', 'value')
        );

        $result = '<div><label for="id">label</label>'
            . '<input id="id" type="text" name="id" value="" readonly />'
            . '</div>' . "\n";
        $this->assertSame(
            $result,
            generate_form_input(
                'id',
                'label',
                '',
                'text',
                True
            )
        );
    }

    public function test_html_form_options_class() {
        $options = new HtmlFormOptions(
            ['a'=> [12, 13], 'b' =>[22, 23]],
            function ($option) { return $option[0]; },
            function ($option) { return $option[1]; }
        );
        $arr = $options->options_array;
        $this->assertSame(12, $options->extract_value($arr['a']));
        $this->assertSame(13, $options->extract_content($arr['a']));
        $this->assertSame(22, $options->extract_value($arr['b']));
        $this->assertSame(23, $options->extract_content($arr['b']));
    }

    public function test_form_options() {
        $options = new HtmlFormOptions(
            [['id' => 1, 'content' => 'one'], ['id' => 2, 'content' => 'two']],
            function ($option) { return $option['id']; },
            function ($option) { return $option['content']; }
        );
        $this->assertSame(
            '<option value="1">one</option><option value="2">two</option>',
            generate_form_options($options)
        );
        $result = '<option value="1">one</option>';
        $result .='<option value="2" selected>two</option>';
        $this->assertSame($result, generate_form_options($options, 2));
    }

    public function test_form_select() {
        $options = new HtmlFormOptions(
            [['id' => 1, 'content' => 'one'], ['id' => 2, 'content' => 'two']],
            function ($option) { return $option['id']; },
            function ($option) { return $option['content']; }
        );
        $result = '<div><label for="id">label</label>'
            . '<select id="id" name="id"><option value="1">one</option>'
            . '<option value="2">two</option></select></div>';
        $this->assertSame($result, generate_form_select(
            'id',
            'label',
            $options
        ));
    }

    public function test_generate_form() {
        $record = new TestRecord();
        $record->one = 1;
        $record->two = 2;
        $result = '<form action="" method="POST">' . "\n"
            . '<div><label for="one">one</label>'
            . '<input id="one" type="text" name="one" value="1" />'
            . '</div>' . "\n"
            . '<div><label for="two">two</label>'
            . '<input id="two" type="text" name="two" value="2" />'
            . '</div>' . "\n"
            . '<div><input type="submit" value="save"></div></form>' . "\n";
        $this->assertSame($result, generate_form($record));
    }
}
