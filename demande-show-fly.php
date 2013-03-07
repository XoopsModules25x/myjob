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
 * Visualisation d'une demande à la volée
 */

include 'header.php';
include_once XOOPS_ROOT_PATH.'/class/template.php';
include_once XOOPS_ROOT_PATH.'/modules/myjob/include/functions.php';

if(!myjob_getmoduleoption('usedemands')) {
    redirect_header('index.php',2,'');
    exit();
}

// Permission d'avoir un caddy ?
if( myjob_getmoduleoption('usecaddy') && myjob_MygetItemIds()) {

} else {
    redirect_header(XOOPS_URL.'/index.php',2,_ERRORS);
    exit();
}

// Paramètres recus
$demandeid = intval($_POST['demandeid']);

// Initialisation des handlers
$demande_handler =& xoops_getmodulehandler('demande', 'myjob');
$demande = null;
$demande = $demande_handler->get($demandeid);

if(!is_object($demande)) {
    redirect_header('index.php',2,_ERRORS);
    exit();
}

if($demande->getVar('datevalidation') == 0 && myjob_getmoduleoption('autoapprovedemands') == 0 ) {
    redirect_header('index.php',2,_MYJOB_ERROR4);
    exit();
}

$tpl = new XoopsTpl();

$typeposte_handler= & xoops_getmodulehandler('typeposte', 'myjob');
$typespostes = $typeposte_handler->getObjects();
$types = array();
foreach($typespostes as $onetypeposte) {
	$types[$onetypeposte->getVar('typeid')] = $onetypeposte->getVar('libelle');
}
$tpl->assign('typesoffres', $types);

// Lecture des situations de famille
$sitfam_handler = & xoops_getmodulehandler('sitfam', 'myjob');
$sitesfams = $sitfam_handler->getObjects();
$tblsitfam = array();
foreach($sitesfams as $onesitfam) {
	$tblsitfam[$onesitfam->getVar('sitfamid')] = $onesitfam->getVar('libelle');
}
$tpl->assign('sitfam', $tblsitfam);

// Relation offre/demande <-> zones géographiques
$demandofferzones_handler = & xoops_getmodulehandler('demandofferzones', 'myjob');

// Relation offre/demande <-> secteurs d'activité
$demandoffersecteurs_handler = & xoops_getmodulehandler('demandoffersecteurs', 'myjob');

$array = $demande->toArray();

$critere = new Criteria('r.demandid', $demande->getVar('demandid'),'=');
$critere->setSort('libelle');
// Récupération des zones géographiques
$tblzones = $demandofferzones_handler->getLibsWithRelation($critere, true);
$libzones = join('<br />',$tblzones);
// Récupération des secteurs
$tblsecteurs = $demandoffersecteurs_handler->getLibsWithRelation($critere, true);
$libsecteurs = join('<br />',$tblsecteurs);


$array['zonesidlibelle']=$libzones;
$array['secteurslibelle']=$libsecteurs;
$authorized = 0;
$authorized = myjob_MygetItemIds();
if(myjob_getmoduleoption('anonymousdemandcontact') || $authorized) {
	$tpl->assign('contactlink',sprintf(_MYJOB_ENTER_CONTACT,XOOPS_URL.'/modules/myjob/demande-contact.php?demandid='.$demande->getVar('demandid')));
}
$tpl->assign('onedemande', $array);

$demande_fields = myjob_get_fields_from_table('myjob_demande');
$demande_fields['secteurid']='secteurid';
$demande_fields['zoneid']='zoneid';

foreach ($demande_fields as $key => $value) {
	$tpl->assign('vis_'.$key, myjob_fields($key,false,'demande'));
}

// Mail
$tpl->assign('mail_link','mailto:?subject='.sprintf(_MYJOB_DEMAND_INTARTICLE,$xoopsConfig['sitename']).'&amp;body='.sprintf(_MYJOB_DEMAND_INTARTFOUND, $xoopsConfig['sitename']).':  '.XOOPS_URL.'/modules/myjob/demande-view.php?demandid='.$demande->getVar('demandid'));

// vCard ?
if(myjob_getmoduleoption('vcarddemands')) {
	if(!is_object($xoopsUser) && !myjob_fields('email',false,'demande') && !myjob_fields('nom',false,'demande')) {
		$tpl->assign('vcardlink','');
	} else {
		$tpl->assign('vcardlink',XOOPS_URL.'/modules/myjob/vcard.php?demandid='.$demande->getVar('demandid'));
	}
}

// Permission d'avoir un caddy ?
$tpl->assign('caddy', false);

// PDF ?
$tpl->assign('usepdf', myjob_getmoduleoption('usepdf'));
$tpl->assign('display_connexes',myjob_getmoduleoption('false'));
$tpl->assign('showspreviousnext', false);
$tpl->assign('showprevious', false);
$tpl->assign('shownotifications', false);
$tpl->assign('showsummary', false);

$html = $tpl->fetch('db:myjob_demandeitem.html');
echo utf8_encode($html);
?>
