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
 * Recherche du fichier attaché à une offre ou à une demande d'emploi
 */
include __DIR__ . '/header.php';
include_once XOOPS_ROOT_PATH . '/modules/myjob/include/functions.php';

$objectid = isset($_GET['objectid']) ? (int)$_GET['objectid'] : 0;
$typeid   = isset($_GET['typeid']) ? (int)$_GET['typeid'] : 0;    // 1=demandes, 2=offres
if (empty($objectid) || empty($typeid)) {
    redirect_header(XOOPS_URL . '/modules/myjob/index.php', 2, _ERRORS);
    exit();
}

$useoffers  = myjob_getmoduleoption('useoffers');
$usedemands = myjob_getmoduleoption('usedemands');

$url = '';

if ($typeid == 1 && $usedemands) {
    $demande_handler = xoops_getModuleHandler('demande', 'myjob');
    $demande         = $demande_handler->get($objectid);
    if (!$demande || $demande->getVar('datevalidation') == 0) {
        redirect_header('index.php', 2, _ERRORS);
        exit();
    }
    if (myjob_fields('attachedfile', false, 'demande')) {
        $url = XOOPS_UPLOAD_URL . '/' . $demande->getVar('attachedfile');
    }
}

if ($typeid == 2 && $useoffers) {
    $offre_handler = xoops_getModuleHandler('offre', 'myjob');
    $offre         = $offre_handler->get($objectid);
    if (!$offre || $offre->getVar('online') != 1) {
        redirect_header('index.php', 2, _ERRORS);
        exit();
    }
    if (myjob_fields('attachedfile', false, 'offre')) {
        $url = XOOPS_UPLOAD_URL . '/' . $offre->getVar('attachedfile');
    }
}

if (!empty($url)) {
    $myts = MyTextSanitizer::getInstance();
    if (!preg_match("/^ed2k*:\/\//i", $url)) {
        header("Location: $url");
    }
    echo "<html><head><meta http-equiv=\"Refresh\" content=\"0; URL=" . $myts->oopsHtmlSpecialChars($url) . "\"></meta></head><body></body></html>";
    exit();
} else {
    redirect_header('index.php', 2, _ERRORS);
}
