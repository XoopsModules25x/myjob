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

/**
 * Returns a module's option
 *
 * @param string $option    Nom de l'option dont on veut récupérer la valeur
 * @param string $repmodule Nom du module dont on veut récupérer l'option
 * @return mixed la valeur de l'option si elle est trouvée
 */
function myjob_getmoduleoption($option, $repmodule = 'myjob')
{
    global $xoopsModuleConfig, $xoopsModule;
    static $tbloptions = array();
    if (is_array($tbloptions) && array_key_exists($option, $tbloptions)) {
        return $tbloptions[$option];
    }

    $retval = false;
    if (isset($xoopsModuleConfig) && (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $repmodule && $xoopsModule->getVar('isactive'))) {
        if (isset($xoopsModuleConfig[$option])) {
            $retval = $xoopsModuleConfig[$option];
        }
    } else {
        $module_handler = xoops_getHandler('module');
        $module         = &$module_handler->getByDirname($repmodule);
        $config_handler = xoops_getHandler('config');
        if ($module) {
            $moduleConfig = &$config_handler->getConfigsByCat(0, $module->getVar('mid'));
            if (isset($moduleConfig[$option])) {
                $retval = $moduleConfig[$option];
            }
        }
    }
    $tbloptions[$option] = $retval;

    return $retval;
}

/**
 * Création d'un nom de fichier unique (nom de fichier crée sur le serveur suite à un téléchargement)
 *
 * @param string  $folder   Nom du dossier dans lequel le fichier sera stocké
 * @param string  $filename Nom du fichier d'origine (utilisé pour trouver l'extension du fichier)
 * @param boolean $trimname Indique si le nom de fichier doit être "court"
 * @return string Un nom de fichier unique pour le répertoire spécifié, avec l'extension de fichier nécessaire
 */
function myjob_createUploadName($folder, $filename, $trimname = false)
{
    $workingfolder = $folder;
    if (xoops_substr($workingfolder, strlen($workingfolder) - 1, 1) !== '/') {
        $workingfolder .= '/';
    }
    $ext  = basename($filename);
    $ext  = explode('.', $ext);
    $ext  = '.' . $ext[count($ext) - 1];
    $true = true;
    while ($true) {
        $ipbits = explode('.', $_SERVER['REMOTE_ADDR']);
        list($usec, $sec) = explode(' ', microtime());
        $usec = (integer)($usec * 65536);
        $sec  = ((integer)$sec) & 0xFFFF;

        if ($trimname) {
            $uid = sprintf('%06x%04x%04x', ($ipbits[0] << 24) | ($ipbits[1] << 16) | ($ipbits[2] << 8) | $ipbits[3], $sec, $usec);
        } else {
            $uid = sprintf('%08x-%04x-%04x', ($ipbits[0] << 24) | ($ipbits[1] << 16) | ($ipbits[2] << 8) | $ipbits[3], $sec, $usec);
        }
        if (!file_exists($workingfolder . $uid . $ext)) {
            $true = false;
        }
    }

    return $uid . $ext;
}

/**
 * Indique si une personne à la permission de ...
 *
 * @param string Nom de la permission à vérifier
 * @return boolean Indique si l'utilisateur courant à le droit appliqué à la permission passée en paramètre
 */
function myjob_MygetItemIds($permtype = 'demand_view')
{
    global $xoopsUser;
    static $tblperms = array();
    if (is_array($tblperms) && array_key_exists($permtype, $tblperms)) {
        return $tblperms[$permtype];
    }

    $module_handler      = xoops_getHandler('module');
    $myjobModule         = $module_handler->getByDirname('myjob');
    $groups              = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    $gperm_handler       = xoops_getHandler('groupperm');
    $topics              = $gperm_handler->getItemIds($permtype, $groups, $myjobModule->getVar('mid'));
    $tblperms[$permtype] = $topics;

    return $topics;
}

/**
 * Create (in a link) a javascript confirmation's box
 *
 * @package         Myjob
 * @author          Instant Zero http://www.instant-zero.com
 * @copyright   (c) Instant Zero http://www.instant-zero.com
 *
 * @param string  $msg  Le message à afficher
 * @param boolean $form Est-ce une confirmation pour un formulaire ?
 * @return string La "commande" javscript à insérer dans le lien
 */
function myjob_JavascriptLinkConfirm($msg, $form = false)
{
    if (!$form) {
        return "onclick=\"javascript:return confirm('" . str_replace("'", ' ', $msg) . "')\"";
    } else {
        return "onSubmit=\"javascript:return confirm('" . str_replace("'", ' ', $msg) . "')\"";
    }
}

/**
 * Création automatique des meta keywords
 *
 * @param string $content Le contenu à partir duquel il faut générer les mots clés
 * @return string Les mots clés séparés par des virgules
 */
function myjob_createmeta_keywords($content)
{
    $tmp = array();
    // Search for the "Minimum keyword length"
    if (isset($_SESSION['myjob_keywords_limit'])) {
        $limit = $_SESSION['myjob_keywords_limit'];
    } else {
        $config_handler                   = xoops_getHandler('config');
        $xoopsConfigSearch                =& $config_handler->getConfigsByCat(XOOPS_CONF_SEARCH);
        $limit                            = $xoopsConfigSearch['keyword_min'];
        $_SESSION['myjob_keywords_limit'] = $limit;
    }
    $myts            = MyTextSanitizer::getInstance();
    $content         = str_replace('<br>', ' ', $content);
    $content         = $myts->undoHtmlSpecialChars($content);
    $content         = strip_tags($content);
    $content         = strtolower($content);
    $search_pattern  = array('&nbsp;', "\t", "\r\n", "\r", "\n", ',', '.', "'", ';', ':', ')', '(', '"', '?', '!', '{', '}', '[', ']', '<', '>', '/', '+', '-', '_', '\\', '*');
    $replace_pattern = array(' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
    $content         = str_replace($search_pattern, $replace_pattern, $content);
    $keywords        = explode(' ', $content);

    switch (myjob_getmoduleoption('metagen_order')) {
        case 0:    // Ordre d'apparition dans le texte
            $keywords = array_unique($keywords);
            break;
        case 1:    // Ordre de fréquence des mots
            $keywords = array_count_values($keywords);
            asort($keywords);
            $keywords = array_keys($keywords);
            break;
        case 2:    // Ordre inverse de la fréquence des mots
            $keywords = array_count_values($keywords);
            arsort($keywords);
            $keywords = array_keys($keywords);
            break;
    }
    // remove black listed words
    $tmp_blacklist = myjob_getmoduleoption('metagen_blacklist');
    $tbl_blacklist = explode(',', $tmp_blacklist);
    array_walk($tbl_blacklist, 'trim');

    $keywords = array_diff($keywords, $tbl_blacklist);

    foreach ($keywords as $keyword) {
        if (strlen($keyword) >= $limit && !is_numeric($keyword)) {
            $tmp[] = $keyword;
        }
    }
    $tmp = array_slice($tmp, 0, myjob_getmoduleoption('metagen_maxwords'));
    if (count($tmp) > 0) {
        return implode(',', $tmp);
    } else {
        if (!isset($config_handler) || !is_object($config_handler)) {
            $config_handler = xoops_getHandler('config');
        }
        $xoopsConfigMetaFooter = &$config_handler->getConfigsByCat(XOOPS_CONF_METAFOOTER);

        return $xoopsConfigMetaFooter['meta_keywords'];
    }
}

/**
 * Suppression du cache module et du cache des blocs
 */
function myjob_updateCache()
{
    global $xoopsModule;
    $folder  = $xoopsModule->getVar('dirname');
    $tpllist = array();
    include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
    include_once XOOPS_ROOT_PATH . '/class/template.php';
    $tplfile_handler = xoops_getHandler('tplfile');
    $tpllist         = $tplfile_handler->find(null, null, null, $folder);
    $xoopsTpl        = new XoopsTpl();
    xoops_template_clear_module_cache($xoopsModule->getVar('mid'));            // Clear module's blocks cache

    // Remove cache for each page.
    foreach ($tpllist as $onetemplate) {
        if ($onetemplate->getVar('tpl_type') === 'module') {
            $files_del = array();
            $files_del = glob(XOOPS_CACHE_PATH . '/*' . $onetemplate->getVar('tpl_file') . '*');
            if (count($files_del) > 0) {
                foreach ($files_del as $one_file) {
                    unlink($one_file);
                }
            }
        }
    }
}

/**
 * Création d'une bulle d'aide pour les liens (destiné à être dans la propriété "title" du lien)
 *
 * @param string $text Le texte à utiliser pour créer l'infobulle
 * @return string le texte à inclure dans l'info bulle
 */
function myjob_make_infotips($text)
{
    $infotips = myjob_getmoduleoption('infotips');
    if ($infotips > 0) {
        $myts = MyTextSanitizer::getInstance();

        return $myts->htmlSpecialChars(xoops_substr(strip_tags($text), 0, $infotips));
    }
}

/**
 * Renvoie la liste des champs d'une table
 *
 * @param string $table Le nom de la table (Sql) dont on veut récupérer la liste des champs
 * @return array La liste des champs de la table
 */
function myjob_get_fields_from_table($table)
{
    static $tblfields = array();
    if (is_array($tblfields) && array_key_exists($table, $tblfields)) {
        return $tblfields[$table];
    }

    global $xoopsDB;
    $ret    = array();
    $result = $xoopsDB->queryF('SHOW COLUMNS FROM ' . $xoopsDB->prefix($table));
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $ret[$myrow['Field']] = $myrow['Field'];
    }
    $tblfields[$table] = $ret;

    return $ret;
}

/**
 * Vérifie si un champ est obligatoire en saisie ou s'il est visible de tous (pour les demandes et les offres)
 *
 * @param string  $fieldname Nom du champ dont on veut avoir l'information
 * @param boolean $mandatory True pour vérifier si le champ est obligatoire, False pour vérifier si le champ est visible publiquement
 * @param string  $type      'demande' ou 'offer'
 * @return boolean indique soit si le champ est visible soit s'il est obligatoire en saisie
 */
function myjob_fields($fieldname, $mandatory = true, $type = 'demande')
{
    // TODO: Rajouter du cache
    $field_name = XOOPS_ROOT_PATH . '/uploads/myjob_fields.txt';    // Nom du fichier contenant les champs obligatoires et les champs publics
    $fields     = array('', '', '', '');
    $content    = '';
    if (file_exists($field_name)) {
        $content = file_get_contents($field_name);
    }
    $fields          = explode('[end]', $content);
    $fields[0]       = isset($fields[0]) ? trim($fields[0]) : '';    // Demandes - Visibles
    $fields[1]       = isset($fields[1]) ? trim($fields[1]) : '';    // Demandes - Obligatoires
    $fields[2]       = isset($fields[2]) ? trim($fields[2]) : '';    // Offres - Visibles
    $fields[3]       = isset($fields[3]) ? trim($fields[3]) : '';    // Offres - Obligatoires
    $demandvisibles  = explode("\r\n", $fields[0]);
    $demandmandatory = explode("\r\n", $fields[1]);
    $offervisibles   = explode("\r\n", $fields[2]);
    $offermandatory  = explode("\r\n", $fields[3]);

    if ($type === 'demande') {
        if (!$mandatory) {
            if (myjob_MygetItemIds()) {    // Si la personne fait partie du groupe autorisé à tout voir

                return true;
            } else {
                return in_array($fieldname, $demandvisibles);
            }
        } else {
            return in_array($fieldname, $demandmandatory);
        }
    } else {
        if (!$mandatory) {
            return in_array($fieldname, $offervisibles);
        } else {
            return in_array($fieldname, $offermandatory);
        }
    }
}

/**
 * Permet de créer une bulle d'aide en dhtml
 *
 * @param string $text Le texte à mettre en tooltip
 * @return string La commande html à utiliser pour avoir le tooltip
 */
function myjob_make_dhtml_tooltip($text)
{
    if (myjob_getmoduleoption('infotips') > 0) {
        $text = str_replace('<br>', "\n", $text);
        $text = str_replace("'", "\\'", $text);
        $text = htmlentities($text);
        $text = xoops_substr($text, 0, myjob_getmoduleoption('infotips'));
        $text = str_replace("\n", '<br>', $text);

        return " onmouseover=\"return escape('" . $text . "')\"";
    }
}

/**
 * Fonction interne utilisée pour les PDF
 * @param $document
 * @return mixed
 */
function myjob_html2text($document)
{
    // PHP Manual:: function preg_replace
    // $document should contain an HTML document.
    // This will remove HTML tags, javascript sections
    // and white space. It will also convert some
    // common HTML entities to their text equivalent.

    $search = array(
        "'<script[^>]*?>.*?</script>'si",  // Strip out javascript
        "'<[\/\!]*?[^<>]*?>'si",          // Strip out HTML tags
        "'([\r\n])[\s]+'",                // Strip out white space
        "'&(quot|#34);'i",                // Replace HTML entities
        "'&(amp|#38);'i",
        "'&(lt|#60);'i",
        "'&(gt|#62);'i",
        "'&(nbsp|#160);'i",
        "'&(iexcl|#161);'i",
        "'&(cent|#162);'i",
        "'&(pound|#163);'i",
        "'&(copy|#169);'i",
        "'&#(\d+);'e"
    );                    // evaluate as php

    $replace = array(
        '',
        '',
        "\\1",
        "\"",
        '&',
        '<',
        '>',
        ' ',
        chr(161),
        chr(162),
        chr(163),
        chr(169),
        "chr(\\1)"
    );

    $text = preg_replace($search, $replace, $document);

    return $text;
}

/**
 * Fonction chargée de renvoyer l'adresse IP du visiteur courant
 *
 * @package         Myjob
 * @author          Instant Zero http://www.instant-zero.com
 * @copyright   (c) Instant Zero http://www.instant-zero.com
 *
 * @return string L'adresse IP (format Ipv4)
 */
function myjob_IP()
{
    $proxy_ip = '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
        $proxy_ip = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
        $proxy_ip = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
        $proxy_ip = $_SERVER['HTTP_FORWARDED'];
    } elseif (!empty($_SERVER['HTTP_VIA'])) {
        $proxy_ip = $_SERVER['HTTP_VIA'];
    } elseif (!empty($_SERVER['HTTP_X_COMING_FROM'])) {
        $proxy_ip = $_SERVER['HTTP_X_COMING_FROM'];
    } elseif (!empty($_SERVER['HTTP_COMING_FROM'])) {
        $proxy_ip = $_SERVER['HTTP_COMING_FROM'];
    }
    $regs = array();
    if (!empty($proxy_ip) && $is_ip = preg_match('/^([0-9]{1,3}\.){3,3}[0-9]{1,3}/', $proxy_ip, $regs) && count($regs) > 0) {
        $the_IP = $regs[0];
    } else {
        $the_IP = $_SERVER['REMOTE_ADDR'];
    }

    return $the_IP;
}

/**
 * Envoi d'un email à partir d'un template à un groupe de personnes
 *
 * @package         Myjob
 * @author          Instant Zero http://www.instant-zero.com
 * @copyright   (c) Instant Zero http://www.instant-zero.com
 *
 * @param string $tpl_name   Nom du template à utiliser
 * @param array  $recipients Liste des destinataires
 * @param string $subject    Sujet du mail
 * @param array  $variables  Variables à passer au template
 * @param string $FromName	Adresse email de l'expéditeur
 * @param string $FromName   Nom de l'expéditeur
 * @return bool Le résultat de l'envoi du mail
 */
function myjob_send_email_from_tpl($tpl_name, $recipients, $subject, $variables, $FromEmail, $FromName)
{
    global $xoopsConfig;
    include_once XOOPS_ROOT_PATH . '/class/xoopsmailer.php';
    if (function_exists('xoops_getMailer')) {
        $xoopsMailer = xoops_getMailer();
    } else {
        $xoopsMailer =& getMailer();
    }
    $xoopsMailer->useMail();
    $xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH . '/modules/myjob/language/' . $xoopsConfig['language'] . '/mail_template');
    $xoopsMailer->setTemplate($tpl_name);
    $xoopsMailer->setToEmails($recipients);
    $xoopsMailer->setFromEmail($FromEmail);
    $xoopsMailer->setFromName($FromName);
    $xoopsMailer->setSubject($subject);
    foreach ($variables as $key => $value) {
        $xoopsMailer->assign($key, $value);
    }
    // On conserve une copie des messages
    $fp = @fopen(XOOPS_UPLOAD_PATH . '/logmail_myjob.txt', 'a');
    if ($fp) {
        fwrite($fp, str_repeat('-', 120) . "\n");
        fwrite($fp, date('d/m/Y H:i:s') . "\n");
        fwrite($fp, 'Nom du template : ' . $tpl_name . "\n");
        fwrite($fp, 'Sujet du mail : ' . $subject . "\n");
        fwrite($fp, 'Destinaire(s) du mail : ' . implode(',', $recipients) . "\n");
        fwrite($fp, 'Variables transmises : ' . implode(',', $variables) . "\n");
        fclose($fp);
    }
    $res = $xoopsMailer->send();
    unset($xoopsMailer);

    return $res;
}
