<?php

require_once __DIR__ . '../database/Database.php';


function handle_record_call(string $record_class, string $table) : Record {
    $db = new Database();

    if (array_key_exists('id', $_POST)) {
        $record = create_record($record_class, $table, $db);
    } else {
        if (array_key_exists('id', $_GET)) {
            $record = read_record($table, $db);
        } else {
            $record = new_Record($record_class, $table, $db);
        }
    }
    return $record;
}

/**
 * @param string $record_class
 * @param string $table
 * @param Database $db
 * @return Record
 */
function new_Record(string $record_class, string $table, Database $db): Record {
    $record = new $record_class();
    $record->id = $db->select_last_record_id($table);
    return $record;
}

/**
 * @param string $table
 * @param Database $db
 * @return Record
 */
function read_record(string $table, Database $db): Record {
    $record = $db->select_record_by_id($table, $_GET['id']);
    return $record;
}

/**
 * @param string $record_class
 * @param string $table
 * @param Database $db
 * @return Record
 */
function create_record(
    string $record_class,
    string $table,
    Database $db
): Record {
    /**@var Record $record_class */
    $record = $record_class::construct_from_alien_array($_POST);
    $db->upsert_record($table, $record);
    return $record;
}
