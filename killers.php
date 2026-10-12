<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>

<div class="">
	<div class="PageHeader-module__HZea0q__header ">
		<div class="PageHeader-module__HZea0q__headerContent">
			<h2 class="PageHeader-module__HZea0q__title">
				<span class="flex items-center gap-2">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-swords w-4 h-4" aria-hidden="true"><polyline points="14.5 17.5 3 6V3h3l11.5 11.5"></polyline><line x1="13" y1="19" x2="19" y2="13"></line><line x1="16" y1="16" x2="20" y2="20"></line><line x1="19" y1="21" x2="21" y2="19"></line><polyline points="14.5 6.5 18 3h3v3l-3.5 3.5"></polyline><line x1="5" y1="14" x2="9" y2="18"></line><line x1="7" y1="17" x2="4" y2="20"></line><line x1="3" y1="21" x2="5" y2="19"></line></svg>
					Killers
				</span>
			</h2>
		</div>
	</div>
	<div class="PageBody-module__OAJIWq__body ">
		<div class="PageBody-module__OAJIWq__content">
			<div class="text-xs text-[var(--text-muted)]">
				Top killers, top victims and the most recent PvP kills.
			</div>
		</div>
	</div>
</div>
<br>

<?php
if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10' || $config['ServerEngine'] == 'OTHIRE') {
	$cache = new Cache('engine/cache/killers');
	if ($cache->hasExpired()) {
		$killers = fetchMurders();
		$cache->setContent($killers);
		$cache->save();
	} else {
		$killers = $cache->load();
	}

	$cache = new Cache('engine/cache/victims');
	if ($cache->hasExpired()) {
		$victims = fetchLoosers();
		$cache->setContent($victims);
		$cache->save();
	} else {
		$victims = $cache->load();
	}

	$cache = new Cache('engine/cache/lastkillers');
	if ($cache->hasExpired()) {
		$latests = mysql_select_multi("SELECT `p`.`name` AS `victim`, `d`.`killed_by` as `killed_by`, `d`.`time` as `time` FROM `player_deaths` as `d` INNER JOIN `players` as `p` ON d.player_id = p.id WHERE d.`is_player`='1' ORDER BY `time` DESC LIMIT 20;");
		if ($latests !== false) {
			$cache->setContent($latests);
			$cache->save();
		}
	} else {
		$latests = $cache->load();
	}
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">Biggest Murders</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<?php if ($killers): ?>
					<div class="overflow-x-auto">
						<table id="killersTable" class="w-full text-xs">
							<thead>
								<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
									<th class="py-1.5 px-2 text-left">Name</th>
									<th class="py-1.5 px-2 text-center">Kills</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($killers as $index => $killer): ?>
									<tr class="border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)] <?php echo ($index % 2 === 0 ? 'bg-[var(--box-inner-bg)]/30' : ''); ?>">
										<td class="py-1 px-2">
											<a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)] hover:underline" href="characterprofile.php?name=<?php echo urlencode($killer['killed_by']); ?>">
												<?php echo htmlspecialchars($killer['killed_by'], ENT_QUOTES, 'UTF-8'); ?>
											</a>
										</td>
										<td class="py-1 px-2 text-center text-[var(--color-danger)]">
											<?php echo (int)$killer['kills']; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div class="text-xs text-[var(--text-muted)]">No player kills exist.</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<br>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">Biggest Victims</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<?php if ($victims): ?>
					<div class="overflow-x-auto">
						<table id="victimsTable" class="w-full text-xs">
							<thead>
								<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
									<th class="py-1.5 px-2 text-left">Name</th>
									<th class="py-1.5 px-2 text-center">Deaths</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($victims as $index => $victim): ?>
									<tr class="border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)] <?php echo ($index % 2 === 0 ? 'bg-[var(--box-inner-bg)]/30' : ''); ?>">
										<td class="py-1 px-2">
											<a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)] hover:underline" href="characterprofile.php?name=<?php echo urlencode($victim['name']); ?>">
												<?php echo htmlspecialchars($victim['name'], ENT_QUOTES, 'UTF-8'); ?>
											</a>
										</td>
										<td class="py-1 px-2 text-center text-[var(--color-danger)]">
											<?php echo (int)$victim['Deaths']; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div class="text-xs text-[var(--text-muted)]">No player kills exist.</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<br>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">Latest Kills</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<?php if ($latests): ?>
					<div class="overflow-x-auto">
						<table id="latestKillsTable" class="w-full text-xs">
							<thead>
								<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
									<th class="py-1.5 px-2 text-left">Killer</th>
									<th class="py-1.5 px-2 text-center">Time</th>
									<th class="py-1.5 px-2 text-left">Victim</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($latests as $index => $last): ?>
									<tr class="border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)] <?php echo ($index % 2 === 0 ? 'bg-[var(--box-inner-bg)]/30' : ''); ?>">
										<td class="py-1 px-2">
											<a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)] hover:underline" href="characterprofile.php?name=<?php echo urlencode($last['killed_by']); ?>">
												<?php echo htmlspecialchars($last['killed_by'], ENT_QUOTES, 'UTF-8'); ?>
											</a>
										</td>
										<td class="py-1 px-2 text-center whitespace-nowrap"><?php echo htmlspecialchars(getClock($last['time'], true), ENT_QUOTES, 'UTF-8'); ?></td>
										<td class="py-1 px-2">
											<a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)] hover:underline" href="characterprofile.php?name=<?php echo urlencode($last['victim']); ?>">
												<?php echo htmlspecialchars($last['victim'], ENT_QUOTES, 'UTF-8'); ?>
											</a>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div class="text-xs text-[var(--text-muted)]">No player kills exist.</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
} else if ($config['ServerEngine'] == 'TFS_03') {
	$cache = new Cache('engine/cache/killers');
	if ($cache->hasExpired()) {
		$deaths = fetchLatestDeaths_03(30, true);
		$cache->setContent($deaths);
		$cache->save();
	} else {
		$deaths = $cache->load();
	}
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">Latest Killers</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<?php if ($deaths && !empty($deaths)): ?>
					<div class="overflow-x-auto">
						<table id="deathsTable" class="w-full text-xs">
							<thead>
								<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
									<th class="py-1.5 px-2 text-left">Killer</th>
									<th class="py-1.5 px-2 text-center">Time</th>
									<th class="py-1.5 px-2 text-left">Victim</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($deaths as $index => $death): ?>
									<tr class="border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)] <?php echo ($index % 2 === 0 ? 'bg-[var(--box-inner-bg)]/30' : ''); ?>">
										<td class="py-1 px-2">
											<a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)] hover:underline" href="characterprofile.php?name=<?php echo urlencode($death['killed_by']); ?>">
												<?php echo htmlspecialchars($death['killed_by'], ENT_QUOTES, 'UTF-8'); ?>
											</a>
										</td>
										<td class="py-1 px-2 text-center whitespace-nowrap"><?php echo htmlspecialchars(getClock($death['time'], true), ENT_QUOTES, 'UTF-8'); ?></td>
										<td class="py-1 px-2">
											At level <?php echo (int)$death['level']; ?>:
											<a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)] hover:underline" href="characterprofile.php?name=<?php echo urlencode($death['victim']); ?>">
												<?php echo htmlspecialchars($death['victim'], ENT_QUOTES, 'UTF-8'); ?>
											</a>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div class="text-xs text-[var(--text-muted)]">No player deaths exist.</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}
include 'layout/overall/footer.php'; ?>
