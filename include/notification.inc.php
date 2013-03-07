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

if (!defined('XOOPS_ROOT_PATH')) {
	die('XOOPS root path not defined');
}

function myjob_notify_iteminfo($category, $item_id)
{
	$moduleDirName = 'myjob';
    global $xoopsDB;

	switch($category)
	{
		case 'offres':
			$sql = 'SELECT description FROM ' . $xoopsDB->prefix('myjob_offres') . ' WHERE offreid = '.$item_id;
			if (!$result = $xoopsDB->query($sql)){
			  	redirect_header('index.php', 2, _ERRORS);
    			exit();
			}
			$result_array = $xoopsDB->fetchArray($result);
			$item['name'] = $result_array['forum_name'];
			$item['url'] = XOOPS_URL . '/modules/' . $moduleDirName . '/admin/index.php?validOffer=' . $item_id;
			return $item;
			break;

		case 'demandes':
			$sql = 'SELECT nom, prenom FROM ' . $xoopsDB->prefix('myjob_demande') . ' WHERE demandid = '.$item_id;
			if (!$result = $xoopsDB->query($sql)){
			  	redirect_header('index.php', 2, _ERRORS);
    			exit();
			}
			$result_array = $xoopsDB->fetchArray($result);
			$item['name'] = xoops_trim($result_array['nom']).' '.xoops_trim($result_array['prenom']);
			$item['url'] = XOOPS_URL . '/modules/' . $moduleDirName . '/admin/index.php?validDemand=' . $item_id;
			return $item;
			break;
	}
}
?>
