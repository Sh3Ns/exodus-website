<?php require_once 'engine/init.php';
if ($config['require_login']['guilds']) protect_page();

// These functions are page-specific and only used in this file. 
function guild_list($TFSVersion) {
	$cache = new Cache(guild_dynasty_enabled() ? 'engine/cache/guildlist_dynasty' : 'engine/cache/guildlist');
	if ($cache->hasExpired()) {
		$guilds = mysql_select_multi("
			SELECT 
				`id`, 
				`name`, 
				`creationdata`, 
				`motd`, 
				" . (guild_dynasty_enabled() ? "`emblem`, `guild_level`, " : "") . "
				(	SELECT 
						COUNT('guild_id') 
					FROM `guild_membership` 
					WHERE `guild_id`=`id`
				) AS `total` 
			FROM `guilds` 
			ORDER BY `name`;
		");

		// Add level data info to guilds
		if ($guilds !== false) {
			$guildIds = array();
			for ($i = 0; $i < count($guilds); $i++) {
				$guilds[$i]['level'] = get_guild_level_data($guilds[$i]['id']);
				$guildIds[] = (int)$guilds[$i]['id'];
			}
			$dynastyStats = guild_fetch_dynasty_stats($guildIds);
			for ($i = 0; $i < count($guilds); $i++) {
				$guildId = (int)$guilds[$i]['id'];
				$guilds[$i]['dynasty_stats'] = isset($dynastyStats[$guildId]) ? $dynastyStats[$guildId] : array(
					'achievements' => 0,
			'achievement_total' => guild_achievement_total(),
					'prisms' => 0,
					'dynasties' => 0,
				);
			}
		}

		$cache->setContent($guilds);
		$cache->save();
	} else {
		$guilds = $cache->load();
	}
	return $guilds;
}

function get_guild_position($rid) {
	$rid = (int)$rid;
	$data = mysql_select_single("SELECT `level` FROM `guild_ranks` WHERE `id`={$rid};");
	return ($data !== false) ? $data['level'] : false;
}

// Check guild logo
function logo_exists($guild) {
	$guild = sanitize($guild);
	echo (file_exists('engine/guildimg/'.$guild.'.gif')) ? 'engine/guildimg/'.$guild.'.gif' : 'engine/guildimg/default@logo.gif';
}

function render_guild_notice($message, $type = 'info') {
	$textClass = 'text-[var(--text-muted)]';
	if ($type === 'error') {
		$textClass = 'text-red-600';
	} else if ($type === 'success') {
		$textClass = 'text-[#22c55e]';
	}
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">Information</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="text-xs <?php echo $textClass; ?>"><?php echo $message; ?></div>
			</div>
		</div>
	</div>
	<br>
	<?php
}

function guild_button_class($extra = '') {
	return trim('inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer ' . $extra);
}

function guild_input_class($extra = '') {
	return trim('w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] ' . $extra);
}

function guild_dynasty_enabled() {
	return !empty($GLOBALS['config']['enableGuildDynasty']);
}

function guild_achievement_total() {
	return 245;
}

function guild_get_emblem_value($guild) {
	if (!guild_dynasty_enabled() || !is_array($guild) || !isset($guild['emblem'])) {
		return false;
	}

	$emblem = (int)$guild['emblem'];
	return ($emblem >= 0 && $emblem < 57) ? $emblem : false;
}

function guild_render_logo_html($guild, $size = 80) {
	$size = (int)$size;
	if ($size <= 0) {
		$size = 80;
	}

	$emblem = guild_get_emblem_value($guild);
	if ($emblem !== false) {
		$columns = 5;
		$tileSize = 128;
		$col = $emblem % $columns;
		$row = (int)floor($emblem / $columns);
		$displaySize = $size;
		$backgroundWidth = $columns * $displaySize;
		$offsetX = -($col * $displaySize);
		$offsetY = -($row * $displaySize);
		$style = sprintf(
			'width:%1$dpx;height:%1$dpx;margin:auto;display:block;background-image:url(\'template/img/guilds/emblems.png\');background-repeat:no-repeat;background-size:%2$dpx auto;background-position:%3$dpx %4$dpx;',
			$displaySize,
			$backgroundWidth,
			$offsetX,
			$offsetY
		);
		return '<span aria-hidden="true" style="' . $style . '"></span>';
	}

	$guildName = isset($guild['name']) ? $guild['name'] : '';
	ob_start();
	logo_exists($guildName);
	$logoPath = trim(ob_get_clean());
	return '<img style="max-height: ' . $size . 'px; margin: auto; display: block;" src="' . htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8') . '" alt="">';
}

function guild_fetch_dynasty_stats($guildIds) {
	$stats = array();
	foreach ($guildIds as $guildId) {
		$stats[(int)$guildId] = array(
			'achievements' => 0,
					'achievement_total' => guild_achievement_total(),
			'prisms' => 0,
			'dynasties' => 0,
		);
	}

	if (!guild_dynasty_enabled() || empty($guildIds)) {
		return $stats;
	}

	$idList = implode(',', array_map('intval', $guildIds));

	$achievementRows = mysql_select_multi("
		SELECT `guild_id`, COUNT(*) AS `count`
		FROM `guild_achievement_progress`
		WHERE `guild_id` IN ($idList) AND `completed_at` > 0
		GROUP BY `guild_id`
	");
	if ($achievementRows !== false) {
		foreach ($achievementRows as $row) {
			$guildId = (int)$row['guild_id'];
			if (isset($stats[$guildId])) {
				$stats[$guildId]['achievements'] = (int)$row['count'];
			}
		}
	}

	$prismRows = mysql_select_multi("
		SELECT `guild_id`, COUNT(*) AS `count`
		FROM `guild_prisms`
		WHERE `guild_id` IN ($idList)
		GROUP BY `guild_id`
	");
	if ($prismRows !== false) {
		foreach ($prismRows as $row) {
			$guildId = (int)$row['guild_id'];
			if (isset($stats[$guildId])) {
				$stats[$guildId]['prisms'] = (int)$row['count'];
			}
		}
	}

	$dynastyRows = mysql_select_multi("
		SELECT `guild_id`, COUNT(*) AS `count`
		FROM `guild_dynasties`
		WHERE `guild_id` IN ($idList)
		GROUP BY `guild_id`
	");
	if ($dynastyRows !== false) {
		foreach ($dynastyRows as $row) {
			$guildId = (int)$row['guild_id'];
			if (isset($stats[$guildId])) {
				$stats[$guildId]['dynasties'] = (int)$row['count'];
			}
		}
	}

	return $stats;
}

function guild_language_label($languageId) {
	$languageId = (int)$languageId;
	$map = array(
		0 => 'Any',
		1 => 'English',
		2 => 'French',
		3 => 'Spanish',
		4 => 'Portuguese',
		5 => 'Polish',
		6 => 'German',
		7 => 'Turkish',
		8 => 'Russian',
	);
	return isset($map[$languageId]) ? $map[$languageId] : 'Any';
}

function guild_join_status_label($statusId) {
	$statusId = (int)$statusId;
	$map = array(
		0 => 'Invite Only',
		1 => 'Public',
		2 => 'Closed',
	);
	return isset($map[$statusId]) ? $map[$statusId] : 'Invite Only';
}

// Remove cid invitation from guild (gid)
function guild_remove_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	mysql_delete("
		DELETE FROM `guild_invites` 
		WHERE `player_id`='{$cid}' 
		AND `guild_id`='{$gid}'
	;");
}

function update_player_guildnick_10($cid, $nick) {
	$cid = (int)$cid;
	$nick = sanitize($nick);
	mysql_update("UPDATE `guild_membership` SET `nick`='{$nick}' WHERE `player_id`={$cid}");
}

function guild_player_join($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;

	// Find rank id for regular member in this guild
	$guildrank = mysql_select_single("
		SELECT `id` 
		FROM `guild_ranks` 
		WHERE `guild_id`='{$gid}' 
		AND `level`='1' LIMIT 1;
	");

	if ($guildrank !== false) {
		$rid = $guildrank['id'];
		// Remove invite
		mysql_delete("DELETE FROM `guild_invites` WHERE `player_id`='{$cid}';");
		// Add to guild
		mysql_insert("
			INSERT INTO `guild_membership` (`player_id`, `guild_id`, `rank_id`, `nick`) 
			VALUES ('{$cid}', '{$gid}', '{$rid}', '')
		;");
		// Return success
		return true;
	}

	return false;
}

function guild_player_leave_10($cid) {
	$cid = (int)$cid;
	mysql_delete("DELETE FROM `guild_membership` WHERE `player_id`='{$cid}' LIMIT 1;");
}

function update_player_guild_position_10($cid, $rid) {
	$cid = (int)$cid;
	$rid = (int)$rid;
	mysql_update("UPDATE `guild_membership` SET `rank_id`='{$rid}' WHERE `player_id`={$cid}");
}

function guild_invite_player($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	mysql_insert("INSERT INTO `guild_invites` (`player_id`, `guild_id`) VALUES ('{$cid}', '{$gid}')");
}

function guild_remove_invites($gid) {
	$gid = (int)$gid;
	mysql_delete("DELETE FROM `guild_invites` WHERE `guild_id`='{$gid}';");
}

function guild_delete($gid) {
	$gid = (int)$gid;
	mysql_delete("DELETE FROM `guilds` WHERE `id`='{$gid}';");
}

// Get guild data, using guild id.
function get_guild_rank_data($gid) {
	$gid = (int)$gid;
	return mysql_select_multi("SELECT `id`, `guild_id`, `name`, `level` FROM `guild_ranks` WHERE `guild_id`='{$gid}' ORDER BY `id` DESC LIMIT 0, 30");
}

// Changes leadership of a guild to player_id
function guild_new_leader($new_leader, $gid) {
	$new_leader = (int)$new_leader;
	$gid = (int)$gid;
	mysql_update("UPDATE `guilds` SET `ownerid`='{$new_leader}' WHERE `id`={$gid}");
}

// Change guild leader (parameters: cid, new and old leader).
function guild_change_leader($nCid, $oCid) {
	$nCid = (int)$nCid;
	$oCid = (int)$oCid;
	$gid = guild_leader_gid($oCid);
	$ranks = get_guild_rank_data($gid);
	$leader_rid = 0;
	$vice_rid = 0;

	// Get rank id for leader and vice leader.
	foreach ($ranks as $rank) {
		if ($rank['level'] == 3) $leader_rid = $rank['id'];
		if ($rank['level'] == 2) $vice_rid = $rank['id'];
	}

	$status = ($leader_rid > 0 && $vice_rid > 0) ? true : false;

	if ($status) {
		// Update players and set their new rank id
		mysql_update("UPDATE `guild_membership` SET `rank_id`='{$leader_rid}' WHERE `player_id`={$nCid} LIMIT 1;");
		mysql_update("UPDATE `guild_membership` SET `rank_id`='{$vice_rid}' WHERE `player_id`={$oCid} LIMIT 1;");

		// Update guilds set new ownerid
		guild_new_leader($nCid, $gid);
	}

	return $status;
}

// Creates a guild, where cid is the owner of the guild, and name is the name of guild.
function create_guild($cid, $name) {
	$cid = (int)$cid;
	$name = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", sanitize($name))));
	$time = time();

	// Create the guild
	mysql_insert("INSERT INTO `guilds` (`name`, `ownerid`, `creationdata`, `motd`) VALUES ('{$name}', '{$cid}', '{$time}', '');");

	// Get guild id
	$gid = get_guild_id($name);

	// Get rank id for guild leader
	$data = mysql_select_single("SELECT `id` FROM `guild_ranks` WHERE `guild_id`='{$gid}' AND `level`='3' LIMIT 1;");
	$rid = ($data !== false) ? $data['id'] : false;

	// Give player rank id for leader of his guild
	mysql_insert("INSERT INTO `guild_membership` (`player_id`, `guild_id`, `rank_id`, `nick`) VALUES ('{$cid}', '{$gid}', '{$rid}', '');");
}

// Returns total members in a guild (integer)
function count_guild_members($gid) {
	$gid = (int)$gid;
	$data = mysql_select_single("SELECT COUNT('guild_id') AS `total` FROM `guild_membership` WHERE `guild_id`='{$gid}';");
	return ($data !== false) ? $data['total'] : false;
}

// Guildwar functions
function guild_war_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	$gname = get_guild_name($cid);
	$ename = get_guild_name($gid);
	$time = time();
	mysql_insert("
		INSERT INTO `guild_wars` (`guild1`, `guild2`, `name1`, `name2`, `status`, `started`, `ended`) 
		VALUES ('{$cid}', '{$gid}', '{$gname}', '{$ename}', '0', '{$time}', '0')
	;");
}

function accept_war_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	mysql_update("UPDATE `guild_wars` SET `status` = 1 WHERE `guild1` = '{$cid}' AND `guild2` = '{$gid}' AND `status` = 0;");
}

function reject_war_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	$time = time();
	mysql_update("UPDATE `guild_wars` SET `status` = 2, `ended` = '{$time}' WHERE `guild1` = '{$cid}' AND `guild2` = '{$gid}';");
}

function cancel_war_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	$time = time();
	mysql_update("UPDATE `guild_wars` SET `status` = 3, `ended` = '{$time}' WHERE `guild2` = '{$cid}' AND `guild1` = '{$gid}';");
}

include 'layout/overall/header.php';

if (user_logged_in() === true) {
	// fetch data
	$char_count = user_character_list_count($session_user_id);
	$char_array = user_character_list($user_data['id']);
	$characters = array();
	$charactersId = array();
	$charactersRank = array();
	if ($char_array !== false) {
		foreach ($char_array as $value) {
			$characters[] = $value['name'];
			$charactersId[] = $value['id'];
			$charactersRank[] = $value['rank_id'];
		}
	}
} else {
	$char_count = 0;
}

// Display the guild list
if (empty($_GET['name'])) {
//data_dump($guild, false, "guild data");
$guilds = guild_list($config['ServerEngine']);

//data_dump($guilds, false, "Guilds");
?>
<style type="text/css">
	#guildsTable tr {
		cursor: pointer;
	}
</style>
<div class="">
	<div class="PageHeader-module__HZea0q__header ">
		<div class="PageHeader-module__HZea0q__headerContent">
			<h2 class="PageHeader-module__HZea0q__title">Guilds</h2>
		</div>
	</div>
	<div class="PageBody-module__OAJIWq__body ">
		<div class="PageBody-module__OAJIWq__content">
			<div class="space-y-3">
				<div class="text-xs text-[var(--text-muted)]">Browse guild profiles, members, and progression stats.</div>
				<?php if (isset($guilds) && !empty($guilds) && $guilds !== false): ?>
					<div class="overflow-x-auto">
						<table id="guildsTable" class="w-full text-xs">
							<thead>
								<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
									<th class="py-1.5 px-2 text-left"><?php echo guild_dynasty_enabled() ? 'Emblem' : 'Logo'; ?></th>
									<th class="py-1.5 px-2 text-left">Description</th>
									<th class="py-1.5 px-2 text-left">Guild data</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($guilds as $guild) {
									if ($guild['total'] >= 1) {
										$url = url("guilds.php?name=". $guild['name']);
										?>
										<tr class="border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)] bg-[var(--box-inner-bg)]/30 special" onclick="javascript:window.location.href='<?php echo $url; ?>'">
											<td class="py-1 px-2" style="width: 100px;">
												<?php echo guild_render_logo_html($guild, 80); ?>
											</td>
											<td class="py-1 px-2">
												<b><?php echo htmlspecialchars($guild['name'], ENT_QUOTES, 'UTF-8'); ?></b>
												<?php if (strlen($guild['motd']) > 0) echo '<br>'.htmlspecialchars($guild['motd'], ENT_QUOTES, 'UTF-8'); ?>
											</td>
											<td class="py-1 px-2">
												<?php echo "Total members: ".$guild['level']['players']; ?>
												<br><?php echo "Average level: ".$guild['level']['avg'].""; ?>
												<br><?php echo "Guild level: ".(guild_dynasty_enabled() && isset($guild['guild_level']) ? (int)$guild['guild_level'] : (int)$guild['level']['total']); ?>
												<?php if (guild_dynasty_enabled()): ?>
													<br><?php echo "Achievements: ".(int)$guild['dynasty_stats']['achievements']." / ".(int)$guild['dynasty_stats']['achievement_total']; ?>
													<br><?php echo "Prisms owned: ".(int)$guild['dynasty_stats']['prisms']; ?>
													<br><?php echo "Dynasty cores owned: ".(int)$guild['dynasty_stats']['dynasties']; ?>
												<?php endif; ?>
											</td>
										</tr>
										<?php
										//echo '<td>'. getClock($guild['creationdata'], true) .'</td>';
									}
								}
								?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div class="text-xs text-[var(--text-muted)]">Guild list is empty.</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<br>

<!-- POST action: create guild -->
<?php
if (user_logged_in() === true) {
	// post verifications
	// CREATE GUILD
	if (!empty($_POST['selected_char']) && !empty($_POST['guild_name'])) {
		data_dump($_POST, false, "debug");
		if ((int)user_character_account_id($_POST['selected_char']) === $session_user_id) {
			data_dump($_POST, false, "debug2");
			//code here
			$name = sanitize($_POST['selected_char']);
			$user_id = user_character_id($name);
			
			$char_data = user_character_data($user_id, 'level');
			$char_data['online'] = (user_is_online_10($user_id)) ? 1 : 0;

			// If character level is high enough
			if ($char_data['level'] >= $config['create_guild_level']) {

				// If character is offline
				if ($char_data['online'] == 0) {

					// If character is premium
					if ($config['guild_require_premium'] == false || $user_data['premdays'] > 0) {

						if (get_character_guild_rank($user_id) < 1) {

							if (preg_match("/^[a-zA-Z_ ]+$/", $_POST['guild_name'])) {
							// Only allow normal symbols as guild name
								if (strlen($_POST['guild_name']) < 31) {

								$guildname = sanitize($_POST['guild_name']);

								$gid = get_guild_id($guildname);
								if ($gid === false) {
									create_guild($user_id, $guildname);
									// Re-cache the guild list
									$guilds = guild_list($config['ServerEngine']);
									header('Location: success.php');
									exit();
									} else render_guild_notice('A guild with that name already exist.', 'error');
								} else render_guild_notice('Guild name is to long. It can has to be 30 or less characters long.', 'error');
							} else render_guild_notice('Guild name may only contain a-z, A-Z and spaces.', 'error');
						} else render_guild_notice('You are already in a guild.', 'error');
					} else render_guild_notice('You need a premium account to create a guild.', 'error');
				} else render_guild_notice('Your character must be offline to create a guild.', 'error');
			} else render_guild_notice(htmlspecialchars($name, ENT_QUOTES, 'UTF-8') .' is level '. (int)$char_data['level'] .'. But you need level '. (int)$config['create_guild_level'] .'+ to create your own guild!', 'error');
		}
	}
	// end
	?>


	<!-- FORMS TO CREATE GUILD-->
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">Create Guild</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<form action="" method="post" class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] gap-2 items-end">
					<div>
						<label for="selected_char" class="block text-xs font-medium mb-1">Character</label>
						<select id="selected_char" name="selected_char" class="w-full text-[10px] px-2 py-1 rounded border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)] text-[var(--text-muted)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)]">
						<?php
						for ($i = 0; $i < $char_count; $i++) {
							echo '<option value="'. htmlspecialchars($characters[$i], ENT_QUOTES, 'UTF-8') .'">'. htmlspecialchars($characters[$i], ENT_QUOTES, 'UTF-8') .'</option>';
						}
						?>
						</select>
					</div>
					<div>
						<label for="guild_name" class="block text-xs font-medium mb-1">Guild Name</label>
						<input id="guild_name" type="text" name="guild_name" class="w-full px-2 py-1 text-[10px] bg-[var(--box-inner-bg)] text-[var(--text-muted)] border border-[var(--box-inner-border)] rounded focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)]">
					</div>
					<div class="sm:self-end">
						<button type="submit" class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer">
							Create Guild
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<br>

	<?php
} else {
	render_guild_notice('You need to be logged in to create guilds.', 'error');
}
?>
<!-- end user-->

<?php
} else { // GUILD OVERVIEW
	$guild = get_guild_data($_GET['name']);
	$gid = (int)$guild['id'];
	if ($gid === false) {
		header('Location: guilds.php');
		exit();
	}
	$gcount = count_guild_members($gid);
	if ($gcount < 1) {
		header('Location: guilds.php');
		exit();
	}
	$guildLevelData = get_guild_level_data($gid);
	$guildDynastyStats = false;
	$guildDynastyMeta = false;
	if (guild_dynasty_enabled()) {
		$guildDynastyStatsMap = guild_fetch_dynasty_stats(array($gid));
		$guildDynastyStats = isset($guildDynastyStatsMap[$gid]) ? $guildDynastyStatsMap[$gid] : array(
			'achievements' => 0,
			'achievement_total' => guild_achievement_total(),
			'prisms' => 0,
			'dynasties' => 0,
		);
		$guildDynastyMeta = mysql_select_single("
			SELECT `ownerid`, `emblem`, `guild_level`, `language`, `join_status`, `required_level`
			FROM `guilds`
			WHERE `id` = {$gid}
			LIMIT 1;
		");
	}
	$inv_data = guild_invite_list($gid);
	$players = get_guild_players($gid);
	$inv_count = 0;

	// Calculate invite count
	if ($inv_data !== false) {
		foreach ($inv_data as $inv) {
			++$inv_count;
		}
	}

	// calculate visitor access
	$highest_access = 0;
	if (user_logged_in() === true) {
		// Get visitor access in this guild
		foreach ($players as $player) {
			$rid = $player['rank_id'];

			for ($i = 0; $i < $char_count; $i++) {

				$playerRank = $charactersRank[$i];

				if ($playerRank == $rid) {
					// Get a player guild position ID, using his rank_id
					$access = get_guild_position($playerRank);
					if ($access == 2 || $access == 3) { //If player got access level vice leader or leader
						if ($access > $highest_access) $highest_access = $access;
					}
				}

			}
		}
	}
	// Display the specific guild page
	?>

	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">Guild: <?php echo sanitize($_GET['name']); ?></h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="space-y-3">
					<?php if (isset($_GET['error'])): ?>
						<div class="text-xs text-red-600"><?php echo sanitize($_GET['error']); ?></div>
					<?php endif; ?>
					<div id="guildTitleDiv">
						<?php if ($config['use_guild_logos'] || guild_dynasty_enabled()): ?>
							<div id="guildImageDiv" style="float: left; margin-right: 10px;">
								<?php echo guild_render_logo_html(array(
									'name' => sanitize($_GET['name']),
									'emblem' => $guildDynastyMeta !== false && isset($guildDynastyMeta['emblem']) ? $guildDynastyMeta['emblem'] : null,
								), 100); ?>
							</div>
						<?php endif; ?>
						<div id="guildDescription" style="overflow: hidden;">
							<p class="text-xs"><?php echo htmlspecialchars($guild['motd'], ENT_QUOTES, 'UTF-8'); ?></p>
							<?php if (guild_dynasty_enabled() && $guildDynastyMeta !== false): ?>
								<?php
								$leaderName = '';
								if (!empty($guildDynastyMeta['ownerid'])) {
									$leaderRow = mysql_select_single("SELECT `name` FROM `players` WHERE `id` = " . (int)$guildDynastyMeta['ownerid'] . " LIMIT 1;");
									$leaderName = $leaderRow !== false ? $leaderRow['name'] : '';
								}
								?>
								<div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-1 text-xs mt-3">
									<div><b>Guild Leader</b> &nbsp; <?php echo htmlspecialchars($leaderName, ENT_QUOTES, 'UTF-8'); ?></div>
									<div><b>Language</b> &nbsp; <?php echo htmlspecialchars(guild_language_label($guildDynastyMeta['language']), ENT_QUOTES, 'UTF-8'); ?></div>
									<div><b>Guild Level</b> &nbsp; <?php echo (int)$guildDynastyMeta['guild_level']; ?></div>
									<div><b>Join Status</b> &nbsp; <?php echo htmlspecialchars(guild_join_status_label($guildDynastyMeta['join_status']), ENT_QUOTES, 'UTF-8'); ?></div>
									<div><b>Guild Members</b> &nbsp; <?php echo (int)$guildLevelData['players']; ?></div>
									<div><b>Req. Level</b> &nbsp; <?php echo (int)$guildDynastyMeta['required_level']; ?></div>
									<div><b>Total Levels</b> &nbsp; <?php echo (int)$guildLevelData['total']; ?></div>
									<div><b>Prisms Owned</b> &nbsp; <?php echo (int)$guildDynastyStats['prisms']; ?></div>
									<div><b>Average Level</b> &nbsp; <?php echo (int)$guildLevelData['avg']; ?></div>
									<div><b>Achievements</b> &nbsp; <?php echo (int)$guildDynastyStats['achievements'] . ' / ' . (int)$guildDynastyStats['achievement_total']; ?></div>
									<div><b>Dynasty Cores Owned</b> &nbsp; <?php echo (int)$guildDynastyStats['dynasties']; ?></div>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="clear-both"></div>

	<!-- Guild information -->
	<div class="overflow-x-auto">
	<table id="guildViewTable" class="w-full text-xs">
		<thead>
		<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
			<th class="py-1.5 px-2 text-left">Rank</th>
			<th class="py-1.5 px-2 text-left">Name</th>
			<th class="py-1.5 px-2 text-center">Level</th>
			<th class="py-1.5 px-2 text-left">Vocation</th>
			<th class="py-1.5 px-2 text-center">Status</th>
		</tr>
		</thead>
		<tbody>
			<?php
			$onlinelist = array();
			$gplayers = array();
			foreach ($players as $player) {
				$gplayers[] = $player['id'];
			}
			$gplayers = join(',',$gplayers);
			$onlinequery = mysql_select_multi("
				SELECT `player_id` 
				FROM `players_online` 
				WHERE `player_id` IN({$gplayers});
			");
			if ($onlinequery !== false) foreach ($onlinequery as $online) {
				$onlinelist[] = $online['player_id'];
			}
			//data_dump($players, false, "Data");
			$rankName = '';
			foreach ($players as $player) {
				$chardata['online'] = (in_array($player['id'], $onlinelist)) ? 1 : 0;
				echo '<tr class="border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)]">';
				echo '<td class="py-1 px-2">' . ($rankName !== $player['rank_name'] ? htmlspecialchars($player['rank_name'], ENT_QUOTES, 'UTF-8') : '') . '</td>';
				$rankName = $player['rank_name'];
				echo '<td class="py-1 px-2"><a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)] hover:underline" href="characterprofile.php?name='. urlencode($player['name']) .'">'. htmlspecialchars($player['name'], ENT_QUOTES, 'UTF-8') .'</a>';
				if (!empty($player['guildnick'])) {
					echo ' ('. htmlspecialchars($player['guildnick'], ENT_QUOTES, 'UTF-8') .')';
				}
				echo '</td>';
				echo '<td class="py-1 px-2 text-center">'. (int)$player['level'] .'</td>';
				echo '<td class="py-1 px-2">'. htmlspecialchars($config['vocations'][$player['vocation']]['name'], ENT_QUOTES, 'UTF-8') .'</td>';
				if ($chardata['online'] == 1) echo '<td class="py-1 px-2 text-center text-[var(--color-success)]"><b>Online</b></td>';
				else echo '<td class="py-1 px-2 text-center text-[var(--text-muted)]">Offline</td>';
				echo '</tr>';
			}
			?>
		</tbody>
	</table>
	</div>

	<?php // Invited characters
	if ($inv_count > 0): ?>
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">Invited characters</h2>
			</div>
		</div>
		<div class="overflow-x-auto">
		<table class="w-full text-xs">
			<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
				<td class="py-1.5 px-2">Name</td>
				<?php
				if ($highest_access == 2 || $highest_access == 3) {
					echo '<td class="py-1.5 px-2">Remove</td>';
				}
				// Shuffle through visitor characters
				for ($i = 0; $i < $char_count; $i++) {
					$exist = false;
					// Shuffle through invited character, see if they match your character.
					if ($inv_data !== false) foreach ($inv_data as $inv) {
						if ($charactersId[$i] == $inv['player_id']) {
							$exist = true;
						}
					}
					if ($exist) echo '<td class="py-1.5 px-2">Join Guild</td><td class="py-1.5 px-2">Reject Invitation</td>';
				}
				?>
			</tr>
				<?php
				$bool = false;
				if ($inv_data !== false) foreach ($inv_data as $inv) {
					echo '<tr class="border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)]">';
					echo '<td class="py-1 px-2">'. htmlspecialchars($inv['name'], ENT_QUOTES, 'UTF-8') .'</td>';
					// Remove invitation
					if ($highest_access == 2 || $highest_access == 3) {
					?> <form action="" method="post"> <?php
						echo '<td class="py-1 px-2">';
						echo '<input type="hidden" name="uninvite" value="' . $inv['player_id'] . '" />';
						echo '<button type="submit" class="'. guild_button_class() .'">Remove Invitation</button>';
						echo '</td>';
					?> </form> <?php
					}
					// Join Guild
					?> <form action="" method="post"> <?php
						for ($i = 0; $i < $char_count; $i++) {
							if ($charactersId[$i] == $inv['player_id']) {
								echo '<td class="py-1 px-2">';
								echo '<input type="hidden" name="joinguild" value="' . $inv['player_id'] . '" />';
								echo '<button type="submit" class="'. guild_button_class() .'">Join Guild</button>';
								echo '</td>';
								$bool = true;
							}
						}
						if (isset($bool, $exist) && !$bool && $exist) {
							echo '<td class="py-1 px-2"></td>';
							$bool = false;
						}
					?> </form> <?php
					// Reject invitation
					?> <form action="" method="post"> <?php
						for ($i = 0; $i < $char_count; $i++) {
							if ($charactersId[$i] == $inv['player_id']) {
								echo '<td class="py-1 px-2">';
								echo '<input type="hidden" name="uninvite" value="' . $inv['player_id'] . '" />';
								echo '<button type="submit" class="'. guild_button_class() .'">Reject Invitation</button>';
								echo '</td>';
								$bool = true;
							}
						}
						if (isset($bool, $exist) && !$bool && $exist) {
							echo '<td class="py-1 px-2"></td>';
							$bool = false;
						}
					?> </form> <?php
					echo '</tr>';
				}
				?>
		</table>
		</div>
	<?php endif; ?>

	<!-- Leader stuff -->
	<?php
	if (user_logged_in() === true) {

		// Uninvite and joinguild is also used for visitors who reject their invitation.
		if (!empty($_POST['uninvite'])) {
			// Is this action being triggered by a vice leader+, or the invited player?
			$u_player = (int)$_POST['uninvite'];
			$u_player = user_character_name($u_player);
			$u_player = (int)user_character_account_id($u_player);
			if (in_array($highest_access, array(2,3)) || $u_player === $session_user_id) {
				guild_remove_invitation($_POST['uninvite'], $gid);
				header('Location: guilds.php?name='. $_GET['name']);
				exit();
			}
		}

		// Join guild
		if (!empty($_POST['joinguild'])) {
			$joining_player_id = (int)$_POST['joinguild'];
			$join_account = (int)user_character_account_id(user_character_name($joining_player_id));
			
			if ($join_account !== $session_user_id) {
				render_guild_notice('Join guild request sent from wrong account.', 'error');
				include 'layout/overall/footer.php';
				exit();
			}

			// Join a guild
			if ($inv_data !== false) foreach ($inv_data as $inv) {
				if ((int)$inv['player_id'] == $joining_player_id) {
					$chardata['online'] = (user_is_online_10($joining_player_id)) ? 1 : 0;
					if ($chardata['online'] == 0) {
						
						// Ensure player is not already a member of another guild
						if (get_character_guild_rank($joining_player_id) === false) {
							if (guild_player_join($joining_player_id, (int)$gid)) {
								header('Location: guilds.php?name='. $_GET['name']);
								exit();
							} else render_guild_notice('Failed to find guild position representing member.', 'error');
						
						} else {
							$already_guild = get_player_guild_data($joining_player_id);
							$already_guild_name = get_guild_name($already_guild['guild_id']);
							
							render_guild_notice("You are already <strong>{$already_guild['rank_name']}</strong> of another guild: <strong><a href='guilds.php?name={$already_guild_name}'>{$already_guild_name}</a></strong>.<br>You need to leave that guild first before you can join another one.", 'error');
						}

					} else render_guild_notice('Character must be offline before joining guild.', 'error');
				}
			}
		}

		// leave guild
		if (!empty($_POST['leave_guild'])) {
			$name = sanitize($_POST['leave_guild']);
			$cidd = user_character_id($name);
			
			$leave_account = (int)user_character_account_id($name);
			if ($leave_account !== $session_user_id) {
				render_guild_notice('Leave guild request sent from wrong account.', 'error');
				include 'layout/overall/footer.php';
				exit();
			}

			// If character is offline
			$chardata['online'] = (user_is_online_10($cidd)) ? 1 : 0;
			if ($chardata['online'] == 0) {
				guild_player_leave_10($cidd);
				header('Location: guilds.php?name='. $_GET['name']);
				exit();
			} else render_guild_notice('Character must be offline first!', 'error');
		}

		// vice leader (and guild leader) actions
		if ($highest_access >= 2) {
			// Vice-leaders and leaders
			// Change Guild Nick
			if (!empty($_POST['player_guildnick'])) {
				if ($config['guild_allow_nicknames']) {
					$p_cid = user_character_id($_POST['player_guildnick']);
					$p_guild = get_player_guild_data($p_cid);
					
					if (preg_match("/^[a-zA-Z_ ]+$/", $_POST['guildnick']) || empty($_POST['guildnick'])) {
						// Only allow normal symbols as guild nick
						$p_nick = sanitize($_POST['guildnick']);
						
						if ($p_guild['guild_id'] == $gid) {
							$chardata['online'] = (user_is_online_10($p_cid)) ? 1 : 0;
							
							if ($chardata['online'] == 0) {
								update_player_guildnick_10($p_cid, $p_nick);
								header('Location: guilds.php?name='. $_GET['name']);
								exit();
							} else render_guild_notice('Character not offline.', 'error');
						}

					} else render_guild_notice('Character guild nick may only contain a-z, A-Z and spaces.', 'error');
				} else render_guild_notice('Change guild nickname feature has been disabled.', 'error');
			}

			// Invite character to guild
			if (!empty($_POST['invite'])) {
				if (user_character_exist($_POST['invite'])) {
					// Make sure they are not in another guild
					$charid = user_character_id($_POST['invite']);
					$membership = mysql_select_single("SELECT `rank_id` FROM `guild_membership` WHERE `player_id`='{$charid}' LIMIT 1;");
					if (!$membership) {
						$status = false;
						if ($inv_data !== false) {
							foreach ($inv_data as $inv) {
								if ($inv['player_id'] == user_character_id($_POST['invite'])) $status = true;
							}
						}
						foreach ($players as $player) {
							if ($player['name'] == $_POST['invite']) $status = true;
						}

						if ($status == false) {
							guild_invite_player($charid, $gid);
							header('Location: guilds.php?name='. $_GET['name']);
							exit();
						} else render_guild_notice('That character is already invited(or a member) on this guild.', 'error');
					} else render_guild_notice('That character is already in a guild.', 'error');

				} else render_guild_notice('That character name does not exist.', 'error');
			}

			// Guild Message (motd)
			if (!empty($_POST['motd'])) {
				$motd = sanitize($_POST['motd']);
				mysql_update("UPDATE `guilds` SET `motd`='{$motd}' WHERE `id`='{$gid}' LIMIT 1;");
				header('Location: guilds.php?name='. $_GET['name']);
				exit();
			}

			// Remove guild member
			if (!empty($_POST['remove_member'])) {
				$name = sanitize($_POST['remove_member']);
				$cid = user_character_id($name);
				
				$p_guild = get_player_guild_data($cid);
				if ($p_guild['guild_id'] == $gid) {
					guild_remove_member_10($cid);
					header('Location: guilds.php?name='. $_GET['name']);
					exit();
				}
			}

			// Create forum guild board
			if (!empty($_POST['forumGuildId'])) {
				
				if ($config['forum']['guildboard'] === true) {
					$forumExist = mysql_select_single("SELECT `id` FROM `znote_forum` WHERE `guild_id`='{$gid}' LIMIT 1;");
						
					if ($forumExist === false) {
						// Insert data
						mysql_insert("
							INSERT INTO `znote_forum` 
							(`name`, `access`, `closed`, `hidden`, `guild_id`)
							VALUES ('Guild','1','0','0','{$gid}')
						;");
						render_guild_notice('Guild board has been created.', 'success');
					} else render_guild_notice('Guild board already exist.', 'error');

				} else {
					render_guild_notice('Error: Guild board system is disabled.', 'error');
				}
			}

			// Guildwar actions
			if ($config['guildwar_enabled'] === true) {

				// Invite another guild to war
				if (!empty($_POST['warinvite'])) {
					$targetGuild = get_guild_id($_POST['warinvite']);
					if ($targetGuild) {
						$status = false;
						
						$war_invite = mysql_select_single("SELECT `id` FROM `guilds` WHERE `id` = '$gid';");
						if ($war_invite !== false) {
							foreach ($war_invite as $inv) {
								if ($inv['id'] == $targetGuild) $status = true;
							}
						}

						$check_guild = get_guild_name($gid);
						foreach ($check_guild as $guild) {
							if ($guild['name'] == $_POST['warinvite']) $status = true;
						}

						if ((int)$gid === (int)$targetGuild) $status = true;

						$wars = mysql_select_multi("
							SELECT 
								`id`, `guild1`, `guild2`, `status` 
							FROM `guild_wars` 
							WHERE (`guild1` = '$gid' OR `guild1` = '$targetGuild') 
							AND (`guild2` = '$gid' OR `guild2` = '$targetGuild') 
							AND `status` IN (0, 1)
						;");

						if ($status == false && $wars == false) {
							guild_war_invitation($gid, $targetGuild);
							$limit = (empty($_POST['limit'])) ? 100 : (int)$_POST['limit'];
							mysql_insert("INSERT INTO `znote_guild_wars` (`limit`) VALUES ('$limit');");
							header('Location: guilds.php?name='. $_GET['name']);
							exit();
						} else render_guild_notice('This guild has already been invited to war(or you\'re trying to invite your own).', 'error');

					} else render_guild_notice('That guild name does not exist.', 'error');
				}

				if (!empty($_POST['cancel_war_invite'])) {
					cancel_war_invitation($_POST['cancel_war_invite'], $gid);
					header('Location: guilds.php?name='. $_GET['name']);
					exit();
				}

				if (!empty($_POST['reject_war_invite'])) {
					reject_war_invitation($_POST['reject_war_invite'], $gid);
					header('Location: guilds.php?name='. $_GET['name']);
					exit();
				}

				if (!empty($_POST['accept_war_invite'])) {
					accept_war_invitation($_POST['accept_war_invite'], $gid);
					header('Location: guilds.php?name='. $_GET['name']);
					exit();
				}
			}

			$members = count_guild_members($gid);
			$ranks = get_guild_rank_data($gid);
			

			// Form to upload guild logo
			if ($config['use_guild_logos']) {
				?>
				<form action="" method="post" enctype="multipart/form-data" class="space-y-2 text-xs">
					<label for="file" class="block font-medium">Upload guild logo <span class="text-[var(--text-muted)]">.gif only, 100x100px</span></label>
					<div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2 items-center">
						<input type="file" name="file" id="file" accept="image/gif" class="<?php echo guild_input_class('file:mr-3 file:border-0 file:bg-[var(--btn-primary-from)] file:text-[var(--btn-primary-text)] file:px-2 file:py-1 file:text-[11px] file:font-semibold file:cursor-pointer cursor-pointer'); ?>">
						<button type="submit" name="submit" class="<?php echo guild_button_class(); ?>">Upload guild logo</button>
					</div>
				</form>
				<?php
				if (!empty($_FILES['file'])) {
					check_image($_FILES['file']);
					echo '<br>';
				}
			}
			?>

			<!-- forms to invite character -->
			<form action="" method="post" class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2 items-end text-xs">
				<div>
					<label for="guild_invite" class="block font-medium mb-1">Invite Character to guild</label>
					<input id="guild_invite" type="text" name="invite" placeholder="Character name" class="<?php echo guild_input_class(); ?>">
				</div>
				<button type="submit" class="<?php echo guild_button_class(); ?>">Invite Character</button>
			</form>

			<!-- Guild message of the day motd -->
			<form action="" method="post" class="space-y-2 text-xs">
				<label for="guild_motd" class="block font-medium">Change guild message</label>
				<textarea id="guild_motd" name="motd" placeholder="Guild Message" rows="3" class="<?php echo guild_input_class('min-h-[72px] resize-y'); ?>"><?php echo htmlspecialchars($guild['motd'], ENT_QUOTES, 'UTF-8'); ?></textarea>
				<div class="flex justify-end">
					<button type="submit" class="<?php echo guild_button_class(); ?>">Update guild message</button>
				</div>
			</form>

			<?php if ($config['guild_allow_nicknames']): ?>
				<!-- FORMS TO CHANGE GUILD NICK -->
				<form action="" method="post" class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] gap-2 items-end text-xs">
					<div>
						<label for="player_guildnick" class="block font-medium mb-1">Change Guild Nick</label>
						<select id="player_guildnick" name="player_guildnick" class="<?php echo guild_input_class(); ?>">
							<?php
							//$gid = get_guild_id($_GET['name']);
							//$players = get_guild_players($gid);
							foreach ($players as $player) {
								if ($player['rank_level'] != 3) {
									echo '<option value="'. $player['name'] .'">'. $player['name'] .'</option>';
								} else {
									if ($highest_access == 3) {
										echo '<option value="'. $player['name'] .'">'. $player['name'] .'</option>';
									}
								}
							}
							?>
						</select>
					</div>
					<div>
						<label for="guildnick" class="block font-medium mb-1">Nick</label>
						<input id="guildnick" type="text" name="guildnick" maxlength="15" placeholder="leave blank to erase" class="<?php echo guild_input_class(); ?>">
					</div>
					<button type="submit" class="<?php echo guild_button_class(); ?>">Change Nick</button>
				</form>
			<?php endif; ?>
				
			<?php if ($members > 1): ?>
				<!-- FORMS TO PROMOTE CHARACTER-->
				<form action="" method="post" class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] gap-2 items-end text-xs">
					<div>
						<label for="promote_character" class="block font-medium mb-1">Promote Character</label>
						<select id="promote_character" name="promote_character" class="<?php echo guild_input_class(); ?>">
							<?php
							//$gid = get_guild_id($_GET['name']);
							//$players = get_guild_players($gid);
							foreach ($players as $player) {
								if ($player['rank_level'] != 3) {
									echo '<option value="'. $player['name'] .'">'. $player['name'] .'</option>';
								}
							}
							?>
						</select>
					</div>
					<div>
						<label for="promote_position" class="block font-medium mb-1">New rank</label>
						<select id="promote_position" name="promote_position" class="<?php echo guild_input_class(); ?>">
								<?php
								foreach ($ranks as $rank) {
									if ($rank['level'] != 3) {
										if ($rank['level'] != 2) {
											echo '<option value="'. $rank['id'] .'">'. $rank['name'] .'</option>';
										} else {
											if ($highest_access == 3) {
												echo '<option value="'. $rank['id'] .'">'. $rank['name'] .'</option>';
											}
										}
									}
								}
								?>
						</select>
					</div>
					<button type="submit" class="<?php echo guild_button_class(); ?>">Promote Member</button>
				</form>

				<!-- Remove member from guild -->
				<form action="" method="post" class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2 items-end text-xs">
					<div>
						<label for="remove_member" class="block font-medium mb-1">Kick member from guild</label>
						<select id="remove_member" name="remove_member" class="<?php echo guild_input_class(); ?>">
								<?php
								//$gid = get_guild_id($_GET['name']);
								//$players = get_guild_players($gid);
								foreach ($players as $player) {
									if ($player['rank_level'] != 3) {
										if ($player['rank_level'] != 2) {
											echo '<option value="'. $player['name'] .'">'. $player['name'] .'</option>';
										} else {
											if ($highest_access == 3) {
												echo '<option value="'. $player['name'] .'">'. $player['name'] .'</option>';
											}
										}
									}
								}
								?>
						</select>
					</div>
					<button type="submit" class="<?php echo guild_button_class(); ?>">Remove member</button>
				</form>
			<?php endif; ?>
			<br><br>
			<?php 
		} // end vice leader+

		// Guild leaders only
		if ($highest_access == 3) { 
			// Promote character to guild position
			if (!empty($_POST['promote_character']) && !empty($_POST['promote_position'])) {
				// Verify that promoted character is from this guild.
				$p_rid = $_POST['promote_position'];
				$p_cid = user_character_id($_POST['promote_character']);
				$p_guild = get_player_guild_data($p_cid);

				if ($p_guild['guild_id'] == $gid) {
					$chardata['online'] = (user_is_online_10($p_cid)) ? 1 : 0;
					if ($chardata['online'] == 0) {
						update_player_guild_position_10($p_cid, $p_rid);
						header('Location: guilds.php?name='. $_GET['name']);
						exit();
					} else render_guild_notice('Character not offline.', 'error');
				}
			}
			
			// Disband guild
			if (!empty($_POST['disband'])) {
				//$gidd = (int)$_POST['disband'];
				$members = get_guild_players($gid);
				$online = false;

				// First figure out if anyone are online.
				foreach ($members as $member) {
					$chardata['online'] = (user_is_online_10(user_character_id($member['name']))) ? 1 : 0;
					if ($chardata['online'] == 1) {
						$online = true;
					}
				}

				if (!$online) {
					// Then remove guild rank from every player.
					foreach ($members as $member) 
						guild_player_leave_10(user_character_id($member['name']));

					// Remove all guild invitations to this guild
					if ($inv_count > 0) 
						guild_remove_invites($gid);

					// Then remove the guild itself.
					guild_delete($gid);
					header('Location: success.php');
					exit();
				} else render_guild_notice('All members must be offline to disband the guild.', 'error');
			}
			
			// Change guild leader
			if (!empty($_POST['new_leader'])) {
				$new_leader = (int)$_POST['new_leader'];
				$old_leader = guild_leader($gid);

				$online = false;
				$newData['online'] = (user_is_online_10($new_leader)) ? 1 : 0;
				$oldData['online'] = (user_is_online_10($old_leader)) ? 1 : 0;

				if ($newData['online'] == 1 || $oldData['online'] == 1) $online = true;

				if ($online == false) {
					if (guild_change_leader($new_leader, $old_leader)) {
						header('Location: guilds.php?name='. $_GET['name']);
						exit();
					} else render_guild_notice('Something went wrong when attempting to change leadership.', 'error');
				} else render_guild_notice('The new and old leader must be offline to change leadership.', 'error');
			}
			
			// Change guild ranks
			if (!empty($_POST['change_ranks'])) {
				//$c_gid = (int)$_POST['change_ranks'];
				$c_ranks = get_guild_rank_data($gid);
				$rank_data = array();
				$rank_ids = array();

				// Feed new rank data
				foreach ($c_ranks as $rank) {
					$tmp = 'rank_name!'. $rank['level'];
					if (!empty($_POST[$tmp])) {
						$rank_data[$rank['level']] = sanitize($_POST[$tmp]);
						$rank_ids[$rank['level']] = $rank['id'];
					}
				}

				// Change guild rank name.
				foreach ($rank_data as $level => $name) {
					$rid = (int)$rank_ids[$level];
					$name = sanitize($name);
					mysql_update("UPDATE `guild_ranks` SET `name`='{$name}' WHERE `id`={$rid}");
				}

				header('Location: guilds.php?name='. $_GET['name']);
				exit();
			}
			?>
			
			<!-- forms to change rank titles -->
			<form action="" method="post" class="space-y-2 text-xs">
				<div class="font-semibold">Change rank titles:</div>
				<div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
					<?php
					$rank_count = 1;
					foreach ($ranks as $rank) {
						echo '<input type="text" class="'. guild_input_class() .'" name="rank_name!'. (int)$rank['level'] .'" value="'. htmlspecialchars($rank['name'], ENT_QUOTES, 'UTF-8') .'">';
					}
					echo '<input type="hidden" name="change_ranks" value="' . $gid . '" />';
					?>
				</div>
				<button type="submit" class="<?php echo guild_button_class(); ?>">Update Ranks</button>
			</form>

			<!-- forms to disband guild -->
			<form action="" method="post" class="flex flex-wrap items-center gap-2 text-xs">
				<span class="font-semibold">DELETE GUILD <span class="text-[var(--text-muted)]">(All members must be offline)</span>:</span>
				<?php echo '<input type="hidden" name="disband" value="' . $gid . '" />'; ?>
				<button type="submit" class="<?php echo guild_button_class(); ?>">Disband Guild</button>
			</form>

			<!-- forms to change leadership-->
			<?php if ($members > 1): ?>
				<form action="" method="post" class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2 items-end text-xs">
					<div>
						<label for="new_leader" class="block font-semibold mb-1">Change Leadership with</label>
						<select id="new_leader" name="new_leader" class="<?php echo guild_input_class(); ?>">
							<?php
							//$gid = get_guild_id($_GET['name']);
							//$players = get_guild_players($gid);
							foreach ($players as $player) {
								if ($player['rank_level'] != 3) {
									echo '<option value="'. $player['id'] .'">'. $player['name'] .'</option>';
								}
							}
							?>
						</select>
					</div>
					<button type="submit" class="<?php echo guild_button_class(); ?>">Change Leadership</button>
				</form>
			<?php endif; ?>

			<!-- Guild war management -->
			<?php if ($config['guildwar_enabled'] === true) { ?>
				<div class="PageHeader-module__HZea0q__header ">
					<div class="PageHeader-module__HZea0q__headerContent">
						<h2 class="PageHeader-module__HZea0q__title">Guild War Management</h2>
					</div>
				</div>
				<form action="" method="post" class="grid grid-cols-1 sm:grid-cols-[1fr_120px_auto] gap-2 items-end text-xs">
					<div>
						<label for="warinvite" class="block font-medium mb-1">Invite guild to war</label>
						<input id="warinvite" type="text" name="warinvite" placeholder="Guild name" class="<?php echo guild_input_class(); ?>">
					</div>
					<div>
						<label for="war_limit" class="block font-medium mb-1">Frag limit</label>
						<input id="war_limit" type="number" min="10" max="999" name="limit" class="<?php echo guild_input_class(); ?>">
					</div>
					<button type="submit" class="<?php echo guild_button_class(); ?>">Invite Guild</button>
				</form>

				<style type="text/css">
					.guild-war-action-form {display: inline;}
					#btnspace{margin-left:100px;}
				</style>
				
				<div class="overflow-x-auto">
				<table id="guildsTable" class="w-full text-xs">
					<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
						<th class="py-1.5 px-2 text-left">Aggressor</th>
						<th class="py-1.5 px-2 text-center">Information</th>
						<th class="py-1.5 px-2 text-left">Enemy</th>
					</tr>
					<?php
					$i = 0;
					$wars = mysql_select_multi("
						SELECT 
							`guild1`, `guild2`, `name1`, `name2`, `started`, 
							(	SELECT `limit` 
								FROM `znote_guild_wars` 
								WHERE `znote_guild_wars`.`id` = `guild_wars`.`id`
							) AS `limit` 
						FROM `guild_wars` 
						WHERE (`guild1` = '$gid' OR `guild2` = '$gid') 
						AND `status` = 0 
						ORDER BY `started` DESC
					");
					if (!empty($wars) || $wars !== false) {
						foreach($wars as $war): 
							$i++;
							$row = "<tr class='border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)]'>";
							$row .= "<td class='py-1 px-2'><a class='text-[var(--text-link)] hover:text-[var(--text-link-hover)] hover:underline' href='guilds.php?name={$war['name1']}'>{$war['name1']}</a></td>";
							
							$row .= '<td class="py-1 px-2"><center>';

							$row .= '<b>Pending invitation</b>';
							$row .= '<br />Invited on '. getClock($war['started'], true) .'.';
							$row .= "<br />The frag limit is set to {$war['limit']} frags.<br />";

							if ($war['guild1'] == $gid) {
								$row .= '<br><form action="" method="post" class="guild-war-action-form">';
								$row .= "<input type='hidden' name='cancel_war_invite' value='{$war['guild2']}' />";
								$row .= '<button type="submit" class="'. guild_button_class() .'">Cancel Invitation</button>';
								$row .= '</form>';
							} else if ($war['guild2'] == $gid) {
								$row .= '<br><form action="" method="post" class="guild-war-action-form">';
								$row .= "<input type='hidden' name='accept_war_invite' value='{$war['guild1']}' />";
								$row .= '<button type="submit" class="'. guild_button_class() .'">Accept Invitation</button>';
								$row .= '</form>';

								$row .= '<form action="" method="post" class="guild-war-action-form">';
								$row .= "<input type='hidden' name='reject_war_invite' value='{$war['guild1']}' />";
								$row .= '<button type="submit" id="btnspace" class="'. guild_button_class() .'">Reject Invitation</button>';
								$row .= '</form>';
							}
							$row .= '</center></td>';
							$row .= "<td class='py-1 px-2'><a class='text-[var(--text-link)] hover:text-[var(--text-link-hover)] hover:underline' href='guilds.php?name={$war['name2']}'>{$war['name2']}</a></td>";
							$row .= '</tr>';
							echo $row;
						endforeach;
					}
					
					if ($i == 0) echo '<tr><td class="py-1 px-2" colspan="3"><center>Currently there are no pending invitations.</center></td></tr>';
					?>
				</table>
				</div>
				<?php
			}
		} // end leader+
	} // end user_logged_in() === true
	?>
	<!-- end leader and vice leader -->

	<?php
	if ($config['guildwar_enabled'] === true) {
		$wardata = get_guild_wars();
		$war_exist = false;
		if ($wardata !== false) {
			foreach ($wardata as $wars) {
				if ($wars['guild1'] == $gid || $wars['guild2'] == $gid) $war_exist = true;
			}
		}

		if ($war_exist) {
			?>
			<div class="PageHeader-module__HZea0q__header ">
				<div class="PageHeader-module__HZea0q__headerContent">
					<h2 class="PageHeader-module__HZea0q__title">War overview</h2>
				</div>
			</div>
			<div class="overflow-x-auto">
			<table class="w-full text-xs">
				<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
					<td class="py-1.5 px-2">Attacker</td>
					<td class="py-1.5 px-2">Defender</td>
					<td class="py-1.5 px-2">Status</td>
					<td class="py-1.5 px-2">Started</td>
				</tr>
				<?php
				foreach ($wardata as $wars): 
					if ($wars['guild1'] == $gid || $wars['guild2'] == $gid): 
						$url = url("guildwar.php?warid=". $wars['id']);
						?>
						<tr class="special border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)]" onclick="javascript:window.location.href='<?php echo $url; ?>'">
							<td class="py-1 px-2"><?php echo htmlspecialchars($wars['name1'], ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="py-1 px-2"><?php echo htmlspecialchars($wars['name2'], ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="py-1 px-2"><?php echo htmlspecialchars($config['war_status'][$wars['status']], ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="py-1 px-2"><?php echo htmlspecialchars(getClock($wars['started'], true), ENT_QUOTES, 'UTF-8'); ?></td>
						</tr>
						<?php
					endif;
				endforeach;
				?>
			</table>
			</div>
			<?php
		}
	}
	?>

	<!-- leave guild with character -->
	<?php
	$bool = false;
	if (user_logged_in() === true) {
		for ($i = 0; $i < $char_count; $i++) {
			foreach ($players as $player) {
				if ($player['name'] == $characters[$i]) $bool = true;
			}
		}
		if ($bool) {
			$forumExist = mysql_select_single("SELECT `id` FROM `znote_forum` WHERE `guild_id`='{$gid}' LIMIT 1;");
			if ($forumExist !== false) {
				?> - <font size="4"><a href="forum.php?cat=<?php echo $forumExist['id']; ?>">Visit Guild Board</a></font><br><br><br><?php
			}
			?>
			<form action="" method="post" class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2 items-end text-xs">
				<div>
					<label for="leave_guild" class="block font-medium mb-1">Leave Guild</label>
					<select id="leave_guild" name="leave_guild" class="<?php echo guild_input_class(); ?>">
							<option disabled>With...</option>
						<?php
						for ($i = 0; $i < $char_count; $i++) {
							foreach ($players as $player) {
								if ($player['name'] == $characters[$i]) {
									$data = get_player_guild_data($player['id']);
									if ($data['rank_level'] != 3) echo '<option value="'. $characters[$i] .'">'. $characters[$i] .'</option>';
									else echo '<option disabled>'. $characters[$i] .' [disabled:Leader]</option>';
								}
							}
						}
						?>
					</select>
				</div>
				<button type="submit" class="<?php echo guild_button_class(); ?>">Leave Guild</button>
			</form>
		<?php
		} // display form if user has a character in guild
	} // user logged in
	?>
				</div>
			</div>
		</div>
	</div>
	<?php
} // end view specific guild
include 'layout/overall/footer.php'; ?>
