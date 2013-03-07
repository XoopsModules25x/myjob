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

include_once XOOPS_ROOT_PATH.'/class/xoopsobject.php';
if (!class_exists('MjXoopsPersistableObjectHandler')) {
	include_once XOOPS_ROOT_PATH.'/modules/myjob/class/PersistableObjectHandler.php';
}

class prolongation extends MjObject
{

	function prolongation()
	{
		$this->initVar('prolongationid',XOBJ_DTYPE_INT,null,false);
		$this->initVar('demandid',XOBJ_DTYPE_INT,null,false);
		$this->initVar('offreid',XOBJ_DTYPE_INT,null,false);
		$this->initVar('date',XOBJ_DTYPE_INT,null,false);
	}
}

class MyjobprolongationHandler extends MjXoopsPersistableObjectHandler
{
	function MyjobprolongationHandler($db)
	{	//											Table					Classe			Id
		$this->MjXoopsPersistableObjectHandler($db, 'myjob_prolongation', 'prolongation', 'prolongationid');
	}

	function &getbydemandid($id)
	{
		$critere = new Criteria('demandid',$id,'=');
		$tbl_prolongations = array();
		$tbl_prolongations = $this->getObjects($critere);
		if(count($tbl_prolongations) > 0) {
			$prolongation = $tbl_prolongations[0];
			return $prolongation;
		} else {
			return false;
		}
	}


	// TODO: supprimer les éléments liés dans les autres tables
	function delete(&$prolongation, $force = false)
	{
		if (get_class($prolongation) != 'prolongation') {
			return false;
		}
		$sql = sprintf("DELETE FROM %s WHERE prolongationid = %u", $this->db->prefix('myjob_prolongation'), $prolongation->getVar('prolongationid'));
		if (false != $force) {
			$result = $this->db->queryF($sql);
		} else {
			$result = $this->db->query($sql);
		}
		if (!$result) {
			return false;
		}
		return true;
	}
}
?>