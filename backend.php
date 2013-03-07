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

include_once '../../mainfile.php';
include_once XOOPS_ROOT_PATH.'/class/template.php';
include_once XOOPS_ROOT_PATH.'/modules/myjob/include/functions.php';

// TODO: Vérifier que cela marche encore
if(!myjob_getmoduleoption('rss')) {
	exit();
}
$useoffers = myjob_getmoduleoption('useoffers');
$usedemands = myjob_getmoduleoption('usedemands');
$demandescount = myjob_getmoduleoption('demandescount');
$offrescount = myjob_getmoduleoption('offrescount');

if (function_exists('mb_http_output')) {
	mb_http_output('pass');
}
$charset='utf-8';
header ('Content-Type:text/xml; charset='.$charset);
$tpl = new XoopsTpl();
$tpl->xoops_setCaching(2);
$tpl->xoops_setCacheTime(3600);
if (!$tpl->is_cached('db:myjob_rss.html')) {
	$sitename = htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES);
	$email = checkEmail($xoopsConfig['adminmail'],true);
	$slogan = htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES);
	$category = 'Job';
	$module = 'Myjob';
	$tpl->assign('charset',$charset);
	$tpl->assign('channel_title', xoops_utf8_encode($sitename));
	$tpl->assign('channel_link', XOOPS_URL.'/');
	$tpl->assign('channel_desc', xoops_utf8_encode($slogan));
	$tpl->assign('channel_lastbuild', formatTimestamp(time(), 'rss'));
	$tpl->assign('channel_webmaster', xoops_utf8_encode($email));
	$tpl->assign('channel_editor', xoops_utf8_encode($email));
	$tpl->assign('channel_category', xoops_utf8_encode($category));
	$tpl->assign('channel_generator', xoops_utf8_encode($module));
	$tpl->assign('channel_language', _LANGCODE);
	$tpl->assign('image_url', XOOPS_URL.'/images/logo.gif');
	$dimention = getimagesize(XOOPS_ROOT_PATH.'/images/logo.gif');
	if (empty($dimention[0])) {
		$width = 88;
	} else {
		$width = ($dimention[0] > 144) ? 144 : $dimention[0];
	}
	if (empty($dimention[1])) {
		$height = 31;
	} else {
		$height = ($dimention[1] > 400) ? 400 : $dimention[1];
	}
	$tpl->assign('image_width', $width);
	$tpl->assign('image_height', $height);

	if($useoffers) {
		$offre_handler =& xoops_getmodulehandler('offre', 'myjob');
		$critere=new Criteria('online', '1', '=');
		$critere->setLimit($offrescount);
		$critere->setStart(0);
		$offres = $offre_handler->getObjects($critere);
		foreach($offres as $oneoffre) {
			$tpl->append('items', array('title' => xoops_utf8_encode(htmlspecialchars(_MYJOB_OFFER)) . xoops_utf8_encode(htmlspecialchars($oneoffre->getVar('titreannonce'), ENT_QUOTES)), 'link' => XOOPS_URL.'/modules/myjob/offer-view.php?offerid='.$oneoffre->getVar('offreid'), 'guid' => XOOPS_URL.'/modules/myjob/offer-view.php?offerid='.$oneoffre->getVar('offreid'), 'pubdate' => formatTimestamp($oneoffre->getVar('datevalidation'), 'rss'), 'description' => xoops_utf8_encode(htmlspecialchars($oneoffre->getVar('description'), ENT_QUOTES))));
		}
	}

	if($usedemands) {
		$demande_handler =& xoops_getmodulehandler('demande', 'myjob');
		$critere=new Criteria('datevalidation', '0','<>');
		$critere->setLimit($demandescount);
		$critere->setStart(0);
		$demandes = $demande_handler->getObjects($critere);
		foreach($demandes as $onedemande) {
			$titreannonce = htmlspecialchars($onedemande->getVar('titreannonce'), ENT_QUOTES);
			$description = htmlspecialchars($onedemande->getVar('experiencedetail'), ENT_QUOTES);
            $tpl->append('items', array('title' => _MYJOB_DEMAND.xoops_utf8_encode($titreannonce),
            	'link' => XOOPS_URL.'/modules/myjob/demande-view.php?demandid='.$onedemande->getVar('demandid'),
            	'guid' => XOOPS_URL.'/modules/myjob/viewdemande.php?demandid='.$onedemande->getVar('demandid'),
            	'pubdate' => formatTimestamp($onedemande->getVar('datevalidation'), 'rss'),
            	'description' => xoops_utf8_encode($description)));
		}
	}
}
$tpl->display('db:myjob_rss.html');
?>
