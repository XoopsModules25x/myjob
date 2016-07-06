<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<h2><{$smarty.const._MYJOB_MANAGE_DEMAND}></h2>
<br>
<{if $op=='login'}>
    <form method='post' action='<{$xoops_url}>/modules/myjob/my-demande.php'>
        <table border="0">
            <{if $errormsg!=''}>
                <tr class="<{cycle values=" even,odd
        "}>">
                    <td colspan="2" align='center'><b><{$errormsg}></b></td>
                </tr>
            <{/if}>
            <tr class="<{cycle values=" even,odd
        "}>">
                <td><{$smarty.const._MYJOB_PASSWORD}></td>
                <td><input type="text" name="password" value=""></td>
            </tr>
            <tr class="<{cycle values=" even,odd
        "}>">
                <td colspan="2" align='center'><input type='hidden' name='demandid' value="<{$onedemande.demandid}>"><input type="hidden" name="op" value="verifypass"><input type="submit" name="go" value="<{$smarty.const._MYJOB_POST}>"></td>
            </tr>
        </table>
    </form>
    <br>
<{elseif $op=='view'}>
    <table border='0' width='95%'>
        <tr>
            <th><{$smarty.const._MYJOB_DEMAND_ZONEGEOGRAPHIQUE}></th>
            <th><{$smarty.const._MYJOB_DEMAND_SECTEURACTIVITE}></th>
            <th><{$smarty.const._MYJOB_DEMAND_EXPERIENCE}></th>
            <th><{$smarty.const._MYJOB_DEMAND_TYPEPOSTE}></th>
            <th><{$smarty.const._MYJOB_DEMAND_DESCRIPTION}></th>
            <th><{$smarty.const._MYJOB_ACTION}></th>
        </tr>
        <tr class="<{cycle values=" even,odd
    "}>">
            <td><a href='view_demande.php?demandid=<{$onedemande.demandid}>' target="_blank"><{$onedemande.zonesgeographiques_libelle}></a></td>
            <td><{$onedemande.secteurid_libelle}></td>
            <td><{$onedemande.experience}></td>
            <td align='center'><{$typesoffres[$onedemande.typeposte]}></td>
            <td><a href='view_demande.php?demandid=<{$onedemande.demandid}>'><{$onedemande.titreannonce}></a></td>
            <td><a title="<{$smarty.const._MYJOB_EDIT}>" href='<{$xoops_url}>/modules/myjob/submit-demande.php?op=edit&demandid=<{$onedemande.demandid}>'><img src='<{$xoops_url}>/modules/myjob/assets/images/edit.gif' alt="<{$smarty.const._MYJOB_EDIT}>"></a> <a title="<{$smarty.const._MYJOB_DELETE}>"
                                                                                                                                                                                                                                                                     href='<{$xoops_url}>/modules/myjob/my-demande.php?op=demanddelete&demandid=<{$onedemande.demandid}>'
                        <{$conf_del_link}>><img src='<{$xoops_url}>/modules/myjob/assets/images/delete.gif' alt="<{$smarty.const._MYJOB_DELETE}>"></a><{if $prolongation}> <a title="<{$smarty.const._MYJOB_PROLONGATION}>"
                                                                                                                                                                              href='<{$xoops_url}>/modules/myjob/my-demande.php?op=demandprolongate&demandid=<{$onedemande.demandid}>'><img
                            src='<{$xoops_url}>/modules/myjob/assets/images/prolongation.gif' alt="<{$smarty.const._MYJOB_PROLONGATION}>"></a><{/if}>
            </td>
        </tr>
    </table>
    <br>
<{/if}>
