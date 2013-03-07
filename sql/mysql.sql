CREATE TABLE `myjob_demande` (
  `demandid` int(10) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `datesoumission` int(10) unsigned NOT NULL default '0',
  `nom` varchar(255) NOT NULL default '',
  `prenom` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `datenaiss` varchar(10) NOT NULL default '',
  `diplome` varchar(255) NOT NULL default '',
  `formation` text NOT NULL,
  `typeposte` tinyint(1) NOT NULL default '0',
  `zonegeographique` varchar(255) NOT NULL default '',
  `secteuractivite` varchar(255) NOT NULL default '',
  `experience` int(10) unsigned NOT NULL default '0',
  `experiencedetail` text NOT NULL,
  `datedispo` int(10) unsigned NOT NULL default '0',
  `adresse` text NOT NULL,
  `cp` varchar(10) NOT NULL default '',
  `ville` varchar(255) NOT NULL default '',
  `telephone` varchar(40) NOT NULL default '',
  `parain` varchar(255) NOT NULL default '',
  `datevalidation` int(10) unsigned NOT NULL default '0',
  `dateexpiration` int(10) unsigned NOT NULL default '0',
  `langues` text NOT NULL,
  `zonelibre` text NOT NULL,
  `sitfam` tinyint(1) NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  `titreannonce` varchar(255) NOT NULL default '',
  `ip` varchar(20) NOT NULL default '',
  `competences` text NOT NULL,
  `divers` text NOT NULL,
  `attachedfile` varchar(255) NOT NULL default '',
  `pass` varchar(32) NOT NULL default '',
  `contacts` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`demandid`),
  KEY `datesoumission` (`datesoumission`),
  KEY `datevalidation` (`datevalidation`),
  KEY `contacts` (`contacts`)
) ENGINE=MyISAM;


CREATE TABLE `myjob_demandoffersecteurs` (
  `demandoffersecteurid` int(10) unsigned NOT NULL auto_increment,
  `demandid` int(10) unsigned NOT NULL default '0',
  `offreid` int(10) unsigned NOT NULL default '0',
  `secteurid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`demandoffersecteurid`),
  KEY `demandid` (`demandid`),
  KEY `offreid` (`offreid`),
  KEY `secteurid` (`secteurid`),
  KEY `demandesecteur` (`demandid`,`secteurid`),
  KEY `offresecteur` (`offreid`,`secteurid`)
) ENGINE=MyISAM;


CREATE TABLE `myjob_demandofferzones` (
  `demandofferzoneid` int(10) unsigned NOT NULL auto_increment,
  `demandid` int(10) unsigned NOT NULL default '0',
  `offreid` int(10) unsigned NOT NULL default '0',
  `zoneid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`demandofferzoneid`),
  KEY `demandid` (`demandid`),
  KEY `offreid` (`offreid`),
  KEY `zoneid` (`zoneid`),
  KEY `demandezone` (`demandid`,`zoneid`),
  KEY `offrezone` (`offreid`,`zoneid`)
) ENGINE=MyISAM;


CREATE TABLE `myjob_experience` (
  `experienceid` int(10) unsigned NOT NULL auto_increment,
  `libelle` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL,
  PRIMARY KEY  (`experienceid`),
  KEY `libelle` (`libelle`)
) ENGINE=MyISAM;


CREATE TABLE `myjob_offre` (
  `offreid` int(10) unsigned NOT NULL auto_increment,
  `secteuractivite` varchar(255) NOT NULL default '',
  `profil` text NOT NULL,
  `lieuactivite` varchar(255) NOT NULL default '',
  `nomentreprise` varchar(255) NOT NULL default '',
  `adresse` text NOT NULL,
  `cp` varchar(10) NOT NULL default '',
  `ville` varchar(255) NOT NULL default '',
  `datedispo` int(10) unsigned NOT NULL default '0',
  `contact` varchar(255) NOT NULL default '',
  `email` varchar(200) NOT NULL default '',
  `telephone` varchar(40) NOT NULL default '',
  `typeposte` tinyint(1) NOT NULL default '0',
  `titreannonce` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `experience` varchar(255) NOT NULL default '',
  `statut` varchar(255) NOT NULL default '',
  `online` tinyint(1) NOT NULL default '0',
  `datesoumission` int(10) unsigned NOT NULL default '0',
  `datevalidation` int(10) unsigned NOT NULL default '0',
  `approver` mediumint(8) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  `ip` varchar(20) NOT NULL default '',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `attachedfile` varchar(255) NOT NULL default '',
  `pass` varchar(32) NOT NULL default '',
  `salary1` int(10) unsigned NOT NULL default '0',
  `salary2` int(10) unsigned NOT NULL default '0',
  `salarytype` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`offreid`),
  KEY `online` (`online`),
  KEY `datesoumission` (`datesoumission`)
) ENGINE=MyISAM;


CREATE TABLE `myjob_prolongation` (
  `prolongationid` int(10) unsigned NOT NULL auto_increment,
  `demandid` int(10) unsigned NOT NULL default '0',
  `offreid` int(10) unsigned NOT NULL default '0',
  `date` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`prolongationid`),
  KEY `date` (`date`)
) ENGINE=MyISAM;


CREATE TABLE `myjob_salarytype` (
  `salarytypeid` int(10) unsigned NOT NULL auto_increment,
  `libelle` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL,
  PRIMARY KEY  (`salarytypeid`),
  KEY `libelle` (`libelle`)
) ENGINE=MyISAM;


CREATE TABLE `myjob_secteuractivite` (
  `secteurid` int(10) unsigned NOT NULL auto_increment,
  `libelle` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL,
  PRIMARY KEY  (`secteurid`),
  KEY `libelle` (`libelle`)
) ENGINE=MyISAM;


CREATE TABLE `myjob_sitfam` (
  `sitfamid` int(10) unsigned NOT NULL auto_increment,
  `libelle` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL,
  PRIMARY KEY  (`sitfamid`),
  KEY `libelle` (`libelle`)
) ENGINE=MyISAM;


CREATE TABLE `myjob_typeposte` (
  `typeid` int(10) unsigned NOT NULL auto_increment,
  `libelle` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL,
  PRIMARY KEY  (`typeid`),
  KEY `libelle` (`libelle`)
) ENGINE=MyISAM;


CREATE TABLE `myjob_zonegeographique` (
  `zoneid` int(10) unsigned NOT NULL auto_increment,
  `libelle` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL,
  PRIMARY KEY  (`zoneid`),
  KEY `libelle` (`libelle`)
) ENGINE=MyISAM;
