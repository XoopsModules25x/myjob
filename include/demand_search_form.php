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
 * Recherche avancée dans les demandes
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
$sform = new XoopsThemeForm(_MYJOB_DEMAND_SEARCH, 'demandsearchform', XOOPS_URL . '/modules/myjob/demandes-search.php', 'post');
// Secteur d'activité *******************************************************************************************************************************
$secteuractiviteselect = new XoopsFormSelect(_MYJOB_DEMAND_SECTEURACTIVITE, 'secteurid', '', 5, true);
foreach ($secteurs as $valeur => $libelle) {
    $secteuractiviteselect->addOption($valeur, $libelle);
}
$sform->addElement($secteuractiviteselect, myjob_fields('secteurid', true, 'demande'));

// Zone géographique ********************************************************************************************************************************
$zonegeographiqueselect = new XoopsFormSelect(_MYJOB_DEMAND_ZONEGEOGRAPHIQUE, 'zoneid', '', 5, true);
foreach ($zones as $valeur => $libelle) {    // Les textes
    $zonegeographiqueselect->addOption($valeur, $libelle);
}
if (!isset($demandofferzones_handler)) {
    $demandofferzones_handler = xoops_getModuleHandler('demandofferzones', 'myjob');
}
$sform->addElement($zonegeographiqueselect, myjob_fields('zoneid', true, 'demande'));

// Expérience ***************************************************************************************************************************************

if (!isset($experience_handler)) {
    $experience_handler = xoops_getModuleHandler('experience', 'myjob');
}
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('1', '1', '='));
$criteria->setSort('experienceid');
$tblexperience    = $experience_handler->getObjects($criteria);
$experienceselect = new XoopsFormSelect(_MYJOB_DEMAND_EXPERIENCE, 'experience');
$experienceselect->addOption(0, '---');
foreach ($tblexperience as $oneexperience) {
    $experienceselect->addOption($oneexperience->getVar('experienceid'), $oneexperience->getVar('libelle'));
}
$sform->addElement($experienceselect, false);

// Demandes datées de xx jours **********************************************************************************************************************
$periodeselect    = new XoopsFormSelect(_MYJOB_DEMAND_SEARCH_FROM, 'since', '', 1, false);
$tblsearchperiods = explode(',', $searchperiods);
foreach ($tblsearchperiods as $oneperiod) {
    $periodeselect->addOption($oneperiod, $oneperiod . _MYJOB_DEMAND_SEARCH_LAST_DAYS);
}
$periodeselect->setValue($oneperiod);    // Par défaut sélection de la plus grande valeur utilisateur

// Valeur 'système', depuis le début.
$periodeselect->addOption(0, _MYJOB_DEMAND_SEARCH_BEGIN);
$sform->addElement($periodeselect, false);

// Date de disponibilité ************************************************************************************************************************************
//$sform->addElement(new XoopsFormTextDateSelect(_MYJOB_DEMAND_DATEDISPO,'datedispo',15,time()),false);
$since_tray = new XoopsFormElementTray(_MYJOB_DEMAND_DATEDISPO, '');

$months = new XoopsFormSelect('', 'month', 0, 1, false);
foreach ($tblmois as $key => $onemonth) {
    $months->addOption($key, $onemonth);
}
$since_tray->addElement($months);

$y     = date('Y');
$years = new XoopsFormSelect('', 'year', 0, 1, false);
$years->addOption(0, '---');
$years->addOption($y, $y);
$years->addOption($y + 1, $y + 1);
$since_tray->addElement($years);
$sform->addElement($since_tray);

// Type de poste ************************************************************************************************************************************
$typeposteselect = new XoopsFormSelect(_MYJOB_DEMAND_TYPEPOSTE, 'typeposte', 0);
$typeposteselect->addOption(0, '---');
foreach ($types as $valeur => $libelle) {
    $typeposteselect->addOption($valeur, $libelle);
}
$sform->addElement($typeposteselect, myjob_fields('typeposte', true, 'demande'));

// Compétences **************************************************************************************************************************************
$sform->addElement(new XoopsFormTextArea(_MYJOB_DEMAND_COMPETENCES, 'competences', '', 5, 60), false);

// Diplôme ******************************************************************************************************************************************
$sform->addElement(new XoopsFormText(_MYJOB_DEMAND_DIPLOME, 'diplome', 50, 255), false);

// **************************************************************************************************************
$sform->addElement(new XoopsFormHidden('op', 'go'));

$button_tray = new XoopsFormElementTray('', '');
$submit_btn  = new XoopsFormButton('', 'post', _MYJOB_POST, 'submit');
$button_tray->addElement($submit_btn);
$sform->addElement($button_tray);
