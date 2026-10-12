<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';

if (!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes_compat(5, $crypto_strong));
	if (!$crypto_strong) {
		// The admin form token does not need cryptographic strength here.
	}
}

protect_page();
admin_only($user_data);

$enc = 100;
$errors = array();

function render_admin_notice($title, $message, $type = 'info') {
	$title = hhb_tohtml($title);
	$text_class = ($type === 'error') ? 'text-red-600' : (($type === 'success') ? 'text-[#22c55e]' : 'text-[var(--text-muted)]');

	echo '
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title"><span class="flex items-center gap-2">' . $title . '</span></h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="text-xs ' . $text_class . '">' . $message . '</div>
			</div>
		</div>
	</div>
	<br>
	';
}

if (empty($_POST) === false) {
	if (empty($_POST['csrf_token'])) {
		http_response_code(400);
		render_admin_notice('Admin Panel', 'Missing CSRF token.', 'error');
		include 'layout/overall/footer.php';
		exit();
	}

	if (!hash_equals($_POST['csrf_token'], $_SESSION['csrf_token'])) {
		http_response_code(400);
		render_admin_notice('Admin Panel', 'CSRF token invalid.', 'error');
		include 'layout/overall/footer.php';
		exit();
	}

	if (!empty($_POST['ban_char']) && !empty($_POST['ban_type']) && !empty($_POST['ban_action']) && !empty($_POST['ban_reason']) && !empty($_POST['ban_time']) && !empty($_POST['ban_comment'])) {
		if (user_character_exist($_POST['ban_char'])) {
			$charname = $_POST['ban_char'];
			$typeid = (int)$_POST['ban_type'] - $enc;
			$actionid = (int)$_POST['ban_action'] - $enc;
			$reasonid = (int)$_POST['ban_reason'] - $enc;
			$time = (int)$_POST['ban_time'] - $enc;
			$comment = $_POST['ban_comment'];

			if (set_rule_violation($charname, $typeid, $actionid, $reasonid, $time, $comment)) {
				$errors[] = 'Violation entry has been set for '. hhb_tohtml($charname) .'.';
			} else {
				$errors[] = 'Website character name: '. hhb_tohtml($config['website_char']) .' does not exist. Create this character name or configure another name in config.php';
				$errors[] = 'Website failed to recognize a character it can represent while inserting a rule violation.';
			}
		} else {
			$errors[] = 'Character '. hhb_tohtml(getValue($_POST['ban_char'])) .' does not exist.';
		}
	}

	if (empty($_POST['del_name']) === false) {
		if (user_character_exist($_POST['del_name'])) {
			user_delete_character(user_character_id($_POST['del_name']));
			$errors[] = 'Character '. hhb_tohtml(getValue($_POST['del_name'])) .' permanently deleted.';
		} else {
			$errors[] = 'Character '. hhb_tohtml(getValue($_POST['del_name'])) .' does not exist.';
		}
	}

	if (empty($_POST['reset_pass']) === false && empty($_POST['new_pass']) === false) {
		if (user_character_exist($_POST['reset_pass'])) {
			$acc_id = user_character_account_id($_POST['reset_pass']);

			if ($acc_id != $session_user_id) {
				if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10' || $config['ServerEngine'] == 'OTHIRE') {
					user_change_password($acc_id, $_POST['new_pass']);
				} else if ($config['ServerEngine'] == 'TFS_03') {
					user_change_password03($acc_id, $_POST['new_pass']);
				}
				$errors[] = 'The password to the account of character name: '. hhb_tohtml(getValue($_POST['reset_pass'])) .' has been set to: '. hhb_tohtml(getValue($_POST['new_pass'])) .'.';
			} else {
				header('Location: changepassword.php');
				exit();
			}
		} else {
			$errors[] = 'Character '. hhb_tohtml(getValue($_POST['reset_pass'])) .' does not exist.';
		}
	}

	if (empty($_POST['points_char']) === false && empty($_POST['points_value']) === false) {
		$char = sanitize($_POST['points_char']);
		$points = (int)$_POST['points_value'];
		$account = mysql_select_single("SELECT `account_id` FROM `players` WHERE `name`='$char' LIMIT 1;");

		if ($account !== false) {
			$znote_account = mysql_select_single("SELECT `id`, `points` FROM `znote_accounts` WHERE `account_id`='". $account['account_id'] ."';");
			if ($znote_account !== false) {
				$total_points = $znote_account['points'] + $points;
				mysql_update("UPDATE `znote_accounts` SET `points`='$total_points' WHERE `account_id`='". $account['account_id'] ."';");
				$errors[] = hhb_tohtml($points) . ' shop points added to '. hhb_tohtml(getValue($_POST['points_char'])) .'.';
			} else {
				$errors[] = 'Could not find Znote account for '. hhb_tohtml(getValue($_POST['points_char'])) .'.';
			}
		} else {
			$errors[] = 'Character '. hhb_tohtml(getValue($_POST['points_char'])) .' does not exist.';
		}
	}

	if (empty($_POST['position_name']) === false && empty($_POST['position_type']) === false) {
		if (user_character_exist($_POST['position_name'])) {
			if (array_key_exists($_POST['position_type'], $config['ingame_positions'])) {
				if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10' || $config['ServerEngine'] == 'OTHIRE') {
					set_ingame_position($_POST['position_name'], $_POST['position_type']);
				} else if ($config['ServerEngine'] == 'TFS_03') {
					set_ingame_position03($_POST['position_name'], $_POST['position_type']);
				}

				$pos = 'Undefined';
				foreach ($config['ingame_positions'] as $key=>$value) {
					if ($key == $_POST['position_type']) {
						$pos = $value;
					}
				}
				$errors[] = 'Character '. hhb_tohtml(getValue($_POST['position_name'])) .' received the ingame position: '. hhb_tohtml($pos) .'.';
			}
		} else {
			$errors[] = 'Character '. hhb_tohtml(getValue($_POST['position_name'])) .' does not exist.';
		}
	}

	if (isset($_POST['from']) && in_array($_POST['from'], array('all', 'only'))) {
		$from = $_POST['from'];
		if ($from === 'only') {
			if (empty($_POST['player_name']) || !user_character_exist($_POST['player_name'])) {
				$errors[] = 'Character '. hhb_tohtml(getValue($_POST['player_name'])) .' does not exist.';
			}
		}

		if (!sizeof($errors)) {
			$to = $_POST['to'];
			$teleportQuery = 'UPDATE `players` SET ';

			if ($to == 'home') {
				$teleportQuery .= '`posx` = 0, `posy` = 0, `posz` = 0 ';
			} else if ($to == 'town') {
				$teleportQuery .= '`posx` = 0, `posy` = 0, `posz` = 0, `town_id` = ' . (int) getValue($_POST['town']) . ' ';
			} else if ($to == 'xyz') {
				$teleportQuery .= '`posx` = ' . (int) getValue($_POST['x']) . ', `posy` = ' . (int) getValue($_POST['y']) . ', `posz` = ' . (int) getValue($_POST['z']) . ' ';
			}

			if ($from === 'only') {
				$teleportQuery .= " WHERE `name` = '" . getValue($_POST['player_name']) . "'";
			}

			mysql_update($teleportQuery);
			$errors[] = 'Teleport request has been applied.';
		}
	}
}

if (empty($errors) === false) {
	render_admin_notice('Admin Result', output_errors($errors), 'info');
}

$basic = user_znote_data('version', 'installed', 'cached');
if ($basic['version'] !== $version) {
	mysql_update("UPDATE `znote` SET `version`='$version';");
	$basic = user_znote_data('version', 'installed', 'cached');
}

$input_class = 'w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none';
$select_class = 'w-full px-2 py-1 text-xs bg-[var(--box-inner-bg)] text-[var(--text-muted)] border border-[var(--box-inner-border)] focus:outline-none';
$button_class = 'inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer';
?>
<div class="">
	<div class="PageHeader-module__HZea0q__header ">
		<div class="PageHeader-module__HZea0q__headerContent">
			<h2 class="PageHeader-module__HZea0q__title"><span class="flex items-center gap-2">Admin Panel</span></h2>
		</div>
	</div>
	<div class="PageBody-module__OAJIWq__body ">
		<div class="PageBody-module__OAJIWq__content">
			<div class="space-y-3">
				<div class="text-xs text-[var(--text-muted)]">
					Running Znote AAC Version: <b><?php echo hhb_tohtml($basic['version']); ?></b><br>
					Last cached on: <b><?php echo hhb_tohtml(getClock($basic['cached'], true)); ?></b>
				</div>

				<div class="pt-3 border-t border-[var(--box-inner-border)]">
					<h3 class="text-xs font-semibold mb-2">Permanently delete character</h3>
					<form action="" method="post" class="grid grid-cols-1 sm:grid-cols-[1fr_150px] gap-2">
						<input type="hidden" name="csrf_token" value="<?php echo hhb_tohtml($_SESSION['csrf_token']);?>" />
						<input class="<?php echo $input_class; ?>" type="text" name="del_name" placeholder="Character name...">
						<button class="<?php echo $button_class; ?>" type="submit">Delete</button>
					</form>
				</div>

				<div class="pt-3 border-t border-[var(--box-inner-border)]">
					<h3 class="text-xs font-semibold mb-2">Ban character and/or account</h3>
					<form action="" method="post" class="space-y-2">
						<input type="hidden" name="csrf_token" value="<?php echo hhb_tohtml($_SESSION['csrf_token']);?>" />
						<input class="<?php echo $input_class; ?>" type="text" name="ban_char" placeholder="Character name...">
						<div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
							<select class="<?php echo $select_class; ?>" name="ban_type"><?php foreach ($config['ban_type'] as $key=>$value): ?><option value="<?php echo hhb_tohtml($enc + $key); ?>"><?php echo hhb_tohtml($value); ?></option><?php endforeach; ?></select>
							<select class="<?php echo $select_class; ?>" name="ban_action"><?php foreach ($config['ban_action'] as $key=>$value): ?><option value="<?php echo hhb_tohtml($enc + $key); ?>"><?php echo hhb_tohtml($value); ?></option><?php endforeach; ?></select>
							<select class="<?php echo $select_class; ?>" name="ban_time"><?php foreach ($config['ban_time'] as $key=>$value): ?><option value="<?php echo hhb_tohtml($enc + $key); ?>"><?php echo hhb_tohtml($value); ?></option><?php endforeach; ?></select>
						</div>
						<select class="<?php echo $select_class; ?>" name="ban_reason"><?php foreach ($config['ban_reason'] as $key=>$value): ?><option value="<?php echo hhb_tohtml($enc + $key); ?>"><?php echo hhb_tohtml($value); ?></option><?php endforeach; ?></select>
						<div class="grid grid-cols-1 sm:grid-cols-[1fr_150px] gap-2">
							<input class="<?php echo $input_class; ?>" type="text" name="ban_comment" maxlength="60" placeholder="Ban for botting rotworms.">
							<button class="<?php echo $button_class; ?>" type="submit">Set Violation</button>
						</div>
					</form>
				</div>

				<div class="pt-3 border-t border-[var(--box-inner-border)]">
					<h3 class="text-xs font-semibold mb-2">Reset account password</h3>
					<form action="" method="post" class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_150px] gap-2">
						<input type="hidden" name="csrf_token" value="<?php echo hhb_tohtml($_SESSION['csrf_token']);?>" />
						<input class="<?php echo $input_class; ?>" type="text" name="reset_pass" placeholder="Character name">
						<input class="<?php echo $input_class; ?>" type="text" name="new_pass" placeholder="New password">
						<button class="<?php echo $button_class; ?>" type="submit">Change</button>
					</form>
				</div>

				<div class="pt-3 border-t border-[var(--box-inner-border)]">
					<h3 class="text-xs font-semibold mb-2">Set character position</h3>
					<?php if ($config['ServerEngine'] == 'TFS_03' && count($config['ingame_positions']) == 5): ?><p class="text-xs text-red-600 mb-2">ERROR: You forgot to add (Senior Tutor) rank in config.php!</p><?php endif; ?>
					<form action="" method="post" class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_150px] gap-2">
						<input type="hidden" name="csrf_token" value="<?php echo hhb_tohtml($_SESSION['csrf_token']);?>" />
						<input class="<?php echo $input_class; ?>" type="text" name="position_name" placeholder="Character name">
						<select class="<?php echo $select_class; ?>" name="position_type"><?php foreach ($config['ingame_positions'] as $key=>$value): ?><option value="<?php echo hhb_tohtml($key); ?>"><?php echo hhb_tohtml($value); ?></option><?php endforeach; ?></select>
						<button class="<?php echo $button_class; ?>" type="submit">Set Position</button>
					</form>
				</div>

				<div class="pt-3 border-t border-[var(--box-inner-border)]">
					<h3 class="text-xs font-semibold mb-2">Give shop points</h3>
					<form action="" method="post" class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_150px] gap-2">
						<input type="hidden" name="csrf_token" value="<?php echo hhb_tohtml($_SESSION['csrf_token']);?>" />
						<input class="<?php echo $input_class; ?>" type="text" name="points_char" placeholder="Character name">
						<input class="<?php echo $input_class; ?>" type="text" name="points_value" placeholder="Points">
						<button class="<?php echo $button_class; ?>" type="submit">Give Points</button>
					</form>
				</div>

				<div class="pt-3 border-t border-[var(--box-inner-border)]">
					<h3 class="text-xs font-semibold mb-2">Teleport player</h3>
					<form action="" method="post" class="space-y-2">
						<input type="hidden" name="csrf_token" value="<?php echo hhb_tohtml($_SESSION['csrf_token']);?>" />
						<div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
							<select class="<?php echo $select_class; ?>" name="from"><option value="all">All</option><option value="only">Only</option></select>
							<input class="<?php echo $input_class; ?>" type="text" name="player_name" placeholder="Player name">
							<select class="<?php echo $select_class; ?>" name="to"><option value="home">Hometown</option><option value="town">Specific Town</option><option value="xyz">Specific Position</option></select>
						</div>
						<div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
							<select class="<?php echo $select_class; ?>" name="town"><?php foreach($config['towns'] as $townId => $townName): ?><option value="<?php echo hhb_tohtml($townId); ?>"><?php echo hhb_tohtml($townName); ?></option><?php endforeach; ?></select>
							<input class="<?php echo $input_class; ?>" type="text" name="x" placeholder="Position X">
							<input class="<?php echo $input_class; ?>" type="text" name="y" placeholder="Position Y">
							<input class="<?php echo $input_class; ?>" type="text" name="z" placeholder="Position Z">
						</div>
						<div class="flex justify-end"><button class="<?php echo $button_class; ?>" type="submit">Teleport</button></div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include 'layout/overall/footer.php'; ?>
