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

function myjob_adminmenu($currentoption = 0, $breadcrumb = '')
{
	include_once XOOPS_ROOT_PATH.'/modules/myjob/include/functions.php';
	$useoffers = myjob_getmoduleoption('useoffers');
	$usedemands = myjob_getmoduleoption('usedemands');

	/* Nice buttons styles */
	echo "
    	<style type='text/css'>
    	#buttontop { float:left; width:100%; background: #e7e7e7; font-size:93%; line-height:normal; border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; margin: 0; }
    	#buttonbar { float:left; width:100%; background: #e7e7e7 url('" . XOOPS_URL . "/modules/myjob/images/bg.png') repeat-x left bottom; font-size:93%; line-height:normal; border-left: 1px solid black; border-right: 1px solid black; margin-bottom: 12px; }
    	#buttonbar ul { margin:0; margin-top: 15px; padding:10px 10px 0; list-style:none; }
		#buttonbar li { display:inline; margin:0; padding:0; }
		#buttonbar a { float:left; background:url('" . XOOPS_URL . "/modules/myjob/images/left_both.png') no-repeat left top; margin:0; padding:0 0 0 9px; border-bottom:1px solid #000; text-decoration:none; }
		#buttonbar a span { float:left; display:block; background:url('" . XOOPS_URL . "/modules/myjob/images/right_both.png') no-repeat right top; padding:5px 15px 4px 6px; font-weight:bold; color:#765; }
		/* Commented Backslash Hack hides rule from IE5-Mac \*/
		#buttonbar a span {float:none;}
		/* End IE5-Mac hack */
		#buttonbar a:hover span { color:#333; }
		#buttonbar #current a { background-position:0 -150px; border-width:0; }
		#buttonbar #current a span { background-position:100% -150px; padding-bottom:5px; color:#333; }
		#buttonbar a:hover { background-position:0% -150px; }
		#buttonbar a:hover span { background-position:100% -150px; }
		</style>
    ";
 	global $xoopsModule, $xoopsConfig;

	$tblColors = array_fill(0,12,'');
	if($currentoption >= 0) {
		$tblColors[$currentoption] = 'current';
	}

	if (file_exists(XOOPS_ROOT_PATH . '/modules/myjob/language/' . $xoopsConfig['language'] . '/modinfo.php')) {
		include_once XOOPS_ROOT_PATH. '/modules/myjob/language/' . $xoopsConfig['language'] . '/modinfo.php';
	} else {
		include_once XOOPS_ROOT_PATH . '/modules/myjob/language/english/modinfo.php';
	}

	echo "<div id='buttontop'>";
	echo "<table style=\"width: 100%; padding: 0; \" cellspacing=\"0\"><tr>";
	echo "<td style=\"width: 60%; font-size: 10px; text-align: left; color: #2F5376; padding: 0 6px; line-height: 18px;\"><a class=\"nobutton\" href=\"../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod=".$xoopsModule->getVar('mid')."\">" . _AM_MYJOB_GENERALSET . "</a> | <a href=\"../index.php\">" . _AM_MYJOB_GOTOMOD . "</a></td>";
	echo "<td style=\"width: 40%; font-size: 10px; text-align: right; color: #2F5376; padding: 0 6px; line-height: 18px;\"><b>" . $xoopsModule->name() . "  " . _AM_MYJOB_MODULEADMIN . "</b> " . $breadcrumb . "</td>";
	echo "</tr></table>";
	echo "</div>";

	echo "<div id='buttonbar'>";
	echo "<ul>";
	echo "<li id='" . $tblColors[0] . "'><a href=\"index.php\"\"><span>"._MI_MYJOB_INDEX ."</span></a></li>\n";
	if($useoffers) {
		echo "<li id='" . $tblColors[1] . "'><a href=\"index.php?op=viewoffers\"\"><span>"._MI_MYJOB_ADMMENU1 ."</span></a></li>\n";
	}
	if($usedemands) {
		echo "<li id='" . $tblColors[2] . "'><a href=\"index.php?op=viewdemands\"><span>" . _MI_MYJOB_ADMMENU2 . "</span></a></li>\n";
	}
	echo "<li id='" . $tblColors[3] . "'><a href=\"index.php?op=managesitfam\"><span>" . _MI_MYJOB_ADMMENU3 . "</span></a></li>\n";
	echo "<li id='" . $tblColors[4] . "'><a href=\"index.php?op=managetypeposte\"><span>" . _MI_MYJOB_ADMMENU4 . "</span></a></li>\n";
	echo "<li id='" . $tblColors[5] . "'><a href=\"index.php?op=geo\"><span>" . _MI_MYJOB_ADMMENU6 . "</span></a></li>\n";
	echo "<li id='" . $tblColors[6] . "'><a href=\"index.php?op=secteurs\"><span>" . _MI_MYJOB_ADMMENU7 . "</span></a></li>\n";
	echo "<li id='" . $tblColors[7] . "'><a href=\"index.php?op=salarytype\"><span>" . _MI_MYJOB_ADMMENU8 . "</span></a></li>\n";
	echo "<li id='" . $tblColors[8] . "'><a href=\"index.php?op=perms\"><span>" . _MI_MYJOB_ADMMENU5 . "</span></a></li>\n";
	echo "<li id='" . $tblColors[9] . "'><a href=\"index.php?op=purge\"><span>" . _MI_MYJOB_ADMMENU9 . "</span></a></li>\n";
	echo "<li id='" . $tblColors[10] . "'><a href=\"index.php?op=fields\"><span>" . _MI_MYJOB_ADMMENU10 . "</span></a></li>\n";
	echo "<li id='" . $tblColors[11] . "'><a href=\"index.php?op=experience\"><span>" . _MI_MYJOB_ADMMENU11 . "</span></a></li>\n";
	echo "</ul></div>";
	echo "<br /><br /><pre>&nbsp;</pre><pre>&nbsp;</pre>";
}



function myjob_collapsableBar($tablename = '', $iconname = '')
{

    ?>
	<script type="text/javascript"><!--
	function goto_URL(object)
	{
		window.location.href = object.options[object.selectedIndex].value;
	}

	function toggle(id)
	{
		if (document.getElementById) { obj = document.getElementById(id); }
		if (document.all) { obj = document.all[id]; }
		if (document.layers) { obj = document.layers[id]; }
		if (obj) {
			if (obj.style.display == "none") {
				obj.style.display = "";
			} else {
				obj.style.display = "none";
			}
		}
		return false;
	}

	var iconClose = new Image();
	iconClose.src = '../images/close12.gif';
	var iconOpen = new Image();
	iconOpen.src = '../images/open12.gif';

	function toggleIcon ( iconName )
	{
		if ( document.images[iconName].src == window.iconOpen.src ) {
			document.images[iconName].src = window.iconClose.src;
		} else if ( document.images[iconName].src == window.iconClose.src ) {
			document.images[iconName].src = window.iconOpen.src;
		}
		return;
	}

	//-->
	</script>
	<?php
	echo "<h4 style=\"color: #2F5376; margin: 6px 0 0 0; \"><a href='#' onClick=\"toggle('" . $tablename . "'); toggleIcon('" . $iconname . "');\">";
}

?>
