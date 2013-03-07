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

include('header.php');
include_once XOOPS_ROOT_PATH.'/modules/myjob/include/functions.php';
include_once XOOPS_ROOT_PATH.'/modules/myjob/class/class.vCard.inc.php';

$useoffers=myjob_getmoduleoption('useoffers');
$usedemands=myjob_getmoduleoption('usedemands');

$offreid = isset($_GET['offreid']) ? intval($_GET['offreid']) : 0;
$demandid = isset($_GET['demandid']) ? intval($_GET['demandid']) : 0;

// Lecture des zones g�ographiques
$zonegeographique_handler=& xoops_getmodulehandler('zonegeographique', 'myjob');
$zonegeographiques = $zonegeographique_handler->getObjects();
$zones=array();
foreach($zonegeographiques as $onezonegeographique) {
	$zones[$onezonegeographique->getVar('zoneid')]=$onezonegeographique->getVar('libelle');
}

// Lecture des secteurs d'activit�
$secteuractivite_handler=& xoops_getmodulehandler('secteuractivite', 'myjob');
$secteuractivites = $secteuractivite_handler->getObjects();
$secteurs=array();
foreach($secteuractivites as $onesecteuractivite) {
	$secteurs[$onesecteuractivite->getVar('secteurid')]=$onesecteuractivite->getVar('libelle');
}

// Chargement de la liste des types de postes
$typeposte_handler=& xoops_getmodulehandler('typeposte', 'myjob');
$typespostes = $typeposte_handler->getObjects();
$types=array();
foreach($typespostes as $onetypeposte) {
	$types[$onetypeposte->getVar('typeid')]=$onetypeposte->getVar('libelle');
}
// Les situations de famille
$sitfam_handler=& xoops_getmodulehandler('sitfam', 'myjob');
$sitesfams = $sitfam_handler->getObjects();
$tblsitfam=array();
foreach($sitesfams as $onesitfam) {
	$tblsitfam[$onesitfam->getVar('sitfamid')]=$onesitfam->getVar('libelle');
}

if(!empty($offreid) && $useoffers && myjob_getmoduleoption('vcardoffers')) {
	$offre_handler =& xoops_getmodulehandler('offre', 'myjob');
	$offre = $offre_handler->get($offreid);
	if(!$offre || $offre->getVar('online')!=1) {
	    redirect_header('index.php', 2, _ERRORS);
	    exit();
	}
	vcardOffer();
}

if(!empty($demandid) && $usedemands && myjob_getmoduleoption('vcarddemands')) {
	$demande_handler =& xoops_getmodulehandler('demande', 'myjob');
	$demande = $demande_handler->get($demandid);
	if(!$demande || $demande->getVar('datevalidation')==0 && !isset($_GET['op'])) {
	    redirect_header('index.php', 2, _ERRORS);
	    exit();
	}
	vcardDemand();
}


// Cr�ation de la vcard d'une demande d'emploi
function vcardDemand()
{
	global $xoopsConfig, $xoopsModule, $demande, $types, $tblsitfam, $zones, $secteurs;
	$myts =& MyTextSanitizer::getInstance();

	$vCard = new vCard(XOOPS_ROOT_PATH.'/uploads','');
	if(myjob_fields('prenom',false,'demande')) $vCard->setFirstName($demande->getVar('prenom'));
	if(myjob_fields('nom',false,'demande')) $vCard->setLastName($demande->getVar('nom'));
	$vCard->setNote($demande->getVar('titreannonce'));
	if(myjob_fields('telephone',false,'demande')) $vCard->setTelephoneWork1($demande->getVar('telephone'));
	if(myjob_fields('adresse',false,'demande')) $vCard->setWorkStreet($demande->getVar('adresse'));
	if(myjob_fields('cp',false,'demande')) $vCard->setWorkZIP($demande->getVar('cp'));
	if(myjob_fields('ville',false,'demande')) $vCard->setWorkCity($demande->getVar('ville'));

	if(myjob_fields('adresse',false,'demande')) $vCard->setHomeStreet($demande->getVar('adresse'));
	if(myjob_fields('cp',false,'demande')) $vCard->setHomeZIP($demande->getVar('cp'));
	if(myjob_fields('ville',false,'demande')) $vCard->setHomeCity($demande->getVar('ville'));

	if(myjob_fields('adresse',false,'demande')) $vCard->setPostalStreet($demande->getVar('adresse'));
	if(myjob_fields('cp',false,'demande')) $vCard->setPostalZIP($demande->getVar('cp'));
	if(myjob_fields('ville',false,'demande')) $vCard->setPostalCity($demande->getVar('ville'));

	$vCard->setURLWork(XOOPS_URL.'/modules/myjob/demande-view.php?demandid='.$demande->getVar('demandid'));
	if(myjob_fields('diplome',false,'demande')) $vCard->setRole($demande->getVar('diplome'));
	// La date de naissance sera certainement � revoir
	if(myjob_fields('datenaiss',false,'demande')) $vCard->setBirthday(strtotime($demande->getVar('datenaiss')));
	if(myjob_fields('email',false,'demande')) $vCard->setEMail($demande->getVar('email'));
	$vCard->writeCardFile();
	Header("Content-Disposition: attachment; filename=$vCard->card_filename");
	Header("Content-Length: ".strlen($vCard->getCardOutput()));
	Header("Connection: close");
	Header("Content-Type: text/x-vCard; name=$vCard->card_filename");
	echo $vCard->getCardOutput();
	exit;
}
?>
