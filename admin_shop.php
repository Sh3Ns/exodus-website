<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';
protect_page();
admin_only($user_data);

function admin_shop_panel_start($title) {
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag w-4 h-4" aria-hidden="true">
							<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
							<path d="M3 6h18"></path>
							<path d="M16 10a4 4 0 0 1-8 0"></path>
						</svg>
						<?php echo hhb_tohtml($title); ?>
					</span>
				</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="space-y-3">
	<?php
}

function admin_shop_panel_end() {
	?></div></div></div></div><br><?php
}

$items = getItemList();

$orders = mysql_select_multi('SELECT * FROM `znote_shop_orders` ORDER BY `id` DESC;');
$order_types = array(1 => 'Item', 2 => 'Premium Days', 3 => 'Gender Change', 4 => 'Name Change', 5 => 'Outfits', 6 => 'Mounts');

admin_shop_panel_start('Pending Orders');
?>
<p class="text-xs text-[var(--text-muted)]">These are pending orders, like items bought, but not received or used yet.</p>
<?php if ($orders !== false): ?>
	<div style="overflow-x: auto;">
		<table class="w-full text-xs text-[var(--text-muted)] border border-[var(--box-inner-border)]" style="border-collapse: collapse;">
			<thead>
				<tr class="bg-[var(--box-inner-bg)]">
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Id</th>
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Account</th>
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Type</th>
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Item</th>
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Count</th>
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Date</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($orders as $order): ?>
					<tr class="hover:bg-[var(--box-inner-bg)]">
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo (int)$order['id']; ?></td>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo hhb_tohtml(user_account_id_from_name($order['account_id'])); ?></td>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo hhb_tohtml(isset($order_types[$order['type']]) ? $order_types[$order['type']] : $order['type']); ?></td>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo '(' . (int)$order['itemid'] . ') ' . hhb_tohtml(isset($items[$order['itemid']]) ? $items[$order['itemid']] : ''); ?></td>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo (int)$order['count']; ?></td>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo hhb_tohtml(date('Y/m/d H:i', $order['time'])); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php else: ?>
	<div class="text-xs text-[var(--text-muted)] text-center py-4">No pending shop orders.</div>
<?php endif; ?>
<?php
admin_shop_panel_end();

$orders = mysql_select_multi('SELECT * FROM `znote_shop_logs` ORDER BY `id` DESC;');
$order_types = array(1 => 'Item', 2 => 'Premium Days', 3 => 'Gender Change', 4 => 'Name Change', 5 => 'Outfit', 6 => 'Mount', 7 => 'Custom');

admin_shop_panel_start('Order History');
?>
<p class="text-xs text-[var(--text-muted)]">This list contains all transactions bought in the shop.</p>
<?php if ($orders !== false): ?>
	<div style="overflow-x: auto;">
		<table class="w-full text-xs text-[var(--text-muted)] border border-[var(--box-inner-border)]" style="border-collapse: collapse;">
			<thead>
				<tr class="bg-[var(--box-inner-bg)]">
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Id</th>
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Account</th>
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Type</th>
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Item</th>
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Count</th>
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Points</th>
					<th class="text-left px-2 py-1 border border-[var(--box-inner-border)]">Date</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($orders as $order): ?>
					<tr class="hover:bg-[var(--box-inner-bg)]">
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo (int)$order['id']; ?></td>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo hhb_tohtml($order['account_id']); ?></td>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo hhb_tohtml(isset($order_types[$order['type']]) ? $order_types[$order['type']] : $order['type']); ?></td>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo '(' . (int)$order['itemid'] . ') ' . hhb_tohtml(isset($items[$order['itemid']]) ? $items[$order['itemid']] : ''); ?></td>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo (int)$order['count']; ?></td>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo (int)$order['points']; ?></td>
						<td class="px-2 py-1 border border-[var(--box-inner-border)]"><?php echo hhb_tohtml(getClock($order['time'], true, false)); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php else: ?>
	<div class="text-xs text-[var(--text-muted)] text-center py-4">No shop order history.</div>
<?php endif; ?>
<?php
admin_shop_panel_end();
include 'layout/overall/footer.php';
?>
