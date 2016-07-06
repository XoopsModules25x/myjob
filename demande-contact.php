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

/**
 * Gère la prise de contact pour une demande d'emploi
 */
include __DIR__ . '/header.php';
include_once XOOPS_ROOT_PATH . '/header.php';
include_once XOOPS_ROOT_PATH . '/modules/myjob/include/functions.php';

if (!myjob_getmoduleoption('usedemands')) {
    redirect_header('index.php', 0, '');
    exit();
}

if (isset($_GET['demandid'])) {
    $demandeid = (int)$_GET['demandid'];
} else {
    if (isset($_POST['demandid'])) {
        $demandeid = (int)$_POST['demandid'];
    } else {
        redirect_header('index.php', 2, _MYJOB_ERROR3);
        exit();
    }
}

$demande_handler = xoops_getModuleHandler('demande', 'myjob');
$demande         = $demande_handler->get($demandeid);

/**
 * Est-ce que la demande existe ?
 */
if (!$demande) {
    redirect_header('index.php', 2, _ERRORS);
    exit();
}

/**
 * Est-ce que la demande est en ligne ?
 */
if ($demande->getVar('datevalidation') == 0 && myjob_getmoduleoption('autoapprovedemands') == 0) {
    redirect_header('index.php', 2, _MYJOB_ERROR4);
    exit();
}

// Est-ce que la prise de contact est autoris�e ?
$authorized = 0;
$authorized = myjob_MygetItemIds();
if (myjob_getmoduleoption('anonymousdemandcontact') || $authorized) {
    if (!isset($_POST['op'])) {
        include_once XOOPS_ROOT_PATH . '/modules/myjob/include/demand_contact_form.php';
    } else {    // Envoi du formulaire de prise de contact ****************************
        $myts             = MyTextSanitizer::getInstance();
        $usersEmail       = $myts->stripSlashesGPC($_POST['email']);
        $usersCompanyName = $myts->stripSlashesGPC($_POST['compagny']);
        $usersComments    = $myts->stripSlashesGPC($_POST['comment']);
        $usersName        = $myts->stripSlashesGPC($_POST['name']);
        $adminMessage     = sprintf(_MYJOB_CONTACT_SUBMITTED, $usersName, XOOPS_URL . '/modules/myjob/demande-view.php?demandid=' . $demande->getVar('demandid'));
        $adminMessage .= "\n";
        $adminMessage .= _MYJOB_CONTACT_EMAIL . " $usersEmail\n";
        if (!empty($usersCompanyName)) {
            $adminMessage .= _MYJOB_CONTACT_COMPAGNY . " $usersCompanyName\n";
        }
        $adminMessage .= _MYJOB_CONTACT_TEXT . "\n";
        $adminMessage .= "\n$usersComments\n";
        $adminMessage .= "\n" . $_SERVER['HTTP_USER_AGENT'] . "\n";
        $subject = $xoopsConfig['sitename'] . ' - ' . $myts->htmlSpecialChars($xoopsModule->name()) . ' - ' . _MYJOB_DEMANDS . ' - ' . _MYJOB_CONTACT_DEMAND;
        if (function_exists('xoops_getMailer')) {
            $xoopsMailer =& xoops_getMailer();
        } else {
            $xoopsMailer =& getMailer();
        }
        $xoopsMailer->useMail();
        $dest   = array();
        $dest[] = $demande->getVar('email');
        if (trim(myjob_getmoduleoption('contactbcc')) != '') {
            $dest[] = myjob_getmoduleoption('contactbcc');
        }
        $xoopsMailer->setToEmails($dest);
        $xoopsMailer->setFromEmail($usersEmail);
        $xoopsMailer->setFromName($xoopsConfig['sitename']);
        $xoopsMailer->setSubject($subject);
        $xoopsMailer->setBody($adminMessage);
        $demande_handler->update_contacts($demandeid);
        if ($xoopsMailer->send()) {
            redirect_header(XOOPS_URL . '/modules/myjob/demande-view.php?demandid=' . $demande->getVar('demandid'), 2, _MYJOB_CONTACT_MSG_SENT);
        } else {
            redirect_header(XOOPS_URL . '/modules/myjob/demande-view.php?demandid=' . $demande->getVar('demandid'), 4, _MYJOB_CONTACT_MSG_NOTSENT);
        }
    }
}

$myts = MyTextSanitizer::getInstance();
// Create page's title
$xoopsTpl->assign('xoops_pagetitle', _MYJOB_CONTACT_FORMNAME . ' - ' . $myts->htmlSpecialChars($xoopsModule->name()));

include_once(XOOPS_ROOT_PATH . '/footer.php');
