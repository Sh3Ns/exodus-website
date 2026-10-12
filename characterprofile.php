<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';

if ($config['log_ip']) znote_visitor_insert_detailed_data(4);

function profile_panel_start($title, $icon = 'user') {
	$icons = array(
		'user' => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
		'skull' => '<path d="m12.5 17-.5-1-.5 1h1z"></path><path d="M15 22a1 1 0 0 0 1-1v-1a2 2 0 0 0 1.56-3.25 8 8 0 1 0-11.12 0A2 2 0 0 0 8 20v1a1 1 0 0 0 1 1z"></path><circle cx="15" cy="12" r="1"></circle><circle cx="9" cy="12" r="1"></circle>',
		'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><path d="M16 3.128a4 4 0 0 1 0 7.744"></path><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><circle cx="9" cy="7" r="4"></circle>',
		'star' => '<path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path>',
		'bag' => '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path>',
	);
	$iconPath = isset($icons[$icon]) ? $icons[$icon] : $icons['user'];
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header "><div class="PageHeader-module__HZea0q__headerContent"><h2 class="PageHeader-module__HZea0q__title"><span class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide w-4 h-4" aria-hidden="true"><?php echo $iconPath; ?></svg><?php echo hhb_tohtml($title); ?></span></h2></div></div>
		<div class="PageBody-module__OAJIWq__body "><div class="PageBody-module__OAJIWq__content"><div class="space-y-3">
	<?php
}
function profile_panel_end() { ?></div></div></div></div><br><?php }
function profile_rows($rows) { ?>
	<div style="overflow-x:auto;"><table class="w-full text-xs text-[var(--text-muted)] border border-[var(--box-inner-border)]" style="border-collapse: collapse;"><tbody>
	<?php foreach ($rows as $row): ?>
		<tr><td class="px-2 py-1 border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)]" style="width:38%;"><b><?php echo hhb_tohtml($row[0]); ?></b></td><td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo $row[1]; ?></td></tr>
	<?php endforeach; ?>
	</tbody></table></div>
<?php }

if (!isset($_GET['name']) || empty($_GET['name'])) {
	header('Location: index.php');
	exit();
}

$name = getValue($_GET['name']);
$user_id = user_character_exist($name);
if ($user_id === false) {
	profile_panel_start('Character Profile');
	?><div class="text-xs text-red-600"><?php echo hhb_tohtml(htmlentities(strip_tags($name, ENT_QUOTES))); ?> does not exist.</div><?php
	profile_panel_end();
	include 'layout/overall/footer.php';
	exit();
}

$loadOutfits = $config['show_outfits']['characterprofile'];
if (!$loadOutfits) {
	$profile_data = user_character_data($user_id, 'account_id', 'name', 'level', 'group_id', 'vocation', 'health', 'healthmax', 'experience', 'mana', 'manamax', 'sex', 'lastlogin');
} else if ($config['client'] < 780) {
	$profile_data = user_character_data($user_id, 'account_id', 'name', 'level', 'group_id', 'vocation', 'health', 'healthmax', 'experience', 'mana', 'manamax', 'sex', 'lastlogin', 'lookbody', 'lookfeet', 'lookhead', 'looklegs', 'looktype');
	$profile_data['lookaddons'] = 0;
} else {
	$profile_data = user_character_data($user_id, 'account_id', 'name', 'level', 'group_id', 'vocation', 'health', 'healthmax', 'experience', 'mana', 'manamax', 'sex', 'lastlogin', 'lookbody', 'lookfeet', 'lookhead', 'looklegs', 'looktype', 'lookaddons');
}
$profile_data['online'] = user_is_online_10($user_id);
$profile_znote_data = user_znote_character_data($user_id, 'created', 'hide_char', 'comment');
$achievementPoints = ($config['Ach']) ? mysql_select_single("SELECT SUM(`value`) AS `sum` FROM `player_storage` WHERE `key` LIKE '30___' AND `player_id`=" . (int)$user_id . " LIMIT 1") : false;
$guild_exist = false;
if (get_character_guild_rank($user_id) > 0) {
	$guild_exist = true;
	$guild = get_player_guild_data($user_id);
	$guild_name = get_guild_name($guild['guild_id']);
}

$rows = array();
if ($profile_data['group_id'] > 1) {
	$position = mysql_select_single("SELECT `a`.`type` FROM `players` AS `p` INNER JOIN `accounts` AS `a` ON `p`.`account_id` = `a`.`id` WHERE `a`.`type` > 1 AND `p`.`id` = '" . (int)$user_id . "'");
	$rows[] = array('Position', hhb_tohtml(isset($config['ingame_positions'][$position['type']]) ? $config['ingame_positions'][$position['type']] : 'Unknown'));
}
$deletion_time = mysql_select_single("SELECT `time` FROM `znote_deleted_characters` WHERE `character_name`='" . getValue($name) . "' AND `done` = '0' LIMIT 1;");
if ($deletion_time !== false) $rows[] = array('Deletion', '<span class="text-red-600">Flagged for deletion by owner after ' . hhb_tohtml($deletion_time['time']) . '.</span>');
$rows[] = array('Sex', ($profile_data['sex'] == 1) ? 'Male' : 'Female');
$rows[] = array('Level', (int)$profile_data['level']);
$rows[] = array('Vocation', hhb_tohtml(vocation_id_to_name($profile_data['vocation'])));
if ($guild_exist) $rows[] = array('Guild', '<b>' . hhb_tohtml($guild['rank_name']) . '</b> of <a class="text-[var(--text-link)]" href="guilds.php?name=' . urlencode($guild_name) . '">' . hhb_tohtml($guild_name) . '</a>');
$rows[] = array('Last Login', ($profile_data['lastlogin'] != 0) ? hhb_tohtml(getClock($profile_data['lastlogin'], true, true)) : 'Never.');
if ($config['Ach'] && (int)$achievementPoints['sum'] > 0) $rows[] = array('Achievement Points', (int)$achievementPoints['sum']);
$houses = mysql_select_multi("SELECT `id`, `owner`, `name`, `town_id` AS `town_id` FROM `houses` WHERE `owner` = " . (int)$user_id . ";");
if ($houses !== false) foreach ($houses as $h) $rows[] = array('House', hhb_tohtml($h['name'] . ', ' . $config['towns'][$h['town_id']]));
$rows[] = array('Status', $profile_data['online'] ? '<span class="text-[#22c55e]">online</span>' : '<span class="text-red-600">offline</span>');
$rows[] = array('Created', hhb_tohtml(getClock($profile_znote_data['created'], true)));

profile_panel_start('Character: ' . $profile_data['name'], 'user');
?>
<div class="flex items-start gap-3">
	<?php if ($loadOutfits): ?>
		<div class="w-16 h-16 flex-shrink-0 overflow-hidden bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)]"><img src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo (int)$profile_data['looktype']; ?>&addons=<?php echo (int)$profile_data['lookaddons']; ?>&head=<?php echo (int)$profile_data['lookhead']; ?>&body=<?php echo (int)$profile_data['lookbody']; ?>&legs=<?php echo (int)$profile_data['looklegs']; ?>&feet=<?php echo (int)$profile_data['lookfeet']; ?>" alt="img"></div>
	<?php endif; ?>
	<div class="flex-1 min-w-0"><?php profile_rows($rows); ?></div>
</div>
<?php profile_panel_end();

if ($config['EQ_shower']['enabled']) {
	$imageServer = $config['shop']['imageServer'];
	$imageType = $config['shop']['imageType'];
	$itemImagePath = '/' . trim($imageServer, '/');
	$PEQ = mysql_select_multi("SELECT `player_id`, `pid`, `itemtype`, `count` FROM `player_items` WHERE `player_id`=" . (int)$user_id . " AND `pid`<'18'");
	$equipmentBySlot = array();
	if ($PEQ !== false && !empty($PEQ)) {
		foreach ($PEQ as $item) {
			$equipmentBySlot[(int)$item['pid']] = $item;
		}
	}
	$equipmentLayout = array(
		array(2, 1, 3),
		array(5, 4, 6),
		array(16, 15, 9),
		array(12, 8, 10),
		array(14, 7, 13),
	);
	$soulStamina = ($config['client'] < 780) ? " 0 AS `soul`, 0 AS `stamina`," : " `soul`, `stamina`,";
	$playerstats = mysql_select_single("SELECT `health`, `healthmax`, `mana`, `manamax`, `cap`, `experience`, `level`, {$soulStamina} `maglevel`, `skill_fist`, `skill_club`, `skill_sword`, `skill_axe`, `skill_dist`, `skill_shielding`, `skill_fishing`, `skill_crafting`, `skill_woodcutting`, `skill_mining`, `skill_herbalist`, `skill_armorsmith`, `skill_weaponsmith`, `skill_jewelsmith`, `pet_level` FROM `players` WHERE `id`=" . (int)$user_id . " LIMIT 1;");
	profile_panel_start('Equipment And Stats', 'bag');
	if (!empty($equipmentBySlot)):?>
	
		<div class="flex justify-center">
			<div class="grid gap-1 mb-3" style="grid-template-columns: repeat(3, 56px);">
				<?php foreach ($equipmentLayout as $slotRow): ?>
					<?php foreach ($slotRow as $slotId): ?>
						<?php if ($slotId !== null && isset($equipmentBySlot[$slotId])):
							$item = $equipmentBySlot[$slotId];
							?>
							<div class="flex flex-col items-center justify-center bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-[10px] text-[var(--text-muted)]" style="width:56px;height:56px;">
								<img src="<?php echo $itemImagePath . '/' . (int)$item['itemtype'] . '.' . $imageType; ?>" style="width:32px;height:32px;image-rendering:pixelated;" alt="item">
							</div>
						<?php else: ?>
							<div class="bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] opacity-40" style="width:56px;height:56px;"></div>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</div>
		</div>
	<?php else: ?>
		<div class="text-xs text-[var(--text-muted)] text-center py-4">No equipment to display.</div>
	<?php endif;
	if ($playerstats !== false): ?>
		<div class="flex justify-center mb-3">
			<div class="grid gap-1" style="grid-template-columns: repeat(3, 56px);">
				<div class="text-center bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-[10px] text-[var(--text-muted)] px-1 py-1">Soul<br><b><?php echo (int)$playerstats['soul']; ?></b></div>
				<div></div>
				<div class="text-center bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-[10px] text-[var(--text-muted)] px-1 py-1">Cap<br><b><?php echo hhb_tohtml(number_format($playerstats['cap'],0,'',',')); ?></b></div>
			</div>
		</div>
	<?php endif;
	if ($config['EQ_shower']['skills'] && $playerstats !== false) profile_rows(array(
		array('Experience', hhb_tohtml(number_format($playerstats['experience'],0,'',','))),
		array('Health', hhb_tohtml(number_format($playerstats['health'],0,'',','))),
		array('Mana', hhb_tohtml(number_format($playerstats['mana'],0,'',','))),
		array('Capacity', hhb_tohtml(number_format($playerstats['cap'],0,'',','))),
		array('Magic Level', (int)$playerstats['maglevel']),
		array('Fist', (int)$playerstats['skill_fist']),
		array('Club', (int)$playerstats['skill_club']),
		array('Sword', (int)$playerstats['skill_sword']),
		array('Axe', (int)$playerstats['skill_axe']),
		array('Distance', (int)$playerstats['skill_dist']),
		array('Shielding', (int)$playerstats['skill_shielding']),
		array('Fishing', (int)$playerstats['skill_fishing']),
		array('Crafting', (int)$playerstats['skill_crafting']),
		array('Woodcutting', (int)$playerstats['skill_woodcutting']),
		array('Mining', (int)$playerstats['skill_mining']),
		array('Herbalist', (int)$playerstats['skill_herbalist']),
		array('Armorsmith', (int)$playerstats['skill_armorsmith']),
		array('Weaponsmith', (int)$playerstats['skill_weaponsmith']),
		array('Jewelsmith', (int)$playerstats['skill_jewelsmith']),
		array('Pet Level', (int)$playerstats['pet_level']),
	));
	profile_panel_end();
}

if ((int)$user_id > 0) {
$achievement_mappings = array(
    '110000' => array(
        'image' => 'template/images/achievements/aquatic_on.png',
        'default_image' => 'template/images/achievements/aquatic_off.png',
        'points_required' => 1,
        'text' => 'Kill Aquatic Overlord Thalassa.<br>Points: 350',
		'achievement_points' => 350,
    ),
    '110001' => array(
        'image' => 'template/images/achievements/azazel_on.png',
        'default_image' => 'template/images/achievements/azazel_off.png',
        'points_required' => 1,
        'text' => 'Kill Azazel The Infernal Seraph.<br>Points: 350',
		'achievement_points' => 350,
    ),
	'110002' => array(
        'image' => 'template/images/achievements/draktul_on.png',
        'default_image' => 'template/images/achievements/draktul_off.png',
        'points_required' => 1,
        'text' => 'Kill Drak"thul The Void Sovereign.<br>Points: 350',
		'achievement_points' => 350,
    ),
    '110003' => array(
        'image' => 'template/images/achievements/dreadbone_on.png',
        'default_image' => 'template/images/achievements/dreadbone_off.png',
        'points_required' => 1,
        'text' => 'Kill Dradbone The Eternal.<br>Points: 350',
		'achievement_points' => 350,
    ),
   '110004' => array(
        'image' => 'template/images/achievements/dreadscale_on.png',
        'default_image' => 'template/images/achievements/dreadscale_off.png',
        'points_required' => 1,
        'text' => 'Kill Dreadscale The Ancient.<br>Points: 350',
		'achievement_points' => 350,
    ),
	'110005' => array(
        'image' => 'template/images/achievements/gorgul_on.png',
        'default_image' => 'template/images/achievements/gorgul_off.png',
        'points_required' => 1,
        'text' => 'Kill Gor"gul The Frienzied.<br>Points: 350',
		'achievement_points' => 350,
    ),
	'110006' => array(
        'image' => 'template/images/achievements/mortis_on.png',
        'default_image' => 'template/images/achievements/mortis_off.png',
        'points_required' => 1,
        'text' => 'Kill Mortis The Sovereign.<br>Points: 350',
		'achievement_points' => 350,
    ),
	'110007' => array(
        'image' => 'template/images/achievements/thalador_on.png',
        'default_image' => 'template/images/achievements/thalador_off.png',
        'points_required' => 1,
        'text' => 'Kill Thalador The Stormbringer.<br>Points: 350',
		'achievement_points' => 350,
    ),
	'110008' => array(
        'image' => 'template/images/achievements/tymagron_on.png',
        'default_image' => 'template/images/achievements/tymagron_off.png',
        'points_required' => 1,
        'text' => 'Kill Tymagron The Earthshaker.<br>Points: 350',
		'achievement_points' => 350,
    ),
	'110009' => array(
        'image' => 'template/images/achievements/vorondor_on.png',
        'default_image' => 'template/images/achievements/vorondor_off.png',
        'points_required' => 1,
        'text' => 'Kill Vorondor The Eternal Flames.<br>Points: 350',
		'achievement_points' => 350,
    ),
	'110010' => array(
        'image' => 'template/images/achievements/scarlet_on.png',
        'default_image' => 'template/images/achievements/scarlet_off.png',
        'points_required' => 1,
        'text' => 'Obtained / Equipped Scarlet Raven Boat.<br>Points: 80',
		'achievement_points' => 80,
    ),
	'110011' => array(
        'image' => 'template/images/achievements/awakener_on.png',
        'default_image' => 'template/images/achievements/awakener_off.png',
        'points_required' => 1,
        'text' => 'Obtained / Equipped Awakener Boat.<br>Points: 115',
		'achievement_points' => 115,
    ),
	'110012' => array(
        'image' => 'template/images/achievements/counterspell_on.png',
        'default_image' => 'template/images/achievements/counterspell_off.png',
        'points_required' => 1,
        'text' => 'Obtained / Equipped Counterspell Boat.<br>Points: 80',
		'achievement_points' => 80,
    ),
	'110013' => array(
        'image' => 'template/images/achievements/explorer_on.png',
        'default_image' => 'template/images/achievements/explorer_off.png',
        'points_required' => 1,
        'text' => 'Obtained / Equipped Explorer Boat.<br>Points: 25',
		'achievement_points' => 25,
    ),
	'110014' => array(
        'image' => 'template/images/achievements/whisperwind_on.png',
        'default_image' => 'template/images/achievements/whisperwind_off.png',
        'points_required' => 1,
        'text' => 'Obtained / Equipped Whisperwind Boat.<br>Points: 45',
		'achievement_points' => 45,
    ),
	'110015' => array(
        'image' => 'template/images/achievements/menethil_on.png',
        'default_image' => 'template/images/achievements/menethil_off.png',
        'points_required' => 1,
        'text' => 'Obtained / Equipped Menethil March Boat.<br>Points: 45',
		'achievement_points' => 45,
    ),
	'110016' => array(
        'image' => 'template/images/achievements/shadowmeld_on.png',
        'default_image' => 'template/images/achievements/shadowmeld_off.png',
        'points_required' => 1,
        'text' => 'Obtained / Equipped Shadowmeld Boat.<br>Points: 45',
		'achievement_points' => 45,
    ),
	'110017' => array(
        'image' => 'template/images/achievements/exotic_ascalon_on.png',
        'default_image' => 'template/images/achievements/exotic_ascalon_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Exotic Ascalon Lv6.<br>Points: 150',
		'achievement_points' => 150,
    ),
	'110023' => array(
        'image' => 'template/images/achievements/chaos_ascalon_on.png',
        'default_image' => 'template/images/achievements/chaos_ascalon_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Chaos Ascalon Lv6.<br>Points: 225',
		'achievement_points' => 225,
    ),
	'110029' => array(
        'image' => 'template/images/achievements/abyssal_ascalon_on.png',
        'default_image' => 'template/images/achievements/abyssal_ascalon_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Abyssal Ascalon Lv6.<br>Points: 300',
		'achievement_points' => 300,
    ),
	'110035' => array(
        'image' => 'template/images/achievements/ancestral_ascalon_on.png',
        'default_image' => 'template/images/achievements/ancestral_ascalon_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Ancestral Ascalon Lv6.<br>Points: 425',
		'achievement_points' => 425,
    ),
	'110041' => array(
        'image' => 'template/images/achievements/exotic_saydon_on.png',
        'default_image' => 'template/images/achievements/exotic_saydon_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Exotic Saydon Lv6.<br>Points: 150',
		'achievement_points' => 150,
    ),
	'110047' => array(
        'image' => 'template/images/achievements/chaos_saydon_on.png',
        'default_image' => 'template/images/achievements/chaos_saydon_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Chaos Saydon Lv6.<br>Points: 225',
		'achievement_points' => 225,
    ),
	'110053' => array(
        'image' => 'template/images/achievements/abyssal_saydon_on.png',
        'default_image' => 'template/images/achievements/abyssal_saydon_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Abyssal Saydon Lv6.<br>Points: 300',
		'achievement_points' => 300,
    ),
	'110059' => array(
        'image' => 'template/images/achievements/ancestral_saydon_on.png',
        'default_image' => 'template/images/achievements/ancestral_saydon_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Ancestral Saydon Lv6.<br>Points: 425',
		'achievement_points' => 425,
    ),
	'110065' => array(
        'image' => 'template/images/achievements/exotic_vercruze_on.png',
        'default_image' => 'template/images/achievements/exotic_vercruze_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Exotic Vercruze Lv6.<br>Points: 150',
		'achievement_points' => 150,
    ),
	'110071' => array(
        'image' => 'template/images/achievements/chaos_vercruze_on.png',
        'default_image' => 'template/images/achievements/chaos_vercruze_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Chaos Vercruze Lv6.<br>Points: 225',
		'achievement_points' => 225,
    ),
	'110077' => array(
        'image' => 'template/images/achievements/abyssal_vercruze_on.png',
        'default_image' => 'template/images/achievements/abyssal_vercruze_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Abyssal Vercruze Lv6.<br>Points: 300',
		'achievement_points' => 300,
    ),
	'110110' => array(
        'image' => 'template/images/achievements/ancestral_vercruze_on.png',
        'default_image' => 'template/images/achievements/ancestral_vercruze_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Ancestral Vercruze Lv6.<br>Points: 425',
		'achievement_points' => 425,
    ),
	'110083' => array(
        'image' => 'template/images/achievements/exotic_kaiser_on.png',
        'default_image' => 'template/images/achievements/exotic_kaiser_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Exotic Kaiser Lv6.<br>Points: 150',
		'achievement_points' => 150,
    ),
	'110089' => array(
        'image' => 'template/images/achievements/chaos_kaiser_on.png',
        'default_image' => 'template/images/achievements/chaos_kaiser_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Chaos Kaiser Lv6.<br>Points: 225',
		'achievement_points' => 225,
    ),
	'110095' => array(
        'image' => 'template/images/achievements/abyssal_kaiser_on.png',
        'default_image' => 'template/images/achievements/abyssal_kaiser_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Abyssal Kaiser Lv6.<br>Points: 300',
		'achievement_points' => 300,
    ),
	'110101' => array(
        'image' => 'template/images/achievements/ancestral_kaiser_on.png',
        'default_image' => 'template/images/achievements/ancestral_kaiser_off.png',
        'points_required' => 1,
        'text' => 'Obtained Legendary Ancestral Kaiser Lv6.<br>Points: 425',
		'achievement_points' => 425,
    ),
);

	$player_storage = mysql_select_multi("
		SELECT `key`, MAX(`value`) AS `value`
		FROM `player_storage`
		WHERE `player_id` = " . (int)$user_id . "
		AND `key` IN ('" . implode("', '", array_keys($achievement_mappings)) . "')
		GROUP BY `key`
	");

	$player_storage_values = array();
	if (is_array($player_storage)) {
		foreach ($player_storage as $storage) {
			$player_storage_values[$storage['key']] = (int)$storage['value'];
		}
	}

	$achievement_html = '';
	$achievement_obtained_count = 0;
	$total_points_obtained = 0;
	$total_points_all = array_sum(array_column($achievement_mappings, 'achievement_points'));

	foreach ($achievement_mappings as $storage_key => $achievement) {
		$storage_value = isset($player_storage_values[$storage_key]) ? $player_storage_values[$storage_key] : 0;
		$achievement_obtained = ($storage_value >= $achievement['points_required']);
		$achievement_image = $achievement_obtained ? $achievement['image'] : $achievement['default_image'];

		if ($achievement_obtained) {
			$achievement_obtained_count++;
			$total_points_obtained += $achievement['achievement_points'];
		}

		$achievement_html .= '
			<div class="text-center">
				<img class="inline-block w-8 h-8 mb-1" src="' . $achievement_image . '" alt="">
				<div class="text-[10px] text-[var(--text-muted)]">' . $achievement['text'] . '</div>
			</div>';
	}

	$achievement_count = count($achievement_mappings);
	$progress_percentage = ($total_points_all > 0) ? (($total_points_obtained / $total_points_all) * 100) : 0;

	profile_panel_start('Achievements', 'star');
	?>
	<div class="text-[10px] text-[var(--text-muted)]">
		<div class="flex flex-wrap items-center gap-2">
			<span>Character: <b><?php echo hhb_tohtml($profile_data['name']); ?></b></span>
			<span>Achievements: <b><?php echo $achievement_obtained_count; ?>/<?php echo $achievement_count; ?></b></span>
			<span>Points: <b><?php echo $total_points_obtained; ?>/<?php echo $total_points_all; ?></b></span>
		</div>
		<div class="mt-2 h-3 border border-[var(--box-inner-border)]" style="background-color: rgba(0, 0, 0, 0.25);">
			<div class="h-full" style="width: <?php echo round($progress_percentage, 2); ?>%; background-color: #2baaff;"></div>
		</div>
		<div class="grid grid-cols-3 sm:grid-cols-6 gap-2 mt-3">
			<?php echo $achievement_html; ?>
		</div>
	</div>
	<?php
	profile_panel_end();
}
if (!empty($profile_znote_data['comment'])) {
	profile_panel_start('Comment', 'user');
	echo '<div class="text-xs text-[var(--text-muted)]">' . nl2br(hhb_tohtml($profile_znote_data['comment'])) . '</div>';
	profile_panel_end();
}

profile_panel_start('Death List', 'skull');
$deaths = mysql_select_multi("SELECT `player_id`, `time`, `level`, `killed_by`, `is_player`, `mostdamage_by`, `mostdamage_is_player`, `unjustified`, `mostdamage_unjustified` FROM `player_deaths` WHERE `player_id`=" . (int)$user_id . " ORDER BY `time` DESC LIMIT 10;");
if ($deaths) {
	echo '<div class="space-y-2">';
	foreach ($deaths as $d) {
		$lasthit = ($d['is_player']) ? '<a class="text-[var(--text-link)]" href="characterprofile.php?name=' . urlencode($d['killed_by']) . '">' . hhb_tohtml($d['killed_by']) . '</a>' : hhb_tohtml($d['killed_by']);
		echo '<div class="p-2 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-xs text-[var(--text-muted)]"><b>' . hhb_tohtml(getClock($d['time'], true, true)) . '</b><br>Killed at level ' . (int)$d['level'] . ' by ' . $lasthit;
		if ($d['unjustified']) echo ' <span class="text-red-600 italic">(unjustified)</span>';
		if ($d['mostdamage_by'] !== $d['killed_by']) {
			$mostdmg = ($d['mostdamage_is_player']) ? '<a class="text-[var(--text-link)]" href="characterprofile.php?name=' . urlencode($d['mostdamage_by']) . '">' . hhb_tohtml($d['mostdamage_by']) . '</a>' : hhb_tohtml($d['mostdamage_by']);
			echo '<br>and by ' . $mostdmg . '.';
			if ($d['mostdamage_unjustified']) echo ' <span class="text-red-600 italic">(unjustified)</span>';
		} else echo ' <b>(soloed)</b>';
		echo '</div>';
	}
	echo '</div>';
} else echo '<div class="text-xs text-[var(--text-muted)] text-center py-4">This player has never died.</div>';
profile_panel_end();

$otherChars = mysql_select_multi("SELECT `p`.`id`, `p`.`name`, `p`.`level`, `p`.`vocation`, `p`.`lastlogin`, CASE WHEN `l`.`player_id` IS NULL THEN 0 else 1 END as `online` FROM `players` as `o` JOIN `players` as `p` ON `o`.`account_id` = `p`.`account_id` LEFT JOIN `znote_players` as `z` ON `p`.`id` = `z`.`player_id` LEFT JOIN `znote_players` as `z2` ON `o`.`id` = `z2`.`player_id` LEFT JOIN `players_online` as `l` ON `p`.`id` = `l`.`player_id` WHERE `o`.`id` = " . (int)$user_id . " AND `p`.`id` != `o`.`id` AND `z`.`hide_char` = 0 AND `z2`.`hide_char` = 0 ORDER BY `p`.`experience` DESC;");
if ($otherChars !== false) {
	profile_panel_start('Other Visible Characters', 'users'); ?>
	<div style="overflow-x:auto;"><table class="w-full text-xs text-[var(--text-muted)] border border-[var(--box-inner-border)]" style="border-collapse: collapse;"><thead><tr class="bg-[var(--box-inner-bg)]"><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Name</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Level</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Vocation</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Last Login</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Status</th></tr></thead><tbody>
	<?php foreach ($otherChars as $char): ?>
		<tr><td class="px-2 py-1 border border-[var(--box-inner-border)]"><a class="text-[var(--text-link)]" href="characterprofile.php?name=<?php echo urlencode($char['name']); ?>"><?php echo hhb_tohtml($char['name']); ?></a></td><td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo (int)$char['level']; ?></td><td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo hhb_tohtml(vocation_id_to_name($char['vocation'])); ?></td><td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo ($char['lastlogin'] != 0) ? hhb_tohtml(getClock($char['lastlogin'], true, true)) : 'Never.'; ?></td><td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo ($char['online']) ? 'online' : 'offline'; ?></td></tr>
	<?php endforeach; ?>
	</tbody></table></div>
	<?php profile_panel_end();
}

profile_panel_start('Address', 'user');
$profileAddress = ($config['htwrite']) ? $_SERVER['HTTP_HOST'] . '/' . $profile_data['name'] : $_SERVER['HTTP_HOST'] . '/characterprofile.php?name=' . $profile_data['name'];
echo '<a class="text-[var(--text-link)] text-xs" href="//' . hhb_tohtml($profileAddress) . '">' . hhb_tohtml($profileAddress) . '</a>';
profile_panel_end();

include 'layout/overall/footer.php';
?>



