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
 * Soumission d'une offre
 */
include __DIR__ . '/header.php';
include_once XOOPS_ROOT_PATH . '/header.php';
include_once XOOPS_ROOT_PATH . '/modules/myjob/include/functions.php';

$useoffers = myjob_getmoduleoption('useoffers');
if (!$useoffers) {
    redirect_header('index.php', 2, '');
    exit();
}

$op = 'form';

if (isset($_POST['preview'])) {
    $op = 'preview';
} elseif (isset($_POST['post'])) {
    $op = 'post';
} elseif (isset($_GET['op']) && $_GET['op'] === 'edit') {
    $op = 'edit';
}

if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    $xoopsTpl->assign('isadmin', true);
    $admin = true;
} else {
    $xoopsTpl->assign('isadmin', false);
    $admin = false;
}

if (isset($_GET['offerid'])) {
    $offreid = (int)$_GET['offerid'];
} else {
    $offreid = 0;
}

$warning           = myjob_getmoduleoption('autoapproveoffers');
$typeposte_handler = xoops_getModuleHandler('typeposte', 'myjob');
$typespostes       = $typeposte_handler->getObjects();
$types             = array();
foreach ($typespostes as $onetypeposte) {
    $types[$onetypeposte->getVar('typeid')] = $onetypeposte->getVar('libelle');
}

$offre_handler = xoops_getModuleHandler('offre', 'myjob');

switch ($op) {
    case 'post':
        if (empty($offreid)) {
            $offreid = isset($_POST['offreid']) ? (int)$_POST['offreid'] : 0;
        }

        if (!empty($offreid)) {    // Mode �dition
            $offre = $offre_handler->get($offreid);
            $offre->setVars($_POST);
            $offre->unsetNew();
            if (isset($_POST['online']) && (int)$_POST['online'] == 1 && $admin) {
                $offre->setVar('datevalidation', time());
                $offre->setVar('approver', $xoopsUser->getVar('uid'));
            }

            if (isset($_POST['online']) && $admin) {
                $offre->setVar('online', (int)$_POST['online']);
                if ((int)$_POST['online'] == 1 && xoops_trim($offre->getVar('email')) != '') {    // Notify the author of the publication
                    if (function_exists('xoops_getMailer')) {
                        $xoopsMailer =& xoops_getMailer();
                    } else {
                        $xoopsMailer =& getMailer();
                    }
                    $xoopsMailer->useMail();
                    $xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH . '/modules/myjob/language/' . $xoopsConfig['language'] . '/mail_template');
                    $xoopsMailer->setTemplate('offre_published_notify_author.tpl');
                    $xoopsMailer->setToEmails($offre->getVar('email'));
                    $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                    $xoopsMailer->setFromName($xoopsConfig['sitename']);
                    $xoopsMailer->setSubject(_MYJOB_OFFER_EMAIL_SUBJECT);
                    $xoopsMailer->assign('URL_OFFER', XOOPS_URL . '/modules/myjob/offer-view.php?offerid=' . $offre->getVar('offreid'));
                    $xoopsMailer->send();
                }
            }
		} else {	// Mode création
            $offre = $offre_handler->create(true);
            $offre->setVars($_POST);
            $offre->setVar('online', 0);
            $offre->setVar('datesoumission', time());
            $offre->setVar('datevalidation', 0);
            $tags                 = array();
            $tags['URL_ADMIN']    = XOOPS_URL . '/modules/myjob/admin/index.php?op=viewoffers';
            $notification_handler = xoops_getHandler('notification');
            $notification_handler->triggerEvent('global', 0, 'offre_submited', $tags);
        }
        if (isset($_POST['datedispo'])) {
            $offre->setVar('datedispo', strtotime($_POST['datedispo']));
        }

        $offre->setVar('ip', myjob_IP());
        if ($offre_handler->insert($offre)) {
            if (isset($_POST['adminside']) && (int)$_POST['adminside'] == 1) {
                redirect_header(XOOPS_URL . '/modules/myjob/admin/index.php', 2, _MYJOB_OFFER_SAVE_OK);
            } else {
                redirect_header(XOOPS_URL . '/modules/myjob/index.php', 2, _MYJOB_OFFER_SAVE_OK);
            }
        } else {
            if (isset($_POST['adminside']) && (int)$_POST['adminside'] == 1) {
                redirect_header(XOOPS_URL . '/modules/myjob/admin/index.php', 5, _MYJOB_ERROR2);
            } else {
                redirect_header(XOOPS_URL . '/modules/myjob/index.php', 5, _MYJOB_ERROR2);
            }
        }
        exit();
        break;

    case 'edit':
    case 'form':
        if (!empty($offreid)) {
            $offre = $offre_handler->get($offreid);
        } else {
            $offre = $offre_handler->create(true);
        }
        break;
}

include_once XOOPS_ROOT_PATH . '/modules/myjob/include/offerform.php';
include_once XOOPS_ROOT_PATH . '/footer.php';
