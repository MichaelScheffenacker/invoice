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
Michael Scheffenacker & | & Klosterstraße 1, 5450 Werfen & | &  +43\,650\,980\,89\,85\, & | & michael.scheffenacker@gmail.com & | & Hypo Tirol Bank, BIC: HYPTAT22, IBAN: AT765700030053302332
\end{tabularx}}
\cfoot[]{}
\ofoot[]{}

\begin{document}
\shorthandoff{"}\newcolumntype{R}{>{\raggedleft\arraybackslash}X}%
\begin{tabularx}{\textwidth}{@{} l R @{}}

\LARGE{Michael Scheffenacker} & \LARGE{Rechnung} \\

\hline \\

\normalsize 
Klosterstraße 1 & Rechnung Nr. <?php echo $invoice_number ?> \\
5450 Werfen & <?php echo $date ?> \\
 +43\,650\,980\,89\,85\,& UID: ATU67000639 \\
 michael.scheffenacker@gmail.com \\
 \end{tabularx}

\begin{tabularx}{\textwidth}{@{} p{10cm}l @{}}
\textit{Rechnung an:} & \textit{Für:}\\
<?php echo $address_first_lines ?>
<?php echo $customer->street ?>  &\\
<?php echo $customer->city ?> &\\
<?php echo $customer->country ?> &\\
<?php echo $vatin ?>
\end{tabularx}


\vspace*{0cm}Sehr <?php echo $salutation ?>,\\
wie vereinbart erlaube ich mir,
erbrachte Leistungen wie folgt in Rechnung zu stellen:

\vspace*{.6cm}

\begin{tabularx}{\textwidth}{@{} l p{8cm} l R @{}}
Leistung & & & Betrag \\
\hline
<?php echo $invoice_items ?>

\hline
Summe Netto & & & <?php echo $f_sum_net ?> \\
 <?php echo $tax_percent ?>\,\% USt.&  &  & <?php echo $f_tax ?> \\
 \hline
 & \textbf{Summe brutto} & \textbf{<?php echo $f_sum_gross ?>} &  \\
 \end{tabularx}

 \vspace*{1cm}
 
{Hiermit stelle ich Ihnen den Betrag von <?php echo $f_sum_gross ?> in Rechnung.
Zahlbar innerhalb von 14 Tagen.
<?php echo $reverse_charge_text ?>}


Kontoinhaber: Michael Scheffenacker\\
Bankinstitut: Hypo Tirol Bank\\
IBAN: AT765700030053302332\\
BIC: HYPTAT22\\

Mit freundlichen Grüßen

\end{document}