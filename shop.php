<?php require_once 'engine/init.php';
include 'layout/overall/header.php';

if (isset($_GET['callback']) && $_GET['callback'] === 'processing') {
	echo '<script>alert("Seu pagamento está sendo processado pelo PagSeguro...");</script>';
}

// Import from config:
$shop = $config['shop'];
if ($shop['loginToView'] === true) protect_page();
$loggedin = user_logged_in();
$shop_messages = array();

if ($config['shop']['useDB']) {
	$shop_list = [];
	$rows = mysql_select_multi("SELECT * FROM `znote_shop_offers` ORDER BY `id` ASC;");

	if ($rows !== false) {
		foreach ($rows as $r) {
			$itemid = (int)$r['itemid'];

			if ((int)$r['type'] === 5) {
				$male = floor($itemid / 10000);
				$female = $itemid % 10000;

				if ($female > 0) {
					$itemid = [$male, $female];
				} else {
					$itemid = [$male];
				}
			}

			$shop_list[(int)$r['id']] = [
				'type' => (int)$r['type'],
				'itemid' => $itemid,
				'count' => (int)$r['count'],
				'description' => $r['description'],
				'points' => (int)$r['points'],
			];
		}
	}
} else {
	$shop_list = $config['shop_offers'];
}

if ($loggedin === true) {
	if (!empty($_POST['buy']) && isset($_SESSION['shop_session'], $_POST['session']) && $_SESSION['shop_session'] == $_POST['session']) {
		$time = time();
		$player_points = (int)$user_znote_data['points'];
		$cid = (int)$user_data['id'];
		// Sanitizing post, setting default buy value
		$buy = false;
		$post = (int)$_POST['buy'];

		foreach ($shop_list as $key => $value) {
			if ($key === $post) {
				$buy = $value;
			}
		}
		if ($buy === false) die("Error: Shop offer ID mismatch.");

		// Verify that user can afford this offer.
		if ($player_points >= $buy['points']) {
			$data = mysql_select_single("SELECT `points` FROM `znote_accounts` WHERE `account_id`='$cid';");
			if (!$data) die("0: Account is not converted to work with Znote AAC");
			$old_points = $data['points'];
			if ((int)$old_points != (int)$player_points) die("1: Failed to equalize your points.");
			// Remove points if they can afford
			// Give points to user
			$expense_points = $buy['points'];
			$new_points = $old_points - $expense_points;
			$update_account = mysql_update("UPDATE `znote_accounts` SET `points`='$new_points' WHERE `account_id`='$cid'");

			$data = mysql_select_single("SELECT `points` FROM `znote_accounts` WHERE `account_id`='$cid';");
			$verify = $data['points'];
			if ((int)$old_points == (int)$verify) die("2: Failed to equalize your points.". var_dump((int)$old_points, (int)$verify, $new_points, $expense_points));

			// If this is an outfit offer, convert array into an integer.
			if ($buy['type'] == 5) {
				if (is_array($buy['itemid'])) {
					if (COUNT($buy['itemid']) == 2) {
						$buy['itemid'] = ($buy['itemid'][0] * 10000) + $buy['itemid'][1];
					} else {
						$buy['itemid'] = $buy['itemid'][0];
					}
				}
			}

			// Do the magic (insert into db, or change sex etc)
			// If type is 2 or 3
			if ($buy['type'] == 2) {
				// Add premium days to account
				user_account_add_premdays($cid, $buy['count']);
				$shop_messages[] = array('success', 'You now have '.(int)$buy['count'].' additional days of premium membership.');
			} else if ($buy['type'] == 3) {
				// Character Gender
				mysql_insert("INSERT INTO `znote_shop_orders` (`account_id`, `type`, `itemid`, `count`, `time`) VALUES ('$cid', '". $buy['type'] ."', '". $buy['itemid'] ."', '". $buy['count'] ."', '$time')");
				$shop_messages[] = array('success', 'You now have access to change character gender on your characters. Visit <a href="myaccount.php">My Account</a> to select character and change the gender.');
			} else if ($buy['type'] == 4) {
				// Character Name
				mysql_insert("INSERT INTO `znote_shop_orders` (`account_id`, `type`, `itemid`, `count`, `time`) VALUES ('$cid', '". $buy['type'] ."', '". $buy['itemid'] ."', '". $buy['count'] ."', '$time')");
				$shop_messages[] = array('success', 'You now have access to change character name on your characters. Visit <a href="myaccount.php">My Account</a> to select character and change the name.');
			} else {
				mysql_insert("INSERT INTO `znote_shop_orders` (`account_id`, `type`, `itemid`, `count`, `time`) VALUES ('$cid', '". $buy['type'] ."', '". $buy['itemid'] ."', '". $buy['count'] ."', '$time')");
				$shop_messages[] = array('success', 'Your order is ready to be delivered. Write this command in-game to get it: [!shop].<br>Make sure you are in depot and can carry it before executing the command!');
			}

			// No matter which type, we will always log it.
			mysql_insert("INSERT INTO `znote_shop_logs` (`account_id`, `player_id`, `type`, `itemid`, `count`, `points`, `time`) VALUES ('$cid', '0', '". $buy['type'] ."', '". $buy['itemid'] ."', '". $buy['count'] ."', '". $buy['points'] ."', '$time')");

		} else {
			$shop_messages[] = array('error', 'You need more points, this offer costs '.(int)$buy['points'].' points.');
		}
		//var_dump($buy);
		//echo '<font color="red" size="4">'. $_POST['buy'] .'</font>';
	}
}

if ($shop['enabled']) {
	$outfitsIds = array(136,137,138,139,140,141,142,147,148,149,150,155,156,157,158,252,269,270,279,288,324,336,366,431,433,464,466,471,513,514,542,128,129,130,131,132,133,134,143,144,145,146,151,152,153,154,251,268,273,278,289,325,335,367,430,432,463,465,472,512,516,541);
	$category_items = array();
	$category_premium = array();
	$category_outfits = array();
	$category_mounts = array();
	$category_misc = array();

	foreach ($shop_list as $key => $offer) {
		switch ($offer['type']) {
			case 1:
				$category_items[$key] = $offer;
			break;
			case 2:
				$category_premium[$key] = $offer;
			break;
			case 5:
				$category_outfits[$key] = $offer;
			break;
			case 6:
				$category_mounts[$key] = $offer;
			break;
			default:
				$category_misc[$key] = $offer;
			break;
		}
	}

	// Store current timestamp to prevent page-reload from processing old purchase.
	$_SESSION['shop_session'] = time();
	$shop_session = $_SESSION['shop_session'];

	function shop_panel_start($title, $icon = 'bag') {
		$icon_path = ($icon === 'coins')
			? '<circle cx="8" cy="8" r="6"></circle><path d="M18.09 10.37A6 6 0 1 1 10.34 18"></path>'
			: (($icon === 'users')
				? '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>'
				: '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path>');
		echo '<div class=""><div class="PageHeader-module__HZea0q__header "><div class="PageHeader-module__HZea0q__headerContent"><h2 class="PageHeader-module__HZea0q__title"><span class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide w-4 h-4" aria-hidden="true">' . $icon_path . '</svg>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</span></h2></div></div><div class="PageBody-module__OAJIWq__body "><div class="PageBody-module__OAJIWq__content">';
	}

	function shop_panel_end() {
		echo '</div></div></div><br>';
	}

	function shop_item_image_url($itemid) {
		global $config;
		$server = rtrim($config['shop']['imageServer'], '/');
		return $server . '/' . (int)$itemid . '.' . hhb_tohtml($config['shop']['imageType']);
	}

	function shop_offer_images($offer, $category) {
		global $config, $outfitsIds;
		$html = '';

		if ($category === 'outfits' && $config['show_outfits']['shop']) {
			$itemids = is_array($offer['itemid']) ? $offer['itemid'] : array($offer['itemid']);
			foreach ($itemids as $outfitId) {
				$html .= '<img class="shop-offer-image" src="' . hhb_tohtml($config['show_outfits']['imageServer']) . '?id=' . (int)$outfitId . '&addons=' . (int)$offer['count'] . '&head=94&body=79&legs=114&feet=114" alt="">';
			}
		} else if ($category === 'mounts' && $config['show_outfits']['shop']) {
			$outfitId = $outfitsIds[((int)$offer['itemid']) % count($outfitsIds)];
			$html .= '<img class="shop-offer-image" src="' . hhb_tohtml($config['show_outfits']['imageServer']) . '?id=' . (int)$outfitId . '&addons=3&head=94&body=79&legs=114&feet=114&mount=' . (int)$offer['itemid'] . '&direction=2" alt="">';
		} else if ($config['shop']['showImage'] && !is_array($offer['itemid'])) {
			$html .= '<img class="shop-offer-image" src="' . shop_item_image_url($offer['itemid']) . '" alt="">';
		}

		if ($html === '') {
			$html = '<div class="shop-offer-placeholder">?</div>';
		}

		return $html;
	}

	function shop_offer_count_label($offer, $category) {
		if ($category === 'premium') return (int)$offer['count'] . ' days';
		if ($category === 'outfits') return ((int)$offer['count'] === 3) ? 'Both addons' : 'Addon ' . (int)$offer['count'];
		if ((int)$offer['count'] === 0) return 'Unlimited';
		return (int)$offer['count'] . 'x';
	}

	function shop_render_offer_card($key, $offer, $category, $loggedin, $shop_session) {
		$title = htmlspecialchars($offer['description'], ENT_QUOTES, 'UTF-8');
		$count_label = htmlspecialchars(shop_offer_count_label($offer, $category), ENT_QUOTES, 'UTF-8');
		$points = (int)$offer['points'];

		if ($category === 'outfits') {
			$itemids = is_array($offer['itemid']) ? $offer['itemid'] : array($offer['itemid']);
			if (count($itemids) > 2) {
				echo '<div class="shop-offer-card"><div class="text-xs text-red-600"><strong>Error:</strong> Outfit offer does not support more than 2 outfits.</div></div>';
				return;
			}
		}

		echo '<div class="shop-offer-card">';
		echo '<div class="shop-offer-visual">' . shop_offer_images($offer, $category) . '</div>';
		echo '<div class="shop-offer-title">' . $title . '</div>';
		echo '<div class="shop-offer-meta">' . $count_label . '</div>';
		echo '<div class="shop-offer-price">' . $points . ' points</div>';

		if ($loggedin === true) {
			echo '<form action="" method="POST" class="mt-3">';
			echo '<input type="hidden" name="buy" value="' . (int)$key . '">';
			echo '<input type="hidden" name="session" value="' . (int)$shop_session . '">';
			echo '<button type="submit" class="needconfirmation inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] w-full cursor-pointer" data-item-name="' . $title . '" data-item-cost="' . $points . '">Purchase</button>';
			echo '</form>';
		}

		echo '</div>';
	}

	function shop_render_category($id, $title, $icon, $offers, $category, $loggedin, $shop_session) {
		if (empty($offers)) return;
		shop_panel_start($title, $icon);
		echo '<div id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">';
		foreach ($offers as $key => $offer) {
			shop_render_offer_card($key, $offer, $category, $loggedin, $shop_session);
		}
		echo '</div>';
		shop_panel_end();
	}
?>

<style>
	.shop-offer-card {
		min-height: 222px;
		background: rgba(115, 96, 62, .18);
		border: 1px solid var(--box-inner-border);
		border-radius: 6px;
		padding: 12px;
		text-align: center;
		transition: border-color .15s ease, background .15s ease, transform .15s ease;
	}
	.shop-offer-card:hover {
		background: rgba(185, 150, 56, .13);
		border-color: rgba(185, 150, 56, .75);
		transform: translateY(-1px);
	}
	.shop-offer-visual {
		height: 82px;
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 4px;
		margin-bottom: 10px;
	}
	.shop-offer-image {
		max-width: 82px;
		max-height: 82px;
		object-fit: contain;
		image-rendering: auto;
	}
	.shop-offer-placeholder {
		width: 54px;
		height: 54px;
		border-radius: 6px;
		display: flex;
		align-items: center;
		justify-content: center;
		background: var(--box-inner-bg);
		border: 1px solid var(--box-inner-border);
		color: var(--text-muted);
		font-weight: 700;
	}
	.shop-offer-title {
		min-height: 38px;
		color: var(--foreground);
		font-size: 12px;
		font-weight: 700;
		line-height: 1.3;
	}
	.shop-offer-meta {
		margin-top: 4px;
		color: var(--text-muted);
		font-size: 11px;
	}
	.shop-offer-price {
		margin-top: 6px;
		color: var(--text-link);
		font-size: 13px;
		font-weight: 800;
	}
	.shop-confirm-overlay {
		position: fixed;
		inset: 0;
		z-index: 99998;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 16px;
		background: rgba(0, 0, 0, .28);
		opacity: 0;
		visibility: hidden;
		pointer-events: none;
		transition: opacity .18s ease, visibility .18s ease;
	}
	.shop-confirm-overlay.is-visible {
		opacity: 1;
		visibility: visible;
		pointer-events: auto;
	}
	.shop-confirm-dialog {
		width: min(100%, 440px);
	}
	.shop-confirm-actions {
		display: flex;
		justify-content: flex-end;
		gap: 8px;
		margin-top: 16px;
	}
</style>

<?php
	$current_points = ($loggedin === true) ? (int)$user_znote_data['points'] : 0;
	if ($loggedin === true && !empty($_POST['buy']) && isset($buy) && $buy !== false && $current_points >= (int)$buy['points']) {
		$current_points = $current_points - (int)$buy['points'];
	}

	shop_panel_start('Shop Offers', 'bag');
	foreach ($shop_messages as $message) {
		$text_class = ($message[0] === 'error') ? 'text-red-600' : 'text-[#22c55e]';
		echo '<div class="mb-3 text-xs ' . $text_class . '">' . $message[1] . '</div>';
	}
	if ($loggedin === true) {
		echo '<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">';
		echo '<div class="text-xs text-[var(--text-muted)]">You have <strong class="text-[var(--text-link)]">' . $current_points . '</strong> points.</div>';
		echo '<a href="buypoints.php" class="inline-flex items-center justify-center font-semibold transition-all duration-150 bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] px-3 py-1 text-[11px] hover:opacity-90">Buy Coins</a>';
		echo '</div>';
		if ($config['shop_auction']['characterAuction']) {
			echo '<div class="mt-3 text-xs text-[var(--text-muted)]">Interested in buying characters? View the <a href="auctionChar.php" class="text-[var(--text-link)] hover:text-[var(--text-link-hover)]">character auction page</a>.</div>';
		}
	} else {
		echo '<div class="text-xs text-[var(--text-muted)]">You need to be logged in to use the shop.</div>';
	}
	shop_panel_end();

	shop_render_category('cat_itemids', 'Items', 'bag', $category_items, 'items', $loggedin, $shop_session);
	shop_render_category('cat_premium', 'Premium', 'coins', $category_premium, 'premium', $loggedin, $shop_session);
	shop_render_category('cat_outfits', 'Outfits', 'users', $category_outfits, 'outfits', $loggedin, $shop_session);
	shop_render_category('cat_mounts', 'Mounts', 'users', $category_mounts, 'mounts', $loggedin, $shop_session);
	shop_render_category('cat_misc', 'Miscellaneous', 'bag', $category_misc, 'misc', $loggedin, $shop_session);

	if (empty($category_items) && empty($category_premium) && empty($category_outfits) && empty($category_mounts) && empty($category_misc)) {
		shop_panel_start('Shop Offers', 'bag');
		echo '<div class="text-center py-4 text-xs text-[var(--text-muted)]">There are no shop offers available.</div>';
		shop_panel_end();
	}
?>

<?php if ($shop['enableShopConfirmation']) { ?>
<div class="shop-confirm-overlay" id="shopConfirmOverlay" aria-hidden="true">
	<div class="shop-confirm-dialog">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart w-4 h-4" aria-hidden="true">
							<circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
						</svg>Confirm Purchase
					</span>
				</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="text-xs text-[var(--text-muted)]">
					Do you really want to purchase <strong id="shopConfirmItem" class="text-[var(--text-link)]"></strong> for <strong id="shopConfirmCost" class="text-[var(--text-link)]"></strong> points?
				</div>
				<div class="shop-confirm-actions">
					<button id="shopConfirmCancel" type="button" class="inline-flex items-center justify-center font-semibold transition-all duration-150 bg-[var(--box-inner-bg)] text-[var(--text-muted)] border border-[var(--box-inner-border)] px-3 py-1 text-[11px] hover:opacity-90 cursor-pointer">
						Cancel
					</button>
					<button id="shopConfirmAccept" type="button" class="inline-flex items-center justify-center font-semibold transition-all duration-150 bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] px-3 py-1 text-[11px] hover:opacity-90 cursor-pointer">
						Purchase
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		var overlay = document.getElementById('shopConfirmOverlay');
		var itemEl = document.getElementById('shopConfirmItem');
		var costEl = document.getElementById('shopConfirmCost');
		var cancelButton = document.getElementById('shopConfirmCancel');
		var acceptButton = document.getElementById('shopConfirmAccept');
		var pendingForm = null;

		function hideConfirm() {
			if (!overlay) return;
			overlay.classList.remove('is-visible');
			overlay.setAttribute('aria-hidden', 'true');
			pendingForm = null;
		}

		document.querySelectorAll('.needconfirmation').forEach(function (button) {
			button.addEventListener('click', function (event) {
				if (!overlay || !itemEl || !costEl) return;

				event.preventDefault();
				pendingForm = button.closest('form');
				itemEl.textContent = button.getAttribute('data-item-name') || 'this item';
				costEl.textContent = button.getAttribute('data-item-cost') || '0';
				overlay.classList.add('is-visible');
				overlay.setAttribute('aria-hidden', 'false');
			});
		});

		if (cancelButton) {
			cancelButton.addEventListener('click', hideConfirm);
		}

		if (overlay) {
			overlay.addEventListener('click', function (event) {
				if (event.target === overlay) hideConfirm();
			});
		}

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') hideConfirm();
		});

		if (acceptButton) {
			acceptButton.addEventListener('click', function () {
				if (!pendingForm) {
					hideConfirm();
					return;
				}

				pendingForm.submit();
			});
		}
	});
</script>
<?php }

} else {
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag w-4 h-4" aria-hidden="true">
							<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path>
						</svg>Shop Disabled
					</span>
				</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="text-center py-4">
					<div class="text-xs text-red-600">Shop system disabled.</div>
					<p class="text-xs text-[var(--text-muted)] mt-2">Sorry, this functionality is disabled.</p>
				</div>
			</div>
		</div>
	</div>
	<?php
}
include 'layout/overall/footer.php'; ?>
