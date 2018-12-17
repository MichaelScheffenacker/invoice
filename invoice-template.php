<?php

require_once 'includes/database/Database.php';
$db = new Database();

$invoice_id = $_GET['invoice_id'] ?? $db->get_last_invoice_id();
$invoice = $db->get_invoice_by_id($invoice_id);
$customer = $db->get_customer_by_id($invoice->customer_id);
$tasks = $db->get_lineitem_by_invoice_id($invoice_id);


function eur_price($price) {
    $f_price = number_format($price, 2, ',', '.');
    return "€\,$f_price";
}

$full_name = "$customer->title $customer->forename $customer->surname";
$purpose = $invoice->reference;
$date = date("j.n.Y", strtotime($invoice->invoice_date));
$vatin = ($customer->vatin != "") ? "UID: $customer->vatin" : "";
if ($customer->gender = 'x') {
    $salutation = 'geehrte Damen und Herren';
}
else {
    $salutation = ($customer->gender == 'm') ? 'geehrter Herr' : 'geehrte Frau ' . $customer->surname;
}
$invoice_items = '';
$sum_net = 0;
/* @var $task LineItemRecord */
foreach ($tasks as $task) {
    $sum_net += $task->price;
    $price = eur_price($task->price);
    $invoice_items .= "\multicolumn{2}{@{}l@{}}{ $task->description } &  & $price \\\\\n";
}
$tax = $sum_net * 0.2;
$sum_gross = $sum_net * 1.2;
$f_tax = eur_price($tax);
$f_sum_net = eur_price($sum_net);
$f_sum_gross = eur_price($sum_gross);
?>
\documentclass[a4paper,fontsize=11pt,parskip=full-]{scrlttr2}
\usepackage[ngerman]{babel}
\usepackage{ucs}
\usepackage{mathpazo}
\usepackage[scaled=.95]{helvet}
\usepackage{courier}

\renewcommand*\familydefault{\sfdefault}
\usepackage[T1]{fontenc}
\usepackage[utf8x]{inputenc}
\usepackage[automark]{scrpage2}
\usepackage[onehalfspacing]{setspace}
\usepackage{eurosym}
\usepackage{geometry}
\geometry{left=20mm,right=20mm,top=25mm,bottom=35mm}
\usepackage{tabularx}
\pagestyle{scrheadings}
\setkomafont{pageheadfoot}{%
\normalfont\normalcolor\tiny 
}
\newcolumntype{Y}{>{\centering}X}

\ihead[]{}
\chead[]{}
\ohead[]{}
\ifoot[]{\renewcommand\tabcolsep{0pt}
\begin{tabularx}{\textwidth}{@{} lYlYlYlYl @{}}
Michael Scheffenacker & | & Klosterstraße 1, 5450 Werfen & | &  +43\,650\,980\,89\,85\, & | & michael.scheffenacker@formiculare.org & | & Hypo Tirol Bank, BIC: HYPTAT22, IBAN: AT765700030053302332
\end{tabularx}}
\cfoot[]{}
\ofoot[]{}

\begin{document}
\shorthandoff{"}\newcolumntype{R}{>{\raggedleft\arraybackslash}X}%
\begin{tabularx}{\textwidth}{@{} l R @{}}

\LARGE{Michael Scheffenacker} & \LARGE{Rechnung} \\

\hline \\

\normalsize 
Klosterstraße 1 & Rechnung Nr. <?php echo $invoice->invoice_number ?> \\
5450 Werfen & <?php echo $date ?> \\
 +43\,650\,980\,89\,85\,& UID: ATU67000639 \\
 michael.scheffenacker@formiculare.org \\
 \end{tabularx}

\begin{tabularx}{\textwidth}{@{} p{10cm}l @{}}
\textit{Rechnung an:} & \textit{Für:}\\
<?php echo $customer->company ?>& <?php echo $purpose?>\\
z.\,H.<?php echo $full_name ?> &\\
<?php echo $customer->street ?>  &\\
<?php echo $customer->city ?> &\\
<?php echo $customer->country ?> &\\
<?php echo $vatin ?>
\end{tabularx}


\vspace*{0cm}Sehr <?php echo $salutation ?>,\\
wie vereinbart erlaube ich mir, erbrachte Leistungen wie folgt in Rechnung zu stellen:

\vspace*{.6cm}

\begin{tabularx}{\textwidth}{@{} l p{8cm} l R @{}}
Leistung & & & Betrag \\
\hline
<?php echo $invoice_items ?>

\hline
Summe Netto & & & <?php echo $f_sum_net ?> \\
 20 \% USt.&  &  & <?php echo $f_tax ?> \\
 \hline
 & \textbf{Summe brutto} & \textbf{<?php echo $f_sum_gross ?>} &  \\
 \end{tabularx}

 \vspace*{1cm}
 
Hiermit stelle ich Ihnen den Betrag von <?php echo $f_sum_gross ?> in Rechnung. Zahlbar innerhalb von 14 Tagen.

Kontoinhaber: Michael Scheffenacker\\
Bankinstitut: Hypo Tirol Bank\\
IBAN: AT765700030053302332\\
BIC: HYPTAT22\\

Mit freundlichen Grüßen

\end{document}