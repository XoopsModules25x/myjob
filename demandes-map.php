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
 * Visualisation des demandes d'emploi sur une carte Google Maps
 */
include __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'myjob_demandes_map.tpl';
include_once XOOPS_ROOT_PATH . '/header.php';
include_once XOOPS_ROOT_PATH . '/modules/myjob/include/functions.php';

$usedemands = myjob_getmoduleoption('usedemands');
if (!$usedemands) {
    redirect_header('index.php', 2, '');
    exit();
}

if (!myjob_getmoduleoption('demandsmap')) {
    redirect_header('index.php', 2, '');
    exit();
}

$demande_handler = xoops_getModuleHandler('demande', 'myjob');

$criteria = new CriteriaCompo();
$criteria->add(new Criteria('datevalidation', '0', '<>'));
$criteria->add(new Criteria('dateexpiration', time(), '>'));
$criteria->setLimit(0);
$criteria->setStart(0);
$criteria->setOrder('DESC');
$criteria->setSort('datevalidation');

$demandes     = $demande_handler->getObjects($criteria);
$demandsvalid = $demande_handler->getCount($criteria);

// Les trucs généraux
$xoopsTpl->assign('welcome', sprintf(_MYJOB_WELCOME_DEMANDS, $xoopsConfig['sitename']));        // Les demandes d'emploi de ....
$xoopsTpl->assign('demandscount', sprintf(_MYJOB_DEMANDS_COUNT, $demandsvalid));                // Nombre total de demandes d'emploi (validées)
$xoopsTpl->assign('gmapapikey', myjob_getmoduleoption('gmapapikey'));

foreach ($demandes as $onedemande) {
    $xoopsTpl->append('demandes', $onedemande->toArray());
}

$myts = MyTextSanitizer::getInstance();
// Create page's title
$xoopsTpl->assign('xoops_pagetitle', _MYJOB_DEMANDS_MAP . ' - ' . $myts->htmlSpecialChars($xoopsModule->name()));
include_once(XOOPS_ROOT_PATH . '/footer.php');
