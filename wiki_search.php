<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>
<?php
function sm_h($value) {
	return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function sm_slugify($name) {
	$slug = strtolower(trim((string)$name));
	$slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
	return trim((string)$slug, '-');
}

function sm_outfit_direction() {
	return 3;
}

function sm_is_true_flag($value) {
	$value = strtolower(trim((string)$value));
	return in_array($value, array('1', 'true', 'yes'), true);
}

function sm_cache_path() {
	return __DIR__ . '/engine/cache/monster_search.cache.php';
}

function sm_items_map() {
	static $items = null;
	if ($items !== null) {
		return $items;
	}

	$items = array('by_id' => array(), 'by_name' => array());
	$file = Config('server_path') . '/data/items/items.xml';
	if (!file_exists($file)) {
		return $items;
	}

	$xml = @simplexml_load_file($file);
	if ($xml === false) {
		return $items;
	}

	foreach ($xml->children() as $item) {
		$name = trim((string)$item['name']);
		if ($name === '') {
			continue;
		}

		if (isset($item['id'])) {
			$id = (int)$item['id'];
			$items['by_id'][$id] = $name;
			$items['by_name'][strtolower($name)] = $id;
			continue;
		}

		if (isset($item['fromid']) && isset($item['toid'])) {
			$fromId = (int)$item['fromid'];
			$toId = (int)$item['toid'];
			for ($id = $fromId; $id <= $toId; $id++) {
				if (!isset($items['by_id'][$id])) {
					$items['by_id'][$id] = $name;
				}
			}
			if (!isset($items['by_name'][strtolower($name)])) {
				$items['by_name'][strtolower($name)] = $fromId;
			}
		}
	}

	return $items;
}

function sm_parse_item_attributes($itemNode) {
	$attributes = array();

	foreach ($itemNode->attribute as $attr) {
		$key = trim((string)$attr['key']);
		$value = trim((string)$attr['value']);

		if ($key === '' || $value === '') {
			continue;
		}

		$attributes[$key] = $value;
	}

	return $attributes;
}

function sm_is_searchable_item($name, $attributes) {
	$name = strtolower(trim((string)$name));

	$blockedTypes = array(
		'teleport',
		'magicfield',
		'bed',
		'door',
		'depot',
		'mailbox',
		'trashholder',
	);

	if (isset($attributes['type']) && in_array(strtolower($attributes['type']), $blockedTypes, true)) {
		return false;
	}

	$blockedNameParts = array(
		'tile',
		'wall',
		'floor',
		'border',
		'stairs',
		'ladder',
		'roof',
		'railing',
		'fence',
		'window',
		'closed door',
		'open door',
		'depot locker',
		'dead',
	);

	foreach ($blockedNameParts as $blocked) {
		if (strpos($name, $blocked) !== false) {
			return false;
		}
	}

	$goodKeys = array(
		'weight',
		'weaponType',
		'slotType',
		'armor',
		'defense',
		'extraDefense',
		'attack',
		'extraAttack',
		'containerSize',
		'ammoType',
		'shootType',
		'range',
		'charges',
		'showcharges',
		'runeSpellName',
		'transformEquipTo',
		'transformDeEquipTo',
		'elementDamage',
		'elementType',
		'absorbPercentPhysical',
		'absorbPercentFire',
		'absorbPercentEnergy',
		'absorbPercentEarth',
		'absorbPercentIce',
		'absorbPercentDeath',
		'absorbPercentHoly',
		'absorbPercentWater',
		'absorbPercentArcane',
	);

	foreach ($goodKeys as $key) {
		if (isset($attributes[$key])) {
			return true;
		}
	}

	return false;
}

function sm_load_items_catalog() {
	static $catalog = null;
	if ($catalog !== null) {
		return $catalog;
	}

	$catalog = array();
	$file = Config('server_path') . '/data/items/items.xml';

	if (!file_exists($file)) {
		return $catalog;
	}

	$xml = @simplexml_load_file($file);
	if ($xml === false) {
		return $catalog;
	}

	foreach ($xml->children() as $item) {
		$name = trim((string)$item['name']);
		if ($name === '') {
			continue;
		}

		$ids = array();

		if (isset($item['id'])) {
			$ids[] = (int)$item['id'];
		} elseif (isset($item['fromid']) && isset($item['toid'])) {
			$fromId = (int)$item['fromid'];
			$toId = (int)$item['toid'];

			for ($id = $fromId; $id <= $toId; $id++) {
				$ids[] = $id;
			}
		}

		if (empty($ids)) {
			continue;
		}

		$attributes = sm_parse_item_attributes($item);

			if (!sm_is_searchable_item($name, $attributes)) {
				continue;
			}

			foreach ($ids as $id) {
			$slug = sm_slugify($name . '-' . $id);

			$catalog[$slug] = array(
				'id' => $id,
				'name' => $name,
				'description' => isset($attributes['description']) ? $attributes['description'] : '',
				'article' => isset($item['article']) ? (string)$item['article'] : '',
				'attributes' => $attributes,
				'slug' => $slug,
			);
		}
	}

	uasort($catalog, function ($left, $right) {
		return strcasecmp($left['name'], $right['name']);
	});

	return $catalog;
}

function sm_item_name_by_id($itemId) {
	$itemsMap = sm_items_map();
	$itemId = (int)$itemId;

	return isset($itemsMap['by_id'][$itemId]) ? $itemsMap['by_id'][$itemId] : 'Unknown item';
}

function sm_find_item_slug_by_id($itemId, $itemCatalog) {
	$itemId = (int)$itemId;

	foreach ($itemCatalog as $slug => $item) {
		if ((int)$item['id'] === $itemId) {
			return $slug;
		}
	}

	return '';
}

function sm_item_detail_url($itemId, $itemCatalog) {
	$slug = sm_find_item_slug_by_id($itemId, $itemCatalog);

	if ($slug === '') {
		return '';
	}

	return 'wiki_search.php?' . http_build_query(array(
		'view' => 'items',
		'item' => $slug,
	));
}

function sm_item_stats_display($attributes) {
	$stats = array();

	$labels = array(
		// Basic combat
		'attack' => array('Attack', '+%s'),
		'defense' => array('Defense', '+%s'),
		'extradef' => array('Extra Defense', '+%s'),
		'extradefense' => array('Extra Defense', '+%s'),
		'armor' => array('Armor', '+%s'),
		'range' => array('Range', '%s'),
		'hitchance' => array('Hit Chance', '+%s%%'),
		'maxhitchance' => array('Max Hit Chance', '+%s%%'),
		'attackspeed' => array('Attack Speed', '%s'),

		// Weapon / slot info
		'weapontype' => array('Weapon Type', '%s'),
		'slottype' => array('Slot Type', '%s'),
		'ammotype' => array('Ammo Type', '%s'),
		'shoottype' => array('Shoot Type', '%s'),

		// Core stats
		'maxhitpoints' => array('Max Health', '+%s'),
		'maxhitpointspercent' => array('Max Health', '+%s%%'),
		'maxmanapoints' => array('Max Mana', '+%s'),
		'maxmanapointspercent' => array('Max Mana', '+%s%%'),
		'magicpoints' => array('Magic Level', '+%s'),
		'magiclevelpoints' => array('Magic Level', '+%s'),
		'magicpointspercent' => array('Magic Level', '+%s%%'),

		// Combat skills
		'skillfist' => array('Fist Fighting', '+%s'),
		'skillclub' => array('Club Fighting', '+%s'),
		'skillsword' => array('Sword Fighting', '+%s'),
		'skillaxe' => array('Axe Fighting', '+%s'),
		'skilldist' => array('Distance Fighting', '+%s'),
		'skillshield' => array('Shielding', '+%s'),
		'skillfish' => array('Fishing', '+%s'),

		// Custom skills
		'skillcrafting' => array('Crafting', '+%s'),
		'skillwoodcutting' => array('Woodcutting', '+%s'),
		'skillmining' => array('Mining', '+%s'),
		'skillherbalist' => array('Herbalist', '+%s'),
		'skillarmorsmith' => array('Armorsmith', '+%s'),
		'skillweaponsmith' => array('Weaponsmith', '+%s'),
		'skilljewelsmith' => array('Jewelsmith', '+%s'),

		// Special skills
		'criticalhitchance' => array('Critical Hit Chance', '+%s%%'),
		'criticalhitamount' => array('Critical Hit Damage', '+%s%%'),
		'lifeleechchance' => array('Life Leech Chance', '+%s%%'),
		'lifeleechamount' => array('Life Leech Amount', '+%s%%'),
		'manaleechchance' => array('Mana Leech Chance', '+%s%%'),
		'manaleechamount' => array('Mana Leech Amount', '+%s%%'),

		// Absorbs / protections
		'absorbpercentphysical' => array('Physical Protection', '+%s%%'),
		'absorbpercentenergy' => array('Energy Protection', '+%s%%'),
		'absorbpercentfire' => array('Fire Protection', '+%s%%'),
		'absorbpercentearth' => array('Earth Protection', '+%s%%'),
		'absorbpercentpoison' => array('Earth Protection', '+%s%%'),
		'absorbpercentice' => array('Ice Protection', '+%s%%'),
		'absorbpercentholy' => array('Holy Protection', '+%s%%'),
		'absorbpercentdeath' => array('Death Protection', '+%s%%'),
		'absorbpercentwater' => array('Water Protection', '+%s%%'),
		'absorbpercentarcane' => array('Arcane Protection', '+%s%%'),
		'absorbpercentdrown' => array('Drown Protection', '+%s%%'),
		'absorbpercentlifedrain' => array('Life Drain Protection', '+%s%%'),
		'absorbpercentmanadrain' => array('Mana Drain Protection', '+%s%%'),
		'absorbpercentall' => array('All Protection', '+%s%%'),
		'absorbpercentallelements' => array('All Elements Protection', '+%s%%'),
		'absorbpercentelements' => array('Elements Protection', '+%s%%'),
		'absorbpercentmagic' => array('Magic Protection', '+%s%%'),

		// Elemental attack
		'elementice' => array('Ice Damage', '+%s'),
		'elementearth' => array('Earth Damage', '+%s'),
		'elementfire' => array('Fire Damage', '+%s'),
		'elementenergy' => array('Energy Damage', '+%s'),
		'elementdeath' => array('Death Damage', '+%s'),
		'elementwater' => array('Water Damage', '+%s'),
		'elementarcane' => array('Arcane Damage', '+%s'),
		'elementholy' => array('Holy Damage', '+%s'),

		// Utility
		'speed' => array('Speed', '+%s'),
		'healthgain' => array('Health Gain', '+%s'),
		'healthticks' => array('Health Ticks', '%s'),
		'managain' => array('Mana Gain', '+%s'),
		'manaticks' => array('Mana Ticks', '%s'),
		'charges' => array('Charges', '%s'),
		'duration' => array('Duration', '%s'),
		'containersize' => array('Container Size', '%s'),
		'rarity' => array('Rarity', '%s'),
	);

	foreach ($attributes as $key => $value) {
		$keyLower = strtolower(trim((string)$key));

		if (!isset($labels[$keyLower])) {
			continue;
		}

		$label = $labels[$keyLower][0];
		$format = $labels[$keyLower][1];

		$stats[] = array(
			'label' => $label,
			'value' => sprintf($format, $value),
			'key' => $keyLower,
		);
	}

	return $stats;
}

function sm_render_clickable_item_line($itemId, $itemName, $count, $itemCatalog, $extraStyle = '') {
	$itemId = (int)$itemId;
	$count = (int)$count;

	$itemImage = 'items/' . $itemId . '.png';
	$itemImagePath = __DIR__ . '/' . $itemImage;
	$itemUrl = sm_item_detail_url($itemId, $itemCatalog);

	ob_start();
	?>
	<span style="display:inline-flex;align-items:center;gap:4px;margin:3px 8px 3px 0;<?php echo $extraStyle; ?>">
		<?php if (file_exists($itemImagePath)) { ?>
			<img src="<?php echo sm_h($itemImage); ?>" width="24" height="24" alt="<?php echo sm_h($itemName); ?>">
		<?php } ?>

		<?php echo $count; ?>x <?php echo sm_h($itemName); ?>
	</span>
	<?php
	$content = ob_get_clean();

	if ($itemUrl !== '') {
		return '<a href="' . sm_h($itemUrl) . '" style="text-decoration:none;color:inherit;">' . $content . '</a>';
	}

	return $content;
}

function sm_find_item_drops($targetItem, $monsterCatalog) {
	$drops = array();

	$targetId = (int)$targetItem['id'];
	$targetName = strtolower(trim($targetItem['name']));

	foreach ($monsterCatalog as $slug => $monster) {
		if (empty($monster['loot'])) {
			continue;
		}

		foreach ($monster['loot'] as $lootItem) {
			$lootId = isset($lootItem['id']) ? (int)$lootItem['id'] : 0;
			$lootName = strtolower(trim((string)$lootItem['name']));

			$matchById = $targetId > 0 && $lootId === $targetId;
			$matchByName = $targetName !== '' && $lootName === $targetName;

			if (!$matchById && !$matchByName) {
				continue;
			}

			$drops[] = array(
				'monster_slug' => $slug,
				'monster_name' => $monster['name'],
				'is_boss' => !empty($monster['is_reward_boss']),
				'chance' => isset($lootItem['chance']) ? (int)$lootItem['chance'] : 0,
				'countmax' => isset($lootItem['countmax']) ? (int)$lootItem['countmax'] : 1,
				'look' => isset($monster['look']) ? $monster['look'] : array(),
			);
		}
	}

	usort($drops, function ($left, $right) {
		return $right['chance'] <=> $left['chance'];
	});

	return $drops;
}

function sm_find_monster_slug_by_name($monsterName, $monsterCatalog) {
	$monsterName = strtolower(trim((string)$monsterName));

	foreach ($monsterCatalog as $slug => $monster) {
		if (strtolower(trim((string)$monster['name'])) === $monsterName) {
			return $slug;
		}
	}

	return '';
}

function sm_parse_enhanced_loot() {
	static $enhancedLoot = null;
	if ($enhancedLoot !== null) {
		return $enhancedLoot;
	}

	$enhancedLoot = array();

	$file = rtrim(Config('server_path'), '/\\') . '/data/scripts/customs/enhanced_loot/enhanced_loot.lua';
	if (!file_exists($file)) {
		return $enhancedLoot;
	}

	$content = @file_get_contents($file);
	if ($content === false || trim($content) === '') {
		return $enhancedLoot;
	}

	// Capture chaque bloc ["monster name"] = { ... } jusqu'au prochain monstre ou fin de config.
	if (preg_match_all('/\["([^"]+)"\]\s*=\s*\{(.*?)(?=\n\s*\["[^"]+"\]\s*=\s*\{|\n\s*\}\s*,?\s*\n\s*local\s+creatureevent)/s', $content, $monsterMatches, PREG_SET_ORDER)) {
		foreach ($monsterMatches as $monsterMatch) {
			$monsterName = strtolower(trim($monsterMatch[1]));
			$monsterBlock = $monsterMatch[2];

			$baseChance = 10000;
			if (preg_match('/chance\s*=\s*(\d+)/', $monsterBlock, $chanceMatch)) {
				$baseChance = (int)$chanceMatch[1];
			}

			// Capture les tiers : tier = {"Rare"...} ... itemList = {{...}}
			if (preg_match_all('/tier\s*=\s*\{\s*"([^"]+)".*?itemList\s*=\s*\{(.*?)\}\s*,\s*bag\s*=/s', $monsterBlock, $tierMatches, PREG_SET_ORDER)) {
				foreach ($tierMatches as $tierMatch) {
					$tierName = trim($tierMatch[1]);
					$itemList = $tierMatch[2];

					if (preg_match_all('/\{\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\}/', $itemList, $itemMatches, PREG_SET_ORDER)) {
						foreach ($itemMatches as $itemMatch) {
							$itemId = (int)$itemMatch[1];

							$enhancedLoot[] = array(
								'monster_name' => $monsterName,
								'item_id' => $itemId,
								'min_count' => (int)$itemMatch[2],
								'max_count' => (int)$itemMatch[3],
								'item_chance' => (int)$itemMatch[4],
								'base_chance' => $baseChance,
								'tier' => $tierName,
							);
						}
					}
				}
			}
		}
	}

	return $enhancedLoot;
}

function sm_find_item_enhanced_drops($targetItem, $monsterCatalog) {
	$targetId = (int)$targetItem['id'];
	$enhancedLoot = sm_parse_enhanced_loot();
	$drops = array();

	foreach ($enhancedLoot as $entry) {
		if ((int)$entry['item_id'] !== $targetId) {
			continue;
		}

		$slug = sm_find_monster_slug_by_name($entry['monster_name'], $monsterCatalog);
		$monsterName = $entry['monster_name'];
		$isBoss = false;
		$look = array();

		if ($slug !== '' && isset($monsterCatalog[$slug])) {
			$monsterName = $monsterCatalog[$slug]['name'];
			$isBoss = !empty($monsterCatalog[$slug]['is_reward_boss']);
			$look = isset($monsterCatalog[$slug]['look']) ? $monsterCatalog[$slug]['look'] : array();
		}

		$drops[] = array(
			'monster_slug' => $slug,
			'monster_name' => $monsterName,
			'is_boss' => $isBoss,
			'look' => $look,
			'tier' => $entry['tier'],
			'base_chance' => (int)$entry['base_chance'],
			'item_chance' => (int)$entry['item_chance'],
			'min_count' => (int)$entry['min_count'],
			'max_count' => (int)$entry['max_count'],
		);
	}

	usort($drops, function ($left, $right) {
		return $right['item_chance'] <=> $left['item_chance'];
	});

	return $drops;
}

function sm_global_special_loot_info($itemId) {
	$itemId = (int)$itemId;

	$globalLoot = array(
		28343 => array(
			'name' => 'Global monster drop',
			'chance_text' => '3%',
			'count_text' => '1-5',
			'note' => 'Can drop from any monster or boss.',
		),
		28344 => array(
			'name' => 'Global monster drop',
			'chance_text' => '3%',
			'count_text' => '1-3',
			'note' => 'Can drop from any monster or boss.',
		),
		29845 => array(
			'name' => 'Global waystone drop',
			'chance_text' => '3% global roll, then 50 weight',
			'count_text' => '1',
			'note' => 'Can drop from any monster or boss.',
		),
		29846 => array(
			'name' => 'Global waystone drop',
			'chance_text' => '3% global roll, then 40 weight',
			'count_text' => '1',
			'note' => 'Can drop from any monster or boss.',
		),
		29847 => array(
			'name' => 'Global waystone drop',
			'chance_text' => '3% global roll, then 30 weight',
			'count_text' => '1',
			'note' => 'Can drop from any monster or boss.',
		),
		29848 => array(
			'name' => 'Global waystone drop',
			'chance_text' => '3% global roll, then 20 weight',
			'count_text' => '1',
			'note' => 'Can drop from any monster or boss.',
		),
		29849 => array(
			'name' => 'Global waystone drop',
			'chance_text' => '3% global roll, then 10 weight',
			'count_text' => '1',
			'note' => 'Can drop from any monster or boss.',
		),
		29850 => array(
			'name' => 'Global waystone drop',
			'chance_text' => '3% global roll, then 5 weight',
			'count_text' => '1',
			'note' => 'Can drop from any monster or boss.',
		),
	);

	return isset($globalLoot[$itemId]) ? $globalLoot[$itemId] : null;
}

function sm_find_item_id($lootItem, $itemsMap) {
	if (isset($lootItem['id'])) {
		return (int)$lootItem['id'];
	}

	$name = trim((string)$lootItem['name']);
	if ($name === '') {
		return 0;
	}

	$key = strtolower($name);
	return isset($itemsMap['by_name'][$key]) ? (int)$itemsMap['by_name'][$key] : 0;
}

function sm_chance_percent($chance) {
	$chance = (float)$chance;
	if ($chance <= 0) {
		return '0%';
	}

	$percent = $chance / 1000;
	return $percent >= 1 ? number_format($percent, 1) . '%' : number_format($percent, 2) . '%';
}

function sm_lua_combat_type_label($type) {
	$type = trim((string)$type);

	$map = array(
		'COMBAT_PHYSICALDAMAGE' => 'Physical',
		'COMBAT_ENERGYDAMAGE' => 'Energy',
		'COMBAT_EARTHDAMAGE' => 'Earth',
		'COMBAT_FIREDAMAGE' => 'Fire',
		'COMBAT_LIFEDRAIN' => 'Life Drain',
		'COMBAT_MANADRAIN' => 'Mana Drain',
		'COMBAT_DROWNDAMAGE' => 'Drown',
		'COMBAT_ICEDAMAGE' => 'Ice',
		'COMBAT_HOLYDAMAGE' => 'Holy',
		'COMBAT_DEATHDAMAGE' => 'Death',
		'COMBAT_WATERDAMAGE' => 'Water',
		'COMBAT_ARCANEDAMAGE' => 'Arcane',
	);

	return isset($map[$type]) ? $map[$type] : $type;
}

function sm_xml_element_label($key) {
	$key = trim((string)$key);

	$map = array(
		'physicalPercent' => 'Physical',
		'energyPercent' => 'Energy',
		'earthPercent' => 'Earth',
		'firePercent' => 'Fire',
		'lifedrainPercent' => 'Life Drain',
		'manadrainPercent' => 'Mana Drain',
		'drownPercent' => 'Drown',
		'icePercent' => 'Ice',
		'holyPercent' => 'Holy',
		'deathPercent' => 'Death',
		'waterPercent' => 'Water',
		'arcanePercent' => 'Arcane',
	);

	return isset($map[$key]) ? $map[$key] : preg_replace('/Percent$/', '', $key);
}

function sm_resistance_type($percent) {
	$percent = (int)$percent;

	if ($percent >= 100) {
		return 'Immune';
	}

	if ($percent > 0) {
		return 'Resistant';
	}

	if ($percent < 0) {
		return 'Weak';
	}

	return 'Normal';
}

function sm_parse_monster_detail($relativeFile, $itemsMap) {
	$file = rtrim(Config('server_path'), '/\\') . '/data/monster/' . ltrim($relativeFile, '/\\');
	if (!file_exists($file)) {
		return null;
	}

	$xml = @simplexml_load_file($file);
	if ($xml === false) {
		return null;
	}

	$look = isset($xml->look) ? $xml->look : null;
	$health = isset($xml->health) ? $xml->health : null;
	$defenses = isset($xml->defenses) ? $xml->defenses : null;

	$flags = array();
	if (isset($xml->flags)) {
		foreach ($xml->flags->children() as $flag) {
			foreach ($flag->attributes() as $key => $value) {
				$flags[(string)$key] = (string)$value;
			}
		}
	}

	$voices = array();
	if (isset($xml->voices)) {
		foreach ($xml->voices->children() as $voice) {
			$sentence = trim((string)$voice['sentence']);
			if ($sentence !== '') {
				$voices[] = $sentence;
			}
		}
	}

	$loot = array();
	if (isset($xml->loot)) {
		foreach ($xml->loot->children() as $lootItem) {
			$itemId = sm_find_item_id($lootItem, $itemsMap);
			$itemName = trim((string)$lootItem['name']);
			if ($itemName === '' && $itemId > 0 && isset($itemsMap['by_id'][$itemId])) {
				$itemName = $itemsMap['by_id'][$itemId];
			}
			if ($itemName === '') {
				$itemName = 'Unknown item';
			}

			$loot[] = array(
				'id' => $itemId,
				'name' => $itemName,
				'chance' => isset($lootItem['chance']) ? (int)$lootItem['chance'] : 0,
				'countmax' => isset($lootItem['countmax']) ? max(1, (int)$lootItem['countmax']) : 1,
			);
		}
	}	

	$elements = array();
	if (isset($xml->elements)) {
		foreach ($xml->elements->children() as $element) {
			foreach ($element->attributes() as $key => $value) {
				$key = (string)$key;
				if (substr($key, -7) !== 'Percent') {
					continue;
				}

				$percent = (int)$value;
				$elements[] = array(
					'name' => sm_xml_element_label($key),
					'percent' => $percent,
					'type' => sm_resistance_type($percent),
				);
			}
		}
	}

	usort($loot, function ($left, $right) {
		return $right['chance'] <=> $left['chance'];
	});

	return array(
		'name' => (string)$xml['name'],
		'description' => (string)$xml['nameDescription'],
		'experience' => (int)$xml['experience'],
		'speed' => (int)$xml['speed'],
		'mana_cost' => (int)$xml['manacost'],
		'race' => (string)$xml['race'],
		'health' => array(
			'now' => $health ? (int)$health['now'] : 0,
			'max' => $health ? (int)$health['max'] : 0,
		),
		'look' => array(
			'type' => $look ? (int)$look['type'] : 0,
			'head' => $look ? (int)$look['head'] : 0,
			'body' => $look ? (int)$look['body'] : 0,
			'legs' => $look ? (int)$look['legs'] : 0,
			'feet' => $look ? (int)$look['feet'] : 0,
			'corpse' => $look ? (int)$look['corpse'] : 0,
		),
		'defenses' => array(
			'armor' => $defenses ? (int)$defenses['armor'] : 0,
			'defense' => $defenses ? (int)$defenses['defense'] : 0,
		),
		'elements' => $elements,
		'is_reward_boss' => isset($flags['isrewardboss']) ? sm_is_true_flag($flags['isrewardboss']) : false,
		'flags' => $flags,
		'voices' => $voices,
		'loot' => $loot,
		'file' => $relativeFile,
		'updated_at' => @filemtime($file) ?: time(),
	);
}

function sm_parse_lua_value($content, $pattern, $default = '') {
	if (preg_match($pattern, $content, $matches)) {
		return $matches[1];
	}
	return $default;
}

function sm_parse_lua_section($content, $sectionName) {
	if (!preg_match('/monster\.' . preg_quote($sectionName, '/') . '\s*=\s*\{(.*?)\n\}/s', $content, $matches)) {
		return '';
	}
	return $matches[1];
}

function sm_parse_lua_loot_items($section, $itemsMap) {
	$loot = array();
	if ($section === '') {
		return $loot;
	}

	if (preg_match_all('/\{[^{}]*id\s*=\s*("([^"]+)"|(\d+))[^{}]*chance\s*=\s*(\d+)(?:[^{}]*maxCount\s*=\s*(\d+))?[^{}]*\}/s', $section, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			$itemName = isset($match[2]) && $match[2] !== '' ? trim($match[2]) : '';
			$itemId = isset($match[3]) && $match[3] !== '' ? (int)$match[3] : 0;
			if ($itemId === 0 && $itemName !== '') {
				$key = strtolower($itemName);
				if (isset($itemsMap['by_name'][$key])) {
					$itemId = (int)$itemsMap['by_name'][$key];
				}
			}
			if ($itemName === '' && $itemId > 0 && isset($itemsMap['by_id'][$itemId])) {
				$itemName = $itemsMap['by_id'][$itemId];
			}
			if ($itemName === '') {
				$itemName = 'Unknown item';
			}

			$loot[] = array(
				'id' => $itemId,
				'name' => $itemName,
				'chance' => (int)$match[4],
				'countmax' => isset($match[5]) && $match[5] !== '' ? max(1, (int)$match[5]) : 1,
			);
		}
	}

	usort($loot, function ($left, $right) {
		return $right['chance'] <=> $left['chance'];
	});

	return $loot;
}

function sm_parse_lua_elements($section) {
	$elements = array();

	if ($section === '') {
		return $elements;
	}

	if (preg_match_all('/\{[^{}]*type\s*=\s*([A-Z0-9_]+)[^{}]*percent\s*=\s*(-?\d+)[^{}]*\}/s', $section, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			$percent = (int)$match[2];

			$elements[] = array(
				'name' => sm_lua_combat_type_label($match[1]),
				'percent' => $percent,
				'type' => sm_resistance_type($percent),
			);
		}
	}

	return $elements;
}

function sm_parse_lua_monster($file, $itemsMap) {
	$content = @file_get_contents($file);
	if ($content === false || trim($content) === '') {
		return null;
	}

	$name = sm_parse_lua_value($content, '/Game\.createMonsterType\("([^"]+)"\)/', '');
	if ($name === '') {
		$name = sm_parse_lua_value($content, '/monster\.name\s*=\s*"([^"]+)"/', basename($file, '.lua'));
	}

	$flagsSection = sm_parse_lua_section($content, 'flags');
	$flags = array();
	if ($flagsSection !== '' && preg_match_all('/([a-zA-Z0-9_]+)\s*=\s*([^,\n]+)/', $flagsSection, $flagMatches, PREG_SET_ORDER)) {
		foreach ($flagMatches as $flagMatch) {
			$flags[$flagMatch[1]] = trim(trim($flagMatch[2]), '"');
		}
	}

	$voicesSection = sm_parse_lua_section($content, 'voices');
	$voices = array();
	if ($voicesSection !== '' && preg_match_all('/text\s*=\s*"([^"]+)"/', $voicesSection, $voiceMatches)) {
		$voices = $voiceMatches[1];
	}

	$defenseSection = sm_parse_lua_section($content, 'defenses');
	$lootSection = sm_parse_lua_section($content, 'loot');
	$elementsSection = sm_parse_lua_section($content, 'elements');
	$healthNow = (int)sm_parse_lua_value($content, '/monster\.health\s*=\s*(\d+)/', 0);
	$healthMax = (int)sm_parse_lua_value($content, '/monster\.maxHealth\s*=\s*(\d+)/', 0);
	if ($healthMax <= 0) {
		$healthMax = $healthNow;
	}

	return array(
		'name' => $name,
		'description' => sm_parse_lua_value($content, '/monster\.description\s*=\s*"([^"]*)"/', ''),
		'experience' => (int)sm_parse_lua_value($content, '/monster\.experience\s*=\s*(\d+)/', 0),
		'speed' => (int)sm_parse_lua_value($content, '/monster\.speed\s*=\s*(\d+)/', 0),
		'mana_cost' => (int)sm_parse_lua_value($content, '/monster\.manaCost\s*=\s*(\d+)/', 0),
		'race' => sm_parse_lua_value($content, '/monster\.race\s*=\s*"([^"]*)"/', ''),
		'health' => array('now' => $healthNow, 'max' => $healthMax),
		'look' => array(
			'type' => (int)sm_parse_lua_value($content, '/lookType\s*=\s*(\d+)/', 0),
			'head' => (int)sm_parse_lua_value($content, '/lookHead\s*=\s*(\d+)/', 0),
			'body' => (int)sm_parse_lua_value($content, '/lookBody\s*=\s*(\d+)/', 0),
			'legs' => (int)sm_parse_lua_value($content, '/lookLegs\s*=\s*(\d+)/', 0),
			'feet' => (int)sm_parse_lua_value($content, '/lookFeet\s*=\s*(\d+)/', 0),
			'corpse' => (int)sm_parse_lua_value($content, '/monster\.corpse\s*=\s*(\d+)/', 0),
		),
		'defenses' => array(
			'armor' => (int)sm_parse_lua_value($defenseSection, '/armor\s*=\s*(\d+)/', 0),
			'defense' => (int)sm_parse_lua_value($defenseSection, '/defense\s*=\s*(\d+)/', 0),
		),
		'elements' => sm_parse_lua_elements($elementsSection),
		'is_reward_boss' => isset($flags['isRewardBoss']) ? sm_is_true_flag($flags['isRewardBoss']) : false,
		'flags' => $flags,
		'voices' => $voices,
		'loot' => sm_parse_lua_loot_items($lootSection, $itemsMap),
		'file' => str_replace(rtrim(Config('server_path'), '/\\') . '/data/monster/', '', str_replace('\\', '/', $file)),
		'updated_at' => @filemtime($file) ?: time(),
	);
}

function sm_load_catalog() {
	$itemsMap = sm_items_map();
	$indexFile = rtrim(Config('server_path'), '/\\') . '/data/monster/monsters.xml';
	$luaDir = rtrim(Config('server_path'), '/\\') . '/data/monster/lua';
	$cacheFile = sm_cache_path();

	$cacheKey = array(
		'xml' => file_exists($indexFile) ? (int)filemtime($indexFile) : 0,
		'lua' => is_dir($luaDir) ? (int)filemtime($luaDir) : 0,
	);

	if (file_exists($cacheFile)) {
		$cached = @include $cacheFile;
		if (is_array($cached) && isset($cached['cache_key'], $cached['built_at'], $cached['monsters']) && $cached['cache_key'] === $cacheKey && (time() - (int)$cached['built_at']) < 300) {
			return $cached['monsters'];
		}
	}

	$monsters = array();
	if (file_exists($indexFile)) {
		$indexXml = @simplexml_load_file($indexFile);
		if ($indexXml !== false) {
			foreach ($indexXml->children() as $monsterNode) {
				$name = trim((string)$monsterNode['name']);
				$relativeFile = trim((string)$monsterNode['file']);
				if ($name === '' || $relativeFile === '') {
					continue;
				}

				$detail = sm_parse_monster_detail($relativeFile, $itemsMap);
				if ($detail === null) {
					continue;
				}

				$slug = sm_slugify($name);
				if (isset($monsters[$slug])) {
					$slug .= '-' . count($monsters);
				}

				$monsters[$slug] = $detail;
				$monsters[$slug]['slug'] = $slug;
			}
		}
	}

	if (is_dir($luaDir)) {
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($luaDir, FilesystemIterator::SKIP_DOTS));
		foreach ($iterator as $luaFile) {
			if (!$luaFile->isFile() || strtolower($luaFile->getExtension()) !== 'lua') {
				continue;
			}

			$detail = sm_parse_lua_monster($luaFile->getPathname(), $itemsMap);
			if ($detail === null || trim($detail['name']) === '') {
				continue;
			}

			$slug = sm_slugify($detail['name']);
			if (isset($monsters[$slug])) {
				$slug .= '-lua-' . count($monsters);
			}

			$monsters[$slug] = $detail;
			$monsters[$slug]['slug'] = $slug;
		}
	}

	uasort($monsters, function ($left, $right) {
		return strcasecmp($left['name'], $right['name']);
	});

	$cachePayload = "<?php\nreturn " . var_export(array(
		'cache_key' => $cacheKey,
		'built_at' => time(),
		'monsters' => $monsters,
	), true) . ";\n";
	@file_put_contents($cacheFile, $cachePayload, LOCK_EX);

	return $monsters;
}

function sm_crafting_files() {
	return array(
		'armorsmith' => 'armorsmith.lua',
		'generalcrafting' => 'generalcrafting.lua',
		'herbalist' => 'herbalist.lua',
		'jewelsmith' => 'jewelsmith.lua',
		'mining' => 'mining.lua',
		'weaponsmith' => 'weaponsmith.lua',
		'woodcutting' => 'woodcutting.lua',
	);
}

function sm_parse_crafting_materials($materialsSection) {
	$materials = array();

	if (preg_match_all('/\{\s*id\s*=\s*(\d+)\s*,\s*count\s*=\s*(\d+)\s*\}/s', $materialsSection, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			$itemId = (int)$match[1];

			$materials[] = array(
				'id' => $itemId,
				'name' => sm_item_name_by_id($itemId),
				'count' => (int)$match[2],
			);
		}
	}

	return $materials;
}

function sm_load_crafting_catalog() {
	static $recipes = null;
	if ($recipes !== null) {
		return $recipes;
	}

	$recipes = array();
	$baseDir = rtrim(Config('server_path'), '/\\') . '/data/scripts/customs/crafting/';
	$files = sm_crafting_files();

	foreach ($files as $profession => $filename) {
		$file = $baseDir . $filename;

		if (!file_exists($file)) {
			continue;
		}

		$content = @file_get_contents($file);
		if ($content === false || trim($content) === '') {
			continue;
		}

		if (!preg_match_all('/\{\s*id\s*=\s*(\d+)\s*,\s*name\s*=\s*"([^"]+)"\s*,\s*level\s*=\s*(\d+)\s*,\s*cost\s*=\s*(\d+)\s*,\s*count\s*=\s*(\d+).*?materials\s*=\s*\{(.*?)\}\s*\}/s', $content, $matches, PREG_SET_ORDER)) {
			continue;
		}

		foreach ($matches as $match) {
			$itemId = (int)$match[1];

			$recipes[] = array(
				'profession' => $profession,
				'id' => $itemId,
				'name' => trim($match[2]),
				'level' => (int)$match[3],
				'cost' => (int)$match[4],
				'count' => (int)$match[5],
				'materials' => sm_parse_crafting_materials($match[6]),
			);
		}
	}

	return $recipes;
}

function sm_find_item_crafting($targetItem) {
	$recipes = sm_load_crafting_catalog();
	$matches = array();

	$targetId = (int)$targetItem['id'];
	$targetName = strtolower(trim($targetItem['name']));

	foreach ($recipes as $recipe) {
		$matchById = $targetId > 0 && (int)$recipe['id'] === $targetId;
		$matchByName = $targetName !== '' && strtolower(trim($recipe['name'])) === $targetName;

		if ($matchById || $matchByName) {
			$matches[] = $recipe;
		}
	}

	return $matches;
}

function sm_find_item_used_in_crafting($targetItem) {
	$recipes = sm_load_crafting_catalog();
	$matches = array();

	$targetId = (int)$targetItem['id'];
	$targetName = strtolower(trim($targetItem['name']));

	foreach ($recipes as $recipe) {
		if (empty($recipe['materials'])) {
			continue;
		}

		foreach ($recipe['materials'] as $material) {
			$materialId = isset($material['id']) ? (int)$material['id'] : 0;
			$materialName = strtolower(trim((string)$material['name']));

			$matchById = $targetId > 0 && $materialId === $targetId;
			$matchByName = $targetName !== '' && $materialName === $targetName;

			if (!$matchById && !$matchByName) {
				continue;
			}

			$matches[] = array(
				'profession' => $recipe['profession'],
				'id' => (int)$recipe['id'],
				'name' => $recipe['name'],
				'level' => (int)$recipe['level'],
				'cost' => (int)$recipe['cost'],
				'count' => (int)$recipe['count'],
				'material_count' => isset($material['count']) ? (int)$material['count'] : 1,
				'materials' => $recipe['materials'],
			);

			break;
		}
	}

	usort($matches, function ($left, $right) {
		if ($left['profession'] === $right['profession']) {
			return $left['level'] <=> $right['level'];
		}

		return strcmp($left['profession'], $right['profession']);
	});

	return $matches;
}

$monsterCatalog = sm_load_catalog();
$itemCatalog = sm_load_items_catalog();

$query = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
$selectedMonsterSlug = isset($_GET['monster']) ? trim((string)$_GET['monster']) : '';
$selectedItemSlug = isset($_GET['item']) ? trim((string)$_GET['item']) : '';

$hasExplicitMonsterSelection = isset($_GET['monster']) && trim((string)$_GET['monster']) !== '';
$hasExplicitItemSelection = isset($_GET['item']) && trim((string)$_GET['item']) !== '';

$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 24;

$viewMode = 'monsters';
if (isset($_GET['view']) && in_array($_GET['view'], array('monsters', 'bosses', 'items'), true)) {
	$viewMode = $_GET['view'];
}

$filteredMonsters = array();
$filteredItems = array();

if ($viewMode === 'items') {
	foreach ($itemCatalog as $slug => $item) {
		if ($query !== '') {
			$needle = mb_strtolower($query, 'UTF-8');
			$haystack = mb_strtolower($item['name'] . ' ' . $item['description'] . ' ' . implode(' ', $item['attributes']), 'UTF-8');

			if (mb_strpos($haystack, $needle) === false && mb_strpos((string)$item['id'], $query) === false) {
				continue;
			}
		}

		$filteredItems[$slug] = $item;
	}

	$totalResults = count($filteredItems);
} else {
	foreach ($monsterCatalog as $slug => $monster) {
		if ($viewMode === 'bosses' && empty($monster['is_reward_boss'])) {
			continue;
		}
		if ($viewMode === 'monsters' && !empty($monster['is_reward_boss'])) {
			continue;
		}

		if ($query !== '') {
			$needle = mb_strtolower($query, 'UTF-8');
			$haystack = mb_strtolower($monster['name'] . ' ' . $monster['description'] . ' ' . $monster['race'], 'UTF-8');

			if (mb_strpos($haystack, $needle) === false) {
				continue;
			}
		}

		$filteredMonsters[$slug] = $monster;
	}

	$totalResults = count($filteredMonsters);
}

$totalPages = max(1, (int)ceil($totalResults / $perPage));
if ($currentPage > $totalPages) {
	$currentPage = $totalPages;
}

$pageOffset = ($currentPage - 1) * $perPage;

$pagedMonsters = $viewMode !== 'items'
	? array_slice($filteredMonsters, $pageOffset, $perPage, true)
	: array();

$pagedItems = $viewMode === 'items'
	? array_slice($filteredItems, $pageOffset, $perPage, true)
	: array();

if ($selectedMonsterSlug !== '' && !isset($monsterCatalog[$selectedMonsterSlug])) {
	$selectedMonsterSlug = '';
	$hasExplicitMonsterSelection = false;
}

if ($selectedItemSlug !== '' && !isset($itemCatalog[$selectedItemSlug])) {
	$selectedItemSlug = '';
	$hasExplicitItemSelection = false;
}

$selectedMonster = ($hasExplicitMonsterSelection && $selectedMonsterSlug !== '' && isset($monsterCatalog[$selectedMonsterSlug]))
	? $monsterCatalog[$selectedMonsterSlug]
	: null;

$selectedItem = ($hasExplicitItemSelection && $selectedItemSlug !== '' && isset($itemCatalog[$selectedItemSlug]))
	? $itemCatalog[$selectedItemSlug]
	: null;

$isDetailView = $selectedMonster !== null || $selectedItem !== null;
?>

<style>
.monster-search-page { display: flex; flex-direction: column; gap: 16px; }
.monster-page-shell { width: min(1180px, 100%); margin: 0 auto; display: flex; flex-direction: column; gap: 16px; }
.monster-toolbar, .monster-panel, .monster-card {
	background: linear-gradient(180deg, rgba(255,255,255,.05), rgba(0,0,0,.04)), var(--box-background, var(--box-inner-bg));
	border: 1px solid var(--box-border, var(--box-inner-border));
	border-radius: 14px;
	box-shadow: 0 10px 24px rgba(0,0,0,.12);
}
.monster-toolbar { padding: 18px; }
.monster-filter-tabs { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 14px; }
.monster-filter-tab {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	padding: 10px 16px;
	border-radius: 12px;
	border: 1px solid rgba(110, 81, 36, .18);
	background: rgba(255,255,255,.55);
	color: #5a4327;
	text-decoration: none;
	font-size: 13px;
	font-weight: 800;
}
.monster-filter-tab.is-active {
	background: linear-gradient(180deg, #d1ac59 0%, #bb9132 100%);
	color: #fff8e8;
	border-color: #a97f24;
}
.monster-toolbar-grid { display: grid; gap: 12px; grid-template-columns: minmax(0, 1fr) auto; align-items: center; }
.monster-search-input {
	width: 100%;
	padding: 14px 16px;
	border-radius: 12px;
	border: 1px solid var(--box-inner-border);
	background: rgba(255,255,255,.72);
	color: #473521;
	font-size: 15px;
}
.monster-search-input:focus {
	outline: none;
	border-color: #b98a33;
	box-shadow: 0 0 0 3px rgba(185, 138, 51, .18);
}
.monster-search-button {
	padding: 12px 18px;
	border: 1px solid var(--btn-primary-border);
	background: linear-gradient(to bottom, var(--btn-primary-from), var(--btn-primary-to));
	color: var(--btn-primary-text);
	border-radius: 12px;
	font-weight: 700;
}
.monster-resistance-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
	gap: 10px;
}

.monster-resistance-item {
	padding: 12px;
	border-radius: 12px;
	background: rgba(255,255,255,.48);
	border: 1px solid rgba(110, 81, 36, .12);
}

.monster-resistance-name {
	font-size: 13px;
	font-weight: 800;
	color: #533d21;
}

.monster-resistance-meta {
	margin-top: 4px;
	font-size: 12px;
	color: #8d7351;
}
.monster-meta-strip { margin-top: 12px; display: flex; flex-wrap: wrap; gap: 8px; }
.monster-pill {
	padding: 6px 10px;
	border-radius: 999px;
	background: rgba(115, 84, 36, .10);
	border: 1px solid rgba(115, 84, 36, .18);
	font-size: 12px;
	color: #6a5131;
}
.monster-layout { display: flex; flex-direction: column; gap: 16px; }
.monster-grid-panel, .monster-detail-panel { padding: 18px; }
.monster-detail-panel { max-width: 980px; margin: 0 auto; width: 100%; }
.monster-back-row { display: flex; justify-content: flex-start; margin-bottom: 14px; }
.monster-back-link {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	padding: 10px 14px;
	border-radius: 12px;
	border: 1px solid rgba(110, 81, 36, .18);
	background: rgba(255,255,255,.60);
	color: #5a4327;
	text-decoration: none;
	font-size: 13px;
	font-weight: 800;
}
.monster-back-link:hover { border-color: #bb8c34; }
.monster-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
	gap: 12px;
}
.monster-card {
	display: block;
	padding: 10px;
	text-decoration: none;
	color: inherit;
	transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
}
.monster-card:hover { transform: translateY(-2px); border-color: #bd9446; }
.monster-card.is-active {
	border-color: #bb8c34;
	box-shadow: 0 12px 28px rgba(97, 68, 22, .22);
}
.monster-card-figure {
	height: 160px !important;
	min-height: 160px !important;
	display: flex !important;
	align-items: center !important;
	justify-content: center !important;
	margin-bottom: 8px;
	padding: 0 !important;
	background: radial-gradient(circle at top, rgba(237, 214, 175, .35), rgba(142, 108, 57, .08));
	border-radius: 10px;
	overflow: visible !important;
	position: relative;
}
.monster-card-figure img {
	display: block !important;
	width: auto !important;
	height: auto !important;
	max-width: 160px !important;
	max-height: 160px !important;
	object-fit: contain !important;
	object-position: center center !important;
	transform: none !important;
	image-rendering: auto;
}
.monster-card-name {
	font-size: 13px;
	font-weight: 700;
	color: #5a4327;
	line-height: 1.25;
	text-align: center;
	min-height: 32px;
}
.monster-card-sub {
	margin-top: 2px;
	font-size: 11px;
	color: #8b7251;
	text-align: center;
}
.monster-section-title { margin: 0 0 12px; font-size: 18px; color: #5a4327; }
.monster-panel-header {
	display: grid;
	grid-template-columns: 180px minmax(0, 1fr);
	gap: 18px;
	align-items: center;
	margin-bottom: 18px;
}
.monster-avatar {
	min-height: 180px;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 18px;
	border-radius: 14px;
	background: radial-gradient(circle at top, rgba(235, 215, 169, .45), rgba(77, 57, 31, .08));
	border: 1px solid rgba(110, 81, 36, .15);
	overflow: visible;
}
.monster-avatar img {
	max-width: 100%;
	max-height: 160px;
	width: auto;
	height: auto;
}
.monster-title { margin: 0; font-size: 30px; color: #5a4327; }
.monster-subtitle { margin-top: 6px; color: #8d7351; font-size: 14px; }
.monster-stats {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
	gap: 10px;
	margin-bottom: 18px;
}
.monster-stat {
	padding: 12px;
	border-radius: 12px;
	background: rgba(255,255,255,.52);
	border: 1px solid rgba(110, 81, 36, .12);
}
.monster-stat-label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #8d7351; }
.monster-stat-value { display: block; margin-top: 4px; font-size: 18px; font-weight: 800; color: #533d21; }
.monster-section + .monster-section { margin-top: 16px; }
.monster-flags { display: flex; flex-wrap: wrap; gap: 8px; }
.monster-loot-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
	gap: 12px;
}
.monster-loot-item {
	display: flex;
	align-items: center;
	gap: 10px;
	padding: 12px;
	border-radius: 12px;
	background: rgba(255,255,255,.48);
	border: 1px solid rgba(110, 81, 36, .12);
}
.monster-loot-icon {
	width: 40px;
	height: 40px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: rgba(106, 81, 49, .08);
	border-radius: 10px;
	border: 1px solid rgba(106, 81, 49, .12);
	flex-shrink: 0;
}
.monster-loot-name { font-size: 14px; font-weight: 700; color: #533d21; }
.monster-loot-meta { margin-top: 2px; font-size: 13px; color: #8d7351; line-height: 1.45; }
.monster-empty {
	padding: 18px;
	border-radius: 12px;
	background: rgba(255,255,255,.42);
	border: 1px dashed rgba(110, 81, 36, .18);
	color: #7a6244;
}
.monster-pagination { margin-top: 16px; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.monster-page-link, .monster-page-current {
	padding: 8px 12px;
	border-radius: 10px;
	border: 1px solid rgba(110, 81, 36, .16);
	background: rgba(255,255,255,.58);
	color: #5a4327;
	text-decoration: none;
	font-size: 12px;
	font-weight: 700;
}
.monster-page-link:hover { border-color: #bb8c34; }
.monster-page-current {
	background: linear-gradient(180deg, #d1ac59 0%, #bb9132 100%);
	color: #fff8e8;
	border-color: #a97f24;
}
@media (max-width: 640px) {
	.monster-toolbar-grid,
	.monster-panel-header {
		grid-template-columns: 1fr;
	}

	.monster-grid {
		grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
		gap: 10px;
	}

	.monster-card {
		padding: 8px;
	}

	.monster-card-figure {
		height: 110px;
		min-height: 110px;
		padding: 6px;
	}

	.monster-card-figure img {
		width: auto !important;
		height: auto !important;
		max-width: 140px !important;
		max-height: 140px !important;
		transform: none !important;
	}

	.monster-avatar {
		min-height: 140px;
	}

	.monster-avatar img {
		max-height: 120px;
	}

	.monster-loot-grid {
		grid-template-columns: 1fr;
	}
}
</style>

<div class="monster-search-page">
	<div class="monster-page-shell">
		<div class="PageHeader-module__HZea0q__header">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<?php echo $viewMode === 'items' ? 'Item Search' : 'Monster Search'; ?>
				</h2>
			</div>
		</div>

		<div class="monster-toolbar">
			<div class="monster-filter-tabs">
				<a class="monster-filter-tab<?php echo $viewMode === 'monsters' ? ' is-active' : ''; ?>" href="wiki_search.php?<?php echo http_build_query(array_filter(array('view' => 'monsters', 'q' => $query))); ?>">Monsters</a>
				<a class="monster-filter-tab<?php echo $viewMode === 'bosses' ? ' is-active' : ''; ?>" href="wiki_search.php?<?php echo http_build_query(array_filter(array('view' => 'bosses', 'q' => $query))); ?>">Bosses</a>
				<a class="monster-filter-tab<?php echo $viewMode === 'items' ? ' is-active' : ''; ?>" href="wiki_search.php?<?php echo http_build_query(array_filter(array('view' => 'items', 'q' => $query))); ?>">Items</a>
			</div>

			<form action="wiki_search.php" method="get" class="monster-toolbar-grid">
				<input type="hidden" name="view" value="<?php echo sm_h($viewMode); ?>">
				<input class="monster-search-input" type="text" name="q" value="<?php echo sm_h($query); ?>" placeholder="<?php echo $viewMode === 'items' ? 'Search items like a wiki: sword, shield, crystal, 30376...' : 'Search monsters like a wiki: Demon, Dragon, Amazon...'; ?>" autocomplete="off">
				<button class="monster-search-button" type="submit">Search</button>
			</form>

			<div class="monster-meta-strip">
				<span class="monster-pill">
					<?php echo $viewMode === 'items' ? 'Items' : ($viewMode === 'bosses' ? 'Bosses' : 'Monsters'); ?> view
				</span>
				<span class="monster-pill">
					<?php echo $viewMode === 'items' ? count($pagedItems) : count($pagedMonsters); ?> shown on this page
				</span>
				<span class="monster-pill"><?php echo $totalResults; ?> total results</span>
				<span class="monster-pill">Page <?php echo $currentPage; ?> / <?php echo $totalPages; ?></span>
			</div>
		</div>

		<div class="monster-layout">

	<?php if (!$isDetailView) { ?>
		<div class="monster-panel monster-grid-panel">
			<h3 class="monster-section-title">
				<?php
				if ($viewMode === 'items') {
					echo 'Item List';
				} else {
					echo $viewMode === 'bosses' ? 'Boss List' : 'Monster List';
				}
				?>
			</h3>

			<?php if ($viewMode === 'items') { ?>

				<?php if (empty($pagedItems)) { ?>
					<div class="monster-empty">No items matched your search.</div>
				<?php } else { ?>
					<div class="monster-grid">
						<?php foreach ($pagedItems as $slug => $item) {
							$queryArgs = array('item' => $slug, 'page' => $currentPage, 'view' => 'items');

							if ($query !== '') {
								$queryArgs['q'] = $query;
							}

							$itemImage = 'items/' . (int)$item['id'] . '.png';
							$itemImagePath = __DIR__ . '/' . $itemImage;
						?>
							<a class="monster-card" href="wiki_search.php?<?php echo http_build_query($queryArgs); ?>">
								<div class="monster-card-figure">
									<?php if (file_exists($itemImagePath)) { ?>
										<img src="<?php echo sm_h($itemImage); ?>" alt="<?php echo sm_h($item['name']); ?>" loading="lazy">
									<?php } else { ?>
										<span class="monster-pill">No image</span>
									<?php } ?>
								</div>

								<div class="monster-card-name"><?php echo sm_h($item['name']); ?></div>
								<div class="monster-card-sub">ID: <?php echo (int)$item['id']; ?></div>
							</a>
						<?php } ?>
					</div>
				<?php } ?>

			<?php } else { ?>

				<?php if (empty($pagedMonsters)) { ?>
					<div class="monster-empty">No monsters matched your search.</div>
				<?php } else { ?>
					<div class="monster-grid">
						<?php foreach ($pagedMonsters as $slug => $monster) {
							$queryArgs = array('monster' => $slug, 'page' => $currentPage, 'view' => $viewMode);

							if ($query !== '') {
								$queryArgs['q'] = $query;
							}

							$look = $monster['look'];
						?>
							<a class="monster-card" href="wiki_search.php?<?php echo http_build_query($queryArgs); ?>">
								<div class="monster-card-figure">
									<?php if ((int)$look['type'] > 0) { ?>
										<img src="outfits/animoutfit.php?id=<?php echo (int)$look['type']; ?>&addons=0&head=<?php echo (int)$look['head']; ?>&body=<?php echo (int)$look['body']; ?>&legs=<?php echo (int)$look['legs']; ?>&feet=<?php echo (int)$look['feet']; ?>&direction=<?php echo sm_outfit_direction(); ?>&v=3" alt="<?php echo sm_h($monster['name']); ?>" loading="lazy">
									<?php } else { ?>
										<span class="monster-pill">No looktype</span>
									<?php } ?>
								</div>

								<div class="monster-card-name"><?php echo sm_h($monster['name']); ?></div>
								<div class="monster-card-sub">
									<?php echo (int)$monster['experience']; ?> exp<?php echo $monster['race'] !== '' ? ' • ' . sm_h(ucfirst($monster['race'])) : ''; ?>
								</div>
							</a>
						<?php } ?>
					</div>
				<?php } ?>

			<?php } ?>

			<?php if ($totalPages > 1) { ?>
				<div class="monster-pagination">
					<?php
					$baseArgs = array('view' => $viewMode);

					if ($query !== '') {
						$baseArgs['q'] = $query;
					}

					if ($currentPage > 1) {
						$prevArgs = $baseArgs;
						$prevArgs['page'] = $currentPage - 1;
						echo '<a class="monster-page-link" href="wiki_search.php?' . http_build_query($prevArgs) . '">&laquo; Prev</a>';
					}

					$windowStart = max(1, $currentPage - 2);
					$windowEnd = min($totalPages, $currentPage + 2);

					for ($page = $windowStart; $page <= $windowEnd; $page++) {
						if ($page === $currentPage) {
							echo '<span class="monster-page-current">' . $page . '</span>';
							continue;
						}

						$pageArgs = $baseArgs;
						$pageArgs['page'] = $page;
						echo '<a class="monster-page-link" href="wiki_search.php?' . http_build_query($pageArgs) . '">' . $page . '</a>';
					}

					if ($currentPage < $totalPages) {
						$nextArgs = $baseArgs;
						$nextArgs['page'] = $currentPage + 1;
						echo '<a class="monster-page-link" href="wiki_search.php?' . http_build_query($nextArgs) . '">Next &raquo;</a>';
					}
					?>
				</div>
			<?php } ?>
		</div>
	<?php } ?>


	<?php if ($selectedMonster !== null) { ?>
		<div class="monster-panel monster-detail-panel">
			<div class="monster-back-row">
				<?php
				$backArgs = array('view' => $viewMode, 'page' => $currentPage);
				if ($query !== '') {
					$backArgs['q'] = $query;
				}
				?>
				<a class="monster-back-link" href="wiki_search.php?<?php echo http_build_query($backArgs); ?>">&larr; Back</a>
			</div>

			<?php $look = $selectedMonster['look']; ?>

			<div class="monster-panel-header">
				<div class="monster-avatar">
					<?php if ((int)$look['type'] > 0) { ?>
						<img src="outfits/animoutfit.php?id=<?php echo (int)$look['type']; ?>&addons=0&head=<?php echo (int)$look['head']; ?>&body=<?php echo (int)$look['body']; ?>&legs=<?php echo (int)$look['legs']; ?>&feet=<?php echo (int)$look['feet']; ?>&direction=<?php echo sm_outfit_direction(); ?>&v=3" alt="<?php echo sm_h($selectedMonster['name']); ?>" loading="eager">
					<?php } else { ?>
						<span class="monster-pill">No preview</span>
					<?php } ?>
				</div>

				<div>
					<h3 class="monster-title"><?php echo sm_h($selectedMonster['name']); ?></h3>
					<div class="monster-subtitle"><?php echo $selectedMonster['description'] !== '' ? sm_h($selectedMonster['description']) : 'Monster data loaded from server files'; ?></div>
					<div class="monster-meta-strip">
						<?php if (!empty($selectedMonster['is_reward_boss'])) { ?><span class="monster-pill">Boss</span><?php } ?>
						<?php if ($selectedMonster['race'] !== '') { ?><span class="monster-pill"><?php echo sm_h(ucfirst($selectedMonster['race'])); ?></span><?php } ?>
					</div>
				</div>
			</div>

			<div class="monster-stats">
				<div class="monster-stat"><span class="monster-stat-label">Experience</span><span class="monster-stat-value"><?php echo number_format((int)$selectedMonster['experience']); ?></span></div>
				<div class="monster-stat"><span class="monster-stat-label">Health</span><span class="monster-stat-value"><?php echo number_format((int)$selectedMonster['health']['max']); ?></span></div>
				<div class="monster-stat"><span class="monster-stat-label">Speed</span><span class="monster-stat-value"><?php echo number_format((int)$selectedMonster['speed']); ?></span></div>
				<div class="monster-stat"><span class="monster-stat-label">Armor</span><span class="monster-stat-value"><?php echo number_format((int)$selectedMonster['defenses']['armor']); ?></span></div>
				<div class="monster-stat"><span class="monster-stat-label">Defense</span><span class="monster-stat-value"><?php echo number_format((int)$selectedMonster['defenses']['defense']); ?></span></div>
				<div class="monster-stat"><span class="monster-stat-label">Mana Cost</span><span class="monster-stat-value"><?php echo number_format((int)$selectedMonster['mana_cost']); ?></span></div>
			</div>

			<div class="monster-section">
				<h4 class="monster-section-title">Resistances</h4>

				<?php if (empty($selectedMonster['elements'])) { ?>
					<div class="monster-empty">No resistance data found for this monster.</div>
				<?php } else { ?>
					<div class="monster-resistance-grid">
						<?php foreach ($selectedMonster['elements'] as $element) { ?>
							<div class="monster-resistance-item">
								<div class="monster-resistance-name"><?php echo sm_h($element['name']); ?></div>
								<div class="monster-resistance-meta">
									<?php echo (int)$element['percent']; ?>% • <?php echo sm_h($element['type']); ?>
								</div>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>

			<div class="monster-section">
				<h4 class="monster-section-title">Loot</h4>

				<?php if (empty($selectedMonster['loot'])) { ?>
					<div class="monster-empty">No loot entries found for this monster.</div>
				<?php } else { ?>
					<div class="monster-loot-grid">
						<?php foreach ($selectedMonster['loot'] as $lootItem) {
							$itemImage = ((int)$lootItem['id'] > 0) ? 'items/' . (int)$lootItem['id'] . '.png' : '';
							$itemImagePath = ($itemImage !== '') ? __DIR__ . '/' . $itemImage : '';
						?>
							<div class="monster-loot-item">
								<?php $lootItemUrl = sm_item_detail_url((int)$lootItem['id'], $itemCatalog); ?>

								<div class="monster-loot-icon">
									<?php if ($lootItemUrl !== '') { ?>
										<a href="<?php echo sm_h($lootItemUrl); ?>" style="text-decoration:none;color:inherit;">
									<?php } ?>

									<?php if ($itemImage !== '' && file_exists($itemImagePath)) { ?>
										<img src="<?php echo sm_h($itemImage); ?>" alt="<?php echo sm_h($lootItem['name']); ?>" width="32" height="32">
									<?php } else { ?>
										<span class="monster-pill">?</span>
									<?php } ?>

									<?php if ($lootItemUrl !== '') { ?>
										</a>
									<?php } ?>
								</div>

								<div>
									<div class="monster-loot-name">
										<?php if ($lootItemUrl !== '') { ?>
											<a href="<?php echo sm_h($lootItemUrl); ?>" style="text-decoration:none;color:inherit;">
												<?php echo sm_h($lootItem['name']); ?>
											</a>
										<?php } else { ?>
											<?php echo sm_h($lootItem['name']); ?>
										<?php } ?>
									</div>

									<div class="monster-loot-meta">
										Chance: <?php echo sm_h(sm_chance_percent($lootItem['chance'])); ?>
										<?php if ((int)$lootItem['countmax'] > 1) { ?> • Max Count: <?php echo (int)$lootItem['countmax']; ?><?php } ?>
										<?php if ((int)$lootItem['id'] > 0) { ?> • ID: <?php echo (int)$lootItem['id']; ?><?php } ?>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>


	<?php if ($selectedItem !== null) {
		$itemImage = 'items/' . (int)$selectedItem['id'] . '.png';
		$itemImagePath = __DIR__ . '/' . $itemImage;
		$itemStats = sm_item_stats_display($selectedItem['attributes']);
		$itemDrops = sm_find_item_drops($selectedItem, $monsterCatalog);
		$itemEnhancedDrops = sm_find_item_enhanced_drops($selectedItem, $monsterCatalog);
		$itemGlobalLoot = sm_global_special_loot_info((int)$selectedItem['id']);
		$itemCrafting = sm_find_item_crafting($selectedItem);
		$itemUsedInCrafting = sm_find_item_used_in_crafting($selectedItem);
	?>
		<div class="monster-panel monster-detail-panel">
			<div class="monster-back-row">
				<?php
				$backArgs = array('view' => 'items', 'page' => $currentPage);
				if ($query !== '') {
					$backArgs['q'] = $query;
				}
				?>
				<a class="monster-back-link" href="wiki_search.php?<?php echo http_build_query($backArgs); ?>">&larr; Back</a>
			</div>

			<div class="monster-panel-header">
				<div class="monster-avatar">
					<?php if (file_exists($itemImagePath)) { ?>
						<img src="<?php echo sm_h($itemImage); ?>" alt="<?php echo sm_h($selectedItem['name']); ?>" loading="eager">
					<?php } else { ?>
						<span class="monster-pill">No image</span>
					<?php } ?>
				</div>

				<div>
					<h3 class="monster-title"><?php echo sm_h($selectedItem['name']); ?></h3>

					<div class="monster-meta-strip">
						<span class="monster-pill">ID: <?php echo (int)$selectedItem['id']; ?></span>
					</div>
				</div>
			</div>

			<?php if (!empty($itemStats)) { ?>
			<div class="monster-section">
				<h4 class="monster-section-title">Item Stats</h4>

				<div class="monster-resistance-grid">
					<?php foreach ($itemStats as $stat) { ?>
						<div class="monster-resistance-item">
							<div class="monster-resistance-name"><?php echo sm_h($stat['label']); ?></div>
							<div class="monster-resistance-meta"><?php echo sm_h($stat['value']); ?></div>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
		<div class="monster-section">
	<h4 class="monster-section-title">Dropped by</h4>

	<?php if (empty($itemDrops) && empty($itemEnhancedDrops) && $itemGlobalLoot === null) { ?>
		<div class="monster-empty">No monster, boss or enhanced loot found for this item.</div>
	<?php } else { ?>

		<?php if ($itemGlobalLoot !== null) { ?>
			<div class="monster-loot-grid" style="margin-bottom:12px;">
				<div class="monster-loot-item">
					<div class="monster-loot-icon">
						<?php if (file_exists($itemImagePath)) { ?>
							<img src="<?php echo sm_h($itemImage); ?>" alt="<?php echo sm_h($selectedItem['name']); ?>" width="32" height="32">
						<?php } else { ?>
							<span class="monster-pill">?</span>
						<?php } ?>
					</div>

					<div>
						<div class="monster-loot-name"><?php echo sm_h($itemGlobalLoot['name']); ?></div>
						<div class="monster-loot-meta">
							Chance: <?php echo sm_h($itemGlobalLoot['chance_text']); ?>
							• Count: <?php echo sm_h($itemGlobalLoot['count_text']); ?>
							<br>
							<?php echo sm_h($itemGlobalLoot['note']); ?>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if (!empty($itemDrops)) { ?>
			<h4 class="monster-section-title" style="font-size:15px;margin-top:12px;">Normal monster loot</h4>

			<div class="monster-loot-grid">
				<?php foreach ($itemDrops as $drop) {
					$look = $drop['look'];
					$monsterArgs = array(
						'monster' => $drop['monster_slug'],
						'view' => $drop['is_boss'] ? 'bosses' : 'monsters',
					);
				?>
					<a class="monster-loot-item" href="wiki_search.php?<?php echo http_build_query($monsterArgs); ?>" style="text-decoration:none;">
						<div class="monster-loot-icon">
							<?php if (!empty($look['type'])) { ?>
								<img src="outfits/animoutfit.php?id=<?php echo (int)$look['type']; ?>&addons=0&head=<?php echo (int)$look['head']; ?>&body=<?php echo (int)$look['body']; ?>&legs=<?php echo (int)$look['legs']; ?>&feet=<?php echo (int)$look['feet']; ?>&direction=<?php echo sm_outfit_direction(); ?>&v=3" alt="<?php echo sm_h($drop['monster_name']); ?>" width="40" height="40">
							<?php } else { ?>
								<span class="monster-pill">?</span>
							<?php } ?>
						</div>

						<div>
							<div class="monster-loot-name">
								<?php echo sm_h($drop['monster_name']); ?>
								<?php if ($drop['is_boss']) { ?> <span class="monster-pill">Boss</span><?php } ?>
							</div>

							<div class="monster-loot-meta">
								Chance: <?php echo sm_h(sm_chance_percent($drop['chance'])); ?>
								<?php if ((int)$drop['countmax'] > 1) { ?>
									• Max Count: <?php echo (int)$drop['countmax']; ?>
								<?php } ?>
							</div>
						</div>
					</a>
				<?php } ?>
			</div>
		<?php } ?>

			<?php if (!empty($itemEnhancedDrops)) { ?>
				<h4 class="monster-section-title" style="font-size:15px;margin-top:16px;">Enhanced loot</h4>

				<div class="monster-loot-grid">
					<?php foreach ($itemEnhancedDrops as $drop) {
						$look = $drop['look'];

						$monsterArgs = array(
							'monster' => $drop['monster_slug'],
							'view' => $drop['is_boss'] ? 'bosses' : 'monsters',
						);

						$hasMonsterPage = $drop['monster_slug'] !== '';
					?>

						<?php if ($hasMonsterPage) { ?>
							<a class="monster-loot-item" href="wiki_search.php?<?php echo http_build_query($monsterArgs); ?>" style="text-decoration:none;">
						<?php } else { ?>
							<div class="monster-loot-item">
						<?php } ?>

							<div class="monster-loot-icon">
								<?php if (!empty($look['type'])) { ?>
									<img src="outfits/animoutfit.php?id=<?php echo (int)$look['type']; ?>&addons=0&head=<?php echo (int)$look['head']; ?>&body=<?php echo (int)$look['body']; ?>&legs=<?php echo (int)$look['legs']; ?>&feet=<?php echo (int)$look['feet']; ?>&direction=<?php echo sm_outfit_direction(); ?>&v=3" alt="<?php echo sm_h($drop['monster_name']); ?>" width="40" height="40">
								<?php } else { ?>
									<span class="monster-pill">EX</span>
								<?php } ?>
							</div>

							<div>
								<div class="monster-loot-name">
									<?php echo sm_h($drop['monster_name']); ?>
									<span class="monster-pill">Enhanced</span>
									<?php if ($drop['is_boss']) { ?> <span class="monster-pill">Boss</span><?php } ?>
								</div>

								<div class="monster-loot-meta">
									Tier: <?php echo sm_h($drop['tier']); ?>
									<br>
									Enhanced roll: <?php echo sm_h(sm_chance_percent($drop['base_chance'])); ?>
									• Item chance inside tier: <?php echo sm_h(sm_chance_percent($drop['item_chance'])); ?>
									<br>
									Count: <?php echo (int)$drop['min_count']; ?>-<?php echo (int)$drop['max_count']; ?>
								</div>
							</div>

						<?php if ($hasMonsterPage) { ?>
							</a>
						<?php } else { ?>
							</div>
						<?php } ?>

					<?php } ?>
				</div>
			<?php } ?>

		<?php } ?>
	</div>

			<div class="monster-section">
				<h4 class="monster-section-title">Crafting</h4>

				<?php if (empty($itemCrafting)) { ?>
					<div class="monster-empty">No crafting recipe found for this item.</div>
				<?php } else { ?>
					<div class="monster-loot-grid">
						<?php foreach ($itemCrafting as $recipe) { ?>
							<div class="monster-loot-item" style="align-items:flex-start;">
								<div class="monster-loot-icon">
									<?php if (file_exists($itemImagePath)) { ?>
										<img src="<?php echo sm_h($itemImage); ?>" alt="<?php echo sm_h($recipe['name']); ?>" width="32" height="32">
									<?php } else { ?>
										<span class="monster-pill">?</span>
									<?php } ?>
								</div>

								<div>
									<div class="monster-loot-name">
										<?php echo sm_h(ucfirst($recipe['profession'])); ?> recipe
									</div>

									<div class="monster-loot-meta">
										Level: <?php echo (int)$recipe['level']; ?>
										• Cost: <?php echo number_format((int)$recipe['cost']); ?>
										• Count: <?php echo (int)$recipe['count']; ?>
									</div>

									<?php foreach ($recipe['materials'] as $material) { ?>
										<?php echo sm_render_clickable_item_line((int)$material['id'], $material['name'], (int)$material['count'], $itemCatalog); ?>
									<?php } ?>

								</div>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
			<div class="monster-section">
	<h4 class="monster-section-title">Used in crafting</h4>

	<?php if (empty($itemUsedInCrafting)) { ?>
		<div class="monster-empty">This item is not used in any crafting recipe.</div>
	<?php } else { ?>
		<div class="monster-loot-grid">
			<?php foreach ($itemUsedInCrafting as $recipe) {
				$craftedImage = 'items/' . (int)$recipe['id'] . '.png';
				$craftedImagePath = __DIR__ . '/' . $craftedImage;
			?>
				<div class="monster-loot-item" style="align-items:flex-start;">
					<?php $craftedUrl = sm_item_detail_url((int)$recipe['id'], $itemCatalog); ?>

					<div class="monster-loot-icon">
							<?php if ($craftedUrl !== '') { ?>
								<a href="<?php echo sm_h($craftedUrl); ?>" style="text-decoration:none;color:inherit;">
							<?php } ?>

							<?php if (file_exists($craftedImagePath)) { ?>
								<img src="<?php echo sm_h($craftedImage); ?>" alt="<?php echo sm_h($recipe['name']); ?>" width="32" height="32">
							<?php } else { ?>
								<span class="monster-pill">?</span>
							<?php } ?>

							<?php if ($craftedUrl !== '') { ?>
								</a>
							<?php } ?>
						</div>

						<div>
							<div class="monster-loot-name">
								<?php if ($craftedUrl !== '') { ?>
									<a href="<?php echo sm_h($craftedUrl); ?>" style="text-decoration:none;color:inherit;">
										<?php echo sm_h($recipe['name']); ?>
									</a>
								<?php } else { ?>
									<?php echo sm_h($recipe['name']); ?>
								<?php } ?>
							</div>

						<div class="monster-loot-meta">
							<?php echo sm_h(ucfirst($recipe['profession'])); ?> recipe
							<br>
							Level: <?php echo (int)$recipe['level']; ?>
							• Cost: <?php echo number_format((int)$recipe['cost']); ?>
							• Output: <?php echo (int)$recipe['count']; ?>
							<br>
							Uses this item: <?php echo (int)$recipe['material_count']; ?>x
						</div>

						<?php if (!empty($recipe['materials'])) { ?>
							<div class="monster-loot-meta" style="margin-top:8px;">
								<strong>Full recipe:</strong><br>

								<?php foreach ($recipe['materials'] as $material) {
									$isCurrentMaterial = (int)$material['id'] === (int)$selectedItem['id'];
								?>
									<?php
									echo sm_render_clickable_item_line(
										(int)$material['id'],
										$material['name'],
										(int)$material['count'],
										$itemCatalog,
										$isCurrentMaterial ? 'font-weight:800;' : ''
									);
									?>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
</div>
		</div>
	<?php } ?>
</div>
	</div>
</div>

<?php include 'layout/overall/footer.php'; ?>
