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

/**
 * Gestion d'une demande d'emploi par l'utilisateur
 */
include('header.php');
$xoopsOption['template_main'] = 'myjob_mydemand.html';
include_once XOOPS_ROOT_PATH.'/header.php';
include_once XOOPS_ROOT_PATH.'/modules/myjob/include/functions.php';
$autoconnect = false;

$usedemands = myjob_getmoduleoption('usedemands');
if(!$usedemands) {
	// Changer ces appels en header()
    redirect_header('index.php',2,'');
    exit();
}
$demande_handler = & xoops_getmodulehandler('demande', 'myjob');

if (isset($_GET['demandid']) ) {
	$demandeid=intval($_GET['demandid']);
} else {
	if(isset($_POST['demandid'])) {
		$demandeid=intval($_POST['demandid']);
	} else {
		// On va v�rifier et rechercher les demandes d'emploi de l'utilisateur courant
		if(!is_object($xoopsUser)) {
    		redirect_header('index.php',2,_MYJOB_ERROR3);
    		exit();
    	} else {
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('datevalidation', '0','<>'));
			$criteria->add(new Criteria('dateexpiration', time(),'>'));
			$criteria->add(new Criteria('uid', $xoopsUser->getVar('uid'),'='));
			if($demande_handler->getCount($criteria)>0) {
				$criteria->setSort('datevalidation');
				$criteria->setLimit(1);
				$criteria->setStart(0);
				$tbldemandes = $demande_handler->getObjects($criteria);
				$lademande = $tbldemandes[0];
				$demandeid = $lademande->getVar('demandid');
				$autoconnect = true;
			}
    	}
    }
}
$demande = $demande_handler->get($demandeid);

// Est-ce que la demande existe ?
if(!$demande) {
    redirect_header('index.php',2,_MYJOB_DONT_EXIST);
    exit();
}

// Est-ce que la demande est en ligne (valid�e) ?
if($demande->getVar('datevalidation')==0) {
    redirect_header('index.php',2,_MYJOB_CANT_MODIFY);
    exit();
}

$prolongation = myjob_getmoduleoption('prolongation');
$typeposte_handler = & xoops_getmodulehandler('typeposte', 'myjob');
$typespostes = $typeposte_handler->getObjects();
$types = array();
foreach($typespostes as $onetypeposte) {
	$types[$onetypeposte->getVar('typeid')]=$onetypeposte->getVar('libelle');
}
$xoopsTpl->assign('typesoffres', $types);

$sitfam_handler = & xoops_getmodulehandler('sitfam', 'myjob');
$sitesfams = $sitfam_handler->getObjects();
$tblsitfam = array();
foreach($sitesfams as $onesitfam) {
	$tblsitfam[$onesitfam->getVar('sitfamid')]=$onesitfam->getVar('libelle');
}
$xoopsTpl->assign('sitfam', $tblsitfam);

if(is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
	$xoopsTpl->assign('isadmin',true);
} else {
    $xoopsTpl->assign('isadmin',false);
}

$op='login';
if(isset($_GET['op'])) {
	$op=$_GET['op'];
} else {
	if(isset($_POST['op'])) {
		$op=$_POST['op'];
	} else {
		if($autoconnect) {
			if(!isset($_SESSION['myjob_demandpassword'])) {
				$_SESSION['myjob_demandpassword']=$demande->getVar('pass');
				$_SESSION['myjob_demandid']=$demandeid;
			}
			$op = 'view';
		}
	}
}

$prolongation_handler =& xoops_getmodulehandler('prolongation', 'myjob');
$cnt=0;
$cnt=$prolongation_handler->getCount(new Criteria('demandid', $demandeid, '='));
if($prolongation && $cnt>0) {	// Les prolongations sont accept�es et la personne a d�j� demand� une prolongation, on d�sactive donc le lien permettant de demander une prolongation
	$prolongation = false;
}

switch ($op) {
	/**
 	* V�rification du mot de passe
 	*/
	case 'verifypass':
		if(isset($_POST['password']) && $demande->getVar('pass')==md5($_POST['password'])) {
			$op='view';
			$_SESSION['myjob_demandpassword']=$demande->getVar('pass');
			$_SESSION['myjob_demandid']=$demandeid;
		} else {
			$op='login';
			$xoopsTpl->assign('errormsg',_MYJOB_BAD_PASSWORD);
		}
		break;

	/**
 	* Edition d'une demande
 	*/
	case 'demandedit':
		if(isset($_SESSION['myjob_demandpassword'])) {
			if($demande->getVar('pass')!=$_SESSION['myjob_demandpassword'] || $_SESSION['myjob_demandid']!=$demandeid) {
			    redirect_header('index.php',2,_ERRORS);
    			exit();
			} else {
				include_once XOOPS_ROOT_PATH.'/modules/myjob/include/demand_form.php';
			}
		} else {
		    redirect_header('index.php',2,_ERRORS);
    		exit();
		}

	/**
 	* Suppression d'une demande
 	*/
	case 'demanddelete':
		// On commence par v�rifier qu'on est connect� et qu'on est propri�taire de l'annonce pour laquelle on fait une demande de prolongation
		if(isset($_SESSION['myjob_demandpassword'])) {
			if($demande->getVar('pass')!=$_SESSION['myjob_demandpassword'] || $_SESSION['myjob_demandid']!=$demandeid) {	// Mauvais mot de passe
			    redirect_header('index.php',2,_ERRORS);
   				exit();
			} else {	// Suppression effective
				// en fait la demande va se faire 'p�rimer' par le script
				$demande->setVar('dateexpiration',time()-86400);	// On la p�rime de la veille
				$prolongation_handler->insert($demande);
				myjob_updateCache();							// Mise � jour du cache
				if(myjob_getmoduleoption('prolongation')) {		// Suppression de la demande de prolongation (s'il y en a une)
					$prolongationo = '';
					$prolongationo = $prolongation_handler->getbydemandid($demandeid);
					if(is_object($prolongationo)) {
						$prolongation_handler->delete($prolongationo,true);
					}
				}
				if(isset($_SESSION['myjob_demands_count'])) {
					$_SESSION['myjob_demands_count'] = intval($_SESSION['myjob_demands_count'])-1;
				}
				redirect_header('index.php',2,_MYJOB_DEMAND_DELETED);
				exit();

/*
				if(trim($demande->getVar('attachedfile'))!='') {	// S'il y a un fichier on le supprime
	    			if (file_exists(XOOPS_UPLOAD_PATH.'/'.$demande->getVar('attachedfile'))) {
    					unlink(XOOPS_UPLOAD_PATH.'/'.$demande->getVar('attachedfile'));
    				}
				}
				$demande_handler->delete($demande,true);		// Suppression de la demande
				myjob_updateCache();							// Mise � jour du cache
				if(myjob_getmoduleoption('prolongation')) {		// Suppression de la demande de prolongation (s'il y en a une)
					$prolongationo = '';
					$prolongationo = $prolongation_handler->getbydemandid($demandeid);
					if(is_object($prolongationo)) {
						$prolongation_handler->delete($prolongationo,true);
					}
				}
				redirect_header('index.php',2,_MYJOB_DEMAND_DELETED);
				exit();
*/

			}
		} else {	// Pas logg�
			    redirect_header('index.php',2,_ERRORS);
    			exit();
		}
		break;


	/**
 	* Demande de prolongation
 	*/
	case 'demandprolongate':
		if($prolongation) {
			// On commence par v�rifier qu'on est connect� et qu'on est propri�taire de l'annonce pour laquelle on fait une demande de prolongation
			if(isset($_SESSION['myjob_demandpassword'])) {
				if($demande->getVar('pass') != $_SESSION['myjob_demandpassword'] || $_SESSION['myjob_demandid']!=$demandeid) {
				    redirect_header('index.php',2,_ERRORS);
    				exit();
				} else {
					// C'est bien l'auteur de la demande d'emploi, on v�rifie maintenant qu'il n'a pas d�j� fait une demande de prolongation
					if($cnt) {	// La personne a d�j� fait une demande de prolongation, on lui indique.
						redirect_header('my-demande.php?demandid='.$demandeid,2,_MYJOB_PROLONGATION_ALREADY_ASKED);
					} else {	// Il n'y a pas eu de demande de prolongation, on l'enregistre
						$prolongationo = $prolongation_handler->create(true);
						$prolongationo->setVar('demandid',$demandeid);
						$prolongationo->setVar('offreid',0);
						$prolongationo->setVar('date',time());
						$res = $prolongation_handler->insert($prolongationo, true);
						if($res!=false) {		// La sauvegarde s'est bien d�roul�e
							redirect_header('my-demande.php?op=view&demandid='.$demandeid,2,_MYJOB_PROLONGATION_OK);
						} else {	// Probl�me pendant la sauvegarde
							redirect_header('my-demande.php?op=view&demandid='.$demandeid,3,_MYJOB_PROLONGATION_PB);
						}
					}
				}
			} else {
			    redirect_header('index.php',2,_ERRORS);
    			exit();
			}
		} else {	// Le module n'a pas �t� param�tr� pour g�rer les demandes de prolongation
		    redirect_header('index.php',2,_ERRORS);
    		exit();
		}
		break;
}

// Lecture des zones g�ographiques
$zonegeographique_handler=& xoops_getmodulehandler('zonegeographique', 'myjob');
$zonegeographiques = $zonegeographique_handler->getObjects();
$zones=array();
foreach($zonegeographiques as $onezonegeographique) {
	$zones[$onezonegeographique->getVar('zoneid')]=$onezonegeographique->getVar('libelle');
}

// Lecture des secteurs d'activit�
$secteuractivite_handler=& xoops_getmodulehandler('secteuractivite', 'myjob');
$secteuractivites = $secteuractivite_handler->getObjects();
$secteurs=array();
foreach($secteuractivites as $onesecteuractivite) {
	$secteurs[$onesecteuractivite->getVar('secteurid')]=$onesecteuractivite->getVar('libelle');
}

// Lecture des situations de famille
$sitfam_handler=& xoops_getmodulehandler('sitfam', 'myjob');
$sitesfams = $sitfam_handler->getObjects();
$tblsitfam=array();
foreach($sitesfams as $onesitfam) {
	$tblsitfam[$onesitfam->getVar('sitfamid')]=$onesitfam->getVar('libelle');
}

$array=$demande->toArray();
// Relation offre/demande <-> zones g�ographiques
$demandofferzones_handler =& xoops_getmodulehandler('demandofferzones', 'myjob');

// Relation offre/demande <-> secteurs d'activit�
$demandoffersecteurs_handler =& xoops_getmodulehandler('demandoffersecteurs', 'myjob');
$critere=new Criteria('r.demandid', $demande->getVar('demandid'),'=');
$critere->setSort('libelle');
// R�cup�ration des zones g�ographiques
$tblzones=$demandofferzones_handler->getLibsWithRelation($critere);
$libzones=join('<br />',$tblzones);
// R�cup�ration des secteurs
$tblsecteurs=$demandoffersecteurs_handler->getLibsWithRelation($critere);
$libsecteurs=join('<br />',$tblsecteurs);


$array['secteurid_libelle'] = $libsecteurs;
$array['zonesgeographiques_libelle'] = $libzones;

$xoopsTpl->assign('onedemande', $array);
$xoopsTpl->assign('demandid',$demandeid);
$xoopsTpl->assign('prolongation', $prolongation);
$xoopsTpl->assign('op', $op);
$xoopsTpl->assign('conf_del_link', myjob_JavascriptLinkConfirm(_MYJOB_DEMAND_CONF_DELETE));
$myts =& MyTextSanitizer::getInstance();
// Create page's title
$xoopsTpl->assign('xoops_pagetitle', _MYJOB_MYDEMAND.' - '.$myts->htmlSpecialChars($xoopsModule->name()));

include_once(XOOPS_ROOT_PATH.'/footer.php');
?>