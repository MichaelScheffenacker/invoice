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
        $invoices = InvoiceRecord::select_all();
        $this->assertInstanceOf('InvoiceRecord', $invoices[0]);
    }

    public function test_select_record_by_id() {
        $invoice = InvoiceRecord::construct_from_id(1);
        $this->assertInstanceOf('InvoiceRecord', $invoice);
    }

    public function test_select_last_record_id() {
        $id = InvoiceRecord::select_last_id();
        $this->assertIsNumeric($id);
    }

//    public function test_insert_record() {
//        $invoice = InvoiceRecord::construct_from_alien_array([
//            'invoice_number' => '5',
//            'invoice_date' => '2020-01-01',
//            'customer_id' => '1',
//            'reference' => 'test2'
//        ]);
//        $invoice->insert();
//        $this->assertSame(1, 1);
//    }

//    public function test_insert_lineitem() {
//        $db = new Database();
//        $lineitem = LineItemRecord::construct_from_alien_array([
//            'invoice_id' => '1',
//            'description' => 'unit_test',
//            'price' => '333'
//        ]);
//        $db->insert_lineitem($lineitem);
//        $this->assertSame(1, 1);
//    }

    public function test_rogue_field() {
        $invoice = $this->db->select_records(RogueInvoiceRecord::class)[0];
        /** @var RogueInvoiceRecord $invoice */
        $this->assertEquals(1,$invoice->id);
        $this->assertEquals(3, $invoice->reference);
        $this->assertEquals(4, $invoice->rogue_field);
    }

    public function test_get_field_names() {
        $expected = ['one', 'two', 'id'];
        $result = TestRecord::get_field_names();
        $this->assertEquals($expected, $result);
    }

    public function test_get_fields() {
        $expected = ['one'=>null, 'two'=>null, 'id'=>null];
        $record = new TestRecord();
        $result = $record->get_fields();
        $this->assertEquals($expected, $result);
    }

    public function test_parent_static_relation() {
        $invoice = InvoiceRecord::construct_new();
        $this->assertEquals('InvoiceRecord', get_class($invoice));
        $customer = CustomerRecord::construct_new();
        $this->assertEquals('CustomerRecord', get_class($customer));

        $invoice = InvoiceRecord::construct_from_id(1);
        $this->assertEquals('InvoiceRecord', get_class($invoice));
        $customer = CustomerRecord::construct_from_id(1);
        $this->assertEquals('CustomerRecord', get_class($customer));
    }

}
