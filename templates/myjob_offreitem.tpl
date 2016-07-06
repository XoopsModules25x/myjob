<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<table border='0' align='center' width='95%'>
    <tr class="<{cycle values=" even,odd
    "}>">
        <th colspan='2' align='center'><strong><{$smarty.const._MYJOB_OFFER_DETAILS}><{$oneoffre.offreid}></strong></th>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_ENTREPRISE}></td>
        <td><{$oneoffre.nomentreprise}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_SECTEUR}></td>
        <td><{$oneoffre.secteuractivite}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_PROFIL}></td>
        <td><{$oneoffre.profil}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_LIEU}></td>
        <td><{$oneoffre.lieuactivite}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_ADRESSE}></td>
        <td><{$oneoffre.adresse}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_CP}></td>
        <td><{$oneoffre.cp}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_VILLE}></td>
        <td><{$oneoffre.ville}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_DATEDISPO}></td>
        <td><{$oneoffre.datedispo|date_format:"%d/%m/%Y"}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_CONTACT}></td>
        <td><{$oneoffre.contact}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_EMAIL}></td>
        <td><{$oneoffre.email}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_TEL}></td>
        <td><{$oneoffre.telephone}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_TYPEPOSTE}></td>
        <td><{$typesoffres[$oneoffre.typeposte]}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_TITREANNONCE}></td>
        <td><{$oneoffre.titreannonce}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_DESCRIPTION}></td>
        <td><{$oneoffre.description}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_EXPERIENCE}></td>
        <td><{$oneoffre.experience}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_STATUT}></td>
        <td><{$oneoffre.statut}></td>
    </tr>
    <tr class="<{cycle values=" even,odd
    "}>">
        <td><{$smarty.const._MYJOB_OFFER_DATESUBMIT}></td>
        <td><{$oneoffre.datesoumission|date_format:"%d/%m/%Y"}></td>
    </tr>
</table>
<br>
<br>
<div align='right'><a target='_blank' rel='nofollow' href='print.php?offreid=<{$oneoffre.offreid}>' title="<{$smarty.const._MYJOB_PRINT}>"><img src="<{$xoops_url}>/modules/myjob/assets/images/print.gif"></a>&nbsp;<a href="mailto:" title="<{$smarty.const._MYJOB_EMAIL}>"><img
                src="<{$xoops_url}>/modules/myjob/assets/images/friend.gif"></a>
    <{if $isadmin}>
        &nbsp;
        <a href='admin/index.php?op=offeredit&offreid=<{$oneoffre.offreid}>' title="<{$smarty.const._MYJOB_EDIT}>"><img src="<{$xoops_url}>/modules/myjob/assets/images/edit.gif"></a>
        &nbsp;
        <a href='admin/index.php?op=offerdelete&offreid=<{$oneoffre.offreid}>' title="<{$smarty.const._MYJOB_DELETE}>"><img src="<{$xoops_url}>/modules/myjob/assets/images/delete.gif"></a>
    <{/if}>
</div>
<div align='center'><a href="javascript: history.back()"><img src="<{$xoops_url}>/modules/myjob/assets/images/back.gif">&nbsp;<{$smarty.const._MYJOB_BACK}></a></div>
<br>
<{include file='db:system_notification_select.tpl'}>
