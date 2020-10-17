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

    public function select_records($Record) : array {
        /** @var Record $Record */
        $table = $Record::_table;
        $sql = "SELECT * FROM $table";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $Record);
        return $stmt->fetchAll();
    }

    public function select_record_by_id($Record, int $id) : Record {
        /** @var Record $Record */
        $table = $Record::_table;
        $sql = "SELECT * FROM $table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, $Record);
        return $stmt->fetch();
    }

    public function insert_record(Record $record) {
        $sql = generate_insert_sql($record);
        $execute_array = create_insert_execute_array($record);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($execute_array);
    }

    public function upsert_record(Record $record) : void {
        $sql = generate_upsert_sql($record);
        $execute_array = create_upsert_execute_array($record);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($execute_array);
        print_r($sql);
    }

    public function select_last_record($Record) {
        /** @var Record $Record */
        $table = $Record::_table;
        $sql = "SELECT id FROM $table ORDER BY id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $Record);
        return $stmt->fetch();
    }

    public function select_last_record_id($Record) : int {
        return $this->select_last_record($Record)->id;
    }

    public function get_last_invoice_number() {
        $stmt_string = 'SELECT invoice_number FROM invoices ORDER BY invoice_number DESC LIMIT 1';
        $stmt = $this->pdo->prepare($stmt_string);
        $stmt->execute();
        return $stmt->fetch()['invoice_number'];
    }

    public function select_wards_by_keeper($Ward, Record $keeper) {
        /** @var Record $Ward */
        $ward_table = $Ward::_table;
        $keeper_column = $keeper::_column;
        $sql = "SELECT * FROM $ward_table WHERE $keeper_column = :keeper_id ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':keeper_id', $keeper->id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $Ward);
        return $stmt->fetch();
    }

    public function get_lineitems_by_invoice_id($invoice_id) {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM lineitems WHERE invoice_id = :invoice_id'
        );
        $stmt->bindParam(':invoice_id', $invoice_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'LineItemRecord');
        $tasks = $stmt->fetchAll();
        return $tasks;
    }

    public function delete_wards_by_keeper($Ward, Record $keeper) {
        /** @var Record $Ward */
        $ward_table = $Ward::_table;
        $keeper_column = $keeper::_column;
        $sql = "DELETE FROM $ward_table WHERE $keeper_column = :keeper_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':keeper_id', $keeper->id);
        $stmt->execute();
    }

    public function delete_lineitem_by_invoice_id($invoice_id) {
        $stmt = $this->pdo->prepare('DELETE FROM lineitems WHERE invoice_id = :invoice_id');
        $stmt->bindParam(':invoice_id',$invoice_id);
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
