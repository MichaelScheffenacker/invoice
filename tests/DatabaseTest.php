<?php

require_once __DIR__ . '/../includes/database/Database.php';

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {

    private $db;

    public function __construct() {
        parent::__construct();
        $this->db = new Database();
    }

    public function test_select_last_record() {
        $invoice = $this->db->select_last_record($this->db->invoice_table);
        $this->assertInstanceOf('InvoiceRecord', $invoice);
    }

    public function test_select_last_record_id() {
        $id = $this->db->select_last_record_id($this->db->invoice_table);
        $this->assertIsNumeric($id);
    }

}
