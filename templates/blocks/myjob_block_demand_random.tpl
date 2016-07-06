<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<table>
    <tr>
        <td>
            <ul>
                <{foreach item=demand from=$block.demands}>
                    <li>
                        <{if $block.sort=="1"}>
                            [<{$demand.datesoumission|date_format:"%d/%m/%Y"}>]
                        <{elseif $block.sort=="2"}>
                            [<{$demand.hits}>]
                        <{/if}>
                        <{$demand.topic_title}> - <a href="<{$xoops_url}>/modules/myjob/view_demande.php?demandid=<{$demand.demandid}>" <{$demand.infotip}>><{$demand.title}></a> <br><{$demand.teaser}>
                    </li>
                <{/foreach}>
            </ul>
        </td>
    </tr>
</table>
