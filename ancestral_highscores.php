<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';

// Fetch top 20 players with their ancestral rank points
$players_query = "
	SELECT `name`, `level`, `experience`, `looktype`, `lookaddons`, `lookhead`, `lookbody`, `looklegs`, `lookfeet`, `ancestral_rank_points`
	FROM `players`
	WHERE `group_id` < {$config['highscore']['ignoreGroupId']}
	ORDER BY `ancestral_rank_points` DESC
	LIMIT 20
";
$players_result = mysql_select_multi($players_query);

function getRankingTitle($points) {
	if ($points >= 10000) {
		return 'Awakening';
	} elseif ($points >= 6321) {
		return 'Chaos';
	} elseif ($points >= 4321) {
		return 'Ascending';
	} elseif ($points >= 2821) {
		return 'Abyssal';
	} elseif ($points >= 1821) {
		return 'Mystic';
	} elseif ($points >= 1221) {
		return 'Ancestral';
	} elseif ($points >= 771) {
		return 'Diamond';
	} elseif ($points >= 471) {
		return 'Platinum';
	} elseif ($points >= 251) {
		return 'Gold';
	} elseif ($points >= 101) {
		return 'Silver';
	} elseif ($points >= 41) {
		return 'Bronze';
	}
	return 'Rookie';
}
?>

<div class="">
	<div class="PageHeader-module__HZea0q__header ">
		<div class="PageHeader-module__HZea0q__headerContent">
			<h2 class="PageHeader-module__HZea0q__title">
				<span class="flex items-center gap-2">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>
					Ancestral Rankings
				</span>
			</h2>
		</div>
	</div>
	<div class="PageBody-module__OAJIWq__body ">
		<div class="PageBody-module__OAJIWq__content">
			<div class="space-y-3">
				<div class="text-xs text-[var(--text-muted)]">
					Top 20 players ordered by ancestral rank points.
				</div>

				<?php if ($players_result): ?>
					<div class="overflow-x-auto">
						<table class="w-full text-xs">
							<thead>
								<tr class="border-b border-[var(--box-inner-border)] text-left">
									<th class="py-2 pr-3 font-semibold">Position</th>
									<th class="py-2 pr-3 font-semibold">Name</th>
									<th class="py-2 pr-3 font-semibold">Outfit</th>
									<th class="py-2 pr-3 font-semibold">Ancestral Rank Points</th>
									<th class="py-2 pr-3 font-semibold">Ranking</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($players_result as $key => $player): ?>
									<tr class="border-b border-[var(--box-inner-border)] last:border-b-0">
										<td class="py-2 pr-3">
											<?php
											$rank = (int)($key + 1);
											$rankClass = '';
											if ($rank === 1) $rankClass = 'text-[#ffd700] font-bold rank-gold';
											elseif ($rank === 2) $rankClass = 'text-[#c0c0c0] font-bold rank-silver';
											elseif ($rank === 3) $rankClass = 'text-[#cd7f32] font-bold rank-bronze';
											?>
											<span class="<?php echo $rankClass; ?>"><?php echo $rank; ?>.</span>
										</td>
										<td class="py-2 pr-3">
											<a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)]" href="characterprofile.php?name=<?php echo urlencode($player['name']); ?>">
												<?php echo htmlspecialchars($player['name'], ENT_QUOTES, 'UTF-8'); ?>
											</a>
										</td>
										<td class="py-2 pr-3">
											<img src="<?php echo htmlspecialchars($config['show_outfits']['imageServer'], ENT_QUOTES, 'UTF-8'); ?>?id=<?php echo (int)$player['looktype']; ?>&addons=<?php echo (int)$player['lookaddons']; ?>&head=<?php echo (int)$player['lookhead']; ?>&body=<?php echo (int)$player['lookbody']; ?>&legs=<?php echo (int)$player['looklegs']; ?>&feet=<?php echo (int)$player['lookfeet']; ?>" alt="outfit">
										</td>
										<td class="py-2 pr-3"><?php echo (int)$player['ancestral_rank_points']; ?></td>
										<td class="py-2 pr-3"><b><?php echo htmlspecialchars(getRankingTitle((int)$player['ancestral_rank_points']), ENT_QUOTES, 'UTF-8'); ?></b></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div class="text-xs text-[var(--text-muted)]">No rankings available yet.</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include 'layout/overall/footer.php'; ?>
