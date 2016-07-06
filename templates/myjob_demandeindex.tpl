<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<div align="right"><a href="<{$xoops_url}>/modules/myjob/demandes-search.php" title="<{$smarty.const._MYJOB_DEMAND_SEARCH}>"><img src="<{$xoops_url}>/modules/myjob/assets/images/search.gif" border="0" alt="<{$smarty.const._MYJOB_DEMAND_SEARCH}>"/></a><{if $rssfeed_link}> <a
        href="<{$xoops_url}>/modules/myjob/backend.php" title="<{$smarty.const._MYJOB_RSSFEED}>"><img src="<{$xoops_url}>/modules/myjob/assets/images/rss.gif" border="0" alt="<{$smarty.const._MYJOB_RSSFEED}>"/></a> <a href="<{$xoops_url}>/modules/myjob/atom.php"
                                                                                                                                                                                                                          title="<{$smarty.const._MYJOB_ATOMFEED}>"><img
                src="<{$xoops_url}>/modules/myjob/assets/images/atom.gif" border="0" alt="<{$smarty.const._MYJOB_ATOMFEED}>"/></a><{/if}> <{if $caddy}><a href="<{$xoops_url}>/modules/myjob/demandes-caddy.php" title="<{$smarty.const._MYJOB_CADDY}>"><img
                src="<{$xoops_url}>/modules/myjob/assets/images/cart.gif" border="0" alt="<{$smarty.const._MYJOB_CADDY}>"/></a><{/if}>
</div>

<p align="center"><strong><{$welcome}></strong>
    <br><br><{$demandscount}>
    <{if $isadmin}>
        <br>
        <{$demandswaiting}>
    <{/if}>
    <br><br><a href="<{$xoops_url}>/modules/myjob/submit-demande.php"><{$smarty.const._MYJOB_DEMANDS_ADD}></a>
</p>

<script type="text/javascript">
    function changeme(qlayer, IdDemande)
    <
    {*
        qlayer = Nom
        du
        div
        sur
        lequel
        on
        travaille, IdDemande = Id
        de
        la
        demande
        choisie *
    }
    >
    {
        var pars = 'demandeid=' + IdDemande;
        var myAjax1 = new Ajax.Updater(qlayer, '<{$xoops_url}>/modules/myjob/demande-fly.php', {method: 'post', parameters: pars});
    }
</script>


<{if $pagenav !=''}>
    <div style="text-align: right; margin: 10px;"><{$pagenav}></div><{/if}>
<table border="0" width="95%">
    <tr>
        <th align="center"><{$smarty.const._MYJOB_OFFER_DATEVALID}></th>
        <th align="center"><{$smarty.const._MYJOB_DEMAND_ZONEGEOGRAPHIQUE}></th>
        <th align="center"><{$smarty.const._MYJOB_DEMAND_SECTEURACTIVITE}></th>
        <th align="center"><{$smarty.const._MYJOB_DEMAND_DESCRIPTION}></th>
        <th align="center"><{$smarty.const._MYJOB_DEMAND_EXPERIENCE}></th>
        <th align="center"><{$smarty.const._MYJOB_DEMAND_TYPEPOSTE}></th>
        <{if $caddy}>
            <th align="center"><{$smarty.const._MYJOB_CADDY}></th>
        <{/if}>
        <th align="center"><{$smarty.const._MYJOB_VIEW}></th>
    </tr>
    <{foreach item=onedemande from=$demandes}>
        <tr class="<{cycle values=" even,odd
    "}>">
            <td><{$onedemande.datevalidation|date_format:"%d/%m/%Y"}></td>
            <td><a href="<{$xoops_url}>/modules/myjob/demande-view.php?demandid=<{$onedemande.demandid}>"><{foreach item=onezone from=$onedemande.zonesidlibelle}><{$onezone}><br><{/foreach}></a></td>
            <td><{$onedemande.secteuridlibelle}></td>
            <td><a href="<{$xoops_url}>/modules/myjob/demande-view.php?demandid=<{$onedemande.demandid}>" <{$onedemande.tooltip}>><{$onedemande.titreannonce}></a></td>
            <td align="center"><{$onedemande.libelle_experience}></td>
            <td align="center"><{$typesoffres[$onedemande.typeposte]}></td>
            <{if $caddy}>
                <td align="center">
                    <div id='d<{$onedemande.demandid}>'>
                        <{if $onedemande.inCaddy}>
                        <img src="<{$xoops_url}>/modules/myjob/assets/images/cartdelete.gif" border="0" onclick="changeme('d<{$onedemande.demandid}>',<{$onedemande.demandid}>);" style="cursor: pointer;" alt="<{$smarty.const._MYJOB_CADDY_REMOVE}>"/></div>
                    <{else}>
                    <img src="<{$xoops_url}>/modules/myjob/assets/images/cartadd.gif" border="0" onclick="changeme('d<{$onedemande.demandid}>',<{$onedemande.demandid}>);" style="cursor: pointer;" alt="<{$smarty.const._MYJOB_CADDY_PUT}>"/></div>
                    <{/if}>
                </td>
            <{/if}>
            <td><a href="<{$xoops_url}>/modules/myjob/demande-view.php?demandid=<{$onedemande.demandid}>"><{$smarty.const._MYJOB_VIEW}></a></td>
        </tr>
    <{/foreach}>
</table>
<{if $pagenav !=''}>
    <div style="text-align: center; margin: 10px;"><{$pagenav}></div><{/if}>
<div align="right"><a href="<{$xoops_url}>/modules/myjob/demandes-search.php" title="<{$smarty.const._MYJOB_DEMAND_SEARCH}>"><img src="<{$xoops_url}>/modules/myjob/assets/images/search.gif" border="0" alt="<{$smarty.const._MYJOB_DEMAND_SEARCH}>"/></a><{if $rssfeed_link}> <a
        href="<{$xoops_url}>/modules/myjob/backend.php" title="<{$smarty.const._MYJOB_RSSFEED}>"><img src="<{$xoops_url}>/modules/myjob/assets/images/rss.gif" border="0" alt="<{$smarty.const._MYJOB_RSSFEED}>"/></a> <a href="<{$xoops_url}>/modules/myjob/atom.php"
                                                                                                                                                                                                                          title="<{$smarty.const._MYJOB_ATOMFEED}>"><img
                src="<{$xoops_url}>/modules/myjob/assets/images/atom.gif" border="0" alt="<{$smarty.const._MYJOB_ATOMFEED}>"/></a><{/if}> <{if $caddy}><a href="<{$xoops_url}>/modules/myjob/demandes-caddy.php" title="<{$smarty.const._MYJOB_CADDY}>"><img
                src="<{$xoops_url}>/modules/myjob/assets/images/cart.gif" border="0" alt="<{$smarty.const._MYJOB_CADDY}>"/></a><{/if}>
</div>
<br>
<script language="javaScript" type="text/javascript" src="<{$xoops_url}>/modules/myjob/js/wz_tooltip.js"></script>
<{include file="db:system_notification_select.tpl"}>
