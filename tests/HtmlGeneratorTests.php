<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 24.12.18
 * Time: 03:14
 */

use PHPUnit\Framework\TestCase;
require_once '../includes/html/generic_html_generators.php';

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
            generate_html_attributes(['value' => 1, 'selected' => ''])
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
            generate_html_void_element('img', ['src' => './logo.png'])
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
}
