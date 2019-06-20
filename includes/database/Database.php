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

    public function __construct()
    {
        $config = new Config();
        $this->pdo = new PDO(
            $config->database['dsn'],
            $config->database['username'],
            $config->database['passwd']
        );
    }

    public function select_records(string $table) : array {
        $sql = "SELECT * FROM $table";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':table' => $table]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, $table);
        return $stmt->fetchAll();
    }

    public function select_record_by_id(string $table, int $id) : Record {
        $sql = "SELECT * FROM $table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, $table);
        return $stmt->fetch();
    }

    public function upsert_record(string $table, Record $record) : void {
        $sql = generate_upsert_sql($table, $record);
        $execute_array = create_upsert_execute_array($record);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($execute_array);
    }

    public function select_last_record_id(string $table): Record {
        $sql = "SELECT id FROM $table ODER BY id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $table);
        return $stmt->fetch()['id'];
    }

    public function get_invoices() {
        $result = $this->pdo->prepare('SELECT * FROM invoices ORDER BY invoice_number DESC ');
        $result->execute();
        $result->setFetchMode(PDO::FETCH_CLASS, 'InvoiceRecord');
        $invoices = $result->fetchAll();
        return $invoices;
    }

    public function get_invoice_by_id($id) {
        $stmt = $this->pdo->prepare('SElECT * FROM invoices WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'InvoiceRecord');
        /* @var $invoice InvoiceRecord */
        $invoice = $stmt->fetch();
        return $invoice;
    }

    public function upsert_invoice($id, $invoice_number, $invoice_date, $customer_id, $reference) {
        $stmt = $this->pdo->prepare(
            'INSERT INTO invoices (id, invoice_number, invoice_date, customer_id, reference) ' .
            '             VALUES (:id, :ins_invoice_number, :ins_invoice_date, :ins_customer_id, :ins_reference)' .
            '         ON DUPLICATE KEY UPDATE invoice_number=:up_invoice_number, invoice_date=:up_invoice_date,' .
            '             customer_id=:up_customer_id, reference=:up_reference'
        );

        $stmt->execute(array(
            ':id' => $id,
            ':ins_invoice_date' => $invoice_date,
            ':ins_invoice_number' => $invoice_number,
            ':ins_customer_id' => $customer_id,
            ':ins_reference' => $reference,
            ':up_invoice_date' => $invoice_date,
            ':up_invoice_number' => $invoice_number,
            ':up_customer_id' => $customer_id,
            ':up_reference' => $reference
        ));
        return $stmt->errorInfo();
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
        $this->upsert_record('customer', $customer);
    }

    public function get_last_customer_id() {
        $stmt = $this->pdo->prepare('SELECT id FROM customers ORDER BY id DESC LIMIT 1');
        $stmt->execute();
        $last_customer_id = $stmt->fetch()['id'];
        return $last_customer_id;
    }

    public function get_lineitem_by_invoice_id($invoice_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM lineitems WHERE invoice_id = :invoice_id');
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

    public function insert_lineitem($invoice_id, $description, $price) {
        $stmt = $this->pdo->prepare(
            'INSERT INTO lineitems (invoice_id, description, price) ' .
            'VALUES (:invoice_id, :description, :price)'
        );
        $stmt->bindParam(':invoice_id', $invoice_id);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
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
