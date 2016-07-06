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

function myjob_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsDB, $xoopsUser;
    // 1) Recherche dans les demandes
    $sql = 'SELECT demandid, uid, datesoumission, titreannonce FROM ' . $xoopsDB->prefix('myjob_demande') . ' WHERE datevalidation<>0';
    if ($userid != 0) {    // Dans le cas de l'affichage d'un profil, la demande est visible
        $sql .= ' AND uid=' . $userid . ' ';
    } else {
        $sql .= ' AND dateexpiration>' . time();
    }

    if (is_array($queryarray) && $count = count($queryarray)) {
        $sql .= " AND ((nom LIKE '%$queryarray[0]%' OR prenom LIKE '%$queryarray[0]%' OR email LIKE '%$queryarray[0]%' OR diplome LIKE '%$queryarray[0]%' OR formation LIKE '%$queryarray[0]%' OR zonegeographique LIKE '%$queryarray[0]%' OR secteuractivite LIKE '%$queryarray[0]%' OR adresse LIKE '%$queryarray[0]%' OR cp LIKE '%$queryarray[0]%' OR ville LIKE '%$queryarray[0]%' OR telephone LIKE '%$queryarray[0]%' OR langues LIKE '%$queryarray[0]%' OR zonelibre LIKE '%$queryarray[0]%' OR titreannonce LIKE '%$queryarray[0]%')";
        for ($i = 1; $i < $count; ++$i) {
            $sql .= " $andor ";
            $sql .= "(nom LIKE '%$queryarray[$i]%' OR prenom LIKE '%$queryarray[$i]%' OR email LIKE '%$queryarray[$i]%' OR diplome LIKE '%$queryarray[$i]%' OR formation LIKE '%$queryarray[$i]%' OR zonegeographique LIKE '%$queryarray[$i]%' OR secteuractivite LIKE '%$queryarray[$i]%' OR adresse LIKE '%$queryarray[$i]%' OR cp LIKE '%$queryarray[$i]%' OR ville LIKE '%$queryarray[$i]%' OR telephone LIKE '%$queryarray[$i]%' OR langues LIKE '%$queryarray[$i]%' OR zonelibre LIKE '%$queryarray[$i]%' OR titreannonce LIKE '%$queryarray[$i]%')";
        }
        $sql .= ') ';
    }

    $sql .= 'ORDER BY datesoumission DESC';
    $result = $xoopsDB->query($sql, $limit, $offset);
    $ret    = array();
    $i      = 0;
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $ret[$i]['image'] = 'images/myjob.gif';
        $ret[$i]['link']  = 'demande-view.php?demandid=' . $myrow['demandid'];
        $ret[$i]['title'] = $myrow['titreannonce'];
        $ret[$i]['time']  = $myrow['datesoumission'];
        $ret[$i]['uid']   = $myrow['uid'];
        ++$i;
    }
    $save = $i;

    // 2) Recherche dans les offres
    $sql = 'SELECT offreid, datesoumission, titreannonce, approver FROM ' . $xoopsDB->prefix('myjob_offre') . ' WHERE (online=1) ';
    if (is_array($queryarray) && $count = count($queryarray)) {
        $sql .= " AND ((secteuractivite LIKE '%$queryarray[0]%' OR profil LIKE '%$queryarray[0]%' OR lieuactivite LIKE '%$queryarray[0]%' OR nomentreprise LIKE '%$queryarray[0]%' OR adresse LIKE '%$queryarray[0]%' OR cp LIKE '%$queryarray[0]%' OR ville LIKE '%$queryarray[0]%' OR contact LIKE '%$queryarray[0]%' OR email LIKE '%$queryarray[0]%' OR telephone LIKE '%$queryarray[0]%' OR titreannonce LIKE '%$queryarray[0]%' OR description LIKE '%$queryarray[0]%' OR statut LIKE '%$queryarray[0]%')";
        for ($i = 1; $i < $count; ++$i) {
            $sql .= " $andor ";
            $sql .= "(secteuractivite LIKE '%$queryarray[$i]%' OR profil LIKE '%$queryarray[$i]%' OR lieuactivite LIKE '%$queryarray[$i]%' OR nomentreprise LIKE '%$queryarray[$i]%' OR adresse LIKE '%$queryarray[$i]%' OR cp LIKE '%$queryarray[$i]%' OR ville LIKE '%$queryarray[$i]%' OR contact LIKE '%$queryarray[$i]%' OR email LIKE '%$queryarray[$i]%' OR telephone LIKE '%$queryarray[$i]%' OR titreannonce LIKE '%$queryarray[$i]%' OR description LIKE '%$queryarray[$i]%' OR statut LIKE '%$queryarray[$i]%')";
        }
        $sql .= ') ';
    }

    $sql .= 'ORDER BY datesoumission DESC';
    $result = $xoopsDB->query($sql, $limit, $offset);
    $i      = $save;
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $ret[$i]['image'] = 'images/myjob.gif';
        $ret[$i]['link']  = 'offer-view.php?offerid=' . $myrow['offreid'];
        $ret[$i]['title'] = $myrow['titreannonce'];
        $ret[$i]['time']  = $myrow['datesoumission'];
        $ret[$i]['uid']   = $myrow['approver'];
        ++$i;
    }

    return $ret;
}
