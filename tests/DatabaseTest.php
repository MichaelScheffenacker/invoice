<?php

require_once __DIR__ . '/../includes/database/Database.php';
require_once __DIR__ . '/../includes/database/InvoiceRecord.php';

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
        $invoice = InvoiceRecord::construct_by_alien_array([
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
        $lineitem = LineItemRecord::construct_by_alien_array([
            'invoice_id' => '1',
            'description' => 'unit_test',
            'price' => '333'
        ]);
        $db->insert_lineitem($lineitem);
        $this->assertSame(1, 1);
    }

}
