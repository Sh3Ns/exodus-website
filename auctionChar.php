<?php require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php';
// Convert a seconds integer value into days, hours, minutes and seconds string.
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

function auction_button_class($extra = '') {
	return trim('inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer ' . $extra);
}

function auction_input_class($extra = '') {
	return trim('w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] disabled:opacity-70 disabled:cursor-not-allowed ' . $extra);
}

function auction_table_class($extra = '') {
	return trim('w-full text-xs ' . $extra);
}

function auction_link_class($extra = '') {
	return trim('text-[var(--text-link)] hover:text-[var(--text-link-hover)] hover:underline ' . $extra);
}

function auction_page_start($title = 'Character auction') {
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="space-y-3">
	<?php
}

function auction_page_end() {
	?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function auction_notice($message, $type = 'info') {
	$textClass = ($type === 'error') ? 'text-red-600' : (($type === 'success') ? 'text-[#22c55e]' : 'text-[var(--text-muted)]');
	?>
	<div class="text-xs <?php echo $textClass; ?>"><?php echo $message; ?></div>
	<?php
}
?>
<?php
// Import from config:
$auction = $config['shop_auction'];
$loadOutfits = ($config['show_outfits']['highscores']) ? true : false;
$this_account_id = (int)$session_user_id;
$is_admin = is_admin($user_data);
auction_page_start('Character auction');

// If character auction is enabled in config.php
if ($auction['characterAuction']) {

	if ($config['ServerEngine'] != 'TFS_10') {
		auction_notice('Character shop auction system is currently only available for ServerEngine TFS_10.', 'error');
		auction_page_end();
		include 'layout/overall/footer.php';
		die();
	}
	if ((int)$auction['storage_account_id'] === (int)$this_account_id) {
		auction_notice('The storage account cannot use the character auction.', 'error');
		auction_page_end();
		include 'layout/overall/footer.php';
		die();
	}
	$step = $auction['step'];
	$step_duration = $auction['step_duration'];
	$actions = array(
		'list',		// list all available players in auction
		'view',		// view a specific player
		'create',	// select which character to add and initial price
		'add',		// add character to list
		'bid',		// Bid or buy a specific player
		'refund',	// Refund a player you added back to your account
		'claim'		// Claim a character you won through purchase or bid
	);

	// Default action is list, but $_GET or $_POST will override it.
	$action = 'list';
	// Load selected string from actions array based on input, strict whitelist validation
	if (isset( $_GET['action']) && in_array( $_GET['action'], $actions)) {
		$action = $actions[array_search( $_GET['action'], $actions, true)];
	}
	if (isset($_POST['action']) && in_array($_POST['action'], $actions)) {
		$action = $actions[array_search($_POST['action'], $actions, true)];
	}

	// Passive check to see if bid period has expired and someone won a deal
	$time = time();
	$expired_auctions = mysql_select_multi("
		SELECT
			`id`,
			`original_account_id`,
			(`bid`+`deposit`) as `points`
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
				SET `sold` = 1
				WHERE `id` IN(".implode(',', $soldIds).")
				LIMIT ".COUNT($soldIds).";
			");
			// Transfer points to seller account
			foreach ($expired_auctions as $a) {
				mysql_update("
					UPDATE `znote_accounts`
					SET `points` = (`points`+{$a['points']})
					WHERE `account_id` = {$a['original_account_id']};
				");
			}
		}
	}
	// end passive check

	// If we bid or buy a character
	// silently continues to list if buy, back to view if bid
	if ($action === 'bid') {
		//data_dump($_POST, false, "Bid or buying:");
		$zaid = (isset($_POST['zaid']) && (int)$_POST['zaid'] > 0) ? (int)$_POST['zaid'] : false;
		$price = (isset($_POST['price']) && (int)$_POST['price'] > 0) ? (int)$_POST['price'] : false;

		$action = 'list';
		if ($zaid !== false && $price !== false) {
			// The account of the buyer, if he can afford what he is trying to pay
			$account = mysql_select_single("
				SELECT
					`a`.`id`,
					`za`.`points`
				FROM `accounts` a
				INNER JOIN `znote_accounts` za
					ON `a`.`id` = `za`.`account_id`
				WHERE `a`.`id`= {$this_account_id}
				AND `za`.`points` >= {$price}
				LIMIT 1;
			");
			//data_dump($account, false, "Buyer account:");

			// The character to buy, presuming it isn't sold, buyer isn't the owner, buyer can afford it
			if ($account !== false) {
				$character = mysql_select_single("
					SELECT
						`za`.`id` AS `zaid`,
						`za`.`player_id`,
						`za`.`original_account_id`,
						`za`.`bidder_account_id`,
						`za`.`time_begin`,
						`za`.`time_end`,
						`za`.`price`,
						`za`.`bid`,
						`za`.`deposit`,
						`za`.`sold`
					FROM `znote_auction_player` za
					WHERE `za`.`id` = {$zaid}
					AND `za`.`sold` = 0
					AND `za`.`original_account_id` != {$this_account_id}
					AND `za`.`price` <= {$price}
					AND `za`.`bid`+{$step} <= {$price}
					LIMIT 1
				");
				//data_dump($character, false, "Character to buy:");

				if ($character !== false) {
					// If auction already have a previous bidder, refund him his points
					if ($character['bid'] > 0 && $character['bidder_account_id'] > 0) {
						mysql_update("
							UPDATE `znote_accounts`
							SET `points` = `points`+{$character['bid']}
							WHERE `account_id` = {$character['bidder_account_id']}
							LIMIT 1;
						");
						// If previous bidder is not you, increase bidding period by 1 hour
						// (Extending bid war to give bidding competitor a chance to retaliate)
						if ((int)$character['bidder_account_id'] !== (int)$account['id']) {
							mysql_update("
								UPDATE `znote_auction_player`
								SET `time_end` = `time_end`+{$step_duration}
								WHERE `id` = {$character['zaid']}
								LIMIT 1;
							");
						}
					}
					// Remove points from buyer
					mysql_update("
						UPDATE `znote_accounts`
						SET `points` = `points`-{$price}
						WHERE `account_id` = {$account['id']}
						LIMIT 1;
					");
					// Update auction, and set new bidder data
					$time = time();
					mysql_update("
						UPDATE `znote_auction_player`
						SET
							`bidder_account_id` = {$account['id']},
							`bid` = {$price},
							`sold` = CASE WHEN {$time} >= `time_end` THEN 1 ELSE 0 END
						WHERE `id` = {$character['zaid']}
						LIMIT 1;
					");
					// If character is sold, give points to seller
					if (time() >= $character['time_end']) {
						mysql_update("
							UPDATE `znote_accounts`
							SET `points` = (`points`+{$character['deposit']}+{$price})
							WHERE `account_id` = {$character['original_account_id']}
							LIMIT 1;
						");
					} else {
						// If character is not sold, this is a bidding war, we want to send user back to view.
						$action = 'view';
					}
					// Note: Transferring character to the new account etc happens later in $action = 'claim'
				}
			}
		}
	}

	// See a specific character in auction,
	// silently fallback to list if he doesn't exist or is already sold
	if ($action === 'view') { // View a character in the auction
		if (!isset($zaid)) {
			$zaid = (isset($_GET['zaid']) && (int)$_GET['zaid'] > 0) ? (int)$_GET['zaid'] : false;
		}
		if ($zaid !== false) {
			// Retrieve basic character information
			$character = mysql_select_single("
				SELECT
					`za`.`id` AS `zaid`,
					`za`.`player_id`,
					`za`.`original_account_id`,
					`za`.`bidder_account_id`,
					`za`.`time_begin`,
					`za`.`time_end`,
					CASE WHEN `za`.`price` > `za`.`bid`
						THEN `za`.`price`
						ELSE `za`.`bid`+{$step}
					END AS `price`,
					CASE WHEN `za`.`original_account_id` = {$this_account_id}
						THEN 1
						ELSE 0
					END AS `own`,
					CASE WHEN `za`.`original_account_id` = {$this_account_id}
						THEN `p`.`name`
						ELSE ''
					END AS `name`,
					CASE WHEN `za`.`original_account_id` = {$this_account_id}
						THEN `za`.`bid`
						ELSE 0
					END AS `bid`,
					CASE WHEN `za`.`original_account_id` = {$this_account_id}
						THEN `za`.`deposit`
						ELSE 0
					END AS `deposit`,
					`p`.`vocation`,
					`p`.`level`,
					`p`.`balance`,
					`p`.`lookbody` AS `body`,
					`p`.`lookfeet` AS `feet`,
					`p`.`lookhead` AS `head`,
					`p`.`looklegs` AS `legs`,
					`p`.`looktype` AS `type`,
					`p`.`lookaddons` AS `addons`,
					`p`.`maglevel` AS `magic`,
					`p`.`skill_fist` AS `fist`,
					`p`.`skill_club` AS `club`,
					`p`.`skill_sword` AS `sword`,
					`p`.`skill_axe` AS `axe`,
					`p`.`skill_dist` AS `dist`,
					`p`.`skill_shielding` AS `shielding`,
					`p`.`skill_fishing` AS `fishing`
				FROM `znote_auction_player` za
				INNER JOIN `players` p
					ON `za`.`player_id` = `p`.`id`
				WHERE `za`.`id` = {$zaid}
				AND `za`.`sold` = 0
				LIMIT 1;
			");
			//data_dump($character, false, "Character info");

			if (is_array($character) && !empty($character)) {
				// If the end of the bid is in the future, the bid is currently ongoing
				$bidding_period = ((int)$character['time_end']+1 > time()) ? true : false;
				$player_items = mysql_select_multi("
					SELECT `itemtype`, SUM(`count`) AS `count`
					FROM `player_items`
					WHERE `player_id` = {$character['player_id']}
					GROUP BY `itemtype`
					ORDER BY MIN(`pid`) ASC
				");
				$depot_items = mysql_select_multi("
					SELECT `itemtype`, SUM(`count`) AS `count`
					FROM `player_depotitems`
					WHERE `player_id` = {$character['player_id']}
					AND `itemtype` NOT IN (
						25453, 25454, 25455, 25456, 25457, 25458, 25459, 25460, 25461,
						25462, 25463, 25464, 25465, 25466, 25467, 25468, 25469, 29841, 29842, 29843, 29844
					)
					GROUP BY `itemtype`
					ORDER BY MIN(`pid`) ASC
				");
				$account = mysql_select_single("
					SELECT `points`
					FROM `znote_accounts`
					WHERE `account_id` = {$this_account_id}
					AND `points` >= {$character['price']}
					LIMIT 1;
				");
				?>
				<div class="text-xs text-[var(--text-muted)]">Detailed character information. <a class="<?php echo auction_link_class(); ?>" href="/auctionChar.php?action=list">Go back to list.</a></div>
				<!-- Basic info -->
				<div class="overflow-x-auto">
				<table class="<?php echo auction_table_class('auction_char'); ?>">
					<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
						<td class="py-1.5 px-2">Level</td>
						<td class="py-1.5 px-2">Vocation</td>
						<?php if ($loadOutfits): ?>
							<td class="py-1.5 px-2">Image</td>
						<?php endif; ?>
						<td class="py-1.5 px-2">Bank</td>
						<td class="py-1.5 px-2">Price</td>
					</tr>
					<tr class="border-b border-[var(--box-inner-border)]">
						<td class="py-1 px-2"><?php echo (int)$character['level']; ?></td>
						<td class="py-1 px-2"><?php echo htmlspecialchars(vocation_id_to_name($character['vocation']), ENT_QUOTES, 'UTF-8'); ?></td>
						<?php if ($loadOutfits): ?>
							<td class="py-1 px-2 outfitColumn">
								<img class="mx-auto" src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo (int)$character['type']; ?>&addons=<?php echo (int)$character['addons']; ?>&head=<?php echo (int)$character['head']; ?>&body=<?php echo (int)$character['body']; ?>&legs=<?php echo (int)$character['legs']; ?>&feet=<?php echo (int)$character['feet']; ?>" alt="Character outfit">
							</td>
						<?php endif; ?>
						<td class="py-1 px-2"><?php echo (int)$character['balance']; ?></td>
						<td class="py-1 px-2"><?php echo (int)$character['price']; ?> points</td>
					</tr>
					<?php if ($bidding_period): ?>
						<tr class="border-b border-[var(--box-inner-border)]">
							<td class="py-1 px-2 text-[var(--text-muted)]" colspan="<?php echo ($loadOutfits) ? 5 : 4; ?>">
								<strong>Remaining bid period:</strong> <?php echo htmlspecialchars(toDuration((int)$character['time_end']-time()), ENT_QUOTES, 'UTF-8'); ?>.
							</td>
						</tr>
					<?php endif; ?>
				</table>
				</div>
				<!-- Bid on character -->
				<?php
				if ($character['own'] == 0) {
					if (is_array($account) && !empty($account)): ?>
						<div class="text-xs text-[var(--text-muted)]">You have <strong><?php echo (int)$account['points']; ?></strong> shop points remaining.</div>

						<?php if ((int)$character['bidder_account_id'] === $this_account_id): ?>
							<div class="text-xs text-[var(--text-muted)]"><strong>So far so good!</strong>
								<br>You currently have the highest bid at: <?php echo (int)$character['price']-$step; ?>
							</div>
							<div class="text-xs text-[var(--text-muted)]">If nobody bids higher than you, this character will be yours in:
								<br><?php echo toDuration((int)$character['time_end']-time()); ?>.
							</div>
						<?php endif; ?>
						<form action="/auctionChar.php" method="POST" class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2 items-end text-xs">
							<input type="hidden" name="action" value="bid">
							<input type="hidden" name="zaid" value="<?php echo $character['zaid']; ?>">
							<div>
								<label for="auction_bid_price" class="block font-medium mb-1">Price</label>
								<input id="auction_bid_price" type="number" name="price" min="<?php echo (int)$character['price']; ?>" max="<?php echo (int)$account['points']; ?>" step="5" value="<?php echo (int)$character['price']; ?>" class="<?php echo auction_input_class(); ?>" <?php if (!$bidding_period) echo 'disabled'; ?>>
							</div>
							<?php if (!$bidding_period): /* Because above input is disabled */ ?>
								<input type="hidden" name="price" value="<?php echo (int)$character['price']; ?>">
							<?php endif; ?>
							<button type="submit" class="<?php echo auction_button_class(); ?>"><?php echo ($bidding_period) ? 'Bid' : 'Buy'; ?></button>
						</form>
					<?php else: ?>
						<?php if ((int)$character['bidder_account_id'] === $this_account_id): ?>
							<div class="text-xs text-[var(--text-muted)]"><strong>So far so good!</strong>
								<br>You currently have the highest bid at: <?php echo (int)$character['price']-$step; ?>
							</div>
							<div class="text-xs text-[var(--text-muted)]">If nobody bids higher than you, this character will be yours in:
								<br><?php echo toDuration((int)$character['time_end']-time()); ?>.
							</div>
						<?php else: ?>
							<?php auction_notice('You cannot afford to buy this character.', 'error'); ?>
						<?php endif; ?>
					<?php endif;
				} else {
					?>
					<div class="text-xs text-[var(--text-muted)]"><strong>You are the seller of this character.</strong>
						<br><strong>Name:</strong> <a class="<?php echo auction_link_class(); ?>" href="/characterprofile.php?name=<?php echo urlencode($character['name']); ?>"><?php echo htmlspecialchars($character['name'], ENT_QUOTES, 'UTF-8'); ?></a>
						<br><strong>Price:</strong> <?php echo (int)$character['price']; ?>
						<br><strong>Bid:</strong> <?php echo (int)$character['bid']; ?>
						<br><strong>Deposit:</strong> <?php echo (int)$character['deposit']; ?>
						<?php if (!$bidding_period): ?>
							<div class="mt-2">The bidding period has ended, you can wait until someone decides to instantly buy it, or you can reclaim your character to your account.</div>
							<form action="/auctionChar.php" method="POST" class="mt-2">
								<input type="hidden" name="action" value="refund">
								<input type="hidden" name="zaid" value="<?php echo (int)$character['zaid']; ?>">
								<button type="submit" class="<?php echo auction_button_class(); ?>">Reclaim character back to your account</button>
							</form>
						<?php else: ?>
							<div class="mt-2">The bidding period will last for <?php echo htmlspecialchars(toDuration($character['time_end']-time()), ENT_QUOTES, 'UTF-8'); ?>. After this period, you can reclaim your character if nobody has bid on it.</div>
						<?php endif; ?>
					</div>
					<?php
				}
				?>
				<!-- SKILLS -->
				<div class="overflow-x-auto">
				<table class="<?php echo auction_table_class('auction_skills'); ?>">
					<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]"><td class="py-1.5 px-2" colspan="2">Character skills</td></tr>
					<tr class="border-b border-[var(--box-inner-border)]"><td class="py-1 px-2">Magic</td><td class="py-1 px-2"><?php echo (int)$character['magic']; ?></td></tr>
					<tr class="border-b border-[var(--box-inner-border)]"><td class="py-1 px-2">Fist</td><td class="py-1 px-2"><?php echo (int)$character['fist']; ?></td></tr>
					<tr class="border-b border-[var(--box-inner-border)]"><td class="py-1 px-2">Club</td><td class="py-1 px-2"><?php echo (int)$character['club']; ?></td></tr>
					<tr class="border-b border-[var(--box-inner-border)]"><td class="py-1 px-2">Sword</td><td class="py-1 px-2"><?php echo (int)$character['sword']; ?></td></tr>
					<tr class="border-b border-[var(--box-inner-border)]"><td class="py-1 px-2">Axe</td><td class="py-1 px-2"><?php echo (int)$character['axe']; ?></td></tr>
					<tr class="border-b border-[var(--box-inner-border)]"><td class="py-1 px-2">Distance</td><td class="py-1 px-2"><?php echo (int)$character['dist']; ?></td></tr>
					<tr class="border-b border-[var(--box-inner-border)]"><td class="py-1 px-2">Shielding</td><td class="py-1 px-2"><?php echo (int)$character['shielding']; ?></td></tr>
					<tr class="border-b border-[var(--box-inner-border)]"><td class="py-1 px-2">Fishing</td><td class="py-1 px-2"><?php echo (int)$character['fishing']; ?></td></tr>
				</table>
				</div>
				<?php
				$server = $config['shop']['imageServer'];
				$imageType = $config['shop']['imageType'];
				$items = getItemList();
				?>
				<!-- Player items -->
				<?php if (is_array($player_items) && !empty($player_items)): ?>
					<div class="overflow-x-auto">
					<table class="<?php echo auction_table_class(); ?>">
						<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
							<td class="py-1.5 px-2" colspan="3">Player items</td>
						</tr>
						<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
							<td class="py-1.5 px-2">Image</td>
							<td class="py-1.5 px-2">Item</td>
							<td class="py-1.5 px-2">Count</td>
						</tr>
						<?php foreach($player_items as $item): ?>
							<tr class="border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)]">
								<td class="py-1 px-2"><img src="<?php echo $server . '/' . (int)$item['itemtype'] . '.' . $imageType; ?>" alt="Item Image" class="w-8 h-8 object-contain"></td>
								<td class="py-1 px-2"><a class="<?php echo auction_link_class(); ?>" href="/market.php?compare=<?php echo (int)$item['itemtype']; ?>" target="_BLANK"><?php echo htmlspecialchars((isset($items[$item['itemtype']])) ? $items[$item['itemtype']] : $item['itemtype'], ENT_QUOTES, 'UTF-8'); ?></a></td>
								<td class="py-1 px-2"><?php echo (int)$item['count']; ?></td>
							</tr>
						<?php endforeach; ?>
					</table>
					</div>
				<?php endif; ?>
				<!-- Depot items -->
				<?php if (is_array($depot_items) && !empty($depot_items)): ?>
					<div class="overflow-x-auto">
					<table class="<?php echo auction_table_class(); ?>">
						<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
							<td class="py-1.5 px-2" colspan="3">Depot items</td>
						</tr>
						<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
							<td class="py-1.5 px-2">Image</td>
							<td class="py-1.5 px-2">Item</td>
							<td class="py-1.5 px-2">Count</td>
						</tr>
						<?php foreach($depot_items as $item): ?>
							<tr class="border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)]">
								<td class="py-1 px-2"><img src="<?php echo $server . '/' . (int)$item['itemtype'] . '.' . $imageType; ?>" alt="Item Image" class="w-8 h-8 object-contain"></td>
								<td class="py-1 px-2"><a class="<?php echo auction_link_class(); ?>" href="/market.php?compare=<?php echo (int)$item['itemtype']; ?>" target="_BLANK"><?php echo htmlspecialchars((isset($items[$item['itemtype']])) ? $items[$item['itemtype']] : $item['itemtype'], ENT_QUOTES, 'UTF-8'); ?></a></td>
								<td class="py-1 px-2"><?php echo (int)$item['count']; ?></td>
							</tr>
						<?php endforeach; ?>
					</table>
					</div>
				<?php endif;
			} else {
				$action = 'list';
			}
		}
	}

	// If we are adding a character to the list
	// silently continues to list
	if ($action === 'add') {
		$pid = (isset($_POST['pid']) && (int)$_POST['pid'] > 0) ? (int)$_POST['pid'] : false;
		$cost = (isset($_POST['cost']) && (int)$_POST['cost'] > 0) ? (int)$_POST['cost'] : false;
		$deposit = (int)$cost * ($auction['deposit'] / 100);
		$password = SHA1($_POST['password']);

		// Verify values
		$status = false;
		$account = false;
		if ($pid > 0 && $cost >= $auction['lowestPrice']) {
			$account = mysql_select_single("
				SELECT `a`.`id`, `a`.`password`, `za`.`points`
				FROM `accounts` a
				INNER JOIN `znote_accounts` za
					ON `a`.`id` = `za`.`account_id`
				WHERE `a`.`id`= {$this_account_id}
				AND `a`.`password`='{$password}'
				AND `za`.`points` >= {$deposit}
				LIMIT 1
			;");
			if (isset($account['password']) && $account['password'] === $password) {
				// Check if player exist, is offline and not already in auction
				// And is not a tutor or a GM+.
				$player = mysql_select_single("
					SELECT `p`.`id`, `p`.`name`,
					CASE
						WHEN `po`.`player_id` IS NULL
						THEN 0
						ELSE 1
					END AS `online`,
					CASE
						WHEN `za`.`player_id` IS NULL
						THEN 0
						ELSE 1
					END AS `alreadyInAuction`
					FROM `players` p
					LEFT JOIN `players_online` po
						ON `p`.`id` = `po`.`player_id`
					LEFT JOIN `znote_auction_player` za
						ON `p`.`id` = `za`.`player_id`
						AND `p`.`account_id` = `za`.`original_account_id`
						AND `za`.`claimed` = 0
					WHERE `p`.`id` = {$pid}
					AND `p`.`account_id` = {$this_account_id}
					AND `p`.`group_id` = 1
					LIMIT 1
				;");
				// Verify storage account ID exist
				$storage_account = mysql_select_single("
					SELECT `id`
					FROM `accounts`
					WHERE `id`={$auction['storage_account_id']}
					LIMIT 1;
				");
				if ($storage_account === false) {
					data_dump($auction, false, "Configured storage_account_id in config.php does not exist!");
				} else {
					if (isset($player['online']) && $player['online'] == 0) {
						if (isset($player['alreadyInAuction']) && $player['alreadyInAuction'] == 0) {
							$status = true;
						}
					}
				}
			}
		}
		if ($status) {
			$time_begin = time();
			$time_end = $time_begin + ($auction['biddingDuration']);
			// Insert row to znote_auction_player
			mysql_insert("
				INSERT INTO `znote_auction_player` (
					`player_id`,
					`original_account_id`,
					`bidder_account_id`,
					`time_begin`,
					`time_end`,
					`price`,
					`bid`,
					`deposit`,
					`sold`,
					`claimed`
				) VALUES (
					{$pid},
					{$this_account_id},
					0,
					{$time_begin},
					{$time_end},
					{$cost},
					0,
					{$deposit},
					0,
					0
				);
			");
			// Move player to storage account
			mysql_update("
				UPDATE `players`
				SET `account_id` = {$auction['storage_account_id']}
				WHERE `id` = {$pid}
				LIMIT 1;
			");
			// Hide character from public character list (in pidprofile.php)
			mysql_update("
				UPDATE `znote_players`
				SET `hide_char` = 1
				WHERE `player_id` = {$pid}
				LIMIT 1;
			");
			// Remove deposit from account
			$afterDeposit = $account['points'] - $deposit;
			mysql_update("
				UPDATE `znote_accounts`
				SET `points` = {$afterDeposit}
				WHERE `account_id` = {$account['id']}
				LIMIT 1;
			");
		}
		$action = 'list';
	}

	// If we are refunding a player back to its original owner
	// silently continues to list
	if ($action === 'refund') {
		$zaid = (isset($_POST['zaid']) && (int)$_POST['zaid'] > 0) ? (int)$_POST['zaid'] : false;
		//data_dump($_POST, false, "POST");
		if ($zaid !== false) {
			$time = time();
			// If original account is the one trying to get it back,
			// and bidding period is over,
			// and its not labeled as sold
			// and nobody has bid on it
			$character = mysql_select_single("
				SELECT `player_id`
				FROM `znote_auction_player`
				WHERE `id`= {$zaid}
				AND `original_account_id` = {$this_account_id}
				AND `time_end` <= {$time}
				AND `bidder_account_id` = 0
				AND `bid` = 0
				AND `sold` = 0
				LIMIT 1
			");
			//data_dump($character, false, "Character");
			if ($character !== false) {
				// Move character to buyer account and give it a new name
				mysql_update("
					UPDATE `players`
					SET `account_id` = {$this_account_id}
					WHERE `id` = {$character['player_id']}
					LIMIT 1;
				");
				// Set label to sold
				mysql_update("
					UPDATE `znote_auction_player`
					SET `sold` = 1
					WHERE `id`= {$zaid}
					LIMIT 1;
				");
				// Show character in public character list (in characterprofile.php)
				mysql_update("
					UPDATE `znote_players`
					SET `hide_char` = 0
					WHERE `player_id` = {$character['player_id']}
					LIMIT 1;
				");
			}
		}
		$action = 'list';
	}

	// If we are claiming a character
	// If validation fails then explain why, but then head over to list regardless of status
	if ($action === 'claim') {
		$zaid = (isset($_POST['zaid']) && (int)$_POST['zaid'] > 0) ? (int)$_POST['zaid'] : false;
		$name = (isset($_POST['name']) && !empty($_POST['name'])) ? getValue($_POST['name']) : false;
		$errors = array();
		//data_dump($_POST, $name, "Post data:");
		if ($zaid === false) {
			$errors[] = 'We are unable to find this auction order.';
		}
		if ((int)$auction['storage_account_id'] === $this_account_id) {
			$errors[] = 'Silly you! You cannot claim characters with the storage account configured in <br>$config[\'shop_auction\'][\'storage_account_id\']<br>because you already have those characters in your account! :P';
			if ($is_admin) {
				$errors[] = "ADMIN: The storage account in config.php should not be the same as the admin account.";
			}
		}
		if ($name === false) {
			$errors[] = 'Please give the character a name.';
		} else {
			// begin name validation
			$name = validate_name($name);
			if (user_character_exist($name) !== false) {
				$errors[] = 'Sorry, that character name already exist.';
			}
			if (!preg_match("/^[a-zA-Z_ ]+$/", $name)) {
				$errors[] = 'Your name may only contain a-z, A-Z and spaces.';
			}
			if (strlen($name) < $config['minL'] || strlen($name) > $config['maxL']) {
				$errors[] = 'Your character name must be between ' . $config['minL'] . ' - ' . $config['maxL'] . ' characters long.';
			}
			// name restriction
			$resname = explode(" ", $name);
			foreach($resname as $res) {
				if(in_array(strtolower($res), $config['invalidNameTags'])) {
					$errors[] = 'Your username contains a restricted word.';
				}
				else if(strlen($res) == 1) {
					$errors[] = 'Too short words in your name.';
				}
			}
			$name = format_character_name($name);
			// end name validation
			if (empty($errors)) {
				// Make sure you have access to claim this zaid character.
				// And that you haven't already claimed it.
				// And that the character isn't online...
				$character = mysql_select_single("
					SELECT
						`za`.`id` AS `zaid`,
						`za`.`player_id`,
						`p`.`account_id`
					FROM `znote_auction_player` za
					INNER JOIN `players` p
						ON `za`.`player_id` = `p`.`id`
					LEFT JOIN `players_online` po
						ON `p`.`id` = `po`.`player_id`
					WHERE `za`.`id` = {$zaid}
					AND `za`.`sold` = 1
					AND `p`.`account_id` != {$this_account_id}
					AND `za`.`bidder_account_id` = {$this_account_id}
					AND `po`.`player_id` IS NULL
				");
				//data_dump($character, false, "Character");
				if ($character !== false) {
					// Set character to claimed
					mysql_update("
						UPDATE `znote_auction_player`
						SET `claimed`='1'
						WHERE `id` = {$character['zaid']}
					");
					// Move character to buyer account and give it a new name
					mysql_update("
						UPDATE `players`
						SET `name` = '{$name}',
							`account_id` = {$this_account_id}
						WHERE `id` = {$character['player_id']}
						LIMIT 1;
					");
					// Show character in public character list (in characterprofile.php)
					mysql_update("
						UPDATE `znote_players`
						SET `hide_char` = 0
						WHERE `player_id` = {$character['player_id']}
						LIMIT 1;
					");
					// Remove character from other players VIP lists
					mysql_delete("
						DELETE FROM `account_viplist`
						WHERE `player_id` = {$character['player_id']}
					");
					// Remove the character deathlist
					mysql_delete("
						DELETE FROM `player_deaths`
						WHERE `player_id` = {$character['player_id']}
					");
				} else {
					$errors[] = "You either don't have access to claim this character, or you have already claimed it, or this character isn't sold yet, or we were unable to find this auction order.";
					if ($is_admin) {
						$errors[] = "ADMIN: ... Or character is online.";
					}
				}
			}
		}
		if (!empty($errors)) {
			//data_dump($errors, false, "Errors:");
			?>
			<div class="overflow-x-auto">
			<table class="<?php echo auction_table_class('auction_error'); ?>">
				<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
					<td class="py-1.5 px-2">#</td>
					<td class="py-1.5 px-2">Issues occurred while claiming your name</td>
				</tr>
				<?php foreach($errors as $i => $error): ?>
					<tr class="border-b border-[var(--box-inner-border)]">
						<td class="py-1 px-2"><?php echo $i+1; ?></td>
						<td class="py-1 px-2 text-red-600"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
			</div>
			<?php
		}
		$action = 'list';
	}

	// List characters currently in the auction
	if ($action === 'list') {
		// If this account have successfully bought or won an auction
		// Intercept the list action and let the user do claim actions
		$pending = mysql_select_multi("
			SELECT
				`za`.`id` AS `zaid`,
				CASE WHEN `za`.`price` > `za`.`bid`
					THEN `za`.`price`
					ELSE `za`.`bid`
				END AS `price`,
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
			AND `za`.`claimed` = 0
			AND `za`.`sold` = 1
			AND `za`.`bidder_account_id` = {$this_account_id}
			ORDER BY `p`.`level` desc
		");
		//data_dump($pending, false, "Pending characters:");
		if ($pending !== false) {
			?>
			<div class="PageHeader-module__HZea0q__header ">
				<div class="PageHeader-module__HZea0q__headerContent">
					<h2 class="PageHeader-module__HZea0q__title">Congratulations!</h2>
				</div>
			</div>
			<div class="text-xs text-[#22c55e] font-semibold">You have <?php echo (COUNT($pending) > 1) ? 'characters' : 'a character'; ?> ready to claim!</div>
			<?php foreach($pending as $character): ?>
			<div class="overflow-x-auto">
			<table class="<?php echo auction_table_class('auction_char'); ?>">
				<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
					<td class="py-1.5 px-2">Level</td>
					<td class="py-1.5 px-2">Vocation</td>
					<td class="py-1.5 px-2">Details</td>
					<td class="py-1.5 px-2">Price</td>
				</tr>
					<tr class="border-b border-[var(--box-inner-border)]">
						<td class="py-1 px-2"><?php echo (int)$character['level']; ?></td>
						<td class="py-1 px-2"><?php echo htmlspecialchars(vocation_id_to_name($character['vocation']), ENT_QUOTES, 'UTF-8'); ?></td>
						<td class="py-1 px-2"><a class="<?php echo auction_link_class(); ?>" href="/auctionChar.php?action=view&zaid=<?php echo (int)$character['zaid']; ?>">View</a></td>
						<td class="py-1 px-2"><?php echo (int)$character['price']; ?></td>
					</tr>
					<tr class="border-b border-[var(--box-inner-border)]">
						<?php if ($loadOutfits): ?>
							<td class="py-1 px-2 outfitColumn">
								<img class="mx-auto" src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo (int)$character['type']; ?>&addons=<?php echo (int)$character['addons']; ?>&head=<?php echo (int)$character['head']; ?>&body=<?php echo (int)$character['body']; ?>&legs=<?php echo (int)$character['legs']; ?>&feet=<?php echo (int)$character['feet']; ?>" alt="Character outfit">
							</td>
						<?php endif; ?>
						<td class="py-1 px-2" colspan="<?php echo $loadOutfits ? 3 : 4; ?>">
							<div class="text-[var(--text-muted)] mb-2">Hello master, what should my new name be?</div>
							<form action="/auctionChar.php" method="POST" class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2 items-end">
								<input type="hidden" name="action" value="claim">
								<input type="hidden" name="zaid" value="<?php echo (int)$character['zaid']; ?>">
								<input type="text" name="name" class="<?php echo auction_input_class(); ?>">
								<button type="submit" class="<?php echo auction_button_class(); ?>">Claim character</button>
							</form>
						</td>
					</tr>
			</table>
			</div>
			<?php endforeach; ?>
			<div class="PageHeader-module__HZea0q__header ">
				<div class="PageHeader-module__HZea0q__headerContent">
					<h2 class="PageHeader-module__HZea0q__title">Ongoing auctions</h2>
				</div>
			</div>
			<?php
		}

		// Show the list
		$characters = mysql_select_multi("
			SELECT
				`za`.`id` AS `zaid`,
				CASE WHEN `za`.`price` > `za`.`bid`
					THEN `za`.`price`
					ELSE `za`.`bid`+{$step}
				END AS `price`,
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
			ORDER BY `p`.`level` desc;
		");
		//data_dump($characters, false, "List characters");
		if ($is_admin) {
			?>
			<div class="text-xs text-[var(--text-muted)]">Admin: <a class="<?php echo auction_link_class(); ?>" href="/admin_auction.php">Character auction history</a></div>
			<?php
		}
		if (is_array($characters) && !empty($characters)):
			?>
			<div class="overflow-x-auto">
			<table class="<?php echo auction_table_class('auction_char'); ?>">
				<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
					<td class="py-1.5 px-2">Level</td>
					<td class="py-1.5 px-2">Vocation</td>
					<?php if ($loadOutfits): ?>
						<td class="py-1.5 px-2">Image</td>
					<?php endif; ?>
					<td class="py-1.5 px-2">Details</td>
					<td class="py-1.5 px-2">Price</td>
					<td class="py-1.5 px-2">Added</td>
					<td class="py-1.5 px-2">Type</td>
				</tr>
				<?php foreach($characters as $character): ?>
					<tr class="border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)]">
						<td class="py-1 px-2"><?php echo (int)$character['level']; ?></td>
						<td class="py-1 px-2"><?php echo htmlspecialchars(vocation_id_to_name($character['vocation']), ENT_QUOTES, 'UTF-8'); ?></td>
						<?php if ($loadOutfits): ?>
							<td class="py-1 px-2 outfitColumn">
								<img class="mx-auto" src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo (int)$character['type']; ?>&addons=<?php echo (int)$character['addons']; ?>&head=<?php echo (int)$character['head']; ?>&body=<?php echo (int)$character['body']; ?>&legs=<?php echo (int)$character['legs']; ?>&feet=<?php echo (int)$character['feet']; ?>" alt="Character outfit">
							</td>
						<?php endif; ?>
						<td class="py-1 px-2"><a class="<?php echo auction_link_class(); ?>" href="/auctionChar.php?action=view&zaid=<?php echo (int)$character['zaid']; ?>">View</a></td>
						<td class="py-1 px-2"><?php echo (int)$character['price']; ?></td>
						<td class="py-1 px-2"><?php
							$ended = (time() > $character['time_end']) ? true : false;
							echo htmlspecialchars(getClock($character['time_begin'], true), ENT_QUOTES, 'UTF-8');
							?>
						</td>
						<td class="py-1 px-2"><?php echo ($ended) ? 'Instant' : 'Bidding<br>('.htmlspecialchars(toDuration(($character['time_end'] - time())), ENT_QUOTES, 'UTF-8').')'; ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
			</div>
			<?php else: ?>
				<?php auction_notice('There are no ongoing auctions right now.'); ?>
			<?php
		endif;
		?>
		<div><a href="/auctionChar.php?action=create"><button class="<?php echo auction_button_class(); ?>">Add a character to the auction</button></a></div>
		<?php

	} elseif ($action === 'create') { // Add player to auction view
		$minToCreate = (int)ceil(($auction['lowestPrice'] / 100) * $auction['deposit']);
		$own_characters = mysql_select_multi("
			SELECT
				`p`.`id`,
				`p`.`name`,
				`p`.`level`,
				`p`.`vocation`,
				`a`.`points`
			FROM `players` p
			INNER JOIN `znote_accounts` a
				ON `p`.`account_id` = `a`.`account_id`
			LEFT JOIN `znote_auction_player` za
				ON `p`.`id` = `za`.`player_id`
				AND `p`.`account_id` = `za`.`original_account_id`
				AND `za`.`claimed` = 0
			LEFT JOIN `players_online` po
				ON `p`.`id` = `po`.`player_id`
			WHERE `p`.`account_id`={$this_account_id}
			AND `za`.`player_id` IS NULL
			AND `po`.`player_id` IS NULL
			AND `p`.`level` >= {$auction['lowestLevel']}
			AND `a`.`points` >= $minToCreate
		;");
		//data_dump($own_characters, false, "own_chars");

		if (is_array($own_characters) && !empty($own_characters)) {
			$max = ($own_characters[0]['points'] / $auction['deposit']) * 100;
			?>
			<div class="text-xs"><a class="<?php echo auction_link_class(); ?>" href="/auctionChar.php?action=list">Go back to list.</a></div>
			<form action="/auctionChar.php" method="POST" class="space-y-3">
				<input type="hidden" name="action" value="add">
				<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
					<label for="auction_pid" class="font-medium">Character <span class="text-[var(--text-muted)]">(must be offline)</span></label>
					<select id="auction_pid" name="pid" class="<?php echo auction_input_class(); ?>">
					<?php if(is_array($own_characters) && !empty($own_characters))
					foreach($own_characters as $char): ?>
						<option value="<?php echo (int)$char['id']; ?>">
							<?php echo htmlspecialchars("Level: ". $char['level'] ." ". vocation_id_to_name($char['vocation']) .": ". $char['name'], ENT_QUOTES, 'UTF-8'); ?>
						</option>
					<?php endforeach; ?>
					</select>
				</div>
				<div class="text-xs text-[var(--text-muted)]"><strong>Shop points:</strong>
					<br>Your current points: <?php echo $own_characters[0]['points']; ?>
					<br>Minimum: <?php echo $auction['lowestPrice']; ?>
					<br>Deposit: <?php echo $auction['deposit']; ?>%
					<br>Your maximum: <?php echo $max; ?>
				</div>
				<div class="text-xs text-[var(--text-muted)]"><strong>Deposit information:</strong>
					<br>To ensure you as the seller is a legitimate account, and to encourage fair prices you have to temporarily invest <?php echo $auction['deposit']; ?>% of the selling price as a deposit.
				</div>
				<div class="text-xs text-[var(--text-muted)]">Once the auction has completed, the deposit fee will be refunded back to your account.</div>
				<div class="text-xs text-[var(--text-muted)]">If you wish to reclaim your character, you can do it after the bidding period if nobody has placed an offer on it. But if you do this you will not get the deposit back. It is therefore advisable that you create a good and appealing offer to our community.</div>
				<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
					<label for="auction_cost" class="font-medium">Sell price</label>
					<input id="auction_cost" type="number" name="cost" min="<?php echo (int)$auction['lowestPrice']; ?>" max="<?php echo (int)$max; ?>" step="5" placeholder="<?php echo (int)$auction['lowestPrice']; ?> - <?php echo (int)$max; ?>" class="<?php echo auction_input_class(); ?>">
				</div>
				<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
					<label for="auction_password" class="font-medium">Verify with your password</label>
					<input id="auction_password" type="password" name="password" class="<?php echo auction_input_class(); ?>">
				</div>
				<div class="flex justify-end">
					<button type="submit" class="<?php echo auction_button_class(); ?>">Sell character</button>
				</div>
			</form>
			<?php
		} else {
			?>
			<div class="text-xs"><a class="<?php echo auction_link_class(); ?>" href="/auctionChar.php?action=list">Go back to list.</a></div>
			<div class="text-xs text-[var(--text-muted)]">
				Your account does not follow the required rules to sell characters.
				<br>1. Minimum level: <?php echo (int)$auction['lowestLevel']; ?>
				<br>2. Minimum already earned shop points: <?php echo (int)$minToCreate; ?>
				<br>3. Eligible characters must be offline.
			</div>
			<?php
		}
	}
} else auction_notice('Character shop auctioning system is disabled.', 'error');
auction_page_end();
include 'layout/overall/footer.php'; ?>
