<?php
require_once 'engine/init.php';
protect_page();
require_once('config.countries.php');

if (empty($_POST) === false) {
	// $_POST['']
	/* Token used for cross site scripting security */
	if (!Token::isValid($_POST['token'])) {
		$errors[] = 'Token is invalid.';
	}
	$required_fields = array('new_email', 'new_flag');
	foreach($_POST as $key=>$value) {
		if (empty($value) && in_array($key, $required_fields) === true) {
			$errors[] = 'You need to fill in all fields.';
			break 1;
		}
	}

	if (empty($errors) === true) {
		if (filter_var($_POST['new_email'], FILTER_VALIDATE_EMAIL) === false) {
			$errors[] = 'A valid email address is required.';
		} else if (user_email_exist($_POST['new_email']) === true && $user_data['email'] !== $_POST['new_email']) {
			$errors[] = 'That email address is already in use.';
		}
	}

	if (empty($errors) === true) {
		$update_data = array(
			'email' => $_POST['new_email']
		);

		$update_znote_data = array(
			'flag' => getValue($_POST['new_flag']),
			'active_email' => '0'
		);

		// If he had previously verified his email address, remove the previously aquired bonus points.
		if ($user_znote_data['active_email'] > 0) {
			$update_znote_data['points'] = $user_znote_data['points'] - $config['mailserver']['verify_email_points'];
		}

		user_update_account($update_data);
		user_update_znote_account($update_znote_data);
		header('Location: settings.php?success');
		exit();
	}
}

$settings_player = mysql_select_single('SELECT `id`, `name`, `ancestral_points` FROM `players` WHERE `account_id` = ' . (int)$session_user_id . ' ORDER BY `level` DESC LIMIT 1');
$settings_player_id = ($settings_player !== false) ? (int)$settings_player['id'] : 0;
$settings_player_name = ($settings_player !== false) ? $settings_player['name'] : '';
$ancestral_points = ($settings_player !== false) ? (int)$settings_player['ancestral_points'] : 0;

include 'layout/overall/header.php';
?>
<div class="">
	<div class="PageHeader-module__HZea0q__header ">
		<div class="PageHeader-module__HZea0q__headerContent">
			<h2 class="PageHeader-module__HZea0q__title">
				<span class="flex items-center gap-2">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user w-4 h-4" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
					General Information
				</span>
			</h2>
		</div>
	</div>

	<div class="PageBody-module__OAJIWq__body ">
		<div class="PageBody-module__OAJIWq__content">
			<div class="mb-3">
				<a href="myaccount.php">
					<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left w-3 h-3 mr-1" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>
						Back to Account
					</button>
				</a>
			</div>

			<div class="space-y-2">
				<table class="w-full text-xs">
					<tbody>
						<tr class="border-b border-[var(--box-inner-border)]">
							<td class="py-1 pr-4 font-medium">
								<span class="flex items-center gap-2">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-3 h-3" aria-hidden="true"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
									Created
								</span>
							</td>
							<td class="py-1"><?php echo (!empty($user_znote_data['created'])) ? date('M d, Y, h:i:s A', (int)$user_znote_data['created']) : 'Unknown'; ?></td>
						</tr>

						<tr class="border-b border-[var(--box-inner-border)]">
							<td class="py-1 pr-4 font-medium">
								<span class="flex items-center gap-2">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-crown w-3 h-3" aria-hidden="true"><path d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z"></path><path d="M5 21h14"></path></svg>
									Account Status
								</span>
							</td>
							<td class="py-1">
								<div>
									<span class="<?php echo ($user_data['premdays'] > 0) ? 'text-[var(--color-success)]' : 'text-[#ef4444]'; ?>"><?php echo ($user_data['premdays'] > 0) ? 'VIP Account' : 'Free Account'; ?></span>
									<p class="text-[10px] text-[var(--text-muted)]">(VIP Days: <?php echo (int)$user_data['premdays']; ?> days)</p>
								</div>
							</td>
						</tr>

						<tr>
							<td class="py-1 pr-4 font-medium">
								<span class="flex items-center gap-2">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-coins w-3 h-3" aria-hidden="true"><circle cx="8" cy="8" r="6"></circle><path d="M18.09 10.37A6 6 0 1 1 10.34 18"></path><path d="M7 6h1v4"></path><path d="m16.71 13.88.7.71-2.82 2.82"></path></svg>
									Coins
								</span>
							</td>
							<td class="py-1">
								<span class="flex items-center gap-1">
									<span class="text-[#f59e0b] font-semibold"><?php echo (int)$user_znote_data['points']; ?></span>
									<img class="w-3 h-3" src="template/images/tibiacoin.png" alt="Tibia Coins">
								</span>
							</td>
						</tr>

						<tr class="border-t border-[var(--box-inner-border)]">
							<td class="py-1 pr-4 font-medium">
								<span class="flex items-center gap-2">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-3 h-3" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>
									Ancestral Points
								</span>
							</td>
							<td class="py-1">
								<span class="flex items-center gap-1">
									<span class="text-[#9b59b6] font-semibold"><?php echo $ancestral_points; ?></span>
									<img class="w-3 h-3" src="template/images/ancestral_coin.png" alt="Ancestral Coins">
								</span>
							</td>
						</tr>
					</tbody>
				</table>
				<?php
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
	SELECT `ps`.`key`, MAX(`ps`.`value`) AS `value`
	FROM `player_storage` AS `ps`
	INNER JOIN `players` AS `p`
		ON `p`.`id` = `ps`.`player_id`
	WHERE `p`.`account_id` = " . (int)$session_user_id . "
	AND `ps`.`key` IN ('" . implode("', '", array_keys($achievement_mappings)) . "')
	GROUP BY `ps`.`key`
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
?>
				<div class="pt-3 border-t border-[var(--box-inner-border)]">
					<div class="flex items-center justify-between gap-3">
						<h4 class="text-xs font-semibold flex items-center gap-1">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-3 h-3" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>
							Account Achievements
						</h4>
					</div>
					<div class="mt-2 text-[10px] text-[var(--text-muted)]">
						<div class="flex flex-wrap items-center gap-2">
							<?php if ($settings_player_id > 0): ?>
								<span>Account character: <b><?php echo htmlspecialchars($settings_player_name, ENT_QUOTES, 'UTF-8'); ?></b></span>
							<?php endif; ?>
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
				</div>

				<div class="pt-2 border-t border-[var(--box-inner-border)]">
					<h4 class="text-xs font-semibold mb-2 flex items-center gap-1">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield w-3 h-3" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path></svg>
						Security
					</h4>
					<div class="flex flex-wrap gap-2">
						<a href="changepassword.php"><button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-secondary-from)] via-[var(--btn-secondary-via)] to-[var(--btn-secondary-to)] text-[var(--btn-secondary-text)] border border-[var(--btn-secondary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-secondary-hover-from)] hover:via-[var(--btn-secondary-hover-via)] hover:to-[var(--btn-secondary-hover-to)] hover:text-[var(--btn-secondary-hover-text)] hover:border-[var(--btn-secondary-hover-border)] hover:opacity-80 active:from-[var(--btn-secondary-active-from)] active:via-[var(--btn-secondary-active-via)] active:to-[var(--btn-secondary-active-to)] px-3 py-1 text-[11px] cursor-pointer">Change Password</button></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
if (isset($_GET['success']) === true && empty($_GET['success']) === true) {
	echo '
	<div id="account-settings" class="mt-3">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail w-4 h-4" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg>
						Account Settings
					</span>
				</h2>
			</div>
		</div>
	<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				
					<div class="mt-3 text-xs text-[#ef4444] font-semibold">Your settings have been updated.</div>
					<div class="flex justify-end">
						<a href="settings.php"><button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer">Back to Settings</button></a>
					</div>
			</div>
		</div>
		</div>';
} else {
	if (empty($errors) === false) {
		echo '
		<div id="account-settings" class="mt-3">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail w-4 h-4" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg>
						Account Settings
					</span>
				</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				
					<div class="mt-3 text-xs text-[#ef4444] font-semibold">' . output_errors($errors) . '</div>
					<div class="flex justify-end">
						<a href="settings.php"><button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer">Back to Settings</button></a>
					</div>
			</div>
		</div></div>';
	}
	?>

	<div id="account-settings" class="mt-3">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail w-4 h-4" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg>
						Account Settings
					</span>
				</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<form action="" method="post" class="space-y-3">
					<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
						<label for="new_email" class="font-medium">Email Address</label>
						<input class="w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none" type="text" name="new_email" id="new_email" value="<?php echo htmlspecialchars($user_data['email'], ENT_QUOTES, 'UTF-8'); ?>">
					</div>

					<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
						<label for="flag_select" class="font-medium">Country</label>
						<select class="w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none" name="new_flag" id="flag_select">
							<option value="">(Please choose)</option>
							<?php
							foreach(array('pl', 'se', 'br', 'us', 'gb') as $c)
								echo '<option value="' . $c . '"' . (isset($user_znote_data['flag']) && $user_znote_data['flag'] == $c ? ' selected' : '') . '>' . $config['countries'][$c] . '</option>';

							echo '<option value="">----------</option>';
							foreach($config['countries'] as $code => $c)
								echo '<option value="' . $code . '"' . (isset($user_znote_data['flag']) && $user_znote_data['flag'] == $code ? ' selected' : '') . '>' . $c . '</option>';
							?>
						</select>
					</div>

					<?php Token::create(); ?>

					<div class="flex justify-end">
						<button type="submit" class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer">Update Settings</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}
include 'layout/overall/footer.php';
?>
