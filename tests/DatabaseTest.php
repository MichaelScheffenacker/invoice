<?php

require_once __DIR__ . '/../includes/database/Database.php';
require_once __DIR__ . '/../includes/database/InvoiceRecord.php';
require_once __DIR__ . '/TestRecord.php';
require_once __DIR__ . '/RogueInvoiceRecord.php';

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {

    private $db;

    public function __construct() {
        parent::__construct();
        $this->db = new Database();
    }

    public function test_select_records() {
        $invoices = $this->db->select_records($this->db->invoice_table);
        $this->assertInstanceOf('InvoiceRecord', $invoices[0]);
    }

    public function test_select_record_by_id() {
        $table = $this->db->invoice_table;
        $invoice = $this->db->select_record_by_id($table, 1);
        $this->assertInstanceOf('InvoiceRecord', $invoice);
    }

    public function test_select_last_record() {
        $invoice = $this->db->select_last_record($this->db->invoice_table);
        $this->assertInstanceOf('InvoiceRecord', $invoice);
    }

    public function test_select_last_record_id() {
        $id = $this->db->select_last_record_id($this->db->invoice_table);
        $this->assertIsNumeric($id);
    }

    public function test_insert_record() {
        $invoice = InvoiceRecord::construct_from_alien_array([
            'invoice_number' => '5',
            'invoice_date' => '2020-01-01',
            'customer_id' => '1',
            'reference' => 'test2'
        ]);
        $this->db->insert_record($this->db->invoice_table, $invoice);
        $this->assertSame(1, 1);
    }

    public function test_insert_lineitem() {
        $db = new Database();
        $lineitem = LineItemRecord::construct_from_alien_array([
            'invoice_id' => '1',
            'description' => 'unit_test',
            'price' => '333'
        ]);
        $db->insert_lineitem($lineitem);
        $this->assertSame(1, 1);
    }

    public function test_rogue_field() {
        $table = new Table('invoices', 'RogueInvoiceRecord');
        $invoice = $this->db->select_records($table)[0];
        /** @var RogueInvoiceRecord $invoice */
        $this->assertEquals(1,$invoice->id);
        $this->assertEquals(3, $invoice->reference);
        $this->assertEquals(4, $invoice->rogue_field);
    }

    public function test_get_field_names() {
        $expected = ['one', 'two'];
        $result = TestRecord::get_field_names();
        $this->assertEquals($expected, $result);
    }

    public function test_get_fields() {
        $expected = ['one'=>null, 'two'=>null];
        $record = new TestRecord();
        $result = $record->get_fields();
        $this->assertEquals($expected, $result);
    }

}
