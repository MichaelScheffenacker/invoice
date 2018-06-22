<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 14.01.18
 * Time: 22:57
 */

require 'includes/html/head.php';

$file_name = 'test';
$tex_file_name = $file_name . '.tex';
$pdf_file_name = $file_name . '.pdf';

$relative_path = '/latex/';
$latex_directory = __DIR__ . $relative_path;
$tex_file_path = $latex_directory . $tex_file_name;
$pdf_file_path = $latex_directory . $pdf_file_name;
$pdf_file_url = '.' . $relative_path . $pdf_file_name;

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
    echo "Writing of <code>$tex_file_path</code> file failed.\n";
}

print "<a href='$pdf_file_url'>$pdf_file_name</a>";

$command = "/usr/bin/pdflatex -output-directory=$latex_directory $tex_file_path 2>&1";
try {
    echo "\n\n<pre> \n";
    echo "command: $command \n\n";
    echo "output of system($command): \n >>>>> \n";
    echo system($command);
    echo "\n <<<<< \n</pre>\n\n";
}
catch (Exception $e) {
    echo "<code>pdflatex</code> failed, command:<code>$command</code>\n";
}

require 'includes/html/tail.php';
