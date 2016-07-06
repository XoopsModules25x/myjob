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

include __DIR__ . '/header.php';
include_once XOOPS_ROOT_PATH . '/modules/myjob/include/functions.php';

$useoffers  = myjob_getmoduleoption('useoffers');
$usedemands = myjob_getmoduleoption('usedemands');

$offreid  = isset($_GET['offreid']) ? (int)$_GET['offreid'] : 0;
$demandid = isset($_GET['demandid']) ? (int)$_GET['demandid'] : 0;

// Lecture des zones géographiques
$zonegeographique_handler = xoops_getModuleHandler('zonegeographique', 'myjob');
$zonegeographiques        = $zonegeographique_handler->getObjects();
$zones                    = array();
foreach ($zonegeographiques as $onezonegeographique) {
    $zones[$onezonegeographique->getVar('zoneid')] = $onezonegeographique->getVar('libelle');
}

// Lecture des secteurs d'activité
$secteuractivite_handler = xoops_getModuleHandler('secteuractivite', 'myjob');
$secteuractivites        = $secteuractivite_handler->getObjects();
$secteurs                = array();
foreach ($secteuractivites as $onesecteuractivite) {
    $secteurs[$onesecteuractivite->getVar('secteurid')] = $onesecteuractivite->getVar('libelle');
}

// Chargement de la liste des types de postes
$typeposte_handler = xoops_getModuleHandler('typeposte', 'myjob');
$typespostes       = $typeposte_handler->getObjects();
$types             = array();
foreach ($typespostes as $onetypeposte) {
    $types[$onetypeposte->getVar('typeid')] = $onetypeposte->getVar('libelle');
}
// Les situations de famille
$sitfam_handler = xoops_getModuleHandler('sitfam', 'myjob');
$sitesfams      = $sitfam_handler->getObjects();
$tblsitfam      = array();
foreach ($sitesfams as $onesitfam) {
    $tblsitfam[$onesitfam->getVar('sitfamid')] = $onesitfam->getVar('libelle');
}

// Relation offre/demande <-> zones géographiques
$demandofferzones_handler = xoops_getModuleHandler('demandofferzones', 'myjob');

// Relation offre/demande <-> secteurs d'activité
$demandoffersecteurs_handler = xoops_getModuleHandler('demandoffersecteurs', 'myjob');

if (!empty($offreid) && $useoffers) {
    $offre_handler = xoops_getModuleHandler('offre', 'myjob');
    $offre         = $offre_handler->get($offreid);
    if (!$offre || $offre->getVar('online') != 1) {
        redirect_header('index.php', 2, _ERRORS);
        exit();
    }
    PrintOffer();
}

if (!empty($demandid) && $usedemands) {
    $demande_handler = xoops_getModuleHandler('demande', 'myjob');
    $demande         = $demande_handler->get($demandid);
    if (!$demande || $demande->getVar('datevalidation') == 0 && !isset($_GET['op'])) {
        redirect_header('index.php', 2, _ERRORS);
        exit();
    }
    $critere = new Criteria('r.demandid', $demande->getVar('demandid'), '=');
    $critere->setSort('libelle');
	// Récupération des zones géographiques
    $tblzones = $demandofferzones_handler->getLibsWithRelation($critere);
    $libzones = implode('<br>', $tblzones);
	// Récupération des secteurs
    $tblsecteurs = $demandoffersecteurs_handler->getLibsWithRelation($critere);
    $libsecteurs = implode('<br>', $tblsecteurs);
    PrintDemand();
}

// Impression d'une offre d'emploi
function PrintOffer()
{
    global $xoopsConfig, $xoopsModule, $offre, $types, $tblsitfam;
    $myts = MyTextSanitizer::getInstance();
    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
    echo '<html><head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=' . _CHARSET . '" />';
    echo '<title>' . $myts->htmlSpecialChars(_MYJOB_OFFER_PRINTABLE) . ' - ' . $xoopsConfig['sitename'] . '</title>';
    echo '<meta name="AUTHOR" content="' . $xoopsConfig['sitename'] . '" />';
    echo '<meta name="COPYRIGHT" content="Copyright (c) 2005 by ' . $xoopsConfig['sitename'] . '" />';
    echo '<meta name="DESCRIPTION" content="' . $xoopsConfig['slogan'] . '" />';
    echo '<meta name="GENERATOR" content="' . XOOPS_VERSION . '" />';
    echo '<body bgcolor="#ffffff" text="#000000" onload="window.print()">
        <table border="0"><tr><td align="center">
        <table border="0" width="100%" cellpadding="0" cellspacing="1" bgcolor="#000000"><tr><td>
        <table border="0" width="100%" cellpadding="20" cellspacing="1" bgcolor="#ffffff"><tr><td align="center">
        <img src="' . XOOPS_URL . '/images/logo.gif" border="0" alt="" /><br><br>
        <h3>' . $offre->getVar('description') . '</h3>';
    echo '<tr valign="top" style="font:12px;"><td>';
    echo "<table border='0' width='100%' align='center'>";
    echo '<tr><td>' . _MYJOB_OFFER_ENTREPRISE . '</td><td>' . $offre->getVar('nomentreprise') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_SECTEUR . '</td><td>' . $offre->getVar('secteuractivite') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_PROFIL . '</td><td>' . $offre->getVar('profil') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_LIEU . '</td><td>' . $offre->getVar('lieuactivite') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_ADRESSE . '</td><td>' . $offre->getVar('adresse') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_CP . '</td><td>' . $offre->getVar('cp') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_VILLE . '</td><td>' . $offre->getVar('ville') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_DATEDISPO . '</td><td>' . formatTimestamp($offre->getVar('datedispo'), 's') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_CONTACT . '</td><td>' . $offre->getVar('contact') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_EMAIL . '</td><td>' . $offre->getVar('email') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_TEL . '</td><td>' . $offre->getVar('telephone') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_TYPEPOSTE . '</td><td>' . $types[$offre->getVar('typeposte')] . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_TITREANNONCE . '</td><td>' . $offre->getVar('titreannonce') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_DESCRIPTION . '</td><td>' . $offre->getVar('description') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_EXPERIENCE . '</td><td>' . $offre->getVar('libelle_experience') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_STATUT . '</td><td>' . $offre->getVar('statut') . '</td></tr>';
    echo '<tr><td>' . _MYJOB_OFFER_DATESUBMIT . '</td><td>' . formatTimestamp($offre->getVar('datesoumission'), 's') . '</td></tr>';
    echo '</table>';
    echo '</td></tr></table></td></tr></table><br><br>';
    printf(_MYJOB_THISCOMESFROM, $xoopsConfig['sitename']);
    echo '<br><a href="' . XOOPS_URL . '/">' . XOOPS_URL . '</a><br><br>' . _MYJOB_URLFOROFFER . ' <br><a href="' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/offer-view.php?offerid=' . $offre->getVar('offreid') . '">' . XOOPS_URL . '/modules/myjob/offer-view.php?offerid='
         . $offre->getVar('offreid') . '</a></td></tr></table></body></html>';
}

// Impression d'une demande d'emploi
function PrintDemand()
{
    global $xoopsConfig, $xoopsModule, $demande, $types, $tblsitfam, $zones, $secteurs, $libzones, $libsecteurs;
    $myts = MyTextSanitizer::getInstance();
    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
    echo '<html><head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=' . _CHARSET . '" />';
    echo '<title>' . $myts->htmlSpecialChars(_MYJOB_DEMAND_PRINTABLE) . ' - ' . $xoopsConfig['sitename'] . '</title>';
    echo '<meta name="AUTHOR" content="' . $xoopsConfig['sitename'] . '" />';
    echo '<meta name="COPYRIGHT" content="Copyright (c) 2005 by ' . $xoopsConfig['sitename'] . '" />';
    echo '<meta name="DESCRIPTION" content="' . $xoopsConfig['slogan'] . '" />';
    echo '<meta name="GENERATOR" content="' . XOOPS_VERSION . '" />';
    echo '<body bgcolor="#ffffff" text="#000000" onload="window.print()">
        <table border="0"><tr><td align="center">
        <table border="0" width="100%" cellpadding="0" cellspacing="1" bgcolor="#000000"><tr><td>
        <table border="0" width="100%" cellpadding="20" cellspacing="1" bgcolor="#ffffff"><tr><td align="center">
        <img src="' . XOOPS_URL . '/images/logo.gif" border="0" alt="" /><br><br>
        <h3>' . $demande->getVar('titreannonce') . '</h3>';
    echo '<tr valign="top" style="font:12px;"><td>';
    echo "<table border='0' width='100%' align='center'>";
    if (myjob_fields('nom', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_NOM . '</td><td>' . $demande->getVar('nom') . '</td></tr>';
    }
    if (myjob_fields('prenom', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_PRENOM . '</td><td>' . $demande->getVar('prenom') . '</td></tr>';
    }
    if (myjob_fields('adresse', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_ADRESSE . '</td><td>' . $demande->getVar('adresse') . '</td></tr>';
    }
    if (myjob_fields('cp', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_CP . '</td><td>' . $demande->getVar('cp') . '</td></tr>';
    }
    if (myjob_fields('ville', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_VILLE . '</td><td>' . $demande->getVar('ville') . '</td></tr>';
    }
    if (myjob_fields('telephone', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_TELEPHONE . '</td><td>' . $demande->getVar('telephone') . '</td></tr>';
    }
    if (myjob_fields('email', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_EMAIL . '</td><td>' . $demande->getVar('email') . '</td></tr>';
    }
    if (myjob_fields('datenaiss', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_DATENAISS . '</td><td>' . $demande->getVar('datenaiss') . '</td></tr>';
    }
    if (myjob_fields('datesoumission', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_DATESOUMISSION . '</td><td>' . formatTimestamp($demande->getVar('datesoumission'), 's') . '</td></tr>';
    }
    if (myjob_fields('dateexpiration', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_EXPIRATION . '</td><td>' . formatTimestamp($demande->getVar('dateexpiration'), 's') . '</td></tr>';
    }
    if (myjob_fields('titreannonce', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_DESCRIPTION . '</td><td>' . $demande->getVar('titreannonce') . '</td></tr>';
    }
    if (myjob_fields('diplome', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_DIPLOME . '</td><td>' . $demande->getVar('diplome') . '</td></tr>';
    }
    if (myjob_fields('formation', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_FORMATION . '</td><td>' . $demande->getVar('formation') . '</td></tr>';
    }
    if (myjob_fields('typeposte', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_TYPEPOSTE . '</td><td>' . $types[$demande->getVar('typeposte')] . '</td></tr>';
    }
    if (myjob_fields('zoneid', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_ZONEGEOGRAPHIQUE . '</td><td>' . $libzones . '</td></tr>';
    }
    if (myjob_fields('zonegeographique', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_ZONEGEOGRAPHIQUEA . '</td><td>' . $demande->getVar('zonegeographique') . '</td></tr>';
    }
    if (myjob_fields('secteurid', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_SECTEURACTIVITE . '</td><td>' . $libsecteurs . '</td></tr>';
    }
    if (myjob_fields('secteuractivite', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_SECTEURACTIVITEA . '</td><td>' . $demande->getVar('secteuractivite') . '</td></tr>';
    }
    if (myjob_fields('experience', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_EXPERIENCE . '</td><td>' . $demande->getVar('libelle_experience') . '</td></tr>';
    }
    if (myjob_fields('experiencedetail', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_OFFER_EXPERIENCEDETAIL . '</td><td>' . $demande->getVar('experiencedetail') . '</td></tr>';
    }
    if (myjob_fields('datedispo', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_DATEDISPO . '</td><td>' . formatTimestamp($demande->getVar('datedispo'), 's') . '</td></tr>';
    }
    if (myjob_fields('parain', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_PARAIN . '</td><td>' . $demande->getVar('parain') . '</td></tr>';
    }
    if (myjob_fields('datevalidation', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_OFFER_DATEVALID . '</td><td>' . formatTimestamp($demande->getVar('datevalidation'), 's') . '</td></tr>';
    }
    if (myjob_fields('langues', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_LANGUES . '</td><td>' . $demande->getVar('langues') . '</td></tr>';
    }
    if (myjob_fields('zonelibre', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_ZONELIBRE . '</td><td>' . $demande->getVar('zonelibre') . '</td></tr>';
    }
    if (myjob_fields('sitfam', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_SITFAM . '</td><td>' . $tblsitfam[$demande->getVar('sitfam')] . '</td></tr>';
    }
    if (myjob_fields('competences', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_COMPETENCES . '</td><td>' . $demande->getVar('competences') . '</td></tr>';
    }
    if (myjob_fields('divers', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_DIVERS . '</td><td>' . $demande->getVar('divers') . '</td></tr>';
    }
    if (myjob_fields('hits', false, 'demande')) {
        echo '<tr><td>' . _MYJOB_DEMAND_HITS . '</td><td>' . $demande->getVar('hits') . '</td></tr>';
    }

    echo '</table>';
    echo '</td></tr></table></td></tr></table><br><br>';
    printf(_MYJOB_THISCOMESFROM, $xoopsConfig['sitename']);
    echo '<br><a href="' . XOOPS_URL . '/">' . XOOPS_URL . '</a><br><br>' . _MYJOB_URLFORDEMAND . ' <br><a href="' . XOOPS_URL . '/modules/myjob/demande-view.php?demandid=' . $demande->getVar('demandid') . '">' . XOOPS_URL . '/modules/myjob/demande-view.php?demandid='
         . $demande->getVar('demandid') . '</a></td></tr></table></body></html>';
}
