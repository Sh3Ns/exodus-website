<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';

if ($config['log_ip']) znote_visitor_insert_detailed_data(3);

function houses_panel_start($title) {
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header "><div class="PageHeader-module__HZea0q__headerContent"><h2 class="PageHeader-module__HZea0q__title"><span class="flex items-center gap-2">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house w-4 h-4" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10.5 12 3l9 7.5"></path><path d="M5 10v11h14V10"></path></svg>
			<?php echo hhb_tohtml($title); ?>
		</span></h2></div></div>
		<div class="PageBody-module__OAJIWq__body "><div class="PageBody-module__OAJIWq__content"><div class="space-y-3">
	<?php
}
function houses_panel_end() { ?></div></div></div></div><br><?php }

$towns = $config['towns'];
$order_allowed = array('id', 'name', 'size', 'beds', 'rent', 'owner');
$type_allowed = array('desc', 'asc');

$townid = (isset($_GET['id']) && (int)$_GET['id'] > 0) ? (int)$_GET['id'] : $config['houseConfig']['HouseListDefaultTown'];
$order = (isset($_GET['order']) && in_array($_GET['order'], $order_allowed)) ? $_GET['order'] : 'id';
$type = (isset($_GET['type']) && in_array($_GET['type'], $type_allowed)) ? $_GET['type'] : 'desc';

$button_class = 'inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:opacity-90 px-3 py-1 text-[11px] cursor-pointer';
$select_class = 'w-full px-2 py-1 text-xs bg-[var(--box-inner-bg)] text-[var(--text-muted)] border border-[var(--box-inner-border)] focus:outline-none';

houses_panel_start('House Search');
?>
<form action="" method="get" class="space-y-3">
	<div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
		<label class="text-xs text-[var(--text-muted)]">Town
			<select name="id" class="<?php echo $select_class; ?> mt-1">
				<?php foreach ($towns as $id => $name): ?>
					<option value="<?php echo (int)$id; ?>" <?php if ($townid == $id) echo 'selected'; ?>><?php echo hhb_tohtml($name); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<label class="text-xs text-[var(--text-muted)]">Order
			<select name="order" class="<?php echo $select_class; ?> mt-1">
				<?php foreach($order_allowed as $o): ?>
					<option value="<?php echo hhb_tohtml($o); ?>" <?php if ($o == $order) echo 'selected'; ?>><?php echo hhb_tohtml(ucfirst($o)); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<label class="text-xs text-[var(--text-muted)]">Sort
			<select name="type" class="<?php echo $select_class; ?> mt-1">
				<?php foreach($type_allowed as $t): ?>
					<option value="<?php echo hhb_tohtml($t); ?>" <?php if ($t == $type) echo 'selected'; ?>><?php echo ($t == 'desc') ? 'Descending' : 'Ascending'; ?></option>
				<?php endforeach; ?>
			</select>
		</label>
	</div>
	<div class="flex justify-end"><button class="<?php echo $button_class; ?>" type="submit">Fetch Houses</button></div>
</form>
<?php houses_panel_end();

$cache = new Cache('engine/cache/houses/houses-' . $order . '-' . $type);
$houses = array();
if ($cache->hasExpired()) {
	$houses = mysql_select_multi("SELECT `id`, `owner`, `paid`, `warnings`, `name`, `rent`, `town_id`, `size`, `beds`, `bid`, `bid_end`, `last_bid`, `highest_bidder` FROM `houses` ORDER BY {$order} {$type};");
	if ($houses !== false) {
		$playerlist = array();
		foreach ($houses as $h) if ($h['owner'] > 0) $playerlist[] = $h['owner'];
		if (!empty($playerlist)) {
			$ids = join(',', $playerlist);
			$tmpPlayers = mysql_select_multi("SELECT `id`, `name` FROM players WHERE `id` IN ($ids);");
			$tmpById = array();
			if ($tmpPlayers !== false) foreach ($tmpPlayers as $p) $tmpById[$p['id']] = $p['name'];
			for ($i = 0; $i < count($houses); $i++) if ($houses[$i]['owner'] > 0 && isset($tmpById[$houses[$i]['owner']])) $houses[$i]['ownername'] = $tmpById[$houses[$i]['owner']];
		}
		$cache->setContent($houses);
		$cache->save();
	}
} else $houses = $cache->load();

houses_panel_start('Houses');
if ($houses !== false && !empty($houses)): ?>
	<div style="overflow-x:auto;">
		<table class="w-full text-xs text-[var(--text-muted)] border border-[var(--box-inner-border)]" style="border-collapse: collapse;">
			<thead><tr class="bg-[var(--box-inner-bg)]">
				<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Name</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Size</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Beds</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Rent</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Owner</th><th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Town</th>
			</tr></thead>
			<tbody>
			<?php foreach ($houses as $house): if ($house['town_id'] == $townid): ?>
				<tr class="hover:bg-[var(--box-inner-bg)]">
					<td class="px-2 py-1 border border-[var(--box-inner-border)]"><a class="text-[var(--text-link)]" href="house.php?id=<?php echo (int)$house['id']; ?>"><?php echo hhb_tohtml($house['name']); ?></a></td>
					<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo (int)$house['size']; ?></td>
					<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo (int)$house['beds']; ?></td>
					<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo (int)$house['rent']; ?></td>
					<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo ($house['owner'] != 0 && isset($house['ownername'])) ? '<a class="text-[var(--text-link)]" href="characterprofile.php?name=' . urlencode($house['ownername']) . '">' . hhb_tohtml($house['ownername']) . '</a>' : (($house['highest_bidder'] == 0) ? 'None' : '<b>Selling</b>'); ?></td>
					<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo hhb_tohtml(isset($towns[$house['town_id']]) ? $towns[$house['town_id']] : 'Town ' . $house['town_id']); ?></td>
				</tr>
			<?php endif; endforeach; ?>
			</tbody>
		</table>
	</div>
<?php else: ?>
	<div class="text-xs text-[var(--text-muted)] text-center py-4">Failed to fetch data from houses table.</div>
<?php endif;
houses_panel_end();
include 'layout/overall/footer.php';
?>
