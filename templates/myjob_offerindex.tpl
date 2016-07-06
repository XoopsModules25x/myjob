<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<{if $rssfeed_link != ""}>
    <div align='right'><{$rssfeed_link}></div><{/if}>
<p align="center"><strong><{$welcome}></strong>
    <br><br><{$offerscount}>
    <{if $isadmin}>
        <br>
        <{$offerswaiting}>
    <{/if}>
    <br><br><a href='submit-offer.php'><{$smarty.const._MYJOB_OFFERS_ADD}></a>
</p>


<div style="text-align: right; margin: 10px;"><{$pagenav}></div>
<table border='0' width='95%'>
    <tr>
        <th><{$smarty.const._MYJOB_OFFER_ENTREPRISE}></th>
        <th><{$smarty.const._MYJOB_OFFER_DATESUBMIT}></th>
        <th><{$smarty.const._MYJOB_OFFER_SECTEUR}></th>
        <th><{$smarty.const._MYJOB_OFFER_TYPEPOSTE}></th>
        <th><{$smarty.const._MYJOB_OFFER_DESCRIPTION}></th>
    </tr>
    <{foreach item=oneoffre from=$offres}>
        <tr class="<{cycle values=" even,odd
    "}>" onclick="window.location='offer-view.php?offerid=<{$oneoffre.offreid}>'">
            <td><a href='offer-view.php?offerid=<{$oneoffre.offreid}>'><{$oneoffre.nomentreprise}></a></td>
            <td><{$oneoffre.datesoumission|date_format:"%d/%m/%Y"}></td>
            <td><{$oneoffre.secteuractivite}></td>
            <td align='center'><{$typesoffres[$oneoffre.typeposte]}></td>
            <td><a href='offer-view.php?offerid=<{$oneoffre.offreid}>'><{$oneoffre.description}></a></td>
        </tr>
    <{/foreach}>
</table>
<div style="text-align: right; margin: 10px;"><{$pagenav}></div>
<br>
<{include file='db:system_notification_select.tpl'}>
