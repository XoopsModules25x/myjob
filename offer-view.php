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
 * Affiche le contenu d'une offre d'emploi
 */
include __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'myjob_offreitem.tpl';
include_once XOOPS_ROOT_PATH . '/header.php';
include_once XOOPS_ROOT_PATH . '/modules/myjob/include/functions.php';

$useoffers = myjob_getmoduleoption('useoffers');
if (!$useoffers) {
    redirect_header('index.php', 2, '');
    exit();
}

if (isset($_GET['offerid'])) {
    $offreid = (int)$_GET['offerid'];
} else {
    redirect_header('index.php', 2, _MYJOB_ERROR1);
    exit();
}

$offre_handler = xoops_getModuleHandler('offre', 'myjob');
$offre         = $offre_handler->get($offreid);
/**
 * Est-ce que l'offre existe ?
 */
if (!$offre) {
    redirect_header('index.php', 2, _ERRORS);
    exit();
}

/**
 * Est-ce que l'offre est en ligne ?
 */
if ($offre->getVar('online') == 0 && myjob_getmoduleoption('autoapproveoffers') == 0) {
    redirect_header('index.php', 2, _MYJOB_ERROR5);
    exit();
}

$typeposte_handler = xoops_getModuleHandler('typeposte', 'myjob');
$typespostes       = $typeposte_handler->getObjects();
$types             = array();
foreach ($typespostes as $onetypeposte) {
    $types[$onetypeposte->getVar('typeid')] = $onetypeposte->getVar('libelle');
}
$xoopsTpl->assign('typesoffres', $types);

if ($offre->getVar('online') == 0) {
    redirect_header('index.php', 2, _ERRORS);
    exit();
}

if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    $xoopsTpl->assign('isadmin', true);
} else {
    $xoopsTpl->assign('isadmin', false);
}

$offre_handler->updateCounter($offreid);

$array = $offre->toArray();
$xoopsTpl->assign('oneoffre', $array);
include_once(XOOPS_ROOT_PATH . '/footer.php');
