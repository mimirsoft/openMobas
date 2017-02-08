<?php
//===================================================================================================
// this is the php file which creates the readme.pdf file, this is not seriously 
// suggested as a good way to create such a file, nor a great example of prose,
// but hopefully it will be useful
//
// adding ?d=1 to the url calling this will cause the pdf code itself to ve echoed to the 
// browser, this is quite useful for debugging purposes.
// there is no option to save directly to a file here, but this would be trivial to implement.
//
// note that this file comprisises both the demo code, and the generator of the pdf documentation
//
//===================================================================================================

// don't want any warnings turning up in the pdf code if the server is set to 'anal' mode.
error_reporting(7);

include '../../../framework/class.ezpdf.php';

// I am in NZ, so will design my page for A4 paper.. but don't get me started on that.
// (defaults to legal)
// this code has been modified to use ezpdf.

$pdf = new Cezpdf('letter','portrait');

$pdf -> ezSetMargins(50,70,50,50);

// put a border round all the pages
$all = $pdf->openObject();
$pdf->rectangle(20,40,558,782);
$pdf->closeObject();
// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
// or 'even'.
$pdf->addObject($all,'all');

$pdf->ezSetDy(-100);

// select a font
$pdf->selectFont('../fonts/Helvetica');

$pdf->ezText("PHP Pdf Creation\n",30,array('justification'=>'centre'));
$pdf->ezText("Module-free creation of Pdf documents\nfrom within PHP\n",20,array('justification'=>'centre'));
$pdf->ezText("developed by R&OS Ltd\nhttp://www.ros.co.nz/pdf\n\nversion 0.05",18,array('justification'=>'centre'));

$pdf->ezSetDy(-150);
// modified to use the local file if it can

if (file_exists('ros.jpg')){
  $pdf->addJpegFromFile('ros.jpg',199,$pdf->y-100,200,0);
} else {
  // comment out these two lines if you do not have GD jpeg support
  // I couldn't quickly see a way to test for this support from the code.
  // you could also copy the file from the locatioin shown and put it in the directory, then 
  // the code above which doesn't use GD will be activated.
  $img = ImageCreatefromjpeg('http://www.ros.co.nz/pdf/ros.jpg');
  $pdf-> addImage($img,199,$pdf->y-100,200,0);
}
  $img = ImageCreatefromjpeg('http://www.ros.co.nz/pdf/ros.jpg');
  $pdf-> addImage($img,199,$pdf->y-200,200,0);

//-----------------------------------------------------------
// load up the document content
$data=file('./data.txt');

$pdf->ezNewPage();

$pdf->ezStartPageNumbers(500,30,10,'','',1);

$size=12;
$height = $pdf->getFontHeight($size);

foreach ($data as $line){
  // go through each line, showing it as required, if it is surrounded by '<>' then 
  // assume that it is a title
  $line=chop($line);
  if ($line[0]=='#'){
    // comment, or new page request
    if ($line=='#NP'){
      $pdf->ezNewPage();
    }
  } else if (($line[1]=='<' || $line[0]=='<') && $line[strlen($line)-1]=='>') {
    // then this is a title
    switch($line[0]){
      case '1':
        $pdf->ezText(substr($line,2,strlen($line)-3),26,array('justification'=>'centre'));
        break;
      default:
        $pdf->ezText(substr($line,2,strlen($line)-3),18,array('justification'=>'left'));
        break;
    }
  } else {
    // then this is just text
    // the ezpdf function will take care of all of the wrapping etc.
    $pdf->ezText($line,$size,array('justification'=>'full'));
  }
  
}

/*
// show all the fonts that we have
$pdf->newPage();
$y=$top;

addTitle($pdf,$y,30,'Font Sampler (basic fonts)');

$fonts=array(
'Courier','Courier-Bold','Courier-BoldOblique','Courier-Oblique'
,'Helvetica','Helvetica-Bold','Helvetica-BoldOblique','Helvetica-Oblique'
,'Times-Roman','Times-Bold','Times-BoldItalic','Times-Italic'
);

foreach($fonts as $font){
  $pdf->selectFont('./fonts/'.$font);
  addText($pdf,$y,18,$font);
  addText($pdf,$y,18,'abcdefghijklmnopqrstuvwxyz');
  addText($pdf,$y,10,'');
}
*/


if ($d){
  $pdfcode = $pdf->ezOutput(1);
  $pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
  echo '<html><body>';
  echo trim($pdfcode);
  echo '</body></html>';
} else {
  $pdf->ezStream();
}
?>