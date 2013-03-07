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
 * Affiche le nombre d'offres d'emploi (en lignes) et le nombre de demandes d'emploi (validées)
 */
include('header.php');
$xoopsOption['template_main'] = 'myjob_index.html';
include_once XOOPS_ROOT_PATH.'/header.php';
include_once XOOPS_ROOT_PATH.'/modules/myjob/include/functions.php';

$useoffers=myjob_getmoduleoption('useoffers');
$usedemands=myjob_getmoduleoption('usedemands');

$xoopsTpl->assign('useoffers', $useoffers);			// Booleen pour indiquer si on utilise les offres d'emploi
$xoopsTpl->assign('usedemands', $usedemands);		// Booleen pour indiquer si on utilise les demandes d'emploi

// Offres d'emploi
if($useoffers) {
	$offre_handler =& xoops_getmodulehandler('offre', 'myjob');
	$offersvalid = $offre_handler->getCount(new Criteria('online', '1'));
	$offerswaiting = $offre_handler->getCount(new Criteria('online', '0'));
	// Nombre d'annonces validées et en attente de validation
	$xoopsTpl->assign('offerscount', sprintf(_MYJOB_OFFERS_COUNT,$offersvalid));			// Nombre total d'offres d'emploi (validées)
	$xoopsTpl->assign('offerswaiting', sprintf(_MYJOB_OFFERS_WAITING,$offerswaiting));		// Nombre total d'offres d'emploi en attente de valiadtion
	$xoopsTpl->assign('offersubmitlink',XOOPS_URL.'/modules/myjob/submit-offer.php');		// Lien pour soumettre une offre d'emploi
	$xoopsTpl->assign('offerviewlink',XOOPS_URL.'/modules/myjob/offres.php');				// Lien pour voir les offres d'emploi
}

// Demandes d'emploi
if($usedemands) {
	$demande_handler =& xoops_getmodulehandler('demande', 'myjob');
	$criteria = new CriteriaCompo();
	$criteria->add(new Criteria('datevalidation', '0','<>'));
	$criteria->add(new Criteria('dateexpiration', time(),'>'));
	$demandsvalid = $demande_handler->getCount($criteria);
	$demandswaiting = $demande_handler->getCount(new Criteria('datevalidation', '0','='));
	// Nombre de demandes d'emploi validées et en attente de validation
	$xoopsTpl->assign('demandscount',sprintf(_MYJOB_DEMANDS_COUNT,$demandsvalid));			// Nombre total de demandes d'emploi (validées)
	$xoopsTpl->assign('denamdswaiting', sprintf(_MYJOB_DEMANDS_WAITING,$demandswaiting));	// Nombre total de demandes d'emploi en attente de valiadtion
	$xoopsTpl->assign('demandsubmitlink',XOOPS_URL.'/modules/myjob/submit-demande.php');		// Lien pour soumettre une demande d'emploi
	$xoopsTpl->assign('demandviewlink',XOOPS_URL.'/modules/myjob/demandes.php');			// Lien pour voir les demandes d'emploi
}


if(myjob_getmoduleoption('rss')) {
	$link  = sprintf("<a href='%s' title='%s'><img src='%s' border=0 alt='%s'></a>",XOOPS_URL."/modules/myjob/backend.php", _MYJOB_RSSFEED, XOOPS_URL."/modules/myjob/images/rss.gif",_MYJOB_RSSFEED);
	$link .= sprintf(" <a href='%s' title='%s'><img src='%s' border=0 alt='%s'></a>",XOOPS_URL."/modules/myjob/atom.php", _MYJOB_ATOMFEED, XOOPS_URL."/modules/myjob/images/atom.gif",_MYJOB_ATOMFEED);
	$xoopsTpl->assign('rssfeed_link',$link);
}

// Les choses génériques
$xoopsTpl->assign('welcome',sprintf(_MYJOB_WELCOME_ALL,$xoopsConfig['sitename']));		// Les offres et demandes d'emploi de ....
$xoopsTpl->assign('imgurl',XOOPS_URL.'/modules/myjob/images/');

if(is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
	$xoopsTpl->assign('isadmin',true);
} else {
    $xoopsTpl->assign('isadmin',false);
}

include_once(XOOPS_ROOT_PATH.'/footer.php');
?>
