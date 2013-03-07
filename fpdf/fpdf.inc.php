<?php
//  ------------------------------------------------------------------------ //
//                      MYJOB - MODULE FOR XOOPS 2.0.x                       //
//                  Copyright (c) 2005-2006 Instant Zero                     //
//                     <http://www.instant-zero.com/>                        //
// ------------------------------------------------------------------------- //
//  This program is NOT free software; you can NOT redistribute it and/or    //
//  modify without my assent.   										     //
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

if (!defined('XOOPS_ROOT_PATH')) {
	die("XOOPS root path not defined");
}

define('MYJOB_FPDF_PATH',XOOPS_ROOT_PATH.'/modules/'.$xoopsModule->getVar('dirname').'/fpdf');
define('FPDF_FONTPATH',MYJOB_FPDF_PATH.'/font/');

require MYJOB_FPDF_PATH.'/gif.php';
require MYJOB_FPDF_PATH.'/fpdf.php';

if(is_readable(MYJOB_FPDF_PATH.'/language/'.$xoopsConfig['language'].'.php')){
	include_once(MYJOB_FPDF_PATH.'/language/'.$xoopsConfig['language'].'.php');
}elseif(is_readable(MYJOB_FPDF_PATH.'/language/english.php')){
	include_once(MYJOB_FPDF_PATH.'/language/english.php');
}else{
	die('No Language File Readable!');
}
include MYJOB_FPDF_PATH.'/makepdf_class.php';
?>