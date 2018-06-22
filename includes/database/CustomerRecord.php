<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 14.01.18
 * Time: 00:04
 */

require_once 'Record.php';
require_once 'utils.php';
require_once 'includes/html/utils.php';

class CustomerRecord extends Record
{
    public $customer_id = '';
    public $gender = '';
    public $name_first = '';
    public $name_last = '';
    public $title = '';
    public $company = '';
    public $street = '';
    public $city = '';
    public $country = '';
    public $phone_office = '';
    public $phone_mobile = '';
    public $mail = '';
    public $uid = '';
    public $vat = '';

}
