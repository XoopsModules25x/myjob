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
 * Comparaisons de demandes d'emploi
 */
include 'header.php';
$xoopsOption['template_main'] = 'myjob_demandscompare.html';
include_once XOOPS_ROOT_PATH.'/header.php';
include_once XOOPS_ROOT_PATH.'/modules/myjob/include/functions.php';

$usedemands = myjob_getmoduleoption('usedemands');
if(!$usedemands) {
    redirect_header('index.php',2,'');
    exit();
}

// Inclusion de Prototype
$url_prototype = '<script type="text/javascript" src="'. XOOPS_URL.'/modules/myjob/js/prototype.js'.'"></script>';
$xoopsTpl->assign('xoops_module_header', $url_prototype);

// Quelques préférences
$xoopsTpl->assign('showprevious', false);
$xoopsTpl->assign('shownotifications', false);
$xoopsTpl->assign('showsummary', false);
$xoopsTpl->assign('caddy', false);	// Permission d'avoir un caddy ?
$xoopsTpl->assign('usepdf', myjob_getmoduleoption('usepdf'));	// PDF ?
$xoopsTpl->assign('display_connexes', false);	// Informations connexes ?
$xoopsTpl->assign('showspreviousnext', false);	// Show previous and next demand ?

$myts =& MyTextSanitizer::getInstance();

$tbl_caddie = array();
if(isset($_SESSION['myjob_caddie'])) {
	$tbl_caddie = $_SESSION['myjob_caddie'];
}
$demandescount = count($tbl_caddie);
$xoopsTpl->assign('demandescount', $demandescount);


$demande_handler =& xoops_getmodulehandler('demande', 'myjob');

if( $demandescount > 0) {
	$lst_demandes = '';
	$lst_demandes = join(',', $tbl_caddie);
	$critere = new Criteria('demandid','('.$lst_demandes.')','IN');
	$critere->setSort('datesoumission, titreannonce');
	$tbl_demandes = array();
	$tbl_demandes = $demande_handler->getObjects($critere);
	foreach($tbl_demandes as $one_demande) {
		$xoopsTpl->append('demandes', $one_demande->toArray());
	}
	// Puis on se positionne, par défaut, sur la première demande
	if(count($tbl_demandes) >0) {
		$demande = $tbl_demandes[0];

		// Lecture des situations de famille
		$sitfam_handler = & xoops_getmodulehandler('sitfam', 'myjob');
		$sitesfams = $sitfam_handler->getObjects();
		$tblsitfam = array();
		foreach($sitesfams as $onesitfam) {
			$tblsitfam[$onesitfam->getVar('sitfamid')] = $onesitfam->getVar('libelle');
		}
		$xoopsTpl->assign('sitfam', $tblsitfam);

		// Relation offre/demande <-> zones géographiques
		$demandofferzones_handler =& xoops_getmodulehandler('demandofferzones', 'myjob');

		// Relation offre/demande <-> secteurs d'activité
		$demandoffersecteurs_handler =& xoops_getmodulehandler('demandoffersecteurs', 'myjob');

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

		$xoopsTpl->assign('onedemande', $array);

		$demande_fields = myjob_get_fields_from_table('myjob_demande');
		$demande_fields['secteurid']='secteurid';
		$demande_fields['zoneid']='zoneid';

		foreach ($demande_fields as $key => $value) {
			$xoopsTpl->assign('vis_'.$key, myjob_fields($key,false,'demande'));
		}

		// Mail
		$xoopsTpl->assign('mail_link','mailto:?subject='.sprintf(_MYJOB_DEMAND_INTARTICLE,$xoopsConfig['sitename']).'&amp;body='.sprintf(_MYJOB_DEMAND_INTARTFOUND, $xoopsConfig['sitename']).':  '.XOOPS_URL.'/modules/myjob/demande-view.php?demandid='.$demande->getVar('demandid'));

		// vCard ?
		if(myjob_getmoduleoption('vcarddemands')) {
			if(!is_object($xoopsUser) && !myjob_fields('email',false,'demande') && !myjob_fields('nom',false,'demande')) {
				$xoopsTpl->assign('vcardlink','');
			} else {
				$xoopsTpl->assign('vcardlink',XOOPS_URL.'/modules/myjob/vcard.php?demandid='.$demande->getVar('demandid'));
			}
		}
	}
}


// Create page's title
$xoopsTpl->assign('xoops_pagetitle', _MYJOB_CADDY .' - '.$myts->htmlSpecialChars($xoopsModule->name()));
// META Keywords and description
$xoopsTpl->assign('xoops_meta_description', _MYJOB_CADDY .' - '.$myts->htmlSpecialChars($xoopsModule->name()) );


include_once(XOOPS_ROOT_PATH.'/footer.php');
?>