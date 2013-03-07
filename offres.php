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
 * Liste les offres d'emploi
 */
include('header.php');
$xoopsOption['template_main'] = 'myjob_offerindex.html';
include_once XOOPS_ROOT_PATH.'/header.php';
include_once XOOPS_ROOT_PATH.'/modules/myjob/include/functions.php';

$useoffers=myjob_getmoduleoption('useoffers');
if(!$useoffers) {
    redirect_header('index.php',2,'');
    exit();
}

if (isset($_GET['start']) ) {
	$start = intval($_GET['start']);
} else {
	$start = 0;
}
$limit=myjob_getmoduleoption('offrescount');

$typeposte_handler=& xoops_getmodulehandler('typeposte', 'myjob');
$typespostes = $typeposte_handler->getObjects();
$types=array();
foreach($typespostes as $onetypeposte) {
	$types[$onetypeposte->getVar('typeid')]=$onetypeposte->getVar('libelle');
}
$xoopsTpl->assign('typesoffres', $types);


$offre_handler =& xoops_getmodulehandler('offre', 'myjob');
$critere=new Criteria('online', '1', '=');
$critere->setLimit($limit);
$critere->setStart($start);

$offersvalid = $offre_handler->getCount(new Criteria('online', '1', '='));
$offerswaiting = $offre_handler->getCount(new Criteria('online', '0', '='));
$offres = $offre_handler->getObjects($critere);

if(is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
	$xoopsTpl->assign('isadmin',true);
} else {
    $xoopsTpl->assign('isadmin',false);
}

if(myjob_getmoduleoption('rss')) {
	$link=sprintf("<a href='%s' title='%s'><img src='%s' border=0 alt='%s'></a>",XOOPS_URL."/modules/myjob/backend.php", _MYJOB_RSSFEED, XOOPS_URL."/modules/myjob/images/rss.gif",_MYJOB_RSSFEED);
	$xoopsTpl->assign('rssfeed_link',$link);
}

// Les trucs généraux
$xoopsTpl->assign('welcome',sprintf(_MYJOB_WELCOME_OFFERS,$xoopsConfig['sitename']));	// Les offres d'emploi de ....
$xoopsTpl->assign('offerscount', sprintf(_MYJOB_OFFERS_COUNT,$offersvalid));			// Nombre total d'offres d'emploi (validées)
$xoopsTpl->assign('offerswaiting', sprintf(_MYJOB_OFFERS_WAITING,$offerswaiting));		// Nombre total d'offres d'emploi en attente de valiadtion

if ( $offersvalid > $limit ) {
	include_once XOOPS_ROOT_PATH.'/class/pagenav.php';
	$pagenav = new XoopsPageNav($offersvalid, $limit , $start, 'start');
	$xoopsTpl->assign('pagenav', $pagenav->renderNav());
} else {
	$xoopsTpl->assign('pagenav', '');
}


foreach($offres as $oneoffre) {
	$array=$oneoffre->toArray();
	$xoopsTpl->append('offres', $array);
}

include_once(XOOPS_ROOT_PATH.'/footer.php');
?>
