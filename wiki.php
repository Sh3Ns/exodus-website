<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>
<?php
$wikiTab = isset($_GET['tab']) && $_GET['tab'] === 'expansions' ? 'expansions' : 'general';
?>
<link rel="stylesheet" href="template/css/rpg-awesome.min.css">
<style>
#wikiPageShell {
    min-height: 2200px;
    position: relative;
}

#wikiPageContent {
    visibility: hidden;
    opacity: 0;
    transition: opacity .25s ease;
}

#wikiPageContent.is-ready {
    visibility: visible;
    opacity: 1;
}
</style>
<style>
.wiki-page {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.wiki-top-tabs {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 6px;
    background: rgba(93, 72, 39, 0.10);
    border: 1px solid var(--box-inner-border);
    border-radius: 10px;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.35);
}

.wiki-top-tab,
.wiki-nav-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 34px;
    padding: 8px 14px;
    border: 1px solid #c9b894;
    border-radius: 8px;
    background: linear-gradient(180deg, #efe4cb 0%, #e4d5b3 100%);
    color: #6a5539;
    text-decoration: none;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s ease;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.45);
}

.wiki-nav-btn {
    justify-content: flex-start;
    text-align: left;
}

.wiki-top-tab:hover,
.wiki-nav-btn:hover {
    background: linear-gradient(180deg, #f4ead5 0%, #eadcbe 100%);
    border-color: #b79d67;
    color: #4f3f2b;
}

.wiki-top-tab.is-active,
.wiki-nav-btn.is-active,
.cls.is-active,
.cls[data-open-tab-active="true"] {
    background: linear-gradient(180deg, #cfa949 0%, #bb9132 100%);
    border-color: #a97f24;
    color: #fff8e8;
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,0.25),
        0 4px 10px rgba(0,0,0,0.18);
}

.wiki-panel,
.wiki-entry,
.tabbox[data-tab-group="classes"] {
    display: none;
}

.wiki-panel.is-active,
.wiki-entry.is-active,
.tabbox[data-tab-group="classes"].is-active {
    display: block;
}

.wiki-layout,
.wiki-sidebar,
.wiki-content-wrap {
    display: block;
}

.wiki-sidebar-wrap,
.wiki-content-wrap {
    background: transparent;
    border: 0;
}

.wiki-sidebar-wrap {
    margin-bottom: 14px;
}

.wiki-content-wrap,
.text-cells,
.text-table,
.text-columns {
    overflow: visible !important;
}

.wiki-sidebar-header,
.wiki-nav-subtitle {
    display: none;
}

.wiki-sidebar-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.wiki-sidebar-list li {
    margin: 0;
    padding: 0;
}

.wiki-nav-icon {
    width: 18px;
    height: 18px;
    min-width: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: currentColor;
}

.wiki-nav-icon i {
    font-size: 15px !important;
    color: currentColor !important;
}

.wiki-nav-main {
    min-width: 0;
    display: inline-flex;
    align-items: center;
}

.wiki-nav-title {
    font-size: 12px;
    font-weight: 700;
    color: inherit;
    line-height: 1;
}

.wiki-entry {
    color: var(--text, #5c4a32);
}

.wiki-entry h1,
.wiki-entry h2,
.wiki-entry h3,
.wiki-entry h4 {
    color: #5c4a32;
    margin-top: 0;
}

.wiki-entry a {
    color: var(--text-link);
}

.wiki-entry p,
.wiki-entry li,
.wiki-entry div,
.wiki-entry span,
.text-cells__item,
.text-cells__item span,
.text-cells__item div,
.text-cells__item p {
    overflow-wrap: break-word !important;
    word-break: normal !important;
    white-space: normal !important;
}

.wiki-entry img,
.wiki-entry video,
.wiki-entry iframe {
    max-width: 100%;
    height: auto;
}

.wiki-entry video {
    display: block;
    width: 100%;
    border-radius: 10px;
    border: 1px solid rgba(107, 82, 46, 0.18);
    background: #000;
}

.wiki-entry > .text-box,
.wiki-entry > .text-table,
.wiki-entry > .video-container,
.wiki-entry > .desc-text,
.wiki-entry > ul.text-list,
.wiki-entry > h3,
.wiki-entry > center,
.wiki-entry > p {
    margin-bottom: 14px;
}

.wiki-entry .text-box,
.wiki-entry .text-table,
.wiki-entry .video-container,
.wiki-entry .desc-text,
.wiki-entry .desc-text__text,
.wiki-entry ul.text-list,
.wiki-entry .wiki-surface,
.wiki-entry .text-cells,
.wiki-entry table {
    background: linear-gradient(180deg, rgba(255,255,255,0.18) 0%, rgba(0,0,0,0.015) 100%), var(--box-inner-bg, #d8c8a8);
    border: 1px solid var(--box-inner-border, #b8a47c);
    border-radius: 8px;
    box-shadow: var(--box-inner-shadow, inset 0 1px 0 rgba(255,255,255,0.25));
}

.wiki-entry .text-box,
.wiki-entry .text-table,
.wiki-entry .desc-text,
.wiki-entry ul.text-list {
    padding: 14px;
}

.wiki-entry .desc-text__text {
    padding: 0;
    background: transparent;
    border: 0;
    box-shadow: none;
}

.wiki-entry ul.text-list {
    list-style: none;
    margin: 0;
    padding-left: 14px;
}

.wiki-entry ul.text-list > li {
    position: relative;
    padding-left: 18px;
    margin: 8px 0;
}

.wiki-entry ul.text-list > li > span:first-child {
    position: absolute;
    left: 0;
    top: .4rem;
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #c2922e;
    box-shadow: 0 0 0 2px rgba(194,146,46,0.18);
}

.wiki-entry .info__line,
.wiki-entry .desc-sep {
    height: 1px;
    margin: 14px 0;
    background: linear-gradient(90deg, transparent 0%, rgba(111,88,52,0.18) 10%, rgba(111,88,52,0.35) 50%, rgba(111,88,52,0.18) 90%, transparent 100%);
}

.wiki-entry .yellowcolor,
.wiki-entry .tibiayellow {
    color: #9a6d00 !important;
    font-weight: 700;
}

.wiki-entry .tibiatext {
    color: #f7efc8;
    font-family: Verdana, Tahoma, sans-serif;
    font-size: 9pt;
    font-weight: bold;
    text-shadow:
        0.1em 0.1em 0.08em #000,
        -0.1em -0.1em 0.08em #000,
        0.1em -0.1em 0.08em #000,
        -0.1em 0.1em 0.08em #000,
        -0.1em 0 0.08em #000,
        0.1em 0 0.08em #000;
}

.wiki-entry .text-columns {
    display: grid;
    gap: 12px;
    padding-left: 0;
    list-style: none;
    margin: 0;
}

.wiki-entry .text-columns[style*="repeat(4, 1fr)"] {
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
}

.wiki-entry .text-columns[style*="repeat(3, 1fr)"] {
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
}

.wiki-entry .text-columns[style*="repeat(2, 1fr)"] {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
}

.wiki-entry .text-columns[style*="300px 1fr 100px"],
.wiki-entry .text-columns[style*="300px 1fr"] {
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
}

.wiki-entry .text-cells {
    min-width: 0;
    margin: 0;
    padding: 12px;
    color: var(--text, #5c4a32) !important;
}

.wiki-entry .text-cells * {
    color: inherit !important;
}

.wiki-entry .text-cells__head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    padding-bottom: 8px;
    font-weight: 700;
    color: #5c4a32;
    border-bottom: 1px solid rgba(107, 82, 46, 0.15);
}

.wiki-entry .text-cells__item {
    min-height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6px 10px;
    margin: 0;
    border-bottom: 1px solid rgba(120, 95, 55, 0.12);
    box-sizing: border-box;
}

.wiki-entry .text-cells__item:last-child {
    border-bottom: 0;
}

.text-cells:first-child .text-cells__item {
    justify-content: flex-start;
}

.text-cells__item b,
.text-cells__item strong {
    white-space: nowrap;
}

.wiki-entry .text-cells__item img,
.wiki-entry .text-cells__head img,
.wiki-entry .aboutitem,
.wiki-entry .icon-h,
.wiki-entry .item__icon {
    max-width: none;
}

.text-cells__item img {
    width: 32px;
    height: 32px;
    object-fit: contain;
    display: block;
}

.wiki-entry .text-cells__item img:not(.icon-h):not(.aboutitem),
.wiki-entry .text-cells__head img:not(.icon-h):not(.aboutitem),
.wiki-entry .aboutitem {
    border-radius: 8px;
}

.wiki-entry center img,
.wiki-entry center video,
.wiki-entry .video-container,
.wiki-entry .img-responsive {
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.wiki-entry .video-container {
    padding: 10px;
}

.wiki-entry .item {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 10px 0;
    border-bottom: 1px solid rgba(107, 82, 46, 0.10);
}

.wiki-entry .item:last-child {
    border-bottom: 0;
}

.wiki-entry .item__icon {
    width: 46px;
    height: 46px;
    object-fit: contain;
    flex-shrink: 0;
}

.wiki-entry .item__title,
.wiki-entry .item__label,
.wiki-entry .right {
    font-weight: 700;
    color: #5c4a32;
}

.wiki-entry .right {
    display: inline-block;
    margin-right: 6px;
}

.wiki-entry table {
    width: 100%;
    border-collapse: collapse;
    overflow: hidden;
}

.wiki-entry th,
.wiki-entry td {
    padding: 8px 10px;
    border: 1px solid rgba(107, 82, 46, 0.14);
}

.wiki-tip-trigger,
.text-cells__item[data-tip-def],
.aboutitem[data-tip-def],
img[data-tip-def] {
    position: relative;
    cursor: help;
}

.wiki-tip-trigger {
    display: inline-block;
    margin-left: 4px;
    color: #b88b2f !important;
    font-weight: 700;
}

.wiki-tip-trigger::after,
.text-cells__item[data-tip-def]::after,
.aboutitem[data-tip-def]::after,
img[data-tip-def]::after {
    content: attr(data-tip-def);
    position: absolute;
    left: 50%;
    bottom: calc(100% + 10px);
    transform: translateX(-50%);
    min-width: 120px;
    max-width: 320px;
    padding: 10px 12px;
    border-radius: 10px;
    background: linear-gradient(to bottom, #d8ccb4, #cfc0a4);
    border: 1px solid #b9a98a;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    color: #5a4632;
    font-size: 12px;
    line-height: 1.4;
    text-align: center;
    white-space: normal;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: all 0.18s ease;
    z-index: 999999;
}

.wiki-tip-trigger::before,
.text-cells__item[data-tip-def]::before,
.aboutitem[data-tip-def]::before,
img[data-tip-def]::before {
    content: '';
    position: absolute;
    left: 50%;
    bottom: calc(100% + 3px);
    transform: translateX(-50%);
    border-width: 7px;
    border-style: solid;
    border-color: #cfc0a4 transparent transparent transparent;
    opacity: 0;
    visibility: hidden;
    transition: all 0.18s ease;
    z-index: 1000000;
}

.wiki-tip-trigger:hover::after,
.wiki-tip-trigger:hover::before,
.text-cells__item[data-tip-def]:hover::after,
.text-cells__item[data-tip-def]:hover::before,
.aboutitem[data-tip-def]:hover::after,
.aboutitem[data-tip-def]:hover::before,
img[data-tip-def]:hover::after,
img[data-tip-def]:hover::before {
    opacity: 1;
    visibility: visible;
}

.wiki-resource-card > img,
.text-cells > img {
    object-fit: contain;
    image-rendering: pixelated;
    align-item: center;
    display: block;
    margin: 12px auto;
}

.wiki-resource-card,
.text-cells {
    text-align: center;
}

.wiki-resource-card img,
.text-cells img {
    display: block;
    margin-left: auto !important;
    margin-right: auto !important;
}

.wiki-html-tooltip {
    position: fixed;
    transform: translate(-50%, -100%);
    background: linear-gradient(to bottom, #d8ccb4, #cfc0a4);
    border: 1px solid #b9a98a;
    border-radius: 10px;
    padding: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    z-index: 99999;
    color: #5a4632;
    font-size: 12px;
    text-align: center;
    pointer-events: none;
    min-width: 80px;
}

.wiki-html-tooltip img {
    display: block;
    margin: 0 auto 6px auto;
    width: 48px;
    height: 48px;
    object-fit: contain;
}

.btns {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 18px;
}

.cls {
    width: 170px;
    min-height: 180px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px;
    border-radius: 12px;
    border: 1px solid #c9b894;
    background: linear-gradient(180deg, #efe4cb 0%, #dfcfaa 100%);
    cursor: pointer;
    transition: all .18s ease;
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,0.45),
        0 2px 6px rgba(0,0,0,0.08);
}

.cls:hover {
    transform: translateY(-2px);
    border-color: #b78f3d;
    background: linear-gradient(180deg, #f4ead5 0%, #e6d5b3 100%);
}

.cls__pic {
    width: 96px;
    height: 96px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(0,0,0,0.08);
    overflow: hidden;
}

.cls__img {
    max-width: 88px;
    max-height: 88px;
    object-fit: contain;
    display: block;
}

.cls__name {
    text-align: center;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.2;
}

@media (max-width: 1100px) {
    .wiki-entry .text-columns[style*="repeat(4, 1fr)"] {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 980px) {
    .wiki-top-tabs,
    .wiki-sidebar-list {
        width: 100%;
    }

    .wiki-sidebar-list li {
        flex: 1 1 calc(50% - 6px);
    }

    .wiki-nav-btn {
        width: 100%;
        justify-content: center;
    }

    .wiki-entry .text-columns[style*="repeat(3, 1fr)"],
    .wiki-entry .text-columns[style*="repeat(2, 1fr)"],
    .wiki-entry .text-columns[style*="300px 1fr 100px"],
    .wiki-entry .text-columns[style*="300px 1fr"] {
        grid-template-columns: 1fr !important;
    }

    .cls {
        width: calc(50% - 6px);
        min-width: 140px;
    }
}

@media (max-width: 640px) {
    .wiki-top-tab {
        flex: 1 1 calc(50% - 6px);
    }

    .wiki-sidebar-list li,
    .cls {
        flex: 1 1 100%;
        width: 100%;
    }

    .wiki-nav-btn {
        justify-content: flex-start;
    }

    .wiki-entry .text-box,
    .wiki-entry .text-table,
    .wiki-entry .desc-text,
    .wiki-entry ul.text-list {
        padding: 12px;
    }

    .wiki-entry .text-columns[style*="repeat(4, 1fr)"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
<div id="wikiPageShell">
    <div id="wikiPageContent">
<div class="">
    <div class="PageHeader-module__HZea0q__header ">
        <div class="PageHeader-module__HZea0q__headerContent">
            <h2 class="PageHeader-module__HZea0q__title">Wiki</h2>
        </div>
    </div>
    <div class="PageBody-module__OAJIWq__body ">
        <div class="PageBody-module__OAJIWq__content">
            <div class="wiki-page">
                <div class="wiki-top-tabs">
                    <a href="?tab=general" class="wiki-top-tab <?php echo $wikiTab === 'general' ? 'is-active' : ''; ?>">General Systems</a>
                    <a href="?tab=expansions" class="wiki-top-tab <?php echo $wikiTab === 'expansions' ? 'is-active' : ''; ?>">Expansions</a>
                </div>

<div class="wiki-panel <?php echo $wikiTab === 'general' ? 'is-active' : ''; ?>" id="panel-general">
<div class="wiki-layout">
<div class="wiki-sidebar-wrap is-open" data-wiki-sidebar>
<div class="wiki-sidebar-header"><span class="wiki-sidebar-title">General Systems</span><button type="button" class="wiki-collapse-btn" data-wiki-collapse>☰</button></div>
<div class="wiki-sidebar"><ul class="wiki-sidebar-list">
<li><button type="button" class="wiki-nav-btn is-active" data-wiki-target="general-jobs">
<span class="wiki-nav-icon"><i class="ra ra-mining-diamonds" style="color:white;font-size:32px;"></i></span>
<span class="wiki-nav-main"><span class="wiki-nav-title">Jobs</span><span class="wiki-nav-subtitle">Professions</span></span>
</button></li>
<li><button type="button" class="wiki-nav-btn" data-wiki-target="general-ancestral">
<span class="wiki-nav-icon"><i class="ra ra-burning-book" style="color:white;font-size:32px;"></i></span>
<span class="wiki-nav-main"><span class="wiki-nav-title">Ancestral System</span><span class="wiki-nav-subtitle">Progress details</span></span>
</button></li>
<li><button type="button" class="wiki-nav-btn" data-wiki-target="general-dungeons">
<span class="wiki-nav-icon"><i class="ra ra-monster-skull" style="color:white;font-size:32px;"></i></span>
<span class="wiki-nav-main"><span class="wiki-nav-title">Dungeons</span><span class="wiki-nav-subtitle">About Dungeons</span></span>
</button></li>
<li><button type="button" class="wiki-nav-btn" data-wiki-target="general-azure">
<span class="wiki-nav-icon"><i class="ra ra-key" style="color:white;font-size:32px;"></i></span>
<span class="wiki-nav-main"><span class="wiki-nav-title">Azure Portals</span><span class="wiki-nav-subtitle">Random Portals</span></span>
</button></li>
<li><button type="button" class="wiki-nav-btn" data-wiki-target="general-tier">
<span class="wiki-nav-icon"><i class="ra ra-bottle-vapors" style="color:white;font-size:32px;"></i></span>
<span class="wiki-nav-main"><span class="wiki-nav-title">Tier System</span><span class="wiki-nav-subtitle">Inlay &amp; Extract</span></span>
</button></li>
<li><button type="button" class="wiki-nav-btn" data-wiki-target="general-badges">
<span class="wiki-nav-icon"><i class="ra ra-frostfire" style="color:white;font-size:32px;"></i></span>
<span class="wiki-nav-main"><span class="wiki-nav-title">Badges</span><span class="wiki-nav-subtitle">New Equipment</span></span>
</button></li>
<li><button type="button" class="wiki-nav-btn" data-wiki-target="general-deck">
<span class="wiki-nav-icon"><i class="ra ra-burning-book" style="color:white;font-size:32px;"></i></span>
<span class="wiki-nav-main"><span class="wiki-nav-title">Deck</span><span class="wiki-nav-subtitle">Deck System</span></span>
</button></li>
<li><button type="button" class="wiki-nav-btn" data-wiki-target="general-rarity">
<span class="wiki-nav-icon"><i class="ra ra-crystal-wand" style="color:white;font-size:32px;"></i></span>
<span class="wiki-nav-main"><span class="wiki-nav-title">Rarity</span><span class="wiki-nav-subtitle">Custom Items</span></span>
</button></li>
<li><button type="button" class="wiki-nav-btn" data-wiki-target="general-boat">
<span class="wiki-nav-icon"><i class="ra ra-octopus" style="color:white;font-size:32px;"></i></span>
<span class="wiki-nav-main"><span class="wiki-nav-title">Boat&#x27;s</span><span class="wiki-nav-subtitle">Explore the ocean</span></span>
</button></li>
<li><button type="button" class="wiki-nav-btn" data-wiki-target="general-balance">
<span class="wiki-nav-icon"><i class="ra ra-aura" style="color:white;font-size:32px;"></i></span>
<span class="wiki-nav-main"><span class="wiki-nav-title">Vocations</span><span class="wiki-nav-subtitle">Spells</span></span>
</button></li>
<li><button type="button" class="wiki-nav-btn" data-wiki-target="general-corruption">
<span class="wiki-nav-icon"><i class="ra ra-bone-bite" style="color:white;font-size:32px;"></i></span>
<span class="wiki-nav-main"><span class="wiki-nav-title">Corruption</span><span class="wiki-nav-subtitle">System</span></span>
</button></li>
</ul></div></div>
<div class="wiki-content-wrap">
<div class="wiki-entry is-active" id="entry-general-jobs">

<div class="text-box">
<h1 class="info-title heading-title">Professions</h1>
<h4 style="text-align:center;">Mining, Woodcutting and Herbalist</h4>
</div>
<div class="info__line"></div>
<div class="text-box">
<center>
<p> <span class="yellowcolor">Jobs</span> are a good starter to gather some materials, increase the skill required and do some gold.
											<br/><br/>
												There are 3 gather professions: Mining, Woodcutting and Herbalist.
												During the collect, your character may not be able to move until the animation finishes.<br/>
												To start gathering materials first of all you need to go on Teleport Island on the first floor and find the npc corresponding to the job in order to bought your first tool.<br/>
												Materials can be used on crafting (Minner's forge, Woodcutting Forge and Herbalist Forge) at the Teleport Island.
											</p>
<p>The Corruption update enlarge the mining, woodcutting and herbalist jobs, with new ressources to gather. See more on the Corruption Tab.</p>
<h1 class="info-title heading-title">Mining:</h1>
<img src="template/img/jobs/mining_skill.gif" style="border-radius:8px;"/><br/>
</center>
</div>
<div class="text-box">
<div class="text-table">
<ul class="text-columns" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/bronze_vein.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Bronze Vein
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/bronze_vein.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Bronze Vein</span></div>
<div class="text-cells__item">Color: <span>Bronze (brown)</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Mining Skill Required: <span>0</span></div>
<div class="text-cells__item">Description: <span>This is the first vein you can start mining.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/silver_vein.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Silver Vein
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/silver_vein.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Silver Vein</span></div>
<div class="text-cells__item">Color: <span>Silver (gray)</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Mining Skill Required: <span>5</span></div>
<div class="text-cells__item">Description: <span>Can be minned with apprentice's pickaxe, crafted on the minner's forge.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/sapphire_vein.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Sapphire Vein
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/sapphire_vein.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Sapphire Vein</span></div>
<div class="text-cells__item">Color: <span>Sapphire (blue)</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Mining Skill Required: <span>15</span></div>
<div class="text-cells__item">Description: <span>Can be minned with minners's pickaxe, crafted on the minner's forge.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/gold_vein.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Gold Vein
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/gold_vein.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Gold Vein</span></div>
<div class="text-cells__item">Color: <span>Gold (yellow)</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Mining Skill Required: <span>25</span></div>
<div class="text-cells__item">Description: <span>Can be minned with expert's pickaxe, crafted on the minner's forge.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/amethyst_vein.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Amethyst Vein
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/amethyst_vein.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Amethyst Vein</span></div>
<div class="text-cells__item">Color: <span>Amethyst (Purple/Pink)</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Mining Skill Required: <span>40</span></div>
<div class="text-cells__item">Description: <span>Can be minned with master's pickaxe, crafted on the minner's forge.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/ruby_vein.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Ruby Vein
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/ruby_vein.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Ruby Vein</span></div>
<div class="text-cells__item">Color: <span>Ruby (Red)</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Mining Skill Required: <span>60</span></div>
<div class="text-cells__item">Description: <span>Can be minned with master's pickaxe, crafted on the minner's forge.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/emerald_vein.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Emerald Vein
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/emerald_vein.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Emerald Vein</span></div>
<div class="text-cells__item">Color: <span>Emerald (Green)</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Mining Skill Required: <span>80</span></div>
<div class="text-cells__item">Description: <span>Can be minned with master's pickaxe, crafted on the minner's forge.</span></div>
</li>
</ul>
</div>
</div>
<div class="info__line"></div>
<center>
<h1 class="info-title heading-title">Woodcutting:</h1>
<img src="template/img/jobs/woodcutter.gif" style="border-radius:8px;"/><br/></center>
<div class="text-box">
<div class="text-table">
<ul class="text-columns" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/big_tree.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Big Tree
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/big_tree.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Big Tree</span></div>
<div class="text-cells__item">Color: <span>Green</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Woodcutting Skill Required: <span>0</span></div>
<div class="text-cells__item">Description: <span>This is the first tree that can be cutted.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/ash_tree.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Ash Tree
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/ash_tree.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Ash Tree</span></div>
<div class="text-cells__item">Color: <span>Green</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Woodcutting Skill Required: <span>5</span></div>
<div class="text-cells__item">Description: <span>Can be cutted with apprentice's axe crafted on Woodcutter's Forge.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/chestnut_tree.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Chestnut Tree
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/chestnut_tree.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Chestnut Tree</span></div>
<div class="text-cells__item">Color: <span>Green (Some Chestnuts)</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Woodcutting Skill Required: <span>15</span></div>
<div class="text-cells__item">Description: <span>Can be cutted with lumberjack's axe crafted on Woodcutter's Forge.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/olive_tree.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Olive Tree
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/olive_tree.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Olive Tree</span></div>
<div class="text-cells__item">Color: <span>Green (Some Olives)</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Woodcutting Skill Required: <span>25</span></div>
<div class="text-cells__item">Description: <span>Can be cutted with expert's axe crafted on Woodcutter's Forge.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/cherry_tree.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Cherry Tree
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/cherry_tree.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Cherry Tree</span></div>
<div class="text-cells__item">Color: <span>Red</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Woodcutting Skill Required: <span>40</span></div>
<div class="text-cells__item">Description: <span>Can be cutted with master's axe crafted on Woodcutter's Forge.</span></div>
</li>
</ul>
</div>
</div>
<div class="info__line"></div>
<center>
<h1 class="info-title heading-title">Herbalist:</h1>
<img src="template/img/jobs/herbalist.gif" style="border-radius:8px;"/><br/></center>
<div class="text-box">
<div class="text-table">
<ul class="text-columns" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/canterbury_bells.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Canterbury Bells
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/canterbury_bells.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Canterbury Bells</span></div>
<div class="text-cells__item">Color: <span>Violet</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Herbalist Skill Required: <span>0</span></div>
<div class="text-cells__item">Description: <span>This is the first plant that can be harvested.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/gentiana.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Gentiana
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/gentiana.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Gentiana</span></div>
<div class="text-cells__item">Color: <span>Gray</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Herbalist Skill Required: <span>5</span></div>
<div class="text-cells__item">Description: <span>Can be harvested with apprentice's scissor's crafter on Herbalist Table.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/scarlet_sage.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Scarlet Sage
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/scarlet_sage.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Scarlet Sage</span></div>
<div class="text-cells__item">Color: <span>Red Tips, green</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Herbalist Skill Required: <span>15</span></div>
<div class="text-cells__item">Description: <span>Can be harvested with herbalist's scissor's crafter on Herbalist Table.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/camellia.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Camellia
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/camellia.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Camellia</span></div>
<div class="text-cells__item">Color: <span>White)</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Herbalist Skill Required: <span>25</span></div>
<div class="text-cells__item">Description: <span>Can be harvested with expert's scissor's crafter on Herbalist Table.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/russula_emetica_plant.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Russula Emetica
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/russula_emetica_plant.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Russula Emetica</span></div>
<div class="text-cells__item">Color: <span>Red)</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Herbalist Skill Required: <span>40</span></div>
<div class="text-cells__item">Description: <span>Can be harvested with master's scissor's crafter on Herbalist Table.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/jobs/anemone_plant.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Anemone
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/jobs/anemone_plant.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Anemone</span></div>
<div class="text-cells__item">Color: <span>Violet</span></div>
<div class="text-cells__item">Spawn Time: <span>15 min</span></div>
<div class="text-cells__item">Herbalist Skill Required: <span>60</span></div>
<div class="text-cells__item">Description: <span>Can be harvested with master's scissor's crafter on Herbalist Table.</span></div>
</li>
</ul>
</div>
</div>
<div class="info__line"></div>

</div>
<div class="wiki-entry" id="entry-general-ancestral">

<div class="text-box">
<h1 class="info-title heading-title">Ancestral System</h1>
<h4 style="text-align:center;">Tasks, Points, Rank, Store and more !</h4>
<p><span class="yellowcolor">Ancestral System</span> is based on tasks, complete tasks opening the Ancestral Task Module and earn 
											Ancestral Points to spend on Ancestral Store, but also <span class="yellowcolor">Ancestral Rank Points</span> to grow your Ancestral Ranking.<br/>
											Ancestral Points are adquired doing Tasks, each task give different amount of Ancestral Rank Points.
											You can check your status on Ancestral System using the command <span class="yellowcolor">!Ancestral</span> you will see your Ranking, Points.</p>
<br/>
<center><img class="img-responsive" src="template/img/ancestral/task_module.png" style="border-radius:8px;"/></center><br/>
<p> Doing Tasks for <span class="yellowcolor">Armin</span> (located on Teleport Island on the bridge ) you will earn <span class="yellowcolor">Ancestral Rank Points</span> this points will upgrade your ranking depeding your points, also, you can complete tasks using the task button on client. See the table below about Rankings.<br/>
<br/>
<center><img src="template/img/ancestral/ancestral_players.png" style="border-radius:8px;"/></center><br/>
<center><p><i>You can check the Rank for every player doing a double click on the character.</i></p></center></p>
<br/>
<div class="text-table">
<ul class="text-columns" style="grid-template-columns: repeat(2, 1fr) !important;">
<li class="text-cells">
<div class="text-cells__head">Ranking Name</div>
<div class="text-cells__item">Rookie</div>
<div class="text-cells__item">Bronze</div>
<div class="text-cells__item">Silver</div>
<div class="text-cells__item">Gold</div>
<div class="text-cells__item">Platinum</div>
<div class="text-cells__item">Diamond</div>
<div class="text-cells__item">Ancestral</div>
<div class="text-cells__item">Mystic</div>
<div class="text-cells__item">Abyssal</div>
<div class="text-cells__item">Ascending</div>
<div class="text-cells__item">Chaos</div>
<div class="text-cells__item">Awakening</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Points</div>
<div class="text-cells__item">1 - 40</div>
<div class="text-cells__item">41 - 100</div>
<div class="text-cells__item">101 - 250</div>
<div class="text-cells__item">251 - 470</div>
<div class="text-cells__item">471 - 770</div>
<div class="text-cells__item">771 - 1220</div>
<div class="text-cells__item">1221 - 1820</div>
<div class="text-cells__item">1821 - 2820</div>
<div class="text-cells__item">2821 - 4320</div>
<div class="text-cells__item">4321 - 6320</div>
<div class="text-cells__item">6321 - 9999</div>
<div class="text-cells__item">10000+</div>
</li>
</ul>
</div>
<div class="info__line"></div>
<ul class="text-list">
<li>
<span></span>
<p>
                                                        Ancestral Store can be opened in game by pressing <img src="template/img/ancestral/ancestral_icon.png"/>
														That will open the Store:
                                                    </p>
<img src="template/img/ancestral/ancestral_store.png" style="border-radius:8px"/>
</li>
</ul>
</div>
<div class="info__line"></div>

</div>
<div class="wiki-entry" id="entry-general-dungeons">

<div class="text-box">
<h2>
<img alt="+" class="icon-h" src="template/img/headers/6.png"/> 
                                                Dungeons
                                            </h2>
<ul class="text-list">
<li>
<span></span>
<p>You can start finding a team on the search team button on dungeon finder. There are some dungeons, each one requires a different level. Defeat monsters to find the boss and claim best rewards.</p>
</li>
</ul><br/>
<img src="template/img/dungeon/dungeon_panel.png">
</img></div>
<div class="info__line"></div>
<div class="text-box">
<h2>
<img alt="+" class="icon-h" src="template/img/headers/8.png"/>
												List of Dungeons:
											</h2>
<div class="text-table">
<ul class="text-list">
<li>
<span></span>
<p>A table with detailed information about Dungeons.</p>
</li>
</ul><br/>
<ul class="text-columns" style="grid-template-columns: repeat(4, 1fr) !important;">
<li class="text-cells">
<div class="text-cells__head">Dungeon Name</div>
<div class="text-cells__item">Bones <span data-tip-def="Only one party can be inside the dungeon. If a battle is already in progress, please wait until the previous party leaves the room. Some dungeons have many instances." class="wiki-tip-trigger">[?]</span></div>
<div class="text-cells__item">Master Element <span data-tip-def="Only one party can be inside the dungeon. If a battle is already in progress, please wait until the previous party leaves the room. Some dungeons have many instances." class="wiki-tip-trigger">[?]</span></div>
<div class="text-cells__item">Forbidden <span data-tip-def="Only one party can be inside the dungeon. If a battle is already in progress, please wait until the previous party leaves the room. Some dungeons have many instances." class="wiki-tip-trigger">[?]</span></div>
<div class="text-cells__item">Rootkraken <span data-tip-def="Only one party can be inside the dungeon. If a battle is already in progress, please wait until the previous party leaves the room. Some dungeons have many instances." class="wiki-tip-trigger">[?]</span></div>
</li>
<li class="text-cells">
<div class="text-cells__head">Level Required</div>
<div class="text-cells__item">200</div>
<div class="text-cells__item">300</div>
<div class="text-cells__item">500</div>
<div class="text-cells__item">600</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Difficulties</div>
<div class="text-cells__item" data-tip-def="There is 5 difficulties for each dungeon, increasing Experience and rewards."><u>5</u></div>
<div class="text-cells__item" data-tip-def="There is 5 difficulties for each dungeon, increasing Experience and rewards."><u>5</u></div>
<div class="text-cells__item" data-tip-def="There is 5 difficulties for each dungeon, increasing Experience and rewards."><u>5</u></div>
<div class="text-cells__item" data-tip-def="There is 5 difficulties for each dungeon, increasing Experience and rewards."><u>5</u></div>
</li>
</ul>
</div>
<img data-tip-def="Dungeon Bones" src="template/img/dungeon/bones.png">
<img data-tip-def="Dungeon Master of Elements" src="template/img/dungeon/master_element.png">
<img data-tip-def="Forbidden Dungeon" src="template/img/dungeon/forbidden.png">
<img data-tip-def="Rootkraken Dungeon" src="template/img/dungeon/rootkraken.png">
</img></img></img></img></div>

</div>
<div class="wiki-entry" id="entry-general-azure">

<div class="text-box">
<h2>
                                                Azure Portals
                                            </h2>
<ul class="text-list">
<p><span class="yellowcolor">Azure Portals</span> are random portals appearing on <span class="yellowcolor">North Thais</span>, <span class="yellowcolor">West Darashia</span>, <span class="yellowcolor">North Est Roshamuul</span> and <span class="yellowcolor">South West Svargrond</span>.<br/>
													Before enter, you can find a new Npc called <span class="yellowcolor">Estherya</span> she trade,
											<span class="yellowcolor">Nightmare Coins</span> are obtained by killing Azure Boss or doing Ancestral Tasks about Azure Portals, for the new outfits <span class="yellowcolor">small angel</span> and <span class="yellowcolor"> everblight</span>.</p><br/>
<center><img data-tip-def="Nightmare Coin" src="template/img/azure/nightmare_coin.png"/></center>
</ul>
<div class="desc-margin-10"></div><br/>
<center><img src="template/img/azure/estherya.png" style="border-radius: 8px;"/></center>
<div class="desc-margin-20"></div><br/>
<p>Azure Portals are for strong players, inside you will find depth creatures and for the most stronger players he can reach the central room, where you need find out how to open the door and enter for fight with Azure.</p>
<div class="desc-margin-10"></div><br/>
<center><img src="template/img/azure/monsters.png" style="border-radius: 8px;"/></center>
<div class="desc-margin-20"></div><br/>
<p>Monsters have level from 1 to 10. They have a low probability to loot an Ancestral Coin that can be used to obtain 1 ancestral point to use on Ancestral Store. </p>
<center><img data-tip-def="Ancestral Coin" src="template/img/azure/ancestral_coin.png" style="border-radius: 8px;"/></center>
<div class="desc-margin-10"></div><br/>
<center><img class="img-responsive" src="template/img/azure/central_room.png" style="    border-radius: 8px;"/></center>
<div class="desc-margin-10"></div><br/>
<p>Find a way to open the door, then defeat Terror Spider, it will transform to Deep Necromancer, defeat Deep Necromancer will transform to Azure Boss, fight and kill it, you will obtain 
											 <span class="yellowcolor">Azure Coins</span> that can be used on Taerar Npc located on Azure Entrance and Exchange it for the new items set.<br/></p>
<center><img data-tip-def="Azure Coin" src="template/img/azure/azure_coin.png" style="    border-radius: 8px;"/></center><br/>
<center><img src="template/img/azure/azure.png" style="border-radius: 8px;"/></center><br/>
<div class="text-table"><center>
<ul class="text-columns" style="display: grid; grid-template-columns: 300px 1fr 100px;">
<li class="text-cells">
<div class="text-cells__head">Item Name</div>
<div class="text-cells__item">Varruth Helmet</div>
<div class="text-cells__item">Varruth Armor</div>
<div class="text-cells__item">Varruth Legs</div>
<div class="text-cells__item">Varruth Boots</div>
<div class="text-cells__item">Varruth Sword</div>
<div class="text-cells__item">Seetheras Helmet</div>
<div class="text-cells__item">Seetheras Armor</div>
<div class="text-cells__item">Seetheras Legs</div>
<div class="text-cells__item">Seetheras Boots</div>
<div class="text-cells__item">Seetheras Sword</div>
<div class="text-cells__item">Netherwave Helmet</div>
<div class="text-cells__item">Netherwave Armor</div>
<div class="text-cells__item">Netherwave Legs</div>
<div class="text-cells__item">Netherwave Boots</div>
<div class="text-cells__item">Frozen Wand</div>
<div class="text-cells__item">Venom Rod</div>
<div class="text-cells__item">Felsinger Helmet</div>
<div class="text-cells__item">Felsinger Armor</div>
<div class="text-cells__item">Felsinger Legs</div>
<div class="text-cells__item">Felsinger Boots</div>
<div class="text-cells__item">Lazering Bow</div>
<div class="text-cells__item">Exowing Bow</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Item Picture</div>
<div class="text-cells__item"><img src="template/img/azure/varruth_helmet.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/varruth_armor.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/varruth_legs.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/varruth_boots.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/varruth_sword.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/seetheras_helmet.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/seetheras_armor.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/seetheras_legs.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/seetheras_boots.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/seetheras_sword.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/netherwave_helmet.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/netherwave_armor.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/netherwave_legs.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/netherwave_boots.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/frozen_wand.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/venom_rod.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/felsinger_helmet.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/felsinger_armor.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/felsinger_legs.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/felsinger_boots.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/lazering_bow.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/azure/exowing_bow.png" style="border-radius: 8px;"/></div>
</li>
</ul></center>
</div>
<br></br>
<h2>
                                                New Outfits
                                            </h2>
<div class="desc-margin-10"></div>
<div class="desc-text__text fz_15">
<center><p>Small Angel</p></center><br/>
<div class="desc-margin-10"></div>
<center><p>Exchange <span class="yellowcolor"> Nightmare Coins </span> with Npc <span class="yellowcolor"> Estherya </span> located outside of each Azure Portal, you can also find it if portal is not here.</p></center><br/>
<div class="desc-margin-10"></div>
<center><img src="template/img/azure/small_angel.png" style="    border-radius: 8px;"/></center>
</div><br/>
<div class="desc-margin-20"></div>
<div class="desc-sep"></div>
<div class="desc-margin-20"></div>
<div class="desc-text__text fz_15">
<center><p>Everblight</p></center><br/>
<div class="desc-margin-10"></div>
<center><p>Exchange <span class="yellowcolor"> Nightmare Coins </span> with Npc <span class="yellowcolor"> Estherya </span> located outside of the Azure Portal.</p></center><br/>
<div class="desc-margin-10"></div>
<center><img src="template/img/azure/everblight.png" style="    border-radius: 8px;"/></center>
</div>
</div>
<div class="info__line"></div>

</div>
<div class="wiki-entry" id="entry-general-tier">

<div class="text-box">
<h4>
<img alt="+" class="icon-h" src="template/img/headers/16.png"/>
												Tier System
											</h4>
<ul class="text-list">
<div class="desc-text">
<div class="desc-margin-10"></div>
<div class="desc-text__text fz_15">
<p><span class="yellowcolor">Tier System</span> is based on custom equipments, that allows you to use an <span class="yellowcolor">Star Inlay Stone</span> in order to insert 3 empty stars to the equipment.<br/>
												Once you have inserted 3 Empty stars, you can insert a star stone following the rarity:<br/>
												-<span class="yellowcolor"> Common (green)</span><br/>
												-<span class="yellowcolor"> Rare (blue)</span><br/>
												-<span class="yellowcolor"> Very Rare (gold)</span><br/>
												-<span class="yellowcolor"> Epic (purple)</span><br/>
												-<span class="yellowcolor"> Legendary (red)</span><br/><p>
<p> Each Tier, has <span class="yellowcolor">1, 2 or 3 Stars</span>.<br/>
<span class="yellowcolor">First Star</span>, will take the base stats of the previous upgrade ( it means 1 star, no primary or secundary stats are incremented )<br/>
<span class="yellowcolor">Second Star</span> will increment secundary stats ( Resistance, Speed, Etc )<br/>
<span class="yellowcolor">Thirst Star</span> will increment first stats (Skills: Axe, distance..., Armor...)<br/><br/>
												To start adding a tier to a custom equipment, first you need defeat <span class="yellowcolor">Master of The Elements</span> and obtain <span class="yellowcolor">Star Inlay Stone</span>, using it on allowed equipment ( Netherwave, Skeletal, Varruth, Seetheras or Felsinger ) , Helmet, Armor, Legs and Boots only you will be able to start inlay a stone, try your chance in order to get the best Inlay Stone to increase your stats.<br/>
												After adding a stone, for example 2 Blue Stars ( Rare ), to an equipment and you will loot for example an Epic or Legendary, and you wish add it to your equipment, you will need use <span class="yellowcolor">Star Extractor</span> on your equipment in order to extract the stone incrusted to your equipment, your item will become again with 3 Empty stars and able to insert the new one. You will receive the stone incrusted to the equipment after extract it, but you will lose the star extractor.</p>
<div class="desc-margin-10"></div><br/>
<center><img class="img-responsive" src="template/img/tiers/insert_extraction.gif" style="    border-radius: 8px;"/></center>
<div class="desc-margin-20"></div><br/>
<center><p>Inlay or extract, doesn't fail.<p></p></p></center>
<div class="desc-margin-10"></div><br/>
<center><img class="img-responsive" src="template/img/tiers/monsters.gif" style="    border-radius: 8px;"/></center>
<div class="desc-margin-20"></div><br/>
<center><span class="yellowcolor"><u>New Monsters</u></span><br/>
<p><span class="yellowcolor">- Bulltauren Alchemist<br/>
													- Bulltauren Brute<br/>
													- Bulltauren Forgepriest<br/>
													- Mega Dragon<br/>
													- Mitmah Vanguard<br/>
													- Tauren Herk<br/>
													- Wardragon</span><br/></p>
</center><br/>
<div class="desc-margin-20"></div>
<center><img class="img-responsive" src="template/img/tiers/masterofthelements.gif" style="    border-radius: 8px;"/></center>
<div class="desc-margin-20"></div><br/>
<center><span class="yellowcolor">New Boss</span><br/>
<p>- <span class="yellowcolor">Master of the Elements</span><br/>
													The New Monsters &amp; Boss, are located on the new <span class="yellowcolor">Dungeon</span> called <span class="yellowcolor">Master of The Elements</span>, you will need a party up to 2 Players and level 200 in order to enter on the dungeon.
													<br/>
													Monsters will have a probability to loot:<br/>
													- Star Extractor
													- Common &amp; Rare Inlay Stones ( 1, 2, 3)<br/>
													- New Ressources<br/>
													- Some Items<br/>
													Boss will have a probability to loot: <br/>
													- Very Rare, Epic, Legendary Inlay Stones ( 1, 2, 3)<br/>
													- Star Extractor<br/>
													- Star Inlay Stone<br/>
													- Werewolf Helmet ( For all classes, do not use moonlight crystals now, later we will work on it).<br/>
													- New Ressources</p><br/>
<span class="yellowcolor">Some Pictures:</span><br/>
<p><i>Old Slots</i></p>
<center><img src="template/img/tiers/set.png" style="    border-radius: 8px;"/></center>
<br/>
<center><img src="template/img/tiers/stars_tier.png" style="    border-radius: 8px;"/></center>
</center></p></p></div>
</div>
</ul>
</div>
<h3> New Tiers Stones </h3>
<div class="text-table"><center>
<ul class="text-columns" style="display: grid; grid-template-columns: 300px 1fr 100px;">
<li class="text-cells">
<div class="text-cells__head">Item Name</div>
<div class="text-cells__item">Star Inlay Stone</div>
<div class="text-cells__item">Star Extractor</div>
<div class="text-cells__item">Common Star Inlay Stone</div>
<div class="text-cells__item">Common Two Star Inlay Stone</div>
<div class="text-cells__item">Common Three Star Inlay Stone</div>
<div class="text-cells__item">Rare Star Inlay Stone</div>
<div class="text-cells__item">Rare Two Star Inlay Stone</div>
<div class="text-cells__item">Rare Three Star Inlay Stone</div>
<div class="text-cells__item">Very Rare Star Inlay Stone</div>
<div class="text-cells__item">Very Rare Two Star Inlay Stone</div>
<div class="text-cells__item">Very Rare Three Star Inlay Stone</div>
<div class="text-cells__item">Epic Star Inlay Stone</div>
<div class="text-cells__item">Epic Two Star Inlay Stone</div>
<div class="text-cells__item">Epic Three Star Inlay Stone</div>
<div class="text-cells__item">Legendary Star Inlay Stone</div>
<div class="text-cells__item">Legendary Two Star Inlay Stone</div>
<div class="text-cells__item">Legendary Three Star Inlay Stone</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Picture</div>
<div class="text-cells__item"><img src="template/img/tiers/item_24477_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24493_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24478_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24479_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24480_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24481_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24482_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24483_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24484_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24485_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24486_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24487_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24488_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24489_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24490_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24491_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24492_.png" style="border-radius: 8px;"/></div>
</li>
</ul></center>
</div>
<div class="info__line"></div>
<div class="text-box">
<h3> New Ressources </h3>
<div class="text-table"><center>
<ul class="text-columns" style="display: grid; grid-template-columns: 300px 1fr 100px;">
<li class="text-cells">
<div class="text-cells__head">Item Name</div>
<div class="text-cells__item">Lesser Guardian Gem</div>
<div class="text-cells__item">Guardian Gem</div>
<div class="text-cells__item">Greater Guardian Gem</div>
<div class="text-cells__item">Lesser Marksman Gem</div>
<div class="text-cells__item">Marksman Gem</div>
<div class="text-cells__item">Greater Marksman Gem</div>
<div class="text-cells__item">Lesser Sage Gem</div>
<div class="text-cells__item">Sage Gem</div>
<div class="text-cells__item">Greater Sage Gem</div>
<div class="text-cells__item">Lesser Mystic Gem</div>
<div class="text-cells__item">Mystic Gem</div>
<div class="text-cells__item">Greater Mystic Gem</div>
<div class="text-cells__item">Splintered Mitmah Gem</div>
<div class="text-cells__item">Atab's Mitmah Helmet</div>
<div class="text-cells__item">Bulltaur Horn</div>
<div class="text-cells__item">Bulltaur Hoof</div>
<div class="text-cells__item">Bulltaur Armor Scrap</div>
<div class="text-cells__item">Strange Substance</div>
<div class="text-cells__item">Staff Piece</div>
<div class="text-cells__item">Idol of the forge</div>
<div class="text-cells__item">Nimmersatt's seal</div>
<div class="text-cells__item">Molten dragon essence</div>
<div class="text-cells__item">Mega dragon heart</div>
<div class="text-cells__item">Strange eye</div>
<div class="text-cells__item">Strange poison gland</div>
<div class="text-cells__item">Tauren Claw</div>
<div class="text-cells__item">Tauren Tooth</div>
<div class="text-cells__item">Exalted Seal</div>
<div class="text-cells__item">Gold-scaled sentinel</div>
<div class="text-cells__item">Crystallised Blood</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Picture</div>
<div class="text-cells__item"><img src="template/img/tiers/item_24494_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24495_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24496_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24497_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24498_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24499_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24500_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24501_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24502_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24503_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24504_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24505_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24506_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24507_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24508_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24509_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24510_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24511_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24512_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24513_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24514_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24515_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24516_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24517_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24518_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24519_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24520_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24521_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24522_.png" style="border-radius: 8px;"/></div>
<div class="text-cells__item"><img src="template/img/tiers/item_24523_.png" style="border-radius: 8px;"/></div>
</li>
</ul></center>
</div>
<br></br>
<h3> New Outfit </h3>
<div class="desc-margin-10"></div>
<div class="desc-text__text fz_15">
<center><p>Herald</p></center><br/>
<div class="desc-margin-10"></div>
<center><p>Exchange <span class="yellowcolor"> Nightmare Coins </span> with Npc <span class="yellowcolor"> Estherya </span> located outside of the Azure Portal.<br/>
<span class="yellowcolor">Master of the Elements</span> also will loot <span class="yellowcolor">Nightmare Coins</span></p><br/>
<div class="desc-margin-10"></div><br/>
<center><img src="template/img/tiers/herald.png" style="    border-radius: 8px;"/></center>
</center></div>
</div>

</div>
<div class="wiki-entry" id="entry-general-badges">

<div class="text-box">
<h4>
<img alt="+" class="icon-h" src="template/img/headers/30.png"/>
												Badges
											</h4>
<ul class="text-list">
<li>
<span></span>
<p>Badges, are new equipment that can be equipped on new slot badges, under ammo slot.</p>
</li>
<center><img src="template/img/badges/slot_badge.png"/></center><br/>
<li>
<span></span>
<p>Badges can be obtained by killing world bosses, see The Isles - World Bosses tab to find a world boss to kill around the continent.<br/>
													There is 9 different badges, that each one can be upgraded to the last tier. To upgrade a badge you will need an Amplifier that using on the correct badge it will upgrade it to the next tier. Badges can fail when upgrade but they are not destroyed.
													</p>
</li>
</ul><br/>
<div class="text-box">
<ul class="text-dropdowns">
<div class="text-dropdowns__visible">
<ul class="text-columns" style="grid-template-columns: repeat(1, 1fr) !important;">
<li class="text-cells">
<div class="text-cells__head">Exiled Spirit Badge</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25187_.png'&gt; Use a Exiled Amplifier Catalyst [1] to upgrade the badge to the next tier." src="template/img/badges/item_25147_.png"/>
<div class="right">Raging Exiled Spirit Badge</div>  Absorb Death 5%, Magic Level +1, Armor +2</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25188_.png'&gt; Use a Exiled Amplifier Catalyst [2] to upgrade the badge to the next tier." src="template/img/badges/item_25148_.png"/>
<div class="right">Furious Exiled Spirit Badge</div>  Absorb Death 6%, Magic Level +2, Mana Points +50, Armor +3</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25189_.png'&gt; Use a Exiled Amplifier Catalyst [3] to upgrade the badge to the next tier." src="template/img/badges/item_25149_.png"/>
<div class="right">Tempest Exiled Spirit Badge</div>  Absorb Death +6%, Magic Level: +3, Mana Points +100, Mana Leech +5%, Mana Amount +3%, Armor +4</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="Last Tier Reached." src="template/img/badges/item_25150_.png"/>
<div class="right">Blazing Exiled Spirit Badge</div>  Absorb Death +8%, Magic Level +4, Mana Points +150, Mana Leech +8%, Mana Amount +5%, Armor +5</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Fury Badge</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25190_.png'&gt; Use a Fury Amplifier Catalyst [1] to upgrade the badge to the next tier." src="template/img/badges/item_25151_.png"/>
<div class="right">Raging Fury Badge</div>  Absorb Energy +5%, Skill Sword, Axe, Club +1, Armor +2</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25191_.png'&gt; Use a Fury Amplifier Catalyst [2] to upgrade the badge to the next tier." src="template/img/badges/item_25152_.png"/>
<div class="right">Furious Fury Badge</div>  Absorb Energy +6%, Skill Sword, Axe, Club +2, Life Points +50, Armor +3</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25192_.png'&gt; Use a Fury Amplifier Catalyst [3] to upgrade the badge to the next tier." src="template/img/badges/item_25153_.png"/>
<div class="right">Tempest Fury Badge</div>  Absorb Energy +7%, Skill Sword, Axe, Club +3, Life Points +100, Life Leech +5%, Life Amount +3%, Armor +4</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="Last Tier Reached." src="template/img/badges/item_25154_.png"/>
<div class="right">Blazing Fury Badge</div>  Absorb Energy +8%, Skill Sword, Axe, Club +4, Life Points +150, Life Leech +8%, Life Amount +5%, Armor +5</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Moon Badge</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25193_.png'&gt; Use a Moon Amplifier Catalyst [1] to upgrade the badge to the next tier." src="template/img/badges/item_25155_.png"/>
<div class="right">Raging Moon Badge</div>  Skill Mining, Woodcutting, Herbalist, Crafting +1, Armor +2</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25194_.png'&gt; Use a Moon Amplifier Catalyst [2] to upgrade the badge to the next tier." src="template/img/badges/item_25156_.png"/>
<div class="right">Furious Moon Badge</div>  Skill Mining, Woodcutting, Herbalist, Crafting +2, Armor +3</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25195_.png'&gt; Use a Moon Amplifier Catalyst [3] to upgrade the badge to the next tier." src="template/img/badges/item_25157_.png"/>
<div class="right">Tempest Moon Badge</div>  Skill Mining, Woodcutting, Herbalist, Crafting +3, Armor +4</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25196_.png'&gt; Use a Moon Amplifier Catalyst [4] to upgrade the badge to the next tier." src="template/img/badges/item_25158_.png"/>
<div class="right">Blazing Moon Badge</div>  Skill Mining, Woodcutting, Herbalist, Crafting +4, Armor +5</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="Last Tier Reached." src="template/img/badges/item_25159_.png"/>
<div class="right">Eternal Moon Badge</div>  Skill Mining, Woodcutting, Herbalist, Crafting +5, Armor +5</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Night Seeker Badge</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25197_.png'&gt; Use a Night Seeker Amplifier Catalyst [1] to upgrade the badge to the next tier." src="template/img/badges/item_25160_.png"/>
<div class="right">Raging Night Seeker Badge</div>  Absorb All +2%, Life &amp; Mana Points +25, Armor +2</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25198_.png'&gt; Use a Night Seeker Amplifier Catalyst [2] to upgrade the badge to the next tier." src="template/img/badges/item_25161_.png"/>
<div class="right">Furious Night Seeker Badge</div>  Absorb All +2%, Life &amp; Mana Points +50, Armor +3</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25199_.png'&gt; Use a Night Seeker Amplifier Catalyst [3] to upgrade the badge to the next tier." src="template/img/badges/item_25162_.png"/>
<div class="right">Tempest Night Seeker Badge</div>  Absorb All +3%, Life &amp; Mana Points +100, Critical Hit &amp; Amount +5%, Armor +4</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25200_.png'&gt; Use a Night Seeker Amplifier Catalyst [4] to upgrade the badge to the next tier." src="template/img/badges/item_25163_.png"/>
<div class="right">Blazing Night Seeker Badge</div>  Absorb All +4%, Life &amp; Mana Points +200, Critical Hit &amp; Amount +10%, Armor +5</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="Last Tier Reached." src="template/img/badges/item_25164_.png"/>
<div class="right">Eternal Night Seeker Badge</div>  Absorb All +5%, Life &amp; Mana Points +300, Critical Hit &amp; Amount +15%, Armor +5</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Earthshade Badge</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25201_.png'&gt; Use a Earthshade Amplifier Catalyst [1] to upgrade the badge to the next tier." src="template/img/badges/item_25165_.png"/>
<div class="right">Raging Earthshade Badge</div>  Absorb Energy, Holy, Ice, Fire +5%, Armor +2</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25202_.png'&gt; Use a Earthshade Amplifier Catalyst [2] to upgrade the badge to the next tier." src="template/img/badges/item_25166_.png"/>
<div class="right">Furious Earthshade Badge</div>  Absorb Energy, Holy, Ice, Fire +7%, Armor +3</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25203_.png'&gt; Use a Earthshade Amplifier Catalyst [3] to upgrade the badge to the next tier." src="template/img/badges/item_25167_.png"/>
<div class="right">Tempest Earthshade Badge</div>  Absorb Energy, Holy, Ice, Fire +9%, Armor +4</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="Last Tier Reached." src="template/img/badges/item_25168_.png"/>
<div class="right">Blazing Earthshade Badge</div>  Absorb Energy, Holy, Ice, Fire +12%, Armor +5</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Reborn Badge</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25204_.png'&gt; Use a Reborn Amplifier Catalyst [1] to upgrade the badge to the next tier." src="template/img/badges/item_25169_.png"/>
<div class="right">Raging Reborn Badge</div>  Absorb Physical +5%, Mana &amp; Life Points +25, Armor +2</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25205_.png'&gt; Use a Reborn Amplifier Catalyst [2] to upgrade the badge to the next tier." src="template/img/badges/item_25170_.png"/>
<div class="right">Furious Reborn Badge</div>  Absorb Physical +8%, Mana &amp; Life Points +50, Armor +3</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25206_.png'&gt; Use a Reborn Amplifier Catalyst [3] to upgrade the badge to the next tier." src="template/img/badges/item_25171_.png"/>
<div class="right">Tempest Reborn Badge</div>  Absorb Physical +11%, Mana &amp; Life Points +100, Armor +4</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25207_.png'&gt; Use a Reborn Amplifier Catalyst [4] to upgrade the badge to the next tier." src="template/img/badges/item_25172_.png"/>
<div class="right">Blazing Reborn Badge</div>  Absorb Physical +14%, Mana &amp; Life Points +200, Armor +4</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="Last Tier Reached." src="template/img/badges/item_25173_.png"/>
<div class="right">Eternal Reborn Badge</div>  Absorb Physical +18%, Mana &amp; Life Points +300, Armor +5</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Chaosfiend Badge</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25208_.png'&gt; Use a Chaosfiend Amplifier Catalyst [1] to upgrade the badge to the next tier." src="template/img/badges/item_25174_.png"/>
<div class="right">Raging Chaosfiend Badge</div>  Life Points +350, Mana Points +100, Armor +2</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25209_.png'&gt; Use a Chaosfiend Amplifier Catalyst [2] to upgrade the badge to the next tier." src="template/img/badges/item_25175_.png"/>
<div class="right">Furious Chaosfiend Badge</div>  Life Points +700, Mana Points +200, Armor +3</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25210_.png'&gt; Use a Chaosfiend Amplifier Catalyst [3] to upgrade the badge to the next tier." src="template/img/badges/item_25176_.png"/>
<div class="right">Tempest Chaosfiend Badge</div>  Life Points +1400, Mana Points +300, Armor +4</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="Last Tier Reached." src="template/img/badges/item_25177_.png"/>
<div class="right">Blazing Chaosfiend Badge</div>  Life Points +2800, Mana Points +400, Armor +5</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Veins Badge</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25211_.png'&gt; Use a Veins Amplifier Catalyst [1] to upgrade the badge to the next tier." src="template/img/badges/item_25178_.png"/>
<div class="right">Raging Veins Badge</div>  Skill Distance +1, Absorb Holy +10%, Armor +2</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25212_.png'&gt; Use a Veins Amplifier Catalyst [2] to upgrade the badge to the next tier." src="template/img/badges/item_25179_.png"/>
<div class="right">Furious Veins Badge</div>  Skill Distance +2, Absorb Ice +10%, Armor +3</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25213_.png'&gt; Use a Veins Amplifier Catalyst [3] to upgrade the badge to the next tier." src="template/img/badges/item_25180_.png"/>
<div class="right">Tempest Veins Badge</div>  Skill Distance +3, Absorb Energy +10%, Life Leech +5%, Life Amount +3%, Armor +4</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="Last Tier Reached." src="template/img/badges/item_25181_.png"/>
<div class="right">Blazing Veins Badge</div>  Skill Distance +4, Absorb Fire +10%, Life Leech +8%, Life Amount +5%, Armor +4</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Daybreak Badge</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25214_.png'&gt; Use a Daybreak Amplifier Catalyst [1] to upgrade the badge to the next tier." src="template/img/badges/item_25182_.png"/>
<div class="right">Raging Daybreak Badge</div>  Life Leech &amp; Amount +3%, Mana Leech &amp; Amount +3%, Armor +3</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25215_.png'&gt; Use a Daybreak Amplifier Catalyst [2] to upgrade the badge to the next tier." src="template/img/badges/item_25183_.png"/>
<div class="right">Furious Daybreak Badge</div>  Life Leech &amp; Amount +5%, Mana Leech &amp; Amount +5%, Armor +3</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25216_.png'&gt; Use a Daybreak Amplifier Catalyst [3] to upgrade the badge to the next tier." src="template/img/badges/item_25184_.png"/>
<div class="right">Tempest Daybreak Badge</div>  Life Leech &amp; Amount +7%, Mana Leech &amp; Amount +7%, Critical Hit &amp; Amount +3%, Armor +4</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="&lt;img src='template/img/badges/item_25217_.png'&gt; Use a Daybreak Amplifier Catalyst [4] to upgrade the badge to the next tier." src="template/img/badges/item_25185_.png"/>
<div class="right">Blazing Daybreak Badge</div>  Life Leech &amp; Amount +9%, Mana Leech &amp; Amount +9%, Critical Hit &amp; Amount +5%, Armor +5</div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" data-tip-def="Last Tier Reached." src="template/img/badges/item_25186_.png"/>
<div class="right">Eternal Daybreak Badge</div>  Life Leech +15%, Life Amount +10%, Mana Leech +15%, Mana Amount +10%, Critical Hit +10%, Critical Amount +8%, Armor +5</div>
</li>
</ul>
</div>
</ul>
</div>
</div>
<div class="info__line"></div>

</div>
<div class="wiki-entry" id="entry-general-deck">

<div class="text-box">
<h2>
<img alt="+" class="icon-h" src="template/img/headers/21.png"/>
												Deck System
											</h2>
<ul class="text-list">
<li>
<span></span>
<p>Deck System, is a new system that make's a vocation possible to be played in another style.<br/>
													Under the ring slot you will find the Deck Slot, able to equip Deck's.</p>
</li>
<center><img src="template/img/deck_system/deck_slot.png"/></center><br/>
<li>
<span></span>
<p>There is 3 Decks: Bronze, Silver and Gold. The bronze Deck and Silver can be looted from any monster from Isle, then the Gold Deck can be looted from Wroldbosses, see The Isles - World Bosses tab to find a world boss to kill around the continent.<br/>
													On The deck's, you can add only Cards, that you can loot from Monsters of The Isles or WorldBosses. <br/>
													Each card, can give a bonus to the player or change the element of a spell when activated.<br/>
													To activate a card, place the card on the Deck then right click on it, you can also use the talkaction <b class="yellowcolor">!deck</b> in order to see the cards that you have activated. Each player can activate a maximum of 5 cards at same time.</p>
</li>
<li>
<span></span>
<p>Here a list of available Cards, there is Normal Cards and Legendary Cards. Cards can be upgraded using Essences Rare Essence, Epic Essence, Mythic or legendary, see the table.</p>
</li>
<li><span></span>
<p><i>Legendary Cards, can looted from Worldbosses, this cards only give's a bonus when they are Level 6.</i></p>
</li>
<li><span></span>
<p>Legendary Cards have 4 Tiers, Each Tier has 6 Levels, the bonus will be on the level 6 of each tier:</p></li>
<li><span></span>
<p style="color:green"> Exotic</p><br/>
<p style="color:purple">Chaos</p><br/>
<p style="color:red"> Abyssal</p><br/>
<p style="color:gold"> Ancestral</p><br/></li>
<li>
<span></span>
<p>Each Tier, the bonus will take effect only on level 6. There is 1 Legendary Card per Vocation, that each card, when activated, only certains weapons can activate the bonus while attacking (Majority they are each weapon of level 55 above for each vocation).</p>
</li>
<center> <p>Upgrade of legendary cards:<p><br/>
<img src="template/img/deck_system/legendary_upgrade.gif" style="width:700px"/>
<br/>
<p>Legendary Cards Summon (Tested with 100% chance for the video) summon will change appareance on each tier.</p><br/>
<img src="template/img/deck_system/legendary_cards.gif" style="width:700px"/>
</p></p></center>
</ul>
</div>
<div class="info__line"></div>
<h2>
<img alt="+" class="icon-h" src="template/img/headers/20.png"/>
												Normal Cards
											</h2>
<div class="text-table"><center>
<ul class="text-columns" style="display: grid; grid-template-columns: 1fr 1fr 1fr;">
<li class="text-cells">
<div class="text-cells__head">Card Name</div>
<div class="text-cells__item"><img data-tip-def="Knights: Convert Exori to Earth Damage" src="template/img/deck_system/item_24826_.png" style="border-radius: 8px;"/> Common Erasmo Card</div>
<div class="text-cells__item"><img data-tip-def="Knights: Convert Exori to Ice Damage" src="template/img/deck_system/item_24827_.png" style="border-radius: 8px;"/> Rare Erasmo Card</div>
<div class="text-cells__item"><img data-tip-def="Knights: Convert Exori to Death Damage" src="template/img/deck_system/item_24828_.png" style="border-radius: 8px;"/> Epic Erasmo Card</div>
<div class="text-cells__item"><img data-tip-def="Knights: Convert Exori to Energy Damage" src="template/img/deck_system/item_24829_.png" style="border-radius: 8px;"/> Mythic Erasmo Card</div>
<div class="text-cells__item"><img data-tip-def="Druid: Convert Exevo Tera Hur to Fire Damage" src="template/img/deck_system/item_24830_.png" style="border-radius: 8px;"/> Common Demetar Card</div>
<div class="text-cells__item"><img data-tip-def="Druid: Convert Exevo Tera Hur to Holy Damage" src="template/img/deck_system/item_24831_.png" style="border-radius: 8px;"/> Rare Demetar Card</div>
<div class="text-cells__item"><img data-tip-def="Druid: Convert Exevo Tera Hur to Death Damage" src="template/img/deck_system/item_24832_.png" style="border-radius: 8px;"/> Epic Demetar Card</div>
<div class="text-cells__item"><img data-tip-def="Druid: Convert Exevo Tera Hur to Energy Damage" src="template/img/deck_system/item_24833_.png" style="border-radius: 8px;"/> Mythic Demetar Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer: Convert Exevo Vis Hur to Holy Damage" src="template/img/deck_system/item_24834_.png" style="border-radius: 8px;"/> Common Kaishuter Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer: Convert Exevo Vis Hur to Death Damage" src="template/img/deck_system/item_24835_.png" style="border-radius: 8px;"/> Rare Kaishuter Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer: Convert Exevo Vis Hur to Ice Damage" src="template/img/deck_system/item_24836_.png" style="border-radius: 8px;"/> Epic Kaishuter Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer: Convert Exevo Vis Hur to Drown Damage" src="template/img/deck_system/item_24837_.png" style="border-radius: 8px;"/> Mythic Kaishuter Card</div>
<div class="text-cells__item"><img data-tip-def="Paladin: Convert Exevo Mas San to Fire Damage" src="template/img/deck_system/item_24838_.png" style="border-radius: 8px;"/> Common Enraged Chuo Card</div>
<div class="text-cells__item"><img data-tip-def="Paladin: Convert Exevo Mas San to Ice Damage" src="template/img/deck_system/item_24839_.png" style="border-radius: 8px;"/> Rare Enraged Chuo Card</div>
<div class="text-cells__item"><img data-tip-def="Paladin: Convert Exevo Mas San to Earth Damage" src="template/img/deck_system/item_24840_.png" style="border-radius: 8px;"/> Epic Enraged Chuo Card</div>
<div class="text-cells__item"><img data-tip-def="Paladin: Convert Exevo Mas San to Death Damage" src="template/img/deck_system/item_24841_.png" style="border-radius: 8px;"/> Mythic Enraged Chuo Card</div>
<div class="text-cells__item"><img data-tip-def="All Vocations: Convert Avalanche Rune to Fire Damage" src="template/img/deck_system/item_24842_.png" style="border-radius: 8px;"/> Common Zaika Card</div>
<div class="text-cells__item"><img data-tip-def="All Vocations: Convert Avalanche Rune to Earth Damage" src="template/img/deck_system/item_24843_.png" style="border-radius: 8px;"/> Rare Zaika Card</div>
<div class="text-cells__item"><img data-tip-def="All Vocations: Convert Avalanche Rune to Energy Damage" src="template/img/deck_system/item_24844_.png" style="border-radius: 8px;"/> Epic Zaika Card</div>
<div class="text-cells__item"><img data-tip-def="All Vocations: Convert Avalanche Rune to Death Damage" src="template/img/deck_system/item_24845_.png" style="border-radius: 8px;"/> Mythic Zaika Card</div>
<div class="text-cells__item"><img data-tip-def="Druid: Convert Exevo Gran Mas Frigo to Fire Damage" src="template/img/deck_system/item_24846_.png" style="border-radius: 8px;"/> Common Rictus Card</div>
<div class="text-cells__item"><img data-tip-def="Druid: Convert Exevo Gran Mas Frigo to Holy Damage" src="template/img/deck_system/item_24847_.png" style="border-radius: 8px;"/> Rare Rictus Card</div>
<div class="text-cells__item"><img data-tip-def="Druid: Convert Exevo Gran Mas Frigo to Death Damage" src="template/img/deck_system/item_24848_.png" style="border-radius: 8px;"/> Epic Rictus Card</div>
<div class="text-cells__item"><img data-tip-def="Druid: Convert Exevo Gran Mas Frigo to Energy Damage" src="template/img/deck_system/item_24849_.png" style="border-radius: 8px;"/> Mythic Rictus Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer: Convert Exevo Gran Mas Vis to Holy Damage" src="template/img/deck_system/item_24850_.png" style="border-radius: 8px;"/> Common Globus Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer: Convert Exevo Gran Mas Vis to Earth Damage" src="template/img/deck_system/item_24851_.png" style="border-radius: 8px;"/> Rare Globus Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer: Convert Exevo Gran Mas Vis to Death Damage" src="template/img/deck_system/item_24852_.png" style="border-radius: 8px;"/> Epic Globus Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer: Convert Exevo Gran Mas Vis to Ice Damage" src="template/img/deck_system/item_24853_.png" style="border-radius: 8px;"/> Mythic Globus Card</div>
<div class="text-cells__item"><img data-tip-def="Knight: Convert Exori mas to Fire Damage" src="template/img/deck_system/item_24854_.png" style="border-radius: 8px;"/> Common Hidaka Card</div>
<div class="text-cells__item"><img data-tip-def="Knight: Convert Exori mas to Earth Damage" src="template/img/deck_system/item_24855_.png" style="border-radius: 8px;"/> Rare Hidaka Card</div>
<div class="text-cells__item"><img data-tip-def="Knight: Convert Exori mas to Ice Damage" src="template/img/deck_system/item_24856_.png" style="border-radius: 8px;"/> Epic Hidaka Card</div>
<div class="text-cells__item"><img data-tip-def="Knight: Convert Exori mas to Death Damage" src="template/img/deck_system/item_24857_.png" style="border-radius: 8px;"/> Mythic Hidaka Card</div>
<div class="text-cells__item"><img data-tip-def="Knight: Convert Utito Tempo with Minor Bonus" src="template/img/deck_system/item_24858_.png" style="border-radius: 8px;"/> Common Brelshaza Card</div>
<div class="text-cells__item"><img data-tip-def="Knight: Convert Utito Tempo with Major Bonus" src="template/img/deck_system/item_24859_.png" style="border-radius: 8px;"/> Rare Brelshaza Card</div>
<div class="text-cells__item"><img data-tip-def="Knight: Convert Utito Tempo with Intense Bonus" src="template/img/deck_system/item_24860_.png" style="border-radius: 8px;"/> Epic Brelshaza Card</div>
<div class="text-cells__item"><img data-tip-def="Knight: Convert Utito Tempo with Powerful Bonus" src="template/img/deck_system/item_24861_.png" style="border-radius: 8px;"/> Mythic Brelshaza Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer: Convert Exevo Gran Flam Hur to Ice Damage" src="template/img/deck_system/item_24862_.png" style="border-radius: 8px;"/> Common Vairgrys Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer: Convert Exevo Gran Flam Hur to Holy Damage" src="template/img/deck_system/item_24863_.png" style="border-radius: 8px;"/> Rare Vairgrys Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer: Convert Exevo Gran Flam Hur to Death Damage" src="template/img/deck_system/item_24864_.png" style="border-radius: 8px;"/> Epic Vairgrys Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer: Convert Exevo Gran Flam Hur to Energy Damage" src="template/img/deck_system/item_24865_.png" style="border-radius: 8px;"/> Mythic Vairgrys Card</div>
<div class="text-cells__item"><img data-tip-def="Druid: Convert Exevo Gran Frigo hur to Fire Damage" src="template/img/deck_system/item_24866_.png" style="border-radius: 8px;"/> Common Enviska Card</div>
<div class="text-cells__item"><img data-tip-def="Druid: Convert Exevo Gran Frigo Hur to Holy Damage" src="template/img/deck_system/item_24867_.png" style="border-radius: 8px;"/> Rare Enviska Card</div>
<div class="text-cells__item"><img data-tip-def="Druid: Convert Exevo Gran Frigo Hur to Death Damage" src="template/img/deck_system/item_24868_.png" style="border-radius: 8px;"/> Epic Enviska Card</div>
<div class="text-cells__item"><img data-tip-def="Druid: Convert Exevo Gran Frigo Hur to Energy Damage" src="template/img/deck_system/item_24869_.png" style="border-radius: 8px;"/> Mythic Enviska Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer, Druid, Paladin: Increase Exura Gran with a Minor Bonus" src="template/img/deck_system/item_24870_.png" style="border-radius: 8px;"/> Common Harzal Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer, Druid, Paladin: Increase Exura Gran with a Major Bonus" src="template/img/deck_system/item_24871_.png" style="border-radius: 8px;"/> Rare Harzal Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer, Druid, Paladin: Increase Exura Gran with a Intense Bonus" src="template/img/deck_system/item_24872_.png" style="border-radius: 8px;"/> Epic Harzal Card</div>
<div class="text-cells__item"><img data-tip-def="Sorcerer, Druid, Paladin: Increase Exura Gran with a Powerful Bonus" src="template/img/deck_system/item_24873_.png" style="border-radius: 8px;"/> Mythic Harzal Card</div>
<div class="text-cells__item"><img data-tip-def="Paladin: Convert Exori San to Fire Damage" src="template/img/deck_system/item_24874_.png" style="border-radius: 8px;"/> Common Agaton Card</div>
<div class="text-cells__item"><img data-tip-def="Paladin: Convert Exori San to Earth Damage" src="template/img/deck_system/item_24875_.png" style="border-radius: 8px;"/> Rare Agaton Card</div>
<div class="text-cells__item"><img data-tip-def="Paladin: Convert Exori San to Death Damage" src="template/img/deck_system/item_24876_.png" style="border-radius: 8px;"/> Epic Agaton Card</div>
<div class="text-cells__item"><img data-tip-def="Paladin: Convert Exori San to Energy Damage" src="template/img/deck_system/item_24877_.png" style="border-radius: 8px;"/> Mythic Agaton Card</div>
<div class="text-cells__item"><img data-tip-def="Knight: Minor Increase Distance of Exeta Res" src="template/img/deck_system/item_24878_.png" style="border-radius: 8px;"/> Common Lumerus Card</div>
<div class="text-cells__item"><img data-tip-def="Knight: Major Increase Distance of Exeta Res" src="template/img/deck_system/item_24879_.png" style="border-radius: 8px;"/> Rare Lumerus Card</div>
<div class="text-cells__item"><img data-tip-def="Knight: Intense Increase Distance of Exeta Res" src="template/img/deck_system/item_24880_.png" style="border-radius: 8px;"/> Epic Lumerus Card</div>
<div class="text-cells__item"><img data-tip-def="Knight: Powerful Increase Distance of Exeta Res" src="template/img/deck_system/item_24881_.png" style="border-radius: 8px;"/> Mythic Lumerus Card</div>
<div class="text-cells__item"><img data-tip-def="Paladin: Increase Utito Tempo San with a Minor Bonus" src="template/img/deck_system/item_24882_.png" style="border-radius: 8px;"/> Common Velganos Card</div>
<div class="text-cells__item"><img data-tip-def="Paladin: Increase Utito Tempo San with a Major Bonus" src="template/img/deck_system/item_24883_.png" style="border-radius: 8px;"/> Rare Velganos Card</div>
<div class="text-cells__item"><img data-tip-def="Paladin: Increase Utito Tempo San with a Intense Bonus" src="template/img/deck_system/item_24884_.png" style="border-radius: 8px;"/> Epic Velganos Card</div>
<div class="text-cells__item"><img data-tip-def="Paladin: Increase Utito Tempo San with a Powerful Bonus" src="template/img/deck_system/item_24885_.png" style="border-radius: 8px;"/> Mythic Velganos Card</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Upgrade</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25047_.png" style="border-radius: 8px;"/>Use the rare essence to upgrade the card to rare tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25048_.png" style="border-radius: 8px;"/>Use the epic essence to upgrade the card to epic tier.</div>
<div class="text-cells__item"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25049_.png" style="border-radius: 8px;"/>Use the mythic essence to upgrade the card to mythic tier.</div>
<div class="text-cells__item">Last Tier.</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Loot from</div>
<div class="text-cells__item">Aeliana, Archon, Baalzebul, Lyrael, Nyxion, Oroniel, Saggitarius, Seraphiel.</div>
<div class="text-cells__item">Aeliana, Archon, Baalzebul, Lyrael, Nyxion, Oroniel, Saggitarius, Seraphiel.</div>
<div class="text-cells__item">Upgrading a Rare Erasmo Card.</div>
<div class="text-cells__item">Upgrading an Epic Erasmo Card.</div>
<div class="text-cells__item">Aeliana, Archon, Baalzebul, Lyrael, Nyxion, Oroniel, Saggitarius, Seraphiel.</div>
<div class="text-cells__item">Aeliana, Archon, Baalzebul, Lyrael, Nyxion, Oroniel, Saggitarius, Seraphiel.</div>
<div class="text-cells__item">Upgrading a Rare Demetar Card.</div>
<div class="text-cells__item">Upgrading an Epic Demetar Card.</div>
<div class="text-cells__item">Fangclaw, Lurskin, Scalebreaker, Slinkhide, Sludgejaw.</div>
<div class="text-cells__item">Fangclaw, Lurskin, Scalebreaker, Slinkhide, Sludgejaw.</div>
<div class="text-cells__item">Upgrading a Rare Kaishuter Card.</div>
<div class="text-cells__item">Upgrading an Epic Kaishuter Card.</div>
<div class="text-cells__item">Fangclaw, Lurskin, Scalebreaker, Slinkhide, Sludgejaw.</div>
<div class="text-cells__item">Fangclaw, Lurskin, Scalebreaker, Slinkhide, Sludgejaw.</div>
<div class="text-cells__item">Upgrading a Rare Enraged Chuo Card.</div>
<div class="text-cells__item">Upgrading an Epic Enraged Chuo Card.</div>
<div class="text-cells__item">Cinderclaw, Scaledrake.</div>
<div class="text-cells__item">Cinderclaw, Scaledrake.</div>
<div class="text-cells__item">Upgrading a Rare Zaika Card.</div>
<div class="text-cells__item">Upgrading an Epic Zaika Card.</div>
<div class="text-cells__item">Cinderclaw, Scaledrake.</div>
<div class="text-cells__item">Cinderclaw, Scaledrake.</div>
<div class="text-cells__item">Upgrading a Rare Rictus Card.</div>
<div class="text-cells__item">Upgrading an Epic Rictus Card.</div>
<div class="text-cells__item">Emerald Mage, Ruby Archer, Sapphire Warrior.</div>
<div class="text-cells__item">Emerald Mage, Ruby Archer, Sapphire Warrior.</div>
<div class="text-cells__item">Upgrading a Rare Globus Card.</div>
<div class="text-cells__item">Upgrading an Epic Globus Card.</div>
<div class="text-cells__item">Emerald Mage, Ruby Archer, Sapphire Warrior.</div>
<div class="text-cells__item">Emerald Mage, Ruby Archer, Sapphire Warrior.</div>
<div class="text-cells__item">Upgrading a Rare Hidaka Card.</div>
<div class="text-cells__item">Upgrading an Epic Hidaka Card.</div>
<div class="text-cells__item">Bonecleaver, Fleshcarver, Fleshrend, Mauler.</div>
<div class="text-cells__item">Bonecleaver, Fleshcarver, Fleshrend, Mauler.</div>
<div class="text-cells__item">Upgrading a Rare Brelshaza Card.</div>
<div class="text-cells__item">Upgrading an Epic Brelshaza Card.</div>
<div class="text-cells__item">Bonecleaver, Fleshcarver, Fleshrend, Mauler.</div>
<div class="text-cells__item">Bonecleaver, Fleshcarver, Fleshrend, Mauler.</div>
<div class="text-cells__item">Upgrading a Rare Vairgrys Card.</div>
<div class="text-cells__item">Upgrading an Epic Vairgrys Card.</div>
<div class="text-cells__item">Grothar, Grugnor, Krag'lok, Krulmash, Mok'gosh, Thokkar.</div>
<div class="text-cells__item">Grothar, Grugnor, Krag'lok, Krulmash, Mok'gosh, Thokkar.</div>
<div class="text-cells__item">Upgrading a Rare Enviska Card.</div>
<div class="text-cells__item">Upgrading an Epic Enviska Card.</div>
<div class="text-cells__item">Grothar, Grugnor, Krag'lok, Krulmash, Mok'gosh, Thokkar.</div>
<div class="text-cells__item">Grothar, Grugnor, Krag'lok, Krulmash, Mok'gosh, Thokkar.</div>
<div class="text-cells__item">Upgrading a Rare Harzal Card.</div>
<div class="text-cells__item">Upgrading an Epic Harzal Card.</div>
<div class="text-cells__item">Blackblade, Blightbearer, Gorthak, Malevolent, Shadowreaper, Urzog.</div>
<div class="text-cells__item">Blackblade, Blightbearer, Gorthak, Malevolent, Shadowreaper, Urzog.</div>
<div class="text-cells__item">Upgrading a Rare Agaton Card.</div>
<div class="text-cells__item">Upgrading an Epic Agaton Card.</div>
<div class="text-cells__item">Blackblade, Blightbearer, Gorthak, Malevolent, Shadowreaper, Urzog.</div>
<div class="text-cells__item">Blackblade, Blightbearer, Gorthak, Malevolent, Shadowreaper, Urzog.</div>
<div class="text-cells__item">Upgrading a Rare Lumerus Card.</div>
<div class="text-cells__item">Upgrading an Epic Lumerus Card.</div>
<div class="text-cells__item">Bonelich, Deathrattle, Doombone, Grimbone, Shadow Reaper, Skullshade.</div>
<div class="text-cells__item">Bonelich, Deathrattle, Doombone, Grimbone, Shadow Reaper, Skullshade.</div>
<div class="text-cells__item">Upgrading a Rare Velganos Card.</div>
<div class="text-cells__item">Upgrading an Epic Velganos Card.</div>
</li>
</ul></center>
</div>
<div class="info__line"></div>
<h2>
<img alt="+" class="icon-h" src="template/img/headers/26.png"/>
												Legendary Cards
											</h2>
<div class="text-table">
<ul class="text-columns" style="display: grid; grid-template-columns: 1fr 1fr 1fr;">
<li class="text-cells">
<div class="text-cells__head">Card Name</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Sorcerer's / Illusionist's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24886_.png" style="border-radius: 8px;"/> Legendary Exotic Ascalon Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Sorcerer's / Illusionist's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24892_.png" style="border-radius: 8px;"/> Legendary Chaos Ascalon Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Sorcerer's / Illusionist's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24898_.png" style="border-radius: 8px;"/> Legendary Abyssal Ascalon Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Sorcerer's / Illusionist's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24904_.png" style="border-radius: 8px;"/> Legendary Ancestral Ascalon Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Druid's / Illusionist's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24910_.png" style="border-radius: 8px;"/> Legendary Exotic Saydon Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Druid's / Illusionist's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24916_.png" style="border-radius: 8px;"/> Legendary Chaos Saydon Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Druid's / Illusionist's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24922_.png" style="border-radius: 8px;"/> Legendary Abyssal Saydon Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Druid's / Illusionist's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24928_.png" style="border-radius: 8px;"/> Legendary Ancestral Saydon Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Paladin's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24934_.png" style="border-radius: 8px;"/> Legendary Exotic Vercruze Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Paladin's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24940_.png" style="border-radius: 8px;"/> Legendary Chaos Vercruze Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Paladin's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24946_.png" style="border-radius: 8px;"/> Legendary Abyssal Vercruze Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Paladin's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24979_.png" style="border-radius: 8px;"/> Legendary Ancestral Vercruze Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Knight's / Monk's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24952_.png" style="border-radius: 8px;"/> Legendary Exotic Kaiser Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Knight's / Monk's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24958_.png" style="border-radius: 8px;"/> Legendary Chaos Kaiser Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Knight's / Monk's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24964_.png" style="border-radius: 8px;"/> Legendary Abyssal Kaiser Card</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Knight's / Monk's Legendary Card, Loot from Worldbosses" src="template/img/deck_system/item_24970_.png" style="border-radius: 8px;"/> Legendary Ancestral Kaiser Card</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Upgrade</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25043_.png" style="border-radius: 8px;"/>Use the Legendary Exotic Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25044_.png" style="border-radius: 8px;"/>Use the Legendary Chaos Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25045_.png" style="border-radius: 8px;"/>Use the Legendary Abyssal Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25045_.png" style="border-radius: 8px;"/>Use the Legendary Ancestral Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25043_.png" style="border-radius: 8px;"/>Use the Legendary Exotic Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25044_.png" style="border-radius: 8px;"/>Use the Legendary Chaos Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25045_.png" style="border-radius: 8px;"/>Use the Legendary Abyssal Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25045_.png" style="border-radius: 8px;"/>Use the Legendary Ancestral Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25043_.png" style="border-radius: 8px;"/>Use the Legendary Exotic Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25044_.png" style="border-radius: 8px;"/>Use the Legendary Chaos Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25045_.png" style="border-radius: 8px;"/>Use the Legendary Abyssal Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25045_.png" style="border-radius: 8px;"/>Use the Legendary Ancestral Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25043_.png" style="border-radius: 8px;"/>Use the Legendary Exotic Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25044_.png" style="border-radius: 8px;"/>Use the Legendary Chaos Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25045_.png" style="border-radius: 8px;"/>Use the Legendary Abyssal Essence to increase Tier of the Card from Level 1 to 6.</div>
<div class="text-cells__item" style="height:56px"><img data-tip-def="Loot from Worldbosses" src="template/img/deck_system/item_25045_.png" style="border-radius: 8px;"/>Use the Legendary Ancestral Essence to increase Tier of the Card from Level 1 to 6.</div>
</li>
<li class="text-cells">
<div class="text-cells__head">Information</div>
<div class="text-cells__item" style="height:56px">Level 6: 2% Chance to Summon Monster that Fight's with you for 25 seconds and increase magic level by 3</div>
<div class="text-cells__item" style="height:56px">Level 6: 3% Chance to Summon Monster that Fight's with you for 45 seconds and increase magic level by 6</div>
<div class="text-cells__item" style="height:56px">Level 6: 4% Chance to Summon Monster that Fight's with you for 65 seconds and increase magic level by 9</div>
<div class="text-cells__item" style="height:56px">Level 6: 5% Chance to Summon Monster that Fight's with you for 90 seconds and increase magic level by 15</div>
<div class="text-cells__item" style="height:56px">Level 6: 2% Chance to Summon Monster that Fight's with you for 25 seconds and increase magic level by 3</div>
<div class="text-cells__item" style="height:56px">Level 6: 3% Chance to Summon Monster that Fight's with you for 45 seconds and increase magic level by 6</div>
<div class="text-cells__item" style="height:56px">Level 6: 4% Chance to Summon Monster that Fight's with you for 65 seconds and increase magic level by 9</div>
<div class="text-cells__item" style="height:56px">Level 6: 5% Chance to Summon Monster that Fight's with you for 90 seconds and increase magic level by 15</div>
<div class="text-cells__item" style="height:56px">Level 6: 2% Chance to Summon Monster that Fight's with you for 25 seconds and increase distance by 3</div>
<div class="text-cells__item" style="height:56px">Level 6: 3% Chance to Summon Monster that Fight's with you for 45 seconds and increase distance by 6</div>
<div class="text-cells__item" style="height:56px">Level 6: 4% Chance to Summon Monster that Fight's with you for 65 seconds and increase distance by 9</div>
<div class="text-cells__item" style="height:56px">Level 6: 5% Chance to Summon Monster that Fight's with you for 90 seconds and increase distance by 15</div>
<div class="text-cells__item" style="height:56px">Level 6: 2% Chance to Summon Monster that Fight's with you for 25 seconds and increase melee skill by 3</div>
<div class="text-cells__item" style="height:56px">Level 6: 3% Chance to Summon Monster that Fight's with you for 45 seconds and increase melee skill by 6</div>
<div class="text-cells__item" style="height:56px">Level 6: 4% Chance to Summon Monster that Fight's with you for 65 seconds and increase melee skill by 9</div>
<div class="text-cells__item" style="height:56px">Level 6: 5% Chance to Summon Monster that Fight's with you for 90 seconds and increase melee skill by 15</div>
</li>
</ul>
</div>

</div>
<div class="wiki-entry" id="entry-general-rarity">

<div class="text-box">
<h2>
<img alt="+" class="icon-h" src="template/img/headers/16.png"/>
												Rarity System:
											</h2>
<ul class="text-list">
<li>
<span></span>
<p>Rarity items can be obtained killing monsters, any kind of monsters and bosses <b class="yellowcolor">only equipments</b> items can get rarity , containers excluded, there is also 5% chance to obtain a rarity item once is crafted at the corresponding table (jewelsmith, armorsmith, weaponsmith).
													Bosses loot goes to Boss Reward Chest.<br/>
</p>
</li>
<li>
<span></span>
<p>Once Looting, and item with rarity, a prefix can appear on the loot message and also a color based of rarity on the prefix. Item name colored will be the item market price.</p>
</li>
<center><img src="template/img/rarity/1739094456383.png"/></center><br/>
<center><img src="template/img/rarity/1739094272463.png"/></center><br/>
<center><img src="template/img/rarity/1739094482530.png"/></center><br/>
<li>
<span></span>
<p>Here a list of Rarity and chances, rarity loot is fixed at 5%, inside this 5% there is another 100% chance distributed between each rarity from least rare to most rare.</p>
</li>
</ul>
<br/>
<ul class="text-columns" style="grid-template-columns: repeat(2, 1fr) !important;">
<li class="text-cells">
<div class="text-cells__item"><span></span><span>Rarity Name:</span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_common_tsp.png"/><span>Common</span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_uncommon_tsp.png"/><span>Uncommon </span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_rare_tsp.png"/><span>Rare</span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_epic_tsp.png"/><span>Epic</span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_legendary_tsp.png"/><span>Legendary</span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_exotic_tsp.png"/><span>Exotic</span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_mythic_tsp.png"/><span>Mythic</span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_chaos_tsp.png"/><span>Chaos</span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_eternal_tsp.png"/><span>Eternal</span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_divine_tsp.png"/><span>Divine</span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_phantasmal_tsp.png"/><span>Phantasmal</span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_celestial_tsp.png"/><span>Celestial</span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_cosmic_tsp.png"/><span>Cosmic</span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_abyssal_tsp.png"/><span>Abyssal</span></div>
<div class="text-cells__item"><img src="template/img/rarity/rarity_transcendent_tsp.png"/><span>Transcendent</span></div>
</li>
<li class="text-cells">
<div class="text-cells__item">Chance:</div>
<div class="text-cells__item">15%</div>
<div class="text-cells__item">14%</div>
<div class="text-cells__item">13%</div>
<div class="text-cells__item">12%</div>
<div class="text-cells__item">10%</div>
<div class="text-cells__item">8%</div>
<div class="text-cells__item">7%</div>
<div class="text-cells__item">6%</div>
<div class="text-cells__item">5%</div>
<div class="text-cells__item">4.5%</div>
<div class="text-cells__item">3.3%</div>
<div class="text-cells__item">2.2%</div>
<div class="text-cells__item">1.8%</div>
<div class="text-cells__item">1.3%</div>
<div class="text-cells__item">1.0%</div>
</li>
</ul>
</div>

</div>
<div class="wiki-entry" id="entry-general-boat">

<div class="text-box">
<h2>
<img alt="+" class="icon-h" src="template/img/headers/33.png"/>
												Boat System:
											</h2>
<ul class="text-list">
<li>
<span></span>
<img src="template/img/boats/magnus.png"/>
<p>From now, each player can start a new mission on Decks, on each city, with <b class="yellowcolor">Magnus Blackwater</b> that it asks you for some materials in order to start the construction of your first boat.<br/>
													Meet <b style="orange">Shipright Trader</b> at south of Dolwatha , passage to Falanaar, to buy the materials to start the construction of your boat then go back to talk with Magnus.<br/>
</p>
</li>
<li>
<span></span>
<p>You will be rewarded with the <b class="yellowcolor">The Explorer</b> first boat to start navigation around the Continent.<br/>
													Make sure to be prepared before start navigating, maybe you will find a storm or some Pirates ships that can start fighting against intruders.<br/>
													A new Slot under Deck slot, can equip Ship's, without a ship you can't navigate.<br/>
													Boat's doesn't have any bonus, they simple have a different look for now.<br/></p>
</li>
<li>
<span></span>
<p>You can explore <b class="yellowcolor">9 New Isles</b> see <b class="yellowcolor">The Isles</b> Tab on Expansion Wiki to check about the Worldbosses that can be founded and new monsters.<br/>
													Also, you can explore or go to other Cities with the boat but also on the starter island you will find a port to dock and help your friends or new players.</p>
</li>
<center><img src="template/img/boats/boats.gif"/></center><br/>
<li>
<span></span>
<p>Navigating you can attack using your skills the pirates you will find, make sure to be prepared, otherwise you can lose your backpack on the ocean!</p>
</li>
<br/>
<center><img class="img-responsive" src="template/img/boats/baots_battle.gif"/></center>
</ul><br/>
<br/>
<ul class="text-columns" style="grid-template-columns: repeat(2, 1fr) !important;">
<li class="text-cells">
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/boats/item_25143_.png"/> <span> The Explorer </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/boats/item_25144_.png"/> <span> Whisperwind </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/boats/item_25145_.png"/> <span> Menethil March </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/boats/item_25146_.png"/> <span> Shadowmeld </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/boats/item_25140_.png"/> <span> Scarlet Raven </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/boats/item_25142_.png"/> <span> The Counterspell </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/boats/item_25141_.png"/> <span> The Awakener </span></div>
</li>
<li class="text-cells">
<div class="text-cells__item">First Boat obtained after completing mission for Magnus Blackwater</div>
<div class="text-cells__item">Loot from Worldbosses</div>
<div class="text-cells__item">Loot from Worldbosses</div>
<div class="text-cells__item">Loot from Worldbosses</div>
<div class="text-cells__item">Loot from Worldbosses</div>
<div class="text-cells__item">Loot from Worldbosses</div>
<div class="text-cells__item">Loot from Worldbosses</div>
</li>
</ul>
</div>

</div>
<div class="wiki-entry" id="entry-general-balance">

<div class="btns">
<div class="button" data-open-tab="classes" data-open-tab-active="true" data-open-tab-group="vocations"><span>Vocations</span> </div>
</div>
<br/><br/>
<div class="tabbox" data-tab="classes" data-tab-group="vocations">
<div class="text-box">
<ul class="text-list">
<li>
<span></span>
<p>Here you will find all vocations with their respective spells.</p>
</li>
</ul><br/>
</div>
<div class="btns">
<div class="cls" data-open-tab="class-1-" data-open-tab-active="true" data-open-tab-group="classes">
<div class="cls__pic">
<img class="cls__img" src="template/img/classes/knight.png"/>
</div>
<div class="cls__name">
													Knights
												</div>
</div>
<div class="cls" data-open-tab="class-2-" data-open-tab-group="classes">
<div class="cls__pic">
<img class="cls__img" src="template/img/classes/paladin.png"/>
</div>
<div class="cls__name">
													Paladins
												</div>
</div>
<div class="cls" data-open-tab="class-3-" data-open-tab-group="classes">
<div class="cls__pic">
<img class="cls__img" src="template/img/classes/sorcerer.png"/>
</div>
<div class="cls__name">
													Sorcerer
												</div>
</div>
<div class="cls" data-open-tab="class-4-" data-open-tab-group="classes">
<div class="cls__pic">
<img class="cls__img" src="template/img/classes/druid.png"/>
</div>
<div class="cls__name">
													Druid
												</div>
</div>
<div class="cls" data-open-tab="class-5-" data-open-tab-group="classes">
<div class="cls__pic">
<img class="cls__img" src="template/img/classes/illusionist.png"/>
</div>
<div class="cls__name">
													Illusionist
												</div>
</div>
<div class="cls" data-open-tab="class-6-" data-open-tab-group="classes">
<div class="cls__pic">
<img class="cls__img" src="template/img/classes/monk.png"/>
</div>
<div class="cls__name">
													Monk
												</div>
</div>
</div>
<br/><br/>
<div class="tabbox" data-tab="class-1-" data-tab-group="classes">
<h4><img alt="+" class="icon-h" src="template/img/headers/2.png"/> Knight &amp; Elite Knights</h4>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Bruise_Bane.gif"/>
<div class="item__content">
<div class="item__title">Bruise Bane</div>
<span class="tibiatext tibiayellow">exura infir ico</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>10, <span class="item__label">Effect: </span>Restores a tiny amount of health.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Lesser_Front_Sweep.gif"/>
<div class="item__content">
<div class="item__title">Lesser Front Sweep</div>
<span class="tibiatext tibiayellow">exori infir min</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>6, <span class="item__label">Effect: </span>Performs a cleave attack, dealing damage to the targets in front of the knight.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Find_Person.gif"/>
<div class="item__content">
<div class="item__title">Find Person</div>
<span class="tibiatext tibiayellow">exiva "name"</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Tells you which direction a certain player is, relative to you, and a rough idea of the distance.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Light.gif"/>
<div class="item__content">
<div class="item__title">Light</div>
<span class="tibiatext tibiayellow">utevo lux</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Provides illumination in a 3 tile radius around the caster (area effect). Duration is 6 minutes, 10 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Wound_Cleansing.gif"/>
<div class="item__content">
<div class="item__title">Wound Cleansing</div>
<span class="tibiatext tibiayellow">exura ico</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Restores a small amount of health.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Magic_Rope.gif"/>
<div class="item__content">
<div class="item__title">Magic Rope</div>
<span class="tibiatext tibiayellow">exani tera</span>
<div class="item__desc"><span class="item__label">Level: </span>9, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Teleports you up through a hole when you are standing in a Rope Spot.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Poison.gif"/>
<div class="item__content">
<div class="item__title">Cure Poison</div>
<span class="tibiatext tibiayellow">exana pox</span>
<div class="item__desc"><span class="item__label">Level: </span>10, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Cures the status of being poisoned.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Levitate.gif"/>
<div class="item__content">
<div class="item__title">Levitate</div>
<span class="tibiatext tibiayellow">exani hur up - exani hur down</span>
<div class="item__desc"><span class="item__label">Level: </span>12, <span class="item__label">Mana: </span>50, <span class="item__label">Effect: </span>Permits vertical movement via levitation, up or down one level.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Great_Light.gif"/>
<div class="item__content">
<div class="item__title">Great Light</div>
<span class="tibiatext tibiayellow">utevo gran lux</span>
<div class="item__desc"><span class="item__label">Level: </span>13, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Increases your base Speed by 30% for 30 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Brutal_Strike.gif"/>
<div class="item__content">
<div class="item__title">Brutal Strike</div>
<span class="tibiatext tibiayellow">exori ico</span>
<div class="item__desc"><span class="item__label">Level: </span>16, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Performs a brutal attack on the selected target, dealing physical damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Challenge.gif"/>
<div class="item__content">
<div class="item__title">Challenge</div>
<span class="tibiatext tibiayellow">exeta res</span>
<div class="item__desc"><span class="item__label">Level: </span>20, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Makes all creatures nearby change their target to the caster for at least 6 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Charge.gif"/>
<div class="item__content">
<div class="item__title">Charge</div>
<span class="tibiatext tibiayellow">utani tempo hur</span>
<div class="item__desc"><span class="item__label">Level: </span>25, <span class="item__label">Mana: </span>100, <span class="item__label">Effect: </span>Increases your speed by 90% for 5 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Whirlwind_Throw.gif"/>
<div class="item__content">
<div class="item__title">Whirlwind Throw</div>
<span class="tibiatext tibiayellow">utani tempo hur</span>
<div class="item__desc"><span class="item__label">Level: </span>28, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Tells you which direction the nearest fiendish creature is, relative to you, and a rough idea of the distance.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Groundshaker.gif"/>
<div class="item__content">
<div class="item__title">Groundshaker</div>
<span class="tibiatext tibiayellow">exori mas</span>
<div class="item__desc"><span class="item__label">Level: </span>33, <span class="item__label">Mana: </span>160, <span class="item__label">Effect: </span>A massive stomp dealing damage acording to the weapon characteristics in a 3 square meter radius.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Berserk.gif"/>
<div class="item__content">
<div class="item__title">Berserk</div>
<span class="tibiatext tibiayellow">exori</span>
<div class="item__desc"><span class="item__label">Level: </span>35, <span class="item__label">Mana: </span>115, <span class="item__label">Effect: </span>Performs a whirlwind attack, dealing damage acording to the weapon characteristics to all surrounding targets.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Inflict_Wound.gif"/>
<div class="item__content">
<div class="item__title">Inflict Wound</div>
<span class="tibiatext tibiayellow">utori kor</span>
<div class="item__desc"><span class="item__label">Level: </span>40, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>It inflicts an open wound on the enemy target inflicting Bleeding condition making them lose health over time.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Bleeding.gif"/>
<div class="item__content">
<div class="item__title">Cure Bleeding</div>
<span class="tibiatext tibiayellow">utori kor</span>
<div class="item__desc"><span class="item__label">Level: </span>45, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Stops the Special Condition called Bleeding.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Recovery.gif"/>
<div class="item__content">
<div class="item__title">Recovery</div>
<span class="tibiatext tibiayellow">utura</span>
<div class="item__desc"><span class="item__label">Level: </span>50, <span class="item__label">Mana: </span>75, <span class="item__label">Effect: </span>A healing effect in intervals over time. that heals 20 health points every 3 seconds for 60 seconds (total of 400 health points).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Protector.gif"/>
<div class="item__content">
<div class="item__title">Protector</div>
<span class="tibiatext tibiayellow">utura</span>
<div class="item__desc"><span class="item__label">Level: </span>55, <span class="item__label">Mana: </span>200, <span class="item__label">Effect: </span>Increases the knight's shielding to 220% for about 12 seconds. During this time, it will also reduce all damage the knight receives by 15%. Concentrating on his protection, the knight's attacks and spells have a -35% penalty during this period of time.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Blood_Rage.gif"/>
<div class="item__content">
<div class="item__title">Blood Rage</div>
<span class="tibiatext tibiayellow">utito tempo</span>
<div class="item__desc"><span class="item__label">Level: </span>60, <span class="item__label">Mana: </span>290, <span class="item__label">Effect: </span>Increases your melee attack skills (fist fighting, axe fighting, club fighting and sword fighting) by 35% for 10 seconds. Concentrating on the fight, a knight will neglect his defense being unable to block any attacks in this condition and suffers additional 15% of damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Front_Sweep.gif"/>
<div class="item__content">
<div class="item__title">Front Sweep</div>
<span class="tibiatext tibiayellow">exori min</span>
<div class="item__desc"><span class="item__label">Level: </span>70, <span class="item__label">Mana: </span>200, <span class="item__label">Effect: </span>Performs a powerful cleave attack, dealing strong damage acording to the weapon characteristics to the targets in front of the knight.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Intense_Wound_Cleansing.gif"/>
<div class="item__content">
<div class="item__title">Intense Wound Cleansing</div>
<span class="tibiatext tibiayellow">exura gran ico</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>200, <span class="item__label">Effect: </span>Heals a large amount of hit points.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Bewitched.gif"/>
<div class="item__content">
<div class="item__title">Cure Bewitched</div>
<span class="tibiatext tibiayellow">exana arcane</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Cures the status of being bewitched (arcane).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Splash.gif"/>
<div class="item__content">
<div class="item__title">Cure Splash</div>
<span class="tibiatext tibiayellow">exana splash</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Cures the status of being splashed (water).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Fierce_Berserk.gif"/>
<div class="item__content">
<div class="item__title">Fierce Berserk</div>
<span class="tibiatext tibiayellow">exori gran</span>
<div class="item__desc"><span class="item__label">Level: </span>90, <span class="item__label">Mana: </span>340, <span class="item__label">Effect: </span>Performs a furious whirlwind attack, dealing high damage acording to the weapon characteristics to all surrounding targets.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Intense_Recovery.gif"/>
<div class="item__content">
<div class="item__title">Intense Recovery</div>
<span class="tibiatext tibiayellow">exori gran</span>
<div class="item__desc"><span class="item__label">Level: </span>100, <span class="item__label">Mana: </span>165, <span class="item__label">Effect: </span>A healing effect in intervals over time that heals 40 health points every 3 seconds for 60 seconds (total of 800 health points).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Annihilation.gif"/>
<div class="item__content">
<div class="item__title">Annihilation</div>
<span class="tibiatext tibiayellow">exori gran ico</span>
<div class="item__desc"><span class="item__label">Level: </span>110, <span class="item__label">Mana: </span>300, <span class="item__label">Effect: </span>Attempts to execute the selected target with a devastating blow, dealing massive physical damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Chivalrous_Challenge.gif"/>
<div class="item__content">
<div class="item__title">Chivalrous Challenge</div>
<span class="tibiatext tibiayellow">exori gran ico</span>
<div class="item__desc"><span class="item__label">Level: </span>150, <span class="item__label">Mana: </span>80, <span class="item__label">Effect: </span>Chains up to 3 creatures prioritizing ranged creatures. Creatures hit are turned into melee fighters for 12 seconds and change their target to the caster for 6 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Summon_Knight_Familiar.gif"/>
<div class="item__content">
<div class="item__title">Summon Knight Familiar</div>
<span class="tibiatext tibiayellow">!summonfamiliar - Choose a Familiar: !choosefamiliar</span>
<div class="item__desc"><span class="item__label">Level: </span>200, <span class="item__label">Mana: </span>0, <span class="item__label">Effect: </span>Summons a Knight Familiar or an unlocked Familiar.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Fair_Wound_Cleansing.gif"/>
<div class="item__content">
<div class="item__title">Fair Wound Cleansing</div>
<span class="tibiatext tibiayellow">exura med ico</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>90, <span class="item__label">Effect: </span>Restores a medium amount of health.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Fair_Wound_Cleansing.gif"/>
<div class="item__content">
<div class="item__title">Fair Wound Cleansing</div>
<span class="tibiatext tibiayellow">exura med ico</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>90, <span class="item__label">Effect: </span>Restores a medium amount of health.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Executioners_Throw.gif"/>
<div class="item__content">
<div class="item__title">Executioner's Throw</div>
<span class="tibiatext tibiayellow">exori amp kor</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>225, <span class="item__label">Effect: </span>Throws your weapon on your target, bouncing it on nearby enemies.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Gift_of_Life.gif"/>
<div class="item__content">
<div class="item__title">Gift of Life</div>
<span class="tibiatext tibiayellow">-</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>0, <span class="item__label">Effect: </span>Under certain circumstances, heals the player before taking otherwise fatal damage, giving them a second chance.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/rage_of_wave.gif"/>
<div class="item__content">
<div class="item__title">Rage of Wave</div>
<span class="tibiatext tibiayellow">Exevo gran mas wave</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>650, <span class="item__label">Effect: </span>Cast a wave in direction of player dealing high damage. Unlocked after equiping a Fractured axe, sword or club.</div>
</div>
</div><br/>
</div>
<div class="tabbox" data-tab="class-2-" data-tab-group="classes">
<h4><img alt="+" class="icon-h" src="template/img/headers/2.png"/> Paladins &amp; Royal Paladins</h4>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Magic_Patch.gif"/>
<div class="item__content">
<div class="item__title">Magic Patch</div>
<span class="tibiatext tibiayellow">exura infir</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>6, <span class="item__label">Effect: </span>Restores a small amount of health.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Arrow_Call.gif"/>
<div class="item__content">
<div class="item__title">Arrow Call</div>
<span class="tibiatext tibiayellow">exevo infir con</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Creates 30 Simple Arrows.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Lesser_Ethereal_Spear.gif"/>
<div class="item__content">
<div class="item__title">Lesser Ethereal Spear</div>
<span class="tibiatext tibiayellow">exori infir con</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>6, <span class="item__label">Effect: </span>Throws a magical spear at the selected target, dealing physical damage based on the distance fighting skill.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Find_Person.gif"/>
<div class="item__content">
<div class="item__title">Find Person</div>
<span class="tibiatext tibiayellow">exiva "name"</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Tells you which direction a certain player is, relative to you, and a rough idea of the distance.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Light.gif"/>
<div class="item__content">
<div class="item__title">Light</div>
<span class="tibiatext tibiayellow">utevo lux</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Provides illumination in a 3 tile radius around the caster (area effect). Duration is 6 minutes, 10 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Light_Healing.gif"/>
<div class="item__content">
<div class="item__title">Light Healing</div>
<span class="tibiatext tibiayellow">exura</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Restores a small amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Magic_Rope.gif"/>
<div class="item__content">
<div class="item__title">Magic Rope</div>
<span class="tibiatext tibiayellow">exani tera</span>
<div class="item__desc"><span class="item__label">Level: </span>9, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Teleports you up through a hole when you are standing in a Rope Spot.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Poison.gif"/>
<div class="item__content">
<div class="item__title">Cure Poison</div>
<span class="tibiatext tibiayellow">exana pox</span>
<div class="item__desc"><span class="item__label">Level: </span>10, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Cures the status of being poisoned.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Levitate.gif"/>
<div class="item__content">
<div class="item__title">Levitate</div>
<span class="tibiatext tibiayellow">exani hur up - exani hur down</span>
<div class="item__desc"><span class="item__label">Level: </span>12, <span class="item__label">Mana: </span>50, <span class="item__label">Effect: </span>Permits vertical movement via levitation, up or down one level.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Great_Light.gif"/>
<div class="item__content">
<div class="item__title">Great Light</div>
<span class="tibiatext tibiayellow">utevo gran lux</span>
<div class="item__desc"><span class="item__label">Level: </span>13, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Increases your base Speed by 30% for 30 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Conjure_Arrow.gif"/>
<div class="item__content">
<div class="item__title">Conjure Arrow</div>
<span class="tibiatext tibiayellow">exevo con</span>
<div class="item__desc"><span class="item__label">Level: </span>13, <span class="item__label">Mana: </span>100, <span class="item__label">Effect: </span>Creates 10 Arrows.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Haste.gif"/>
<div class="item__content">
<div class="item__title">Haste</div>
<span class="tibiatext tibiayellow">utani hur</span>
<div class="item__desc"><span class="item__label">Level: </span>14, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Increases your base Speed by 30% for 30 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Intense_Healing.gif"/>
<div class="item__content">
<div class="item__title">Intense Healing</div>
<span class="tibiatext tibiayellow">exura gran</span>
<div class="item__desc"><span class="item__label">Level: </span>20, <span class="item__label">Mana: </span>70, <span class="item__label">Effect: </span>Restores a medium amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ethereal_Spear.gif"/>
<div class="item__content">
<div class="item__title">Ethereal Spear</div>
<span class="tibiatext tibiayellow">exori con</span>
<div class="item__desc"><span class="item__label">Level: </span>23, <span class="item__label">Mana: </span>25, <span class="item__label">Effect: </span>Throws a magical spear at the selected target, dealing physical damage based on the distance fighting skill.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Conjure_Explosive_Arrow.gif"/>
<div class="item__content">
<div class="item__title">Conjure Explosive Arrow</div>
<span class="tibiatext tibiayellow">exori con flam</span>
<div class="item__desc"><span class="item__label">Level: </span>25, <span class="item__label">Mana: </span>290, <span class="item__label">Effect: </span>Creates 8 Burst Arrows.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Divine_Healing.gif"/>
<div class="item__content">
<div class="item__title">Divine Healing</div>
<span class="tibiatext tibiayellow">exura san</span>
<div class="item__desc"><span class="item__label">Level: </span>35, <span class="item__label">Mana: </span>160, <span class="item__label">Effect: </span>Restores a great amount of health to the target. This spell when used will also cure paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Divine_Missile.gif"/>
<div class="item__content">
<div class="item__title">Divine Missile</div>
<span class="tibiatext tibiayellow">exori san</span>
<div class="item__desc"><span class="item__label">Level: </span>40, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Blasts the selected target with divine energy, dealing holy damage. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Enchant_Spear.gif"/>
<div class="item__content">
<div class="item__title">Enchant Spear</div>
<span class="tibiatext tibiayellow">exeta con</span>
<div class="item__desc"><span class="item__label">Level: </span>45, <span class="item__label">Mana: </span>350, <span class="item__label">Effect: </span>Turns one ordinary Spear into an Enchanted Spear. You need to have at least one ordinary spear in your backpack to make one.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Divine_Caldera.gif"/>
<div class="item__content">
<div class="item__title">Divine Caldera</div>
<span class="tibiatext tibiayellow">exevo mas san</span>
<div class="item__desc"><span class="item__label">Level: </span>50, <span class="item__label">Mana: </span>160, <span class="item__label">Effect: </span>Calls down a rain of holy fire to purify the area around the caster, dealing holy damage in a radius of 3 squares.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Divine_Caldera.gif"/>
<div class="item__content">
<div class="item__title">Divine Caldera</div>
<span class="tibiatext tibiayellow">exevo mas san</span>
<div class="item__desc"><span class="item__label">Level: </span>50, <span class="item__label">Mana: </span>160, <span class="item__label">Effect: </span>Calls down a rain of holy fire to purify the area around the caster, dealing holy damage in a radius of 3 squares.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Recovery.gif"/>
<div class="item__content">
<div class="item__title">Recovery</div>
<span class="tibiatext tibiayellow">utura</span>
<div class="item__desc"><span class="item__label">Level: </span>50, <span class="item__label">Mana: </span>75, <span class="item__label">Effect: </span>A healing effect in intervals over time. that heals 20 health points every 3 seconds for 60 seconds (total of 400 health points).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Swift_Foot.gif"/>
<div class="item__content">
<div class="item__title">Swift Foot</div>
<span class="tibiatext tibiayellow">utamo tempo san</span>
<div class="item__desc"><span class="item__label">Level: </span>55, <span class="item__label">Mana: </span>400, <span class="item__label">Effect: </span>Increases the paladin's speed by 80% for 10 seconds, they however can not attack or use attack spells during this time.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Salvation.gif"/>
<div class="item__content">
<div class="item__title">Salvation</div>
<span class="tibiatext tibiayellow">exura gran san</span>
<div class="item__desc"><span class="item__label">Level: </span>60, <span class="item__label">Mana: </span>210, <span class="item__label">Effect: </span>Heals a huge amount of hitpoints.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Holy_Flash.gif"/>
<div class="item__content">
<div class="item__title">Holy Flash</div>
<span class="tibiatext tibiayellow">utori san</span>
<div class="item__desc"><span class="item__label">Level: </span>70, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Makes the target become dazzled, receiving holy damage over time.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Curse.gif"/>
<div class="item__content">
<div class="item__title">Cure Curse</div>
<span class="tibiatext tibiayellow">exana mort</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Cures the Cursed condition.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Bewitched.gif"/>
<div class="item__content">
<div class="item__title">Cure Bewitched</div>
<span class="tibiatext tibiayellow">exana arcane</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Cures the status of being bewitched (arcane).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Splash.gif"/>
<div class="item__content">
<div class="item__title">Cure Splash</div>
<span class="tibiatext tibiayellow">exana splash</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Cures the status of being splashed (water).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Strong_Ethereal_Spear.gif"/>
<div class="item__content">
<div class="item__title">Strong Ethereal Spear</div>
<span class="tibiatext tibiayellow">exori gran con</span>
<div class="item__desc"><span class="item__label">Level: </span>90, <span class="item__label">Mana: </span>55, <span class="item__label">Effect: </span>A strong version of the Ethereal Spear spell.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Intense_Recovery.gif"/>
<div class="item__content">
<div class="item__title">Intense Recovery</div>
<span class="tibiatext tibiayellow">exori gran</span>
<div class="item__desc"><span class="item__label">Level: </span>100, <span class="item__label">Mana: </span>165, <span class="item__label">Effect: </span>A healing effect in intervals over time that heals 40 health points every 3 seconds for 60 seconds (total of 800 health points).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Divine_Grenade.gif"/>
<div class="item__content">
<div class="item__title">Divine Grenade</div>
<span class="tibiatext tibiayellow">exevo tempo mas san</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>160, <span class="item__label">Effect: </span>Plants a marker at the feet of your target that explodes after 3 seconds dealing holy damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Gift_of_Life.gif"/>
<div class="item__content">
<div class="item__title">Gift of Life</div>
<span class="tibiatext tibiayellow">-</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>0, <span class="item__label">Effect: </span>Under certain circumstances, heals the player before taking otherwise fatal damage, giving them a second chance.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/rage_of_arrows.gif"/>
<div class="item__content">
<div class="item__title">Rage of Arrows</div>
<span class="tibiatext tibiayellow">Exevo gran mas arrow</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>1050, <span class="item__label">Effect: </span>Casts an area damage falling arrows. Unlocked after equiping a Fractured Bow or Crossbow.</div>
</div>
</div><br/>
</div>
<div class="tabbox" data-tab="class-3-" data-tab-group="classes">
<h4><img alt="+" class="icon-h" src="template/img/headers/2.png"/> Sorcerer &amp; Master Sorcerer </h4>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Magic_Patch.gif"/>
<div class="item__content">
<div class="item__title">Magic Patch</div>
<span class="tibiatext tibiayellow">exura infir</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>6, <span class="item__label">Effect: </span>Restores a small amount of health.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Buzz.gif"/>
<div class="item__content">
<div class="item__title">Buzz</div>
<span class="tibiatext tibiayellow">exori infir vis</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>6, <span class="item__label">Effect: </span>Shoots a weak energy missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Scorch.gif"/>
<div class="item__content">
<div class="item__title">Scorch</div>
<span class="tibiatext tibiayellow">exevo infir flam hur</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>8, <span class="item__label">Effect: </span>Shoots a weak area attack directly in front of the caster, dealing fire damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Apprentice's_Strike.gif"/>
<div class="item__content">
<div class="item__title">Apprentice's Strike</div>
<span class="tibiatext tibiayellow">exori min flam</span>
<div class="item__desc"><span class="item__label">Level: </span>6, <span class="item__label">Mana: </span>6, <span class="item__label">Effect: </span>Shoots a weak fire missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Find_Person.gif"/>
<div class="item__content">
<div class="item__title">Find Person</div>
<span class="tibiatext tibiayellow">exiva "name"</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Tells you which direction a certain player is, relative to you, and a rough idea of the distance.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Light.gif"/>
<div class="item__content">
<div class="item__title">Light</div>
<span class="tibiatext tibiayellow">utevo lux</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Provides illumination in a 3 tile radius around the caster (area effect). Duration is 6 minutes, 10 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Light_Healing.gif"/>
<div class="item__content">
<div class="item__title">Light Healing</div>
<span class="tibiatext tibiayellow">exura</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Restores a small amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Flame_Strike.gif"/>
<div class="item__content">
<div class="item__title">Flame Strike</div>
<span class="tibiatext tibiayellow">exori flam</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots a fire missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ice_Strike.gif"/>
<div class="item__content">
<div class="item__title">Ice Strike</div>
<span class="tibiatext tibiayellow">exori frigo</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots an ice missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Magic_Rope.gif"/>
<div class="item__content">
<div class="item__title">Magic Rope</div>
<span class="tibiatext tibiayellow">exani tera</span>
<div class="item__desc"><span class="item__label">Level: </span>9, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Teleports you up through a hole when you are standing in a Rope Spot.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Poison.gif"/>
<div class="item__content">
<div class="item__title">Cure Poison</div>
<span class="tibiatext tibiayellow">exana pox</span>
<div class="item__desc"><span class="item__label">Level: </span>10, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Cures the status of being poisoned.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Levitate.gif"/>
<div class="item__content">
<div class="item__title">Levitate</div>
<span class="tibiatext tibiayellow">exani hur up - exani hur down</span>
<div class="item__desc"><span class="item__label">Level: </span>12, <span class="item__label">Mana: </span>50, <span class="item__label">Effect: </span>Permits vertical movement via levitation, up or down one level.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Energy_Strike.gif"/>
<div class="item__content">
<div class="item__title">Energy Strike</div>
<span class="tibiatext tibiayellow">exori vis</span>
<div class="item__desc"><span class="item__label">Level: </span>12, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots an energy missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Great_Light.gif"/>
<div class="item__content">
<div class="item__title">Great Light</div>
<span class="tibiatext tibiayellow">utevo gran lux</span>
<div class="item__desc"><span class="item__label">Level: </span>13, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Increases your base Speed by 30% for 30 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Terra_Strike.gif"/>
<div class="item__content">
<div class="item__title">Terra Strike</div>
<span class="tibiatext tibiayellow">exori tera</span>
<div class="item__desc"><span class="item__label">Level: </span>13, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots an earth missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Haste.gif"/>
<div class="item__content">
<div class="item__title">Haste</div>
<span class="tibiatext tibiayellow">utani hur</span>
<div class="item__desc"><span class="item__label">Level: </span>14, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Increases your base Speed by 30% for 30 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Magic_Shield.gif"/>
<div class="item__content">
<div class="item__title">Magic Shield</div>
<span class="tibiatext tibiayellow">utamo vita</span>
<div class="item__desc"><span class="item__label">Level: </span>14, <span class="item__label">Mana: </span>50, <span class="item__label">Effect: </span>Damage taken reduces Mana instead of Hit Points.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Death_Strike.gif"/>
<div class="item__content">
<div class="item__title">Death Strike</div>
<span class="tibiatext tibiayellow">exori mort</span>
<div class="item__desc"><span class="item__label">Level: </span>16, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots a death missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Fire_Wave.gif"/>
<div class="item__content">
<div class="item__title">Fire Wave</div>
<span class="tibiatext tibiayellow">exevo flam hur</span>
<div class="item__desc"><span class="item__label">Level: </span>18, <span class="item__label">Mana: </span>25, <span class="item__label">Effect: </span>Shoots an area attack directly in front of the caster, dealing fire damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Intense_Healing.gif"/>
<div class="item__content">
<div class="item__title">Intense Healing</div>
<span class="tibiatext tibiayellow">exura gran</span>
<div class="item__desc"><span class="item__label">Level: </span>20, <span class="item__label">Mana: </span>70, <span class="item__label">Effect: </span>Restores a medium amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Strong_Haste.gif"/>
<div class="item__content">
<div class="item__title">Strong Haste</div>
<span class="tibiatext tibiayellow">utani gran hur</span>
<div class="item__desc"><span class="item__label">Level: </span>20, <span class="item__label">Mana: </span>100, <span class="item__label">Effect: </span>Increases your base Speed by 70% for 21 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Creature_Illusion.gif"/>
<div class="item__content">
<div class="item__title">Creature Illusion</div>
<span class="tibiatext tibiayellow">utevo res ina "creature"</span>
<div class="item__desc"><span class="item__label">Level: </span>23, <span class="item__label">Mana: </span>100, <span class="item__label">Effect: </span>Turns you into a certain creature for three minutes.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Summon_Creature.gif"/>
<div class="item__content">
<div class="item__title">Summon Creature</div>
<span class="tibiatext tibiayellow">utevo res "creature name"</span>
<div class="item__desc"><span class="item__label">Level: </span>25, <span class="item__label">Mana: </span>varies, <span class="item__label">Effect: </span>Summons a creature to help you.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ultimate_Light.gif"/>
<div class="item__content">
<div class="item__title">Ultimate Light</div>
<span class="tibiatext tibiayellow">utevo vis lux</span>
<div class="item__desc"><span class="item__label">Level: </span>26, <span class="item__label">Mana: </span>140, <span class="item__label">Effect: </span>Fills the dark with light.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ignite.gif"/>
<div class="item__content">
<div class="item__title">Ignite</div>
<span class="tibiatext tibiayellow">utori flam</span>
<div class="item__desc"><span class="item__label">Level: </span>26, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Deals Fire Damage over time, similar to Electrify.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Great_Energy_Beam.gif"/>
<div class="item__content">
<div class="item__title">Great Energy Beam</div>
<span class="tibiatext tibiayellow">exevo gran vis lux</span>
<div class="item__desc"><span class="item__label">Level: </span>29, <span class="item__label">Mana: </span>110, <span class="item__label">Effect: </span>Shoots a powerful, 8 square long beam directly in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ultimate_Healing.gif"/>
<div class="item__content">
<div class="item__title">Ultimate Healing</div>
<span class="tibiatext tibiayellow">exura vita</span>
<div class="item__desc"><span class="item__label">Level: </span>30, <span class="item__label">Mana: </span>160, <span class="item__label">Effect: </span>Restores a large amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Electrify.gif"/>
<div class="item__content">
<div class="item__title">Electrify</div>
<span class="tibiatext tibiayellow">utori vis</span>
<div class="item__desc"><span class="item__label">Level: </span>34, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>It deals Energy Damage over time.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Invisible.gif"/>
<div class="item__content">
<div class="item__title">Invisible</div>
<span class="tibiatext tibiayellow">utana vid</span>
<div class="item__desc"><span class="item__label">Level: </span>35, <span class="item__label">Mana: </span>440, <span class="item__label">Effect: </span>Makes the caster invisible to some creatures. Duration is 3 minutes, 20 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Energy_Wave.gif"/>
<div class="item__content">
<div class="item__title">Energy Wave</div>
<span class="tibiatext tibiayellow">exevo vis hur</span>
<div class="item__desc"><span class="item__label">Level: </span>38, <span class="item__label">Mana: </span>170, <span class="item__label">Effect: </span>Shoots a powerful area attack directly in front of the caster, dealing strong energy damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Great_Fire_Wave.gif"/>
<div class="item__content">
<div class="item__title">Great Fire Wave</div>
<span class="tibiatext tibiayellow">exevo gran flam hur</span>
<div class="item__desc"><span class="item__label">Level: </span>38, <span class="item__label">Mana: </span>120, <span class="item__label">Effect: </span>Shoots an area attack directly in front of the caster, dealing fire damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Rage_of_the_Skies.gif"/>
<div class="item__content">
<div class="item__title">Rage of the Skies</div>
<span class="tibiatext tibiayellow">exevo gran mas vis</span>
<div class="item__desc"><span class="item__label">Level: </span>55, <span class="item__label">Mana: </span>600, <span class="item__label">Effect: </span>Invokes an immense lightning storm, dealing extremely high energy damage on a total of 85 squares around the caster. The spell deals about 5-10% less damage than Hell's Core, but its area of effect is larger.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Lightning.gif"/>
<div class="item__content">
<div class="item__title">Lightning</div>
<span class="tibiatext tibiayellow">exori amp vis</span>
<div class="item__desc"><span class="item__label">Level: </span>55, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Stronger version of Energy strike, can do around 50% more damage and has a 5 square range making it a very useful long-range single target spell.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Hell's_Core.gif"/>
<div class="item__content">
<div class="item__title">Hell's Core</div>
<span class="tibiatext tibiayellow">exevo gran mas flam</span>
<div class="item__desc"><span class="item__label">Level: </span>60, <span class="item__label">Mana: </span>1100, <span class="item__label">Effect: </span>Causes a devastating explosion of flames, dealing extremely high fire damage on a total of 73 squares around the caster. The spell deals about 5-10% more damage than Rage of the Skies, but its area of effect is smaller.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Strong_Flame_Strike.gif"/>
<div class="item__content">
<div class="item__title">Strong Flame Strike</div>
<span class="tibiatext tibiayellow">exori gran flam</span>
<div class="item__desc"><span class="item__label">Level: </span>70, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Shoots a powerful fire missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Curse.gif"/>
<div class="item__content">
<div class="item__title">Curse</div>
<span class="tibiatext tibiayellow">utori mort</span>
<div class="item__desc"><span class="item__label">Level: </span>75, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Inflicts cursed conditions on the target.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Bewitched.gif"/>
<div class="item__content">
<div class="item__title">Cure Bewitched</div>
<span class="tibiatext tibiayellow">exana arcane</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Cures the status of being bewitched (arcane).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Splash.gif"/>
<div class="item__content">
<div class="item__title">Cure Splash</div>
<span class="tibiatext tibiayellow">exana splash</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Cures the status of being splashed (water).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Strong_Energy_Strike.gif"/>
<div class="item__content">
<div class="item__title">Strong Energy Strike</div>
<span class="tibiatext tibiayellow">exori gran vis</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Shoots a powerful energy missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ultimate_Flame_Strike.gif"/>
<div class="item__content">
<div class="item__title">Ultimate Flame Strike</div>
<span class="tibiatext tibiayellow">exori max flam</span>
<div class="item__desc"><span class="item__label">Level: </span>90, <span class="item__label">Mana: </span>100, <span class="item__label">Effect: </span>Shoots a very powerful fire missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ultimate_Energy_Strike.gif"/>
<div class="item__content">
<div class="item__title">Ultimate Energy Strike</div>
<span class="tibiatext tibiayellow">exori max vis</span>
<div class="item__desc"><span class="item__label">Level: </span>100, <span class="item__label">Mana: </span>100, <span class="item__label">Effect: </span>Shoots a very powerful energy missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Expose_Weakness.gif"/>
<div class="item__content">
<div class="item__title">Expose Weakness</div>
<span class="tibiatext tibiayellow">exori moe</span>
<div class="item__desc"><span class="item__label">Level: </span>275, <span class="item__label">Mana: </span>400, <span class="item__label">Effect: </span>Increases the sensitivity of all creatures in an area (4 sqm radius) to all elements by 4% for 16 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Restoration.gif"/>
<div class="item__content">
<div class="item__title">Restoration</div>
<span class="tibiatext tibiayellow">exura max vita</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>260, <span class="item__label">Effect: </span>Restores a very large amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Great_Death_Beam.gif"/>
<div class="item__content">
<div class="item__title">Great Death Beam</div>
<span class="tibiatext tibiayellow">exevo max mort</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>140, <span class="item__label">Effect: </span>A beam spell that deals death damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Gift_of_Life.gif"/>
<div class="item__content">
<div class="item__title">Gift of Life</div>
<span class="tibiatext tibiayellow">-</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>0, <span class="item__label">Effect: </span>Under certain circumstances, heals the player before taking otherwise fatal damage, giving them a second chance.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/rage_of_nova.gif"/>
<div class="item__content">
<div class="item__title">Rage of Nova</div>
<span class="tibiatext tibiayellow">Exevo gran mas nova</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>1150, <span class="item__label">Effect: </span>Deals high damage in area. Unlocked after equiping a fractured wand or rod.</div>
</div>
</div><br/>
</div>
<div class="tabbox" data-tab="class-4-" data-tab-group="classes">
<h4><img alt="+" class="icon-h" src="template/img/headers/2.png"/> Druid &amp; Elder Druid</h4>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Magic_Patch.gif"/>
<div class="item__content">
<div class="item__title">Magic Patch</div>
<span class="tibiatext tibiayellow">exura infir</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>6, <span class="item__label">Effect: </span>Restores a small amount of health.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Chill_Out.gif"/>
<div class="item__content">
<div class="item__title">Chill Out</div>
<span class="tibiatext tibiayellow">exevo infir frigo hur</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>8, <span class="item__label">Effect: </span>Shoots a weak area attack directly in front of the caster, dealing ice damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Mud_Attack.gif"/>
<div class="item__content">
<div class="item__title">Mud Attack</div>
<span class="tibiatext tibiayellow">exevo infir tera</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>6, <span class="item__label">Effect: </span>Shoots a weak earth missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Apprentice's_Strike.gif"/>
<div class="item__content">
<div class="item__title">Apprentice's Strike</div>
<span class="tibiatext tibiayellow">exori min flam</span>
<div class="item__desc"><span class="item__label">Level: </span>6, <span class="item__label">Mana: </span>6, <span class="item__label">Effect: </span>Shoots a weak fire missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Find_Person.gif"/>
<div class="item__content">
<div class="item__title">Find Person</div>
<span class="tibiatext tibiayellow">exiva "name"</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Tells you which direction a certain player is, relative to you, and a rough idea of the distance.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Light.gif"/>
<div class="item__content">
<div class="item__title">Light</div>
<span class="tibiatext tibiayellow">utevo lux</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Provides illumination in a 3 tile radius around the caster (area effect). Duration is 6 minutes, 10 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Light_Healing.gif"/>
<div class="item__content">
<div class="item__title">Light Healing</div>
<span class="tibiatext tibiayellow">exura</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Restores a small amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Flame_Strike.gif"/>
<div class="item__content">
<div class="item__title">Flame Strike</div>
<span class="tibiatext tibiayellow">exori flam</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots a fire missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ice_Strike.gif"/>
<div class="item__content">
<div class="item__title">Ice Strike</div>
<span class="tibiatext tibiayellow">exori frigo</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots an ice missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Magic_Rope.gif"/>
<div class="item__content">
<div class="item__title">Magic Rope</div>
<span class="tibiatext tibiayellow">exani tera</span>
<div class="item__desc"><span class="item__label">Level: </span>9, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Teleports you up through a hole when you are standing in a Rope Spot.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Poison.gif"/>
<div class="item__content">
<div class="item__title">Cure Poison</div>
<span class="tibiatext tibiayellow">exana pox</span>
<div class="item__desc"><span class="item__label">Level: </span>10, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Cures the status of being poisoned.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Levitate.gif"/>
<div class="item__content">
<div class="item__title">Levitate</div>
<span class="tibiatext tibiayellow">exani hur up - exani hur down</span>
<div class="item__desc"><span class="item__label">Level: </span>12, <span class="item__label">Mana: </span>50, <span class="item__label">Effect: </span>Permits vertical movement via levitation, up or down one level.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Energy_Strike.gif"/>
<div class="item__content">
<div class="item__title">Energy Strike</div>
<span class="tibiatext tibiayellow">exori vis</span>
<div class="item__desc"><span class="item__label">Level: </span>12, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots an energy missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Great_Light.gif"/>
<div class="item__content">
<div class="item__title">Great Light</div>
<span class="tibiatext tibiayellow">utevo gran lux</span>
<div class="item__desc"><span class="item__label">Level: </span>13, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Increases your base Speed by 30% for 30 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Terra_Strike.gif"/>
<div class="item__content">
<div class="item__title">Terra Strike</div>
<span class="tibiatext tibiayellow">exori tera</span>
<div class="item__desc"><span class="item__label">Level: </span>13, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots an earth missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Haste.gif"/>
<div class="item__content">
<div class="item__title">Haste</div>
<span class="tibiatext tibiayellow">utani hur</span>
<div class="item__desc"><span class="item__label">Level: </span>14, <span class="item__label">Mana: </span>120, <span class="item__label">Effect: </span>Increases your base Speed by 30% for 30 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/food.gif"/>
<div class="item__content">
<div class="item__title">Food</div>
<span class="tibiatext tibiayellow">exevo pan</span>
<div class="item__desc"><span class="item__label">Level: </span>14, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Creates various kinds of food.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Magic_Shield.gif"/>
<div class="item__content">
<div class="item__title">Magic Shield</div>
<span class="tibiatext tibiayellow">utamo vita</span>
<div class="item__desc"><span class="item__label">Level: </span>14, <span class="item__label">Mana: </span>50, <span class="item__label">Effect: </span>Damage taken reduces Mana instead of Hit Points.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Physical_Strike.gif"/>
<div class="item__content">
<div class="item__title">Physical Strike</div>
<span class="tibiatext tibiayellow">exori moe ico</span>
<div class="item__desc"><span class="item__label">Level: </span>16, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots a physical damage missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Heal_Friend.gif"/>
<div class="item__content">
<div class="item__title">Heal Friend</div>
<span class="tibiatext tibiayellow">exura sio "name"</span>
<div class="item__desc"><span class="item__label">Level: </span>18, <span class="item__label">Mana: </span>120, <span class="item__label">Effect: </span>Heals a specified player. This cannot be used on yourself anymore.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ice_Wave.gif"/>
<div class="item__content">
<div class="item__title">Ice Wave</div>
<span class="tibiatext tibiayellow">exevo frigo hur</span>
<div class="item__desc"><span class="item__label">Level: </span>18, <span class="item__label">Mana: </span>25, <span class="item__label">Effect: </span>Shoots an area attack directly in front of the caster, dealing ice damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Intense_Healing.gif"/>
<div class="item__content">
<div class="item__title">Intense Healing</div>
<span class="tibiatext tibiayellow">exura gran</span>
<div class="item__desc"><span class="item__label">Level: </span>20, <span class="item__label">Mana: </span>70, <span class="item__label">Effect: </span>Restores a medium amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Strong_Haste.gif"/>
<div class="item__content">
<div class="item__title">Strong Haste</div>
<span class="tibiatext tibiayellow">utani gran hur</span>
<div class="item__desc"><span class="item__label">Level: </span>20, <span class="item__label">Mana: </span>100, <span class="item__label">Effect: </span>Increases your base Speed by 70% for 21 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Electrification.gif"/>
<div class="item__content">
<div class="item__title">Cure Electrification</div>
<span class="tibiatext tibiayellow">exana vis</span>
<div class="item__desc"><span class="item__label">Level: </span>22, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Stops the Special Condition called Electrified.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Creature_Illusion.gif"/>
<div class="item__content">
<div class="item__title">Creature Illusion</div>
<span class="tibiatext tibiayellow">utevo res ina "creature"</span>
<div class="item__desc"><span class="item__label">Level: </span>23, <span class="item__label">Mana: </span>100, <span class="item__label">Effect: </span>Turns you into a certain creature for three minutes.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Summon_Creature.gif"/>
<div class="item__content">
<div class="item__title">Summon Creature</div>
<span class="tibiatext tibiayellow">utevo res "creature name"</span>
<div class="item__desc"><span class="item__label">Level: </span>25, <span class="item__label">Mana: </span>varies, <span class="item__label">Effect: </span>Summons a creature to help you.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ultimate_Light.gif"/>
<div class="item__content">
<div class="item__title">Ultimate Light</div>
<span class="tibiatext tibiayellow">utevo vis lux</span>
<div class="item__desc"><span class="item__label">Level: </span>26, <span class="item__label">Mana: </span>140, <span class="item__label">Effect: </span>Fills the dark with light.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ultimate_Healing.gif"/>
<div class="item__content">
<div class="item__title">Ultimate Healing</div>
<span class="tibiatext tibiayellow">exura vita</span>
<div class="item__desc"><span class="item__label">Level: </span>30, <span class="item__label">Mana: </span>160, <span class="item__label">Effect: </span>Restores a large amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Burning.gif"/>
<div class="item__content">
<div class="item__title">Cure Burning</div>
<span class="tibiatext tibiayellow">exana flam</span>
<div class="item__desc"><span class="item__label">Level: </span>30, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Stops the Special Condition called Burning.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Heal_Party.gif"/>
<div class="item__content">
<div class="item__title">Heal Party</div>
<span class="tibiatext tibiayellow">utura mas sio</span>
<div class="item__desc"><span class="item__label">Level: </span>32, <span class="item__label">Mana: </span>varies, <span class="item__label">Effect: </span>The Heal Party spell will increase the regeneration speed of players on his party to 20 hit points every 2 seconds for a period of 2 minutes, totaling up 1200 hitpoints.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Invisible.gif"/>
<div class="item__content">
<div class="item__title">Invisible</div>
<span class="tibiatext tibiayellow">utana vid</span>
<div class="item__desc"><span class="item__label">Level: </span>35, <span class="item__label">Mana: </span>440, <span class="item__label">Effect: </span>Makes the caster invisible to some creatures. Duration is 3 minutes, 20 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Mass_Healing.gif"/>
<div class="item__content">
<div class="item__title">Mass Healing</div>
<span class="tibiatext tibiayellow">exura gran mas res</span>
<div class="item__desc"><span class="item__label">Level: </span>36, <span class="item__label">Mana: </span>150, <span class="item__label">Effect: </span>Heals all players and summons around the caster and will also heal the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Terra_Wave.gif"/>
<div class="item__content">
<div class="item__title">Terra Wave</div>
<span class="tibiatext tibiayellow">exevo tera hur</span>
<div class="item__desc"><span class="item__label">Level: </span>38, <span class="item__label">Mana: </span>170, <span class="item__label">Effect: </span>Shoots a powerful area attack directly in front of the caster, dealing strong earth damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Strong_Ice_Wave.gif"/>
<div class="item__content">
<div class="item__title">Strong Ice Wave</div>
<span class="tibiatext tibiayellow">exevo gran frigo hur</span>
<div class="item__desc"><span class="item__label">Level: </span>40, <span class="item__label">Mana: </span>170, <span class="item__label">Effect: </span>Shoots a powerful, but short ranged area attack directly in front of the caster, dealing strong ice damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Bleeding.gif"/>
<div class="item__content">
<div class="item__title">Cure Bleeding</div>
<span class="tibiatext tibiayellow">utori kor</span>
<div class="item__desc"><span class="item__label">Level: </span>45, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Stops the Special Condition called Bleeding.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Envenom.gif"/>
<div class="item__content">
<div class="item__title">Envenom</div>
<span class="tibiatext tibiayellow">utori pox</span>
<div class="item__desc"><span class="item__label">Level: </span>50, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Deals poisoned condition.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Wrath_of_Nature.gif"/>
<div class="item__content">
<div class="item__title">Wrath of Nature</div>
<span class="tibiatext tibiayellow">exevo gran mas tera</span>
<div class="item__desc"><span class="item__label">Level: </span>55, <span class="item__label">Mana: </span>700, <span class="item__label">Effect: </span>Calls forth a huge mass of poisonous vines to strangle enemies, dealing extremely high earth damage on a total of 101 squares around the caster. The spell deals about 5-10% less damage than Eternal Winter, but its area of effect is larger.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Eternal_Winter.gif"/>
<div class="item__content">
<div class="item__title">Eternal Winter</div>
<span class="tibiatext tibiayellow">exevo gran mas frigo</span>
<div class="item__desc"><span class="item__label">Level: </span>60, <span class="item__label">Mana: </span>1050, <span class="item__label">Effect: </span>Summons a raging blizzard, dealing extremely high ice damage on a total of 64 squares around the caster. The spell deals about 5-10% more damage than Wrath of Nature, but its area of effect is smaller.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Strong_Terra_Strike.gif"/>
<div class="item__content">
<div class="item__title">Strong Terra Strike</div>
<span class="tibiatext tibiayellow">exori gran tera</span>
<div class="item__desc"><span class="item__label">Level: </span>70, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Shoots a powerful earth missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Strong_Ice_Strike.gif"/>
<div class="item__content">
<div class="item__title">Strong Ice Strike</div>
<span class="tibiatext tibiayellow">exori gran frigo</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Shoots a powerful ice missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Bewitched.gif"/>
<div class="item__content">
<div class="item__title">Cure Bewitched</div>
<span class="tibiatext tibiayellow">exana arcane</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Cures the status of being bewitched (arcane).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Splash.gif"/>
<div class="item__content">
<div class="item__title">Cure Splash</div>
<span class="tibiatext tibiayellow">exana splash</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Cures the status of being splashed (water).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ultimate_Terra_Strike.gif"/>
<div class="item__content">
<div class="item__title">Ultimate Terra Strike</div>
<span class="tibiatext tibiayellow">exori max tera</span>
<div class="item__desc"><span class="item__label">Level: </span>90, <span class="item__label">Mana: </span>100, <span class="item__label">Effect: </span>Shoots a very powerful earth missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ultimate_Ice_Strike.gif"/>
<div class="item__content">
<div class="item__title">Ultimate Ice Strike</div>
<span class="tibiatext tibiayellow">exori max frigo</span>
<div class="item__desc"><span class="item__label">Level: </span>100, <span class="item__label">Mana: </span>100, <span class="item__label">Effect: </span>Shoots a very powerful ice missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Nature's_Embrace.gif"/>
<div class="item__content">
<div class="item__title">Natur's Embrace</div>
<span class="tibiatext tibiayellow">exura gran sio "name"</span>
<div class="item__desc"><span class="item__label">Level: </span>275, <span class="item__label">Mana: </span>400, <span class="item__label">Effect: </span>Heals a specified player.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Restoration.gif"/>
<div class="item__content">
<div class="item__title">Restoration</div>
<span class="tibiatext tibiayellow">exura max vita</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>260, <span class="item__label">Effect: </span>Restores a very large amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ice_Burst.gif"/>
<div class="item__content">
<div class="item__title">Ice Burst</div>
<span class="tibiatext tibiayellow">exevo ulus frigo</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>230, <span class="item__label">Effect: </span>Casts a powerful ring of ice around the character.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Terra_Burst.gif"/>
<div class="item__content">
<div class="item__title">Terra Burst</div>
<span class="tibiatext tibiayellow">exevo ulus tera</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>230, <span class="item__label">Effect: </span>Casts a powerful ring of earth around the character.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Gift_of_Life.gif"/>
<div class="item__content">
<div class="item__title">Gift of Life</div>
<span class="tibiatext tibiayellow">-</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>0, <span class="item__label">Effect: </span>Under certain circumstances, heals the player before taking otherwise fatal damage, giving them a second chance.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/rage_of_nova.gif"/>
<div class="item__content">
<div class="item__title">Rage of Nova</div>
<span class="tibiatext tibiayellow">Exevo gran mas nova</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>1150, <span class="item__label">Effect: </span>Deals high damage in area. Unlocked after equiping a fractured wand or rod.</div>
</div>
</div><br/>
</div>
<div class="tabbox" data-tab="class-5-" data-tab-group="classes">
<h4><img alt="+" class="icon-h" src="template/img/headers/2.png"/> Illusionist &amp; Arch Illusionist</h4>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Magic_Patch.gif"/>
<div class="item__content">
<div class="item__title">Magic Patch</div>
<span class="tibiatext tibiayellow">exura infir</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>6, <span class="item__label">Effect: </span>Restores a small amount of health.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Buzz.gif"/>
<div class="item__content">
<div class="item__title">Buzz</div>
<span class="tibiatext tibiayellow">exori infir vis</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>6, <span class="item__label">Effect: </span>Shoots a weak energy missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Scorch.gif"/>
<div class="item__content">
<div class="item__title">Scorch</div>
<span class="tibiatext tibiayellow">exevo infir flam hur</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>8, <span class="item__label">Effect: </span>Shoots a weak area attack directly in front of the caster, dealing fire damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Apprentice's_Strike.gif"/>
<div class="item__content">
<div class="item__title">Apprentice's Strike</div>
<span class="tibiatext tibiayellow">exori min flam</span>
<div class="item__desc"><span class="item__label">Level: </span>6, <span class="item__label">Mana: </span>6, <span class="item__label">Effect: </span>Shoots a weak fire missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Find_Person.gif"/>
<div class="item__content">
<div class="item__title">Find Person</div>
<span class="tibiatext tibiayellow">exiva "name"</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Tells you which direction a certain player is, relative to you, and a rough idea of the distance.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Light.gif"/>
<div class="item__content">
<div class="item__title">Light</div>
<span class="tibiatext tibiayellow">utevo lux</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Provides illumination in a 3 tile radius around the caster (area effect). Duration is 6 minutes, 10 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Light_Healing.gif"/>
<div class="item__content">
<div class="item__title">Light Healing</div>
<span class="tibiatext tibiayellow">exura</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Restores a small amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Flame_Strike.gif"/>
<div class="item__content">
<div class="item__title">Flame Strike</div>
<span class="tibiatext tibiayellow">exori flam</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots a fire missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ice_Strike.gif"/>
<div class="item__content">
<div class="item__title">Ice Strike</div>
<span class="tibiatext tibiayellow">exori frigo</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots an ice missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Magic_Rope.gif"/>
<div class="item__content">
<div class="item__title">Magic Rope</div>
<span class="tibiatext tibiayellow">exani tera</span>
<div class="item__desc"><span class="item__label">Level: </span>9, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Teleports you up through a hole when you are standing in a Rope Spot.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Poison.gif"/>
<div class="item__content">
<div class="item__title">Cure Poison</div>
<span class="tibiatext tibiayellow">exana pox</span>
<div class="item__desc"><span class="item__label">Level: </span>10, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Cures the status of being poisoned.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Levitate.gif"/>
<div class="item__content">
<div class="item__title">Levitate</div>
<span class="tibiatext tibiayellow">exani hur up - exani hur down</span>
<div class="item__desc"><span class="item__label">Level: </span>12, <span class="item__label">Mana: </span>50, <span class="item__label">Effect: </span>Permits vertical movement via levitation, up or down one level.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Arcane_Strike.gif"/>
<div class="item__content">
<div class="item__title">Arcane Strike</div>
<span class="tibiatext tibiayellow">exori ar</span>
<div class="item__desc"><span class="item__label">Level: </span>12, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots an arcane missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Energy_Strike.gif"/>
<div class="item__content">
<div class="item__title">Energy Strike</div>
<span class="tibiatext tibiayellow">exori vis</span>
<div class="item__desc"><span class="item__label">Level: </span>12, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots an energy missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Great_Light.gif"/>
<div class="item__content">
<div class="item__title">Great Light</div>
<span class="tibiatext tibiayellow">utevo gran lux</span>
<div class="item__desc"><span class="item__label">Level: </span>13, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Increases your base Speed by 30% for 30 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Terra_Strike.gif"/>
<div class="item__content">
<div class="item__title">Terra Strike</div>
<span class="tibiatext tibiayellow">exori tera</span>
<div class="item__desc"><span class="item__label">Level: </span>13, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Shoots an earth missile at the selected target up to 3 square meters away. If no target is selected, the damage is focused on the single square right in front of the caster.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Haste.gif"/>
<div class="item__content">
<div class="item__title">Haste</div>
<span class="tibiatext tibiayellow">utani hur</span>
<div class="item__desc"><span class="item__label">Level: </span>14, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Increases your base Speed by 30% for 30 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Magic_Shield.gif"/>
<div class="item__content">
<div class="item__title">Magic Shield</div>
<span class="tibiatext tibiayellow">utamo vita</span>
<div class="item__desc"><span class="item__label">Level: </span>14, <span class="item__label">Mana: </span>50, <span class="item__label">Effect: </span>Damage taken reduces Mana instead of Hit Points.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Intense_Healing.gif"/>
<div class="item__content">
<div class="item__title">Intense Healing</div>
<span class="tibiatext tibiayellow">exura gran</span>
<div class="item__desc"><span class="item__label">Level: </span>20, <span class="item__label">Mana: </span>70, <span class="item__label">Effect: </span>Restores a medium amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Strong_Haste.gif"/>
<div class="item__content">
<div class="item__title">Strong Haste</div>
<span class="tibiatext tibiayellow">utani gran hur</span>
<div class="item__desc"><span class="item__label">Level: </span>20, <span class="item__label">Mana: </span>100, <span class="item__label">Effect: </span>Increases your base Speed by 70% for 21 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Creature_Illusion.gif"/>
<div class="item__content">
<div class="item__title">Creature Illusion</div>
<span class="tibiatext tibiayellow">utevo res ina "creature"</span>
<div class="item__desc"><span class="item__label">Level: </span>23, <span class="item__label">Mana: </span>100, <span class="item__label">Effect: </span>Turns you into a certain creature for three minutes.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Summon_Creature.gif"/>
<div class="item__content">
<div class="item__title">Summon Creature</div>
<span class="tibiatext tibiayellow">utevo res "creature name"</span>
<div class="item__desc"><span class="item__label">Level: </span>25, <span class="item__label">Mana: </span>varies, <span class="item__label">Effect: </span>Summons a creature to help you.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ultimate_Light.gif"/>
<div class="item__content">
<div class="item__title">Ultimate Light</div>
<span class="tibiatext tibiayellow">utevo vis lux</span>
<div class="item__desc"><span class="item__label">Level: </span>26, <span class="item__label">Mana: </span>140, <span class="item__label">Effect: </span>Fills the dark with light.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Ultimate_Healing.gif"/>
<div class="item__content">
<div class="item__title">Ultimate Healing</div>
<span class="tibiatext tibiayellow">exura vita</span>
<div class="item__desc"><span class="item__label">Level: </span>30, <span class="item__label">Mana: </span>160, <span class="item__label">Effect: </span>Restores a large amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Invisible.gif"/>
<div class="item__content">
<div class="item__title">Invisible</div>
<span class="tibiatext tibiayellow">utana vid</span>
<div class="item__desc"><span class="item__label">Level: </span>35, <span class="item__label">Mana: </span>440, <span class="item__label">Effect: </span>Makes the caster invisible to some creatures. Duration is 3 minutes, 20 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Exevo_Cruz.gif"/>
<div class="item__content">
<div class="item__title">Cruz</div>
<span class="tibiatext tibiayellow">exevo cruz</span>
<div class="item__desc"><span class="item__label">Level: </span>38, <span class="item__label">Mana: </span>140, <span class="item__label">Effect: </span>Summons spectral copies of the caster in a cross-shaped formation centered on the caster dealing arcane damage. Each copy lasts for 1 second.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Arcane_Wave.gif"/>
<div class="item__content">
<div class="item__title">Arcane Wave</div>
<span class="tibiatext tibiayellow">exevo wave</span>
<div class="item__desc"><span class="item__label">Level: </span>40, <span class="item__label">Mana: </span>175, <span class="item__label">Effect: </span>Shoots an area attack directly in front of the caster, dealing arcane damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Bewitched_curse.gif"/>
<div class="item__content">
<div class="item__title">Bewitched Curse</div>
<span class="tibiatext tibiayellow">exevo witched</span>
<div class="item__desc"><span class="item__label">Level: </span>60, <span class="item__label">Mana: </span>75, <span class="item__label">Effect: </span>Inflicts arcane conditions on the target.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Bewitched.gif"/>
<div class="item__content">
<div class="item__title">Cure Bewitched</div>
<span class="tibiatext tibiayellow">exana arcane</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Cures the status of being bewitched (arcane).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Splash.gif"/>
<div class="item__content">
<div class="item__title">Cure Splash</div>
<span class="tibiatext tibiayellow">exana splash</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Cures the status of being splashed (water).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Phobia.gif"/>
<div class="item__content">
<div class="item__title">Phobia</div>
<span class="tibiatext tibiayellow">exevo phobia</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>200, <span class="item__label">Effect: </span>Instills fear in nearby enemies, causing them to flee in terror for a short duration.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Exevo_Gran_Mas_Evasion.gif"/>
<div class="item__content">
<div class="item__title">Evasion</div>
<span class="tibiatext tibiayellow">exevo gran mas evasion</span>
<div class="item__desc"><span class="item__label">Level: </span>100, <span class="item__label">Mana: </span>1350, <span class="item__label">Effect: </span>Turns the caster invisible and creates 4 illusory copies of the caster for 8 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Adento_Grav.gif"/>
<div class="item__content">
<div class="item__title">Adento Grav</div>
<span class="tibiatext tibiayellow">exevo phobia</span>
<div class="item__desc"><span class="item__label">Level: </span>150, <span class="item__label">Mana: </span>370, <span class="item__label">Effect: </span>Creates a rotating arcane area centered on the caster, dealing arcane damage each second for 8 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Restoration.gif"/>
<div class="item__content">
<div class="item__title">Restoration</div>
<span class="tibiatext tibiayellow">exura max vita</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>260, <span class="item__label">Effect: </span>Restores a very large amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Gift_of_Life.gif"/>
<div class="item__content">
<div class="item__title">Gift of Life</div>
<span class="tibiatext tibiayellow">-</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>0, <span class="item__label">Effect: </span>Under certain circumstances, heals the player before taking otherwise fatal damage, giving them a second chance.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/rage_of_nova.gif"/>
<div class="item__content">
<div class="item__title">Rage of Nova</div>
<span class="tibiatext tibiayellow">Exevo gran mas nova</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>1150, <span class="item__label">Effect: </span>Deals high damage in area. Unlocked after equiping a fractured wand or rod.</div>
</div>
</div><br/>
</div>
<div class="tabbox" data-tab="class-6-" data-tab-group="classes">
<h4><img alt="+" class="icon-h" src="template/img/headers/2.png"/> Monk &amp; Exalted Monk</h4>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Swift_Jab.gif"/>
<div class="item__content">
<div class="item__title">Swift Jab</div>
<span class="tibiatext tibiayellow">exori infir pug</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>3, <span class="item__label">Effect: </span>Deals damage to an adjacent enemy.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Tiger_Clash.gif"/>
<div class="item__content">
<div class="item__title">Tiger Clash</div>
<span class="tibiatext tibiayellow">exori infir nia</span>
<div class="item__desc"><span class="item__label">Level: </span>1, <span class="item__label">Mana: </span>18, <span class="item__label">Effect: </span>Deals damage to an adjacent enemy.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Lesser_Mystic_Repulse.gif"/>
<div class="item__content">
<div class="item__title">Lesser Mystic Repulse</div>
<span class="tibiatext tibiayellow">exori infir amp pug</span>
<div class="item__desc"><span class="item__label">Level: </span>6, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>A weaker version of Mystic Repulse.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Light_Healing.gif"/>
<div class="item__content">
<div class="item__title">Light Healing</div>
<span class="tibiatext tibiayellow">exura</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Restores a small amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Light.gif"/>
<div class="item__content">
<div class="item__title">Light</div>
<span class="tibiatext tibiayellow">utevo lux</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Provides illumination in a 3 tile radius around the caster (area effect). Duration is 6 minutes, 10 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Find_Person.gif"/>
<div class="item__content">
<div class="item__title">Find Person</div>
<span class="tibiatext tibiayellow">exiva "name"</span>
<div class="item__desc"><span class="item__label">Level: </span>8, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Tells you which direction a certain player is, relative to you, and a rough idea of the distance.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Magic_Rope.gif"/>
<div class="item__content">
<div class="item__title">Magic Rope</div>
<span class="tibiatext tibiayellow">exani tera</span>
<div class="item__desc"><span class="item__label">Level: </span>9, <span class="item__label">Mana: </span>20, <span class="item__label">Effect: </span>Teleports you up through a hole when you are standing in a Rope Spot.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Poison.gif"/>
<div class="item__content">
<div class="item__title">Cure Poison</div>
<span class="tibiatext tibiayellow">exana pox</span>
<div class="item__desc"><span class="item__label">Level: </span>10, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Cures the status of being poisoned.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Levitate.gif"/>
<div class="item__content">
<div class="item__title">Levitate</div>
<span class="tibiatext tibiayellow">exani hur up - exani hur down</span>
<div class="item__desc"><span class="item__label">Level: </span>12, <span class="item__label">Mana: </span>50, <span class="item__label">Effect: </span>Permits vertical movement via levitation, up or down one level.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Great_Light.gif"/>
<div class="item__content">
<div class="item__title">Great Light</div>
<span class="tibiatext tibiayellow">utevo gran lux</span>
<div class="item__desc"><span class="item__label">Level: </span>13, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Increases your base Speed by 30% for 30 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Double_Jab.gif"/>
<div class="item__content">
<div class="item__title">Double Jab</div>
<span class="tibiatext tibiayellow">exori pug</span>
<div class="item__desc"><span class="item__label">Level: </span>14, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Deals damage to an adjacent enemy.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Haste.gif"/>
<div class="item__content">
<div class="item__title">Haste</div>
<span class="tibiatext tibiayellow">utani hur</span>
<div class="item__desc"><span class="item__label">Level: </span>14, <span class="item__label">Mana: </span>60, <span class="item__label">Effect: </span>Increases your base Speed by 30% for 30 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Greater_Tiger_Clash.gif"/>
<div class="item__content">
<div class="item__title">Greater Tiger Clash</div>
<span class="tibiatext tibiayellow">exori nia</span>
<div class="item__desc"><span class="item__label">Level: </span>18, <span class="item__label">Mana: </span>50, <span class="item__label">Effect: </span>Deals damage to an adjacent enemy.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Restore_Balance.gif"/>
<div class="item__content">
<div class="item__title">Restore Balance</div>
<span class="tibiatext tibiayellow">exura tio sio "name"</span>
<div class="item__desc"><span class="item__label">Level: </span>18, <span class="item__label">Mana: </span>120, <span class="item__label">Effect: </span>Heals target ally.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Virtue_of_Harmony.gif"/>
<div class="item__content">
<div class="item__title">Virtue of Harmony</div>
<span class="tibiatext tibiayellow">utito virtu</span>
<div class="item__desc"><span class="item__label">Level: </span>20, <span class="item__label">Mana: </span>210, <span class="item__label">Effect: </span>Increases 4% or 8% if Focus Serenity is active, all damage done from spells. Only one virtue at same time can be effective.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Virtue_of_Justice.gif"/>
<div class="item__content">
<div class="item__title">Virtue of Justice</div>
<span class="tibiatext tibiayellow">utito virtu</span>
<div class="item__desc"><span class="item__label">Level: </span>20, <span class="item__label">Mana: </span>210, <span class="item__label">Effect: </span>Increases fist fighting by 8%, or 16% if Focus Serenity is active. Only one virtue at same time can be effective.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Virtue_of_Sustain.gif"/>
<div class="item__content">
<div class="item__title">Virtue of Sustain</div>
<span class="tibiatext tibiayellow">utura tio</span>
<div class="item__desc"><span class="item__label">Level: </span>20, <span class="item__label">Mana: </span>210, <span class="item__label">Effect: </span>Increases all healing spells by 35%, or 70% if Focus Serenity is active. Only one virtue at same time can be effective.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Intense_Healing.gif"/>
<div class="item__content">
<div class="item__title">Intense Healing</div>
<span class="tibiatext tibiayellow">exura gran</span>
<div class="item__desc"><span class="item__label">Level: </span>20, <span class="item__label">Mana: </span>70, <span class="item__label">Effect: </span>Restores a medium amount of health and cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Strong_Haste.gif"/>
<div class="item__content">
<div class="item__title">Strong Haste</div>
<span class="tibiatext tibiayellow">utani gran hur</span>
<div class="item__desc"><span class="item__label">Level: </span>20, <span class="item__label">Mana: </span>100, <span class="item__label">Effect: </span>Increases your base Speed by 70% for 21 seconds. Cures paralysis.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Mystic_Repulse.gif"/>
<div class="item__content">
<div class="item__title">Mystic Repulse</div>
<span class="tibiatext tibiayellow">exori amp pug</span>
<div class="item__desc"><span class="item__label">Level: </span>30, <span class="item__label">Mana: </span>150, <span class="item__label">Effect: </span>Deals high damage to a single target in up to 7 distance.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Enlighten_Party.gif"/>
<div class="item__content">
<div class="item__title">Enlighten Party</div>
<span class="tibiatext tibiayellow">utevo mas sio</span>
<div class="item__desc"><span class="item__label">Level: </span>32, <span class="item__label">Mana: </span>75, <span class="item__label">Effect: </span>Slowly restores Mana over time.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Flurry_of_Blows.gif"/>
<div class="item__content">
<div class="item__title">Flurry of Blows</div>
<span class="tibiatext tibiayellow">exori mas pug</span>
<div class="item__desc"><span class="item__label">Level: </span>35, <span class="item__label">Mana: </span>110, <span class="item__label">Effect: </span>Deals damage to enemies in front and to the side of the monk.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Bleeding.gif"/>
<div class="item__content">
<div class="item__title">Cure Bleeding</div>
<span class="tibiatext tibiayellow">utori kor</span>
<div class="item__desc"><span class="item__label">Level: </span>45, <span class="item__label">Mana: </span>30, <span class="item__label">Effect: </span>Stops the Special Condition called Bleeding.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Sweeping_Takedown.gif"/>
<div class="item__content">
<div class="item__title">Sweeping Takedown</div>
<span class="tibiatext tibiayellow">exori mas nia</span>
<div class="item__desc"><span class="item__label">Level: </span>60, <span class="item__label">Mana: </span>195, <span class="item__label">Effect: </span>Causes high damage to the 3x2 box in the direction of the attack and an additional 75% of the damage to the following connected 3x2 box.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Chained_Penance.gif"/>
<div class="item__content">
<div class="item__title">Chained Penance</div>
<span class="tibiatext tibiayellow">exori med pug</span>
<div class="item__desc"><span class="item__label">Level: </span>70, <span class="item__label">Mana: </span>180, <span class="item__label">Effect: </span>Deals damage to a target within range 3 and chains to up to 4 additional targets. </div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Spirit_Mend.gif"/>
<div class="item__content">
<div class="item__title">Spirit Mend</div>
<span class="tibiatext tibiayellow">exura gran tio</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>210, <span class="item__label">Effect: </span>Heals the monk.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Bewitched.gif"/>
<div class="item__content">
<div class="item__title">Cure Bewitched</div>
<span class="tibiatext tibiayellow">exana arcane</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Cures the status of being bewitched (arcane).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Cure_Splash.gif"/>
<div class="item__content">
<div class="item__title">Cure Splash</div>
<span class="tibiatext tibiayellow">exana splash</span>
<div class="item__desc"><span class="item__label">Level: </span>80, <span class="item__label">Mana: </span>40, <span class="item__label">Effect: </span>Cures the status of being splashed (water).</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Greater_Flurry_of_Blows.gif"/>
<div class="item__content">
<div class="item__title">Greater Flurry of Blows</div>
<span class="tibiatext tibiayellow">exori gran mas pug</span>
<div class="item__desc"><span class="item__label">Level: </span>90, <span class="item__label">Mana: </span>300, <span class="item__label">Effect: </span>Deals considerable area damage.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Forceful_Uppercut.gif"/>
<div class="item__content">
<div class="item__title">Forceful Uppercut</div>
<span class="tibiatext tibiayellow">exori gran pug</span>
<div class="item__desc"><span class="item__label">Level: </span>110, <span class="item__label">Mana: </span>325, <span class="item__label">Effect: </span>Deals high damage to a single target in melee.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Mentor_Other.gif"/>
<div class="item__content">
<div class="item__title">Mentor Other</div>
<span class="tibiatext tibiayellow">uteta tio "name"</span>
<div class="item__desc"><span class="item__label">Level: </span>110, <span class="item__label">Mana: </span>300, <span class="item__label">Effect: </span>Grants the target and the monk an effect for 60 seconds. Knight: Damage received reduced by 3%, Paladin: Damage caused by auto attacks increased by 5%, Sorcerer: Damage caused by spells and runes increased by 5%, Druid: Healing caused increased by 5%.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Focus_Serenity.gif"/>
<div class="item__content">
<div class="item__title">Focus Serenity</div>
<span class="tibiatext tibiayellow">utamo tio</span>
<div class="item__desc"><span class="item__label">Level: </span>150, <span class="item__label">Mana: </span>500, <span class="item__label">Effect: </span>Makes the monk serene for 7 seconds increasing damage done.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Mass_Spirit_Mend.gif"/>
<div class="item__content">
<div class="item__title">Mass Spirit Mend</div>
<span class="tibiatext tibiayellow">exura mas nia</span>
<div class="item__desc"><span class="item__label">Level: </span>150, <span class="item__label">Mana: </span>250, <span class="item__label">Effect: </span>A healing spell.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Balanced_Brawl.gif"/>
<div class="item__content">
<div class="item__title">Balanced Brawl</div>
<span class="tibiatext tibiayellow">exori mas res</span>
<div class="item__desc"><span class="item__label">Level: </span>175, <span class="item__label">Mana: </span>80, <span class="item__label">Effect: </span>Turns enemies in a half-circle shape in front of the monk into melee fighters for 16 seconds.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Summon_Knight_Familiar.gif"/>
<div class="item__content">
<div class="item__title">Summon Monk Familiar</div>
<span class="tibiatext tibiayellow">!summonfamiliar - Choose a Familiar: !choosefamiliar</span>
<div class="item__desc"><span class="item__label">Level: </span>200, <span class="item__label">Mana: </span>0, <span class="item__label">Effect: </span>Summons a Monk Familiar or an unlocked Familiar.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/Gift_of_Life.gif"/>
<div class="item__content">
<div class="item__title">Gift of Life</div>
<span class="tibiatext tibiayellow">-</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>0, <span class="item__label">Effect: </span>Under certain circumstances, heals the player before taking otherwise fatal damage, giving them a second chance.</div>
</div>
</div><br/>
<div class="item">
<img alt="icon" class="item__icon" src="template/img/spells/rage_of_totems.gif"/>
<div class="item__content">
<div class="item__title">Rage of Totems</div>
<span class="tibiatext tibiayellow">Exevo gran mas totem</span>
<div class="item__desc"><span class="item__label">Level: </span>300, <span class="item__label">Mana: </span>1000, <span class="item__label">Effect: </span>Deals damage in cross area. Unlocked after equiping a fractured fist weapon.</div>
</div>
</div><br/>
</div>
</div>

</div>
<div class="wiki-entry" id="entry-general-corruption">

<div class="text-box">
<h2>
<img alt="+" class="icon-h" src="template/img/headers/40.png"/>
												Corruption System:
											</h2>
<ul class="text-list">
<li>
<span></span>
<p>Corruption system is a new system that allows you to insert stats on any equipable item on the game.<br/>
													To start crafting Sigils, Catalyst, Scrolls or Runes, you need first to reach level 50 on mining, herbalist and woodcutting. After that to craft these items you will need level 20-40 on crafting skill.<br/>
</p>
</li>
<li><span></span>
<p>There is 9 new isles around the continent, where you will find new ressources to gather, but pay attention there is new monsters and Bosses around, you will die if you are not prepared, required level 600+.</p>
</li>
<li><span></span>
<p>New Hyper Bosses, loot items to craft the new equipments (silent lotus, phantom, twilight and disciple set) but also normal tier items for the corruption system (rathmora and evolution), the other bosses, they loot the scroll to corrupt each one for one tier, there is 5 tiers of corruption.</p>
</li>
<li><span></span>
<p>Once you get runes, sigils, catalysts you will start adding stats on the item you wish, after reaching 7 stats on a item, and desired stats you can start corrupting the item, there is 5 corruptions tiers. If you dont like one stat that you added, you can remove it using a purification scroll about the stat/element.</p>
</li>
<br/>
<h2>
												Corruption Tiers:</h2>
<li><span></span>
<p><b class="yellowcolor">Corrupted</b>: First mutation of the item, 50/50 chance, +5% or -5% to all stats, does not break, the item is locked.</p></li>
<li><span></span>
<p><b class="yellowcolor">Void Corruption</b>: Stable refinement, 70% +5%, 25% +10%, 5% -10% to all stats, does not break, the item is locked.</p></li>
<li><span></span>
<p><b class="yellowcolor">Ancient Corruption</b>: Strong enhancement, 50/50 chance, improves one stat by 1.35x, if it fails the item is destroyed and reset, +30 levels, it is locked, there is no possibility of protection.</p></li>
<li><span></span>
<p><b class="yellowcolor">Infernal Corruption</b>: Major transformation, 50/50 chance, doubles one stat and adds a new one to the maximum, if it fails the item is destroyed (or saved with protection), it is locked.</p></li>
<li><span></span>
<p><b class="yellowcolor">Fractured</b>: Final/Legendary Corruption, 30% success, adds a bonus stat to the maximum and unlocks a spell, depending on the corrupted weapon (weapons only), if it fails it is destroyed (or saved with protection), it remains locked.</p></li>
<br/>
</ul>
<h2>Normal Tier</h2>
<ul class="text-columns" style="grid-template-columns: repeat(2, 1fr) !important;">
<li class="text-cells">
<div class="text-cells__item"><span> Item Name </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27616.png"/> <span> Sigil of Arcane </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27617.png"/> <span> Sigil of Death </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27618.png"/> <span> Sigil of Drown </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27619.png"/> <span> Sigil of Earth </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27620.png"/> <span> Sigil of Energy </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27621.png"/> <span> Sigil of Fire </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27622.png"/> <span> Sigil of Holy </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27623.png"/> <span> Sigil of Ice </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27624.png"/> <span> Sigil of Physical </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27625.png"/> <span> Sigil of Water </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27626.png"/> <span> Catalyst of Arcane </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27627.png"/> <span> Catalyst of Death </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27628.png"/> <span> Catalyst of Earth </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27629.png"/> <span> Catalyst of Energy </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27630.png"/> <span> Catalyst of Fire </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27631.png"/> <span> Catalyst of Holy </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27632.png"/> <span> Catalyst of Ice </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27633.png"/> <span> Catalyst of Water </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27634.png"/> <span> Rune of Armorsmith </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27635.png"/> <span> Rune of Axe </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27636.png"/> <span> Rune of Club </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27637.png"/> <span> Rune of Crafting </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27638.png"/> <span> Rune of Critical </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27639.png"/> <span> Rune of Distance </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27640.png"/> <span> Rune of Fist </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27641.png"/> <span> Rune of Herbalist </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27642.png"/> <span> Rune of Jewelsmith </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27643.png"/> <span> Rune of Life </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27644.png"/> <span> Rune of Magic </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27645.png"/> <span> Rune of Mana </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27646.png"/> <span> Rune of Mining </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27647.png"/> <span> Rune of Sword </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27648.png"/> <span> Rune of Weaponsmith </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27649.png"/> <span> Rune of Woodcutting </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27741.png"/> <span> Rune of Shield </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27742.png"/> <span> Rune of Fishing </span></div>
</li>
<li class="text-cells">
<div class="text-cells__item">Stats</div>
<div class="text-cells__item">1-3% arcane absorbtion</div>
<div class="text-cells__item">1-3% death absorbtion</div>
<div class="text-cells__item">1-3% drown absorbtion</div>
<div class="text-cells__item">1-3% earth absorbtion</div>
<div class="text-cells__item">1-3% energy absorbtion</div>
<div class="text-cells__item">1-3% fire absorbtion</div>
<div class="text-cells__item">1-3% holy absorbtion</div>
<div class="text-cells__item">1-3% ice absorbtion</div>
<div class="text-cells__item">1-3% physical absorbtion</div>
<div class="text-cells__item">1-3% water absorbtion</div>
<div class="text-cells__item">1-10 arcane element attack</div>
<div class="text-cells__item">1-10 death element attack</div>
<div class="text-cells__item">1-10 earth element attack</div>
<div class="text-cells__item">1-10 energy element attack</div>
<div class="text-cells__item">1-10 fire element attack</div>
<div class="text-cells__item">1-10 holy element attack</div>
<div class="text-cells__item">1-10 ice element attack</div>
<div class="text-cells__item">1-10 water element attack</div>
<div class="text-cells__item">1-3 armorsmith skill</div>
<div class="text-cells__item">1-3 axe skill</div>
<div class="text-cells__item">1-3 club skill</div>
<div class="text-cells__item">1-3 crafting skill</div>
<div class="text-cells__item">1-3 critical / chance skill</div>
<div class="text-cells__item">1-3 distance skill</div>
<div class="text-cells__item">1-3 fist skill</div>
<div class="text-cells__item">1-3 herbalist skill</div>
<div class="text-cells__item">1-3 jewelsmith skill</div>
<div class="text-cells__item">1-3 life leech / life leech amount skill</div>
<div class="text-cells__item">1-3 magic level skill</div>
<div class="text-cells__item">1-3 mana leech / mana leech amount skill</div>
<div class="text-cells__item">1-3 mining skill</div>
<div class="text-cells__item">1-3 sword skill</div>
<div class="text-cells__item">1-3 weaponsmith skill</div>
<div class="text-cells__item">1-3 woodcutting skill</div>
<div class="text-cells__item">1-3 shielding skill</div>
<div class="text-cells__item">1-3 fist skill</div>
</li>
</ul><br/>
<h2>Higher Tier</h2>
<ul class="text-columns" style="grid-template-columns: repeat(2, 1fr) !important;">
<li class="text-cells">
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27685.png"/> <span> Void Sigil of Arcane </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27686.png"/> <span> Void Sigil of Death </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27687.png"/> <span> Void Sigil of Drown </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27688.png"/> <span> Void Sigil of Earth </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27689.png"/> <span> Void Sigil of Energy </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27690.png"/> <span> Void Sigil of Fire </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27691.png"/> <span> Void Sigil of Holy </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27692.png"/> <span> Void Sigil of Ice </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27693.png"/> <span> Void Sigil of Physical </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27694.png"/> <span> Void Sigil of Water </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27695.png"/> <span> Catalyst of Primal Arcane </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27696.png"/> <span> Catalyst of Primal Death </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27697.png"/> <span> Catalyst of Primal Earth </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27698.png"/> <span> Catalyst of Primal Energy </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27699.png"/> <span> Catalyst of Primal Fire </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27700.png"/> <span> Catalyst of Primal Holy </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27701.png"/> <span> Catalyst of Primal Ice </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27702.png"/> <span> Catalyst of Primal Water </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27703.png"/> <span> Runic Codex of Armorsmith </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27704.png"/> <span> Runic Codex of Axe </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27705.png"/> <span> Runic Codex of Club </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27706.png"/> <span> Runic Codex of Crafting </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27707.png"/> <span> Runic Codex of Critical </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27708.png"/> <span> Runic Codex of Distance </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27709.png"/> <span> Runic Codex of Fist </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27710.png"/> <span> Runic Codex of Herbalist </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27711.png"/> <span> Runic Codex of Jewelsmith </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27712.png"/> <span> Runic Codex of Life </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27713.png"/> <span> Runic Codex of Magic </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27714.png"/> <span> Runic Codex of Mana </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27715.png"/> <span> Runic Codex of Mining </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27716.png"/> <span> Runic Codex of Sword </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27717.png"/> <span> Runic Codex of Weaponsmith </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27718.png"/> <span> Runic Codex of Woodcutting </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27743.png"/> <span> Runic Codex of Shielding </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27744.png"/> <span> Runic Codex of Fishing </span></div>
</li>
<li class="text-cells">
<div class="text-cells__item">4-6% arcane absorbtion</div>
<div class="text-cells__item">4-6% death absorbtion</div>
<div class="text-cells__item">4-6% drown absorbtion</div>
<div class="text-cells__item">4-6% earth absorbtion</div>
<div class="text-cells__item">4-6% energy absorbtion</div>
<div class="text-cells__item">4-6% fire absorbtion</div>
<div class="text-cells__item">4-6% holy absorbtion</div>
<div class="text-cells__item">4-6% ice absorbtion</div>
<div class="text-cells__item">4-6% physical absorbtion</div>
<div class="text-cells__item">4-6% water absorbtion</div>
<div class="text-cells__item">11-15 arcane element attack</div>
<div class="text-cells__item">11-15 death element attack</div>
<div class="text-cells__item">11-15 earth element attack</div>
<div class="text-cells__item">11-15 energy element attack</div>
<div class="text-cells__item">11-15 fire element attack</div>
<div class="text-cells__item">11-15 holy element attack</div>
<div class="text-cells__item">11-15 ice element attack</div>
<div class="text-cells__item">11-15 water element attack</div>
<div class="text-cells__item">4-6 armorsmith skill</div>
<div class="text-cells__item">4-6 axe skill</div>
<div class="text-cells__item">4-6 club skill</div>
<div class="text-cells__item">4-6 crafting skill</div>
<div class="text-cells__item">4-6 critical / chance skill</div>
<div class="text-cells__item">4-6 distance skill</div>
<div class="text-cells__item">4-6 fist skill</div>
<div class="text-cells__item">4-6 herbalist skill</div>
<div class="text-cells__item">4-6 jewelsmith skill</div>
<div class="text-cells__item">4-6 life leech / life leech amount skill</div>
<div class="text-cells__item">4-6 magic level skill</div>
<div class="text-cells__item">4-6 mana leech / mana leech amount skill</div>
<div class="text-cells__item">4-6 mining skill</div>
<div class="text-cells__item">4-6 sword skill</div>
<div class="text-cells__item">4-6 weaponsmith skill</div>
<div class="text-cells__item">4-6 woodcutting skill</div>
<div class="text-cells__item">4-6 shielding skill</div>
<div class="text-cells__item">4-6 fishing skill</div>
</li>
</ul><br/>
<h2>Remover Scrolls</h2>
<ul class="text-columns" style="grid-template-columns: repeat(2, 1fr) !important;">
<li class="text-cells">
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27650.png"/> <span> Scroll of Purification Arcane resistance </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27651.png"/> <span> Scroll of Purification Arcane element </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27652.png"/> <span> Scroll of Purification Armorsmith skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27653.png"/> <span> Scroll of Purification Axe skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27654.png"/> <span> Scroll of Purification Club skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27655.png"/> <span> Scroll of Purification Crafting skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27656.png"/> <span> Scroll of Purification Critical skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27657.png"/> <span> Scroll of Purification Death resistance</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27658.png"/> <span> Scroll of Purification Death element</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27659.png"/> <span> Scroll of Purification Distance skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27660.png"/> <span> Scroll of Purification Drown resistance</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27661.png"/> <span> Scroll of Purification Earth resistance</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27662.png"/> <span> Scroll of Purification Earth element</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27663.png"/> <span> Scroll of Purification Energy resistance</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27664.png"/> <span> Scroll of Purification Energy element</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27665.png"/> <span> Scroll of Purification Fire resistance</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27666.png"/> <span> Scroll of Purification Fire element</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27667.png"/> <span> Scroll of Purification Fist skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27668.png"/> <span> Scroll of Purification Herbalist skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27669.png"/> <span> Scroll of Purification Holy resistance</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27670.png"/> <span> Scroll of Purification Holy element</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27671.png"/> <span> Scroll of Purification Ice resistance</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27672.png"/> <span> Scroll of Purification Ice element</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27673.png"/> <span> Scroll of Purification Jewelsmith skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27674.png"/> <span> Scroll of Purification Life</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27675.png"/> <span> Scroll of Purification Magic skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27676.png"/> <span> Scroll of Purification Mana</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27677.png"/> <span> Scroll of Purification Mining skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27678.png"/> <span> Scroll of Purification Physical resistance</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27679.png"/> <span> Scroll of Purification Shielding skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27680.png"/> <span> Scroll of Purification Sword skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27681.png"/> <span> Scroll of Purification Water resistance</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27682.png"/> <span> Scroll of Purification Water element</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27683.png"/> <span> Scroll of Purification Weaponsmith skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27684.png"/> <span> Scroll of Purification Woodcutting skill</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27745.png"/> <span> Scroll of Purification Fishing skill</span></div>
</li>
<li class="text-cells">
<div class="text-cells__item">Removes the absorbtion</div>
<div class="text-cells__item">Removes the element</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the absorbtion</div>
<div class="text-cells__item">Removes the element</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the absorbtion</div>
<div class="text-cells__item">Removes the absorbtion</div>
<div class="text-cells__item">Removes the element</div>
<div class="text-cells__item">Removes the absorbtion</div>
<div class="text-cells__item">Removes the element</div>
<div class="text-cells__item">Removes the absorbtion</div>
<div class="text-cells__item">Removes the element</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the absorbtion</div>
<div class="text-cells__item">Removes the element</div>
<div class="text-cells__item">Removes the absorbtion</div>
<div class="text-cells__item">Removes the element</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the absorbtion</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the absorbtion</div>
<div class="text-cells__item">Removes the element</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the skill</div>
<div class="text-cells__item">Removes the skill</div>
</li>
</ul><br/>
<h2>Corruption Scrolls</h2>
<ul class="text-columns" style="grid-template-columns: 178px 1fr !important;">
<li class="text-cells">
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27734.png"/> <span> Seal of Corruption  </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27735.png"/> <span> Seal of Void Corruption </span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27736.png"/> <span> Seal of Ancient Corruption</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27737.png"/> <span> Seal of Infernal Corruption</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27739.png"/> <span> Seal Infernal Protection</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27738.png"/> <span> Fractured Seal</span></div>
<div class="text-cells__item"><img alt="icon" class="aboutitem" src="template/img/corruption/27740.png"/> <span> Seal Fractured Protection</span></div>
</li>
<li class="text-cells">
<div class="text-cells__item">Can corrupt an item that has 7 inscribed stats. Sucess +5% all stats or -5% all stats if fail.</div>
<div class="text-cells__item">Can turn a corrupted item in to a void corrupted item. 70% Chances +5% all stats, 25% Chances +10% all stats and 5% chances -10% all stats.</div>
<div class="text-cells__item">Can turn a void corrupted item in to an ancient corrupted item. It has 50% chance to sucess or reset totally the item. Increment one random stat x1.35 (+35%).</div>
<div class="text-cells__item">Can turn a ancient corrupted item in to an infernal corrupted item. It has 50% chance to sucess or broke's the item, use a seal infernal protection to avoid broke the item. Duplicates one stat randomly and adds a new line of stat random.</div>
<div class="text-cells__item">Item used on backpack to avoid broke attempting to corrupt an ancient corruption item in to an infernal corrupted item.</div>
<div class="text-cells__item">Can turn an infernal corrupted item in to a fractured item. It has 30% chance of sucess or broke's the item, use a seal fractured protection to avoid broke the item. Adds a new line of stat random. If used on a weapon it unlocks a new spell requiring the weapon to use it.</div>
<div class="text-cells__item">Item used on backpack to avoid broke attempting to corrupt an infernal corrupted item into a fractured item.</div>
</li>
</ul>
<br/>
<h2>Some Videos:</h2>
<li><span></span><p>New Ressources that can be founded:</p></li>
<div class="video-container">
<video autoplay="" controls="" loop="" muted="" width="100%">
<source src="template/img/corruption/ressources.mp4" type="video/mp4"/>
														Your browser does not support the video tag.
													</video>
</div>
<li><span></span><p>New items gathered from ressources:</p></li>
<div class="video-container">
<video autoplay="" controls="" loop="" muted="" width="100%">
<source src="template/img/corruption/ressources_gather.mp4" type="video/mp4"/>
														Your browser does not support the video tag.
													</video>
</div>
<li><span></span><p>New Sigils, Scrolls, Catalyst and Runes from Crafting:</p></li>
<div class="video-container">
<video autoplay="" controls="" loop="" muted="" width="100%">
<source src="template/img/corruption/crafting_items.mp4" type="video/mp4"/>
														Your browser does not support the video tag.
													</video>
</div>
<li><span></span><p>New monsters:</p></li>
<div class="video-container">
<video autoplay="" controls="" loop="" muted="" width="100%">
<source src="template/img/corruption/monsters.mp4" type="video/mp4"/>
														Your browser does not support the video tag.
													</video>
</div>
<li><span></span><p>New Bosses:</p></li>
<div class="video-container">
<video autoplay="" controls="" loop="" muted="" width="100%">
<source src="template/img/corruption/Bosses2.mp4" type="video/mp4"/>
														Your browser does not support the video tag.
													</video>
</div>
<li><span></span><p>New Hyper Bosses:</p></li>
<div class="video-container">
<video autoplay="" controls="" loop="" muted="" width="100%">
<source src="template/img/corruption/Bosses_Hyper.mp4" type="video/mp4"/>
														Your browser does not support the video tag.
													</video>
</div>
<li><span></span><p>New Items Set with loot of Hyper Bosses:</p></li>
<div class="video-container">
<video autoplay="" controls="" loop="" muted="" width="100%">
<source src="template/img/corruption/items.mp4" type="video/mp4"/>
														Your browser does not support the video tag.
													</video>
</div>
<li><span></span><p>New Spells unlocked after corrupted a fractured weapon depending on class:</p></li>
<div class="video-container">
<video autoplay="" controls="" loop="" muted="" width="100%">
<source src="template/img/corruption/corrupted_spells.mp4" type="video/mp4"/>
														Your browser does not support the video tag.
													</video>
</div>
<li><span></span><p>Corruption on a new item example:</p></li>
<div class="video-container">
<video autoplay="" controls="" loop="" muted="" width="100%">
<source src="template/img/corruption/corruption_item.mp4" type="video/mp4"/>
														Your browser does not support the video tag.
													</video>
</div>
<br/>
<br/>
</div>

</div>
</div></div></div>
<div class="wiki-panel <?php echo $wikiTab === 'expansions' ? 'is-active' : ''; ?>" id="panel-expansions">
<div class="wiki-layout">
<div class="wiki-sidebar-wrap is-open" data-wiki-sidebar>
<div class="wiki-sidebar-header"><span class="wiki-sidebar-title">Expansions</span><button type="button" class="wiki-collapse-btn" data-wiki-collapse>☰</button></div>
<div class="wiki-sidebar"><ul class="wiki-sidebar-list">
<li><button type="button" class="wiki-nav-btn is-active" data-wiki-target="expansions-isles">
<span class="wiki-nav-icon"><i class="ra ra-dragon" style="color:white;font-size:32px;"></i></span>
<span class="wiki-nav-main"><span class="wiki-nav-title">The Isles</span><span class="wiki-nav-subtitle">New Zones</span></span>
</button></li>
<li><button type="button" class="wiki-nav-btn" data-wiki-target="expansions-expeditions">
<span class="wiki-nav-icon"><i class="ra ra-two-dragons" style="color:white;font-size:32px;"></i></span>
<span class="wiki-nav-main"><span class="wiki-nav-title">Expeditions</span><span class="wiki-nav-subtitle">New Isles</span></span>
</button></li>
<li><button type="button" class="wiki-nav-btn" data-wiki-target="expansions-waystone">
<span class="wiki-nav-icon"><i class="ra ra-rune-stone" style="color:white;font-size:32px;"></i></span>
<span class="wiki-nav-main"><span class="wiki-nav-title">Waystone</span><span class="wiki-nav-subtitle">New T1-T6 Maps</span></span>
</button></li>
</ul></div></div>
<div class="wiki-content-wrap">
<div class="wiki-entry is-active" id="entry-expansions-isles">

<div class="text-box">
<h1 class="info-title heading-title">The Isles, New Zones, New Monsters, World Bosses</h1>
</div>
<div class="info__line"></div>
<div class="text-box">
<center>
<p>	There is <span class="yellowcolor">47 New Monsters</span>, recommended for levels 400+ that can be founded on new isles.
											<br/><br/>
												There is <span class="yellowcolor">10 New World Bosses</span>, each one is unique, can loot Cards for the new system <b>Deck</b>, new <b>Ships</b> looktype, new items (jungle, eldritch, naga, and new quivers ), <b>Badges</b>, upgrades ressources, and more.
												<br/>
												The worldbosses appear on a specific day with a specific time, they are extremely strong, we recommend going there only in party at least with 2 players.<br/>
												A boss bar will appear while a boss will be near the player.<br/>
												The isles in a rpg way, can be explored taking your ship and navigating from the port on any city.
											</p>
<center><img alt="" class="img-responsive" src="template/img/isles/boss_bar.png"/></center>
<br/>
<div class="info__line"></div>
<h1 class="info-title heading-title">World Bosses Information &amp; Timers</h1>
</center>
</div>
<div class="text-box">
<div class="text-table">
<ul class="text-columns" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/isles/aquatic.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Aquatic Overlord Thalassa
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/isles/aquatic.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Aquatic Overlord Thalassa</span></div>
<div class="text-cells__item">Spawn: <span>Appears on the Isle at west of Venore.</span></div>
<div class="text-cells__item">Spawn Time: <span>Monday at 16:00</span></div>
<div class="text-cells__item">Life: <span>1.000.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/isles/azazel.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Azazel The Infernal Seraph
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/isles/azazel.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Azazel The Infernal Seraph</span></div>
<div class="text-cells__item">Spawn: <span>Appears on the Isle at west of Edron.</span></div>
<div class="text-cells__item">Spawn Time: <span>Friday at 20:00</span></div>
<div class="text-cells__item">Life: <span>1.000.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/isles/darktul.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Drak'thul The Void Sovereign
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/isles/darktul.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Drak'thul The Void Sovereign</span></div>
<div class="text-cells__item">Spawn: <span>Appears on the Isle at south of Yalahar.</span></div>
<div class="text-cells__item">Spawn Time: <span>Tuesday at 20:00</span></div>
<div class="text-cells__item">Life: <span>1.000.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/isles/dreadbone.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Dreadbone The Eternal
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/isles/dreadbone.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Dreadbone The Eternal</span></div>
<div class="text-cells__item">Spawn: <span>Appears on the Isle to north west of Yalahar with Mortis The Sovereign.</span></div>
<div class="text-cells__item">Spawn Time: <span>Saturday at 12:00</span></div>
<div class="text-cells__item">Life: <span>1.000.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/isles/azazel.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Mortis The Sovereign
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/isles/azazel.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Mortis The Sovereign</span></div>
<div class="text-cells__item">Spawn: <span>Appears on the Isle to north west of Yalahar with Dreadbone The Eternal.</span></div>
<div class="text-cells__item">Spawn Time: <span>Saturday at 12:00</span></div>
<div class="text-cells__item">Life: <span>1.000.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/isles/dreadscale.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Dreadscale The Ancient
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/isles/dreadscale.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Dreadscale The Ancient</span></div>
<div class="text-cells__item">Spawn: <span>Appears on the Isle to north of Carlin.</span></div>
<div class="text-cells__item">Spawn Time: <span>Monday at 20:00</span></div>
<div class="text-cells__item">Life: <span>1.000.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/isles/gorgul.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Gor'gul The Frienzied
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/isles/gorgul.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Gor'gul The Frienzied</span></div>
<div class="text-cells__item">Spawn: <span>Appears on the Isle at North East of Yalahar, North West of Svargrond.</span></div>
<div class="text-cells__item">Spawn Time: <span>Sunday at 20:00</span></div>
<div class="text-cells__item">Life: <span>1.000.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/isles/thalador.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Thalador The Stormbringer
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/isles/thalador.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Thalador The Stormbringer</span></div>
<div class="text-cells__item">Spawn: <span>Appears on the Isle at North West of Rookgaard.</span></div>
<div class="text-cells__item">Spawn Time: <span>Wednesday at 20:00</span></div>
<div class="text-cells__item">Life: <span>1.000.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/isles/tymagron.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Tymagron The Earthshaker
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/isles/tymagron.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Tymagron The Earthshaker</span></div>
<div class="text-cells__item">Spawn: <span>Appears on the Isle at south of Svargrond.</span></div>
<div class="text-cells__item">Spawn Time: <span>Saturday at 20:00</span></div>
<div class="text-cells__item">Life: <span>1.000.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/isles/vorondor.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Vorondor The Eternal Flames
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/isles/vorondor.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Vorondor The Eternal Flames</span></div>
<div class="text-cells__item">Spawn: <span>Appears on the Isle at north of Venore.</span></div>
<div class="text-cells__item">Spawn Time: <span>Thursday at 20:00</span></div>
<div class="text-cells__item">Life: <span>1.000.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
</ul>
<br/>
<div class="info__line"></div>
<h1 class="info-title heading-title">Media</h1>
<center>
<img class="img-responsive" src="template/img/isles/aquatic_overlord_thalassa.png"/><br/>
<img class="img-responsive" src="template/img/isles/azazel_infernal_seraph.png"/><br/>
<img class="img-responsive" src="template/img/isles/draktul_the_void_sovereign.png"/><br/>
<img class="img-responsive" src="template/img/isles/dread_scale_the_ancient.png"/><br/>
<img class="img-responsive" src="template/img/isles/mortis_dreadbone.png"/><br/>
<img class="img-responsive" src="template/img/isles/tahalador_the_stormbringer.png"/><br/>
<img class="img-responsive" src="template/img/isles/gorgul-boss.png"/><br/>
<img class="img-responsive" src="template/img/isles/tymagron-boss.png"/><br/>
<img class="img-responsive" src="template/img/isles/vorondor_eternal_flames.png"/>
</center>
</div>
</div>

</div>
<div class="wiki-entry" id="entry-expansions-expeditions">

<div class="text-box">
<h1 class="info-title heading-title">Expeditions</h1>
<div class="info__line"></div>
<h4 style="text-align:center;">Missions, Reputation, Coins, New Sets, Belts &amp; Gloves, New Monsters &amp; Bosses!</h4>
<p class="yellowcolor">Recomended Level: 500+</p><br/>
<p>Expeditions, are quests that can be done each day on the isles, npc will appear on a certain hour of day, missions will be unlocked and you can gain reputation doing missions, but also coins of each reputation.
											There is 5 New Isles:<br/></p>
<li><p>Vyrsk Island: Main one, the central isle you will find depot, where under the depot you will find the Npc Reputation for of each isle, you can buy items exchanging your coins earned by doing missions.</p></li>
<li><p>Dorn Isle</p></li>
<li><p>Frosthold Isle</p></li>
<li><p>Hallowfall Isle</p></li>
<li><p>Vardenfell Isle</p></li>
<p>Each isle has many different new monsters, vardenfell it includes new ones like arbaziloth, etc. 
											There is <b class="yellowcolor">6 New Bosses</b>, each one is unique, and have their own custom mechanic.</p>
<p>To Reach the last Boss , accessed by the Teleport on Depot of Vyrsk you need Kill 5 of each bosses located on the isles, appearing many times per day.</p>
<p>To acess to the <b>Expeditions</b> you need travel from Thais Boat to <b class="yellowcolor">Vyrsk Island</b>.<br/></p>
<center><img alt="" class="img-responsive" src="template/img/expeditions/expedition_map.png"/></center><br/>
<br/>
<div class="info__line"></div>
<h4 style="text-align:center;">New Bosses:</h4>
<div class="text-table">
<ul class="text-columns" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/expeditions/1.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Ashseer Dravok
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/expeditions/1.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Ashseer Dravok</span></div>
<div class="text-cells__item">Spawn: <span>Appears on Dorn Isle.</span></div>
<div class="text-cells__item">Spawn Time: <span>All days 08:00 and 16:00</span></div>
<div class="text-cells__item">Life: <span>700.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/expeditions/2.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Icebound Thryssan
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/expeditions/2.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Icebound Thryssan</span></div>
<div class="text-cells__item">Spawn: <span>Appears on Frosthold Isle.</span></div>
<div class="text-cells__item">Spawn Time: <span>All days at 11:00 and 21:00</span></div>
<div class="text-cells__item">Life: <span>700.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/expeditions/3.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Voidweaver Krythax
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/expeditions/3.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Voidweaver Krythax</span></div>
<div class="text-cells__item">Spawn: <span>Appears on Vyrsk Isle.</span></div>
<div class="text-cells__item">Spawn Time: <span>All days at 14:00 and 19:00</span></div>
<div class="text-cells__item">Life: <span>700.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/expeditions/4.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Tidebreaker Voryn
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/expeditions/4.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Tidebreaker Voryn</span></div>
<div class="text-cells__item">Spawn: <span>Appears on Vardenfell Isle.</span></div>
<div class="text-cells__item">Spawn Time: <span>All days at 13:00 and 20:00</span></div>
<div class="text-cells__item">Life: <span>700.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/expeditions/5.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Hollowborn Zepharix
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/expeditions/5.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Hollowborn Zepharix</span></div>
<div class="text-cells__item">Spawn: <span>Appears on Hallowfall Isle.</span></div>
<div class="text-cells__item">Spawn Time: <span>All days at 09:00 and 17:00</span></div>
<div class="text-cells__item">Life: <span>700.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/expeditions/6.png" style="width:22px;height:22px;object-fit:contain;"/> 
													Emberlord Zanarth
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/expeditions/6.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Emberlord Zanarth</span></div>
<div class="text-cells__item">Spawn: <span>Appears on Vyrsk Island (Teleport on Depot)</span></div>
<div class="text-cells__item">Spawn Time: <span>Monday at 20:00</span></div>
<div class="text-cells__item">Life: <span>1.500.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Badges first tier, Cards &amp; Legendary Cards Lv1, Upgrade Ressources, Ships.</span></div>
</li>
</ul>
</div>
<div class="info__line"></div>
</div>

</div>
<div class="wiki-entry" id="entry-expansions-waystone">

<div class="text-box">
<h1 class="info-title heading-title">Waystone Maps</h1>
<div class="info__line"></div>
<h4 style="text-align:center;">New System directly from your house!</h4>
<p class="yellowcolor">Recomended Level: 500+</p><br/>
<p>What is Waystone System ? Waystone System, it's a system implemented inside the houses, in order to visit and achieve the waystone maps, kill the boss and earn rewards, you will need a <b class="yellowcolor"> Waystone Portal Device </b> that you can get by completing the quest at right of Thais depot from <b class="yellowcolor">Illetrya</b>. <br/></p>
<p>After completing the quest you will receive a <b class="yellowcolor"> Waystone Device </b> place it on your house, on the middle of the room or in a corner, then open it, you will require a waystone rune in order to travel to your first map.</p>
<p>Waystone runes are looted from any monster, they are random, from Tier 1 to Tier 6.</p>
<p>With this, we will have a new module called Character Upgrades, you will be able to upgrade element stats for your character, but also skills, rarity finder, rarity loot, experience and others, using orbs looted from waystone maps, see at the bottom the information.</p>
<p>There is 4 new sets, from left: Myst Regalia (Sorc, Druids &amp; Illusionist), Seraphic (Paladins), Dawnforge (Knights), Molten (Monks).</p>
<center>
<img src="template/img/waystone/sets.gif" style="object-fit:cover;border-radius:8px;display:block;"/>
</center><br/>
<center>
<p>Place the waystone device at your house:</p>
<img src="template/img/waystone/waystone_device.png" style="object-fit:cover;border-radius:8px;display:block;"/>
<br/>
<p>Open it and place a wayston tier on the panel, in order to open a portal, you can also choice a portal visual, can be purchased on store in game.</p>
<img src="template/img/waystone/waystone_tier_place.png" style="object-fit:cover;border-radius:8px;display:block;"/> <br/>
<p>Press Open Portal, portals will appear, enter on the map, solo or in party.</p>
<img src="template/img/waystone/portals_waystone.png" style="object-fit:cover;border-radius:8px;display:block;"/> <br/>
<p>Once inside you have 30 minuts to defeat monsters and find the boss.</p>
<img src="template/img/waystone/enter_1.png" style="object-fit:cover;border-radius:8px;display:block;"/> <br/>
<p>Good Luck!</p>
<img src="template/img/waystone/enter_2.png" style="object-fit:cover;border-radius:8px;display:block;"/>
<br/>
<p>Increase your character stats!</p>
<img src="template/img/waystone/characterupgrade1.png" style="object-fit:cover;border-radius:8px;display:block;"/>
<br/>
<p>Increase your character stats!</p>
<img src="template/img/waystone/characterupgrade2.png" style="object-fit:cover;border-radius:8px;display:block;"/>
</center>
<div class="info__line"></div>
<h4 style="text-align:center;">Bosses of Waystone Maps:</h4>
<div class="text-table">
<ul class="text-columns" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/waystone/bossT1.gif" style="width:22px;height:22px;object-fit:contain;"/> 
													Tier I
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/waystone/bossT1.gif" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Vorgrun of the Jade Fangs</span></div>
<div class="text-cells__item">Life: <span>250.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Stoic Items, New Ressources for craft new sets, Orbs.</span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/waystone/bossT2.gif" style="width:22px;height:22px;object-fit:contain;"/> 
													Tier II
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/waystone/bossT2.gif" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Fractalis the Shardmaw</span></div>
<div class="text-cells__item">Life: <span>300.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Stoic Items, New Ressources for craft new sets, Orbs. <span data-tip-def="Chance of loot increased" class="wiki-tip-trigger">[?]</span></span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/waystone/bossT3.gif" style="width:22px;height:22px;object-fit:contain;"/> 
													Tier III
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/waystone/bossT3.gif" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Frostreaver the Glacialblade</span></div>
<div class="text-cells__item">Life: <span>400.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Stoic Items, New Ressources for craft new sets, Orbs. <span data-tip-def="Chance of loot increased" class="wiki-tip-trigger">[?]</span></span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/waystone/bossT4.gif" style="width:22px;height:22px;object-fit:contain;"/> 
													Tier IV
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/waystone/bossT4.gif" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Rhazgor the Crimson Slayer</span></div>
<div class="text-cells__item">Life: <span>400.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Stoic Items, New Ressources for craft new sets, Orbs. <span data-tip-def="Chance of loot increased" class="wiki-tip-trigger">[?]</span></span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/waystone/bossT5.gif" style="width:22px;height:22px;object-fit:contain;"/> 
													Tier V
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/waystone/bossT5.gif" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Morgrath the Umbral Archmage</span></div>
<div class="text-cells__item">Life: <span>450.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Stoic Items, New Ressources for craft new sets, Orbs. <span data-tip-def="Chance of loot increased" class="wiki-tip-trigger">[?]</span></span></div>
</li>
<li class="text-cells" style="background:var(--box-inner-bg);border:1px solid var(--box-inner-border);border-radius:10px;padding:12px;">
<div class="text-cells__head" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
<img alt="icon" class="aboutitem" src="template/img/waystone/bossT6.gif" style="width:22px;height:22px;object-fit:contain;"/> 
													Tier VI
													</div>
<div class="text-cells__item" style="margin-bottom:10px;">
<img src="template/img/waystone/bossT6.gif" style="object-fit:cover;border-radius:8px;display:block;"/>
</div>
<div class="text-cells__item">Name: <span>Nyxdrath the Midnight Terror</span></div>
<div class="text-cells__item">Life: <span>1.000.000 HP</span></div>
<div class="text-cells__item">Loot: <span>Stoic Items, New Ressources for craft new sets, Orbs. <span data-tip-def="Chance of loot increased" class="wiki-tip-trigger">[?]</span></span></div>
</li>
</ul>
</div>
<div class="info__line"></div>
<h4 style="text-align:center;">Items:</h4>
<div class="text-table">
<ul class="text-columns" style="grid-template-columns: repeat(2, 1fr) !important;">
<li class="text-cells">
<div class="text-cells__head">Item</div>
<div class="text-cells__item"><img src="template/img/waystone/item_27189_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
<div class="text-cells__item"><img src="template/img/waystone/item_27190_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
<div class="text-cells__item"><img src="template/img/waystone/item_27191_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
<div class="text-cells__item"><img src="template/img/waystone/item_27192_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
<div class="text-cells__item"><img src="template/img/waystone/item_27193_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
<div class="text-cells__item"><img src="template/img/waystone/item_27194_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
<div class="text-cells__item"><img src="template/img/waystone/item_27195_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
<div class="text-cells__item"><img src="template/img/waystone/item_27196_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
<div class="text-cells__item"><img src="template/img/waystone/item_27197_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
<div class="text-cells__item"><img src="template/img/waystone/item_27198_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
<div class="text-cells__item"><img src="template/img/waystone/item_27199_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
<div class="text-cells__item"><img src="template/img/waystone/item_27200_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
<div class="text-cells__item"><img src="template/img/waystone/item_27201_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
<div class="text-cells__item"><img src="template/img/waystone/item_27202_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
<div class="text-cells__item"><img src="template/img/waystone/item_27203_.png" style="object-fit:cover;border-radius:8px;display:block;"/></div>
</li>
<li class="text-cells">
<div class="text-cells__head">Description</div>
<div class="text-cells__item">Wayston Tier I, used to open a portal to Tier I map.</div>
<div class="text-cells__item">Wayston Tier II, used to open a portal to Tier II map.</div>
<div class="text-cells__item">Wayston Tier III, used to open a portal to Tier III map.</div>
<div class="text-cells__item">Wayston Tier IV, used to open a portal to Tier IV map.</div>
<div class="text-cells__item">Wayston Tier V, used to open a portal to Tier V map.</div>
<div class="text-cells__item">Wayston Tier VI, used to open a portal to Tier VI map.</div>
<div class="text-cells__item">Orb of Transmutation, used on character upgrades.</div>
<div class="text-cells__item">Orb of Augmentation, used on character upgrades.</div>
<div class="text-cells__item">Regal Orb, used on character upgrades.</div>
<div class="text-cells__item">Vaal Orb, used on character upgrades.</div>
<div class="text-cells__item">Exalted Orb, used on character upgrades.</div>
<div class="text-cells__item">Chaos Orb, used on character upgrades, also used to reroll a rarity stats of an item.</div>
<div class="text-cells__item">Divine Orb, used on character upgrades, also used to insert rarity stats to an item.</div>
<div class="text-cells__item">Angel Orb, used on character upgrades.</div>
<div class="text-cells__item">Mirror of Kalandra, used on character upgrades.</div>
</li>
</ul>
</div>
<div class="info__line"></div>
</div>

</div>
</div></div></div>

            </div>
        </div>
    </div>
</div>
</div>
</div>
<script>
window.addEventListener('load', function () {
    const wikiContent = document.getElementById('wikiPageContent');
    const loader = document.getElementById('sitePageLoader');

    if (wikiContent) {
        wikiContent.classList.add('is-ready');
    }

    if (loader) {
        loader.classList.remove('is-visible');
        loader.setAttribute('aria-hidden', 'true');
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-wiki-sidebar]').forEach(function (sidebarWrap) {
        var collapseBtn = sidebarWrap.querySelector('[data-wiki-collapse]');
        if (collapseBtn) {
            collapseBtn.addEventListener('click', function () {
                sidebarWrap.classList.toggle('is-open');
            });
        }
    });

    document.querySelectorAll('.wiki-panel').forEach(function (panel) {
        var buttons = panel.querySelectorAll('.wiki-nav-btn');
        var entries = panel.querySelectorAll('.wiki-entry');

        function activate(targetId) {
            buttons.forEach(function (btn) {
                btn.classList.toggle('is-active', btn.getAttribute('data-wiki-target') === targetId);
            });
            entries.forEach(function (entry) {
                entry.classList.toggle('is-active', entry.id === 'entry-' + targetId);
            });
        }

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                activate(btn.getAttribute('data-wiki-target'));
                if (window.innerWidth <= 980) {
                    var wrap = panel.querySelector('[data-wiki-sidebar]');
                    if (wrap) wrap.classList.remove('is-open');
                }
            });
        });

        panel.querySelectorAll('[data-tab-button],[data-tab-target]').forEach(function(btn){
            btn.addEventListener('click', function(){
                var target = btn.getAttribute('data-tab-button') || btn.getAttribute('data-tab-target');
                if (!target) return;
                var scope = btn.closest('.wiki-entry') || panel;
                scope.querySelectorAll('[data-tab]').forEach(function(el){
                    var match = el.getAttribute('data-tab') === target;
                    el.style.display = match ? '' : 'none';
                    el.classList.toggle('active', match);
                    el.classList.toggle('is-active', match);
                });
                scope.querySelectorAll('[data-tab-button],[data-tab-target]').forEach(function(other){
                    var match = (other.getAttribute('data-tab-button') || other.getAttribute('data-tab-target')) === target;
                    other.classList.toggle('active', match);
                    other.classList.toggle('is-active', match);
                });
            });
        });

        var groupedTabboxes = {};
        panel.querySelectorAll('.tabbox[data-tab-group]').forEach(function(box){
            var group = box.getAttribute('data-tab-group');
            if (!groupedTabboxes[group]) groupedTabboxes[group] = [];
            groupedTabboxes[group].push(box);
        });
        Object.keys(groupedTabboxes).forEach(function(group){
            groupedTabboxes[group].forEach(function(box, index){
                if (!box.classList.contains('active') && !box.classList.contains('is-active')) {
                    box.style.display = index === 0 ? '' : 'none';
                    if (index === 0) {
                        box.classList.add('active');
                    }
                }
            });
        });
    });
});
</script>
<script>
document.querySelectorAll('.aboutitem[data-tip-def]').forEach(function(el) {
    el.addEventListener('mouseenter', function() {
        let tooltip = document.createElement('div');
        tooltip.className = 'wiki-html-tooltip';
        tooltip.innerHTML = el.getAttribute('data-tip-def');
        document.body.appendChild(tooltip);

        const rect = el.getBoundingClientRect();
        tooltip.style.left = (rect.left + rect.width / 2) + 'px';
        tooltip.style.top = (rect.top - 12) + 'px';

        el._tooltip = tooltip;
    });

    el.addEventListener('mouseleave', function() {
        if (el._tooltip) {
            el._tooltip.remove();
            el._tooltip = null;
        }
    });
});
</script>
<script>
document.querySelectorAll('.text-cells__item img[data-tip-def]').forEach(function(img) {
    const tip = img.getAttribute('data-tip-def');
    img.parentElement.setAttribute('data-tip-def', tip);
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('[data-open-tab-group]').forEach(function(button) {
        button.addEventListener('click', function() {
            const tab = this.getAttribute('data-open-tab');
            const group = this.getAttribute('data-open-tab-group');

            document.querySelectorAll('[data-open-tab-group="' + group + '"]').forEach(function(el) {
                el.classList.remove('is-active');
                el.removeAttribute('data-open-tab-active');
            });

            document.querySelectorAll('[data-tab-group="' + group + '"]').forEach(function(el) {
                el.classList.remove('is-active');
                el.style.display = 'none';
            });

            this.classList.add('is-active');
            this.setAttribute('data-open-tab-active', 'true');

            const target = document.querySelector('[data-tab="' + tab + '"][data-tab-group="' + group + '"]');
            if (target) {
                target.classList.add('is-active');
                target.style.display = 'block';
            }
        });
    });

    document.querySelectorAll('[data-tab-group]').forEach(function(el) {
        el.style.display = 'none';
    });

    document.querySelectorAll('[data-open-tab-active="true"]').forEach(function(activeButton) {
        activeButton.classList.add('is-active');

        const tab = activeButton.getAttribute('data-open-tab');
        const group = activeButton.getAttribute('data-open-tab-group');

        const target = document.querySelector('[data-tab="' + tab + '"][data-tab-group="' + group + '"]');
        if (target) {
            target.classList.add('is-active');
            target.style.display = 'block';
        }
    });

});
</script>
<?php include 'layout/overall/footer.php'; ?>
