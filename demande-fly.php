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
 * AJAX - Ajout de demandes d'emploi dans le caddy, à la volée
 */
include __DIR__ . '/header.php';
include_once XOOPS_ROOT_PATH . '/modules/myjob/include/functions.php';

$uid = 0;
if (!is_object($xoopsUser)) {
    redirect_header(XOOPS_URL . '/index.php', 2, _ERRORS);
    exit();
}

// Les demandes d'emploi sont activées sur le site ?
if (!myjob_getmoduleoption('usedemands')) {
    redirect_header('index.php', 2, '');
    exit();
}

// Permission d'avoir un caddy ?
if (myjob_getmoduleoption('usecaddy') && myjob_MygetItemIds()) {
} else {
    redirect_header(XOOPS_URL . '/index.php', 2, _ERRORS);
    exit();
}

// Paramètres recus
$demandeid = (int)$_POST['demandeid'];

// Initialisation des handlers
$demande_handler = xoops_getModuleHandler('demande', 'myjob');
$demande         = null;
$demande         = $demande_handler->get($demandeid);

/**
 * Est-ce que la demande existe ?
 */
if (!is_object($demande)) {
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

// Ajout ou suppression dans le caddy
$tbl_caddie = array();
if (isset($_SESSION['myjob_caddie'])) {
    $tbl_caddie = $_SESSION['myjob_caddie'];
}

if (isset($tbl_caddie[$demandeid])) {    // On supprime la demande d'emploi
    unset($tbl_caddie[$demandeid]);
    $picture = 'cartadd.gif';
    $text    = _MYJOB_CADDY_PUT;
} else {                                // On rajoute la demande d'emploi
    $tbl_caddie[$demandeid] = $demandeid;
    $picture                = 'cartdelete.gif';
    $text                   = _MYJOB_CADDY_REMOVE;
}
$_SESSION['myjob_caddie'] = $tbl_caddie;

$XOOPS_URL = XOOPS_URL;
$resultat  = "<img src=\"$XOOPS_URL/modules/myjob/assets/images/$picture\" border='0' style=\"cursor: pointer;\" alt=\"$text\" onclick=\"changeme('d$demandeid',$demandeid);\" />";
echo $resultat;
