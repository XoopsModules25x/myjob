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
 * Soumission d'une demande d'emploi
 */
include __DIR__ . '/header.php';
include_once XOOPS_ROOT_PATH . '/header.php';
include_once XOOPS_ROOT_PATH . '/modules/myjob/include/functions.php';
include_once XOOPS_ROOT_PATH . '/class/uploader.php';
include_once XOOPS_ROOT_PATH . '/modules/myjob/class/password.php';

$usedemands = myjob_getmoduleoption('usedemands');
if (!$usedemands) {
    redirect_header('index.php', 2, '');
    exit();
}

/**
 * Permet de savoir si la demande en cours de traitement est celle de l'utilisateur ou pas
 */
function is_it_mydemand()
{
    global $admin, $demande;
    if (!$admin) {
        if (isset($_SESSION['myjob_demandpassword'])) {
            if ($demande->getVar('pass') != $_SESSION['myjob_demandpassword'] || $_SESSION['myjob_demandid'] != $demande->getVar('demandid')) {
                return false;
            }
        } else {    // Ejecté direct

            return false;
        }
    }

    return true;
}

$op = 'form';

if (isset($_POST['preview'])) {
    $op = 'preview';
} elseif (isset($_POST['post'])) {
    $op = 'post';
} elseif (isset($_GET['op']) && $_GET['op'] === 'edit') {
    $op = 'edit';
}

if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    $xoopsTpl->assign('isadmin', true);
    $admin = true;
} else {
    $xoopsTpl->assign('isadmin', false);
    $admin = false;
}

if (isset($_GET['demandid'])) {
    $demandid = (int)$_GET['demandid'];
} else {
    $demandid = 0;
}

$warning         = myjob_getmoduleoption('autoapprovedemands');
$defaultduration = myjob_getmoduleoption('defaultduration');

// Chargement de la liste des types de postes
$critere = new Criteria('libelle', '###', '<>');
$critere->setSort('libelle');

$typeposte_handler = xoops_getModuleHandler('typeposte', 'myjob');
$typespostes       = $typeposte_handler->getObjects($critere);
$types             = array();
foreach ($typespostes as $onetypeposte) {
    $types[$onetypeposte->getVar('typeid')] = $onetypeposte->getVar('libelle');
}

// Chargement de la liste des situations famillialles
$sitfam_handler = xoops_getModuleHandler('sitfam', 'myjob');
$sitesfams      = $sitfam_handler->getObjects($critere);
$tblsitfam      = array();
foreach ($sitesfams as $onesitfam) {
    $tblsitfam[$onesitfam->getVar('sitfamid')] = $onesitfam->getVar('libelle');
}

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
// Relation offre/demande <-> zones géographiques
$demandofferzones_handler = xoops_getModuleHandler('demandofferzones', 'myjob');

// Relation offre/demande <-> secteurs d'activité
$demandoffersecteurs_handler = xoops_getModuleHandler('demandoffersecteurs', 'myjob');

$demande_handler = xoops_getModuleHandler('demande', 'myjob');

switch ($op) {
    case 'post':
        $pass = Text_Password::create(12, 'pronounceable', '0123456789');
        if (empty($demandid)) {
            $demandid = isset($_POST['demandid']) ? (int)$_POST['demandid'] : 0;
        }
        if (!empty($demandid)) {    // Mode Edition
            $isnew   = false;
            $demande = $demande_handler->get($demandid);
            if (isset($_POST['delupload'])) {
                unlink(XOOPS_UPLOAD_PATH . '/' . $demande->getVar('attachedfile'));
            }
            $demande->setVars($_POST);
            $demande->unsetNew();
        } else {    // Mode création
            $isnew   = true;
            $demande = $demande_handler->create(true);
            $demande->setVars($_POST);
            $demande->setVar('datesoumission', time());
            if (myjob_getmoduleoption('autoapprovedemands')) {
                $demande->setVar('datevalidation', time());
            } else {
                $demande->setVar('datevalidation', 0);
            }
            $demande->setVar('dateexpiration', time() + $defaultduration * 86400);
            // Création de l'alerte sur la création d'une demande d'emploi
            $tags                 = array();
            $tags['URL_ADMIN']    = XOOPS_URL . '/modules/myjob/admin/index.php?op=viewdemands';
            $notification_handler = xoops_getHandler('notification');
            $notification_handler->triggerEvent('global', 0, 'demand_submitted', $tags);
        }

        // Cas du mot de passe.
        if (myjob_getmoduleoption('autoapprovedemands')) {    // en auto-approbation le mot de passe est renseigné tout de suite du fait que le mail va être envoyé dans la foulée
            if (trim($demande->getVar('pass')) == '') {
                $demande->setVar('pass', md5($pass));
            }
        } else {    // Lorsqu'on n'est pas en approbation
            if (!$isnew && trim($demande->getVar('pass')) == '') {    // Si on est en édition et que le mot de passe n'est pas déjà renseigné
                $demande->setVar('pass', md5($pass));
            }
        }

        // Id de l'utilisateur
        if ($isnew) {
            if (is_object($xoopsUser)) {
                $demande->setVar('uid', $xoopsUser->getVar('uid'));
            } else {
                $demande->setVar('uid', 0);
            }
        }

        // Mise à jour des dates au bon format
        if (isset($_POST['dateexpiration']) && $admin) {
            $demande->setVar('dateexpiration', strtotime($_POST['dateexpiration']));
        }

        if (isset($_POST['datesoumission'])) {
            $submited = $_POST['datesoumission'];
            $demande->setVar('datesoumission', strtotime($submited['date']) + $submited['time']);
        }
        if (isset($_POST['datedispo'])) {
            $demande->setVar('datedispo', strtotime($_POST['datedispo']));
        }
        if (isset($_POST['dateexpiration'])) {
            $demande->setVar('dateexpiration', strtotime($_POST['dateexpiration']));
        }

        $destname = '';
        if (myjob_getmoduleoption('uploaddemands')) { // Manage upload(s)
            if (isset($_POST['xoops_upload_file'])) {
                $fldname = $_FILES[$_POST['xoops_upload_file'][0]];
                $fldname = get_magic_quotes_gpc() ? stripslashes($fldname['name']) : $fldname['name'];
                if (xoops_trim($fldname != '')) {
                    $destname       = myjob_createUploadName(XOOPS_UPLOAD_PATH, $fldname);
                    $permittedtypes = array(
                        'image/gif',
                        'image/jpeg',
                        'image/pjpeg',
                        'image/x-png',
                        'image/png',
                        'application/x-zip-compressed',
                        'application/zip',
                        'application/pdf',
                        'application/x-gtar',
                        'application/x-tar',
                        'application/msword',
                        'application/vnd.ms-excel',
                        'application/vnd.oasis.opendocument.text',
                        'application/vnd.oasis.opendocument.spreadsheet',
                        'application/vnd.oasis.opendocument.presentation',
                        'application/vnd.oasis.opendocument.graphics',
                        'application/vnd.oasis.opendocument.chart',
                        'application/vnd.oasis.opendocument.formula',
                        'application/vnd.oasis.opendocument.database',
                        'application/vnd.oasis.opendocument.image',
                        'application/vnd.oasis.opendocument.text-master'
                    );
                    $uploader       = new XoopsMediaUploader(XOOPS_UPLOAD_PATH, $permittedtypes, myjob_getmoduleoption('maxuploadsize'));
                    $uploader->setTargetFileName($destname);
                    if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
                        if ($uploader->upload()) {
                            if (trim($demande->getVar('attachedfile') != '') && file_exists(XOOPS_UPLOAD_PATH . '/' . $demande->getVar('attachedfile'))) {
                                unlink(XOOPS_UPLOAD_PATH . '/' . $demande->getVar('attachedfile'));
                            }
                            $demande->setVar('attachedfile', $destname);
                        } else {
                            echo _MYJOB_UPLOAD_ERROR . ' ' . $uploader->getErrors();
                        }
                    } else {
                        echo $uploader->getErrors();
                    }
                }
            }
        }
        $demande->setVar('ip', myjob_IP());
        $res = $demande_handler->insert($demande);
        if ($res != false) {        // La sauvegarde s'est bien déroulée
            // On sauvegarde les zones géographiques
            if (!$isnew) {    // En commencant par supprimer les zones actuelles
                $criteredel = new Criteria('demandid', $res, '=');
                $demandofferzones_handler->deleteAll($criteredel);
            }
            // ensuite on les sauvegarde
            if (isset($_POST['zoneid']) && is_array($_POST['zoneid'])) {
                foreach ($_POST['zoneid'] as $zoneidpost) {
                    $zonegeo = $demandofferzones_handler->create(true);
                    $zonegeo->setVar('demandid', $res);
                    $zonegeo->setVar('offreid', 0);
                    $zonegeo->setVar('zoneid', $zoneidpost);
                    $demandofferzones_handler->insert($zonegeo);
                    unset($zonegeo);
                }
            }

            // Sauvegarde des secteurs d'activité
            if (!$isnew) {    // On commence par supprimer les secteurs actuels
                $criteredel = new Criteria('demandid', $res, '=');
                $demandoffersecteurs_handler->deleteAll($criteredel);
            }

            // Ensuite on les sauvegarde
            if (isset($_POST['secteurid']) && is_array($_POST['secteurid'])) {
                foreach ($_POST['secteurid'] as $zonesecteurpost) {
                    $secteur = $demandoffersecteurs_handler->create(true);
                    $secteur->setVar('demandid', $res);
                    $secteur->setVar('offreid', 0);
                    $secteur->setVar('secteurid', $zonesecteurpost);
                    $demandoffersecteurs_handler->insert($secteur);
                    unset($secteur);
                }
            }

            // Si on est en validation de la demande ou si les demandes sont automatiquement approuvées
            // Attention, les personnes qui n'ont pas renseigné leur adresse email ne seront pas en mesure de gérer leurs demandes d'emploi.
            $sendmail = false;
            if (myjob_getmoduleoption('autoapprovedemands')) {
                $sendmail = true;
            } else {    // On est en approbation, le mail ne doit être envoyé que si on vient de valider la demande
                if (!$isnew && trim($demande->getVar('datevalidation')) != 0) {
                    $sendmail = true;
                }
            }

            if (isset($_SESSION['myjob_demandid']) && $_SESSION['myjob_demandid'] == $demande->getVar('demandid')) {
                $sendmail = false;
            }

            if ($sendmail && xoops_trim($demande->getVar('email')) != '') {    // Création du mail de validation
                if ($demande->getVar('uid') > 0) {        // Le mail est envoyé à une personne qui a un compte Xoops sur le site
                    $template = 'demand_published_notify_user.tpl';
                } else {    // Utilisateur inconnu ou qui n'est pas connecté
                    $template = 'demand_published_notify_author.tpl';
                }
                $template                = 'demand_published_notify_author.tpl';
                $variables               = array();
                $variables['URL_DEMAND'] = XOOPS_URL . '/modules/myjob/demande-view.php?demandid=' . $res;
                $variables['PASSWORD']   = $pass;
                $variables['DEMANDID']   = $demande->getVar('demandid');
                $variables['MANAGE_URL'] = XOOPS_URL . '/modules/myjob/my-demande.php?demandid=' . $res;
                $res_email               = myjob_send_email_from_tpl($template, $demande->getVar('email'), _MYJOB_DEMAND_EMAIL_SUBJECT, $variables, $xoopsConfig['adminmail'], $xoopsConfig['sitename']);
            }

            if (myjob_getmoduleoption('autoapprovedemands')) {    // Dans le cas de l'auto-approbation
                if (isset($_POST['adminside']) && (int)$_POST['adminside'] == 1) {
                    redirect_header(XOOPS_URL . '/modules/myjob/admin/index.php?op=viewdemands', 2, _MYJOB_DEMAND_SAVE_OK2);
                } else {
                    if (isset($_SESSION['myjob_demandpassword']) && $demande->getVar('pass') == $_SESSION['myjob_demandpassword'] && $_SESSION['myjob_demandid'] == $demandeid) {
                        redirect_header(XOOPS_URL . '/modules/myjob/my-demande.php?op=view&demandid=' . $demande->getVar('demandid'), 2, _MYJOB_DEMAND_SAVE_OK2);
                    } else {
                        redirect_header(XOOPS_URL . '/modules/myjob/index.php', 2, _MYJOB_DEMAND_SAVE_OK2);
                    }
                }
            } else {
                if (isset($_POST['adminside']) && (int)$_POST['adminside'] == 1) {
                    redirect_header(XOOPS_URL . '/modules/myjob/admin/index.php?op=viewdemands', 2, _MYJOB_DEMAND_SAVE_OK2);
                } else {
                    if (isset($_SESSION['myjob_demandpassword']) && $demande->getVar('pass') == $_SESSION['myjob_demandpassword'] && $_SESSION['myjob_demandid'] == $demandeid) {
                        redirect_header(XOOPS_URL . '/modules/myjob/my-demande.php?op=view&demandid=' . $demande->getVar('demandid'), 2, _MYJOB_DEMAND_SAVE_OK1);
                    } else {
                        redirect_header(XOOPS_URL . '/modules/myjob/index.php', 2, _MYJOB_DEMAND_SAVE_OK1);
                    }
                }
            }
        } else {    // Erreur pendant la sauvegarde
            if (trim($destname) != '') {    // Suppression du fichier uploadï¿½s'il existe
                if (file_exists(XOOPS_UPLOAD_PATH . '/' . $destname)) {
                    unlink(XOOPS_UPLOAD_PATH . '/' . $destname);
                }
            }
            if (isset($_POST['adminside']) && (int)$_POST['adminside'] == 1) {
                redirect_header(XOOPS_URL . '/modules/myjob/admin/index.php', 5, _MYJOB_ERROR2);
            } else {
                redirect_header(XOOPS_URL . '/modules/myjob/index.php', 5, _MYJOB_ERROR2);
            }
        }
        exit();
        break;

    case 'edit':
    case 'form':
        if (!empty($demandid)) {
            $demande = $demande_handler->get($demandid);
            $isnew   = false;
            // Vérification des droits
            if (!is_it_mydemand()) {
                redirect_header('index.php', 2, _MYJOB_DEMAND_NOPERM_TO_EDIT);
                exit();
            }
        } else {
            $demande = $demande_handler->create(true);
            $isnew   = true;
        }
        break;
}

include_once XOOPS_ROOT_PATH . '/modules/myjob/include/demand_form.php';
include_once XOOPS_ROOT_PATH . '/footer.php';
