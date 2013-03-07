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


class offre extends MjObject
{

	function offre()
	{
		$this->initVar('offreid',XOBJ_DTYPE_INT,null,false);
		$this->initVar('secteurid',XOBJ_DTYPE_INT,null,false);
		$this->initVar('secteuractivite',XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('profil',XOBJ_DTYPE_TXTAREA, null, false);
		$this->initVar('zoneid',XOBJ_DTYPE_INT,null,false);
		$this->initVar('lieuactivite',XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('nomentreprise',XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('adresse',XOBJ_DTYPE_TXTAREA, null, false);
		$this->initVar('cp',XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('ville',XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('datedispo',XOBJ_DTYPE_INT,null,false);
		$this->initVar('contact',XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('email',XOBJ_DTYPE_EMAIL, null, false);
		$this->initVar('telephone',XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('typeposte',XOBJ_DTYPE_INT,null,false);
		$this->initVar('titreannonce',XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('description',XOBJ_DTYPE_TXTAREA, null, false);
		$this->initVar('experience',XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('statut',XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('online',XOBJ_DTYPE_INT,null,false);
		$this->initVar('datesoumission',XOBJ_DTYPE_INT,null,false);
		$this->initVar('datevalidation',XOBJ_DTYPE_INT,null,false);
		$this->initVar('approver',XOBJ_DTYPE_INT,null,false);
		$this->initVar('hits',XOBJ_DTYPE_INT,null,false);
		$this->initVar('ip',XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('uid',XOBJ_DTYPE_INT,null,false);
		$this->initVar('attachedfile',XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('pass',XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('salary1',XOBJ_DTYPE_INT,null,false);
		$this->initVar('salary2',XOBJ_DTYPE_INT,null,false);
		$this->initVar('salarytype',XOBJ_DTYPE_INT,null,false);
	}
}


class MyjobOffreHandler extends MjXoopsPersistableObjectHandler
{

	function MyjobOffreHandler($db)
	{	//											Table		Classe		Id
		$this->MjXoopsPersistableObjectHandler($db, 'myjob_offre', 'offre', 'offreid');
	}


	function updateCounter($offreid)
	{
		$sql = 'UPDATE ' . $this->db->prefix('myjob_offre') . ' SET hits=hits+1 WHERE offreid = ' . intval($offreid);
		If ($this->db->queryF($sql)) {
			return true;
		} else {
			return false;
		}
	}


	function delete(&$offre, $force = false)
	{
		if (get_class($offre) != 'offre') {
			return false;
		}
		// TODO: Compléter pour les tables liées
		$sql = sprintf("DELETE FROM %s WHERE offreid = %u", $this->db->prefix('myjob_offre'), $offre->getVar('offreid'));
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


	function unvalidate($offreid)
	{
		$sql = 'UPDATE ' . $this->db->prefix('myjob_offre') . ' SET online=0 WHERE offreid = ' . intval($offreid);
		if ($this->db->queryF($sql)) {
			return true;
		} else {
			return false;
		}
	}
}
?>