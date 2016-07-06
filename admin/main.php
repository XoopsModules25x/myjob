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

include_once __DIR__ . '/../../../include/cp_header.php';
include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
include_once XOOPS_ROOT_PATH . '/modules/myjob/admin/functions.php';
include_once XOOPS_ROOT_PATH . '/modules/myjob/include/functions.php';

if (file_exists(XOOPS_ROOT_PATH . '/modules/myjob/language/' . $xoopsConfig['language'] . '/main.php')) {
    include_once XOOPS_ROOT_PATH . '/modules/myjob/language/' . $xoopsConfig['language'] . '/main.php';
} else {
    include_once XOOPS_ROOT_PATH . '/modules/myjob/language/english/main.php';
}

/**
 * Longueur par défaut des titres
 */
$longtit    = 50;    // Longuer par défaut des titres
$field_name = XOOPS_ROOT_PATH . '/uploads/myjob_fields.txt';    // Nom du fichier contenant les champs obligatoires et les champs publics

/**
 * Lecture des paramètres
 */
$useoffers  = myjob_getmoduleoption('useoffers');
$usedemands = myjob_getmoduleoption('usedemands');
$limit      = myjob_getmoduleoption('perpage');    // Nombre maximum d'éléments à afficher dans l'admin

/**
 * Lecture des listes (types de postes et situations de famille)
 */
$critere = new Criteria('libelle', '###', '<>');
$critere->setSort('libelle');

// Types de poste
$typeposte_handler = xoops_getModuleHandler('typeposte', 'myjob');
$typespostes       = $typeposte_handler->getObjects($critere);
$types             = array();
foreach ($typespostes as $onetypeposte) {
    $types[$onetypeposte->getVar('typeid')] = $onetypeposte->getVar('libelle');
}

// Expérience
$experience_handler = xoops_getModuleHandler('experience', 'myjob');

// Zones géographiques
$zonegeographique_handler = xoops_getModuleHandler('zonegeographique', 'myjob');
$zonegeographiques        = $zonegeographique_handler->getObjects($critere);
$zones                    = array();
foreach ($zonegeographiques as $onezonegeographique) {
    $zones[$onezonegeographique->getVar('zoneid')] = $onezonegeographique->getVar('libelle');
}

// Secteurs d'activité
$secteuractivite_handler = xoops_getModuleHandler('secteuractivite', 'myjob');
$secteuractivites        = $secteuractivite_handler->getObjects($critere);
$secteurs                = array();
foreach ($secteuractivites as $onesecteuractivite) {
    $secteurs[$onesecteuractivite->getVar('secteurid')] = $onesecteuractivite->getVar('libelle');
}

// Situations familialles
$sitfam_handler = xoops_getModuleHandler('sitfam', 'myjob');
$sitesfams      = $sitfam_handler->getObjects($critere);
$tblsitfam      = array();
foreach ($sitesfams as $onesitfam) {
    $tblsitfam[$onesitfam->getVar('sitfamid')] = $onesitfam->getVar('libelle');
}

// Type de rémunération
$salarytype_handler = xoops_getModuleHandler('salarytype', 'myjob');
$salarytypes        = $salarytype_handler->getObjects($critere);
$tblsalarytype      = array();
foreach ($salarytypes as $onesalarytype) {
    $tblsalarytype[$onesalarytype->getVar('salarytypeid')] = $onesalarytype->getVar('libelle');
}

// Prolongations
$prolongation_handler = xoops_getModuleHandler('prolongation', 'myjob');

// Relation offre/demande <-> zones géographiques
$demandofferzones_handler = xoops_getModuleHandler('demandofferzones', 'myjob');

// Relation offre/demande <-> secteurs d'activité
$demandoffersecteurs_handler = xoops_getModuleHandler('demandoffersecteurs', 'myjob');

/**
 * Edition d'un élément de type sitfam ou typepost
 */
function EditElement($itemid, $handlername, $id, $label, $tabnum, $redirect = '')
{
    include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    $obj_handler = xoops_getModuleHandler($handlername, 'myjob');
    $item        = $obj_handler->get($itemid);
    $sform       = new XoopsThemeForm(_AM_MYJOB_ITEMEDIT, 'itemform', XOOPS_URL . '/modules/myjob/admin/main.php');
    $sform->setExtra('enctype="multipart/form-data"');
    $sform->addElement(new XoopsFormText(_AM_MYJOB_DESCRIPTION, 'item_title', 50, 255, $item->getVar($label)), true);
    $sform->addElement(new XoopsFormHidden('itemid', $item->getVar($id)), false);
    $sform->addElement(new XoopsFormHidden('op', 'itemsave'), false);
    $sform->addElement(new XoopsFormHidden('handlername', $handlername), false);
    $sform->addElement(new XoopsFormHidden('id', $id), false);
    $sform->addElement(new XoopsFormHidden('label', $label), false);
    $sform->addElement(new XoopsFormHidden('tabnum', $tabnum), false);
    $sform->addElement(new XoopsFormHidden('redirect', $redirect), false);

    if (trim($item->getVar('image')) != '' && file_exists(XOOPS_UPLOAD_PATH . '/' . trim($item->getVar('image')))) {
        $sform->addElement(new XoopsFormLabel(_AM_MYJOB_ACTUAL_PICTURE, "<img src='" . XOOPS_UPLOAD_URL . '/' . $item->getVar('image') . "' alt='' border='0' />"));
    }
    $sform->addElement(new XoopsFormFile(_AM_MYJOB_PICTURE, 'attachedfile', myjob_getmoduleoption('maxuploadsize')), false);

    $button_tray = new XoopsFormElementTray('', '');
    $submit_btn  = new XoopsFormButton('', 'post', _AM_MYJOB_MODIFY, 'submit');
    $button_tray->addElement($submit_btn);
    $sform->addElement($button_tray);
    $sform->display();
    unset($obj_handler);
    unset($item);
}

/**
 * Fonction chargée de lister les éléments d'un objet
 */
function ListElements($handler, $id, $lib, $handlername, $tabnum, $redirect, $start)
{
    global $limit;
    $critere = new Criteria('libelle', '###', '<>');
    $critere->setSort($lib);
    $critere->setStart($start);
    $critere->setLimit($limit);
    $elements = $handler->getObjects($critere);
    $class    = '';
    foreach ($elements as $oneelement) {
        $class    = ($class === 'even') ? 'odd' : 'even';
        $paramsup = '&fieldid=' . $id . '&fieldlabel=' . $lib . '&redirect=' . $redirect;
        $edit     =
            "<a title='" . _AM_MYJOB_EDIT . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=itemedit&itemid=' . $oneelement->getVar($id) . '&handlertype=' . $handlername . '&tabnum=' . $tabnum . $paramsup . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/edit.gif' alt='"
            . _AM_MYJOB_EDIT . "'></a>";
        $dele     =
            "<a title='" . _AM_MYJOB_DELETE . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=itemdelete&itemid=' . $oneelement->getVar($id) . '&handlername=' . $handlername . '&tabnum=' . $tabnum . $paramsup . "'" . myjob_JavascriptLinkConfirm(_AM_MYJOB_CONF_DELETE) . "><img src='"
            . XOOPS_URL . "/modules/myjob/assets/images/delete.gif' alt='" . _AM_MYJOB_DELETE . "'></a>";
        echo "<tr class='" . $class . "'><td align='center'>" . $oneelement->getVar($id) . '</td><td>' . $oneelement->getVar($lib) . "</td><td align='center'>" . $edit . ' ' . $dele . '</td></tr>';
    }

    return $class;
}

// **********************************************************************************************************************************************
// **** Main
// **********************************************************************************************************************************************
$op = 'default';
if (isset($_POST['op'])) {
    $op = $_POST['op'];
} else {
    if (isset($_GET['op'])) {
        $op = $_GET['op'];
    }
}
$offre_handler   = xoops_getModuleHandler('offre', 'myjob');
$demande_handler = xoops_getModuleHandler('demande', 'myjob');

switch ($op) {
    /**
     * Gestion des offres d'emploi
     */
    case 'viewoffers':
        xoops_cp_header();
        // myjob_adminmenu(1);
        if ($useoffers) {
            echo '<br><br>';
            // Offres en attente
            $offerswaitingcount = 0;
            $offerswaitingcount = $offre_handler->getCount(new Criteria('online', '0', '='));
            if ($offerswaitingcount > 0) {
                myjob_collapsableBar('offerswait', 'offerswaiticon');
                echo "<img onclick='toggle('toptable'); toggleIcon('toptableicon');' id='offerswaiticon' name='offerswaiticon' src=" . XOOPS_URL . "/modules/myjob/assets/images/close12.gif alt='' /></a>&nbsp;" . _AM_MYJOB_OFFERSWAITING . '</h4>';
                echo "<div id='offerswait'>";
                echo '<br>';
                $offerswaiting = $offre_handler->getObjects(new Criteria('online', '0', '='));
                echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
                echo '<tr><th>' . _MYJOB_OFFER_ENTREPRISE . '</th><th>' . _MYJOB_OFFER_DATESUBMIT . '</th><th>' . _MYJOB_OFFER_SECTEUR . '</th><th>' . _MYJOB_OFFER_TYPEPOSTE . '</th><th>' . _MYJOB_OFFER_DESCRIPTION . "</th><th align='center'>" . _AM_MYJOB_ACTION . '</th></tr>';
                $class = '';
                foreach ($offerswaiting as $oneoffre) {
                    $class = ($class === 'even') ? 'odd' : 'even';
                    $edit  = "<a title='" . _AM_MYJOB_EDIT . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=offeredit&offerid=' . $oneoffre->getVar('offreid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/edit.gif' alt='" . _AM_MYJOB_EDIT . "'></a>";
                    $dele  = "<a title='" . _AM_MYJOB_DELETE . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=offerdelete&offerid=' . $oneoffre->getVar('offreid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/delete.gif' alt='" . _AM_MYJOB_DELETE . "'></a>";
                    echo "<tr class='" . $class . "'><td>" . xoops_substr($oneoffre->getVar('nomentreprise'), 0, $longtit) . '</td><td>' . formatTimestamp($oneoffre->getVar('datesoumission')) . '</td><td>' . xoops_substr($oneoffre->getVar('secteuractivite'), 0, $longtit) . '</td><td>'
                         . $types[$oneoffre->getVar('typeposte')] . '</td><td>' . xoops_substr($oneoffre->getVar('titreannonce'), 0, $longtit) . "</td><td align='center'>" . $edit . ' ' . $dele . '</td></tr>';
                }
                echo '</table><br><br>';
                echo '</div>';
            }

            // Les (x) dernières offres
            $start            = isset($_GET['start']) ? (int)$_GET['start'] : 0;
            $offersvalidcount = $offre_handler->getCount(new Criteria('online', '1', '='));
            $pagenav          = new XoopsPageNav($offersvalidcount, $xoopsModuleConfig['offrescount'], $start, 'start', 'op=viewoffers');
            $limit            = myjob_getmoduleoption('offrescount');
            $critere          = new Criteria('online', '1', '=');
            $critere->setLimit($limit);
            $critere->setStart($start);
            $critere->setSort('datesoumission');
            $critere->setOrder('DESC');
            $offres = $offre_handler->getObjects($critere);
            myjob_collapsableBar('offersvalid', 'offersvalidicon');
            echo "<img onclick='toggle('toptable'); toggleIcon('toptableicon');' id='offersvalidicon' name='offersvalidicon' src=" . XOOPS_URL . "/modules/myjob/assets/images/close12.gif alt='' /></a>&nbsp;" . sprintf(_AM_MYJOB_LAST_OFFERS, $limit) . '</h4>';
            echo "<div id='offersvalid'>";
            echo '<br>';
            echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
            echo '<tr><th>' . _MYJOB_OFFER_ENTREPRISE . '</th><th>' . _MYJOB_OFFER_DATESUBMIT . '</th><th>' . _MYJOB_OFFER_SECTEUR . '</th><th>' . _MYJOB_OFFER_TYPEPOSTE . '</th><th>' . _MYJOB_OFFER_DESCRIPTION . '</th><th>' . _AM_MYJOB_VIEW . "</th><th align='center'>" . _AM_MYJOB_ACTION
                 . '</th></tr>';
            $class = '';
            foreach ($offres as $oneoffre) {
                $class = ($class === 'even') ? 'odd' : 'even';
                $edit  = "<a title='" . _AM_MYJOB_EDIT . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=offeredit&offerid=' . $oneoffre->getVar('offreid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/edit.gif' alt='" . _AM_MYJOB_EDIT . "'></a>";
                $dele  = "<a title='" . _AM_MYJOB_DELETE . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=offerdelete&offerid=' . $oneoffre->getVar('offreid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/delete.gif' alt='" . _AM_MYJOB_DELETE . "'></a>";
                $unval = "<a title='" . _AM_MYJOB_UNVALIDATE . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=offerunval&offerid=' . $oneoffre->getVar('offreid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/unvalidate.gif' alt='" . _AM_MYJOB_UNVALIDATE . "'></a>";
                echo "<tr class='" . $class . "'><td>" . xoops_substr($oneoffre->getVar('nomentreprise'), 0, $longtit) . '</td><td>' . formatTimestamp($oneoffre->getVar('datesoumission')) . '</td><td>' . xoops_substr($oneoffre->getVar('secteuractivite'), 0, $longtit) . '</td><td>'
                     . $types[$oneoffre->getVar('typeposte')] . '</td><td>' . xoops_substr($oneoffre->getVar('titreannonce'), 0, $longtit) . "</td><td align='right'>" . $oneoffre->getVar('hits') . "</td><td align='center'>" . $edit . ' ' . $dele . ' ' . $unval . '</td></tr>';
            }
            echo "</table><div align='right'>" . $pagenav->renderNav() . '</div></div><br>';
        } else {
            echo '<h1>' . _AM_MYJOB_OFFERS_NOUSE . '</h1>';
        }
        break;

    /**
     * Dévalidation d'une offre d'emploi
     */
    case 'offerunval':
        if ($useoffers) {
            if (isset($_GET['offerid'])) {
                $offre_handler->unvalidate((int)$_GET['offerid']);
                myjob_updateCache();
                redirect_header('main.php?op=viewoffers', 2, _AM_MYJOB_UNVALIDATE_OK);
            } else {
                redirect_header('main.php?op=viewoffers', 2, _ERRORS);
                exit();
            }
        }
        break;

    /**
     * Edition d'une offre d'emploi
     */
    case 'offeredit':
        xoops_cp_header();
        // myjob_adminmenu(1);
        if ($useoffers) {
            $warning = myjob_getmoduleoption('autoapproveoffers');
            if (isset($_GET['offerid'])) {
                $offerid = (int)$_GET['offerid'];
            } else {
                redirect_header('main.php', 2, _ERRORS);
                exit();
            }
            $offre     = $offre_handler->get($offerid);
            $adminside = true;
            include_once XOOPS_ROOT_PATH . '/modules/myjob/include/offerform.php';
        } else {
            echo '<h1>' . _AM_MYJOB_OFFERS_NOUSE . '</h1>';
        }
        break;

    /**
     * Suppression d'une offre d'emploi
     */
    case 'offerdelete':
        if ($useoffers) {
            if (!isset($_POST['ok'])) {
                xoops_cp_header();
                echo '<h4>' . _AM_MYJOB_DELETION . '</h4>';
                if (isset($_GET['offerid'])) {
                    $offerid = (int)$_GET['offerid'];
                } else {
                    redirect_header('main.php', 2, _ERRORS);
                    exit();
                }
                $offre = $offre_handler->get($offerid);
                xoops_confirm(array('op' => 'offerdelete', 'offerid' => $offerid, 'ok' => 1), 'main.php', _AM_MYJOB_WARNING_DELETE_OFFER . '<br>' . $offre->getVar('titreannonce'));
            } else {
                if (isset($_POST['offerid'])) {
                    $offerid = (int)$_POST['offerid'];
                } else {
                    redirect_header('main.php', 2, _ERRORS);
                    exit();
                }
                $offre = $offre_handler->get($offerid);
                $offre_handler->delete($offre);
                myjob_updateCache();
                redirect_header('main.php?op=viewoffers', 1, _AM_MYJOB_DBUPDATED);
                exit();
            }
        }
        break;

    /**
     * Validation ou suppression d'une demande de prolongation pour une demande
     */
    case 'demandprolongvalid':
        $op = 'viewdemands';
        if (isset($_GET['action']) && $_GET['action'] == 'yes') {
            $demandid = (int)$_GET['demandid'];
            $demande_handler->prolongate($demandid);
            $prolongation = $prolongation_handler->getbydemandid($demandid);
            $prolongation_handler->delete($prolongation, true);
            redirect_header('main.php?op=viewdemands', 2, _AM_MYJOB_DBUPDATED);
        }

        if (isset($_GET['action']) && $_GET['action'] == 'no') {
            $demandid     = (int)$_GET['demandid'];
            $prolongation = $prolongation_handler->getbydemandid($demandid);
            $prolongation_handler->delete($prolongation, true);
            redirect_header('main.php?op=viewdemands', 2, _AM_MYJOB_DBUPDATED);
        }
        break;

    /**
     * Gestion des demandes d'emploi
     */
    case 'viewdemands':
        xoops_cp_header();
        // myjob_adminmenu(2);
        if ($usedemands) {
            echo '<br><br>';
            // Demandes en attente ************************************************************************************
            $demandswaitingcound = 0;
            $demandswaitingcound = $demande_handler->getCount(new Criteria('datevalidation', '0', '='));
            if ($demandswaitingcound > 0) {
                myjob_collapsableBar('demandswait', 'demandswaiticon');
                echo "<img onclick='toggle('toptable'); toggleIcon('toptableicon');' id='demandswaiticon' name='demandswaiticon' src=" . XOOPS_URL . "/modules/myjob/assets/images/close12.gif alt='' /></a>&nbsp;" . _AM_MYJOB_DEMANDSWAITING . '</h4>';
                echo "<div id='demandswait'>";
                echo '<br>';
                $demandswaiting = $demande_handler->getObjects(new Criteria('datevalidation', '0', '='));
                echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
                echo '<tr><th>' . _MYJOB_DEMAND_ID . '</th><th>' . _MYJOB_DEMAND_DATESOUMISSION . '</th><th>' . _MYJOB_DEMAND_DESCRIPTION . '</th><th>' . _MYJOB_DEMAND_NOM . '</th><th>' . _MYJOB_DEMAND_PRENOM . "</th><th align='center'>" . _AM_MYJOB_ACTION . '</th></tr>';
                $class = '';
                foreach ($demandswaiting as $onedemande) {
                    $class = ($class === 'even') ? 'odd' : 'even';
                    $edit  = "<a title='" . _AM_MYJOB_EDIT . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=demandedit&demandid=' . $onedemande->getVar('demandid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/edit.gif' alt='" . _AM_MYJOB_EDIT . "'></a>";
                    $dele  = "<a title='" . _AM_MYJOB_DELETE . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=demanddelete&demandid=' . $onedemande->getVar('demandid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/delete.gif' alt='" . _AM_MYJOB_DELETE . "'></a>";
                    echo "<tr class='" . $class . "'><td align='center'>" . $onedemande->getVar('demandid') . '</td><td>' . formatTimestamp($onedemande->getVar('datesoumission'), 's') . '</td><td>' . xoops_substr($onedemande->getVar('titreannonce'), 0, $longtit) . '</td><td>'
                         . xoops_substr($onedemande->getVar('nom'), 0, $longtit) . '</td><td>' . xoops_substr($onedemande->getVar('prenom'), 0, $longtit) . "</td><td align='center'>" . $edit . ' ' . $dele . '</td></tr>';
                }
                echo '</table><br><br></div>';
            }

            // Les demandes de prolongation ***************************************************************************
            if (myjob_getmoduleoption('prolongation')) {
                $cnt = $prolongation_handler->getCount(new Criteria('demandid', 0, '<>'));
                if ($cnt > 0) {
                    myjob_collapsableBar('demandsprolong', 'demandsprolongicon');
                    echo "<img onclick='toggle('toptable'); toggleIcon('toptableicon');' id='demandsprolongicon' name='demandsprolongicon' src=" . XOOPS_URL . "/modules/myjob/assets/images/close12.gif alt='' /></a>&nbsp;" . _AM_MYJOB_PROLONGATION_DEMAND . '</h4>';
                    echo "<div id='demandsprolong'>";
                    echo '<br>';
                    echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
                    echo '<tr><th>' . _MYJOB_DEMAND_ZONEGEOGRAPHIQUE . '</th><th>' . _MYJOB_DEMAND_SECTEURACTIVITE . '</th><th>' . _MYJOB_DEMAND_DESCRIPTION . '</th><th>' . _MYJOB_DEMAND_NOM . '</th><th>' . _MYJOB_DEMAND_PRENOM . "</th><th align='center'>" . _AM_MYJOB_PROLONGATION_EXPIRATION
                         . "</th><th align='center'>" . _AM_MYJOB_ACTION . '</th></tr>';
                    $class    = '';
                    $demandes = $demande_handler->getAllProlongationsDemands();
                    foreach ($demandes as $onedemande) {
                        $class     = ($class === 'even') ? 'odd' : 'even';
                        $edit      = "<a title='" . _AM_MYJOB_EDIT . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=demandedit&demandid=' . $onedemande->getVar('demandid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/edit.gif' alt='" . _AM_MYJOB_EDIT . "'></a>";
                        $dele      = "<a title='" . _AM_MYJOB_DELETE . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=demanddelete&demandid=' . $onedemande->getVar('demandid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/delete.gif' alt='" . _AM_MYJOB_DELETE . "'></a>";
                        $unval     =
                            "<a title='" . _AM_MYJOB_UNVALIDATE . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=demandunval&demandid=' . $onedemande->getVar('demandid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/unvalidate.gif' alt='" . _AM_MYJOB_UNVALIDATE . "'></a>";
                        $prolongok = "<a title='" . _AM_MYJOB_PROLONGATION_YES . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=demandprolongvalid&action=yes&demandid=' . $onedemande->getVar('demandid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/button_ok.png' alt='"
                                     . _AM_MYJOB_PROLONGATION_YES . "'></a>";
                        $prolongno = "<a title='" . _AM_MYJOB_PROLONGATION_NO . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=demandprolongvalid&action=no&demandid=' . $onedemande->getVar('demandid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/button_cancel.png' alt='"
                                     . _AM_MYJOB_PROLONGATION_NO . "'></a>";
                        $critere   = new Criteria('r.demandid', $onedemande->getVar('demandid'), '=');
                        $critere->setSort('libelle');
                        // Récupération des zones géographiques
                        $tblzones = $demandofferzones_handler->getLibsWithRelation($critere);
                        $libzones = implode('<br>', $tblzones);
                        // Récupération des secteurs
                        $tblsecteurs = $demandoffersecteurs_handler->getLibsWithRelation($critere);
                        $libsecteurs = implode('<br>', $tblsecteurs);

                        echo "<tr class='" . $class . "'><td><a target='_blank' href='../demande-view.php?demandid=" . $onedemande->getVar('demandid') . "'>" . $libzones . '</a></td><td>' . $libsecteurs . '</td><td>' . xoops_substr($onedemande->getVar('titreannonce'), 0, $longtit) . '</td><td>'
                             . xoops_substr($onedemande->getVar('nom'), 0, $longtit) . "</td><td align='center'>" . xoops_substr($onedemande->getVar('prenom'), 0, $longtit) . '</td><td>' . formatTimestamp($onedemande->getVar('dateexpiration')) . '</td><td>' . $edit . ' ' . $dele . ' ' . $unval . ' '
                             . $prolongok . ' ' . $prolongno . '</td></tr>';
                    }
                    echo '</table></div><br>';
                }
            }

            // Les (x) dernières demandes *****************************************************************************
            $start    = isset($_GET['start']) ? (int)$_GET['start'] : 0;
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('datevalidation', '0', '<>'));
            $criteria->add(new Criteria('dateexpiration', time(), '>'));

            $demandsvalidcount = $demande_handler->getCount($criteria);
            $pagenav           = new XoopsPageNav($demandsvalidcount, $xoopsModuleConfig['demandescount'], $start, 'start', 'op=viewdemands');
            $limit             = myjob_getmoduleoption('demandescount');

            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('datevalidation', '0', '<>'));
            $criteria->add(new Criteria('dateexpiration', time(), '>'));
            $criteria->setLimit($limit);
            $criteria->setStart($start);
            $criteria->setOrder('DESC');
            $criteria->setSort('datesoumission');

            $demandes = $demande_handler->getObjects($criteria);
            myjob_collapsableBar('demandsvalid', 'demandsvalidicon');
            echo "<img onclick='toggle('toptable'); toggleIcon('toptableicon');' id='demandsvalidicon' name='demandsvalidicon' src=" . XOOPS_URL . "/modules/myjob/assets/images/close12.gif alt='' /></a>&nbsp;" . sprintf(_AM_MYJOB_LAST_DEMANDS, $limit) . '</h4>';
            echo "<div id='demandsvalid'>";
            echo '<br>';
            echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
            echo '<tr><th>' . _MYJOB_DEMAND_ZONEGEOGRAPHIQUE . '</th><th>' . _MYJOB_DEMAND_SECTEURACTIVITE . '</th><th>' . _MYJOB_DEMAND_EXPERIENCE . '</th><th>' . _MYJOB_DEMAND_TYPEPOSTE . '</th><th>' . _MYJOB_DEMAND_DESCRIPTION . '</th><th>' . _MYJOB_DEMAND_NOM . '</th><th>' . _MYJOB_DEMAND_PRENOM
                 . "</th><th align='center'>" . _AM_MYJOB_ACTION . '</th></tr>';
            $class = '';
            foreach ($demandes as $onedemande) {
                $class   = ($class === 'even') ? 'odd' : 'even';
                $edit    = "<a title='" . _AM_MYJOB_EDIT . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=demandedit&demandid=' . $onedemande->getVar('demandid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/edit.gif' alt='" . _AM_MYJOB_EDIT . "'></a>";
                $dele    = "<a title='" . _AM_MYJOB_DELETE . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=demanddelete&demandid=' . $onedemande->getVar('demandid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/delete.gif' alt='" . _AM_MYJOB_DELETE . "'></a>";
                $unval   = "<a title='" . _AM_MYJOB_UNVALIDATE . "' href='" . XOOPS_URL . '/modules/myjob/admin/main.php?op=demandunval&demandid=' . $onedemande->getVar('demandid') . "'><img src='" . XOOPS_URL . "/modules/myjob/assets/images/unvalidate.gif' alt='" . _AM_MYJOB_UNVALIDATE . "'></a>";
                $critere = new Criteria('r.demandid', $onedemande->getVar('demandid'), '=');
                $critere->setSort('libelle');
                // Récupération des zones géographiques
                $tblzones = $demandofferzones_handler->getLibsWithRelation($critere);
                $libzones = implode('<br>', $tblzones);
                // Récupération des secteurs
                $tblsecteurs = $demandoffersecteurs_handler->getLibsWithRelation($critere);
                $libsecteurs = implode('<br>', $tblsecteurs);
                echo "<tr class='" . $class . "'><td><a target='_blank' href='../demande-view.php?demandid=" . $onedemande->getVar('demandid') . "'>" . $libzones . '</a></td><td>' . $libsecteurs . '</td><td>' . xoops_substr($onedemande->getVar('libelle_experience'), 0, $longtit) . '</td><td>'
                     . $types[$onedemande->getVar('typeposte')] . '</td><td>' . xoops_substr($onedemande->getVar('titreannonce'), 0, $longtit) . '</td><td>' . xoops_substr($onedemande->getVar('nom'), 0, $longtit) . "</td><td align='center'>" . xoops_substr($onedemande->getVar('prenom'), 0, $longtit)
                     . '</td><td>' . $edit . ' ' . $dele . ' ' . $unval . '</td></tr>';
            }
            echo "</table><div align='right'>" . $pagenav->renderNav() . '</div></div><br>';
        } else {
            echo '<h1>' . _AM_MYJOB_DEMANDS_NOUSE . '</h1>';
        }
        break;

    /**
     * Dévalidation d'une demande d'emploi
     */
    case 'demandunval':
        if ($usedemands) {
            if (isset($_GET['demandid'])) {
                $demande_handler->unvalidate((int)$_GET['demandid']);
                myjob_updateCache();
                redirect_header('main.php?op=viewdemands', 2, _AM_MYJOB_UNVALIDATE_OK);
            } else {
                redirect_header('main.php?op=viewdemands', 2, _ERRORS);
                exit();
            }
        }
        break;

    /**
     * Edition d'une demande d'emploi
     */
    case 'demandedit':
        xoops_cp_header();
        // myjob_adminmenu(2);
        echo '<br><br>';
        if ($usedemands) {
            $warning = myjob_getmoduleoption('autoapprovedemands');
            if (isset($_GET['demandid'])) {
                $demandid = (int)$_GET['demandid'];
            } else {
                redirect_header('main.php', 2, _ERRORS);
                exit();
            }
            $demande   = $demande_handler->get($demandid);
            $adminside = true;
            include_once XOOPS_ROOT_PATH . '/modules/myjob/include/demandform.php';
        } else {
            echo '<h1>' . _AM_MYJOB_DEMANDS_NOUSE . '</h1>';
        }
        break;

    /**
     * Suppression d'une demande d'emploi
     */
    case 'demanddelete':
        if ($usedemands) {
            if (!isset($_POST['ok'])) {
                xoops_cp_header();
                echo '<h4>' . _AM_MYJOB_DELETION . '</h4>';
                if (isset($_GET['demandid'])) {
                    $demandid = (int)$_GET['demandid'];
                } else {
                    redirect_header('main.php', 2, _ERRORS);
                    exit();
                }
                $demande = $demande_handler->get($demandid);
                xoops_confirm(array('op' => 'demanddelete', 'demandid' => $demandid, 'ok' => 1), 'main.php', _AM_MYJOB_WARNING_DELETE_DEMAND . '<br>' . $demande->getVar('titreannonce'));
            } else {
                if (isset($_POST['demandid'])) {
                    $demandid = (int)$_POST['demandid'];
                } else {
                    redirect_header('main.php', 2, _ERRORS);
                    exit();
                }
                $demande = $demande_handler->get($demandid);
                if (trim($demande->getVar('attachedfile')) != '') {
                    if (file_exists(XOOPS_UPLOAD_PATH . '/' . $demande->getVar('attachedfile'))) {
                        unlink(XOOPS_UPLOAD_PATH . '/' . $demande->getVar('attachedfile'));
                    }
                }
                $demande_handler->delete($demande);
                myjob_updateCache();
                redirect_header('main.php?op=viewdemands', 1, _AM_MYJOB_DBUPDATED);
                exit();
            }
        }
        break;

    /**
     * Gestion de l'expérience
     */
    case 'experience':
        xoops_cp_header();
        // myjob_adminmenu(11);
        echo '<br><br>';
        echo '<h1>' . _MI_MYJOB_ADMMENU11 . '</h1>';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_MYJOB_ID . "</th><th align='center'>" . _AM_MYJOB_DESCRIPTION . "</th><th align='center'>" . _AM_MYJOB_ACTION . '</th></tr>';
        $start   = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $pagenav = new XoopsPageNav($experience_handler->getCount(), $limit, $start, 'start', 'op=' . $op);
        $class   = ListElements($experience_handler, 'experienceid', 'libelle', 'experience', 11, 'experience', $start);
        $class   = ($class === 'even') ? 'odd' : 'even';
        echo "<tr class='" . $class . "'><td align='center' colspan='3'><form method='post' action='main.php'><input type='hidden' name='handlername' value='experience'><input type='hidden' name='op' value='additem'><input type='submit' name='go' value='" . _AM_MYJOB_ITEMADD . "'></form></td></tr>";
        echo '</table>';
        echo "<br><div align='right'>" . $pagenav->renderNav() . '</div>';
        break;

    /**
     * Gestion des secteurs géographiques
     */
    case 'geo':
        xoops_cp_header();
        // myjob_adminmenu(5);
        echo '<br><br>';
        echo '<h1>' . _MI_MYJOB_ADMMENU6 . '</h1>';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_MYJOB_ID . "</th><th align='center'>" . _AM_MYJOB_DESCRIPTION . "</th><th align='center'>" . _AM_MYJOB_ACTION . '</th></tr>';
        $start   = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $class   = ListElements($zonegeographique_handler, 'zoneid', 'libelle', 'zonegeographique', 6, 'geo', $start);
        $pagenav = new XoopsPageNav($zonegeographique_handler->getCount(), $limit, $start, 'start', 'op=' . $op);
        $class   = ($class === 'even') ? 'odd' : 'even';
        echo "<tr class='" . $class . "'><td align='center' colspan='3'><form method='post' action='main.php'><input type='hidden' name='handlername' value='zonegeographique'><input type='hidden' name='op' value='additem'><input type='submit' name='go' value='" . _AM_MYJOB_ITEMADD
             . "'></form></td></tr>";
        echo '</table>';
        echo "<br><div align='right'>" . $pagenav->renderNav() . '</div>';
        break;

    /**
     * Gestion des secteurs d'activité
     */
    case 'secteurs':
        xoops_cp_header();
        // myjob_adminmenu(6);
        echo '<br><br>';
        echo '<h1>' . _MI_MYJOB_ADMMENU7 . '</h1>';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_MYJOB_ID . "</th><th align='center'>" . _AM_MYJOB_DESCRIPTION . "</th><th align='center'>" . _AM_MYJOB_ACTION . '</th></tr>';
        $start   = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $class   = ListElements($secteuractivite_handler, 'secteurid', 'libelle', 'secteuractivite', 6, 'secteurs', $start);
        $pagenav = new XoopsPageNav($secteuractivite_handler->getCount(), $limit, $start, 'start', 'op=' . $op);
        $class   = ($class === 'even') ? 'odd' : 'even';
        echo "<tr class='" . $class . "'><td align='center' colspan='3'><form method='post' action='main.php'><input type='hidden' name='handlername' value='secteuractivite'><input type='hidden' name='op' value='additem'><input type='submit' name='go' value='" . _AM_MYJOB_ITEMADD
             . "'></form></td></tr>";
        echo '</table>';
        echo "<br><div align='right'>" . $pagenav->renderNav() . '</div>';
        break;

    /**
     * Gestion des situations de famille
     */
    case 'managesitfam':
        xoops_cp_header();
        // myjob_adminmenu(3);
        echo '<br><br>';
        echo '<h1>' . _MI_MYJOB_ADMMENU3 . '</h1>';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_MYJOB_ID . "</th><th align='center'>" . _AM_MYJOB_DESCRIPTION . "</th><th align='center'>" . _AM_MYJOB_ACTION . '</th></tr>';
        $start   = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $class   = ListElements($sitfam_handler, 'sitfamid', 'libelle', 'sitfam', 3, 'managesitfam', $start);
        $pagenav = new XoopsPageNav($sitfam_handler->getCount(), $limit, $start, 'start', 'op=' . $op);
        $class   = ($class === 'even') ? 'odd' : 'even';
        echo "<tr class='" . $class . "'><td align='center' colspan='3'><form method='post' action='main.php'><input type='hidden' name='handlername' value='sitfam'><input type='hidden' name='op' value='additem'><input type='submit' name='go' value='" . _AM_MYJOB_ITEMADD . "'></form></td></tr>";
        echo '</table>';
        echo "<br><div align='right'>" . $pagenav->renderNav() . '</div>';
        break;

    /**
     * Gestion des types de postes
     */
    case 'managetypeposte':
        xoops_cp_header();
        // myjob_adminmenu(4);
        echo '<br><br>';
        echo '<h1>' . _MI_MYJOB_ADMMENU4 . '</h1>';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_MYJOB_ID . "</th><th align='center'>" . _AM_MYJOB_DESCRIPTION . "</th><th align='center'>" . _AM_MYJOB_ACTION . '</th></tr>';
        $start   = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $class   = ListElements($typeposte_handler, 'typeid', 'libelle', 'typeposte', 4, 'managetypeposte', $start);
        $pagenav = new XoopsPageNav($typeposte_handler->getCount(), $limit, $start, 'start', 'op=' . $op);
        $class   = ($class === 'even') ? 'odd' : 'even';
        echo "<tr class='" . $class . "'><td align='center' colspan='3'><form method='post' action='main.php'><input type='hidden' name='handlername' value='typeposte'><input type='hidden' name='op' value='additem'><input type='submit' name='go' value='" . _AM_MYJOB_ITEMADD . "'></form></td></tr>";
        echo '</table>';
        echo "<br><div align='right'>" . $pagenav->renderNav() . '</div>';
        break;

    /**
     * Gestion des types de rémunération
     */
    case 'salarytype':
        xoops_cp_header();
        // myjob_adminmenu(7);
        echo '<br><br>';
        echo '<h1>' . _MI_MYJOB_ADMMENU8 . '</h1>';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_MYJOB_ID . "</th><th align='center'>" . _AM_MYJOB_DESCRIPTION . "</th><th align='center'>" . _AM_MYJOB_ACTION . '</th></tr>';
        $start   = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $class   = ListElements($salarytype_handler, 'salarytypeid', 'libelle', 'salarytype', 7, 'salarytype', $start);
        $pagenav = new XoopsPageNav($salarytype_handler->getCount(), $limit, $start, 'start', 'op=' . $op);
        $class   = ($class === 'even') ? 'odd' : 'even';
        echo "<tr class='" . $class . "'><td align='center' colspan='3'><form method='post' action='main.php'><input type='hidden' name='handlername' value='salarytype'><input type='hidden' name='op' value='additem'><input type='submit' name='go' value='" . _AM_MYJOB_ITEMADD . "'></form></td></tr>";
        echo '</table>';
        echo "<br><div align='right'>" . $pagenav->renderNav() . '</div>';
        break;

    /**
     * Affichage du formulaire de saisie pour ajouter un élément de type typepost, sitam, zonegeographique, secteuractivite, experience et salarytype
     */
    case 'additem':
        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        xoops_cp_header();
        $handlername = $_POST['handlername'];
        switch ($handlername) {
            case 'sitfam':
                // myjob_adminmenu(3);
                break;
            case 'typeposte':
                // myjob_adminmenu(4);
                break;
            case 'zonegeographique':
                // myjob_adminmenu(5);
                break;
            case 'secteuractivite':
                // myjob_adminmenu(6);
                break;
            case 'salarytype':
                // myjob_adminmenu(7);
                break;
            case 'experience':
                // myjob_adminmenu(11);
                break;
        }
        echo '<br><br>';
        $obj_handler = xoops_getModuleHandler($handlername, 'myjob');
        $sform       = new XoopsThemeForm(_AM_MYJOB_ITEMADD, 'additem', XOOPS_URL . '/modules/myjob/admin/main.php', 'post');
        $sform->addElement(new XoopsFormHidden('op', 'additemforsave'), false);
        $sform->addElement(new XoopsFormHidden('handlername', $handlername), false);
        $sform->addElement(new XoopsFormText(_AM_MYJOB_DESCRIPTION, 'libelle', 50, 255, ''), true);
        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn  = new XoopsFormButton('', 'post', _AM_MYJOB_ITEMADD_BTN, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform->display();
        break;

    /**
     * La sauvegarde d'un élément de type typepost ou sitam ou ...
     */
    case 'additemforsave':
        $handlername = $_POST['handlername'];
        switch ($handlername) {
            case 'sitfam':
                $op = 'managesitfam';
                break;
            case 'typeposte':
                $op = 'managetypeposte';
                break;
            case 'zonegeographique':
                $op = 'geo';
                break;
            case 'secteuractivite':
                $op = 'secteurs';
                break;
            case 'salarytype':
                $op = 'salarytype';
                break;
            case 'experience':
                $op = 'experience';
                break;
        }
        $obj_handler = xoops_getModuleHandler($handlername, 'myjob');
        $item        = $obj_handler->create(true);
        $item->setVar('libelle', $_POST['libelle']);
        $res = $obj_handler->insert($item);
        myjob_updateCache();
        if ($res) {
            redirect_header('main.php?op=' . $op, 2, _AM_MYJOB_DBUPDATED);
        } else {
            redirect_header('main.php?op=' . $op, 3, _AM_MYJOB_DB_NOTUPDATED);
        }
        break;

    /**
     * Enregistrement d'une élément de type typepost ou sitfam
     */
    case 'itemsave':
        $obj_handler = xoops_getModuleHandler($_POST['handlername'], 'myjob');
        $item        = $obj_handler->get((int)$_POST['itemid']);
        $item->unsetNew();
        $item->setVar($_POST['label'], $_POST['item_title']);

        if (isset($_POST['xoops_upload_file'])) {
            include_once XOOPS_ROOT_PATH . '/class/uploader.php';
            $fldname = $_FILES[$_POST['xoops_upload_file'][0]];
            $fldname = get_magic_quotes_gpc() ? stripslashes($fldname['name']) : $fldname['name'];
            if (xoops_trim($fldname != '')) {
                $dstpath        = XOOPS_UPLOAD_PATH;
                $destname       = myjob_createUploadName($dstpath, $fldname, true);
                $permittedtypes = explode("\n", str_replace("\r", '', myjob_getmoduleoption('picturemimetypes')));
                array_walk($permittedtypes, 'trim');

                $uploader = new XoopsMediaUploader($dstpath, $permittedtypes, myjob_getmoduleoption('maxuploadsize'));
                $uploader->setTargetFileName($destname);
                if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
                    if ($uploader->upload()) {
                        $item->setVar('image', basename($destname));
                    } else {
                        echo _AM_MYJOB_UPLOAD_ERROR . ' ' . $uploader->getErrors();
                    }
                } else {
                    echo $uploader->getErrors();
                }
            }
        }

        $res = $obj_handler->insert($item);
        myjob_updateCache();
        unset($obj_handler);
        unset($item);
        if ($res) {
            redirect_header('main.php?op=' . $_POST['redirect'], 2, _AM_MYJOB_DBUPDATED);
        } else {
            redirect_header('main.php', 3, _AM_MYJOB_DB_NOTUPDATED);
        }
        break;

    /**
     * Suppression d'un élément de type typepost ou sitfam
     */
    case 'itemdelete':
        $obj_handler = xoops_getModuleHandler($_GET['handlername'], 'myjob');
        $item        = $obj_handler->get((int)$_GET['itemid']);
        $res         = $obj_handler->delete($item, true);
        myjob_updateCache();
        if ($res) {
            redirect_header('main.php?op=' . $_GET['redirect'], 2, _AM_MYJOB_DBUPDATED);
        } else {
            redirect_header('main.php', 3, _AM_MYJOB_DB_NOTUPDATED);
        }
        break;

    /**
     * Edition d'un élément de type typepost ou sitfam
     */
    case 'itemedit':
        xoops_cp_header();
        // myjob_adminmenu((int)($_GET['tabnum']));
        echo '<br><br>';
        EditElement((int)$_GET['itemid'], $_GET['handlertype'], $_GET['fieldid'], $_GET['fieldlabel'], (int)$_GET['tabnum'], $_GET['redirect']);
        break;

    /**
     * Gestion des permissions
     */
    case 'perms':
        xoops_cp_header();
        include_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';
        // myjob_adminmenu(8);
        echo '<br><br>';
        $permform = new XoopsGroupPermForm(_AM_MYJOB_PERMS, $xoopsModule->getVar('mid'), 'demand_view', _AM_MYJOB_PERMS_DESCR);
        $permform->addItem('1', _AM_MYJOB_VIEW_DEMANDS_DETAILS);
        echo $permform->render();
        echo "<br><br><br><br>\n";
        unset($permform);
        break;

    /**
     * Sauvegarde des champs visibles et obligatoires
     */
    case 'fieldssave':
        xoops_cp_header();
        // myjob_adminmenu(10);
        echo '<br><br>';
        $fp = fopen($field_name, 'w') || exit(_AM_MYJOB_FIELDS_ERROR);
        if (isset($_POST['demandvisibles'])) {
            foreach ($_POST['demandvisibles'] as $key => $value) {
                fwrite($fp, trim($value) . "\r\n");
            }
        }
        fwrite($fp, '[end]' . "\r\n");

        if (isset($_POST['demandmandatory'])) {
            foreach ($_POST['demandmandatory'] as $key => $value) {
                fwrite($fp, trim($value) . "\r\n");
            }
        }
        fwrite($fp, '[end]' . "\r\n");

        if (isset($_POST['offervisibles'])) {
            foreach ($_POST['offervisibles'] as $key => $value) {
                fwrite($fp, trim($value) . "\r\n");
            }
        }
        fwrite($fp, '[end]' . "\r\n");

        if (isset($_POST['offermandatory'])) {
            foreach ($_POST['offermandatory'] as $key => $value) {
                fwrite($fp, trim($value) . "\r\n");
            }
        }
        fclose($fp);
        redirect_header('main.php?op=fields', 2, _AM_MYJOB_DBUPDATED);
        break;

    /**
     * Gestion des champs obligatoires et des champs visibles (offres et demandes)
     */
    case 'fields':
        xoops_cp_header();
        // myjob_adminmenu(10);
        echo '<br><br><br>';
        $demande_fields              = myjob_get_fields_from_table('myjob_demande');
        $demande_fields['secteurid'] = 'secteurid';
        $demande_fields['zoneid']    = 'zoneid';
        $offer_fields                = myjob_get_fields_from_table('myjob_offre');
        if (file_exists($field_name)) {
            $content = file_get_contents($field_name);
        }
        $fields    = array('', '', '', '');
        $fields    = explode('[end]', $content);
        $fields[0] = trim($fields[0]);    // Visibles
        $fields[1] = trim($fields[1]);    // Obligatoires
        $fields[2] = trim($fields[2]);    // Visibles
        $fields[3] = trim($fields[3]);    // Obligatoires

        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $sform = new XoopsThemeForm(_AM_MYJOB_FIELDS_MANAGER, 'fieldsform', XOOPS_URL . '/modules/myjob/admin/main.php');
        $sform->addElement(new XoopsFormHidden('op', 'fieldssave'), false);
        $sform->insertBreak('<center><b>' . _AM_MYJOB_DEMANDS . '</b></center>', 'even');

        $demandvisibles = new XoopsFormSelect(_AM_MYJOB_VISIBLES, 'demandvisibles', explode("\r\n", $fields[0]), 5, true);
        foreach ($demande_fields as $key => $value) {
            $demandvisibles->addOption(trim($key), trim($value));
        }
        $sform->addElement($demandvisibles, false);

        $demandmandatory = new XoopsFormSelect(_AM_MYJOB_MANDATORY, 'demandmandatory', explode("\r\n", $fields[1]), 5, true);
        foreach ($demande_fields as $key => $value) {
            $demandmandatory->addOption(trim($key), trim($value));
        }
        $sform->addElement($demandmandatory, false);

        $sform->insertBreak('<center><b>' . _AM_MYJOB_OFFERS . '</b></center>', 'even');
        $offervisibles = new XoopsFormSelect(_AM_MYJOB_VISIBLES, 'offervisibles', explode("\r\n", $fields[2]), 5, true);
        foreach ($offer_fields as $key => $value) {
            $offervisibles->addOption(trim($key), trim($value));
        }
        $sform->addElement($offervisibles, false);

        $offermandatory = new XoopsFormSelect(_AM_MYJOB_MANDATORY, 'offermandatory', explode("\r\n", $fields[3]), 5, true);
        foreach ($offer_fields as $key => $value) {
            $offermandatory->addOption(trim($key), trim($value));
        }
        $sform->addElement($offermandatory, false);
        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn  = new XoopsFormButton('', 'post', _AM_MYJOB_MODIFY, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform->display();

        $visibledemands   = XOOPS_ROOT_PATH . '/modules/myjob/visibledemands.txt';
        $mandatorydemands = XOOPS_ROOT_PATH . '/modules/myjob/mandatorydemands.txt';
        $visibleoffers    = XOOPS_ROOT_PATH . '/modules/myjob/visibleoffers.txt';
        $mandatorydemands = XOOPS_ROOT_PATH . '/modules/myjob/mandatorydemands.txt';
        break;

    /**
     * Action par défaut, résumé des offres et demandes
     */
    case 'default':
    default:
        xoops_cp_header();
        // myjob_adminmenu(0);
        echo '<br><br>';
        if ($useoffers) {
            $offersvalid   = $offre_handler->getCount(new Criteria('online', '1', '='));
            $offerswaiting = $offre_handler->getCount(new Criteria('online', '0', '='));
        }
        if ($usedemands) {
            $demandsvalid   = $demande_handler->getCount(new Criteria('datevalidation', '0', '<>'));
            $demandswaiting = $demande_handler->getCount(new Criteria('datevalidation', '0', '='));
        }
        echo '<h3>' . _AM_MYJOB_STATS . '</h3>';
        echo "<table border='1'>";
        if ($useoffers) {
            echo "<tr class='even'><td>" . sprintf(_MYJOB_OFFERS_COUNT, $offersvalid) . "</td></tr><tr class='odd'><td>" . sprintf(_MYJOB_OFFERS_WAITING, $offerswaiting) . '</td></tr>';
        }
        if ($usedemands) {
            echo "<tr class='even'><td>" . sprintf(_MYJOB_DEMANDS_COUNT, $demandsvalid) . "</td></tr><tr class='odd'><td>" . sprintf(_MYJOB_DEMANDS_WAITING, $demandswaiting) . '</tr></td>';
        }
        echo '</table>';
        break;
}
xoops_cp_footer();
