<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';
protect_page();
admin_only($user_data);

function admin_skills_panel_start($title) {
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles w-4 h-4" aria-hidden="true">
							<path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .962 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.582a.5.5 0 0 1 0 .962L15.5 14.064a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.962 0z"></path>
							<path d="M20 3v4"></path>
							<path d="M22 5h-4"></path>
							<path d="M4 17v2"></path>
							<path d="M5 18H3"></path>
						</svg>
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

function admin_skills_panel_end() {
	?></div></div></div></div><br><?php
}

function admin_skills_notice($message, $type = 'success') {
	$text_class = ($type === 'error') ? 'text-red-600' : 'text-[#22c55e]';
	?>
	<div class="p-3 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-xs <?php echo $text_class; ?>">
		<?php echo hhb_tohtml($message); ?>
	</div>
	<?php
}

function playerSkill($skills, $id) {
	if (!$skills) return 0;
	return $skills[$id]['value'];
}

$button_class = 'inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer';
$input_class = 'w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none';
$select_class = 'w-full px-2 py-1 text-xs bg-[var(--box-inner-bg)] text-[var(--text-muted)] border border-[var(--box-inner-border)] focus:outline-none';
$messages = array();

if (isset($_POST['pid']) && (int)$_POST['pid'] > 0) {
	$pid = (int)$_POST['pid'];
	if ($config['ServerEngine'] != 'TFS_10') $status = user_is_online($pid);
	else $status = user_is_online_10($pid);

	if (!$status) {
		$level = (int)$_POST['level'];
		$statgain = $config['vocations_gain'][(int)$_POST['vocation']];
		$playercnf = $config['player'];

		$LevelsFromBase = $level - $playercnf['base']['level'];
		$newhp = $playercnf['base']['health'] + ($statgain['hp'] * $LevelsFromBase);
		$newmp = $playercnf['base']['mana'] + ($statgain['mp'] * $LevelsFromBase);
		$newcap = $playercnf['base']['cap'] + ($statgain['cap'] * $LevelsFromBase);

		if ($config['ServerEngine'] != 'TFS_10') {
			mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['fist'] ."' WHERE `player_id`='$pid' AND `skillid`='0' LIMIT 1;");
			mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['club'] ."' WHERE `player_id`='$pid' AND `skillid`='1' LIMIT 1;");
			mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['sword'] ."' WHERE `player_id`='$pid' AND `skillid`='2' LIMIT 1;");
			mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['axe'] ."' WHERE `player_id`='$pid' AND `skillid`='3' LIMIT 1;");
			mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['dist'] ."' WHERE `player_id`='$pid' AND `skillid`='4' LIMIT 1;");
			mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['shield'] ."' WHERE `player_id`='$pid' AND `skillid`='5' LIMIT 1;");
			mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['fish'] ."' WHERE `player_id`='$pid' AND `skillid`='6' LIMIT 1;");
			mysql_update("UPDATE `players` SET `maglevel`='". (int)$_POST['magic'] ."' WHERE `id`='$pid' LIMIT 1;");
			mysql_update("UPDATE `players` SET `vocation`='". (int)$_POST['vocation'] ."' WHERE `id`='$pid' LIMIT 1;");
			mysql_update("UPDATE `players` SET `level`='". $level ."' WHERE `id`='$pid' LIMIT 1;");
			mysql_update("UPDATE `players` SET `experience`='". level_to_experience($level) ."' WHERE `id`='$pid' LIMIT 1;");
			mysql_update("UPDATE `players` SET `health`='". $newhp ."', `healthmax`='". $newhp ."', `mana`='". $newmp ."', `manamax`='". $newmp ."', `cap`='". $newcap ."' WHERE `id`='$pid' LIMIT 1;");
		} else {
			mysql_update("UPDATE `players` SET `health`='". $newhp ."', `healthmax`='". $newhp ."', `mana`='". $newmp ."', `manamax`='". $newmp ."', `cap`='". $newcap ."', `vocation`='". (int)$_POST['vocation'] ."', `skill_fist`='". (int)$_POST['fist'] ."', `skill_club`='". (int)$_POST['club'] ."', `skill_sword`='". (int)$_POST['sword'] ."', `skill_axe`='". (int)$_POST['axe'] ."', `skill_dist`='". (int)$_POST['dist'] ."', `skill_shielding`='". (int)$_POST['shield'] ."', `skill_fishing`='". (int)$_POST['fish'] ."', `maglevel`='". (int)$_POST['magic'] ."', `level`='". $level ."', `experience`='". level_to_experience($level) ."' WHERE `id`='$pid' LIMIT 1;");
		}
		$messages[] = array('Player skills updated.', 'success');
	} else {
		$messages[] = array('Player must be offline.', 'error');
	}
}

$name = isset($_GET['name']) ? getValue($_GET['name']) : false;
$skills = false;
$pid = 0;

if ($name !== false) {
	if (user_character_exist($name)) {
		$pid = user_character_id($name);
		if ($config['ServerEngine'] != 'TFS_10') {
			$skills = mysql_select_multi("SELECT `value` FROM `player_skills` WHERE `player_id`='$pid' LIMIT 7;");
			$player = mysql_select_single("SELECT `maglevel`, `level`, `vocation` FROM `players` WHERE `id`='$pid' LIMIT 1;");
			$skills[] = array('value' => $player['maglevel']);
			$skills[] = array('value' => $player['level']);
			$skills[] = array('value' => $player['vocation']);
		} else {
			$player = mysql_select_single("SELECT `skill_fist`, `skill_club`, `skill_sword`, `skill_axe`, `skill_dist`, `skill_shielding`, `skill_fishing`, `maglevel`, `level`, `vocation` FROM `players` WHERE `id`='$pid' LIMIT 1;");
			$skills = array(
				0 => array('value' => $player['skill_fist']),
				1 => array('value' => $player['skill_club']),
				2 => array('value' => $player['skill_sword']),
				3 => array('value' => $player['skill_axe']),
				4 => array('value' => $player['skill_dist']),
				5 => array('value' => $player['skill_shielding']),
				6 => array('value' => $player['skill_fishing']),
				7 => array('value' => $player['maglevel']),
				8 => array('value' => $player['level']),
				9 => array('value' => $player['vocation'])
			);
		}
	} else {
		$messages[] = array('Character could not be found.', 'error');
		$name = false;
	}
}

admin_skills_panel_start('Player Skills Administration');
foreach ($messages as $message) admin_skills_notice($message[0], $message[1]);
?>
<form action="" method="<?php echo (!$name) ? 'get' : 'post'; ?>" class="space-y-3">
	<input type="hidden" name="pid" value="<?php echo (int)$pid; ?>">

	<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
		<label class="text-xs text-[var(--text-muted)] sm:col-span-2">Character name
			<input name="name" type="text" placeholder="Character name" class="<?php echo $input_class; ?> mt-1" <?php if ($name !== false) echo 'value="' . hhb_tohtml($name) . '" disabled'; ?>>
		</label>

		<label class="text-xs text-[var(--text-muted)]">Vocation
			<select name="vocation" class="<?php echo $select_class; ?> mt-1" <?php if (!$name) echo 'disabled'; ?>>
				<?php foreach ($config['vocations'] as $vid => $vname): ?>
					<option value="<?php echo (int)$vid; ?>" <?php if ($vid == playerSkill($skills, 9)) echo 'selected'; ?>><?php echo hhb_tohtml($vname['name']); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<label class="text-xs text-[var(--text-muted)]">Level
			<input name="level" type="text" class="<?php echo $input_class; ?> mt-1" <?php if (!$name) echo 'disabled'; ?> value="<?php echo (int)playerSkill($skills, 8); ?>">
		</label>
		<label class="text-xs text-[var(--text-muted)]">Magic level
			<input name="magic" type="text" class="<?php echo $input_class; ?> mt-1" <?php if (!$name) echo 'disabled'; ?> value="<?php echo (int)playerSkill($skills, 7); ?>">
		</label>
		<label class="text-xs text-[var(--text-muted)]">Fist fighting
			<input name="fist" type="text" class="<?php echo $input_class; ?> mt-1" <?php if (!$name) echo 'disabled'; ?> value="<?php echo (int)playerSkill($skills, 0); ?>">
		</label>
		<label class="text-xs text-[var(--text-muted)]">Club fighting
			<input name="club" type="text" class="<?php echo $input_class; ?> mt-1" <?php if (!$name) echo 'disabled'; ?> value="<?php echo (int)playerSkill($skills, 1); ?>">
		</label>
		<label class="text-xs text-[var(--text-muted)]">Sword fighting
			<input name="sword" type="text" class="<?php echo $input_class; ?> mt-1" <?php if (!$name) echo 'disabled'; ?> value="<?php echo (int)playerSkill($skills, 2); ?>">
		</label>
		<label class="text-xs text-[var(--text-muted)]">Axe fighting
			<input name="axe" type="text" class="<?php echo $input_class; ?> mt-1" <?php if (!$name) echo 'disabled'; ?> value="<?php echo (int)playerSkill($skills, 3); ?>">
		</label>
		<label class="text-xs text-[var(--text-muted)]">Distance fighting
			<input name="dist" type="text" class="<?php echo $input_class; ?> mt-1" <?php if (!$name) echo 'disabled'; ?> value="<?php echo (int)playerSkill($skills, 4); ?>">
		</label>
		<label class="text-xs text-[var(--text-muted)]">Shield fighting
			<input name="shield" type="text" class="<?php echo $input_class; ?> mt-1" <?php if (!$name) echo 'disabled'; ?> value="<?php echo (int)playerSkill($skills, 5); ?>">
		</label>
		<label class="text-xs text-[var(--text-muted)]">Fishing
			<input name="fish" type="text" class="<?php echo $input_class; ?> mt-1" <?php if (!$name) echo 'disabled'; ?> value="<?php echo (int)playerSkill($skills, 6); ?>">
		</label>
	</div>

	<div class="flex flex-col sm:flex-row justify-between gap-2 pt-3 border-t border-[var(--box-inner-border)]">
		<a class="<?php echo $button_class; ?>" href="admin_skills.php">Reset / search new character</a>
		<button class="<?php echo $button_class; ?>" type="submit"><?php echo (!$name) ? 'Fetch Character Skills Info' : 'Update Skills'; ?></button>
	</div>
</form>
<?php
admin_skills_panel_end();
include 'layout/overall/footer.php';
?>
