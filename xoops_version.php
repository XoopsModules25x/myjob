<?php
//  ------------------------------------------------------------------------ //
//                      MYJOB - MODULE FOR XOOPS 2.0.x                       //
//                  Copyright (c) Instant Zero - Hervé Thouzard              //
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

if (!defined('XOOPS_ROOT_PATH')) {
	die('XOOPS root path not defined');
}


$modversion['name'] = _MI_MYJOB_NAME;
$modversion['version'] = 2.1;
$modversion['description'] = _MI_MYJOB_DESC;
$modversion['credits'] = '';
$modversion['author'] = 'Hervé Thouzard - Instant Zero';
$modversion['help'] = '';
$modversion['license'] = 'Commercial';
$modversion['official'] = 0;
$modversion['image'] = 'images/myjob_logo.gif';
$modversion['dirname'] = 'myjob';

$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';

// Tables created by sql file (without prefix!)
$modversion['tables'][0]  = 'myjob_demande';
$modversion['tables'][1]  = 'myjob_demandoffersecteurs';
$modversion['tables'][2]  = 'myjob_demandofferzones';
$modversion['tables'][3]  = 'myjob_experience';
$modversion['tables'][4]  = 'myjob_offre';
$modversion['tables'][5]  = 'myjob_prolongation';
$modversion['tables'][6]  = 'myjob_salarytype';
$modversion['tables'][7]  = 'myjob_secteuractivite';
$modversion['tables'][8]  = 'myjob_sitfam';
$modversion['tables'][9]  = 'myjob_typeposte';
$modversion['tables'][10] = 'myjob_zonegeographique';

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Templates
$i=0;

$i++;
$modversion['templates'][$i]['file'] = 'myjob_demandeitem.html';
$modversion['templates'][$i]['description'] = 'View a specific demand';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_offreitem.html';
$modversion['templates'][$i]['description'] = 'View a specific offer';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_index.html';
$modversion['templates'][$i]['description'] = 'Module index';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_demandeindex.html';
$modversion['templates'][$i]['description'] = 'View all demands';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_offerindex.html';
$modversion['templates'][$i]['description'] = 'View all offers';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_rss.html';
$modversion['templates'][$i]['description'] = 'RSS Feed';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_mydemand.html';
$modversion['templates'][$i]['description'] = 'Manage your own demand';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_myoffers.html';
$modversion['templates'][$i]['description'] = 'Manage my offers';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_atom.html';
$modversion['templates'][$i]['description'] = 'Atom Feed';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_demandesearch.html';
$modversion['templates'][$i]['description'] = 'Search demands';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_offersearch.html';
$modversion['templates'][$i]['description'] = 'Search offers';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_demandscompare.html';
$modversion['templates'][$i]['description'] = 'Compare a limitied number of demands';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_offerscompare.html';
$modversion['templates'][$i]['description'] = 'Compare a limitied number of offers';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_offerscaddy.html';
$modversion['templates'][$i]['description'] = 'Offers Caddy';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_demandes_caddy.html';
$modversion['templates'][$i]['description'] = 'Caddy des demandes';

$i++;
$modversion['templates'][$i]['file'] = 'myjob_demandes_map.html';
$modversion['templates'][$i]['description'] = 'Visualisation des demandes sur une carte';




// Blocks
$cptb = 0;
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'myjob_demand_recent.php';
$modversion['blocks'][$cptb]['name'] = _MI_MYJOB_BNAME1;
$modversion['blocks'][$cptb]['description'] = "Shows recent demands";
$modversion['blocks'][$cptb]['show_func'] = "b_myjob_recent_demand_show";
$modversion['blocks'][$cptb]['edit_func'] = "b_myjob_recent_demand_edit";
$modversion['blocks'][$cptb]['options'] = "1|10|50|0|0|0|0";	// 1=Trié par date (2=par lectures), 10=Nombre d'éléments à afficher, 30=Longueur du titre, 0=longueur du texte d'intro, 0=type de poste (0=tous sinon l'id), 0=zone géographique (0=tous sinon l'id), 0=secteur d'activité (0=tous sinon l'id)
$modversion['blocks'][$cptb]['template'] = 'myjob_block_demand_recent.html';

$cptb++;
$modversion['blocks'][$cptb]['file'] = 'myjob_demand_top.php';
$modversion['blocks'][$cptb]['name'] = _MI_MYJOB_BNAME2;
$modversion['blocks'][$cptb]['description'] = "Shows top view demands";
$modversion['blocks'][$cptb]['show_func'] = "b_myjob_top_demand_show";
$modversion['blocks'][$cptb]['edit_func'] = "b_myjob_top_demand_edit";
$modversion['blocks'][$cptb]['options'] = "2|10|50|0|0|0|0";	// 1=Trié par date (2=par lectures), 10=Nombre d'éléments à afficher, 30=Longueur du titre, 0=longueur du texte d'intro, 0=type de poste (0=tous sinon l'id), 0=zone géographique (0=tous sinon l'id), 0=secteur d'activité (0=tous sinon l'id)
$modversion['blocks'][$cptb]['template'] = 'myjob_block_demand_top.html';

$cptb++;
$modversion['blocks'][$cptb]['file'] = 'myjob_demand_random.php';
$modversion['blocks'][$cptb]['name'] = _MI_MYJOB_BNAME3;
$modversion['blocks'][$cptb]['description'] = "Shows random demands";
$modversion['blocks'][$cptb]['show_func'] = "b_myjob_random_demand_show";
$modversion['blocks'][$cptb]['edit_func'] = "b_myjob_random_demand_edit";
$modversion['blocks'][$cptb]['options'] = "1|10|50|0|0|0|0";	// 1=Trié par date (2=par lectures), 10=Nombre d'éléments à afficher, 30=Longueur du titre, 0=longueur du texte d'intro, 0=type de poste (0=tous sinon l'id), 0=zone géographique (0=tous sinon l'id), 0=secteur d'activité (0=tous sinon l'id)
$modversion['blocks'][$cptb]['template'] = 'myjob_block_demand_random.html';

$cptb++;
$modversion['blocks'][$cptb]['file'] = 'myjob_offer_recent.php';
$modversion['blocks'][$cptb]['name'] = _MI_MYJOB_BNAME4;
$modversion['blocks'][$cptb]['description'] = "Shows recent offers";
$modversion['blocks'][$cptb]['show_func'] = "b_myjob_recent_offer_show";
$modversion['blocks'][$cptb]['edit_func'] = "b_myjob_recent_offer_edit";
$modversion['blocks'][$cptb]['options'] = "1|10|50|0";	// 1=Sort by date, 2=Sort by reads, 10=Elements count, 30=Title's length, 0=teaser
$modversion['blocks'][$cptb]['template'] = 'myjob_block_offer_recent.html';

$cptb++;
$modversion['blocks'][$cptb]['file'] = 'myjob_offer_top.php';
$modversion['blocks'][$cptb]['name'] = _MI_MYJOB_BNAME5;
$modversion['blocks'][$cptb]['description'] = "Shows top view offers";
$modversion['blocks'][$cptb]['show_func'] = "b_myjob_top_offer_show";
$modversion['blocks'][$cptb]['edit_func'] = "b_myjob_top_offer_edit";
$modversion['blocks'][$cptb]['options'] = "1|10|50|0";	// 1=Sort by date, 2=Sort by reads, 10=Elements count, 30=Title's length, 0=teaser
$modversion['blocks'][$cptb]['template'] = 'myjob_block_offer_top.html';

$cptb++;
$modversion['blocks'][$cptb]['file'] = 'myjob_offer_random.php';
$modversion['blocks'][$cptb]['name'] = _MI_MYJOB_BNAME6;
$modversion['blocks'][$cptb]['description'] = "Shows random offers";
$modversion['blocks'][$cptb]['show_func'] = "b_myjob_random_offer_show";
$modversion['blocks'][$cptb]['edit_func'] = "b_myjob_random_offer_edit";
$modversion['blocks'][$cptb]['options'] = "1|10|50|0";	// 1=Sort by date, 2=Sort by reads, 10=Elements count, 30=Title's length, 0=teaser
$modversion['blocks'][$cptb]['template'] = 'myjob_block_offer_random.html';

$cptb++;
$modversion['blocks'][$cptb]['file'] = 'myjob_stats.php';
$modversion['blocks'][$cptb]['name'] = _MI_MYJOB_BNAME7;
$modversion['blocks'][$cptb]['description'] = "Shows statistics about demands and offers (if used)";
$modversion['blocks'][$cptb]['show_func'] = "b_myjob_stats_show";
$modversion['blocks'][$cptb]['edit_func'] = "";
$modversion['blocks'][$cptb]['options'] = "";
$modversion['blocks'][$cptb]['template'] = 'myjob_block_stats.html';

$cptb++;
$modversion['blocks'][$cptb]['file'] = 'myjob_demand_top_wanted.php';
$modversion['blocks'][$cptb]['name'] = _MI_MYJOB_BNAME8;
$modversion['blocks'][$cptb]['description'] = "Shows top requested sectors/activities/geographical sectors in the demands (if used)";
$modversion['blocks'][$cptb]['show_func'] = "b_myjob_topx_demand_show";
$modversion['blocks'][$cptb]['edit_func'] = "b_myjob_topx_demand_edit";
$modversion['blocks'][$cptb]['options'] = "0|10|0";	// 0=Quoi voir ? (0=types de poste, 1=secteurs, 2=zones géographiques), 10=Nombre d'éléments à voir, 0=trier par compteur ou 1 par libellé
$modversion['blocks'][$cptb]['template'] = 'myjob_block_demand_xstats.html';

$cptb++;
$modversion['blocks'][$cptb]['file'] = 'myjob_offer_top_wanted.php';
$modversion['blocks'][$cptb]['name'] = _MI_MYJOB_BNAME9;
$modversion['blocks'][$cptb]['description'] = "Shows top requested sectors/activities/geographical sectors in the offers (if used)";
$modversion['blocks'][$cptb]['show_func'] = "b_myjob_topx_offer_show";
$modversion['blocks'][$cptb]['edit_func'] = "b_myjob_topx_offer_edit";
$modversion['blocks'][$cptb]['options'] = "0|10|0";	// 0=Quoi voir ? (0=types de poste, 1=secteurs, 2=zones géographiques), 10=Nombre d'éléments à voir, 0=trier par compteur ou 1 par libellé
$modversion['blocks'][$cptb]['template'] = 'myjob_block_offer_xstats.html';

// Menu
include_once XOOPS_ROOT_PATH.'/modules/myjob/include/functions.php';
$modversion['hasMain'] = 1;
$cptm = 0;
if(myjob_getmoduleoption('useoffers')) {
	$cptm++;
	$modversion['sub'][$cptm]['name'] = _MI_MYJOB_MENU1;
	$modversion['sub'][$cptm]['url'] = 'offres.php';
}
if(myjob_getmoduleoption('usedemands')) {
	$cptm++;
	$modversion['sub'][$cptm]['name'] = _MI_MYJOB_MENU2;
	$modversion['sub'][$cptm]['url'] = 'demandes.php';
}
if(myjob_getmoduleoption('useoffers')) {
	$cptm++;
	$modversion['sub'][$cptm]['name'] = _MI_MYJOB_MENU3;
	$modversion['sub'][$cptm]['url'] = 'submit-offer.php';
}
if(myjob_getmoduleoption('usedemands')) {
	$cptm++;
	$modversion['sub'][$cptm]['name'] = _MI_MYJOB_MENU4;
	$modversion['sub'][$cptm]['url'] = 'submit-demande.php';
}

if(myjob_getmoduleoption('usecaddy')) {
	$cptm++;
	$modversion['sub'][$cptm]['name'] = _MI_MYJOB_MENU7;
	$modversion['sub'][$cptm]['url'] = 'demandes-caddy.php';
}

if(myjob_getmoduleoption('demandsmap')) {
	$cptm++;
	$modversion['sub'][$cptm]['name'] = _MI_MYJOB_MENU8;
	$modversion['sub'][$cptm]['url'] = 'demandes-map.php';
}

/**
 * Mes demandes et mes offres
 */
global $xoopsUser, $xoopsModule;
if (is_object($xoopsUser) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $modversion['dirname'] && $xoopsModule->getVar('isactive')) {
	// On commence par les demandes d'emploi
	if(!isset($_SESSION['myjob_demands_count'])) {
	$demande_handler =& xoops_getmodulehandler('demande', 'myjob');
	$criteria = new CriteriaCompo();
	$criteria->add(new Criteria('datevalidation', '0','<>'));
	$criteria->add(new Criteria('dateexpiration', time(),'>'));
	$criteria->add(new Criteria('uid', $xoopsUser->getVar('uid'),'='));
		$_SESSION['myjob_demands_count'] = $demande_handler->getCount($criteria);
	}
	if($_SESSION['myjob_demands_count'] > 0) {
		$cptm++;
		$modversion['sub'][$cptm]['name'] = _MI_MYJOB_MENU5;
		$modversion['sub'][$cptm]['url'] = 'my-demande.php';
	}
}

// Search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'myjob_search';

// Comments
$modversion['hasComments'] = 0;

$cpto = 0;

/**
 * Activer les offres d'emploi ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'useoffers';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT0';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT0_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Activer les demandes d'emploi ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'usedemands';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT0B';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT0B_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;


/**
 * Durée par défaut des annonces
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'defaultduration';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT1';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT1_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 90;

/**
 * Approbation automatique des offres d'emploi ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'autoapproveoffers';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT2';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT2_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Approbation automatique des demandes d'emploi ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'autoapprovedemands';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT3';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT3_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Nombre d'offres d'emploi visibles par page
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'offrescount';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT4';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT4_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 10;

/**
 * Nombre de demandes d'emploi visibles par page
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'demandescount';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT5';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT5_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 10;

/**
 * Baratin légal affiché avant soumission d'une offre ou d'une demande d'emploi
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'legaltext';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT6';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT6_DSC';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = _MI_MYJOB_OPT6_DEFVAL;

/**
 * Texte à afficher durant la soumission d'une offre d'emploi
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'textoffer';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT11';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT11_DSC';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = _MI_MYJOB_OPT11_DEFVAL;

/**
 * Texte à afficher durant la soumission d'une demande d'emploi
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'textdemand';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT12';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT12_DSC';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = _MI_MYJOB_OPT12_DEFVAL;


/**
 * Activer les flux RSS ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'rss';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT7';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT7_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Permettre l'upload dans les offres
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'uploadoffers';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT8';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT8_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Permettre l'upload dans les demandes
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'uploaddemands';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT9';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT9_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Taille maxi des fichiers en upload
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'maxuploadsize';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT10';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT10_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1048576;

/**
 * Autoriser les prolongations ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'prolongation';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT13';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT13_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Adresse email devant être en BCC pour les demandes de contact
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'contactbcc';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT14';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT14_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = '';

/**
 * Permette aux anonymes de faire des demandes de contact ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'anonymousdemandcontact';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT15';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT15_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Longueur des bulles d'aide
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'infotips';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT16';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT16_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 300;

/**
 * Permettre l'export des demandes d'emploi au format vCard ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'vcarddemands';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT17';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT17_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Permettre l'export des offres d'emploi au format vCard ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'vcardoffers';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT18';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT18_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Rechercher depuis ... jours
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'searchperiods';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT19';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT19_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = "1, 2, 5, 10, 20, 30, 60, 100";

/**
 * Afficher un lien vers l'offre et la demande précédente et suivante ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'previousnextlink';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT20';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT20_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Afficher une table résumant les x dernières offres et demandes ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'summarytable';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT21';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT21_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 10;

/**
 * Autoriser l'utilisation des PDF ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'usepdf';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT22';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT22_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * METAGEN, Nombre maximal de meta mots clés à générer
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'metagen_maxwords';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT23';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT23_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 40;

/**
 * METAGEN - Ordre des mots clés
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'metagen_order';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT24';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT24_DSC';
$modversion['config'][$cpto]['formtype'] = 'select';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 5;
$modversion['config'][$cpto]['options'] = array('_MI_MYJOB_OPT241' => 0, '_MI_MYJOB_OPT242' => 1, '_MI_MYJOB_OPT243' => 2);

/**
 * METAGEN - Liste noire
 */
$cpto++;
// TODO: Passer en textarea
$modversion['config'][$cpto]['name'] = 'metagen_blacklist';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT25';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT25_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = "mais,ou,est,donc,or,ni,car";

/**
 * Activer les informations connexes ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'display_connexes';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT26';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT26_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Types Mime
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'picturemimetypes';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT27';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT27_DSC';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = "image/gif\nimage/jpeg\nimage/pjpeg\nimage/x-png\nimage/png";

/**
 * Autoriser la syndication des recherches ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'syndicateseach';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT28';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT28_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * Utiliser le caddy ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'usecaddy';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT29';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT29_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;


/**
 * Voir les offres d'emploi sur une carte ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'offersmap';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT30';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT30_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Voir les demandes d'emploi sur une carte ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'demandsmap';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT31';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT31_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;


/**
 * Clé pour l'API Google Maps
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'gmapapikey';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT32';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT32_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = '';

/**
 * Nombre d'éléments visibles dans l'administration
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'perpage';
$modversion['config'][$cpto]['title'] = '_MI_MYJOB_OPT33';
$modversion['config'][$cpto]['description'] = '_MI_MYJOB_OPT33_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 15;


// Notifications
$modversion['hasNotification'] = 1;
$modversion['notification']['lookup_file'] = 'include/notification.inc.php';
$modversion['notification']['lookup_func'] = 'myjob_notify_iteminfo';

// Catégories
$modversion['notification']['category'][1]['name'] = 'offres';
$modversion['notification']['category'][1]['title'] = _MI_MYJOB_NOTIFY1;
$modversion['notification']['category'][1]['description'] = _MI_MYJOB_NOTIFY1_DSC;
$modversion['notification']['category'][1]['subscribe_from'] = array('index.php', 'offres.php','offer-view.php','submit-offer.php');
$modversion['notification']['category'][1]['item_name'] = 'offreid';
$modversion['notification']['category'][1]['allow_bookmark'] = 1;

$modversion['notification']['category'][2]['name'] = 'demandes';
$modversion['notification']['category'][2]['title'] = _MI_MYJOB_NOTIFY2;
$modversion['notification']['category'][2]['description'] = _MI_MYJOB_NOTIFY2_DSC;
$modversion['notification']['category'][2]['subscribe_from'] = array('index.php', 'demandes.php','demande-view.php','submit-demande.php');
$modversion['notification']['category'][2]['item_name'] = 'demandid';
$modversion['notification']['category'][2]['allow_bookmark'] = 1;

$modversion['notification']['category'][3]['name'] = 'global';
$modversion['notification']['category'][3]['title'] = _MI_MYJOB_GLOBAL_NOTIFY;
$modversion['notification']['category'][3]['description'] = _MI_MYJOB_GLOBAL_NOTIFYDSC;
$modversion['notification']['category'][3]['subscribe_from'] = array('index.php', 'offres.php', 'demandes.php','offer-view.php','demande-view.php','submit-demande.php','submit-offer.php');


// Evènements
$modversion['notification']['event'][1]['name'] = 'offre_submited';
$modversion['notification']['event'][1]['category'] = 'global';
$modversion['notification']['event'][1]['title'] = _MI_MYJOB_NOTIFY3;
$modversion['notification']['event'][1]['caption'] = _MI_MYJOB_NOTIFY3_CAP;
$modversion['notification']['event'][1]['description'] = _MI_MYJOB_NOTIFY3_DSC;
$modversion['notification']['event'][1]['mail_template'] = 'offre_submited_notify';
$modversion['notification']['event'][1]['mail_subject'] = _MI_MYJOB_NOTIFY_MAIL1;
$modversion['notification']['event'][1]['admin_only'] = 1;

$modversion['notification']['event'][2]['name'] = 'demand_submitted';
$modversion['notification']['event'][2]['category'] = 'global';
$modversion['notification']['event'][2]['title'] = _MI_MYJOB_NOTIFY5;
$modversion['notification']['event'][2]['caption'] = _MI_MYJOB_NOTIFY5_CAP;
$modversion['notification']['event'][2]['description'] = _MI_MYJOB_NOTIFY5_DSC;
$modversion['notification']['event'][2]['mail_template'] = 'demand_submitted_notify';
$modversion['notification']['event'][2]['mail_subject'] = _MI_MYJOB_NOTIFY_MAIL3;
$modversion['notification']['event'][2]['admin_only'] = 1;
?>