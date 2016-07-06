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

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

if (file_exists(XOOPS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/calendar.php')) {
    include_once XOOPS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/calendar.php';
} else {
    include_once XOOPS_ROOT_PATH . '/language/english/calendar.php';
}

if (!isset($isnew)) {
    $isnew = false;
}

if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    $admin = true;
} else {
    $admin = false;
}

include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
$sform = new XoopsThemeForm(_MYJOB_SUBMITDEMAND, 'demandform', XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/submit-demande.php');
$sform->setExtra('enctype="multipart/form-data"');

if (trim(myjob_getmoduleoption('textdemand')) != '') {
    $sform->insertBreak('<center><b>' . myjob_getmoduleoption('textdemand') . '</b></center>', 'even');
}

if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    if ($demande->getVar('demandid')) {
        $sform->addElement(new XoopsFormDateTime(_MYJOB_DEMAND_DATESOUMISSION, 'datesoumission', 15, $demande->getVar('datesoumission', 'e')), true);
        if ($demande->getVar('datevalidation', 's') == 0) {
            $ladate = ((int)((time() - $demande->getVar('datesoumission')) / 86400) > 0) ? $ldate = sprintf(_MYJOB_DEMAND_WAITINGSINCE, (int)((time() - $demande->getVar('datesoumission')) / 86400)) : _MYJOB_DEMAND_TODAY;
        } else {
            $ladate = sprintf(_MYJOB_DEMAND_VALIDATED, formatTimestamp($demande->getVar('datevalidation', 's')), (int)((time() - $demande->getVar('datesoumission')) / 86400));
        }
        $sform->addElement(new XoopsFormLabel(_MYJOB_STATUS, $ladate));
        $sform->addElement(new XoopsFormTextDateSelect(_MYJOB_DEMAND_EXPIRATION, 'dateexpiration', 15, $demande->getVar('dateexpiration', 'e')), true);
    }
    if (!$demande->getVar('datevalidation')) {
        $sform->addElement(new XoopsFormHidden('datevalidation', time()));
    }
}

$sform->addElement(new XoopsFormText(_MYJOB_DEMAND_NOM, 'nom', 50, 255, $demande->getVar('nom', 'e')), myjob_fields('nom', true, 'demande'));
$sform->addElement(new XoopsFormText(_MYJOB_DEMAND_PRENOM, 'prenom', 50, 255, $demande->getVar('prenom', 'e')), myjob_fields('prenom', true, 'demande'));

$emailzn = new XoopsFormText(_MYJOB_OFFER_EMAIL, 'email', 50, 200, $demande->getVar('email', 'e'));
$sform->addElement($emailzn, myjob_fields('email', true, 'demande'));
$sform->addElement(new XoopsFormText(_MYJOB_DEMAND_DATENAISS, 'datenaiss', 10, 10, $demande->getVar('datenaiss', 'e')), myjob_fields('datenaiss', true, 'demande'));
$sform->addElement(new XoopsFormTextArea(_MYJOB_DEMAND_ADRESSE, 'adresse', $demande->getVar('adresse', 'e'), 3, 60), myjob_fields('adresse', true, 'demande'));
$sform->addElement(new XoopsFormText(_MYJOB_DEMAND_CP, 'cp', 10, 10, $demande->getVar('cp', 'e')), myjob_fields('cp', true, 'demande'));
$sform->addElement(new XoopsFormText(_MYJOB_DEMAND_VILLE, 'ville', 50, 255, $demande->getVar('ville', 'e')), myjob_fields('ville', true, 'demande'));
$sform->addElement(new XoopsFormText(_MYJOB_DEMAND_TELEPHONE, 'telephone', 40, 40, $demande->getVar('telephone', 'e')), myjob_fields('telephone', true, 'demande'));

$sform->addElement(new XoopsFormText(_MYJOB_DEMAND_DESCRIPTION, 'titreannonce', 50, 255, $demande->getVar('titreannonce', 'e')), myjob_fields('titreannonce', true, 'demande'));
$sform->addElement(new XoopsFormText(_MYJOB_DEMAND_DIPLOME, 'diplome', 50, 255, $demande->getVar('diplome', 'e')), myjob_fields('diplome', true, 'demande'));
$sform->addElement(new XoopsFormTextArea(_MYJOB_DEMAND_FORMATION, 'formation', $demande->getVar('formation', 'e'), 15, 80), myjob_fields('formation', true, 'demande'));

$typeposteselect = new XoopsFormSelect(_MYJOB_DEMAND_TYPEPOSTE, 'typeposte', $demande->getVar('typeposte', 'e'));
foreach ($types as $valeur => $libelle) {
    $typeposteselect->addOption($valeur, $libelle);
}
$sform->addElement($typeposteselect, myjob_fields('typeposte', true, 'demande'));
// **************************************************************************************************************
$zonegeographiqueselect = new XoopsFormSelect(_MYJOB_DEMAND_ZONEGEOGRAPHIQUE, 'zoneid', '', 5, true);
foreach ($zones as $valeur => $libelle) {    // Les textes
    $zonegeographiqueselect->addOption($valeur, $libelle);
}
if (!isset($demandofferzones_handler)) {
    $demandofferzones_handler = xoops_getModuleHandler('demandofferzones', 'myjob');
}
$tblzones=array();	// Ce qui a été sélectionné
if ($demande->getVar('demandid') != 0) {
    $tblzones = $demandofferzones_handler->getArray(new Criteria('demandid', $demande->getVar('demandid'), '='));
}
$zonegeographiqueselect->setValue($tblzones);
$sform->addElement($zonegeographiqueselect, myjob_fields('zoneid', true, 'demande'));

$sform->addElement(new XoopsFormText(_MYJOB_DEMAND_ZONEGEOGRAPHIQUEA, 'zonegeographique', 50, 255, $demande->getVar('zonegeographique', 'e')), myjob_fields('zonegeographique', true, 'demande'));
// **************************************************************************************************************
$secteuractiviteselect = new XoopsFormSelect(_MYJOB_DEMAND_SECTEURACTIVITE, 'secteurid', '', 5, true);
foreach ($secteurs as $valeur => $libelle) {
    $secteuractiviteselect->addOption($valeur, $libelle);
}
if (!isset($demandoffersecteurs_handler)) {
    $demandoffersecteurs_handler = xoops_getModuleHandler('demandoffersecteurs', 'myjob');
}
$tblsecteurs=array();	// Ce qui a été sélectionné
if ($demande->getVar('demandid') != 0) {
    $tblsecteurs = $demandoffersecteurs_handler->getArray(new Criteria('demandid', $demande->getVar('demandid'), '='));
}
$secteuractiviteselect->setValue($tblsecteurs);
$sform->addElement($secteuractiviteselect, myjob_fields('secteurid', true, 'demande'));

$sform->addElement(new XoopsFormText(_MYJOB_DEMAND_SECTEURACTIVITEA, 'secteuractivite', 50, 255, $demande->getVar('secteuractivite', 'e')), myjob_fields('secteuractivite', true, 'demande'));

// Exp�rience ************************************
if (!isset($experience_handler)) {
    $experience_handler = xoops_getModuleHandler('experience', 'myjob');
}
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('1', '1', '='));
$criteria->setSort('experienceid');
$tblexperience    = $experience_handler->getObjects($criteria);
$experienceselect = new XoopsFormSelect(_MYJOB_DEMAND_EXPERIENCE, 'experience', $demande->getVar('experience', 'e'));
foreach ($tblexperience as $oneexperience) {
    $experienceselect->addOption($oneexperience->getVar('experienceid'), $oneexperience->getVar('libelle'));
}
$sform->addElement($experienceselect, myjob_fields('', true, 'experience'));
// **********************************************************************************************

$sform->addElement(new XoopsFormTextArea(_MYJOB_OFFER_EXPERIENCEDETAIL, 'experiencedetail', $demande->getVar('experiencedetail', 'e'), 15, 80), myjob_fields('experiencedetail', true, 'demande'));
$sform->addElement(new XoopsFormTextDateSelect(_MYJOB_DEMAND_DATEDISPO, 'datedispo', 15, $demande->getVar('datedispo', 'e')), myjob_fields('datedispo', true, 'demande'));
$sform->addElement(new XoopsFormTextArea(_MYJOB_DEMAND_LANGUES, 'langues', $demande->getVar('langues', 'e'), 5, 60), myjob_fields('langues', true, 'demande'));
$sform->addElement(new XoopsFormTextArea(_MYJOB_DEMAND_ZONELIBRE, 'zonelibre', $demande->getVar('zonelibre', 'e'), 15, 80), myjob_fields('zonelibre', true, 'demande'));

$sitfamselect = new XoopsFormSelect(_MYJOB_DEMAND_SITFAM, 'sitfam', $demande->getVar('sitfam', 'e'));
foreach ($tblsitfam as $valeur => $libelle) {
    $sitfamselect->addOption($valeur, $libelle);
}
$sform->addElement($sitfamselect, myjob_fields('sitfam', true, 'demande'));

if ($admin) {
    $sform->addElement(new XoopsFormText(_MYJOB_DEMAND_PARAIN, 'parain', 50, 255, $demande->getVar('parain', 'e')), myjob_fields('parrain', true, 'demande'));
    $sform->addElement(new XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
}

$sform->addElement(new XoopsFormTextArea(_MYJOB_DEMAND_COMPETENCES, 'competences', $demande->getVar('competences', 'e'), 15, 80), myjob_fields('competences', true, 'demande'));
$sform->addElement(new XoopsFormTextArea(_MYJOB_DEMAND_DIVERS, 'divers', $demande->getVar('divers', 'e'), 15, 80), myjob_fields('divers', true, 'demande'));

$sform->addElement(new XoopsFormHidden('demandid', $demande->getVar('demandid')));
if (!isset($adminside)) {
    $adminside = false;
}
$sform->addElement(new XoopsFormHidden('adminside', $adminside));

if ($demande->getVar('dateexpiration') != 0) {
    $sform->addElement(new XoopsFormLabel(_MYJOB_DEMAND_EXPIRATION, formatTimestamp($demande->getVar('dateexpiration', 's'))), true);
}

if (myjob_getmoduleoption('uploaddemands')) {
    if (trim($demande->getVar('attachedfile')) != '') {
        $uploadedfile = $demande->getVar('attachedfile');
        $upl_tray     = new XoopsFormElementTray(_AM_MYJOB_UPLOAD_ATTACHFILE, '');
        $upl_tray->setDescription(_AM_MYJOB_DELETE_FILE);
        $upl_checkbox = new XoopsFormCheckBox('', 'delupload');
        $link         = sprintf("<a href='%s/%s' target='_blank'>%s</a>\n", XOOPS_UPLOAD_URL, $uploadedfile, $uploadedfile);
        $upl_checkbox->addOption($uploadedfile, $link);
        $upl_tray->addElement($upl_checkbox, false);
        $sform->addElement($upl_tray);
    }
    $sform->addElement(new XoopsFormFile(_MYJOB_ATTACHED_FILE, 'attachedfile', myjob_getmoduleoption('maxuploadsize')), myjob_fields('attachedfile', true, 'demande'));
}

if (!$warning) {
    $sform->insertBreak('<center><b>' . _MYJOB_SUBMITDEMAND_WARNING . '</b></center>', 'even');
}
$button_tray = new XoopsFormElementTray('', '');
$submit_btn  = new XoopsFormButton('', 'post', _MYJOB_POST, 'submit');
$button_tray->addElement($submit_btn);
$sform->addElement($button_tray);
if (trim(myjob_getmoduleoption('legaltext')) != '') {
    $sform->insertBreak('<center><b>' . myjob_getmoduleoption('legaltext') . '</b></center>', 'even');
}
$sform->display();
$myts = MyTextSanitizer::getInstance();
// Create page's title
if (isset($xoopsTpl) && is_object($xoopsTpl)) {
    $xoopsTpl->assign('xoops_pagetitle', _MYJOB_DEMAND_FORM . ' - ' . $myts->htmlSpecialChars($xoopsModule->name()));
}
