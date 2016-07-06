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

if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    $admin = true;
} else {
    $admin = false;
}

include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
$sform = new XoopsThemeForm(_MYJOB_SUBMITOFFER, 'offerform', XOOPS_URL . '/modules/myjob/submit-offer.php');
if (trim(myjob_getmoduleoption('legaltext')) != '' && !$admin) {
    $sform->insertBreak('<center><b>' . myjob_getmoduleoption('legaltext') . '</b></center>', 'even');
}

$sform->addElement(new XoopsFormText(_MYJOB_OFFER_ENTREPRISE, 'nomentreprise', 50, 255, $offre->getVar('nomentreprise', 'e')), false);
$sform->addElement(new XoopsFormTextArea(_MYJOB_OFFER_ADRESSE, 'adresse', $offre->getVar('adresse', 'e'), 15, 80), false);
$sform->addElement(new XoopsFormText(_MYJOB_OFFER_CP, 'cp', 10, 10, $offre->getVar('cp', 'e')), false);
$sform->addElement(new XoopsFormText(_MYJOB_OFFER_VILLE, 'ville', 50, 255, $offre->getVar('ville', 'e')), false);
$sform->addElement(new XoopsFormText(_MYJOB_OFFER_CONTACT, 'contact', 50, 255, $offre->getVar('contact', 'e')), false);

$emailzn = new XoopsFormText(_MYJOB_OFFER_EMAIL, 'email', 50, 200, $offre->getVar('email', 'e'));
$emailzn->setDescription(_MYJOB_ENTER_EMAIL);
$sform->addElement($emailzn, false);

$sform->addElement(new XoopsFormText(_MYJOB_OFFER_TEL, 'telephone', 40, 400, $offre->getVar('telephone', 'e')), false);
$sform->addElement(new XoopsFormText(_MYJOB_OFFER_SECTEUR, 'secteuractivite', 50, 255, $offre->getVar('secteuractivite', 'e')), false);
$sform->addElement(new XoopsFormTextArea(_MYJOB_OFFER_PROFIL, 'profil', $offre->getVar('profil', 'e'), 15, 80), false);
$sform->addElement(new XoopsFormText(_MYJOB_OFFER_LIEU, 'lieuactivite', 50, 255, $offre->getVar('lieuactivite', 'e')), false);
$sform->addElement(new XoopsFormTextDateSelect(_MYJOB_OFFER_DATEDISPO, 'datedispo', 15, $offre->getVar('datedispo', 'e')), false);

$typeposteselect = new XoopsFormSelect(_MYJOB_OFFER_TYPEPOSTE, 'typeposte', $offre->getVar('typeposte', 'e'));
foreach ($types as $valeur => $libelle) {
    $typeposteselect->addOption($valeur, $libelle);
}
$sform->addElement($typeposteselect);

$sform->addElement(new XoopsFormText(_MYJOB_OFFER_TITREANNONCE, 'titreannonce', 50, 255, $offre->getVar('titreannonce', 'e')), false);
$sform->addElement(new XoopsFormTextArea(_MYJOB_OFFER_DESCRIPTION, 'description', $offre->getVar('description', 'e'), 15, 80), false);
$sform->addElement(new XoopsFormText(_MYJOB_OFFER_EXPERIENCE, 'experience', 50, 255, $offre->getVar('experience', 'e')), false);

$statutzn = new XoopsFormText(_MYJOB_OFFER_STATUT, 'statut', 50, 255, $offre->getVar('statut', 'e'));
$statutzn->setDescription(_MYJOB_OFFER_STATUT_DSC);
$sform->addElement($statutzn, false);
$sform->addElement(new XoopsFormHidden('offreid', $offre->getVar('offreid')));
$sform->addElement(new XoopsFormHidden('approver', $offre->getVar('approver')));

if ($admin) {
    $sform->addElement(new XoopsFormHidden('admin', 1));
    $sform->addElement(new XoopsFormRadioYN(_MYJOB_OFFER_ONLINE, 'online', $offre->getVar('online', 'e'), _YES, _NO));
}

if (!isset($adminside)) {
    $adminside = false;
}
$sform->addElement(new XoopsFormHidden('adminside', $adminside));

if (!$warning && !$admin) {
    $sform->insertBreak('<center><b>' . _MYJOB_SUBMITOFFER_WARNING . '</b></center>', 'even');
}
$button_tray = new XoopsFormElementTray('', '');
$submit_btn  = new XoopsFormButton('', 'post', _MYJOB_POST, 'submit');
$button_tray->addElement($submit_btn);
$sform->addElement($button_tray);
$sform->display();
