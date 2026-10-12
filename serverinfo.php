<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';

function serverinfo_panel_start($title, $icon = 'info') {
	$icons = array(
		'info' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="M7 10h10"></path><path d="M7 6h10"></path>',
		'star' => '<path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path>',
		'home' => '<path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10.5 12 3l9 7.5"></path><path d="M5 10v11h14V10"></path>',
		'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><path d="M16 3.128a4 4 0 0 1 0 7.744"></path><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><circle cx="9" cy="7" r="4"></circle>',
		'heart' => '<path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"></path>',
		'skull' => '<path d="m12.5 17-.5-1-.5 1h1z"></path><path d="M15 22a1 1 0 0 0 1-1v-1a2 2 0 0 0 1.56-3.25 8 8 0 1 0-11.12 0A2 2 0 0 0 8 20v1a1 1 0 0 0 1 1z"></path><circle cx="15" cy="12" r="1"></circle><circle cx="9" cy="12" r="1"></circle>',
	);
	$iconPath = isset($icons[$icon]) ? $icons[$icon] : $icons['info'];
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide w-4 h-4" aria-hidden="true"><?php echo $iconPath; ?></svg>
						<?php echo hhb_tohtml($title); ?>
					</span>
				</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="space-y-3">
	<?php
}

function serverinfo_panel_end() {
	?></div></div></div></div><br><?php
}

function serverinfo_admin_notice($message, $type = 'success') {
	$text_class = ($type === 'error') ? 'text-red-600' : 'text-[#22c55e]';
	?>
	<div class="p-3 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-xs <?php echo $text_class; ?>"><?php echo hhb_tohtml($message); ?></div>
	<?php
}

function serverinfo_value($data, $key, $default = '-') {
	return (is_array($data) && array_key_exists($key, $data) && $data[$key] !== '') ? $data[$key] : $default;
}

function serverinfo_yes_no($value) {
	if ($value === true || $value === 'true' || $value === 1 || $value === '1') return 'Yes';
	if ($value === false || $value === 'false' || $value === 0 || $value === '0') return 'No';
	return $value;
}

function serverinfo_duration_seconds($seconds) {
	$seconds = (int)$seconds;
	if ($seconds <= 0) return '0 seconds';
	$days = floor($seconds / 86400);
	$seconds %= 86400;
	$hours = floor($seconds / 3600);
	$seconds %= 3600;
	$minutes = floor($seconds / 60);
	$seconds %= 60;
	$parts = array();
	if ($days) $parts[] = $days . ' day' . ($days == 1 ? '' : 's');
	if ($hours) $parts[] = $hours . ' hour' . ($hours == 1 ? '' : 's');
	if ($minutes) $parts[] = $minutes . ' minute' . ($minutes == 1 ? '' : 's');
	if ($seconds) $parts[] = $seconds . ' second' . ($seconds == 1 ? '' : 's');
	return implode(', ', $parts);
}

function serverinfo_duration_ms($ms) {
	return serverinfo_duration_seconds(round(((int)$ms) / 1000));
}

function serverinfo_render_rows($rows) {
	?>
	<div style="overflow-x:auto;">
		<table class="w-full text-xs text-[var(--text-muted)] border border-[var(--box-inner-border)]" style="border-collapse: collapse;">
			<tbody>
				<?php foreach ($rows as $row): ?>
					<tr>
						<td class="px-2 py-1 border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)]" style="width: 38%;"><b><?php echo hhb_tohtml($row[0]); ?></b></td>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo hhb_tohtml($row[1]); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

function serverinfo_strip_lua_comment($line) {
	$inString = false;
	$quote = '';
	$len = strlen($line);
	for ($i = 0; $i < $len - 1; $i++) {
		$char = $line[$i];
		if (($char === '"' || $char === "'") && ($i === 0 || $line[$i - 1] !== '\\')) {
			if (!$inString) {
				$inString = true;
				$quote = $char;
			} else if ($quote === $char) {
				$inString = false;
			}
		}
		if (!$inString && $char === '-' && $line[$i + 1] === '-') {
			return substr($line, 0, $i);
		}
	}
	return $line;
}

function serverinfo_parse_lua_value($value, $knownValues = array()) {
	$value = trim($value);
	if ($value === '') return '';
	if (substr($value, -1) === ',') $value = trim(substr($value, 0, -1));
	$lower = strtolower($value);
	if ($lower === 'true') return true;
	if ($lower === 'false') return false;
	if ($lower === 'nil') return null;
	if (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'")) {
		return substr($value, 1, -1);
	}
	if (isset($knownValues[$value])) return $knownValues[$value];
	if (is_numeric($value)) return $value + 0;
	if (preg_match('/^[0-9\s\+\-\*\/\(\)\.]+$/', $value)) {
		$result = @eval('return ' . $value . ';');
		return ($result === false && $value !== '0') ? $value : $result;
	}
	return $value;
}

function serverinfo_parse_experience_stages($rawConfig) {
	$stages = array();
	if (!preg_match('/experienceStages\s*=\s*\{(.*?)\n\}/s', $rawConfig, $match)) return $stages;
	if (preg_match_all('/\{\s*minlevel\s*=\s*(\d+)\s*(?:,\s*maxlevel\s*=\s*(\d+))?\s*,\s*multiplier\s*=\s*([0-9\.]+)\s*\}/', $match[1], $stageMatches, PREG_SET_ORDER)) {
		foreach ($stageMatches as $stage) {
			$stages[] = array(
				'minlevel' => (int)$stage[1],
				'maxlevel' => (isset($stage[2]) && $stage[2] !== '') ? (int)$stage[2] : '',
				'multiplier' => $stage[3] + 0,
			);
		}
	}
	return $stages;
}

function serverinfo_parse_config_lua($rawConfig) {
	$values = array();
	$values['experienceStages'] = serverinfo_parse_experience_stages($rawConfig);
	$rawWithoutStages = preg_replace('/experienceStages\s*=\s*\{.*?\n\}/s', '', $rawConfig);
	$lines = preg_split('/\r\n|\r|\n/', $rawWithoutStages);
	foreach ($lines as $line) {
		$line = trim(serverinfo_strip_lua_comment($line));
		if ($line === '' || strpos($line, '=') === false) continue;
		list($key, $value) = explode('=', $line, 2);
		$key = trim($key);
		if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) continue;
		$values[$key] = serverinfo_parse_lua_value($value, $values);
	}
	$values['_imported_at'] = time();
	return $values;
}

function serverinfo_render_stage_table($stages) {
	if (!is_array($stages) || empty($stages)) return;
	?>
	<div style="overflow-x:auto;">
		<table class="w-full text-xs text-[var(--text-muted)] border border-[var(--box-inner-border)]" style="border-collapse: collapse;">
			<thead>
				<tr class="bg-[var(--box-inner-bg)]">
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">From Level</th>
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">To Level</th>
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Multiplier</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($stages as $stage): ?>
					<tr>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo (int)$stage['minlevel']; ?></td>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo ($stage['maxlevel'] !== '') ? (int)$stage['maxlevel'] : '8'; ?></td>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo hhb_tohtml($stage['multiplier']); ?>x</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

$isAdmin = user_logged_in() && is_admin($user_data);
$messages = array();
$cache = new Cache('engine/cache/serverinfo_manual');
$cache->useMemory(false);

if ($isAdmin && !empty($_POST)) {
	if (!Token::isValid($_POST['token'])) {
		$messages[] = array('Token is invalid. Please try again.', 'error');
	} else if (isset($_POST['reset_serverinfo'])) {
		$cache->setContent(array());
		$cache->save();
		$messages[] = array('Server info cache reset. Paste config.lua and import again.', 'success');
	} else if (isset($_POST['import_serverinfo'])) {
		$configData = isset($_POST['configData']) ? trim($_POST['configData']) : '';
		if ($configData === '') {
			$messages[] = array('Paste your config.lua content before importing.', 'error');
		} else {
			$serverInfo = serverinfo_parse_config_lua($configData);
			$cache->setContent($serverInfo);
			$cache->save();
			$messages[] = array('Server information imported from config.lua.', 'success');
		}
	}
}

$serverInfo = $cache->load();
if (!is_array($serverInfo) || empty($serverInfo)) $serverInfo = false;

if ($isAdmin) {
	serverinfo_panel_start('Import Config Lua', 'info');
	foreach ($messages as $message) serverinfo_admin_notice($message[0], $message[1]);
	if ($serverInfo !== false && isset($serverInfo['_imported_at'])): ?>
		<div class="text-xs text-[var(--text-muted)]">Last import: <?php echo hhb_tohtml(getClock($serverInfo['_imported_at'], true, true)); ?></div>
	<?php endif; ?>
	<form method="post" class="space-y-3">
		<textarea name="configData" rows="10" class="w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none" placeholder="Paste config.lua here, then import."></textarea>
		<?php Token::create(); ?>
		<div class="flex flex-col sm:flex-row justify-end gap-2">
			<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:opacity-90 px-3 py-1 text-[11px] cursor-pointer" type="submit" name="reset_serverinfo" value="1" onclick="return confirm('Reset imported server info?');">Reset imported info</button>
			<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:opacity-90 px-3 py-1 text-[11px] cursor-pointer" type="submit" name="import_serverinfo" value="1">Import / Reload config</button>
		</div>
	</form>
	<?php serverinfo_panel_end();
}

if ($serverInfo === false) {
	serverinfo_panel_start('Server Information', 'info');
	?>
	<div class="text-xs text-[var(--text-muted)] text-center py-4">The server administrator has not imported config.lua yet.</div>
	<?php
	serverinfo_panel_end();
	include 'layout/overall/footer.php';
	exit;
}

serverinfo_panel_start('General Information', 'info');
serverinfo_render_rows(array(
	array('Server name', serverinfo_value($serverInfo, 'serverName', $config['site_title'])),
	array('MOTD', serverinfo_value($serverInfo, 'motd')),
	array('Location', serverinfo_value($serverInfo, 'location')),
	array('Owner', serverinfo_value($serverInfo, 'ownerName')),
	array('Website', serverinfo_value($serverInfo, 'url')),
	array('Map', serverinfo_value($serverInfo, 'mapName')),
	array('Map author', serverinfo_value($serverInfo, 'mapAuthor')),
));
serverinfo_panel_end();

serverinfo_panel_start('Server Rates', 'star');
if (!empty($serverInfo['experienceStages'])) {
	serverinfo_render_stage_table($serverInfo['experienceStages']);
} else {
	serverinfo_render_rows(array(array('Experience', serverinfo_value($serverInfo, 'rateExp') . 'x')));
}
serverinfo_render_rows(array(
	array('Skill', serverinfo_value($serverInfo, 'rateSkill') . 'x'),
	array('Magic', serverinfo_value($serverInfo, 'rateMagic') . 'x'),
	array('Loot', serverinfo_value($serverInfo, 'rateLoot') . 'x'),
	array('Spawn', serverinfo_value($serverInfo, 'rateSpawn') . 'x'),
));
serverinfo_panel_end();

serverinfo_panel_start('Connection', 'users');
serverinfo_render_rows(array(
	array('World type', serverinfo_value($serverInfo, 'worldType')),
	array('Client', isset($config['client']) ? ($config['client'] / 100) : '-'),
	array('Login port', serverinfo_value($serverInfo, 'loginProtocolPort')),
	array('Game port', serverinfo_value($serverInfo, 'gameProtocolPort')),
	array('Status port', serverinfo_value($serverInfo, 'statusProtocolPort')),
	array('Max players', (serverinfo_value($serverInfo, 'maxPlayers', 0) > 0) ? serverinfo_value($serverInfo, 'maxPlayers') : 'Unlimited'),
	array('One player per account', serverinfo_yes_no(serverinfo_value($serverInfo, 'onePlayerOnlinePerAccount'))),
	array('Allow clones', serverinfo_yes_no(serverinfo_value($serverInfo, 'allowClones'))),
	array('Allow walkthrough', serverinfo_yes_no(serverinfo_value($serverInfo, 'allowWalkthrough'))),
));
serverinfo_panel_end();

serverinfo_panel_start('Combat', 'skull');
serverinfo_render_rows(array(
	array('Protection level', serverinfo_value($serverInfo, 'protectionLevel')),
	array('Hotkey aimbot', serverinfo_yes_no(serverinfo_value($serverInfo, 'hotkeyAimbotEnabled'))),
	array('Kills to red skull', serverinfo_value($serverInfo, 'killsToRedSkull')),
	array('Kills to black skull', serverinfo_value($serverInfo, 'killsToBlackSkull')),
	array('White skull time', serverinfo_duration_seconds(serverinfo_value($serverInfo, 'whiteSkullTime', 0))),
	array('PZ lock', serverinfo_duration_ms(serverinfo_value($serverInfo, 'pzLocked', 0))),
	array('Time to decrease frags', serverinfo_duration_seconds(serverinfo_value($serverInfo, 'timeToDecreaseFrags', 0))),
	array('Stair jump exhaustion', serverinfo_duration_ms(serverinfo_value($serverInfo, 'stairJumpExhaustion', 0))),
	array('Experience by killing players', serverinfo_yes_no(serverinfo_value($serverInfo, 'experienceByKillingPlayers'))),
	array('Rune charges removed', serverinfo_yes_no(serverinfo_value($serverInfo, 'removeChargesFromRunes'))),
	array('Potion charges removed', serverinfo_yes_no(serverinfo_value($serverInfo, 'removeChargesFromPotions'))),
));
serverinfo_panel_end();

serverinfo_panel_start('Houses', 'home');
serverinfo_render_rows(array(
	array('House price each SQM', serverinfo_value($serverInfo, 'housePriceEachSQM') . ' gp'),
	array('House rent period', serverinfo_value($serverInfo, 'houseRentPeriod')),
	array('House owned by account', serverinfo_yes_no(serverinfo_value($serverInfo, 'houseOwnedByAccount'))),
	array('House door show price', serverinfo_yes_no(serverinfo_value($serverInfo, 'houseDoorShowPrice'))),
	array('Only invited can move house items', serverinfo_yes_no(serverinfo_value($serverInfo, 'onlyInvitedCanMoveHouseItems'))),
));
serverinfo_panel_end();

serverinfo_panel_start('Market', 'info');
serverinfo_render_rows(array(
	array('Market offer duration', serverinfo_duration_seconds(serverinfo_value($serverInfo, 'marketOfferDuration', 0))),
	array('Premium to create market offer', serverinfo_yes_no(serverinfo_value($serverInfo, 'premiumToCreateMarketOffer'))),
	array('Check expired offers every', serverinfo_value($serverInfo, 'checkExpiredMarketOffersEachMinutes') . ' minutes'),
	array('Expire offer batch size', serverinfo_value($serverInfo, 'checkExpireOfferBatchSize')),
	array('Max market offers per player', serverinfo_value($serverInfo, 'maxMarketOffersAtATimePerPlayer')),
));
serverinfo_panel_end();

serverinfo_panel_start('Stamina', 'heart');
serverinfo_render_rows(array(
	array('Stamina system', serverinfo_yes_no(serverinfo_value($serverInfo, 'staminaSystem'))),
	array('Free premium', serverinfo_yes_no(serverinfo_value($serverInfo, 'freePremium'))),
	array('AFK kickout', serverinfo_value($serverInfo, 'kickIdlePlayerAfterMinutes') . ' minutes'),
	array('VIP free limit', serverinfo_value($serverInfo, 'vipFreeLimit')),
	array('VIP premium limit', serverinfo_value($serverInfo, 'vipPremiumLimit')),
	array('Depot free limit', serverinfo_value($serverInfo, 'depotFreeLimit')),
	array('Depot premium limit', serverinfo_value($serverInfo, 'depotPremiumLimit')),
));
serverinfo_panel_end();

serverinfo_panel_start('Monsters And Items', 'star');
serverinfo_render_rows(array(
	array('Monster Level bonus health', serverinfo_value($serverInfo, 'monsterBonusHealth')),
	array('Monster Level bonus speed', serverinfo_value($serverInfo, 'monsterBonusSpeed')),
	array('Monster Level bonus damage', serverinfo_value($serverInfo, 'monsterBonusDamage')),
	array('Time between actions', serverinfo_duration_ms(serverinfo_value($serverInfo, 'timeBetweenActions', 0))),
	array('Time between ex-actions', serverinfo_duration_ms(serverinfo_value($serverInfo, 'timeBetweenExActions', 0))),
	array('Reward base rate', serverinfo_value($serverInfo, 'rewardBaseRate')),
	array('Reward damage done', serverinfo_value($serverInfo, 'rewardRateDamageDone')),
	array('Reward damage taken', serverinfo_value($serverInfo, 'rewardRateDamageTaken')),
	array('Reward healing done', serverinfo_value($serverInfo, 'rewardRateHealingDone')),
));
serverinfo_panel_end();

serverinfo_panel_start('Server Save And Misc', 'info');
serverinfo_render_rows(array(
	array('Server save notify message', serverinfo_yes_no(serverinfo_value($serverInfo, 'serverSaveNotifyMessage'))),
	array('Server save notify duration', serverinfo_value($serverInfo, 'serverSaveNotifyDuration') . ' minutes'),
	array('Server save clean map', serverinfo_yes_no(serverinfo_value($serverInfo, 'serverSaveCleanMap'))),
	array('Server save close', serverinfo_yes_no(serverinfo_value($serverInfo, 'serverSaveClose'))),
	array('Server save shutdown', serverinfo_yes_no(serverinfo_value($serverInfo, 'serverSaveShutdown'))),
	array('Weather rain', serverinfo_yes_no(serverinfo_value($serverInfo, 'weatherRain'))),
	array('Thunder effect', serverinfo_yes_no(serverinfo_value($serverInfo, 'thunderEffect'))),
	array('Default world light', serverinfo_yes_no(serverinfo_value($serverInfo, 'defaultWorldLight'))),
));
serverinfo_panel_end();

include 'layout/overall/footer.php';
?>
