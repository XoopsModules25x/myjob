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
defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

//$path = dirname(dirname(dirname(__DIR__)));
//include_once $path . '/mainfile.php';

$moduleDirName = basename(dirname(__DIR__));

$moduleHandler = xoops_getHandler('module');
$module        = $moduleHandler->getByDirname($moduleDirName);
$pathIcon32    = '../../' . $module->getInfo('sysicons32');
xoops_loadLanguage('modinfo', $module->dirname());

$xoopsModuleAdminPath = XOOPS_ROOT_PATH . '/' . $module->getInfo('dirmoduleadmin');
if (!file_exists($fileinc = $xoopsModuleAdminPath . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/' . 'main.php')) {
    $fileinc = $xoopsModuleAdminPath . '/language/english/main.php';
}
include_once $fileinc;

$adminmenu[] = array(
    'title' => _AM_MODULEADMIN_HOME,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png'
);

$adminmenu[] = array(
    'title' => _MI_MYJOB_INDEX,
    'link'  => 'admin/main.php',
    'icon'  => $pathIcon32 . '/manage.png'
);

$adminmenu[] = array(
    'title' => _MI_MYJOB_ADMMENU1,
    'link'  => 'admin/main.php?op=viewoffers',
    'icon'  => $pathIcon32 . '/face-smile.png'
);

$adminmenu[] = array(
    'title' => _MI_MYJOB_ADMMENU2,
    'link'  => 'admin/main.php?op=viewdemands.php',
    'icon'  => $pathIcon32 . '/alert.png'
);

$adminmenu[] = array(
    'title' => _MI_MYJOB_ADMMENU3,
    'link'  => 'admin/main.php?op=managesitfam',
    'icon'  => $pathIcon32 . '/users.png'
);

$adminmenu[] = array(
    'title' => _MI_MYJOB_ADMMENU4,
    'link'  => 'admin/main.php?op=managetypeposte',
    'icon'  => $pathIcon32 . '/type.png'
);

$adminmenu[] = array(
    'title' => _MI_MYJOB_ADMMENU6,
    'link'  => 'admin/main.php?op=geo',
    'icon'  => $pathIcon32 . '/languages.png'
);

$adminmenu[] = array(
    'title' => _MI_MYJOB_ADMMENU7,
    'link'  => 'admin/main.php?op=secteurs',
    'icon'  => $pathIcon32 . '/delivery.png'
);

$adminmenu[] = array(
    'title' => _MI_MYJOB_ADMMENU5,
    'link'  => 'admin/main.php?op=perms',
    'icon'  => $pathIcon32 . '/permissions.png'
);

$adminmenu[] = array(
    'title' => _MI_MYJOB_ADMMENU8,
    'link'  => 'admin/main.php?op=salarytype',
    'icon'  => $pathIcon32 . '/cash_stack.png'
);

$adminmenu[] = array(
    'title' => _MI_MYJOB_ADMMENU9,
    'link'  => 'admin/main.php?op=purge',
    'icon'  => $pathIcon32 . '/prune.png'
);

$adminmenu[] = array(
    'title' => _MI_MYJOB_ADMMENU10,
    'link'  => 'admin/main.php?op=fields',
    'icon'  => $pathIcon32 . '/insert_table_row.png'
);

$adminmenu[] = array(
    'title' => _MI_MYJOB_ADMMENU11,
    'link'  => 'admin/main.php?op=experience',
    'icon'  => $pathIcon32 . '/wizard.png'
);

$adminmenu[] = array(
    'title' => _AM_MODULEADMIN_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png'
);
