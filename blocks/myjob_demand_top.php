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

include_once XOOPS_ROOT_PATH . '/modules/myjob/include/functions.php';

/**
 * Show recent demands
 * @param $options
 * @return array
 */
function b_myjob_top_demand_show($options)
{
	// '1|10|30|0|0|0|0';	// 1=Trié par date (2=par lectures), 10=Nombre d'éléments à afficher, 30=Longueur du titre, 0=longueur du texte d'intro, 0=type de poste (0=tous sinon l'id), 0=zone géographique (0=tous sinon l'id), 0=secteur d'activité (0=tous sinon l'id)
    $block         = array();
    $start         = 0;
    $limit         = $options[1];
    $block['sort'] = $options[0];

	// Relation offre/demande <-> zones géographiques
    $demandofferzones_handler = xoops_getModuleHandler('demandofferzones', 'myjob');

	// Relation offre/demande <-> secteurs d'activité
    $demandoffersecteurs_handler = xoops_getModuleHandler('demandoffersecteurs', 'myjob');

	// Lecture des demandes récentes
    // Les filtres :
    // $options[4]=type de poste,
	// $options[5]=zone géographique
	// $options[6]=secteur d'activité
    $demande_handler = xoops_getModuleHandler('demande', 'myjob');
    $criteria        = new CriteriaCompo();
    $criteria->add(new Criteria('d.datevalidation', '0', '<>'));
    $criteria->add(new Criteria('d.dateexpiration', time(), '>'));
    if ($options[6] != 0) {
        $criteria->add(new Criteria('s.secteurid', $options[6], '='));
    }
    if ($options[5] != 0) {
        $criteria->add(new Criteria('z.zoneid', $options[5], '='));
    }
    if ($options[4] != 0) {
        $criteria->add(new Criteria('p.typeid', $options[4], '='));
    }
    $criteria->setLimit($limit);
    $criteria->setStart($start);
    if ($options[0] == '1') {
        $criteria->setSort('datevalidation');
    } else {
        $criteria->setSort('hits');
    }
    $criteria->setOrder('DESC');
    $demandes = $demande_handler->getFilteredDemands($criteria);

    foreach ($demandes as $onedemande) {
        $array   = $onedemande->toArray();
        $critere = new Criteria('r.demandid', $onedemande->getVar('demandid'), '=');
        $critere->setSort('libelle');
		// Récupération des zones géographiques
        $tblzones = $demandofferzones_handler->getLibsWithRelation($critere);
        $libzones = implode('<br>', $tblzones);
		// Récupération des secteurs
        $tblsecteurs               = $demandoffersecteurs_handler->getLibsWithRelation($critere);
        $libsecteurs               = implode('<br>', $tblsecteurs);
        $array['zonesidlibelle']   = $libzones;
        $array['secteuridlibelle'] = $libsecteurs;
        $array['title']            = xoops_substr(strip_tags($onedemande->getVar('titreannonce')), 0, (int)$options[2]);
        if ((int)$options[3] > 0) {
            $array['teaser'] = xoops_substr(strip_tags($onedemande->getVar('experiencedetail')), 0, (int)$options[3]);
        }
        $array['infotip']   = 'title="' . myjob_make_infotips($onedemande->getVar('experiencedetail')) . '"';
        $block['demands'][] = $array;
    }

    return $block;
}

function b_myjob_top_demand_edit($options)
{
    global $xoopsConfig;
	// '1|10|30|0|0|0|0';	// 1=Trié par date (2=par lectures), 10=Nombre d'éléments à afficher, 30=Longueur du titre, 0=longueur du texte d'intro, 0=type de poste (0=tous sinon l'id), 0=zone géographique (0=tous sinon l'id), 0=secteur d'activité (0=tous sinon l'id)
    if (file_exists(XOOPS_ROOT_PATH . '/modules/myjob/language/' . $xoopsConfig['language'] . '/main.php')) {
        include_once XOOPS_ROOT_PATH . '/modules/myjob/language/' . $xoopsConfig['language'] . '/main.php';
    } else {
        include_once XOOPS_ROOT_PATH . '/modules/myjob/language/english/main.php';
    }

    $form = _MB_MYJOB_ORDER . "&nbsp;<select name='options[]'>\n";
    $form .= "<option value='1'";
    if ($options[0] == '1') {
        $form .= " selected='selected'";
    }
    $form .= '>' . _MB_MYJOB_DATE . "</option>\n";
    $form .= "<option value='2'";
    if ($options[0] == '2') {
        $form .= " selected='selected'";
    }
    $form .= '>' . _MB_MYJOB_HITS . "</option>\n";
    $form .= "</select>\n";
    $form .= '&nbsp;' . _MB_MYJOB_DISP . "&nbsp;<input type='text' name='options[]' value='" . $options[1] . "'/>&nbsp;" . _MB_MYJOB_ELEMENTS . "\n";
    $form .= "&nbsp;<br><br>\n" . _MB_MYJOB_CHARS . "&nbsp;<input type='text' name='options[]' value='" . $options[2] . "'/>&nbsp;" . _MB_MYJOB_LENGTH . "\n<br><br>\n";
    $form .= _MB_MYJOB_TEASER . " <input type='text' name='options[]' value='" . $options[3] . "' />" . _MB_MYJOB_LENGTH . "<br>\n";

    $critere = new Criteria('libelle', '###', '<>');
    $critere->setSort('libelle');

    // Types de poste
    $form .= '<br>' . _MYJOB_OFFER_TYPEPOSTE . " <select name='options[]'>\n";
    $checked = $options[4] == 0 ? ' selected="selected" ' : '';
    $form .= "<option value='0'" . $checked . '>' . _MYJOB_ALL . "</option>\n";
    $typeposte_handler = xoops_getModuleHandler('typeposte', 'myjob');
    $typespostes       = $typeposte_handler->getObjects($critere);
    foreach ($typespostes as $onetypeposte) {
        $checked = $options[4] == $onetypeposte->getVar('typeid') ? ' selected="selected" ' : '';
        $form .= '<option ' . $checked . " value='" . $onetypeposte->getVar('typeid') . "'>" . $onetypeposte->getVar('libelle') . "</option>\n";
    }
    $form .= '</select>';

	// Lecture des zones géographiques
    $form .= '<br>' . _MYJOB_DEMAND_ZONEGEOGRAPHIQUE . " <select name='options[]'>\n";
    $checked = $options[5] == 0 ? ' selected="selected" ' : '';
    $form .= "<option value='0'" . $checked . '>' . _MYJOB_ALL . "</option>\n";
    $zonegeographique_handler = xoops_getModuleHandler('zonegeographique', 'myjob');
    $zonegeographiques        = $zonegeographique_handler->getObjects($critere);
    foreach ($zonegeographiques as $onezonegeographique) {
        $checked = $options[5] == $onezonegeographique->getVar('zoneid') ? ' selected="selected" ' : '';
        $form .= '<option ' . $checked . " value='" . $onezonegeographique->getVar('zoneid') . "'>" . $onezonegeographique->getVar('libelle') . "</option>\n";
    }
    $form .= '</select>';

	// Lecture des secteurs d'activité
    $form .= '<br>' . _MYJOB_DEMAND_SECTEURACTIVITE . " <select name='options[]'>\n";
    $checked = $options[6] == 0 ? ' selected="selected" ' : '';
    $form .= "<option value='0'" . $checked . '>' . _MYJOB_ALL . "</option>\n";
    $secteuractivite_handler = xoops_getModuleHandler('secteuractivite', 'myjob');
    $secteuractivites        = $secteuractivite_handler->getObjects($critere);
    foreach ($secteuractivites as $onesecteuractivite) {
        $checked = $options[6] == $onesecteuractivite->getVar('secteurid') ? ' selected="selected" ' : '';
        $form .= '<option ' . $checked . " value='" . $onesecteuractivite->getVar('secteurid') . "'>" . $onesecteuractivite->getVar('libelle') . "</option>\n";
    }
    $form .= '</select>';

    return $form;
}

function b_myjob_top_demand_onthefly($options)
{
    $options = explode('|', $options);
    $block   = &b_myjob_top_demand_show($options);

    $tpl = new XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('db:myjob_block_demand_top.tpl');
}
