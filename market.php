<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';

function market_attr_read_uint8($blob, &$offset) {
	if ($offset + 1 > strlen($blob)) return null;
	$value = unpack('C', substr($blob, $offset, 1));
	$offset += 1;
	return (int)$value[1];
}

function market_attr_read_uint16($blob, &$offset) {
	if ($offset + 2 > strlen($blob)) return null;
	$value = unpack('v', substr($blob, $offset, 2));
	$offset += 2;
	return (int)$value[1];
}

function market_attr_read_uint32($blob, &$offset) {
	if ($offset + 4 > strlen($blob)) return null;
	$value = unpack('V', substr($blob, $offset, 4));
	$offset += 4;
	return (int)$value[1];
}

function market_attr_read_uint64($blob, &$offset) {
	if ($offset + 8 > strlen($blob)) return null;
	$parts = unpack('Vlow/Vhigh', substr($blob, $offset, 8));
	$offset += 8;
	return (int)($parts['low'] + ($parts['high'] * 4294967296));
}

function market_attr_read_string($blob, &$offset) {
	$length = market_attr_read_uint16($blob, $offset);
	if ($length === null || $offset + $length > strlen($blob)) return null;
	$value = substr($blob, $offset, $length);
	$offset += $length;
	return $value;
}

function market_attr_skip_bytes($blob, &$offset, $length) {
	if ($offset + $length > strlen($blob)) return false;
	$offset += $length;
	return true;
}

function market_extract_rarity_from_attributes($blob) {
	if (!is_string($blob) || $blob === '') {
		return 0;
	}

	$directMarkers = array(
		"\x06\x00rarity\x02",
		"\x06rarity\x02"
	);
	foreach ($directMarkers as $directMarker) {
		$markerPos = strpos($blob, $directMarker);
		if ($markerPos !== false) {
			$rarityOffset = $markerPos + strlen($directMarker);
			$rarityValue = market_attr_read_uint64($blob, $rarityOffset);
			if ($rarityValue !== null && $rarityValue > 0) {
				return (int)$rarityValue;
			}
		}
	}

	$offset = 0;
	$length = strlen($blob);
	while ($offset < $length) {
		$attrType = market_attr_read_uint8($blob, $offset);
		if ($attrType === null || $attrType === 0) {
			break;
		}

		switch ($attrType) {
			case 1:
			case 14:
			case 17:
			case 32:
			case 33:
			case 37:
			case 46:
				if (!market_attr_skip_bytes($blob, $offset, 1)) return 0;
				break;

			case 2:
			case 3:
			case 22:
			case 36:
			case 44:
			case 47:
				if (!market_attr_skip_bytes($blob, $offset, 2)) return 0;
				break;

			case 8:
			case 16:
			case 18:
			case 27:
			case 28:
			case 29:
			case 30:
			case 31:
			case 35:
			case 38:
			case 42:
			case 45:
				if (!market_attr_skip_bytes($blob, $offset, 4)) return 0;
				break;

			case 39:
			case 41:
				if (!market_attr_skip_bytes($blob, $offset, 1)) return 0;
				break;

			case 40:
				if (!market_attr_skip_bytes($blob, $offset, 15)) return 0;
				break;

			case 6:
			case 7:
			case 10:
			case 11:
			case 24:
			case 25:
			case 26:
				if (market_attr_read_string($blob, $offset) === null) return 0;
				break;

			case 34:
				$customCount = market_attr_read_uint64($blob, $offset);
				if ($customCount === null) return 0;
				for ($i = 0; $i < $customCount; $i++) {
					$key = market_attr_read_string($blob, $offset);
					if ($key === null) return 0;
					$valueType = market_attr_read_uint8($blob, $offset);
					if ($valueType === null) return 0;

					if ($valueType === 1) {
						$value = market_attr_read_string($blob, $offset);
						if ($value === null) return 0;
					} elseif ($valueType === 2) {
						$value = market_attr_read_uint64($blob, $offset);
						if ($value === null) return 0;
						if ($key === 'rarity') {
							return (int)$value;
						}
					} elseif ($valueType === 3) {
						if (!market_attr_skip_bytes($blob, $offset, 8)) return 0;
					} elseif ($valueType === 4) {
						if (!market_attr_skip_bytes($blob, $offset, 1)) return 0;
					} else {
						return 0;
					}
				}
				break;

			default:
				return 0;
		}
	}

	return 0;
}

function market_extract_custom_bool_attribute($blob, $attributeKey) {
	if (!is_string($blob) || $blob === '' || !is_string($attributeKey) || $attributeKey === '') {
		return false;
	}

	$lengthMarker = pack('v', strlen($attributeKey));
	$directMarker = $lengthMarker . $attributeKey . "\x04";
	$markerPos = strpos($blob, $directMarker);
	if ($markerPos !== false) {
		$valueOffset = $markerPos + strlen($directMarker);
		$value = market_attr_read_uint8($blob, $valueOffset);
		return $value === 1;
	}

	return false;
}

function market_extract_corruption_from_attributes($blob) {
	$corruptionStages = array(
		array('key' => 'fractured', 'id' => 'fractured'),
		array('key' => 'infernal_corrupted', 'id' => 'infernal'),
		array('key' => 'ancient_corrupted', 'id' => 'ancient'),
		array('key' => 'void_corrupted', 'id' => 'void'),
		array('key' => 'corrupted', 'id' => 'corrupted'),
	);

	foreach ($corruptionStages as $stage) {
		if (market_extract_custom_bool_attribute($blob, $stage['key'])) {
			return $stage['id'];
		}
	}

	return 'none';
}

function market_get_rarity_meta($rarityId) {
	$rarityId = (int)$rarityId;
	$map = array(
		1 => array('label' => 'Common', 'color' => '#b9c2d0', 'background' => 'rgba(185,194,208,.14)'),
		2 => array('label' => 'Uncommon', 'color' => '#59c66d', 'background' => 'rgba(89,198,109,.14)'),
		3 => array('label' => 'Rare', 'color' => '#3f8cff', 'background' => 'rgba(63,140,255,.14)'),
		4 => array('label' => 'Epic', 'color' => '#b04dff', 'background' => 'rgba(176,77,255,.14)'),
		5 => array('label' => 'Legendary', 'color' => '#ff9d2f', 'background' => 'rgba(255,157,47,.16)'),
		6 => array('label' => 'Exotic', 'color' => '#ff5f7a', 'background' => 'rgba(255,95,122,.16)'),
		7 => array('label' => 'Mythic', 'color' => '#ff6fd8', 'background' => 'rgba(255,111,216,.16)'),
		8 => array('label' => 'Chaos', 'color' => '#00c2b8', 'background' => 'rgba(0,194,184,.16)'),
		9 => array('label' => 'Eternal', 'color' => '#ffd84d', 'background' => 'rgba(255,216,77,.18)'),
		10 => array('label' => 'Divine', 'color' => '#7ee0ff', 'background' => 'rgba(126,224,255,.18)'),
		11 => array('label' => 'Phantasmal', 'color' => '#a9b8ff', 'background' => 'rgba(169,184,255,.18)'),
		12 => array('label' => 'Celestial', 'color' => '#7c5cff', 'background' => 'rgba(124,92,255,.18)'),
		13 => array('label' => 'Cosmic', 'color' => '#ffe17c', 'background' => 'rgba(255,225,124,.18)'),
		14 => array('label' => 'Abyssal', 'color' => '#ff7cd6', 'background' => 'rgba(255,124,214,.18)'),
		15 => array('label' => 'Transcendent', 'color' => '#ff4d4d', 'background' => 'rgba(255,77,77,.18)'),
	);

	return isset($map[$rarityId]) ? $map[$rarityId] : array(
		'label' => 'Tier ' . $rarityId,
		'color' => '#8d6b30',
		'background' => 'rgba(141,107,48,.14)',
	);
}

function market_get_corruption_meta($corruptionId) {
	$map = array(
		'corrupted' => array('label' => 'Corruption', 'color' => '#b357ff', 'background' => 'rgba(179,87,255,.16)'),
		'void' => array('label' => 'Void Corruption', 'color' => '#7a5cff', 'background' => 'rgba(122,92,255,.18)'),
		'ancient' => array('label' => 'Ancient Corruption', 'color' => '#3bb273', 'background' => 'rgba(59,178,115,.18)'),
		'infernal' => array('label' => 'Infernal Corruption', 'color' => '#ff7a1a', 'background' => 'rgba(255,122,26,.18)'),
		'fractured' => array('label' => 'Fractured', 'color' => '#ff3f5e', 'background' => 'rgba(255,63,94,.18)'),
	);

	return isset($map[$corruptionId]) ? $map[$corruptionId] : null;
}

function market_render_rarity_badge($attributesBlob) {
	$rarityId = market_extract_rarity_from_attributes($attributesBlob);
	if ($rarityId <= 0) {
		return '<span class="text-[var(--text-muted)]">None</span>';
	}

	$meta = market_get_rarity_meta($rarityId);
	$style = 'display:inline-flex;align-items:center;padding:3px 8px;border-radius:999px;border:1px solid ' . $meta['color'] . ';color:' . $meta['color'] . ';background:' . $meta['background'] . ';font-weight:700;white-space:nowrap;';
	return '<span style="' . $style . '">[' . hhb_tohtml($meta['label']) . ']</span>';
}

function market_render_corruption_badge($attributesBlob) {
	$corruptionId = market_extract_corruption_from_attributes($attributesBlob);
	$meta = market_get_corruption_meta($corruptionId);
	if ($meta === null) {
		return '<span class="text-[var(--text-muted)]">None</span>';
	}

	$style = 'display:inline-flex;align-items:center;padding:3px 8px;border-radius:999px;border:1px solid ' . $meta['color'] . ';color:' . $meta['color'] . ';background:' . $meta['background'] . ';font-weight:700;white-space:nowrap;';
	return '<span style="' . $style . '">[' . hhb_tohtml($meta['label']) . ']</span>';
}

function market_panel_start($title) {
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header "><div class="PageHeader-module__HZea0q__headerContent"><h2 class="PageHeader-module__HZea0q__title"><span class="flex items-center gap-2">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-store w-4 h-4" aria-hidden="true"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"></path><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"></path><path d="M2 7h20"></path><path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"></path></svg>
			<?php echo hhb_tohtml($title); ?>
		</span></h2></div></div>
		<div class="PageBody-module__OAJIWq__body "><div class="PageBody-module__OAJIWq__content"><div class="space-y-3">
	<?php
}
function market_panel_end() { ?></div></div></div></div><br><?php }

function market_render_offers($offers, $items, $server, $imageType, $showCompare = true) {
	$button_class = 'inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] hover:opacity-90 px-3 py-1 text-[11px] cursor-pointer';
	?>
	<div style="overflow-x:auto;">
		<table class="w-full text-xs text-[var(--text-muted)] border border-[var(--box-inner-border)]" style="border-collapse: collapse;">
			<thead><tr class="bg-[var(--box-inner-bg)]">
				<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Item name</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Item</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Rarity</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Corruption</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Count</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Price for 1</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Added</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">By</th><?php if ($showCompare): ?><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Compare</th><?php endif; ?>
			</tr></thead>
			<tbody>
			<?php foreach (($offers ? $offers : array()) as $o): ?>
				<tr class="hover:bg-[var(--box-inner-bg)]">
					<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo hhb_tohtml(isset($items[$o['item_id']]) ? $items[$o['item_id']] : $o['item_id']); ?></td>
					<td class="px-2 py-1 border border-[var(--box-inner-border)]"><img src="<?php echo 'http://' . $server . '/' . (int)$o['item_id'] . '.' . $imageType; ?>" alt="Item Image" style="width:32px;height:32px;image-rendering:pixelated;"></td>
					<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo market_render_rarity_badge(isset($o['attributes']) ? $o['attributes'] : ''); ?></td>
					<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo market_render_corruption_badge(isset($o['attributes']) ? $o['attributes'] : ''); ?></td>
					<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo (int)$o['amount']; ?></td>
					<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo number_format($o['price'], 0, '', ' '); ?></td>
					<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo hhb_tohtml(getClock($o['created'], true, true)); ?></td>
					<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo ($o['anonymous'] == 1) ? 'Anonymous' : '<a class="text-[var(--text-link)]" href="characterprofile.php?name=' . urlencode($o['player_name']) . '">' . hhb_tohtml($o['player_name']) . '</a>'; ?></td>
					<?php if ($showCompare): ?><td class="px-2 py-1 border border-[var(--box-inner-border)]"><a href="?compare=<?php echo (int)$o['item_id']; ?>"><button class="<?php echo $button_class; ?>">Compare</button></a></td><?php endif; ?>
				</tr>
			<?php endforeach; ?>
			<?php if (!$offers): ?><tr><td class="px-2 py-3 text-center border border-[var(--box-inner-border)]" colspan="<?php echo $showCompare ? 9 : 8; ?>">No offers found.</td></tr><?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

$server = $config['shop']['imageServer'];
$imageType = $config['shop']['imageType'];
$items = getItemList();
$compare = isset($_GET['compare']) ? $_GET['compare'] : false;
$button_class = 'inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] hover:opacity-90 px-3 py-1 text-[11px] cursor-pointer';
$input_class = 'px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none';

if (is_array($items) === false) {
	market_panel_start('Marketplace');
	?><div class="text-xs text-red-600">Failed to load item list. Attempted: <?php echo hhb_tohtml($items); ?></div><?php
	market_panel_end();
	include 'layout/overall/footer.php';
	die();
}

if (!$compare) {
	$offers = array(
		'wts' => mysql_select_multi("SELECT `mo`.`id`, `mo`.`itemtype` AS `item_id`, `mo`.`attributes`, `mo`.`amount`, `mo`.`price`, `mo`.`created`, `mo`.`anonymous`, `p`.`name` AS `player_name` FROM `market_offers` AS `mo` INNER JOIN `players` AS `p` ON `mo`.`player_id`=`p`.`id` WHERE `mo`.`sale` = '1' ORDER BY `mo`.`created` DESC;"),
		'wtb' => mysql_select_multi("SELECT `mo`.`id`, `mo`.`itemtype` AS `item_id`, `mo`.`attributes`, `mo`.`amount`, `mo`.`price`, `mo`.`created`, `mo`.`anonymous`, `p`.`name` AS `player_name` FROM `market_offers` AS `mo` INNER JOIN `players` AS `p` ON `mo`.`player_id`=`p`.`id` WHERE `mo`.`sale` = '0' ORDER BY `mo`.`created` DESC;")
	);

	market_panel_start('Marketplace'); ?>
	<p class="text-xs text-[var(--text-muted)]">You can buy and sell items by using the market in depot.</p>
	<form action="" class="flex flex-col sm:flex-row gap-2 items-start sm:items-center">
		<label class="text-xs text-[var(--text-muted)]" for="compareSearch">Item search:</label>
		<input class="<?php echo $input_class; ?>" type="text" id="compareSearch" name="compare">
		<button class="<?php echo $button_class; ?>" type="submit">Search</button>
	</form>
	<?php market_panel_end();

	market_panel_start('WTS: Want to Sell');
	market_render_offers($offers['wts'], $items, $server, $imageType, true);
	market_panel_end();

	market_panel_start('WTB: Want to Buy');
	market_render_offers($offers['wtb'], $items, $server, $imageType, true);
	market_panel_end();
} else {
	$compare = ((int)$compare > 0) ? (int)$compare : getValue($compare);
	$condition = "`itemtype`='$compare'";
	if (is_string($compare)) {
		$query = array();
		foreach ($items as $id => $name) if (strpos(strtolower($name), stripslashes(strtolower($compare))) !== false) $query[] = $id;
		$condition = (!empty($query)) ? "`itemtype` IN (". implode(',', $query) .")" : false;
	}
	if ($condition === false) {
		$offers = array();
		$historyOffers = array();
	} else {
		$offers = mysql_select_multi("SELECT `mo`.`id`, `mo`.`sale`, `mo`.`itemtype` AS `item_id`, `mo`.`attributes`, `mo`.`amount`, `mo`.`price`, `mo`.`created`, `mo`.`anonymous`, `p`.`name` AS `player_name` FROM `market_offers` AS `mo` INNER JOIN `players` AS `p` ON `mo`.`player_id`=`p`.`id` WHERE `mo`.$condition ORDER BY `mo`.`price` ASC;");
		$historyOffers = mysql_select_multi("SELECT `id`, `itemtype` AS `item_id`, `amount`, `price`, `inserted`, `expires_at` FROM `market_history` WHERE $condition AND `state`='255' ORDER BY `price` ASC;");
	}
	$buylist = false;
	$selllist = array();
	foreach (($offers ? $offers : array()) as $o) {
		if ($o['sale'] == 0) { if ($buylist === false) $buylist = array(); $buylist[] = $o; }
		else $selllist[] = $o;
	}
	$itemname = (isset($items[$compare])) ? $items[$compare] : $compare;
	market_panel_start(is_string($compare) ? 'Search: ' . stripslashes($compare) : 'Comparing Item: ' . $itemname); ?>
	<a href="market.php"><button class="<?php echo $button_class; ?>">Go Back</button></a>
	<?php market_panel_end();
	market_panel_start('Active Offers');
	market_render_offers($selllist, $items, $server, $imageType, false);
	market_panel_end();
	market_panel_start('Want to Buy');
	market_render_offers($buylist, $items, $server, $imageType, false);
	market_panel_end();
	market_panel_start('Old Purchased Offers'); ?>
	<div style="overflow-x:auto;"><table class="w-full text-xs text-[var(--text-muted)] border border-[var(--box-inner-border)]" style="border-collapse: collapse;"><thead><tr class="bg-[var(--box-inner-bg)]"><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Item name</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Item</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Count</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Price for 1</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Offer sold</th></tr></thead><tbody>
	<?php foreach (($historyOffers ? $historyOffers : array()) as $o): ?>
		<tr><td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo hhb_tohtml(isset($items[$o['item_id']]) ? $items[$o['item_id']] : $o['item_id']); ?></td><td class="px-2 py-1 border border-[var(--box-inner-border)]"><img src="<?php echo 'http://' . $server . '/' . (int)$o['item_id'] . '.' . $imageType; ?>" alt="Item Image" style="width:32px;height:32px;image-rendering:pixelated;"></td><td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo (int)$o['amount']; ?></td><td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo number_format($o['price'], 0, '', ' '); ?></td><td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo hhb_tohtml(getClock($o['inserted'], true, true)); ?></td></tr>
	<?php endforeach; ?>
	<?php if (!$historyOffers): ?><tr><td class="px-2 py-3 text-center border border-[var(--box-inner-border)]" colspan="5">No history found.</td></tr><?php endif; ?>
	</tbody></table></div>
	<?php market_panel_end();
}
include 'layout/overall/footer.php';
?>
