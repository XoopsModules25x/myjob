<?php
//  ------------------------------------------------------------------------ //
//                      MYJOB - MODULE FOR XOOPS 2.0.x                       //
//                  Copyright (c) 2005-2006 Instant Zero                     //
//                     <http://www.instant-zero.com/>                        //
// ------------------------------------------------------------------------- //
//  This program is NOT free software; you can NOT redistribute it and/or    //
//  modify without my assent.                                                //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed WITHOUT ANY WARRANTY; without even the       //
//  implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. //
//                                                                           //
//  ------------------------------------------------------------------------ //

//edit all these global variables to your's!

//the fpdf-creator-string
$xcreator = 'FPDF v1.53';

//your homepage-url
$xurl = XOOPS_URL;

//your contact email
$xmail = ('mailto:' . checkEmail($xoopsConfig['adminmail'], true));

//the slogan of your site
$xslogan = $myts->htmlSpecialChars($xoopsConfig['slogan']);

//your logo name, located in .../makepdf/ (if you wish)
//must be a png (best, recommended!), gif (only with gif.php!, slow) or jpg (good)
//recommended size: 320x60px, if you'll try other dimensions you must edit the
//appropriate line with ->Image($xlogo,...) at the end in makepdf.php!
$xlogo = '../../images/logo.png';

//the global scaling factor for images if the HTTP-Var $scale is not passed
//to the script 'makepdf.php'
$xscale = '0.8';

//footer() in makepdf_class.php
//the date format string

$xdformat = _DATESTRING;
//the string for time and pagenumber
$xsdatepage = _MD_PDF_PAGE;

//remarks at bottom of last page
//the opening string
$xsopen = _MYJOB_POSTEDON;
//the author string
$xsauthor = _POSTEDBY . ': ';
