<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 14.01.18
 * Time: 22:57
 */

require_once 'includes/database/Database.php';
require_once 'config.php';
$error_outputs ='';
$db = new Database();

$invoice_id = $_GET['invoice_id'] ?? $db->get_last_invoice_id();
$invoice = $db->get_invoice_by_id($invoice_id);
$customer = $db->get_customer_by_id($invoice->customer_id);
$linetems = $db->get_lineitem_by_invoice_id($invoice_id);

function eur_price($price) {
    $f_price = number_format($price, 2, ',', '.');
    return "€\,$f_price";
}

function convert_to_file_name_format($str) {
    $str = preg_replace('/[^\w -]/', '', $str);
    $str = preg_replace('/[ ]/', '-', $str);
    return strtolower($str);
}


### template variable definitions ###

// definitions (more or less) in order of appearance in the template

$invoice_number = $invoice->invoice_number;
$company = $customer->company;
$full_name = "$customer->title $customer->forename $customer->surname";
$purpose = $invoice->reference;
$address_first_lines ='';
if ($company) {
    $address_first_lines .= "$company & $purpose \\\\\n";
    $address_first_lines .= "z.\,H. $full_name &\\\\\n";
}
else {
    $address_first_lines .= "$full_name & $purpose \\\\\n";
}
$date = date("j.n.Y", strtotime($invoice->invoice_date));
$vatin = ($customer->vatin != "") ? "UID: $customer->vatin" : "";
$gender = $customer->gender;
$salutation ='';
if ($gender == 'none') {
    $salutation .= 'geehrte Damen und Herren';
}
else {
    $salutation .= ($gender == 'male') ? 'geehrter Herr ' : 'geehrte Frau ';
    $salutation .= $customer->surname;
}
$invoice_items = '';
$sum_net = 0;
/* @var $lineitem LineItemRecord */
foreach ($linetems as $lineitem) {
    $sum_net += $lineitem->price;
    $price = eur_price($lineitem->price);
    $invoice_items .= "\multicolumn{2}{@{}l@{}}{ $lineitem->description } &  & $price \\\\\n";
}
$tax = $sum_net * 0.2;
$sum_gross = $sum_net * 1.2;
$f_tax = eur_price($tax);
$f_sum_net = eur_price($sum_net);
$f_sum_gross = eur_price($sum_gross);


### file name definitions ###
$leading_zeros = str_repeat('0', 3 - floor(log10($invoice_number)));
$file_number = "R$leading_zeros$invoice_number";
$file_surname = convert_to_file_name_format($customer->surname);
$file_reference = convert_to_file_name_format($purpose);
$file_name =
    "$file_number" . '_' .
    "$file_surname" . '_' .
    "$file_reference" . '_' .
    "$invoice->invoice_date";
$tex_file_name = $file_name . '.tex';
$pdf_file_name = $file_name . '.pdf';

$relative_path = '/latex/';
$latex_directory = __DIR__ . $relative_path;
$tex_file_path = $latex_directory . $tex_file_name;
$pdf_file_path = $latex_directory . $pdf_file_name;
$pdf_file_url = '.' . $relative_path . $pdf_file_name;

$config = new Config();


### latex ###

// ob captures the entire output buffer (ob) between start and end. All code
// is executed in php fashion and the result is returned.
ob_start();
require 'invoice-template.php';
$invoice_content_tex = ob_get_contents();
ob_end_clean();

try {
    $file = fopen($tex_file_path, 'w');
    fwrite($file, $invoice_content_tex);
    fclose($file);
}
catch (Exception $e) {
    $error_outputs .= "Writing of <code>$tex_file_path</code> file failed.\n";
}

$command = "$config->pdflatex_path -output-directory=$latex_directory $tex_file_path 2>&1";
$command_output = '';
try {
    exec($command, $command_output_lines);
}
catch (Exception $e) {
    $error_outputs .= "<code>pdflatex</code> failed, command:<code>$command</code>\n";
}


### html ###

require 'includes/html/head.php';

print "<p>";
print generate_html_element(
    'a',
    $pdf_file_name,
    ['href' => $pdf_file_url, 'class' => 'invoice-link']
);
print "</p>";

print "\n\n<pre> \n";
if ($error_outputs){
    print "caught errors:\n";
    print "$error_outputs";
}
print_r($invoice);
print_r($customer);
print_r($linetems);
print "\n\ncommand: $command \n\n";
print "output of exec($command): \n >>>>> \n";
foreach ($command_output_lines as $line) {
    print "$line \n" ;
}
print "\n <<<<< \n</pre>\n\n";

require 'includes/html/tail.php';
