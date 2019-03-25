<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 15.01.18
 * Time: 19:45
 */

require_once __DIR__ . '/../../includes/html/utils.php';

class Record
{
    /*
     * Records represent records in the database (single lines of a table) or
     * a Tuple in terms of Relational Calculus.  Records are also designed to
     * be used with PDO.  PDO::FETCH_CLASS does not allow to define an explicit
     * constructor.  Therefore a class that is derived from Record has to
     * implement all column names, reflecting the original column names letter
     * by letter.
     */
    public function __construct()
    {
    }

    public static function get_field_names() : array {
        return array_keys(get_class_vars(static::class));
    }

    public function get_fields() : array  {
        return get_object_vars($this);
    }

    public static function construct_by_alien_array(array $array) : Record {
        $record = new static();
        $properties = self::get_field_names();
        foreach ($properties as $property) {
            $record->$property = $array[$property] ?? '';
        }
        return $record;
    }
}