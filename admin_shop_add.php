<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';

function admin_shop_add_panel_start($title) {
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package-plus w-4 h-4" aria-hidden="true">
							<path d="M16 16h6"></path>
							<path d="M19 13v6"></path>
							<path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"></path>
							<path d="m7.5 4.27 9 5.15"></path>
							<polyline points="3.29 7 12 12 20.71 7"></polyline>
							<line x1="12" x2="12" y1="22" y2="12"></line>
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

function admin_shop_add_panel_end() {
	?></div></div></div></div><br><?php
}

function admin_shop_add_notice($message, $type = 'success') {
	$text_class = ($type === 'error') ? 'text-red-600' : 'text-[#22c55e]';
	?>
	<div class="p-3 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-xs <?php echo $text_class; ?>">
		<?php echo hhb_tohtml($message); ?>
	</div>
	<?php
}

$button_class = 'inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer';
$input_class = 'w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none';
$select_class = 'w-full px-2 py-1 text-xs bg-[var(--box-inner-bg)] text-[var(--text-muted)] border border-[var(--box-inner-border)] focus:outline-none';
$messages = array();

if (!user_logged_in()) {
	admin_shop_add_panel_start('Admin Shop Manager');
	admin_shop_add_notice('You must be logged in.', 'error');
	admin_shop_add_panel_end();
	include 'layout/overall/footer.php';
	exit;
}

$accountName = $user_data['name'];
if (!in_array(strtolower($accountName), array_map('strtolower', $config['page_admin_access']))) {
	admin_shop_add_panel_start('Admin Shop Manager');
	admin_shop_add_notice('Access denied.', 'error');
	admin_shop_add_panel_end();
	include 'layout/overall/footer.php';
	exit;
}

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'];

function encodeOutfitPair($male, $female) { return ((int)$male) * 10000 + (int)$female; }
function decodeOutfitPair($packed) {
	$male = (int) floor($packed / 10000);
	$female = (int) ($packed % 10000);
	return array($male, $female);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['del']) && hash_equals($csrf, $_POST['csrf'])) {
	$id = (int)$_POST['del'];
	mysql_delete("DELETE FROM `znote_shop_offers` WHERE `id`=$id LIMIT 1;");
	$messages[] = array('Offer #' . $id . ' removed.', 'success');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add']) && hash_equals($csrf, $_POST['csrf'])) {
	$type = (int)$_POST['type'];
	$itemRaw = trim($_POST['itemid']);
	$count = max(0, (int)$_POST['count']);
	$points = max(1, (int)$_POST['points']);
	$desc = trim(substr($_POST['description'], 0, 255));
	$time = time();
	$adminId = (int)$user_data['id'];

	if ($type <= 0 || $desc === '' || $points <= 0) {
		$messages[] = array('Missing/invalid fields.', 'error');
	} else {
		if ($type == 5) {
			if (!preg_match('/^\s*(\d+)\s*,\s*(\d+)\s*$/', $itemRaw, $m)) {
				$messages[] = array('For outfit, use "maleId,femaleId".', 'error');
			} else {
				$itemid = encodeOutfitPair($m[1], $m[2]);
				mysql_insert("INSERT INTO `znote_shop_offers` (`type`,`itemid`,`count`,`description`,`points`,`created_by`,`created_at`) VALUES ($type,$itemid,$count,'". sanitize($desc) ."',$points,$adminId,$time)");
				$messages[] = array('Outfit offer added.', 'success');
			}
		} else {
			if (!preg_match('/^\d+$/', $itemRaw)) {
				$messages[] = array('Item ID must be a number.', 'error');
			} else {
				$itemid = (int)$itemRaw;
				mysql_insert("INSERT INTO `znote_shop_offers` (`type`,`itemid`,`count`,`description`,`points`,`created_by`,`created_at`) VALUES ($type,$itemid,$count,'". sanitize($desc) ."',$points,$adminId,$time)");
				$messages[] = array('Offer added.', 'success');
			}
		}
	}
}

$offers = mysql_select_multi("SELECT * FROM `znote_shop_offers` ORDER BY `id` ASC;");

admin_shop_add_panel_start('Admin Shop Manager');
foreach ($messages as $message) admin_shop_add_notice($message[0], $message[1]);
?>
<form method="post" class="space-y-3">
	<input type="hidden" name="csrf" value="<?php echo hhb_tohtml($csrf); ?>">
	<input type="hidden" name="add" value="1">

	<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
		<label class="text-xs text-[var(--text-muted)]">Type
			<select name="type" class="<?php echo $select_class; ?> mt-1">
				<option value="1">1 - Item</option>
				<option value="2">2 - Premium Days</option>
				<option value="3">3 - Change Sex</option>
				<option value="4">4 - Change Name</option>
				<option value="5">5 - Outfit (male,female)</option>
				<option value="6">6 - Mount</option>
				<option value="8">8 - Custom</option>
			</select>
		</label>
		<label class="text-xs text-[var(--text-muted)]">Item ID <span class="text-[10px]">(for outfit: maleId,femaleId)</span>
			<input name="itemid" class="<?php echo $input_class; ?> mt-1" placeholder="e.g. 1219,1218 or 1030">
		</label>
		<label class="text-xs text-[var(--text-muted)]">Count / Addon / Days
			<input name="count" type="number" min="0" class="<?php echo $input_class; ?> mt-1" value="1">
		</label>
		<label class="text-xs text-[var(--text-muted)]">Points
			<input name="points" type="number" min="1" class="<?php echo $input_class; ?> mt-1" value="10">
		</label>
		<label class="text-xs text-[var(--text-muted)] sm:col-span-2">Description
			<input name="description" class="<?php echo $input_class; ?> mt-1" placeholder="Short text shown in shop">
		</label>
	</div>

	<div class="flex justify-end pt-2 border-t border-[var(--box-inner-border)]">
		<button class="<?php echo $button_class; ?>">Add Offer</button>
	</div>
</form>
<?php
admin_shop_add_panel_end();

admin_shop_add_panel_start('Offers');
if ($offers === false): ?>
	<div class="text-xs text-[var(--text-muted)] text-center py-4">No offers yet.</div>
<?php else: ?>
	<div class="gap-2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
		<?php foreach ($offers as $o): ?>
			<div class="py-2 px-3 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-xs text-[var(--text-muted)]" style="min-height: 150px; display: flex; flex-direction: column; gap: 6px;">
				<div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
					<div><b>#<?php echo (int)$o['id']; ?></b> <span class="text-[10px]">Type <?php echo (int)$o['type']; ?></span></div>
					<div class="font-semibold"><?php echo (int)$o['points']; ?> pts</div>
				</div>
				<div class="text-center" style="min-height: 54px;">
					<?php
					if ((int)$o['type'] === 5) {
						list($male, $female) = decodeOutfitPair((int)$o['itemid']);
						echo '<img src="' . $config['show_outfits']['imageServer'] . '?id=' . $male . '&addons=' . (int)$o['count'] . '&head=' . rand(1,132) . '&body=' . rand(1,132) . '&legs=' . rand(1,132) . '&feet=' . rand(1,132) . '&direction=2" style="height:48px; display:inline-block;">';
						echo '<img src="' . $config['show_outfits']['imageServer'] . '?id=' . $female . '&addons=' . (int)$o['count'] . '&head=' . rand(1,132) . '&body=' . rand(1,132) . '&legs=' . rand(1,132) . '&feet=' . rand(1,132) . '&direction=2" style="height:48px; display:inline-block;">';
					} else if ((int)$o['type'] === 6) {
						$outfitsIds = array(136,137,138,139,140,141,142,147,148,149,150,155,156,157,158,252,269,270,279,288,324,336,366,431,433,464,466,471,513,514,542,128,129,130,131,132,133,134,143,144,145,146,151,152,153,154,251,268,273,278,289,325,335,367,430,432,463,465,472,512,516,541);
						$oid = $outfitsIds[array_rand($outfitsIds)];
						echo '<img src="' . $config['show_outfits']['imageServer'] . '?id=' . $oid . '&addons=' . rand(1,3) . '&head=' . rand(1,132) . '&body=' . rand(1,132) . '&legs=' . rand(1,132) . '&feet=' . rand(1,132) . '&mount=' . (int)$o['itemid'] . '&direction=2" style="height:48px; display:inline-block;">';
					} else if (!empty($config['shop']['showImage'])) {
						echo '<img src="/' . $config['shop']['imageServer'] . '/' . (int)$o['itemid'] . '.' . $config['shop']['imageType'] . '" style="height:32px; display:inline-block;">';
					}
					?>
				</div>
				<div style="line-height: 1.35;">
					<div><b>Item:</b>
						<?php
						if ((int)$o['type'] === 5) {
							list($male, $female) = decodeOutfitPair((int)$o['itemid']);
							echo hhb_tohtml($male . ',' . $female);
						} else {
							echo (int)$o['itemid'];
						}
						?>
					</div>
					<div><b>Count:</b> <?php echo (int)$o['count']; ?></div>
					<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo hhb_tohtml($o['description']); ?>"><?php echo hhb_tohtml($o['description']); ?></div>
				</div>
				<div>
					<form method="post" onsubmit="return confirm('Remove this offer?');">
						<input type="hidden" name="csrf" value="<?php echo hhb_tohtml($csrf); ?>">
						<button class="<?php echo $button_class; ?> w-full" name="del" value="<?php echo (int)$o['id']; ?>">Remove</button>
					</form>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif;
admin_shop_add_panel_end();
include 'layout/overall/footer.php';
?>
