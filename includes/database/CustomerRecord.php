<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 14.01.18
 * Time: 00:04
 */

require_once __DIR__ . '/Record.php';

class CustomerRecord extends Record
{
    public const _table = 'customers';
    public const _column = 'customer_id';
    public $id = '';
    public $gender = '';
    public $title = '';
    public $forename = '';
    public $surname = '';
    public $company = '';
    public $street = '';
    public $city = '';
    public $country = '';
    public $vatin = '';

}
