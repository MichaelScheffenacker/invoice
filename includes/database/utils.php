<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 31.01.18
 * Time: 21:49
 */


function generate_upsert_sql(string $table, Record $record) {
    $columns_string = implode_column_names($record);
    $insert_string = implode_insert_place_holders($record);
    $update_string = implode_update_fields($record);
//    todo: whitelist $table names
    return
        "INSERT INTO $table ($columns_string) VALUES ($insert_string) "
        . "ON DUPLICATE KEY UPDATE $update_string";
}

function create_upsert_execute_array(Record $record) : array {
    $insert_array = create_insert_execute_array($record);
    $update_array = create_update_execute_array($record);
    $execute_array = array_merge($insert_array, $update_array);
    return $execute_array;
}

function create_insert_execute_array(Record $record) : array {
    $place_holders = create_prefixed_insert_fields($record);
    $values = create_insert_values($record);
    return array_combine($place_holders, $values);
}

function create_update_execute_array(Record $record) : array {
    $place_holders = create_prefixed_update_fields($record);
    $values = create_update_values($record);
    return array_combine($place_holders, $values);
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

function implode_column_names(Record $record) : string {
    $field_names = $record::get_field_names();
    $quoted_field_names = quote_columns($field_names);
    return implode(', ', $quoted_field_names);
}

function quote_columns(array $columns) : array {
    return array_map_wrap('`', $columns);
}

function implode_insert_place_holders(Record $record): string {
    $fields_names = create_raw_insert_fields($record);
    $identifiers = prefix_insert_identifiers($fields_names);
    return implode(', ', $identifiers);
}

function create_raw_insert_fields(Record $record): array {
    return $record::get_field_names();
}

function create_insert_values(Record $record): array {
    return$record->get_fields();
}

function prefix_insert_identifiers(array $fields): array {
    return array_map_prefix(':ins_', $fields);
}

function create_prefixed_insert_fields(Record $record) {
    $field_names = create_raw_insert_fields($record);
    return prefix_insert_identifiers($field_names);
}

function prefix_update_identifiers(array $fields): array {
    return array_map_prefix(':up_', $fields);
}

function implode_update_fields(Record $record): string {
    $update_fields = create_update_fields($record);
    return implode(", ", $update_fields);
}

function create_raw_update_fields(Record $record): array {
    $fields = create_filtered_update_fields($record);
    return array_keys($fields);
}

function create_update_values(Record $record) {
    $fields = create_filtered_update_fields($record);
    return array_values($fields);
}

function create_filtered_update_fields(Record $record): array {
    $fields = $record->get_fields();
    return array_diff_key($fields, ['id' => null]);
}

function create_update_fields(Record $record) : array {
    $columns = create_quoted_columns($record);
    $place_holders = create_prefixed_update_fields($record);
    return array_map_meld('=', $columns, $place_holders);
}

function create_quoted_columns(Record $record) {
    $field_names = create_raw_update_fields($record);
    return quote_columns($field_names);
}

function create_prefixed_update_fields(Record $record) {
    $field_names = create_raw_update_fields($record);
    return prefix_update_identifiers($field_names);
}
