<?php require_once 'engine/init.php'; include 'layout/overall/header.php';

if ($config['log_ip']) {
	znote_visitor_insert_detailed_data(3);
}

// Fetch highscore type
$type = (isset($_GET['type'])) ? (int)getValue($_GET['type']) : 7;
if ($type > 9) $type = 7;

// Fetch highscore vocation
$configVocations = $config['vocations'];
//$debug['configVocations'] = $configVocations;

$vocationIds = array_keys($configVocations);

$vocation = 'all';
if (isset($_GET['vocation']) && is_numeric($_GET['vocation'])) {
	$vocation = (int)$_GET['vocation'];
	if (!in_array($vocation, $vocationIds)) {
		$vocation = "all";
	}
}

// Fetch highscore page
$page = getValue(@$_GET['page']);
if (!$page || $page == 0) $page = 1;
else $page = (int)$page;

$highscore = $config['highscore'];
$loadFlags = ($config['country_flags']['enabled'] && $config['country_flags']['highscores']) ? true : false;
$loadOutfits = ($config['show_outfits']['highscores']) ? true : false;

$rows = $highscore['rows'];
$rowsPerPage = $highscore['rowsPerPage'];

function skillName($type) {
	$types = array(
		1 => "Club",
		2 => "Sword",
		3 => "Axe",
		4 => "Distance",
		5 => "Shield",
		6 => "Fish",
		7 => "Experience", // Hardcoded
		8 => "Magic Level", // Hardcoded
		9 => "Fist", // Since 0 returns false I will make 9 = 0. :)
	);
	return $types[(int)$type];
}

function pageCheck($index, $page, $rowPerPage) {
	return ($index < ($page * $rowPerPage) && $index >= ($page * $rowPerPage) - $rowPerPage) ? true : false;
}

$cache = new Cache('engine/cache/highscores');
if ($cache->hasExpired()) {
	$vocGroups = fetchAllScores($rows, $config['ServerEngine'], $highscore['ignoreGroupId'], $configVocations, $vocation, $loadFlags, $loadOutfits);
	$cache->setContent($vocGroups);
	$cache->save();
} else {
	$vocGroups = $cache->load();
}

if ($vocGroups) {
	$vocGroup = (is_array($vocGroups[$vocation])) ? $vocGroups[$vocation] : $vocGroups[$vocGroups[$vocation]];
	?>
	<?php
	$selected_vocation_label = ($vocation === 'all') ? 'Any vocation' : vocation_id_to_name($vocation);
	$pages = ($vocGroup[$type] !== false) ? ceil(min(($highscore['rows'] / $highscore['rowsPerPage']), (count($vocGroup[$type]) / $highscore['rowsPerPage']))) : 1;
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trophy w-4 h-4" aria-hidden="true"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"></path></svg>
						Highscores
					</span>
				</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="space-y-3">
					<div class="text-xs text-[var(--text-muted)]">
						Ranking for <b><?php echo htmlspecialchars(skillName($type), ENT_QUOTES, 'UTF-8'); ?></b>, <?php echo htmlspecialchars($selected_vocation_label, ENT_QUOTES, 'UTF-8'); ?>.
					</div>

					<form action="" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-2 items-end">
						<div>
							<label for="hs_type" class="block text-xs font-medium mb-1">Type</label>
							<select id="hs_type" name="type" class="text-[10px] px-2 py-1 rounded border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)] text-[var(--text-muted)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)]">
								<option value="7" <?php if ($type == 7) echo "selected"; ?>>Experience</option>
								<option value="8" <?php if ($type == 8) echo "selected"; ?>>Magic</option>
								<option value="5" <?php if ($type == 5) echo "selected"; ?>>Shield</option>
								<option value="2" <?php if ($type == 2) echo "selected"; ?>>Sword</option>
								<option value="1" <?php if ($type == 1) echo "selected"; ?>>Club</option>
								<option value="3" <?php if ($type == 3) echo "selected"; ?>>Axe</option>
								<option value="4" <?php if ($type == 4) echo "selected"; ?>>Distance</option>
								<option value="6" <?php if ($type == 6) echo "selected"; ?>>Fish</option>
								<option value="9" <?php if ($type == 9) echo "selected"; ?>>Fist</option>
							</select>
						</div>

						<div>
							<label for="hs_vocation" class="block text-xs font-medium mb-1">Vocation</label>
							<select id="hs_vocation" name="vocation" class="text-[10px] px-2 py-1 rounded border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)] text-[var(--text-muted)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)]">
								<option value="all" <?php if (!is_int($vocation)) echo "selected"; ?>>Any vocation</option>
								<?php
								foreach ($configVocations as $v_id => $v_data) {
									if ($v_data['fromVoc'] === false) {
										$selected = (is_int($vocation) && $vocation == $v_id) ? ' selected' : '';
										echo '<option value="'. (int)$v_id .'"'. $selected .'>'. htmlspecialchars($v_data['name'], ENT_QUOTES, 'UTF-8') .'</option>';
									}
								}
								?>
							</select>
						</div>

						<div>
							<label for="hs_page" class="block text-xs font-medium mb-1">Page</label>
							<select id="hs_page" name="page" class="text-[10px] px-2 py-1 rounded border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)] text-[var(--text-muted)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)]">
								<?php
								for ($i = 0; $i < $pages; $i++) {
									$x = $i + 1;
									if ($x == $page) echo "<option value='". (int)$x ."' selected>Page: ". (int)$x ."</option>";
									else echo "<option value='". (int)$x ."'>Page: ". (int)$x ."</option>";
								}
								?>
							</select>
						</div>

						<div class="flex sm:justify-end">
							<button type="submit" class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer">
								View
							</button>
						</div>
					</form>

					<div class="overflow-x-auto">
						<table id="highscoresTable" class="w-full text-xs">
							<tr class="border-b border-[var(--box-inner-border)] text-left">
								<?php if ($loadOutfits) echo '<th class="py-2 pr-3 font-semibold">Outfit</th>'; ?>
								<th class="py-2 pr-3 font-semibold">Rank</th>
								<th class="py-2 pr-3 font-semibold">Name</th>
								<th class="py-2 pr-3 font-semibold">Vocation</th>
								<th class="py-2 pr-3 font-semibold">Level</th>
								<?php if ($type === 7) echo '<th class="py-2 pr-3 font-semibold">Points</th>'; ?>
							</tr>

							<?php
							if ($vocGroup[$type] === false) {
								$colspan = $loadOutfits ? (($type === 7) ? 6 : 5) : (($type === 7) ? 5 : 4);
								echo '<tr><td colspan="'. (int)$colspan .'" class="py-2">Nothing to show here yet.</td></tr>';
							} else {
								for ($i = 0; $i < count($vocGroup[$type]); $i++) {
									if (pageCheck($i, $page, $rowsPerPage)) {
										$flag = ($loadFlags === true && isset($vocGroup[$type][$i]['flag']) && strlen($vocGroup[$type][$i]['flag']) > 1)
											? '<img class="inline-block w-4 h-3 mr-1 align-middle" src="' . htmlspecialchars($config['country_flags']['server'], ENT_QUOTES, 'UTF-8') . '/' . htmlspecialchars($vocGroup[$type][$i]['flag'], ENT_QUOTES, 'UTF-8') . '.png" alt="flag">'
											: '';
										?>
										<tr class="border-b border-[var(--box-inner-border)] last:border-b-0">
											<?php if ($loadOutfits): ?>
												<td class="py-2 pr-3"><img src="<?php echo htmlspecialchars($config['show_outfits']['imageServer'], ENT_QUOTES, 'UTF-8'); ?>?id=<?php echo (int)$vocGroup[$type][$i]['type']; ?>&addons=<?php echo (int)$vocGroup[$type][$i]['addons']; ?>&head=<?php echo (int)$vocGroup[$type][$i]['head']; ?>&body=<?php echo (int)$vocGroup[$type][$i]['body']; ?>&legs=<?php echo (int)$vocGroup[$type][$i]['legs']; ?>&feet=<?php echo (int)$vocGroup[$type][$i]['feet']; ?>" alt="outfit"></td>
											<?php endif; ?>
											<td class="py-2 pr-3">
												<?php
												$rank = (int)($i + 1);
												$rankClass = '';
												if ($rank === 1) $rankClass = 'text-[#ffd700] font-bold rank-gold';
												elseif ($rank === 2) $rankClass = 'text-[#c0c0c0] font-bold rank-silver';
												elseif ($rank === 3) $rankClass = 'text-[#cd7f32] font-bold rank-bronze';
												?>
												<span class="<?php echo $rankClass; ?>"><?php echo $rank; ?>.</span>
											</td>
											<td class="py-2 pr-3"><?php echo $flag; ?><a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)]" href="characterprofile.php?name=<?php echo urlencode($vocGroup[$type][$i]['name']); ?>"><?php echo htmlspecialchars($vocGroup[$type][$i]['name'], ENT_QUOTES, 'UTF-8'); ?></a></td>
											<td class="py-2 pr-3"><?php echo htmlspecialchars(vocation_id_to_name($vocGroup[$type][$i]['vocation']), ENT_QUOTES, 'UTF-8'); ?></td>
											<td class="py-2 pr-3"><?php echo (int)$vocGroup[$type][$i]['value']; ?></td>
											<?php if ($type === 7) echo '<td class="py-2 pr-3">'. (int)$vocGroup[$type][$i]['experience'] .'</td>'; ?>
										</tr>
										<?php
									}
								}
							}
							?>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
include 'layout/overall/footer.php'; ?>
