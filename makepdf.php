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

error_reporting(0);
include_once 'header.php';
require_once XOOPS_ROOT_PATH.'/modules/myjob/fpdf/fpdf.inc.php';
include_once XOOPS_ROOT_PATH.'/modules/myjob/include/functions.php';

if(!myjob_getmoduleoption('usepdf')) {
   redirect_header('index.php', 2, _ERRORS);
   exit();
}

$myts =& MyTextSanitizer::getInstance();
$useoffers=myjob_getmoduleoption('useoffers');
$usedemands=myjob_getmoduleoption('usedemands');

$offreid = isset($_GET['offreid']) ? intval($_GET['offreid']) : 0;
$demandid = isset($_GET['demandid']) ? intval($_GET['demandid']) : 0;


if(!empty($demandid) && $usedemands) {
	$zonegeographique_handler=& xoops_getmodulehandler('zonegeographique', 'myjob');
	$zonegeographiques = $zonegeographique_handler->getObjects();
	$zones=array();
	foreach($zonegeographiques as $onezonegeographique) {
		$zones[$onezonegeographique->getVar('zoneid')]=$onezonegeographique->getVar('libelle');
	}

	// Lecture des secteurs d'activité
	$secteuractivite_handler=& xoops_getmodulehandler('secteuractivite', 'myjob');
	$secteuractivites = $secteuractivite_handler->getObjects();
	$secteurs=array();
	foreach($secteuractivites as $onesecteuractivite) {
		$secteurs[$onesecteuractivite->getVar('secteurid')]=$onesecteuractivite->getVar('libelle');
	}

	// Chargement de la liste des types de postes
	$typeposte_handler=& xoops_getmodulehandler('typeposte', 'myjob');
	$typespostes = $typeposte_handler->getObjects();
	$types=array();
	foreach($typespostes as $onetypeposte) {
		$types[$onetypeposte->getVar('typeid')]=$onetypeposte->getVar('libelle');
	}
	// Les situations de famille
	$sitfam_handler=& xoops_getmodulehandler('sitfam', 'myjob');
	$sitesfams = $sitfam_handler->getObjects();
	$tblsitfam=array();
	foreach($sitesfams as $onesitfam) {
		$tblsitfam[$onesitfam->getVar('sitfamid')]=$onesitfam->getVar('libelle');
	}

	// Relation offre/demande <-> zones géographiques
	$demandofferzones_handler =& xoops_getmodulehandler('demandofferzones', 'myjob');

	// Relation offre/demande <-> secteurs d'activité
	$demandoffersecteurs_handler =& xoops_getmodulehandler('demandoffersecteurs', 'myjob');

	$demande_handler =& xoops_getmodulehandler('demande', 'myjob');
	$demande = $demande_handler->get($demandid);
	if(!$demande || $demande->getVar('datevalidation')==0 && !isset($_GET['op'])) {
	    redirect_header('index.php', 2, _ERRORS);
	    exit();
	}
	$critere=new Criteria('r.demandid', $demande->getVar('demandid'),'=');
	$critere->setSort('libelle');
	// Récupération des zones géographiques
	$tblzones=$demandofferzones_handler->getLibsWithRelation($critere);
	$libzones=join('<br />',$tblzones);
	// Récupération des secteurs
	$tblsecteurs=$demandoffersecteurs_handler->getLibsWithRelation($critere);
	$libsecteurs=join('<br />',$tblsecteurs);
	$content = '';
	if(myjob_fields('nom',false,'demande')) $content .= _MYJOB_DEMAND_NOM." : ".$demande->getVar("nom").'<br />';
	if(myjob_fields('prenom',false,'demande')) $content .= _MYJOB_DEMAND_PRENOM." : ".$demande->getVar("prenom").'<br />';
	if(myjob_fields('adresse',false,'demande')) $content .= _MYJOB_DEMAND_ADRESSE." : ".$demande->getVar("adresse")."<br />";
	if(myjob_fields('cp',false,'demande')) $content .= _MYJOB_DEMAND_CP." : ".$demande->getVar("cp")."<br />";
	if(myjob_fields('ville',false,'demande')) $content .= _MYJOB_DEMAND_VILLE." : ".$demande->getVar("ville")."<br />";
	if(myjob_fields('telephone',false,'demande')) $content .= _MYJOB_DEMAND_TELEPHONE." : ".$demande->getVar("telephone")."<br />";
	if(myjob_fields('email',false,'demande')) $content .= _MYJOB_DEMAND_EMAIL." : ".$demande->getVar("email")."<br />";
	if(myjob_fields('datenaiss',false,'demande')) $content .= _MYJOB_DEMAND_DATENAISS." : ".$demande->getVar("datenaiss")."<br />";
	if(myjob_fields('datesoumission',false,'demande')) $content .= _MYJOB_DEMAND_DATESOUMISSION." : ".formatTimestamp($demande->getVar("datesoumission"),'s')."<br />";
	if(myjob_fields('dateexpiration',false,'demande')) $content .= _MYJOB_DEMAND_EXPIRATION." : ".formatTimestamp($demande->getVar("dateexpiration"),'s')."<br />";
	if(myjob_fields('titreannonce',false,'demande')) $content .= _MYJOB_DEMAND_DESCRIPTION." : ".$demande->getVar("titreannonce")."<br />";
	if(myjob_fields('diplome',false,'demande')) $content .= _MYJOB_DEMAND_DIPLOME." : ".$demande->getVar("diplome")."<br />";
	if(myjob_fields('formation',false,'demande')) $content .= _MYJOB_DEMAND_FORMATION." : ".$demande->getVar("formation")."<br />";
	if(myjob_fields('typeposte',false,'demande')) $content .= _MYJOB_DEMAND_TYPEPOSTE." : ".$types[$demande->getVar("typeposte")]."<br />";
	if(myjob_fields('zoneid',false,'demande')) {
		$content .= _MYJOB_DEMAND_ZONEGEOGRAPHIQUE." : ".$libzones."<br />";
	}
	if(myjob_fields('zonegeographique',false,'demande')) $content .= _MYJOB_DEMAND_ZONEGEOGRAPHIQUEA." : ".$demande->getVar("zonegeographique")."<br />";
	if(myjob_fields('secteurid',false,'demande')) $content .= _MYJOB_DEMAND_SECTEURACTIVITE." : ".$libsecteurs."<br />";
	if(myjob_fields('secteuractivite',false,'demande')) $content .= _MYJOB_DEMAND_SECTEURACTIVITEA." : ".$demande->getVar("secteuractivite")."<br />";
	if(myjob_fields('experience',false,'demande')) $content .= _MYJOB_DEMAND_EXPERIENCE." : ".$demande->getVar("libelle_experience")."<br />";
	if(myjob_fields('experiencedetail',false,'demande')) $content .= _MYJOB_OFFER_EXPERIENCEDETAIL." : ".$demande->getVar("experiencedetail")."<br />";
	if(myjob_fields('datedispo',false,'demande')) $content .= _MYJOB_DEMAND_DATEDISPO." : ".formatTimestamp($demande->getVar("datedispo"),'s')."<br />";
	if(myjob_fields('parain',false,'demande')) $content .= _MYJOB_DEMAND_PARAIN." : ".$demande->getVar("parain")."<br />";
	if(myjob_fields('datevalidation',false,'demande')) $content .= _MYJOB_OFFER_DATEVALID." : ".formatTimestamp($demande->getVar("datevalidation"),'s')."<br />";
	if(myjob_fields('langues',false,'demande')) $content .= _MYJOB_DEMAND_LANGUES." : ".$demande->getVar("langues")."<br />";
	if(myjob_fields('zonelibre',false,'demande')) $content .= _MYJOB_DEMAND_ZONELIBRE." : ".$demande->getVar("zonelibre")."<br />";
	if(myjob_fields('sitfam',false,'demande')) $content .= _MYJOB_DEMAND_SITFAM." : ".$tblsitfam[$demande->getVar("sitfam")]."<br />";
	if(myjob_fields('competences',false,'demande')) $content .= _MYJOB_DEMAND_COMPETENCES." : ".$demande->getVar("competences")."<br />";
	if(myjob_fields('divers',false,'demande')) $content .= _MYJOB_DEMAND_DIVERS." : ".$demande->getVar("divers")."<br />";
	if(myjob_fields('hits',false,'demande')) $content .= _MYJOB_DEMAND_HITS." : ".$demande->getVar("hits")."<br />";

	$author = '';
	if(myjob_fields('nom',false,'demande')) $author .= $demande->getVar("nom").'<br />';
	if(myjob_fields('prenom',false,'demande')) $author .= $demande->getVar("prenom").'<br />';

	$pdf_title = $demande->getVar('titreannonce');
	$pdf_content = $content;
	$pdf_author = $author;
	$pdf_topic_title = $demande->getVar('titreannonce');
	$pdf_title = $demande->getVar('titreannonce');
	$pdf_subtitle = '';
	$pdf_subsubtitle = '';
	$pdf_author = $author;
	$pdf_date = formatTimestamp($demande->getVar('datevalidation'));
	$pdf_url = XOOPS_URL.'/modules/myjob/demande-view.php?demandid='.$demandid;
}

// ***************************************************************************************************************************************


	$pdf_topic_title = myjob_html2text($myts->undoHtmlSpecialChars($pdf_topic_title));
	$forumdata['topic_title'] = $pdf_topic_title;
	$pdf_data['title'] = $pdf_title;
	$pdf_data['subtitle'] = myjob_html2text($pdf_subtitle);
	$pdf_data['subsubtitle'] = myjob_html2text($pdf_subsubtitle);
	$pdf_data['date'] = $pdf_date;
	$pdf_data['content'] = $myts->undoHtmlSpecialChars($pdf_content);
	$pdf_data['author'] = $pdf_author;

	//Other stuff
	$puff='<br />';
	$puffer='<br /><br /><br />';

	//create the A4-PDF...
	$pdf_config['slogan']=$xoopsConfig['sitename'].' - '.$xoopsConfig['slogan'];
	$pdf_config['creator'] = 'MYJOB';
	$pdf_config['url'] = $pdf_url;

	$pdf=new PDF();
	if(method_exists($pdf, 'encoding')){
		$pdf->encoding($pdf_data, _CHARSET);
	}
	$pdf->SetCreator($pdf_config['creator']);
	$pdf->SetTitle($pdf_data['title']);
	$pdf->SetAuthor($pdf_config['url']);
	$pdf->SetSubject($pdf_data['author']);
	$out=$pdf_config['url'].', '.$pdf_data['author'].', '.$pdf_data['title'].', '.$pdf_data['subtitle'].', '.$pdf_data['subsubtitle'];
	$pdf->SetKeywords($out);
	$pdf->SetAutoPageBreak(true,25);
	$pdf->SetMargins($pdf_config['margin']['left'],$pdf_config['margin']['top'],$pdf_config['margin']['right']);
	$pdf->Open();

	//First page
	$pdf->AddPage();
	$pdf->SetXY(24,25);
	$pdf->SetTextColor(10,60,160);
	$pdf->SetFont($pdf_config['font']['slogan']['family'],$pdf_config['font']['slogan']['style'],$pdf_config['font']['slogan']['size']);
	$pdf->WriteHTML($pdf_config['slogan'], $pdf_config['scale']);
	$pdf->Line(25,30,190,30);
	$pdf->SetXY(25,35);
	$pdf->SetFont($pdf_config['font']['title']['family'],$pdf_config['font']['title']['style'],$pdf_config['font']['title']['size']);
	$pdf->WriteHTML($pdf_data['title'],$pdf_config['scale']);

	if ($pdf_data['subtitle']<>''){
		$pdf->WriteHTML($puff,$pdf_config['scale']);
		$pdf->SetFont($pdf_config['font']['subtitle']['family'],$pdf_config['font']['subtitle']['style'],$pdf_config['font']['subtitle']['size']);
		$pdf->WriteHTML($pdf_data['subtitle'],$pdf_config['scale']);
	}
	if ($pdf_data['subsubtitle']<>'') {
		$pdf->WriteHTML($puff,$pdf_config['scale']);
		$pdf->SetFont($pdf_config['font']['subsubtitle']['family'],$pdf_config['font']['subsubtitle']['style'],$pdf_config['font']['subsubtitle']['size']);
		$pdf->WriteHTML($pdf_data['subsubtitle'],$pdf_config['scale']);
	}

	$pdf->WriteHTML($puff,$pdf_config['scale']);
	$pdf->SetFont($pdf_config['font']['author']['family'],$pdf_config['font']['author']['style'],$pdf_config['font']['author']['size']);
	$out=MYJOB_PDF_AUTHOR.': ';
	$out.=$pdf_data['author'];
	$pdf->WriteHTML($out,$pdf_config['scale']);
	$pdf->WriteHTML($puff,$pdf_config['scale']);
	$out=MYJOB_PDF_DATE;
	$out.=$pdf_data['date'];
	$pdf->WriteHTML($out,$pdf_config['scale']);
	$pdf->WriteHTML($puff,$pdf_config['scale']);

	$pdf->SetTextColor(0,0,0);
	$pdf->WriteHTML($puffer,$pdf_config['scale']);

	$pdf->SetFont($pdf_config['font']['content']['family'],$pdf_config['font']['content']['style'],$pdf_config['font']['content']['size']);
	$pdf->WriteHTML($pdf_data['content'],$pdf_config['scale']);
	$pdf->Output();
?>