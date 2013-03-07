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
 * Recherche dans les demandes d'emploi
*/
include 'header.php';
$xoopsOption['template_main'] = 'myjob_demandesearch.html';
include_once XOOPS_ROOT_PATH.'/header.php';
include_once XOOPS_ROOT_PATH.'/modules/myjob/include/functions.php';
$myts =& MyTextSanitizer::getInstance();

$usedemands = myjob_getmoduleoption('usedemands');
if(!$usedemands) {
    redirect_header('index.php',1,_ERRORS);
    exit();
}

if (file_exists(XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/calendar.php')) {
	include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/calendar.php';
} else {
	include_once XOOPS_ROOT_PATH.'/language/english/calendar.php';
}

// Constitution des mois
$tblmois = array('---',_CAL_JANUARY,_CAL_FEBRUARY,_CAL_MARCH,_CAL_APRIL,_CAL_MAY,_CAL_JUNE,_CAL_JULY,_CAL_AUGUST,_CAL_SEPTEMBER,_CAL_OCTOBER,_CAL_NOVEMBER,_CAL_DECEMBER);

$experience_handler = & xoops_getmodulehandler('experience', 'myjob');
$typeposte_handler = & xoops_getmodulehandler('typeposte', 'myjob');
$demandofferzones_handler =& xoops_getmodulehandler('demandofferzones', 'myjob');
$demandoffersecteurs_handler =& xoops_getmodulehandler('demandoffersecteurs', 'myjob');
$secteuractivite_handler=& xoops_getmodulehandler('secteuractivite', 'myjob');
$zonegeographique_handler=& xoops_getmodulehandler('zonegeographique', 'myjob');
$demande_handler =& xoops_getmodulehandler('demande', 'myjob');

// Périodes de recherche
$searchperiods = myjob_getmoduleoption('searchperiods');

// Lecture des types de postes
$typespostes = $typeposte_handler->getObjects();
$types = array();
foreach($typespostes as $onetypeposte) {
	$types[$onetypeposte->getVar('typeid')]=$onetypeposte->getVar('libelle');
}
$xoopsTpl->assign('typesoffres', $types);

$critere = new Criteria('secteurid', 0, '<>');
$critere->setSort('libelle');
// Secteurs d'activité
$secteuractivites = $secteuractivite_handler->getObjects($critere);
$secteurs = array();
foreach($secteuractivites as $onesecteuractivite) {
	$secteurs[$onesecteuractivite->getVar('secteurid')]=$onesecteuractivite->getVar('libelle');
}

// Zones géographiques
$critere = new Criteria('zoneid', 0, '<>');
$critere->setSort('libelle');
$zonegeographiques = $zonegeographique_handler->getObjects($critere);
$zones = array();
foreach($zonegeographiques as $onezonegeographique) {
	$zones[$onezonegeographique->getVar('zoneid')]=$onezonegeographique->getVar('libelle');
}


if((isset($_POST['op']) && $_POST['op'] == 'go') || isset($_GET['start'])) {
	// Create page's title
	$xoopsTpl->assign('xoops_pagetitle', _MYJOB_DEMAND_SEARCH_RESULTS.' - '.$myts->htmlSpecialChars($xoopsModule->name()));
	$xoopsTpl->assign('search_results', true);
	$criteria = new CriteriaCompo();

	if(!isset($_GET['start'])) {
		$help = '';
		// Premier critère, uniquement les demandes qui ne sont pas périmées
		$criteria->add(new Criteria('d.datevalidation', '0','<>'));
		$criteria->add(new Criteria('d.dateexpiration', time(),'>'));

		if(isset($_POST['connexes']) && intval($_POST['connexes']) == 1) {
			$valeur_recherchee = $_POST['sel_connexes'];
			$id = intval(substr($valeur_recherchee,1));
			$type_recherche = substr($valeur_recherchee,0,1);
			if($type_recherche=='Z') {
				$help .= _MYJOB_DEMAND_ZONEGEOGRAPHIQUE.' :<br>';
				$help .= $zones[$id].'<br>';
				$criteria->add(new Criteria('z.zoneid',$id,'='));
			} elseif($type_recherche=='S') {
				$help .= _MYJOB_DEMAND_SECTEURACTIVITE .' :<br>';
				$help .= $secteurs[$id].'<br>';
				$criteria->add(new Criteria('s.secteurid',$id,'='));
			}
		}

		// Secteur d'activité
		if(isset($_POST['secteurid'])) {
			$help .= _MYJOB_DEMAND_SECTEURACTIVITE .' :<br>';
			foreach($_POST['secteurid'] as $onesecteurid) {
				$help .= $secteurs[$onesecteurid].'<br>';
			}
			$secteurssec = $myts->addSlashes(join(',',$_POST['secteurid']));
			$criteria->add(new Criteria('s.secteurid','('.$secteurssec.')','IN'));
		}
		// Zone géographique
		if(isset($_POST['zoneid'])) {
			$help .= _MYJOB_DEMAND_ZONEGEOGRAPHIQUE.' :<br>';
			foreach($_POST['zoneid'] as $onezonegeo) {
				$help .= $zones[$onezonegeo].'<br>';
			}
			$zonesgeo = $myts->addSlashes(join(',',$_POST['zoneid']));
			$criteria->add(new Criteria('z.zoneid','('.$zonesgeo.')','IN'));
		}
		// Expérience
		if(isset($_POST['experience']) && intval($_POST['experience'])!=0) {
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('experienceid', intval($_POST['experience']),'='));
			$tblexperience = $experience_handler->getObjects($criteria,true);
			$help .= _MYJOB_DEMAND_EXPERIENCE . ' : '.$tblexperience[intval($_POST['experience'])]->getVar('libelle').'<br>';
			$criteria->add(new Criteria('experience',intval($_POST['experience']),'='));
		}
		// Depuis
		if(isset($_POST['since']) && intval($_POST['since'])>0) {
			$help .= _MYJOB_DEMAND_SEARCH_FROM .' : '.intval($_POST['since']).' '._MYJOB_DEMAND_SEARCH_LAST_DAYS.'<br>';
			$criteria->add(new Criteria('d.datevalidation',time()-intval($_POST['since'])*86400,'>='));
		}
		// Disponibilité
		if(isset($_POT['month']) && intval($_POST['month'])>0 && isset($_POST['year']) && intval($_POST['year'])>0) {
			$help .= _MYJOB_DEMAND_DATEDISPO . $tblmois[intval($_POST['month'])].' '.intval($_POST['year']).'<br>';
			$timestamp = mktime(1,0,0,intval($_POST['month']),1,intval($_POST['year']));
			$criteria->add(new Criteria('d.datedispo',$timestamp,'>='));
		}
		// Type de poste
		if(isset($_POST['typeposte']) && intval($_POST['typeposte'])>0) {
			$help .= _MYJOB_DEMAND_TYPEPOSTE . ' : '.$types[intval($_POST['typeposte'])].'<br>';
			$criteria->add(new Criteria('p.typeid',intval($_POST['typeposte']),'='));
		}
		// Compétences
		if(isset($_POST['competences']) && !empty($_POST['competences'])) {
			$help .= _MYJOB_DEMAND_COMPETENCES .' : '.$myts->addSlashes($_POST['competences']).'<br>';
			$criteria->add(new Criteria('competences','%'.$myts->addSlashes($_POST['competences']).'%','LIKE'));
		}
		// Diplôme
		if(isset($_POST['diplome']) && !empty($_POST['diplome'])) {
			$help .= _MYJOB_DEMAND_DIPLOME .' : '.$myts->addSlashes($_POST['diplome']).'<br>';
			$criteria->add(new Criteria('diplome','%'.$myts->addSlashes($_POST['diplome']).'%','LIKE'));
		}
		$_SESSION['help'] = $help;
		$_SESSION['critmyjob'] = serialize($criteria);
	} else {	// On a cliqué sur un chevron pour aller voir les autres pages, il faut travailler à partir des informations de la session
		if(isset($_SESSION['critmyjob'])) {
			$criteria = unserialize($_SESSION['critmyjob']);
		}
	}

	if(isset($_SESSION['help']) && xoops_trim($_SESSION['help']) != '') {
		$xoopsTpl->assign('help', myjob_make_dhtml_tooltip($_SESSION['help']));
	}
	$limit = myjob_getmoduleoption('demandescount');
	$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
	$criteria->setSort('datevalidation');
	$criteria->setOrder('DESC');
	$criteria->setLimit(0);
	$criteria->setStart(0);
	$criteria->setGroupby('d.demandid');

	// On commence par rechercher le nombre de demandes qui correspondent à la recherche
	$demandescnt = $demande_handler->getFilteredDemands($criteria, false, true);
	if ($demandescnt > $limit) {
		include_once XOOPS_ROOT_PATH.'/class/pagenav.php';
		$pagenav = new XoopsPageNav($demandescnt, $limit , $start, 'start');
		$xoopsTpl->assign('pagenav', $pagenav->renderNav());
	} else {
		$xoopsTpl->assign('pagenav', '');
	}
	// Ensuite on recherche les demandes
	$criteria->setLimit($limit);
	$criteria->setStart($start);
	$demandes = $demande_handler->getFilteredDemands($criteria);

	foreach($demandes as $onedemande) {
		$array = $onedemande->toArray();
		$critere = new Criteria('r.demandid', $onedemande->getVar('demandid'), '=');
		$critere->setSort('libelle');
		// Récupération des zones géographiques
		$tblzones = $demandofferzones_handler->getLibsWithRelation($critere);
		$libzones = join('<br />',$tblzones);
		// Récupération des secteurs
		$tblsecteurs = $demandoffersecteurs_handler->getLibsWithRelation($critere);
		$libsecteurs = join('<br />',$tblsecteurs);

		$array['zonesidlibelle'] = $libzones;
		$array['secteuridlibelle'] = $libsecteurs;
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
		$xoopsTpl->append('demandes', $array);
	}
} else {	// Affichage du formulaire de recherche
	// Create page's title
	$xoopsTpl->assign('xoops_pagetitle', _MYJOB_DEMAND_SEARCH.' - '.$myts->htmlSpecialChars($xoopsModule->name()));
	$xoopsTpl->assign('search_results',false);
}

include_once XOOPS_ROOT_PATH.'/modules/myjob/include/demand_search_form.php';
$xoopsTpl->assign('search_form',$sform->render());

include_once(XOOPS_ROOT_PATH.'/footer.php');
?>
