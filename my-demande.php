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
 * Gestion d'une demande d'emploi par l'utilisateur
 */
include __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'myjob_mydemand.tpl';
include_once XOOPS_ROOT_PATH . '/header.php';
include_once XOOPS_ROOT_PATH . '/modules/myjob/include/functions.php';
$autoconnect = false;

$usedemands = myjob_getmoduleoption('usedemands');
if (!$usedemands) {
    // Changer ces appels en header()
    redirect_header('index.php', 2, '');
    exit();
}
$demande_handler = xoops_getModuleHandler('demande', 'myjob');

if (isset($_GET['demandid'])) {
    $demandeid = (int)$_GET['demandid'];
} else {
    if (isset($_POST['demandid'])) {
        $demandeid = (int)$_POST['demandid'];
    } else {
		// On va vérifier et rechercher les demandes d'emploi de l'utilisateur courant
        if (!is_object($xoopsUser)) {
            redirect_header('index.php', 2, _MYJOB_ERROR3);
            exit();
        } else {
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('datevalidation', '0', '<>'));
            $criteria->add(new Criteria('dateexpiration', time(), '>'));
            $criteria->add(new Criteria('uid', $xoopsUser->getVar('uid'), '='));
            if ($demande_handler->getCount($criteria) > 0) {
                $criteria->setSort('datevalidation');
                $criteria->setLimit(1);
                $criteria->setStart(0);
                $tbldemandes = $demande_handler->getObjects($criteria);
                $lademande   = $tbldemandes[0];
                $demandeid   = $lademande->getVar('demandid');
                $autoconnect = true;
            }
        }
    }
}
$demande = $demande_handler->get($demandeid);

// Est-ce que la demande existe ?
if (!$demande) {
    redirect_header('index.php', 2, _MYJOB_DONT_EXIST);
    exit();
}

// Est-ce que la demande est en ligne (validée) ?
if ($demande->getVar('datevalidation') == 0) {
    redirect_header('index.php', 2, _MYJOB_CANT_MODIFY);
    exit();
}

$prolongation      = myjob_getmoduleoption('prolongation');
$typeposte_handler = xoops_getModuleHandler('typeposte', 'myjob');
$typespostes       = $typeposte_handler->getObjects();
$types             = array();
foreach ($typespostes as $onetypeposte) {
    $types[$onetypeposte->getVar('typeid')] = $onetypeposte->getVar('libelle');
}
$xoopsTpl->assign('typesoffres', $types);

$sitfam_handler = xoops_getModuleHandler('sitfam', 'myjob');
$sitesfams      = $sitfam_handler->getObjects();
$tblsitfam      = array();
foreach ($sitesfams as $onesitfam) {
    $tblsitfam[$onesitfam->getVar('sitfamid')] = $onesitfam->getVar('libelle');
}
$xoopsTpl->assign('sitfam', $tblsitfam);

if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    $xoopsTpl->assign('isadmin', true);
} else {
    $xoopsTpl->assign('isadmin', false);
}

$op = 'login';
if (isset($_GET['op'])) {
    $op = $_GET['op'];
} else {
    if (isset($_POST['op'])) {
        $op = $_POST['op'];
    } else {
        if ($autoconnect) {
            if (!isset($_SESSION['myjob_demandpassword'])) {
                $_SESSION['myjob_demandpassword'] = $demande->getVar('pass');
                $_SESSION['myjob_demandid']       = $demandeid;
            }
            $op = 'view';
        }
    }
}

$prolongation_handler = xoops_getModuleHandler('prolongation', 'myjob');
$cnt                  = 0;
$cnt                  = $prolongation_handler->getCount(new Criteria('demandid', $demandeid, '='));
if($prolongation && $cnt>0) {	// Les prolongations sont acceptées et la personne a déjà demandé une prolongation, on désactive donc le lien permettant de demander une prolongation
    $prolongation = false;
}

switch ($op) {
    /**
 	* Vérification du mot de passe
     */
    case 'verifypass':
        if (isset($_POST['password']) && $demande->getVar('pass') == md5($_POST['password'])) {
            $op                               = 'view';
            $_SESSION['myjob_demandpassword'] = $demande->getVar('pass');
            $_SESSION['myjob_demandid']       = $demandeid;
        } else {
            $op = 'login';
            $xoopsTpl->assign('errormsg', _MYJOB_BAD_PASSWORD);
        }
        break;

    /**
     * Edition d'une demande
     */
    case 'demandedit':
        if (isset($_SESSION['myjob_demandpassword'])) {
            if ($demande->getVar('pass') != $_SESSION['myjob_demandpassword'] || $_SESSION['myjob_demandid'] != $demandeid) {
                redirect_header('index.php', 2, _ERRORS);
                exit();
            } else {
                include_once XOOPS_ROOT_PATH . '/modules/myjob/include/demand_form.php';
            }
        } else {
            redirect_header('index.php', 2, _ERRORS);
            exit();
        }

    /**
     * Suppression d'une demande
     */
    case 'demanddelete':
		// On commence par vérifier qu'on est connecté et qu'on est propriétaire de l'annonce pour laquelle on fait une demande de prolongation
        if (isset($_SESSION['myjob_demandpassword'])) {
            if ($demande->getVar('pass') != $_SESSION['myjob_demandpassword'] || $_SESSION['myjob_demandid'] != $demandeid) {    // Mauvais mot de passe
                redirect_header('index.php', 2, _ERRORS);
                exit();
            } else {    // Suppression effective
				// en fait la demande va se faire 'périmer' par le script
                $demande->setVar('dateexpiration', time() - 86400);    // On la périme de la veille
                $prolongation_handler->insert($demande);
                myjob_updateCache();                            // Mise à jour du cache
                if (myjob_getmoduleoption('prolongation')) {        // Suppression de la demande de prolongation (s'il y en a une)
                    $prolongationo = '';
                    $prolongationo = $prolongation_handler->getbydemandid($demandeid);
                    if (is_object($prolongationo)) {
                        $prolongation_handler->delete($prolongationo, true);
                    }
                }
                if (isset($_SESSION['myjob_demands_count'])) {
                    $_SESSION['myjob_demands_count'] = (int)$_SESSION['myjob_demands_count'] - 1;
                }
                redirect_header('index.php', 2, _MYJOB_DEMAND_DELETED);
                exit();

                /*
                                if (trim($demande->getVar('attachedfile'))!='') {   // S'il y a un fichier on le supprime
                                    if (file_exists(XOOPS_UPLOAD_PATH.'/'.$demande->getVar('attachedfile'))) {
                                        unlink(XOOPS_UPLOAD_PATH.'/'.$demande->getVar('attachedfile'));
                                    }
                                }
                                $demande_handler->delete($demande,true);        // Suppression de la demande
				                myjob_updateCache();							// Mise à jour du cache
                                if (myjob_getmoduleoption('prolongation')) {        // Suppression de la demande de prolongation (s'il y en a une)
                                    $prolongationo = '';
                                    $prolongationo = $prolongation_handler->getbydemandid($demandeid);
                                    if (is_object($prolongationo)) {
                                        $prolongation_handler->delete($prolongationo,true);
                                    }
                                }
                                redirect_header('index.php',2,_MYJOB_DEMAND_DELETED);
                                exit();
                */
            }
		} else {	// Pas loggé
            redirect_header('index.php', 2, _ERRORS);
            exit();
        }
        break;

    /**
     * Demande de prolongation
     */
    case 'demandprolongate':
        if ($prolongation) {
			// On commence par vérifier qu'on est connecté et qu'on est propriétaire de l'annonce pour laquelle on fait une demande de prolongation
            if (isset($_SESSION['myjob_demandpassword'])) {
                if ($demande->getVar('pass') != $_SESSION['myjob_demandpassword'] || $_SESSION['myjob_demandid'] != $demandeid) {
                    redirect_header('index.php', 2, _ERRORS);
                    exit();
                } else {
					// C'est bien l'auteur de la demande d'emploi, on vérifie maintenant qu'il n'a pas déjà fait une demande de prolongation
					if($cnt) {	// La personne a déjà fait une demande de prolongation, on lui indique.
                        redirect_header('my-demande.php?demandid=' . $demandeid, 2, _MYJOB_PROLONGATION_ALREADY_ASKED);
                    } else {    // Il n'y a pas eu de demande de prolongation, on l'enregistre
                        $prolongationo = $prolongation_handler->create(true);
                        $prolongationo->setVar('demandid', $demandeid);
                        $prolongationo->setVar('offreid', 0);
                        $prolongationo->setVar('date', time());
                        $res = $prolongation_handler->insert($prolongationo, true);
						if($res!=false) {		// La sauvegarde s'est bien déroulée
                            redirect_header('my-demande.php?op=view&demandid=' . $demandeid, 2, _MYJOB_PROLONGATION_OK);
						} else {	// Problème pendant la sauvegarde
                            redirect_header('my-demande.php?op=view&demandid=' . $demandeid, 3, _MYJOB_PROLONGATION_PB);
                        }
                    }
                }
            } else {
                redirect_header('index.php', 2, _ERRORS);
                exit();
            }
		} else {	// Le module n'a pas été paramétré pour gèrer les demandes de prolongation
            redirect_header('index.php', 2, _ERRORS);
            exit();
        }
        break;
}

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

// Lecture des situations de famille
$sitfam_handler = xoops_getModuleHandler('sitfam', 'myjob');
$sitesfams      = $sitfam_handler->getObjects();
$tblsitfam      = array();
foreach ($sitesfams as $onesitfam) {
    $tblsitfam[$onesitfam->getVar('sitfamid')] = $onesitfam->getVar('libelle');
}

$array = $demande->toArray();
// Relation offre/demande <-> zones géographiques
$demandofferzones_handler = xoops_getModuleHandler('demandofferzones', 'myjob');

// Relation offre/demande <-> secteurs d'activité
$demandoffersecteurs_handler = xoops_getModuleHandler('demandoffersecteurs', 'myjob');
$critere                     = new Criteria('r.demandid', $demande->getVar('demandid'), '=');
$critere->setSort('libelle');
// Récupération des zones géographiques
$tblzones = $demandofferzones_handler->getLibsWithRelation($critere);
$libzones = implode('<br>', $tblzones);
// Récupération des secteurs
$tblsecteurs = $demandoffersecteurs_handler->getLibsWithRelation($critere);
$libsecteurs = implode('<br>', $tblsecteurs);

$array['secteurid_libelle']          = $libsecteurs;
$array['zonesgeographiques_libelle'] = $libzones;

$xoopsTpl->assign('onedemande', $array);
$xoopsTpl->assign('demandid', $demandeid);
$xoopsTpl->assign('prolongation', $prolongation);
$xoopsTpl->assign('op', $op);
$xoopsTpl->assign('conf_del_link', myjob_JavascriptLinkConfirm(_MYJOB_DEMAND_CONF_DELETE));
$myts = MyTextSanitizer::getInstance();
// Create page's title
$xoopsTpl->assign('xoops_pagetitle', _MYJOB_MYDEMAND . ' - ' . $myts->htmlSpecialChars($xoopsModule->name()));

include_once(XOOPS_ROOT_PATH . '/footer.php');
