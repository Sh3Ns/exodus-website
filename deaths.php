<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
$cache = new Cache('engine/cache/deaths');
if ($cache->hasExpired()) {
	if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10') {
		$deaths = fetchLatestDeaths();
	} else if ($config['ServerEngine'] == 'TFS_03' || $config['ServerEngine'] == 'OTHIRE') {
		$deaths = fetchLatestDeaths_03(30);
	}
	$cache->setContent($deaths);
	$cache->save();
} else {
	$deaths = $cache->load();
}
?>

<div class="">
	<div class="PageHeader-module__HZea0q__header ">
		<div class="PageHeader-module__HZea0q__headerContent">
			<h2 class="PageHeader-module__HZea0q__title">
				<span class="flex items-center gap-2">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-skull w-4 h-4" aria-hidden="true"><path d="m12.5 17-.5-1-.5 1h1Z"></path><path d="M15 12h.01"></path><path d="M9 12h.01"></path><path d="M10 16a2 2 0 0 0 4 0"></path><path d="M19 8a7 7 0 0 0-14 0v2a7 7 0 0 0 14 0Z"></path><path d="M8 20v2"></path><path d="M16 20v2"></path><path d="M12 20v2"></path><path d="M7 16h10"></path></svg>
					Latest Deaths
				</span>
			</h2>
		</div>
	</div>
	<div class="PageBody-module__OAJIWq__body ">
		<div class="PageBody-module__OAJIWq__content">
			<div class="space-y-3">
				<div class="text-xs text-[var(--text-muted)]">
					Recent player deaths with level, time, and killer details.
				</div>

				<?php if ($deaths): ?>
					<div class="overflow-x-auto">
						<table id="deathsTable" class="w-full text-xs">
							<thead>
								<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
									<th class="py-1.5 px-2 text-left">Victim</th>
									<th class="py-1.5 px-2 text-center">Time</th>
									<th class="py-1.5 px-2 text-left">Killer</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($deaths as $index => $death): ?>
									<tr class="border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)] <?php echo ($index % 2 === 0 ? 'bg-[var(--box-inner-bg)]/30' : ''); ?>">
										<td class="py-1 px-2">
											At level <?php echo (int)$death['level']; ?>:
											<a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)] hover:underline" href="characterprofile.php?name=<?php echo urlencode($death['victim']); ?>">
												<?php echo htmlspecialchars($death['victim'], ENT_QUOTES, 'UTF-8'); ?>
											</a>
										</td>
										<td class="py-1 px-2 text-center whitespace-nowrap">
											<?php echo htmlspecialchars(getClock($death['time'], true), ENT_QUOTES, 'UTF-8'); ?>
										</td>
										<td class="py-1 px-2">
											<?php if ((int)$death['is_player'] === 1): ?>
												Player:
												<a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)] hover:underline" href="characterprofile.php?name=<?php echo urlencode($death['killed_by']); ?>">
													<?php echo htmlspecialchars($death['killed_by'], ENT_QUOTES, 'UTF-8'); ?>
												</a>
											<?php elseif ((int)$death['is_player'] === 0): ?>
												<?php
												$killerName = $death['killed_by'];
												if ($config['ServerEngine'] == 'TFS_03') {
													$killerName = str_replace("a ", "", $killerName);
												}
												?>
												Monster: <?php echo htmlspecialchars(ucfirst($killerName), ENT_QUOTES, 'UTF-8'); ?>
											<?php else: ?>
												<?php echo htmlspecialchars($death['killed_by'], ENT_QUOTES, 'UTF-8'); ?>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div class="text-xs text-[var(--text-muted)]">No deaths exist.</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<br>

<?php include 'layout/overall/footer.php'; ?>
