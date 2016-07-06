<?xml version="1.0" encoding="<{$charset}>"?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title><{$channel_title}></title>
    <link href="<{$channel_link}>"/>
    <updated><{$channel_lastbuild}></updated>
    <author>
        <name><{$channel_webmaster}></name>
        <email><{$channel_editor}></email>
    </author>
    <id><{$channel_link}></id>
    <category term="<{$channel_category}>"/>
    <generator uri="http://www.herve-thouzard.com" version="<{$module_version}>">
        Herv?houzard
    </generator>
    <logo><{$image_url}></logo>
    <rights><{$rights}></rights>
    <subtitle><{$channel_desc}></subtitle>

    <{foreach item=item from=$items}>
        <entry>
            <id><{$item.guid}></id>
            <title><{$item.title}></title>
            <updated><{$item.pubdate}></updated>
            <content><{$item.description}></content>
            <link rel="alternate" href="<{$item.link}>"/>
            <published><{$item.pubdate}></published>
            <category term="<{$item.category}>"/>
        </entry>
    <{/foreach}>
</feed>
