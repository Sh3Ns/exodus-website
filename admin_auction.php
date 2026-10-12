<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
protect_page();
admin_only($user_data);
$auction = $config['shop_auction'];
$step = $auction['step'];
$step_duration = $auction['step_duration'];
$loadOutfits = ($config['show_outfits']['highscores']) ? true : false;
function toDuration($is) {
	$duration['day'] = $is / (24 * 60 * 60);
	if (($duration['day'] - (int)$duration['day']) > 0)
		$duration['hour'] = ($duration['day'] - (int)$duration['day']) * 24;
	if (isset($duration['hour'])) {
		if (($duration['hour'] - (int)$duration['hour']) > 0)
			$duration['minute'] = ($duration['hour'] - (int)$duration['hour']) * 60;
		if (isset($duration['minute'])) {
			if (($duration['minute'] - (int)$duration['minute']) > 0)
				$duration['second'] = ($duration['minute'] - (int)$duration['minute']) * 60;
		}
	}
	$tmp = array();
	foreach ($duration as $type => $value) {
		if ($value >= 1) {
			$pluralType = ((int)$value === 1) ? $type : $type . 's';
			if ($type !== 'second') $tmp[] = (int)$value . " $pluralType";
			else $tmp[] = (int)$value . " $pluralType";
		}
	}
	return implode(', ', $tmp);
}
// start

// Passive check to see if bid period has expired and someone won a deal
$time = time();
$expired_auctions = mysql_select_multi("
	SELECT `id`
	FROM `znote_auction_player`
	WHERE `sold` = 0
	AND `time_end` < {$time}
	AND `bidder_account_id` > 0
");
//data_dump($expired_auctions, $this_account_id, "expired_auctions");
if ($expired_auctions !== false) {
	$soldIds = array();
	foreach ($expired_auctions as $a) {
		$soldIds[] = $a['id'];
	}
	if (!empty($soldIds)) {
		mysql_update("
			UPDATE `znote_auction_player`
			SET `sold`=1
			WHERE `id` IN(".implode(',', $soldIds).")
			LIMIT ".COUNT($soldIds).";
		");
	}
}
// end passive check
// Pending auctions
$pending = mysql_select_multi("
	SELECT
		`za`.`id` AS `zaid`,
		`za`.`price`,
		`za`.`bid`,
		`za`.`time_begin`,
		`za`.`time_end`,
		`p`.`id` AS `player_id`,
		`p`.`name`,
		`p`.`vocation`,
		`p`.`level`,
		`p`.`lookbody` AS `body`,
		`p`.`lookfeet` AS `feet`,
		`p`.`lookhead` AS `head`,
		`p`.`looklegs` AS `legs`,
		`p`.`looktype` AS `type`,
		`p`.`lookaddons` AS `addons`
	FROM `znote_auction_player` za
	INNER JOIN `players` p
		ON `za`.`player_id` = `p`.`id`
	WHERE `p`.`account_id` = {$auction['storage_account_id']}
	AND `za`.`claimed` = 0
	AND `za`.`sold` = 1
	ORDER BY `za`.`time_end` desc
");
// ongoing auctions
$ongoing = mysql_select_multi("
	SELECT
		`za`.`id` AS `zaid`,
		`za`.`price`,
		`za`.`bid`,
		`za`.`time_begin`,
		`za`.`time_end`,
		`p`.`vocation`,
		`p`.`level`,
		`p`.`lookbody` AS `body`,
		`p`.`lookfeet` AS `feet`,
		`p`.`lookhead` AS `head`,
		`p`.`looklegs` AS `legs`,
		`p`.`looktype` AS `type`,
		`p`.`lookaddons` AS `addons`
	FROM `znote_auction_player` za
	INNER JOIN `players` p
		ON `za`.`player_id` = `p`.`id`
	WHERE `p`.`account_id` = {$auction['storage_account_id']}
	AND `za`.`sold` = 0
	ORDER BY `za`.`time_end` desc;
");
// Completed auctions
$completed = mysql_select_multi("
	SELECT
		`za`.`id` AS `zaid`,
		`za`.`price`,
		`za`.`bid`,
		`za`.`time_begin`,
		`za`.`time_end`,
		`p`.`id` AS `player_id`,
		`p`.`name`,
		`p`.`vocation`,
		`p`.`level`,
		`p`.`lookbody` AS `body`,
		`p`.`lookfeet` AS `feet`,
		`p`.`lookhead` AS `head`,
		`p`.`looklegs` AS `legs`,
		`p`.`looktype` AS `type`,
		`p`.`lookaddons` AS `addons`
	FROM `znote_auction_player` za
	INNER JOIN `players` p
		ON `za`.`player_id` = `p`.`id`
	WHERE `za`.`claimed` = 1
	ORDER BY `za`.`time_end` desc
");
?>
<div class="">
	<div class="PageHeader-module__HZea0q__header ">
		<div class="PageHeader-module__HZea0q__headerContent">
			<h2 class="PageHeader-module__HZea0q__title">
				<span class="flex items-center gap-2">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-gavel w-4 h-4" aria-hidden="true">
						<path d="m14 13-7.5 7.5c-.83.83-2.17.83-3 0s-.83-2.17 0-3L11 10"></path><path d="m16 16 6-6"></path><path d="m8 8 6-6"></path><path d="m9 7 8 8"></path><path d="m21 11-8-8"></path>
					</svg>Character Auction History
				</span>
			</h2>
		</div>
	</div>
	<div class="PageBody-module__OAJIWq__body ">
		<div class="PageBody-module__OAJIWq__content">
			<div class="space-y-3">
				<p class="text-xs text-[var(--text-muted)]">
					<strong>Let players sell, buy and bid on characters.</strong><br>
					Creates a deeper shop economy and gives admins a quick overview of pending, active, and completed auctions.
				</p>
				<div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs text-[var(--text-muted)]">
					<div class="p-2 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)]">Step: <b><?php echo hhb_tohtml($step); ?></b></div>
					<div class="p-2 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)]">Step duration: <b><?php echo hhb_tohtml(toDuration($step_duration)); ?></b></div>
					<div class="p-2 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)]">Storage account: <b><?php echo hhb_tohtml($auction['storage_account_id']); ?></b></div>
				</div>
			</div>
		</div>
	</div>
</div>
<br>

<?php
function render_auction_section($title, $rows, $type) {
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title"><span class="flex items-center gap-2"><?php echo hhb_tohtml($title); ?></span></h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="space-y-2">
					<?php if (is_array($rows) && !empty($rows)): ?>
						<?php foreach($rows as $character): ?>
							<div class="grid grid-cols-1 sm:grid-cols-[1fr_80px_120px_90px_90px] gap-2 items-center py-2 px-3 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-xs">
								<div class="min-w-0">
									<?php if (isset($character['name'])): ?>
										<a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)] font-semibold" href="/characterprofile.php?name=<?php echo urlencode($character['name']); ?>"><?php echo hhb_tohtml($character['name']); ?></a>
									<?php else: ?>
										<a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)] font-semibold" href="/auctionChar.php?action=view&zaid=<?php echo (int)$character['zaid']; ?>">View auction #<?php echo (int)$character['zaid']; ?></a>
									<?php endif; ?>
									<div class="text-[10px] text-[var(--text-muted)]">
										Added: <?php echo hhb_tohtml(getClock($character['time_begin'], true)); ?> |
										Ended: <?php echo hhb_tohtml(getClock($character['time_end'], true)); ?>
									</div>
								</div>
								<div>Level <b><?php echo (int)$character['level']; ?></b></div>
								<div><?php echo hhb_tohtml(vocation_id_to_name($character['vocation'])); ?></div>
								<div>Price <b><?php echo (int)$character['price']; ?></b></div>
								<div>Bid <b><?php echo (int)$character['bid']; ?></b></div>
							</div>
							<?php if ($type === 'ongoing'): ?>
								<?php $ended = (time() > $character['time_end']) ? true : false; ?>
								<div class="text-[10px] text-[var(--text-muted)] px-3">
									Type: <?php echo ($ended) ? 'Instant' : 'Bidding (' . hhb_tohtml(toDuration(($character['time_end'] - time()))) . ')'; ?>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php else: ?>
						<div class="text-xs text-[var(--text-muted)] text-center py-4">No auctions found.</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<br>
	<?php
}

render_auction_section('Pending orders to be claimed', $pending, 'pending');
render_auction_section('Ongoing auctions', $ongoing, 'ongoing');
render_auction_section('Completed auctions', $completed, 'completed');

include 'layout/overall/footer.php';
?>
