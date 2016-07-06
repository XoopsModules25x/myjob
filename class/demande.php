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

include_once XOOPS_ROOT_PATH . '/modules/myjob/include/functions.php';
include_once XOOPS_ROOT_PATH . '/kernel/object.php';
if (!class_exists('MjXoopsPersistableObjectHandler')) {
    include_once XOOPS_ROOT_PATH . '/modules/myjob/class/PersistableObjectHandler.php';
}

class demande extends MjObject
{
    public function __construct()
    {
        $this->initVar('demandid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('datesoumission', XOBJ_DTYPE_INT, null, false);
        $this->initVar('nom', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prenom', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('email', XOBJ_DTYPE_EMAIL, null, false);
        $this->initVar('datenaiss', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('diplome', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('formation', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('typeposte', XOBJ_DTYPE_INT, null, false);
        $this->initVar('zonegeographique', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('secteuractivite', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('experience', XOBJ_DTYPE_INT, null, false);
        $this->initVar('experiencedetail', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('datedispo', XOBJ_DTYPE_INT, null, false);
        $this->initVar('adresse', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('cp', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('ville', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('telephone', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('parain', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('datevalidation', XOBJ_DTYPE_INT, null, false);
        $this->initVar('dateexpiration', XOBJ_DTYPE_INT, null, false);
        $this->initVar('langues', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('zonelibre', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('sitfam', XOBJ_DTYPE_INT, null, false);
        $this->initVar('hits', XOBJ_DTYPE_INT, null, false);
        $this->initVar('titreannonce', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('ip', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('competences', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('divers', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('attachedfile', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('pass', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('contacts', XOBJ_DTYPE_INT, null, false);    // Nombre de prises de contacts
        // Champs ajout� par relation
		$this->initVar('libelle_experience',XOBJ_DTYPE_TXTBOX, null, false);	// Libellé de l'expérience
    }
}

class MyjobDemandeHandler extends MjXoopsPersistableObjectHandler
{
    public function __construct($db)
    {    //                                         Table           Classe          Id
        parent::__construct($db, 'myjob_demande', 'demande', 'demandid');
    }

    /**
     * @param  mixed $id        ID of the object - or array of ids for joint keys. Joint keys MUST be given in the same order as in the constructor
     * @param  bool  $as_object whether to return an object or an array
     * @return mixed reference to the object, FALSE if failed
     */
    public function &get($id, $as_object = true)
    {
        $sql = 'SELECT d.*, e.libelle as libelle_experience FROM ' . $this->db->prefix('myjob_demande') . ' d LEFT OUTER JOIN ' . $this->db->prefix('myjob_experience') . ' e ON d.experience = e.experienceid WHERE demandid=' . (int)$id;
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        if ($numrows == 1) {
            $demande = $this->create();
            $demande->assignVars($this->db->fetchArray($result));

            return $demande;
        }

        return false;
    }

    public function delete(XoopsObject $demande, $force = false)
    {
        if (get_class($demande) !== 'demande') {
            return false;
        }
		// On commence par supprimer toutes les demandes de prolongation qui sont rattachées à cette offre.
        $sql    = 'DELETE FROM ' . $this->db->prefix('myjob_prolongation') . ' WHERE demandid=' . $demande->getVar('demandid');
        $result = $this->db->queryF($sql);
		// Puis toutes les zones géographiques rattachées
        $sql    = 'DELETE FROM ' . $this->db->prefix('myjob_demandofferzones') . ' WHERE demandid=' . $demande->getVar('demandid');
        $result = $this->db->queryF($sql);
		// Puis tous les secteurs géographiques rattachés
        $sql    = 'DELETE FROM ' . $this->db->prefix('myjob_demandoffersecteurs') . ' WHERE demandid=' . $demande->getVar('demandid');
        $result = $this->db->queryF($sql);
		// Le fichier attaché s'il existe
        if (trim($demande->getVar('attachedfile')) != '') {    // S'il y a un fichier on le supprime
            if (file_exists(XOOPS_UPLOAD_PATH . '/' . $demande->getVar('attachedfile'))) {
                unlink(XOOPS_UPLOAD_PATH . '/' . $demande->getVar('attachedfile'));
            }
        }
		// Ensuite on passe à la demande elle même
        $sql = sprintf('DELETE FROM %s WHERE demandid = %u', $this->db->prefix('myjob_demande'), $demande->getVar('demandid'));
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

    /**
     * Prolonge une demande d'emploi
     * @param $id
     */
    public function prolongate($id)
    {
        $duree  = myjob_getmoduleoption('defaultduration') * 86400;
        $sql    = 'UPDATE ' . $this->db->prefix('myjob_demande') . ' SET dateexpiration=dateexpiration+' . $duree . ' WHERE demandid=' . (int)$id;
        $result = $this->db->queryF($sql);
    }

    /**
	* Mise à jour du compteur de prises de contact
     * @param $id
     */
    public function update_contacts($id)
    {
        $sql    = 'UPDATE ' . $this->db->prefix('myjob_demande') . ' SET contacts=contacts+1 WHERE demandid=' . (int)$id;
        $result = $this->db->queryF($sql);
    }

    /**
	* Récupère toutes les offres pour lesquelles il y a eu une demande de prolongation
     */
    public function &getAllProlongationsDemands()
    {
        $ret = array();
		// TODO: A vérifier
        $sql = 'SELECT ' . $this->db->prefix('myjob_demande') . '.*, e.libelle as libelle_experience  FROM ' . $this->db->prefix('myjob_prolongation') . ' LEFT OUTER JOIN ' . $this->db->prefix('myjob_demande') . ' ON ' . $this->db->prefix('myjob_prolongation') . '.demandid='
               . $this->db->prefix('myjob_demande') . '.demandid';
        $sql .= ' LEFT OUTER JOIN ' . $this->db->prefix('myjob_experience') . ' e ON ' . $this->db->prefix('myjob_demande') . '.experience = e.experienceid';
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $demande = $this->create();
            $demande->assignVars($myrow);
            $ret[$myrow['demandid']] = $demande;
        }

        return $ret;
    }

    /**
	* Fonction générique permettant de récupérer des enregistrements (sous la forme d'objets) répondants à des critères
     * @param null|CriteriaElement $criteria  {@link CriteriaElement} conditions to be met
     * @param bool                 $id_as_key
     * @param bool                 $as_object return an array of objects?
     * @return array
     */
    public function &getObjects(CriteriaElement $criteria = null, $id_as_key = false, $as_object = true)
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT d.*, e.libelle as libelle_experience FROM ' . $this->db->prefix('myjob_demande') . ' d LEFT OUTER JOIN ' . $this->db->prefix('myjob_experience') . ' e ON d.experience = e.experienceid';
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
            $obj = $this->create(false);
            $obj->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $obj;
            } else {
                $ret[$myrow['demandid']] = $obj;
            }
        }

        return $ret;
    }

    /**
	* Renvoie les demandes d'emploi dont les dictionnaires correspondent à des critères (utilisé dans la recherche et dans les blocs)
     * @param null $criteria
     * @param bool $id_as_key
     * @param bool $count
     * @return array
     */
    public function getFilteredDemands($criteria = null, $id_as_key = false, $count = false)
    {
        $ret   = array();
        $limit = $start = 0;
        if ($count) {
            $what = 'Count(Distinct d.demandid)';
        } else {
            $what = 'Distinct d.*, e.libelle as libelle_experience';
        }
        //$sql = 'SELECT '.$what.' FROM '.$this->db->prefix('myjob_demande').' d LEFT OUTER JOIN '.$this->db->prefix('myjob_demandoffersecteurs').' s ON d.demandid=s.demandid LEFT OUTER JOIN '.$this->db->prefix('myjob_demandofferzones').' z ON d.demandid=z.demandid LEFT OUTER JOIN '.$this->db->prefix('myjob_typeposte').' p ON d.typeposte = p.typeid LEFT OUTER JOIN '.$this->db->prefix('myjob_experience').' e ON d.experience = e.experienceid ';
        $sql =
            'SELECT ' . $what . ' FROM ' . $this->db->prefix('myjob_demande') . ' d JOIN ' . $this->db->prefix('myjob_demandoffersecteurs') . ' s ON d.demandid=s.demandid JOIN ' . $this->db->prefix('myjob_demandofferzones') . ' z ON d.demandid=z.demandid JOIN ' . $this->db->prefix('myjob_typeposte')
            . ' p ON d.typeposte = p.typeid JOIN ' . $this->db->prefix('myjob_experience') . ' e ON d.experience = e.experienceid ';
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
        if ($count) {
            list($cnt) = $this->db->fetchRow($result);

            return $cnt;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $obj = $this->create(false);
            $obj->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $obj;
            } else {
                $ret[$myrow['demandid']] = $obj;
            }
        }

        return $ret;
    }

    /**
	* Renvoie des demandes d'emploi de manière aléatoire
     * @param null $criteria
     * @param bool $id_as_key
     * @return array
     */
    public function getRandomDemands($criteria = null, $id_as_key = false)
    {
        $ret   = $rand_keys = $ret3 = array();
        $limit = $start = 0;
        $sql   = 'SELECT distinct d.*, e.libelle as libelle_experience FROM ' . $this->db->prefix('myjob_demande') . ' d LEFT OUTER JOIN ' . $this->db->prefix('myjob_demandoffersecteurs') . ' s ON d.demandid=s.demandid LEFT OUTER JOIN ' . $this->db->prefix('myjob_demandofferzones')
                 . ' z ON d.demandid=z.demandid LEFT OUTER JOIN ' . $this->db->prefix('myjob_typeposte') . ' p ON d.typeposte = p.typeid ';
        $sql .= ' LEFT OUTER JOIN ' . $this->db->prefix('myjob_experience') . ' e ON d.experience = e.experienceid ';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY RAND(),' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $demande = $this->create();
            $demande->assignVars($myrow);
            $ret[] = $demande;
        }

        return $ret;
    }

    /**
	 * Mise à jour du compteur de hits
     * @param $demandid
     * @return bool
     */
    public function updateCounter($demandid)
    {
        $sql = 'UPDATE ' . $this->db->prefix('myjob_demande') . ' SET hits=hits+1 WHERE demandid = ' . (int)$demandid;
        if ($this->db->queryF($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
	 * Dévalidation d'une demande d'emploi
     * @param $demandid
     * @return bool
     */
    public function unvalidate($demandid)
    {
        $sql = 'UPDATE ' . $this->db->prefix('myjob_demande') . ' SET datevalidation=0 WHERE demandid = ' . (int)$demandid;
        if ($this->db->queryF($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
	 * Renvoie les x types de contrats les plus demandés
     * @param int    $limit
     * @param int    $start
     * @param string $orderby
     * @param string $desc
     * @return array
     */
    public function getTopTypeContrat($limit = 0, $start = 0, $orderby = 'cpt', $desc = '')
    {
        $ret = array();
        $ts  =& MyTextSanitizer::getInstance();
        $sql = 'SELECT COUNT(*) AS cpt, d.typeposte, t.libelle FROM ' . $this->db->prefix('myjob_demande') . ' d LEFT OUTER JOIN ' . $this->db->prefix('myjob_typeposte') . ' t ON d.typeposte= t.typeid WHERE d.datevalidation<>0 AND d.dateexpiration > ' . time() . ' GROUP BY d.typeposte ORDER BY '
               . $orderby . ' ' . $desc;

        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[$myrow['typeposte']] = array('count' => $myrow['cpt'], 'title' => $ts->htmlSpecialChars($myrow['libelle']));
        }

        return $ret;
    }
}
