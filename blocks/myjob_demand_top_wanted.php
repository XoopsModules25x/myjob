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

function b_myjob_topx_demand_show($options)
{
	// '0|10|0' 0=Quoi voir ? (0=types de poste, 1=secteurs, 2=zones géographiques), 10=Nb éléments à voir, 0=trier par compteur ou 1 par libellé
    $block = array();

    $usedemands = myjob_getmoduleoption('usedemands');

    if ($usedemands) {
		// Relation offre/demande <-> zones géographiques
        $demandofferzones_handler = xoops_getModuleHandler('demandofferzones', 'myjob');
		// Relation offre/demande <-> secteurs d'activité
        $demandoffersecteurs_handler = xoops_getModuleHandler('demandoffersecteurs', 'myjob');
        // Demandes d'emploi
        $demande_handler = xoops_getModuleHandler('demande', 'myjob');

        $tbldatas = array();
        $sortby   = $options[2] == 0 ? 'libelle' : 'cpt';
        $desc     = $options[2] == 0 ? '' : 'DESC';

        switch ((int)$options[0]) {
            case 0:    // Types de poste (CDD/CDI)
                $tbldatas = $demande_handler->getTopTypeContrat((int)$options[1], 0, $sortby, $desc);
                break;

			case 1:	// Secteurs d'activité
                $tbldatas = $demandoffersecteurs_handler->getTop('demandes', (int)$options[1], 0, $sortby, $desc);
                break;

			case 2:	// Zones géographiques
                $tbldatas = $demandofferzones_handler->getTop('demandes', (int)$options[1], 0, $sortby, $desc);
                break;
        }

        foreach ($tbldatas as $onedatakey => $onedatavalues) {
            $block['stats'][] = array('title' => $onedatavalues['title'], 'count' => $onedatavalues['count']);
        }
    }

    return $block;
}

function b_myjob_topx_demand_edit($options)
{
    global $xoopsConfig;
	// '0|10|0' 0=Quoi voir ? (0=types de poste, 1=secteurs, 2=zones géographiques), 10=Nb éléments à voir, 0=trier par compteur ou 1 par libellé
    if (file_exists(XOOPS_ROOT_PATH . '/modules/myjob/language/' . $xoopsConfig['language'] . '/main.php')) {
        include_once XOOPS_ROOT_PATH . '/modules/myjob/language/' . $xoopsConfig['language'] . '/main.php';
    } else {
        include_once XOOPS_ROOT_PATH . '/modules/myjob/language/english/main.php';
    }

    $form = _MB_MYJOB_WHAT2SEE . "&nbsp;<select name='options[]'>\n";
    $form .= "<option value='0'";
    if ($options[0] == '0') {
        $form .= " selected='selected'";
    }
    $form .= '>' . _MYJOB_OFFER_TYPEPOSTE . "</option>\n";
    $form .= "<option value='1'";
    if ($options[0] == '1') {
        $form .= " selected='selected'";
    }
    $form .= '>' . _MYJOB_OFFER_SECTEUR . "</option>\n";

    $form .= "<option value='2'";
    if ($options[0] == '2') {
        $form .= " selected='selected'";
    }
    $form .= '>' . _MYJOB_DEMAND_ZONEGEOGRAPHIQUE . "</option>\n";
    $form .= "</select>\n";

    $form .= '<br>' . _MB_MYJOB_DISP . "&nbsp;<input type='text' name='options[]' size='4' value='" . $options[1] . "'/>&nbsp;" . _MB_MYJOB_ELEMENTS . "<br>\n";

    $form .= _MB_MYJOB_SORT_BY . "&nbsp;<select name='options[]'>\n";
    $form .= "<option value='0'";
    if ($options[2] == '0') {
        $form .= " selected='selected'";
    }
    $form .= '>' . _MB_MYJOB_SORT_BY_LIB . "</option>\n";
    $form .= "<option value='1'";
    if ($options[2] == '1') {
        $form .= " selected='selected'";
    }
    $form .= '>' . _MB_MYJOB_SORT_BY_COUNTER . "</option>\n";
    $form .= "</select>\n";

    return $form;
}

function b_myjob_topx_demand_onthefly($options)
{
    $options = explode('|', $options);
    $block   = &b_myjob_topx_demand_show($options);
    $tpl     = new XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('db:myjob_block_demand_xstats.tpl');
}
