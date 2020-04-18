<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 31.01.18
 * Time: 21:49
 */

/*
 * A prepared statement can only handle different identifiers
 * in an combined statement, like the upsert statement, even
 * if they represent the same value. This means the identifiers
 * of an upsert have to be prefixed for for the insert part and
 * for the update part. But of course those identifiers have to
 * have matching prefixes in the SQL statement and the execute
 * array for the prepared statement.
 *
 * tl;dr Just use the prefix constants for the upsert.
 */

const insert_prefix = ':ins_';
const update_prefix = ':up_';

//    todo: whitelist $table names
class Table {
    public $name;
    public $class;
    public $column;
    public function __construct($name, $class, $column) {
        $this->name = $name;
        $this->class = $class;
        $this->column;
    }
}

function generate_upsert_sql(Record $record) : string {
    /*
     * Upserts utilize the id (primary key) in the insert part
     * to identify possible duplicates and forward the request
     * to the update part. But in this case the update part does
     * require not to include the id. (Absurdly this is just the 
     * opposite from what insert and update alone would require.)
     */

    $table = $record::_table;
    $fields = $record->get_fields();
    $update_fields = subtract_id($fields);
    
    $columns_string = implode_columns($fields);
    $insert_string = implode_insert_place_holders($fields);
    $update_string = implode_update_fields($update_fields);
    return
        "INSERT INTO $table ($columns_string) VALUES ($insert_string) "
        . "ON DUPLICATE KEY UPDATE $update_string;";
}

function generate_insert_sql(Record $record) : string {
    /*
     * Inserts ignore the id (primary key), they utilize 
     * auto increment.
     */
    $table = $record::_table;
    $fields = subtract_id($record->get_fields());
    $columns_string = implode_columns($fields);
    $insert_string = implode_insert_place_holders($fields);
    return
        "INSERT INTO $table ($columns_string) VALUES ($insert_string)";
}

function create_upsert_execute_array(Record $record) : array {
    $fields = $record->get_fields();
    $update_fields = subtract_id($fields);
    
    $insert_array = create_execute_array(insert_prefix, $fields);
    $update_array = create_execute_array(update_prefix, $update_fields);
    $execute_array = array_merge($insert_array, $update_array);
    return $execute_array;
}

function create_insert_execute_array(Record $record) : array {
    $fields = subtract_id($record->get_fields());
    return create_execute_array(insert_prefix, $fields);
}

function create_execute_array(string $prefix, array $fields) : array {
    $columns = array_keys($fields);
    $place_holders = array_map_prefix($prefix, $columns);
    return array_combine($place_holders, $fields);
}

function implode_columns(array $fields) : string {
    $columns = array_keys($fields);
    $quoted_field_names = quote_columns($columns);
    return implode(', ', $quoted_field_names);
}

function implode_insert_place_holders(array $fields): string {
    $fields_names = array_keys($fields);
    $identifiers = array_map_prefix(insert_prefix, $fields_names);
    return implode(', ', $identifiers);
}

function implode_update_fields(array $fields): string {
    $columns = array_keys($fields);
    $quoted_columns = quote_columns($columns);
    $place_holders = array_map_prefix(update_prefix, $columns);
    $update_fields = array_map_meld('=', $quoted_columns, $place_holders);
    return implode(", ", $update_fields);
}


function subtract_id(array $fields): array {
    return array_diff_key($fields, ['id' => null]);
}

function quote_columns(array $columns) : array {
    return array_map_wrap('`', $columns);
}

function prefix_identifiers(array $fields): array {
    return array_map_prefix(':ins_', $fields);
}


function array_map_meld(string $glue, array $array1, array $array2) : array {
    return array_map(
        function ($el1, $el2) use ($glue) { return $el1 . $glue . $el2; },
        $array1,
        $array2
    );
}

function array_map_prefix(string $prefix, array $array) : array {
    return array_map(
        function ($el) use ($prefix) { return $prefix . $el; },
        $array
    );
}

function array_map_wrap(string $wrapper, array $array) : array {
    return array_map (
        function ($el) use ($wrapper) { return $wrapper . $el . $wrapper; },
        $array
    );
}
