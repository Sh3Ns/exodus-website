<?php
require_once 'engine/init.php';
if (empty($config['enableGuildDynasty'])) {
    header('Location: index.php');
    exit;
}
include 'layout/overall/header.php';
?>
<style>
.gh-page { display:flex; flex-direction:column; gap:16px; }
.gh-tabs { display:flex; flex-wrap:wrap; gap:6px; padding:6px; background:rgba(93,72,39,.10); border:1px solid var(--box-inner-border); border-radius:10px; box-shadow:inset 0 1px 0 rgba(255,255,255,.35); }
.gh-tab-btn {
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    min-height:34px; padding:8px 14px; border:1px solid #c9b894; border-radius:8px;
    background:linear-gradient(180deg,#efe4cb 0%,#e4d5b3 100%); color:#6a5539;
    text-decoration:none; font-size:12px; font-weight:700; cursor:pointer; transition:all .15s ease;
    box-shadow:inset 0 1px 0 rgba(255,255,255,.45);
}
.gh-tab-btn:hover { background:linear-gradient(180deg,#f4ead5 0%,#eadcbe 100%); border-color:#b79d67; color:#4f3f2b; }
.gh-tab-btn.is-active { background:linear-gradient(180deg,#cfa949 0%,#bb9132 100%); border-color:#a97f24; color:#fff8e8; box-shadow:inset 0 1px 0 rgba(255,255,255,.25),0 4px 10px rgba(0,0,0,.18); }
.gh-panel { display:none; }
.gh-panel.is-active { display:block; }
.gh-card, .gh-list, .gh-table, .gh-note {
    background:linear-gradient(180deg, rgba(255,255,255,.18) 0%, rgba(0,0,0,.015) 100%), var(--box-inner-bg, #d8c8a8);
    border:1px solid var(--box-inner-border, #b8a47c); border-radius:8px; box-shadow:var(--box-inner-shadow, inset 0 1px 0 rgba(255,255,255,.25));
}
.gh-card, .gh-note { padding:16px; }
.gh-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
.gh-grid-3 { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
.gh-title { margin:0 0 10px; color:#5c4a32; font-size:18px; font-weight:700; }
.gh-subtitle { margin:0 0 8px; color:#8d6b30; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
.gh-card p, .gh-card li, .gh-note p, .gh-note li { color:var(--text,#5c4a32); font-size:13px; line-height:1.6; }
.gh-list ul { list-style:none; margin:0; padding:0; }
.gh-list li { position:relative; padding-left:18px; margin:10px 0; }
.gh-list li::before {
    content:''; position:absolute; left:0; top:.55rem; width:8px; height:8px; border-radius:999px;
    background:#c2922e; box-shadow:0 0 0 2px rgba(194,146,46,.18);
}
.gh-divider { height:1px; margin:16px 0; background:linear-gradient(90deg,transparent 0%,rgba(111,88,52,.18) 10%,rgba(111,88,52,.35) 50%,rgba(111,88,52,.18) 90%,transparent 100%); }
.gh-pills { display:flex; flex-wrap:wrap; gap:8px; margin-top:10px; }
.gh-pill { display:inline-flex; align-items:center; padding:6px 10px; border-radius:999px; background:rgba(189,144,53,.14); border:1px solid rgba(169,127,36,.28); color:#6a5539; font-size:12px; font-weight:700; }
.gh-data-table { width:100%; border-collapse:collapse; overflow:hidden; }
.gh-data-table th, .gh-data-table td { padding:10px 12px; border:1px solid rgba(107,82,46,.14); text-align:left; color:#5c4a32; font-size:12px; vertical-align:top; }
.gh-data-table th { background:rgba(255,255,255,.16); font-weight:700; }
.gh-data-table td strong { color:#5c4a32; }
.gh-mini { font-size:12px; color:#8d6b30; font-weight:700; }
@media (max-width: 1100px) { .gh-grid-3 { grid-template-columns:1fr 1fr; } }
@media (max-width: 900px) { .gh-grid, .gh-grid-3 { grid-template-columns:1fr; } .gh-tab-btn { flex:1 1 calc(50% - 6px); } }
@media (max-width: 640px) { .gh-tab-btn { flex:1 1 100%; } }
</style>
<div class="">
    <div class="PageHeader-module__HZea0q__header ">
        <div class="PageHeader-module__HZea0q__headerContent">
            <h2 class="PageHeader-module__HZea0q__title">Guild Prism &amp; Dynasty Guide</h2>
        </div>
    </div>
    <div class="PageBody-module__OAJIWq__body ">
        <div class="PageBody-module__OAJIWq__content">
            <div class="gh-page">
                <div class="gh-tabs">
                    <button type="button" class="gh-tab-btn is-active" data-gh-tab="overview">Overview</button>
                    <button type="button" class="gh-tab-btn" data-gh-tab="storage">Guild Storage</button>
                    <button type="button" class="gh-tab-btn" data-gh-tab="guild-perks">Guild Perks</button>
                    <button type="button" class="gh-tab-btn" data-gh-tab="guild-quests">Guild Quests</button>
                    <button type="button" class="gh-tab-btn" data-gh-tab="guild-achievements">Guild Achievements</button>
                    <button type="button" class="gh-tab-btn" data-gh-tab="prisms">Prisms</button>
                    <button type="button" class="gh-tab-btn" data-gh-tab="dynasty-core">Dynasty Core</button>
                    <button type="button" class="gh-tab-btn" data-gh-tab="dynasty-perks">Dynasty Perks</button>
                    <button type="button" class="gh-tab-btn" data-gh-tab="event-calendar">Event Calendar</button>
                    <button type="button" class="gh-tab-btn" data-gh-tab="dynasty-quests">Dynasty Quests</button>
                    <button type="button" class="gh-tab-btn" data-gh-tab="dynasty-achievements">Dynasty Achievements</button>
                    <button type="button" class="gh-tab-btn" data-gh-tab="defenders">Defenders</button>
                    <button type="button" class="gh-tab-btn" data-gh-tab="passive-tree">Passive Tree</button>
                </div>

                <div class="gh-panel is-active" data-gh-panel="overview">
                    <div class="gh-grid">
                        <div class="gh-card">
                            <div class="gh-subtitle">Core Idea</div>
                            <h3 class="gh-title">The Guild Hall has two power layers.</h3>
                            <p><b>Prisms</b> are the guild-versus-guild pressure tools. They create immediate PvP objectives, can be attacked by enemy guilds, and let attackers steal part of the prism value if they win.</p>
                            <p><b>Dynasty Cores</b> are the long-term city control tools. They convert ownership into taxes, passive dynasty tokens, dynasty experience, defenders, perks, board quests, and passive-tree progression.</p>
                            <div class="gh-pills">
                                <span class="gh-pill">Prisms = active conflict</span>
                                <span class="gh-pill">Dynasty = city progression</span>
                                <span class="gh-pill">Guild Quests = recurring goals</span>
                                <span class="gh-pill">Achievements = long milestones</span>
                            </div>
                        </div>
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Recommended Reading Order</div>
                            <h3 class="gh-title">How to understand the system fast</h3>
                            <ul>
                                <li>Start with <b>Guild Storage</b> and <b>Guild Perks</b> to understand the base guild progression.</li>
                                <li>Read <b>Guild Quests</b> and <b>Guild Achievements</b> to see how the guild earns rewards and long-term milestones.</li>
                                <li>Read <b>Prisms</b> for direct PvP contesting and stolen-value pressure.</li>
                                <li>Then move into <b>Dynasty Core</b>, <b>Dynasty Perks</b>, <b>Dynasty Quests</b>, <b>Dynasty Achievements</b>, <b>Defenders</b>, and <b>Passive Tree</b>.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="gh-note" style="margin-top:16px;">
                        <div class="gh-subtitle">Important Rule</div>
                        <p>You cannot safely ignore world assets. If a guild still owns an active <b>Prism</b> or an active <b>Dynasty Core</b>, disband is blocked until those assets are cleared. Guild storage must also be emptied first.</p>
                    </div>
                </div>

                <div class="gh-panel" data-gh-panel="storage">
                    <div class="gh-card gh-list">
                        <div class="gh-subtitle">Guild Storage</div>
                        <h3 class="gh-title">Shared guild inventory and upgrade material bank</h3>
                        <ul>
                            <li>Guild Storage is the shared container for guild items and upgrade materials.</li>
                            <li>It is required for several Guild Hall systems because many perks, upgrades, and dynasty systems consume items from storage.</li>
                            <li>Storage size starts at <b>10 slots</b> and grows through the <b>Storage Expansion</b> guild perk.</li>
                            <li>If there are still items inside guild storage, the guild cannot be disbanded.</li>
                            <li>For dynasty systems, core fragments and perk materials are commonly pulled from guild storage during upgrades.</li>
                        </ul>
                    </div>
                </div>

                <div class="gh-panel" data-gh-panel="guild-perks">
                    <div class="gh-card">
                        <div class="gh-subtitle">Guild Perks</div>
                        <h3 class="gh-title">Permanent guild-wide upgrades</h3>
                        <div class="gh-table">
                            <table class="gh-data-table">
                                <thead>
                                    <tr><th>Perk</th><th>Effect</th><th>Unlock</th><th>Max Level</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td><strong>Health Regeneration</strong></td><td>Improves guild members' health regeneration by +2% per level.</td><td>Guild Lv. 5</td><td>10</td></tr>
                                    <tr><td><strong>Mana Regeneration</strong></td><td>Improves guild members' mana regeneration by +2% per level.</td><td>Guild Lv. 10</td><td>10</td></tr>
                                    <tr><td><strong>Critical Chance</strong></td><td>Improves critical strike chance by +1% per level.</td><td>Guild Lv. 15</td><td>5</td></tr>
                                    <tr><td><strong>Critical Damage</strong></td><td>Improves critical strike damage by +1% per level.</td><td>Guild Lv. 15</td><td>5</td></tr>
                                    <tr><td><strong>Monster Damage</strong></td><td>Improves PvE damage against monsters by +2% per level.</td><td>Guild Lv. 15</td><td>10</td></tr>
                                    <tr><td><strong>Physical Protection</strong></td><td>Reduces physical damage taken by 2% per level.</td><td>Guild Lv. 20</td><td>5</td></tr>
                                    <tr><td><strong>Storage Expansion</strong></td><td>Adds +5 guild storage slots per level.</td><td>Guild Lv. 15</td><td>38</td></tr>
                                    <tr><td><strong>Member Expansion</strong></td><td>Adds +5 guild member slots per level.</td><td>Guild Lv. 5</td><td>18</td></tr>
                                    <tr><td><strong>Prism Capacity</strong></td><td>Adds +1 active prism slot per level.</td><td>Guild Lv. 15</td><td>4</td></tr>
                                    <tr><td><strong>Dynasty Capacity</strong></td><td>Adds +1 active dynasty slot per level.</td><td>Guild Lv. 20</td><td>3</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="gh-divider"></div>
                        <p class="gh-mini">Why these perks matter: Storage Expansion and Member Expansion improve guild logistics. Prism Capacity and Dynasty Capacity decide how many world assets your guild can maintain at once. The combat perks improve everyday strength for guild members.</p>
                    </div>
                </div>

                <div class="gh-panel" data-gh-panel="guild-quests">
                    <div class="gh-grid">
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Guild Quests</div>
                            <h3 class="gh-title">Recurring objectives for the whole guild</h3>
                            <ul>
                                <li>The guild quest board rotates through <b>3 daily</b>, <b>3 weekend</b>, and <b>3 monthly</b> quests.</li>
                                <li>These quests are shared guild goals rather than personal missions.</li>
                                <li>They reward the guild with combinations of <b>gold</b>, <b>guild experience</b>, and <b>storage items</b>.</li>
                                <li>Quest sets are designed to push guilds into activity every day, every weekend, and over longer monthly spans.</li>
                            </ul>
                        </div>
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">What Guild Quests Cover</div>
                            <h3 class="gh-title">Main quest families</h3>
                            <ul>
                                <li><b>Combat:</b> kill players with your guild, kill monsters with your guild, or kill bosses with nearby guild members.</li>
                                <li><b>Contribution:</b> donate gold or premium points to strengthen the guild economy.</li>
                                <li><b>Growth:</b> gain guild experience and push the guild level forward.</li>
                                <li><b>Prism Activity:</b> win prism defenses or take part in prism conflict goals.</li>
                                <li><b>Professions:</b> mining, herbalist, woodcutting, fishing, crafting, armorsmith, weaponsmith, and jewelsmith.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="gh-card gh-list" style="margin-top:16px;">
                        <div class="gh-subtitle">Examples</div>
                        <h3 class="gh-title">What a guild quest asks you to do</h3>
                        <ul>
                            <li>Kill a target number of demons, dragons, grim reapers, hydras, or other specific monsters with guildmates nearby.</li>
                            <li>Defeat a boss such as Azazel while a required number of guild players are present.</li>
                            <li>Donate premium points or gold to improve shared guild progression.</li>
                            <li>Complete profession tasks so non-combat players still contribute meaningfully to guild growth.</li>
                        </ul>
                    </div>
                </div>

                <div class="gh-panel" data-gh-panel="guild-achievements">
                    <div class="gh-grid">
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Guild Achievements</div>
                            <h3 class="gh-title">Long-term guild milestones</h3>
                            <ul>
                                <li>Guild achievements are permanent progression milestones rather than rotating quests.</li>
                                <li>They cover growth, contribution, PvP, prism warfare, professions, and guild longevity.</li>
                                <li>Some are hand-crafted unique achievements, while others are threshold ladders that scale up over many ranks.</li>
                                <li>Rewards commonly include guild gold, guild experience, and item rewards, with some special cosmetic or prestige rewards in higher tracks.</li>
                            </ul>
                        </div>
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Main Guild Achievement Categories</div>
                            <h3 class="gh-title">What is tracked</h3>
                            <ul>
                                <li><b>Guild growth:</b> guild level reached, guild member count, guild age in days.</li>
                                <li><b>Contribution:</b> gold contribution total, premium contribution total, quest claims completed.</li>
                                <li><b>Prism warfare:</b> prism attack kills, prism defense kills, prism destructions, and prism loot collected.</li>
                                <li><b>Combat:</b> global player kills, monster kill totals, and boss kills with guild requirements.</li>
                                <li><b>Professions and world systems:</b> dynasty placements, mining, herbalist, woodcutting, crafting, and other profession totals.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="gh-note" style="margin-top:16px;">
                        <div class="gh-subtitle">How to read them</div>
                        <p>Guild quests tell your guild what to do <b>right now</b>. Guild achievements tell your guild what it has built <b>over time</b>. A strong guild usually progresses both at once: short-cycle quests feed rewards, while achievement ladders record the guild's real history.</p>
                    </div>
                </div>

                <div class="gh-panel" data-gh-panel="prisms">
                    <div class="gh-grid">
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Prisms</div>
                            <h3 class="gh-title">Direct guild-vs-guild control points</h3>
                            <ul>
                                <li>Prisms are active guild objectives placed into the world.</li>
                                <li>Enemy guilds can attack them to challenge your control and steal part of the prism value on victory.</li>
                                <li>Prism attacks have preparation time, battle duration, shield time after defense, and reattack cooldowns.</li>
                                <li>The current server setup steals <b>25%</b> of prism value when attackers win.</li>
                            </ul>
                        </div>
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Prism Rules</div>
                            <h3 class="gh-title">Key limits and timings</h3>
                            <ul>
                                <li>Base prism capacity starts at <b>0</b> and must be unlocked through guild perks.</li>
                                <li>Maximum prism capacity is <b>4</b> through guild perk progression.</li>
                                <li>Prism placement cooldown is <b>2 hours</b>.</li>
                                <li>Attack preparation is <b>10 minutes</b>, battle duration is <b>15 minutes</b>, and post-defense shield is <b>6 hours</b>.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="gh-panel" data-gh-panel="dynasty-core">
                    <div class="gh-grid">
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Dynasty Core</div>
                            <h3 class="gh-title">Your guild's long-term city control node</h3>
                            <ul>
                                <li>A Dynasty Core is placed in a city and controls a tax radius around that location.</li>
                                <li>Default radius is <b>80</b> tiles.</li>
                                <li>NPC buy and sell actions inside the radius are taxed, and that value feeds the dynasty system.</li>
                                <li>The core stores gold, passively generates dynasty tokens, and gains dynasty experience.</li>
                                <li>Default passive token generation is <b>1 token per hour</b>.</li>
                            </ul>
                        </div>
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Attack &amp; Collection</div>
                            <h3 class="gh-title">What happens if enemies win</h3>
                            <ul>
                                <li>Dynasty attacks use a 10 minute prepare window and a 15 minute battle window.</li>
                                <li>After a defense win, the city gets a 6 hour shield.</li>
                                <li>If attackers destroy the core, they can steal the stored <b>gold buffer</b> and the stored <b>dynasty tokens</b>.</li>
                                <li>The attack cooldown for the same guild against the same dynasty is <b>7 days</b>.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="gh-card gh-list" style="margin-top:16px;">
                        <div class="gh-subtitle">City Board Quests</div>
                        <h3 class="gh-title">Owner rewards for the placed core</h3>
                        <ul>
                            <li>Small board quests reward the city core with <b>250 dynasty exp</b>, <b>350 guild gold</b>, and <b>1 dynasty token</b>.</li>
                            <li>Medium board quests reward the city core with <b>500 dynasty exp</b>, <b>650 guild gold</b>, and <b>2 dynasty tokens</b>.</li>
                            <li>Large board quests reward the city core with <b>750 dynasty exp</b>, <b>850 guild gold</b>, and <b>3 dynasty tokens</b>.</li>
                            <li>These rewards go to the <b>placed core</b>, not just to the guild row, so board quests directly strengthen the city you own.</li>
                        </ul>
                    </div>
                </div>

                <div class="gh-panel" data-gh-panel="dynasty-perks">
                    <div class="gh-card">
                        <div class="gh-subtitle">Dynasty Perks</div>
                        <h3 class="gh-title">City-specific upgrades and elemental scaling</h3>
                        <div class="gh-table">
                            <table class="gh-data-table">
                                <thead>
                                    <tr><th>Perk</th><th>Type</th><th>Effect</th><th>Max Level</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td><strong>Unlock Defender Knight</strong></td><td>Frontline Defender</td><td>Unlocks and improves how many Knight defenders can be fielded.</td><td>3</td></tr>
                                    <tr><td><strong>Unlock Paladin Defender</strong></td><td>Ranged Defender</td><td>Unlocks and improves how many Paladin defenders can be fielded.</td><td>3</td></tr>
                                    <tr><td><strong>Unlock Mage Defender</strong></td><td>Arcane Defender</td><td>Unlocks and improves how many Mage defenders can be fielded.</td><td>3</td></tr>
                                    <tr><td><strong>Enhanced Physical Element</strong></td><td>Elemental Bonus</td><td>+1% physical damage per level while your guild owns an active dynasty core.</td><td>5</td></tr>
                                    <tr><td><strong>Enhanced Fire Element</strong></td><td>Elemental Bonus</td><td>+1% fire damage per level while your guild owns an active dynasty core.</td><td>5</td></tr>
                                    <tr><td><strong>Enhanced Arcane Element</strong></td><td>Elemental Bonus</td><td>+1% arcane damage per level while your guild owns an active dynasty core.</td><td>5</td></tr>
                                    <tr><td><strong>Enhanced Ice Element</strong></td><td>Elemental Bonus</td><td>+1% ice damage per level while your guild owns an active dynasty core.</td><td>5</td></tr>
                                    <tr><td><strong>Enhanced Earth Element</strong></td><td>Elemental Bonus</td><td>+1% earth damage per level while your guild owns an active dynasty core.</td><td>5</td></tr>
                                    <tr><td><strong>Enhanced Water Element</strong></td><td>Elemental Bonus</td><td>+1% water damage per level while your guild owns an active dynasty core.</td><td>5</td></tr>
                                    <tr><td><strong>Enhanced Energy Element</strong></td><td>Elemental Bonus</td><td>+1% energy damage per level while your guild owns an active dynasty core.</td><td>5</td></tr>
                                    <tr><td><strong>Enhanced Holy Element</strong></td><td>Elemental Bonus</td><td>+1% holy damage per level while your guild owns an active dynasty core.</td><td>5</td></tr>
                                    <tr><td><strong>Enhanced Death Element</strong></td><td>Elemental Bonus</td><td>+1% death damage per level while your guild owns an active dynasty core.</td><td>5</td></tr>
                                    <tr><td><strong>Enhanced Combat Healing</strong></td><td>Elemental Bonus</td><td>+1% combat healing per level while your guild owns an active dynasty core.</td><td>5</td></tr>
                                    <tr><td><strong>Increased Rarity Loot</strong></td><td>Loot Bonus</td><td>+1% rarity loot chance per level while your guild owns an active dynasty core.</td><td>10</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="gh-panel" data-gh-panel="event-calendar">
                    <div class="gh-grid">
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Event Calendar</div>
                            <h3 class="gh-title">Rotating dynasty events with temporary bonuses</h3>
                            <ul>
                                <li>The event calendar rotates special dynasty events that temporarily modify how a guild benefits from its active Dynasty Core.</li>
                                <li>These events are designed to make different days favor different strategies instead of every city behaving the same way all week.</li>
                                <li>Some events push economy, some give direct player EXP, some activate board quests, and some improve passive dynasty token gain.</li>
                                <li>The active event is visible in the dynasty interface so guilds can plan defense, attacks, farming, and quest routing around it.</li>
                            </ul>
                        </div>
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">How To Use It</div>
                            <h3 class="gh-title">Treat it like a planning tool</h3>
                            <ul>
                                <li>Tax-focused events are best used on active trade days where the city is busy.</li>
                                <li>Quest and experience events are strong when your guild wants to push player progression in one session.</li>
                                <li>Board quest days are ideal for feeding both personal rewards and owner rewards into the placed core.</li>
                                <li>Token surge days reward simply keeping the core alive and protected for the full event window.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="gh-card" style="margin-top:16px;">
                        <div class="gh-subtitle">Current Event Types</div>
                        <h3 class="gh-title">What each event does</h3>
                        <div class="gh-table">
                            <table class="gh-data-table">
                                <thead>
                                    <tr><th>Event</th><th>Effect</th><th>Best Use</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Tax Boom</strong></td>
                                        <td>Improves value generated from city tax activity around an active Dynasty Core.</td>
                                        <td>Best when your city has heavy trading, buying, and selling activity.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Guild EXP Boost</strong></td>
                                        <td>Gives guild members with an active Dynasty Core a <b>+100% player EXP bonus everywhere</b> during the event window.</td>
                                        <td>Best for hunting, leveling characters fast, and combining progression with city ownership.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pet Double EXP</strong></td>
                                        <td>Boosts pet experience gains while the event is active.</td>
                                        <td>Best when members want to progress pets while still using the city system.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>World Quest Board</strong></td>
                                        <td>Activates the dynasty board quest system for the current city rotation.</td>
                                        <td>Best for stacking personal quest rewards with owner rewards for the placed core.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Boosted Creature Hunt</strong></td>
                                        <td>Rotates one random creature from the dynasty hunt list and gives guild members with an active Dynasty Core <b>+50% experience</b> when killing that selected target during the event window.</td>
                                        <td>Best when your guild wants to hunt the current boosted target and turn one focused creature rotation into faster leveling.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Dynasty Token Surge</strong></td>
                                        <td>Doubles passive dynasty token generation from <b>1 token per hour</b> to <b>2 tokens per hour</b> for 24 hours.</td>
                                        <td>Best for safe city ownership and passive token accumulation.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="gh-note" style="margin-top:16px;">
                        <div class="gh-subtitle">Planning Tip</div>
                        <p>Efficient guilds do not treat every day the same. Watch the event calendar first, then decide whether the best use of that day is tax farming, board quest pushing, guild progression, pet leveling, or passive token accumulation.</p>
                    </div>
                </div>

                <div class="gh-panel" data-gh-panel="dynasty-quests">
                    <div class="gh-grid">
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Dynasty Quests</div>
                            <h3 class="gh-title">Recurring quests tied to owning a city</h3>
                            <ul>
                                <li>Dynasty quests also rotate as <b>3 daily</b>, <b>3 weekend</b>, and <b>3 monthly</b> quests.</li>
                                <li>They are built around actually owning and using an active Dynasty Core.</li>
                                <li>Rewards commonly include <b>gold</b>, <b>experience</b>, and <b>dynasty tokens</b>.</li>
                                <li>Many dynasty quests are only meaningful if your guild is defending, taxing, or farming around a real active city.</li>
                            </ul>
                        </div>
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Main Dynasty Quest Families</div>
                            <h3 class="gh-title">What is tracked</h3>
                            <ul>
                                <li><b>City economy:</b> generate dynasty tax gold and collect city value.</li>
                                <li><b>Token growth:</b> gain dynasty tokens and feed long-term progression.</li>
                                <li><b>Combat ownership:</b> win dynasty defenses or kill bosses while your guild owns an active core.</li>
                                <li><b>Professions under control:</b> gather and craft while your core is active in its city.</li>
                                <li><b>Duration goals:</b> keep a dynasty core active for a target number of days.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="gh-card gh-list" style="margin-top:16px;">
                        <div class="gh-subtitle">How they feel in practice</div>
                        <h3 class="gh-title">Dynasty quests reward ownership, not only combat</h3>
                        <ul>
                            <li>A guild that controls a city and keeps the core alive will steadily progress dynasty quests even without nonstop PvP.</li>
                            <li>However, the best dynasty progression comes when city ownership, board quest completion, taxes, defenders, and active protection all work together.</li>
                        </ul>
                    </div>
                </div>

                <div class="gh-panel" data-gh-panel="dynasty-achievements">
                    <div class="gh-grid">
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Dynasty Achievements</div>
                            <h3 class="gh-title">Long-term milestones for city control</h3>
                            <ul>
                                <li>Dynasty achievements mirror guild achievements, but they focus on city ownership and dynasty warfare instead of general guild growth.</li>
                                <li>They reward repeated city control, successful attacks, defenses, resource collection, and defender progression.</li>
                                <li>Some dynasty achievements also award passive-tree related progression through defender growth tracks.</li>
                            </ul>
                        </div>
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Main Dynasty Achievement Categories</div>
                            <h3 class="gh-title">What is recorded</h3>
                            <ul>
                                <li><b>Ownership:</b> total core placements and attack starts.</li>
                                <li><b>Defense:</b> defense participation totals and defense wins.</li>
                                <li><b>Resources:</b> collected gold totals, collected dynasty token totals, and board quest experience collection.</li>
                                <li><b>Combat:</b> player kills during attacks and defenses, plus core destruction totals.</li>
                                <li><b>Defender growth:</b> paladin, knight, and mage defender level milestones.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="gh-note" style="margin-top:16px;">
                        <div class="gh-subtitle">Why dynasty achievements matter</div>
                        <p>They are the best record of whether a guild actually owned, defended, and exploited a city over time. Guild achievements show the history of the guild. Dynasty achievements show the history of its territorial control.</p>
                    </div>
                </div>

                <div class="gh-panel" data-gh-panel="defenders">
                    <div class="gh-grid-3">
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Dynasty Paladin</div>
                            <h3 class="gh-title">Ranged Defender</h3>
                            <ul>
                                <li>Base role: ranged pressure and holy arrow coverage.</li>
                                <li>Base health: <b>3500</b>.</li>
                                <li>Base damage: <b>140 - 320</b>.</li>
                                <li>Base defense / armor: <b>28 / 26</b>.</li>
                                <li>Target distance: <b>3</b>.</li>
                                <li>Scales through defender level milestones all the way to level <b>1000</b>.</li>
                            </ul>
                        </div>
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Dynasty Knight</div>
                            <h3 class="gh-title">Frontline Defender</h3>
                            <ul>
                                <li>Base role: melee anchor and cleave pressure.</li>
                                <li>Base health: <b>4800</b>.</li>
                                <li>Base damage: <b>180 - 420</b>.</li>
                                <li>Base defense / armor: <b>38 / 34</b>.</li>
                                <li>Target distance: <b>1</b>.</li>
                                <li>Best suited for holding invaders in close combat around the city core.</li>
                            </ul>
                        </div>
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Dynasty Mage</div>
                            <h3 class="gh-title">Arcane Defender</h3>
                            <ul>
                                <li>Base role: elemental burst and zone pressure.</li>
                                <li>Base health: <b>3000</b>.</li>
                                <li>Base damage: <b>170 - 300</b>.</li>
                                <li>Base defense / armor: <b>20 / 18</b>.</li>
                                <li>Target distance: <b>2</b>.</li>
                                <li>Uses energy, fire, and ice patterns as the branch grows.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="gh-note" style="margin-top:16px;">
                        <div class="gh-subtitle">Defender Scaling</div>
                        <p>All defenders scale per level. Current live formulas increase health by <b>0.10%</b> per level, speed by <b>0.35%</b> per level, and damage by <b>0.11%</b> per level. Major milestone upgrade costs also spike at levels 100, 200, 300, and onward up to 1000.</p>
                    </div>
                </div>

                <div class="gh-panel" data-gh-panel="passive-tree">
                    <div class="gh-card">
                        <div class="gh-subtitle">Defender Passive Tree</div>
                        <h3 class="gh-title">Each defender has its own linear milestone branch</h3>
                        <p>Passive nodes unlock in order. Your guild cannot skip ahead in a branch; each defender's nodes must be unlocked sequentially, and the defender must meet the required level milestone.</p>
                    </div>
                    <div class="gh-grid" style="margin-top:16px;">
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Paladin Branch</div>
                            <ul>
                                <li><b>100:</b> Vitality I, +8% max health.</li>
                                <li><b>200:</b> Precision I, +6% damage.</li>
                                <li><b>300:</b> Stride I, +4% speed.</li>
                                <li><b>400:</b> Guard I, +3 defense and +3 armor.</li>
                                <li><b>500:</b> Mastery I, +5% health and +5% damage.</li>
                                <li><b>600:</b> Holy Volley.</li>
                                <li><b>700:</b> Radiant Burst.</li>
                                <li><b>800:</b> Sanctuary.</li>
                                <li><b>900:</b> Judgment Chain.</li>
                                <li><b>1000:</b> Divine Storm.</li>
                            </ul>
                        </div>
                        <div class="gh-card gh-list">
                            <div class="gh-subtitle">Knight Branch</div>
                            <ul>
                                <li><b>100:</b> Vitality I, +8% max health.</li>
                                <li><b>200:</b> Bulwark I, +4 defense and +4 armor.</li>
                                <li><b>300:</b> Fury I, +6% damage.</li>
                                <li><b>400:</b> Stride I, +4% speed.</li>
                                <li><b>500:</b> Mastery I, +5% health and +5% damage.</li>
                                <li><b>600:</b> Cleave.</li>
                                <li><b>700:</b> Shield Bash.</li>
                                <li><b>800:</b> War Stomp.</li>
                                <li><b>900:</b> Earthshatter.</li>
                                <li><b>1000:</b> Execution.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="gh-card gh-list" style="margin-top:16px;">
                        <div class="gh-subtitle">Mage Branch</div>
                        <ul>
                            <li><b>100:</b> Vitality I, +8% max health.</li>
                            <li><b>200:</b> Arcana I, +6% damage.</li>
                            <li><b>300:</b> Flow I, +4% speed.</li>
                            <li><b>400:</b> Focus I, +3 defense and +2 armor.</li>
                            <li><b>500:</b> Mastery I, +5% health and +5% damage.</li>
                            <li><b>600:</b> Arcane Nova.</li>
                            <li><b>700:</b> Flame Burst.</li>
                            <li><b>800:</b> Frost Wave.</li>
                            <li><b>900:</b> Chain Lightning.</li>
                            <li><b>1000:</b> Astral Cataclysm.</li>
                        </ul>
                    </div>
                    <div class="gh-note" style="margin-top:16px;">
                        <div class="gh-subtitle">Passive Tree Rules</div>
                        <p>Nodes cost dynasty tokens, gold, and core fragments. A node only becomes available when the previous nodes in the same branch are already unlocked and the defender has reached the milestone level. Resetting a defender branch costs <b>1 dynasty token</b>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('[data-gh-tab]');
    const panels = document.querySelectorAll('[data-gh-panel]');
    function activate(tab) {
        buttons.forEach(function (btn) {
            btn.classList.toggle('is-active', btn.getAttribute('data-gh-tab') === tab);
        });
        panels.forEach(function (panel) {
            panel.classList.toggle('is-active', panel.getAttribute('data-gh-panel') === tab);
        });
    }
    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            activate(btn.getAttribute('data-gh-tab'));
        });
    });
});
</script>
<?php include 'layout/overall/footer.php'; ?>
