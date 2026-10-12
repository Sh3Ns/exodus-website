<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; 

$history = array(
	"enabled" => true,
	"days" => 14,
	"cache" => 300
);

// Returns a list of players online
$array = false;
$loadFlags = ($config['country_flags']['enabled'] && $config['country_flags']['onlinelist']) ? true : false;
$loadOutfits = ($config['show_outfits']['onlinelist']) ? true : false;
if ($config['client'] < 780) {
	$outfitQuery = ($loadOutfits) ? ", `p`.`lookbody` AS `body`, `p`.`lookfeet` AS `feet`, `p`.`lookhead` AS `head`, `p`.`looklegs` AS `legs`, `p`.`looktype` AS `type`" : "";
} else {
	$outfitQuery = ($loadOutfits) ? ", `p`.`lookbody` AS `body`, `p`.`lookfeet` AS `feet`, `p`.`lookhead` AS `head`, `p`.`looklegs` AS `legs`, `p`.`looktype` AS `type`, `p`.`lookaddons` AS `addons`" : "";
}

// Small 30 seconds players_online cache.
$cache = new Cache('engine/cache/onlinelist');
$cache->setExpiration(30);
if ($cache->hasExpired()) {
	// Load online list data from SQL
	$array = ($loadFlags === true) ? mysql_select_multi("SELECT `p`.`name` AS `name`, `p`.`level` AS `level`, `p`.`vocation` AS `vocation`, `g`.`name` AS `gname`, `za`.`flag` AS `flag` $outfitQuery FROM `players_online` AS `o` INNER JOIN `players` AS `p` ON `o`.`player_id` = `p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id` = `za`.`account_id` LEFT JOIN `guild_membership` AS `gm` ON `o`.`player_id` = `gm`.`player_id` LEFT JOIN `guilds` AS `g` ON `gm`.`guild_id` = `g`.`id`;") : mysql_select_multi("SELECT `p`.`name` AS `name`, `p`.`level` AS `level`, `p`.`vocation` AS `vocation`, `g`.`name` AS `gname` $outfitQuery FROM `players_online` AS `o` INNER JOIN `players` AS `p` ON `o`.`player_id` = `p`.`id` LEFT JOIN `guild_membership` AS `gm` ON `o`.`player_id` = `gm`.`player_id` LEFT JOIN `guilds` AS `g` ON `gm`.`guild_id` = `g`.`id`;");
	// End loading data from SQL
	$cache->setContent($array);
	$cache->save();
} else {
	$array = $cache->load();
}
// End cache

// 5 minute logout history cache
if ($history["enabled"]) {
	$time = time();
	$cache = new Cache('engine/cache/onlinelist_rec');
	$cache->setExpiration($history['cache']);
	if ($cache->hasExpired()) {
		// Load online list data from SQL
		$recents = ($loadFlags === true) ? mysql_select_multi("
			SELECT 
				`p`.`name` AS `name`, 
				`p`.`level` AS `level`, 
				`p`.`vocation` AS `vocation`, 
				`p`.`lastlogout`,
				`g`.`name` AS `gname`, 
				`za`.`flag` AS `flag` 
				$outfitQuery 
			FROM `players` AS `p` 
			INNER JOIN `znote_accounts` AS `za` 
				ON `p`.`account_id` = `za`.`account_id` 
			LEFT JOIN `guild_membership` AS `gm` 
				ON `p`.`id` = `gm`.`player_id` 
			LEFT JOIN `guilds` AS `g` 
				ON `gm`.`guild_id` = `g`.`id`
			WHERE `p`.`lastlogout` >= $time - ({$history['days']} * 24 * 60 * 60)
			ORDER BY `p`.`lastlogout` DESC;
		") : mysql_select_multi("
			SELECT 
				`p`.`name` AS `name`, 
				`p`.`level` AS `level`, 
				`p`.`vocation` AS `vocation`, 
				`p`.`lastlogout`,
				`g`.`name` AS `gname` 
				$outfitQuery 
			FROM `players` AS `p` 
			LEFT JOIN `guild_membership` AS `gm` 
				ON `p`.`id` = `gm`.`player_id` 
			LEFT JOIN `guilds` AS `g` 
				ON `gm`.`guild_id` = `g`.`id`
			WHERE `p`.`lastlogout` >= $time - ({$history['days']} * 24 * 60 * 60)
			ORDER BY `p`.`lastlogout` DESC;
		");
		// End loading data from SQL
		$cache->setContent($recents);
		$cache->save();
	} else {
		$recents = $cache->load();
	}
}
// End cache

?>
<div class="">
	<div class="PageHeader-module__HZea0q__header ">
		<div class="PageHeader-module__HZea0q__headerContent">
			<h2 class="PageHeader-module__HZea0q__title">
				<span class="flex items-center gap-2">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users w-4 h-4" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
					Who Is Online
				</span>
			</h2>
		</div>
	</div>
	<div class="PageBody-module__OAJIWq__body ">
		<div class="PageBody-module__OAJIWq__content">
			<div class="space-y-2 text-xs text-[var(--text-muted)]">
				<span>Live player list and recent logout activity.</span>
			</div>
		</div>
	</div>
</div>
<br>
<?php

// Players currently logged in
?>
<div class="">
	<div class="PageHeader-module__HZea0q__header ">
		<div class="PageHeader-module__HZea0q__headerContent">
			<h2 class="PageHeader-module__HZea0q__title">
				<span class="flex items-center gap-2">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-activity w-4 h-4" aria-hidden="true"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
					Currently Online
				</span>
			</h2>
		</div>
	</div>
	<div class="PageBody-module__OAJIWq__body ">
		<div class="PageBody-module__OAJIWq__content">
			<?php if (!empty($array) && $array !== false): ?>
				<div class="overflow-x-auto">
					<table id="onlinelistTable" class="w-full text-xs">
						<tr class="border-b border-[var(--box-inner-border)] text-left">
							<?php if ($loadOutfits): ?><th class="py-2 pr-3 font-semibold">Outfit</th><?php endif; ?>
							<th class="py-2 pr-3 font-semibold">Name</th>
							<th class="py-2 pr-3 font-semibold">Guild</th>
							<th class="py-2 pr-3 font-semibold">Level</th>
							<th class="py-2 pr-3 font-semibold">Vocation</th>
						</tr>
						<?php foreach ($array as $value): ?>
							<?php
							$flag = ($loadFlags === true && isset($value['flag']) && strlen($value['flag']) > 1)
								? '<img class="inline-block w-4 h-3 mr-1 align-middle" src="' . htmlspecialchars($config['country_flags']['server'], ENT_QUOTES, 'UTF-8') . '/' . htmlspecialchars($value['flag'], ENT_QUOTES, 'UTF-8') . '.png" alt="flag">'
								: '';
							$guildname = (!empty($value['gname']))
								? '<a href="guilds.php?name=' . urlencode($value['gname']) . '" class="text-[var(--text-link)] hover:text-[var(--text-link-hover)]">' . htmlspecialchars($value['gname'], ENT_QUOTES, 'UTF-8') . '</a>'
								: '-';
							?>
							<tr class="border-b border-[var(--box-inner-border)] last:border-b-0">
								<?php if ($loadOutfits): ?>
									<td class="py-2 pr-3">
										<img src="<?php echo htmlspecialchars($config['show_outfits']['imageServer'], ENT_QUOTES, 'UTF-8'); ?>?id=<?php echo (int)$value['type']; ?>&addons=<?php echo isset($value['addons']) ? (int)$value['addons'] : 0; ?>&head=<?php echo (int)$value['head']; ?>&body=<?php echo (int)$value['body']; ?>&legs=<?php echo (int)$value['legs']; ?>&feet=<?php echo (int)$value['feet']; ?>" alt="outfit">
									</td>
								<?php endif; ?>
								<td class="py-2 pr-3">
									<?php echo $flag; ?><a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)]" href="characterprofile.php?name=<?php echo urlencode($value['name']); ?>"><?php echo htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8'); ?></a>
								</td>
								<td class="py-2 pr-3"><?php echo $guildname; ?></td>
								<td class="py-2 pr-3"><?php echo (int)$value['level']; ?></td>
								<td class="py-2 pr-3"><?php echo htmlspecialchars(vocation_id_to_name($value['vocation']), ENT_QUOTES, 'UTF-8'); ?></td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
			<?php else: ?>
				<div class="text-xs text-[var(--text-muted)]">Nobody is online.</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php

// Players online logout history
if ($history["enabled"]) {
	$time = time();
	?>
	<br>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-history w-4 h-4" aria-hidden="true"><path d="M3 3v5h5"></path><path d="M3.05 13a9 9 0 1 0 .5-4.5L3 8"></path><path d="M12 7v5l4 2"></path></svg>
						Online Past <?php echo (int)$history['days']; ?> Days
					</span>
				</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<?php if (!empty($recents) && $recents !== false): ?>
					<div class="overflow-x-auto">
						<table id="recentlistTable" class="w-full text-xs">
							<tr class="border-b border-[var(--box-inner-border)] text-left">
								<?php if ($loadOutfits): ?><th class="py-2 pr-3 font-semibold">Outfit</th><?php endif; ?>
								<th class="py-2 pr-3 font-semibold">Name</th>
								<th class="py-2 pr-3 font-semibold">Guild</th>
								<th class="py-2 pr-3 font-semibold">Level</th>
								<th class="py-2 pr-3 font-semibold">Logout [days] - date</th>
							</tr>
							<?php foreach ($recents as $value): ?>
								<?php
								$days = floor(($time - $value['lastlogout']) / 86400);
								$flag = ($loadFlags === true && isset($value['flag']) && strlen($value['flag']) > 1)
									? '<img class="inline-block w-4 h-3 mr-1 align-middle" src="' . htmlspecialchars($config['country_flags']['server'], ENT_QUOTES, 'UTF-8') . '/' . htmlspecialchars($value['flag'], ENT_QUOTES, 'UTF-8') . '.png" alt="flag">'
									: '';
								$guildname = (!empty($value['gname']))
									? '<a href="guilds.php?name=' . urlencode($value['gname']) . '" class="text-[var(--text-link)] hover:text-[var(--text-link-hover)]">' . htmlspecialchars($value['gname'], ENT_QUOTES, 'UTF-8') . '</a>'
									: '-';
								?>
								<tr class="border-b border-[var(--box-inner-border)] last:border-b-0">
									<?php if ($loadOutfits): ?>
										<td class="py-2 pr-3">
											<img src="<?php echo htmlspecialchars($config['show_outfits']['imageServer'], ENT_QUOTES, 'UTF-8'); ?>?id=<?php echo (int)$value['type']; ?>&addons=<?php echo isset($value['addons']) ? (int)$value['addons'] : 0; ?>&head=<?php echo (int)$value['head']; ?>&body=<?php echo (int)$value['body']; ?>&legs=<?php echo (int)$value['legs']; ?>&feet=<?php echo (int)$value['feet']; ?>" alt="outfit">
										</td>
									<?php endif; ?>
									<td class="py-2 pr-3">
										<?php echo $flag; ?><a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)]" href="characterprofile.php?name=<?php echo urlencode($value['name']); ?>"><?php echo htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8'); ?></a>
									</td>
									<td class="py-2 pr-3"><?php echo $guildname; ?></td>
									<td class="py-2 pr-3"><?php echo (int)$value['level']; ?></td>
									<td class="py-2 pr-3"><?php echo (int)$days; ?>D: <?php echo htmlspecialchars(getClock($value['lastlogout'], true), ENT_QUOTES, 'UTF-8'); ?></td>
								</tr>
							<?php endforeach; ?>
						</table>
					</div>
				<?php else: ?>
					<div class="text-xs text-[var(--text-muted)]">Nobody has logged in past <?php echo (int)$history['days']; ?> days.</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}

include 'layout/overall/footer.php'; ?>
