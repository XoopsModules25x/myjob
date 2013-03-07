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
 * Affiche le contenu d'une demande d'emploi
 */
include('header.php');
$xoopsOption['template_main'] = 'myjob_demandeitem.html';
include_once XOOPS_ROOT_PATH.'/header.php';
include_once XOOPS_ROOT_PATH.'/modules/myjob/include/functions.php';

if(!myjob_getmoduleoption('usedemands')) {
    redirect_header('index.php',2,'');
    exit();
}

if(isset($_GET['demandid']) ) {
	$demandeid = intval($_GET['demandid']);
} else {
    redirect_header('index.php',2,_MYJOB_ERROR3);
    exit();
}

$demande_handler =& xoops_getmodulehandler('demande', 'myjob');
$demande = $demande_handler->get($demandeid);

/**
 * Est-ce que la demande existe ?
 */
if(!$demande) {
    redirect_header('index.php',2,_ERRORS);
    exit();
}

/**
 * Est-ce que la demande est en ligne ?
 */
if($demande->getVar('datevalidation')==0 && myjob_getmoduleoption('autoapprovedemands') ==0 ) {
    redirect_header('index.php',2,_MYJOB_ERROR4);
    exit();
}

$myts =& MyTextSanitizer::getInstance();

$typeposte_handler=& xoops_getmodulehandler('typeposte', 'myjob');
$typespostes = $typeposte_handler->getObjects();
$types = array();
foreach($typespostes as $onetypeposte) {
	$types[$onetypeposte->getVar('typeid')] = $onetypeposte->getVar('libelle');
}
$xoopsTpl->assign('typesoffres', $types);

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

if(is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
	$xoopsTpl->assign('isadmin',true);
} else {
    $xoopsTpl->assign('isadmin',false);
}

$demande_handler->updateCounter($demandeid);

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
	$xoopsTpl->assign('contactlink',sprintf(_MYJOB_ENTER_CONTACT,XOOPS_URL.'/modules/myjob/demande-contact.php?demandid='.$demande->getVar('demandid')));
}
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

// Permission d'avoir un caddy ?
if( myjob_getmoduleoption('usecaddy') && myjob_MygetItemIds()) {
	$xoopsTpl->assign('caddy', true);
} else {
	$xoopsTpl->assign('caddy', false);
}

// PDF ?
$xoopsTpl->assign('usepdf', myjob_getmoduleoption('usepdf'));

// Informations connexes ?
$xoopsTpl->assign('display_connexes',myjob_getmoduleoption('display_connexes'));
if(myjob_getmoduleoption('display_connexes')) {
	$connexes  = "<select name='sel_connexes' onChange=\"document.forms['fconnexes'].submit()\">";
	$connexes .= '<optgroup label="'._MYJOB_DEMAND_ZONEGEOGRAPHIQUE.'">';
	foreach($tblzones as $id => $libelle) {
		$connexes .= '<option value="Z'.$id.'">'.$libelle.'</option>';
	}
	$connexes .= '</optgroup>';

	$connexes .= '<optgroup label="'._MYJOB_OFFER_SECTEUR.'">';
	foreach($tblsecteurs as $id => $libelle) {
		$connexes .= '<option value="S'.$id.'">'.$libelle.'</option>';
	}
	$connexes .= '</optgroup>';
	$connexes .= '</select>';
	$xoopsTpl->assign('connexes_select',$connexes);
}

// Create page's title
$xoopsTpl->assign('xoops_pagetitle', strip_tags($demande->getVar('titreannonce')).' - '.$myts->htmlSpecialChars($xoopsModule->name()));
// META Keywords and description
$xoopsTpl->assign('xoops_meta_keywords', myjob_createmeta_keywords(strip_tags($demande->getVar('titreannonce')).' '.strip_tags($demande->getVar('experiencedetail'))));
$xoopsTpl->assign('xoops_meta_description', strip_tags($demande->getVar('titreannonce')));


/**
 * Show previous and next demand ?
 */
if(myjob_getmoduleoption('previousnextlink')) {
	$xoopsTpl->assign('showspreviousnext', true);

	// Recherche de l'élément suivant l'annonce en cours.
	$criteria = new CriteriaCompo();
	$criteria->add(new Criteria('datevalidation', '0','<>'));
	$criteria->add(new Criteria('dateexpiration', time(),'>'));
	$criteria->add(new Criteria('demandid', $demandeid,'>'));
	$criteria->setOrder('DESC');
	$criteria->setSort('datevalidation');
	$criteria->setLimit(1);
	$tbl = array();
	$tbl = $demande_handler->getObjects($criteria);
	if(count($tbl)==1) {	// Trouvé
		$demandetempo = $tbl[0];
	   	$xoopsTpl->assign('next_demand_id',$demandetempo->getVar('demandid'));
   		$xoopsTpl->assign('next_demand_title',$demandetempo->getVar('titreannonce'));
	}

	// Recherche de l'élément précédant l'annonce en cours.
	$criteria = new CriteriaCompo();
	$criteria->add(new Criteria('datevalidation', '0','<>'));
	$criteria->add(new Criteria('dateexpiration', time(),'>'));
	$criteria->add(new Criteria('demandid', $demandeid,'<'));
	$criteria->setOrder('DESC');
	$criteria->setSort('datevalidation');
	$criteria->setLimit(1);
	$tbl = array();
	$tbl = $demande_handler->getObjects($criteria);
	if(count($tbl)==1) {	// Trouvé
		$demandetempo = $tbl[0];
	   	$xoopsTpl->assign('previous_demand_id',$demandetempo->getVar('demandid'));
   		$xoopsTpl->assign('previous_demand_title',$demandetempo->getVar('titreannonce'));
	}
} else {
	$xoopsTpl->assign('showspreviousnext', false);
}

$xoopsTpl->assign('showprevious', true);
$xoopsTpl->assign('shownotifications', true);


/**
 * Show a summary of recent demands ?
 */
if(myjob_getmoduleoption('summarytable')>0) {
	$cnt = myjob_getmoduleoption('summarytable');
	$xoopsTpl->assign('showsummary', true);
	$criteria = new CriteriaCompo();
	$criteria->add(new Criteria('datevalidation', '0','<>'));
	$criteria->add(new Criteria('dateexpiration', time(),'>'));
	$criteria->add(new Criteria('demandid', $demandeid,'<>'));
	$criteria->setLimit($cnt);
	$criteria->setOrder('DESC');
	$criteria->setSort('datevalidation');
	$tbldemandes = $demande_handler->getObjects($criteria);
	foreach($tbldemandes as $onedemande) {
		$array=$onedemande->toArray();
		$tooltip = '';
		$tooltip .= myjob_fields('nom',false,'demande') ? $onedemande->getVar('nom').'<br>' : '';
		$tooltip .= myjob_fields('prenom',false,'demande') ? $onedemande->getVar('prenom').'<br>' : '';
		$tooltip .= myjob_fields('diplome',false,'demande') ? $onedemande->getVar('diplome').'<br>' : '';
		$tooltip .= myjob_fields('adresse',false,'demande') ? $onedemande->getVar('adresse').'<br>' : '';
		$tooltip .= myjob_fields('cp',false,'demande') ? $onedemande->getVar('cp').'<br>' : '';
		$tooltip .= myjob_fields('ville',false,'demande') ? $onedemande->getVar('ville').'<br>' : '';
		$tooltip .= myjob_fields('datenaiss',false,'demande') ? $onedemande->getVar('datenaiss').'<br>' : '';
		$tooltip .= myjob_fields('diplome',false,'demande') ? $onedemande->getVar('diplome').'<br>' : '';
		$tooltip .= myjob_fields('formation',false,'demande') ? $onedemande->getVar('formation').'<br>' : '';
		$tooltip .= myjob_fields('zonegeographique',false,'demande') ? $onedemande->getVar('zonegeographique').'<br>' : '';
		$tooltip .= myjob_fields('secteuractivite',false,'demande') ? $onedemande->getVar('secteuractivite').'<br>' : '';
		$tooltip .= myjob_fields('experiencedetail',false,'demande') ? $onedemande->getVar('experiencedetail').'<br>' : '';
		$array['tooltip'] = myjob_make_dhtml_tooltip($tooltip);
		$xoopsTpl->append('summary_demandes', $array);
	}
} else {
	$xoopsTpl->assign('showsummary', false);
}

include_once(XOOPS_ROOT_PATH.'/footer.php');
?>