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

/**
 * Formulaire de demande de contact pour une demande d'emploi
*/
if (!defined('XOOPS_ROOT_PATH')) {
	die('XOOPS root path not defined');
}

include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
$sform = new XoopsThemeForm(_MYJOB_CONTACT_FORMNAME, 'contactdemandform', XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/demande-contact.php');
$sform->insertBreak('<center><b>'._MYJOB_CONTACT_FORMTEXT.'</b></center>','even');
$sform->addElement(new XoopsFormText(_MYJOB_CONTACT_NAME,'name',50,255,'',true));
$sform->addElement(new XoopsFormText(_MYJOB_CONTACT_EMAIL,'email',50,255,'',false));
$sform->addElement(new XoopsFormText(_MYJOB_CONTACT_COMPAGNY,'compagny',50,255,'',true));
$sform->addElement(new XoopsFormTextArea(_MYJOB_CONTACT_TEXT,'comment', '', 15,70), true);
$sform->addElement(new XoopsFormHidden('op', 'send'));
$sform->addElement(new XoopsFormHidden('demandid', $demande->getVar('demandid')));
$button_tray = new XoopsFormElementTray('' ,'');
$submit_btn = new XoopsFormButton('', 'post', _MYJOB_POST, 'submit');
$button_tray->addElement($submit_btn);
$sform->addElement($button_tray);
$sform->display();
?>
