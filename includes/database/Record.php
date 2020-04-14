<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 15.01.18
 * Time: 19:45
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../../includes/html/utils.php';

abstract class Record
{
    /*
     * Records represent records in the database (single lines of a table) or
     * a Tuple in terms of Relational Calculus.  Records are also designed to
     * be used with PDO.  PDO::FETCH_CLASS does not allow to define an explicit
     * constructor.  Therefore a class that is derived from Record has to
     * implement all column names, reflecting the original column names letter
     * by letter.
     */

    protected $_table_name;
    protected $_table;
    protected $_db;

    public function __construct()
    {
        $this->_table = new Table($this->_table_name, static::class);
        $this->_db = new Database();
    }

    public function select() : array {
        return $this->_db->select_records($this->_table);
    }

    public function select_by_id(int $id) : Record {
        return $this->_db->select_record_by_id($this->_table, $id);
    }

    public function set_by_id(int $id) : void {
        $record = $this->select_by_id($id);
        $properties = self::get_field_names();
        foreach ($properties as $property) {
            $this->$property = $record->$property;
        }
    }

    public function insert() : void {
        $this->_db->insert_record($this->_table, $this);
    }

    public function upsert() : void {
        $this->_db->upsert_record($this->_table, $this);
    }

    public function select_last_id() : int {
        return $this->_db->select_last_record_id($this->_table);
    }

    public function set_new_id(): void {
        $this->id = $this->select_last_id() + 1;
    }

    public static function get_field_names() : array {
        $fields = get_class_vars(static::class);
        return array_keys(self::remove_privates($fields));
    }

    public function get_fields() : array  {
        $fields = get_object_vars($this);
        return self::remove_privates($fields);
    }

    private static function remove_privates(array $fields) : array {
        $excludees = [
            '_table_name'=>null,
            '_table'=>null,
            '_db'=>null
        ];
        return array_diff_key($fields, $excludees);
    }

    public static function construct_from_alien_array(array $array) : Record {
        $record = new static();
        $properties = self::get_field_names();
        foreach ($properties as $property) {
            $record->$property = $array[$property] ?? '';
        }
        return $record;
    }
}