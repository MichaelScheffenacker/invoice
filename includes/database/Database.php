<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 15.01.18
 * Time: 03:21
 */

require_once 'utils.php';
require_once 'CustomerRecord.php';
require_once 'InvoiceRecord.php';
require_once 'LineItemRecord.php';
require_once 'config.php';

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
        $properties = get_object_vars($customer);
        $prop_names = array_keys($properties);
        $insert_columns_string = implode(", ", array_map_wrap('`', $prop_names));
        $insert_place_h = array_map_prefix(':ins_', $prop_names);
        $insert_place_h_string = implode(", ", $insert_place_h);
        $insert_array = array_combine($insert_place_h, $properties);

        $update_prop = array_diff_key($properties, array('id' => null));
        $update_prop_names = array_keys($update_prop);
        $update_columns = array_map_wrap('`', $update_prop_names);
        $update_place_h = array_map_prefix(':up_', $update_prop_names);
        $update_string = implode(", ", array_map_meld('=', $update_columns, $update_place_h));
        $update_array = array_combine($update_place_h, $update_prop);

        $execute_array = array_merge($insert_array, $update_array);

        $stmt = $this->pdo->prepare(
            "INSERT INTO customers ($insert_columns_string) VALUES ($insert_place_h_string) ".
            " ON DUPLICATE KEY UPDATE $update_string"
        );
        $stmt->execute($execute_array);
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
