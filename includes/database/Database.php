<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 15.01.18
 * Time: 03:21
 */

require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/CustomerRecord.php';
require_once __DIR__ . '/InvoiceRecord.php';
require_once __DIR__ . '/LineItemRecord.php';
require_once __DIR__ . '/../../config.php';



class Database
{
    private $pdo;
    public $invoice_table;
    public $lineitem_table;
    public $customer_table;

    public function __construct()
    {
        $config = new Config();
        $this->pdo = new PDO(
            $config->database['dsn'],
            $config->database['username'],
            $config->database['passwd']
        );

        $this->customer_table = new Table(
            'customers',
            'CustomerRecord'
        );

        $this->invoice_table = new Table(
            'invoices',
            'InvoiceRecord'
        );

        $this->lineitem_table = new Table(
            'lineitems',
            'LineItemRecord'
        );

    }

    public function select_records(Table $table) : array {
        $sql = "SELECT * FROM $table->name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $table->class);
        return $stmt->fetchAll();
    }

    public function select_record_by_id(Table $table, int $id) : Record {
        $sql = "SELECT * FROM $table->name WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, $table->class);
        return $stmt->fetch();
    }

    public function insert_record(Table $table, Record $record) {
        $sql = generate_insert_sql($table, $record);
        $execute_array = create_insert_execute_array($record);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($execute_array);
    }

    public function upsert_record(Table $table, Record $record) : void {
        $sql = generate_upsert_sql($table, $record);
        $execute_array = create_upsert_execute_array($record);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($execute_array);
    }

    public function select_last_record(Table $table) {
        $sql = "SELECT id FROM $table->name ORDER BY id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'InvoiceRecord');
        return $stmt->fetch();
    }

    public function select_last_record_id(Table $table) : int {
        return $this->select_last_record($table)->id;
    }

    public function get_invoices() {
//        'SELECT * FROM invoices ORDER BY invoice_number DESC '
        return $this->select_records($this->invoice_table);
    }

    public function get_invoice_by_id($id) {
        return $this->select_record_by_id($this->invoice_table, $id);
    }

    public function upsert_invoice(InvoiceRecord $invoice) {
        $this->upsert_record($this->invoice_table, $invoice);
    }

    public function get_last_invoice_id() {
        $stmt = $this->pdo->prepare('SELECT id FROM invoices ORDER BY id DESC LIMIT 1');
        $stmt->execute();
        $last_invoice_id = $stmt->fetch()['id'];
        return $last_invoice_id;
    }

    public function get_last_invoice_number() {
        $stmt_string = 'SELECT invoice_number FROM invoices ORDER BY invoice_number DESC LIMIT 1';
        $stmt = $this->pdo->prepare($stmt_string);
        $stmt->execute();
        $last_invoice_number = $stmt->fetch()['invoice_number'];
        return $last_invoice_number;
    }

    public function get_customers() {
        $stmt = $this->pdo->prepare('SELECT * FROM customers');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'CustomerRecord');
        $customers = $stmt->fetchAll();
        return $customers;
    }

    public function get_customer_by_id($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM customers WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'CustomerRecord');
        /* @var $customer CustomerRecord */
        $customer = $stmt->fetch();
        return $customer;
    }

    public function upsert_customer(CustomerRecord $customer) {
        $this->upsert_record($this->customer_table, $customer);
    }

    public function get_last_customer_id() {
        $stmt = $this->pdo->prepare('SELECT id FROM customers ORDER BY id DESC LIMIT 1');
        $stmt->execute();
        $last_customer_id = $stmt->fetch()['id'];
        return $last_customer_id;
    }

    public function get_lineitem_by_invoice_id($invoice_id) {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM lineitems WHERE invoice_id = :invoice_id'
        );
        $stmt->bindParam(':invoice_id', $invoice_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'LineItemRecord');
        $tasks = $stmt->fetchAll();
        return $tasks;
    }

    public function delete_lineitem_by_invoice_id($invoice_id) {
        $stmt = $this->pdo->prepare('DELETE FROM lineitems WHERE invoice_id = :invoice_id');
        $stmt->bindParam(':invoice_id',$invoice_id);
        $stmt->execute();
    }

    public function insert_lineitem(LineItemRecord $record) {
        $this->insert_record($this->lineitem_table, $record);
    }

    public function execute($sql) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
    }

    public function lookup_name_by_identifier(string $identifier): string {
        /*
         * This is a hardcoded lookup table. On occasion it should be moved to
         * database; at least if there is demand for runtime manageability.
         */
        $lookup_table = array(
            'id' => 'ID',
            'gender' => 'Geschlecht',
            'forename' => 'Vorname',
            'surname' => 'Nachname',
            'title' => 'Titel',
            'company' => 'Firma',
            'street' => 'Straße',
            'city' => 'Stadt',
            'country' => 'Land',
            'vatin' => 'UID',
            'invoice_number' => '№',
            'invoice_date' => 'Datum',
            'reference' => 'Referenz',
            'price' => 'Betrag',
        );

        if (isset($lookup_table[$identifier])) {
            return $lookup_table[$identifier];
        }
        else if (is_numeric($identifier)){
            return '';
        }
        else {
            return $identifier;
        }
    }

}
