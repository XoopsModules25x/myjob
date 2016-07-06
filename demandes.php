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
 * Liste les demandes d'emploi
 */
include __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'myjob_demandeindex.tpl';
include_once XOOPS_ROOT_PATH . '/header.php';
include_once XOOPS_ROOT_PATH . '/modules/myjob/include/functions.php';

$usedemands = myjob_getmoduleoption('usedemands');
if (!$usedemands) {
    redirect_header('index.php', 2, '');
    exit();
}
$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$limit = myjob_getmoduleoption('demandescount');

// flechebas.png flechebassel.png flechehaut.png flechehautsel.png
$tblsortorder = array('ASC', 'DESC');
$tblsortfield = array('datevalidation', 'experience', 'typeposte', 'titreannonce');

$sortorder = isset($_GET['sortorder']) ? (int)$_GET['sortorder'] : 0;
$sortfield = isset($_GET['sortfield']) ? (int)$_GET['sortfield'] : 0;

$tblimghaut = array_fill(0, count($tblsortfield), 'flechehaut.png');
$tblimgbas  = array_fill(0, count($tblsortfield), 'flechebas.png');

// TODO: Finir !
if ($sortorder == 0) {
} else {
}

// Inclusion de Prototype
$url_prototype = '<script type="text/javascript" src="' . XOOPS_URL . '/modules/myjob/js/prototype.js' . '"></script>';
$xoopsTpl->assign('xoops_module_header', $url_prototype);

// Lecture des types de postes
$typeposte_handler = xoops_getModuleHandler('typeposte', 'myjob');
$typespostes       = $typeposte_handler->getObjects();
$types             = array();
foreach ($typespostes as $onetypeposte) {
    $types[$onetypeposte->getVar('typeid')] = $onetypeposte->getVar('libelle');
}
$xoopsTpl->assign('typesoffres', $types);

// Relation offre/demande <-> zones géographiques
$demandofferzones_handler = xoops_getModuleHandler('demandofferzones', 'myjob');

// Relation offre/demande <-> secteurs d'activité
$demandoffersecteurs_handler = xoops_getModuleHandler('demandoffersecteurs', 'myjob');

$demande_handler = xoops_getModuleHandler('demande', 'myjob');

$criteria = new CriteriaCompo();
$criteria->add(new Criteria('datevalidation', '0', '<>'));
$criteria->add(new Criteria('dateexpiration', time(), '>'));
$criteria->setLimit($limit);
$criteria->setStart($start);
$criteria->setOrder('DESC');
$criteria->setSort('datevalidation');

$demandsvalid   = $demande_handler->getCount($criteria);
$demandswaiting = $demande_handler->getCount(new Criteria('datevalidation', '0'));
$demandes       = $demande_handler->getObjects($criteria);

if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    $xoopsTpl->assign('isadmin', true);
} else {
    $xoopsTpl->assign('isadmin', false);
}

if (myjob_getmoduleoption('rss')) {
    $xoopsTpl->assign('rssfeed_link', true);
}

// Les trucs généraux
$xoopsTpl->assign('welcome', sprintf(_MYJOB_WELCOME_DEMANDS, $xoopsConfig['sitename']));        // Les demandes d'emploi de ....
$xoopsTpl->assign('demandscount', sprintf(_MYJOB_DEMANDS_COUNT, $demandsvalid));                // Nombre total de demandes d'emploi (validées)
$xoopsTpl->assign('demandswaiting', sprintf(_MYJOB_DEMANDS_WAITING, $demandswaiting));        // Nombre total de demandes d'emploi en attente de validation

// Permission d'avoir un caddy ?
if (myjob_getmoduleoption('usecaddy') && myjob_MygetItemIds()) {
    $xoopsTpl->assign('caddy', true);
} else {
    $xoopsTpl->assign('caddy', false);
}

if ($demandsvalid > $limit) {
    include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
    $pagenav = new XoopsPageNav($demandsvalid, $limit, $start, 'start');
    $xoopsTpl->assign('pagenav', $pagenav->renderNav());
} else {
    $xoopsTpl->assign('pagenav', '');
}

$tbl_caddie = array();
if (isset($_SESSION['myjob_caddie'])) {
    $tbl_caddie = $_SESSION['myjob_caddie'];
}

foreach ($demandes as $onedemande) {
    $array = $onedemande->toArray();
    if (isset($tbl_caddie[$onedemande->getVar('demandid')])) {
        $array['inCaddy'] = true;
    } else {
        $array['inCaddy'] = false;
    }

    $critere = new Criteria('r.demandid', $onedemande->getVar('demandid'), '=');
    $critere->setSort('libelle');
	// Récupération des zones géographiques
    $tblzones = $demandofferzones_handler->getLibsWithRelation($critere);
    $libzones = implode('<br>', $tblzones);
    // R�cup�ration des secteurs
    $tblsecteurs = $demandoffersecteurs_handler->getLibsWithRelation($critere);
    $libsecteurs = implode('<br>', $tblsecteurs);

    $array['zonesidlibelle']   = $libzones;
    $array['secteuridlibelle'] = $libsecteurs;
    $tooltip                   = '';
    $tooltip .= myjob_fields('nom', false, 'demande') ? $onedemande->getVar('nom') . '<br>' : '';
    $tooltip .= myjob_fields('prenom', false, 'demande') ? $onedemande->getVar('prenom') . '<br>' : '';
    $tooltip .= myjob_fields('diplome', false, 'demande') ? $onedemande->getVar('diplome') . '<br>' : '';
    $tooltip .= myjob_fields('adresse', false, 'demande') ? $onedemande->getVar('adresse') . '<br>' : '';
    $tooltip .= myjob_fields('cp', false, 'demande') ? $onedemande->getVar('cp') . '<br>' : '';
    $tooltip .= myjob_fields('ville', false, 'demande') ? $onedemande->getVar('ville') . '<br>' : '';
    $tooltip .= myjob_fields('datenaiss', false, 'demande') ? $onedemande->getVar('datenaiss') . '<br>' : '';
    $tooltip .= myjob_fields('diplome', false, 'demande') ? $onedemande->getVar('diplome') . '<br>' : '';
    $tooltip .= myjob_fields('formation', false, 'demande') ? $onedemande->getVar('formation') . '<br>' : '';
    $tooltip .= myjob_fields('zonegeographique', false, 'demande') ? $onedemande->getVar('zonegeographique') . '<br>' : '';
    $tooltip .= myjob_fields('secteuractivite', false, 'demande') ? $onedemande->getVar('secteuractivite') . '<br>' : '';
    $tooltip .= myjob_fields('experiencedetail', false, 'demande') ? $onedemande->getVar('experiencedetail') . '<br>' : '';
    $array['tooltip'] = myjob_make_dhtml_tooltip($tooltip);
    $xoopsTpl->append('demandes', $array);
}

$myts = MyTextSanitizer::getInstance();
// Create page's title
$xoopsTpl->assign('xoops_pagetitle', _MYJOB_DEMANDS . ' - ' . $myts->htmlSpecialChars($xoopsModule->name()));
include_once(XOOPS_ROOT_PATH . '/footer.php');
