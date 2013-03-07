<?php
//  ------------------------------------------------------------------------ //
//                      MYJOB - MODULE FOR XOOPS 2.0.x                       //
//                  Copyright (c) 2005-2006 Instant Zero                     //
//                     <http://www.instant-zero.com/>                        //
// ------------------------------------------------------------------------- //
//  This program is NOT free software; you can NOT redistribute it and/or    //
//  modify without my assent.   										     //
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

include_once XOOPS_ROOT_PATH.'/modules/myjob/include/functions.php';

function b_myjob_stats_show()
{
	global $xoopsConfig;
	$block = array();
	if (file_exists(XOOPS_ROOT_PATH.'/modules/myjob/language/' . $xoopsConfig['language'] . '/main.php')) {
		include_once XOOPS_ROOT_PATH.'/modules/myjob/language/' . $xoopsConfig['language'] . '/main.php';
	} else {
		include_once XOOPS_ROOT_PATH.'/modules/myjob/language/english/main.php';
	}
	$useoffers=myjob_getmoduleoption('useoffers');
	$usedemands=myjob_getmoduleoption('usedemands');
	$prolongation_handler =& xoops_getmodulehandler('prolongation', 'myjob');

	if($useoffers) {
		$offre_handler =& xoops_getmodulehandler('offre', 'myjob');
		$offersvalid = $offre_handler->getCount(new Criteria('online', '1', '='));
		$offerswaiting = $offre_handler->getCount(new Criteria('online', '0', '='));
		$offersprolongations = $prolongation_handler->getCount(new Criteria('offreid', 0,'<>'));
		$block['stats'][] = sprintf(_MYJOB_OFFERS_COUNT,$offersvalid);
		$block['stats'][] = sprintf(_MYJOB_OFFERS_WAITING,$offerswaiting);
		$block['stats'][] = sprintf(_MYJOB_OFFERS_PROLONGATE,$offersprolongations);
	}
	if($usedemands) {
		$demande_handler =& xoops_getmodulehandler('demande', 'myjob');
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('datevalidation', '0','<>'));
		$criteria->add(new Criteria('dateexpiration', time(),'>'));
		$demandsvalid = $demande_handler->getCount($criteria);
		$demandswaiting = $demande_handler->getCount(new Criteria('datevalidation', '0','='));
		$demandsprolongations = $prolongation_handler->getCount(new Criteria('demandid', 0,'<>'));
		$block['stats'][] = sprintf(_MYJOB_DEMANDS_COUNT,$demandsvalid);
		$block['stats'][] = sprintf(_MYJOB_DEMANDS_WAITING,$demandswaiting);
		$block['stats'][] = sprintf(_MYJOB_DEMANDS_PROLONGATE,$demandsprolongations);
	}
	return $block;
}



function b_myjob_stats_show_onthefly()
{
	$block = & b_myjob_stats_show();
	$tpl = new XoopsTpl();
	$tpl->assign('block', $block);
	$tpl->display('db:myjob_block_stats.html');
}
?>