<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';

if ($config['log_ip']) znote_visitor_insert_detailed_data(3);

function house_panel_start($title) {
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header "><div class="PageHeader-module__HZea0q__headerContent"><h2 class="PageHeader-module__HZea0q__title"><span class="flex items-center gap-2">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house w-4 h-4" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10.5 12 3l9 7.5"></path><path d="M5 10v11h14V10"></path></svg>
			<?php echo hhb_tohtml($title); ?>
		</span></h2></div></div>
		<div class="PageBody-module__OAJIWq__body "><div class="PageBody-module__OAJIWq__content"><div class="space-y-3">
	<?php
}
function house_panel_end() { ?></div></div></div></div><br><?php }
function house_notice($message, $type = 'success') {
	$text_class = ($type === 'error') ? 'text-red-600' : (($type === 'warning') ? 'text-[#f59e0b]' : 'text-[#22c55e]');
	?><div class="p-3 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-xs <?php echo $text_class; ?>"><?php echo $message; ?></div><?php
}
function house_rows($rows) { ?>
	<div style="overflow-x:auto;"><table class="w-full text-xs text-[var(--text-muted)] border border-[var(--box-inner-border)]" style="border-collapse: collapse;"><tbody>
	<?php foreach ($rows as $row): ?><tr><td class="px-2 py-1 border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)]" style="width:38%;"><b><?php echo hhb_tohtml($row[0]); ?></b></td><td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo $row[1]; ?></td></tr><?php endforeach; ?>
	</tbody></table></div>
<?php }

$button_class = 'inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] hover:opacity-90 px-3 py-1 text-[11px] cursor-pointer';
$input_class = 'px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none';
$select_class = 'px-2 py-1 text-xs bg-[var(--box-inner-bg)] text-[var(--text-muted)] border border-[var(--box-inner-border)] focus:outline-none';
$messages = array();

$houseId = (isset($_GET['id']) && (int)$_GET['id'] > 0) ? (int)$_GET['id'] : false;
if ($houseId === false) {
	house_panel_start('No House Selected');
	house_notice('Go back to the <a class="text-[var(--text-link)]" href="houses.php">house list</a> and select a house for further details.', 'warning');
	house_panel_end();
	include 'layout/overall/footer.php';
	exit();
}

$house_SQL = "SELECT `h`.`id`, `h`.`owner`, `h`.`paid`, `h`.`name`, `h`.`rent`, `h`.`town_id`, `h`.`size`, `h`.`beds`, `h`.`bid`, `h`.`bid_end`, `h`.`last_bid`, `h`.`highest_bidder`, `p`.`name` AS `ownername` FROM `houses` AS `h` LEFT JOIN `players` AS `p` ON `h`.`owner` > 0 AND `p`.`id` = `h`.`owner` WHERE `h`.`id`='{$houseId}';";
$house = mysql_select_single($house_SQL);
if ($house === false) {
	house_panel_start('House Not Found');
	house_notice('This house could not be found.', 'error');
	house_panel_end();
	include 'layout/overall/footer.php';
	exit();
}

$minbid = $config['houseConfig']['minimumBidSQM'] * $house['size'];
if ($house['owner'] == 0) unset($house['ownername']);
if ($config['houseConfig']['shopPoints']['enabled']) {
	$house['points'] = $house['size'];
	foreach ($config['houseConfig']['shopPoints']['cost'] as $cost_sqm => $cost_points) if ($cost_sqm < $house['size']) $house['points'] = $cost_points;
}

$bid_char = isset($_POST['char']) ? (int)$_POST['char'] : false;
$bid_amount = isset($_POST['amount']) ? (int)$_POST['amount'] : false;

if ($bid_amount && $bid_char) {
	$player = mysql_select_single("SELECT `id`, `account_id`, `name`, `level`, `balance` FROM `players` WHERE `id`='$bid_char' LIMIT 1;");
	if (user_logged_in() === true && $player !== false && $player['account_id'] == $session_user_id) {
		$premstatus = ($config['houseConfig']['requirePremium'] && $user_data['premdays'] == 0) ? false : true;
		if ($premstatus) {
			$pHouseCount = mysql_select_single("SELECT COUNT('id') AS `value` FROM `houses` WHERE ((`highest_bidder`='{$bid_char}' AND `owner`='{$bid_char}') OR (`highest_bidder`='{$bid_char}') OR (`owner`='{$bid_char}')) AND `id`!='{$house['id']}' LIMIT 1;");
			if ($pHouseCount['value'] < $config['houseConfig']['housesPerPlayer']) {
				if ($player['level'] >= $config['houseConfig']['levelToBuyHouse']) {
					if ($player['balance'] > $bid_amount) {
						if ($bid_amount > $house['bid']) {
							if ($bid_amount > $minbid) {
								$lastbid = ($house['highest_bidder'] != $player['id']) ? $house['bid'] + 1 : $house['last_bid'];
								if ($house['highest_bidder'] == $player['id']) $messages[] = array('You have raised the house pledge to ' . $bid_amount . 'gp.', 'success');
								if ($house['bid_end'] > 0) {
									if ($house['bid_end'] > time()) mysql_update("UPDATE `houses` SET `highest_bidder`='{$player['id']}', `bid`='{$bid_amount}', `last_bid`='{$lastbid}' WHERE `id`='{$house['id']}' LIMIT 1;");
								} else {
									$lastbid = $minbid + 1;
									$bidend = time() + $config['houseConfig']['auctionPeriod'];
									mysql_update("UPDATE `houses` SET `highest_bidder`='{$player['id']}', `bid`='{$bid_amount}', `last_bid`='{$lastbid}', `bid_end`='{$bidend}' WHERE `id`='{$house['id']}' LIMIT 1;");
								}
								$house = mysql_select_single($house_SQL);
								$messages[] = array('You have the highest bid on this house.', 'success');
							} else $messages[] = array('You need to place a bid higher or equal to ' . $minbid . 'gp.', 'error');
						} else if ($bid_amount > $house['last_bid']) {
							if ($house['highest_bidder'] != $player['id']) {
								$lastbid = $bid_amount + 1;
								mysql_update("UPDATE `houses` SET `last_bid`='$lastbid' WHERE `id`='{$house['id']}' LIMIT 1;");
								$house = mysql_select_single($house_SQL);
								$messages[] = array('Unfortunately your bid was not higher than previous bidder.', 'warning');
							} else $messages[] = array('You already have a higher pledge on this house.', 'warning');
						} else $messages[] = array('Too low bid amount, someone else has a higher bid active.', 'error');
					} else $messages[] = array('You do not have enough money to bid this high.', 'error');
				} else $messages[] = array('Your character is too low level. Minimum level: ' . $config['houseConfig']['levelToBuyHouse'] . '.', 'error');
			} else $messages[] = array('You cannot have more houses.', 'error');
		} else $messages[] = array('You need premium account to purchase houses.', 'error');
	} else $messages[] = array('You may only bid on houses for characters on your account.', 'error');
}

if ($config['houseConfig']['shopPoints']['enabled'] && isset($_POST['instantbuy']) && $bid_char && $house['owner'] == 0 && isset($house['points'])) {
	$account_points = (int)$user_znote_data['points'];
	if ($account_points >= $house['points']) {
		$player = mysql_select_single("SELECT `id`, `account_id`, `name`, `level` FROM `players` WHERE `id`='$bid_char' LIMIT 1;");
		$pHouseCount = mysql_select_single("SELECT COUNT('id') AS `value` FROM `houses` WHERE ((`highest_bidder`='$bid_char' AND `owner`='$bid_char') OR (`highest_bidder`='$bid_char') OR (`owner`='$bid_char')) AND `id`!='{$house['id']}' LIMIT 1;");
		if (user_logged_in() === true && $player['account_id'] == $session_user_id && $player['level'] >= $config['houseConfig']['levelToBuyHouse'] && $pHouseCount['value'] < $config['houseConfig']['housesPerPlayer']) {
			$house_points = (int)$house['points'];
			$house_id = (int)$house['id'];
			mysql_update("UPDATE `znote_accounts` SET `points` = `points`-{$house_points} WHERE `account_id`={$session_user_id} LIMIT 1;");
			mysql_update("UPDATE `houses` SET `owner` = {$bid_char} WHERE `id` = {$house_id} LIMIT 1;");
			$time = time();
			mysql_insert("INSERT INTO `znote_shop_logs` (`account_id`, `player_id`, `type`, `itemid`, `count`, `points`, `time`) VALUES ({$session_user_id}, {$bid_char}, 7, {$house_id}, 1, {$house_points}, {$time})");
			mysql_insert("INSERT INTO `znote_shop_orders` (`account_id`, `type`, `itemid`, `count`, `time`) VALUES ({$session_user_id}, 7, {$house_id}, {$bid_char}, {$time})");
			$house = mysql_select_single($house_SQL);
			if ($house['owner'] > 0) $house['ownername'] = user_name($house['owner']);
			$messages[] = array('Congratulations! You now own this house. Say !shop in-game to process your ownership.', 'success');
		} else $messages[] = array('Either your level is too low, or your player already has or is bidding on another house.', 'error');
	} else $messages[] = array('You do not have enough shop points to instantly buy this house.', 'error');
}

house_panel_start('House: ' . $house['name']);
foreach ($messages as $message) house_notice(hhb_tohtml($message[0]), $message[1]);
$town_name = isset($config['towns'][$house['town_id']]) ? $config['towns'][$house['town_id']] : 'Town ' . $house['town_id'];
$rows = array(
	array('Town', '<a class="text-[var(--text-link)]" href="houses.php?id=' . (int)$house['town_id'] . '">' . hhb_tohtml($town_name) . '</a>'),
	array('Size', (int)$house['size']),
	array('Beds', (int)$house['beds']),
	array('Owner', ($house['owner'] > 0 && isset($house['ownername'])) ? '<a class="text-[var(--text-link)]" href="characterprofile.php?name=' . urlencode($house['ownername']) . '">' . hhb_tohtml($house['ownername']) . '</a>' : 'Available for auction.'),
	array('Rent', (int)$house['rent']),
);
if ($house['owner'] == 0 && isset($house['points'])) $rows[] = array('Shop points', (int)$house['points']);
house_rows($rows);
house_panel_end();

if ($house['owner'] == 0) {
	house_panel_start('House Auction');
	if ($house['highest_bidder'] == 0) house_notice('<p style="color:brown">This house does not have any bidders yet.</p>', 'warning');
	else {
		$bidder = mysql_select_single("SELECT `name` FROM `players` WHERE `id`='{$house['highest_bidder']}' LIMIT 1;");
		house_rows(array(
			array('Active bid', (int)$house['last_bid'] . ' gp'),
			array('Active bid by', '<a class="text-[var(--text-link)]" href="characterprofile.php?name=' . urlencode($bidder['name']) . '">' . hhb_tohtml($bidder['name']) . '</a>'),
			array('Bid ends', hhb_tohtml(getClock($house['bid_end'], true))),
		));
	}
	if ($house['bid_end'] == 0 || $house['bid_end'] > time()) {
		if (user_logged_in()) {
			$yourChars = mysql_select_multi("SELECT `id`, `name`, `balance` FROM `players` WHERE `account_id`='{$user_data['id']}';");
			if ($yourChars !== false) { ?>
				<form class="space-y-2" action="" method="post">
					<select name="char" class="<?php echo $select_class; ?>">
						<?php foreach ($yourChars as $char): ?><option value="<?php echo (int)$char['id']; ?>"><?php echo hhb_tohtml($char['name']); ?> [<?php echo (int)$char['balance']; ?>]</option><?php endforeach; ?>
					</select>
					<input class="<?php echo $input_class; ?>" type="text" name="amount" placeholder="Min bid: <?php echo (int)($minbid + 1); ?>">
					<button class="<?php echo $button_class; ?>" type="submit">Bid on this house</button>
				</form>
				<?php if ($house['owner'] == 0 && isset($house['points'])): ?>
					<form class="space-y-2 pt-3 border-t border-[var(--box-inner-border)]" action="" method="post">
						<div class="text-xs text-[var(--text-muted)]">Your account has <b><?php echo (int)$user_znote_data['points']; ?></b> available shop points.</div>
						<select name="char" class="<?php echo $select_class; ?>">
							<?php foreach ($yourChars as $char): ?><option value="<?php echo (int)$char['id']; ?>"><?php echo hhb_tohtml($char['name']); ?></option><?php endforeach; ?>
						</select>
						<button class="<?php echo $button_class; ?>" type="submit" name="instantbuy" value="1" <?php if ((int)$user_znote_data['points'] < $house['points']) echo 'disabled'; ?>>Buy now for <?php echo (int)$house['points']; ?> shop points</button>
					</form>
				<?php endif;
			} else house_notice('You need a character to bid on this house.', 'warning');
		} else house_notice('You need to login before you can bid on houses.', 'warning');
	} else house_notice('Bid has ended. House transaction will proceed next server restart if active bidder has sufficient balance.', 'warning');
	house_panel_end();
}

include 'layout/overall/footer.php';
?>
