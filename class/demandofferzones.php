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

include_once XOOPS_ROOT_PATH . '/kernel/object.php';
include_once XOOPS_ROOT_PATH . '/modules/myjob/include/functions.php';
if (!class_exists('MjXoopsPersistableObjectHandler')) {
    include_once XOOPS_ROOT_PATH . '/modules/myjob/class/PersistableObjectHandler.php';
}

class demandofferzones extends MjObject
{
    public function __construct()
    {
        $this->initVar('demandofferzoneid', XOBJ_DTYPE_INT, null, false);        // Id unique de l'enregistrement
        $this->initVar('demandid', XOBJ_DTYPE_INT, null, false);                // Id de la demande
        $this->initVar('offreid', XOBJ_DTYPE_INT, null, false);                    // Id de l'offre
        $this->initVar('zoneid', XOBJ_DTYPE_INT, null, false);                    // Id de la zone g�ographique
    }
}

class MyjobDemandofferzonesHandler extends MjXoopsPersistableObjectHandler
{
    public function __construct($db)
    {    //                                         Table                       Classe              Id
        parent::__construct($db, 'myjob_demandofferzones', 'demandofferzones', 'demandofferzoneid');
    }

    public function getLibsWithRelation($criteria = null, $id_as_key = false)
    {
        $ret   = array();
        $ts    = MyTextSanitizer::getInstance();
        $limit = $start = 0;
        $sql   = 'SELECT r.*, z.* FROM ' . $this->db->prefix('myjob_demandofferzones') . ' r, ' . $this->db->prefix('myjob_zonegeographique') . ' z';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $sql .= ' AND r.zoneid=z.zoneid ';
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            if (!$id_as_key) {
                $ret[] = $ts->htmlSpecialChars($myrow['libelle']);
            } else {
                $ret[$myrow['zoneid']] = $ts->htmlSpecialChars($myrow['libelle']);
            }
        }

        return $ret;
    }

    public function getArray($criteria = null)
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT zoneid FROM ' . $this->db->prefix('myjob_demandofferzones');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[] = $myrow['zoneid'];
        }

        return $ret;
    }

    /**
	* Renvoie les x zones les plus demandées soit dans les offres soit dans les demandes
     * @param string $type
     * @param int    $limit
     * @param int    $start
     * @param string $orderby
     * @param string $desc
     * @return array
     */
    public function getTop($type = 'demandes', $limit = 0, $start = 0, $orderby = 'cpt', $desc = '')
    {
        $ret = array();
        $ts  = MyTextSanitizer::getInstance();
        if ($type === 'demandes') {
            $field = 'demandid';
        } else {
            $field = 'offreid';
        }
        $sql    =
            'SELECT z.zoneid, Count(z.zoneid) As cpt, s.libelle FROM ' . $this->db->prefix('myjob_demandofferzones') . ' z LEFT JOIN ' . $this->db->prefix('myjob_zonegeographique') . ' s ON z.zoneid=s.zoneid LEFT JOIN ' . $this->db->prefix('myjob_demande') . ' d on z.demandid=d.demandid WHERE z.'
            . $field . '<>0 GROUP BY z.zoneid ORDER BY ' . $orderby . ' ' . $desc;
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[$myrow['zoneid']] = array('count' => $myrow['cpt'], 'title' => $ts->htmlSpecialChars($myrow['libelle']));
        }

        return $ret;
    }
}
