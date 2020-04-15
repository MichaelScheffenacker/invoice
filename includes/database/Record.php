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

    protected const _table_name = null;  // self::NAME workaround to force definition in derived classes
    protected static $_table;
    protected static $_db;

    public $id;

    private static function init() {
        if (static::$_table == null) {
            static::$_table = new Table(static::_table_name, static::class);
            self::$_db = new Database();
        }
    }

    public static function select_all() : array {
        self::init();
        return self::$_db->select_records(static::$_table);
    }

    public static function construct_from_id(int $id) : Record {
        self::init();
        return self::$_db->select_record_by_id(static::$_table, $id);
    }

    public static function construct_from_alien_array(array $array) : Record {
        self::init();
        $record = new static();
        $properties = self::get_field_names();
        foreach ($properties as $property) {
            $record->$property = $array[$property] ?? '';
        }
        return $record;
    }

    public static function construct_new() : Record {
        self::init();
        $record = new static();
        $record->id = self::select_last_id() + 1;
        return $record;
    }

    public static function get_field_names() : array {
        self::init();
        $fields = get_class_vars(static::class);
        return array_keys(self::remove_privates($fields));
    }

    public static function select_last_id() : int {
        self::init();
        return self::$_db->select_last_record_id(static::$_table);
    }

    private static function remove_privates(array $fields) : array {
        $excludees = [
            '_table_name'=>null,
            '_table'=>null,
            '_db'=>null
        ];
        return array_diff_key($fields, $excludees);
    }


    public function __construct() {
        self::init();
    }

    public function insert() : void {
        self::$_db->insert_record(static::$_table, $this);
    }

    public function upsert() : void {
        self::$_db->upsert_record(static::$_table, $this);
    }

    public function get_fields() : array  {
        $fields = get_object_vars($this);
        return self::remove_privates($fields);
    }

    public function set_new_id() : void {
        $this->id = self::select_last_id() + 1;
    }
}


