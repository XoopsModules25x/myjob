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

class secteuractivite extends MjObject
{
	function secteuractivite()
	{
		$this->initVar('secteurid',XOBJ_DTYPE_INT,null,false);
		$this->initVar('libelle',XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('image',XOBJ_DTYPE_TXTBOX, null, false);
	}
}

class MyjobSecteuractiviteHandler extends MjXoopsPersistableObjectHandler
{
	function MyjobSecteuractiviteHandler($db)
	{	//												Table					Classe			Id
		$this->MjXoopsPersistableObjectHandler($db, 'myjob_secteuractivite', 'secteuractivite', 'secteurid');
	}


	function delete(&$secteuractivite, $force = false)
	{
		if (get_class($secteuractivite) != 'secteuractivite') {
			return false;
		}

		// Vérification, est-ce que ce secteur d'activité n'est pas utilisée dans les demandes ?
		$sql='SELECT Count(*) as cpt FROM '.$this->db->prefix('myjob_demandoffersecteurs').' WHERE secteurid='.$secteuractivite->getVar('secteurid');
		$myrow = $this->db->fetchArray($this->db->query($sql));
		if($myrow['cpt']>0) {
			return false;
		}

		$sql = sprintf("DELETE FROM %s WHERE secteurid = %u", $this->db->prefix('myjob_secteuractivite'), $secteuractivite->getVar('secteurid'));
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