<?php
require_once 'engine/init.php';
protect_page();
admin_only($user_data);
include 'layout/overall/header.php';

$statusTypes = array(
	0 => array('label' => 'Reported', 'class' => 'text-purple-700'),
	1 => array('label' => 'To-Do List', 'class' => 'text-blue-800'),
	2 => array('label' => 'Confirmed bug', 'class' => 'text-red-600'),
	3 => array('label' => 'Invalid', 'class' => 'text-gray-600'),
	4 => array('label' => 'Rejected', 'class' => 'text-gray-600'),
	5 => array('label' => 'Fixed', 'class' => 'text-[#22c55e] font-bold'),
);
$statusChangeLog = array(0,5);
$hideStatus = array(3, 4, 5);
$messages = array();

function status_label($statusTypes, $status) {
	$status = (int)$status;
	return isset($statusTypes[$status]) ? '<span class="' . $statusTypes[$status]['class'] . '">' . hhb_tohtml($statusTypes[$status]['label']) . '</span>' : hhb_tohtml($status);
}
function admin_reports_panel_start($title) {
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header "><div class="PageHeader-module__HZea0q__headerContent"><h2 class="PageHeader-module__HZea0q__title"><span class="flex items-center gap-2"><?php echo hhb_tohtml($title); ?></span></h2></div></div>
		<div class="PageBody-module__OAJIWq__body "><div class="PageBody-module__OAJIWq__content"><div class="space-y-3">
	<?php
}
function admin_reports_panel_end() {
	?></div></div></div></div><br><?php
}

$button_class = 'inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer';
$input_class = 'w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none';
$select_class = 'w-full px-2 py-1 text-xs bg-[var(--box-inner-bg)] text-[var(--text-muted)] border border-[var(--box-inner-border)] focus:outline-none';

$reportsData = mysql_select_multi('SELECT id, name, posx, posy, posz, report_description, date, status FROM znote_player_reports ORDER BY id DESC;');
$reports = array();
if ($reportsData !== false) {
	for ($i = 0; $i < count($reportsData); $i++) {
		foreach ($statusTypes as $key => $value) {
			if ($key == $reportsData[$i]['status']) $reports[$key][$reportsData[$i]['id']] = $reportsData[$i];
		}
	}
}

if (!empty($_POST)) {
	$playerName = getValue($_POST['playerName']);
	$status = getValue($_POST['status']);
	$price = getValue($_POST['price']);
	$customPoints = getValue($_POST['customPoints']);
	$reportId = getValue($_POST['id']);
	$changelogReportId = (isset($_POST['changelogReportId']) && (int)$_POST['changelogReportId'] > 0) ? (int)$_POST['changelogReportId'] : false;
	$changelogValue = isset($_POST['changelogValue']) ? $_POST['changelogValue'] : '1';
	$changelogText = isset($_POST['changelogText']) ? getValue($_POST['changelogText']) : false;
	$changelogStatus = ($changelogReportId !== false && $changelogValue === '2' && $changelogText !== false) ? true : false;

	if ($customPoints !== false) $price = (int)($price + $customPoints);
	mysql_update("UPDATE `znote_player_reports` SET `status`='$status' WHERE `id`='$reportId' LIMIT 1;");
	$messages[] = 'Report status updated to ' . (isset($statusTypes[(int)$status]) ? $statusTypes[(int)$status]['label'] : $status) . '.';

	foreach ($reports as $sid => $sa) foreach ($sa as $rid => $ra) if ($reportId == $rid) {
		$reports[$status][$reportId] = $reports[$sid][$rid];
		$reports[$status][$reportId]['status'] = $status;
		unset($reports[$sid][$rid]);
	}

	if ($changelogStatus) {
		$time = time();
		$changelog = mysql_select_single("SELECT * FROM `znote_changelog` WHERE `report_id`='$changelogReportId' LIMIT 1;");
		$updatechangelog = false;
		if ($changelog !== false) {
			mysql_update("UPDATE `znote_changelog` SET `text`='$changelogText', `time`='$time' WHERE `id`='".$changelog['id']."' LIMIT 1;");
			$messages[] = 'Changelog message updated.';
			$updatechangelog = true;
		} else {
			mysql_insert("INSERT INTO `znote_changelog` (`text`, `time`, `report_id`, `status`) VALUES ('$changelogText', '$time', '$changelogReportId', '$status');");
			$messages[] = 'Changelog message created.';
			$updatechangelog = true;
		}
		if ($updatechangelog) {
			$cache = new Cache('engine/cache/changelog');
			$cache->setContent(mysql_select_multi("SELECT `id`, `text`, `time`, `report_id`, `status` FROM `znote_changelog` ORDER BY `id` DESC;"));
			$cache->save();
		}
	}

	if ($price > 0) {
		$account = mysql_select_single("SELECT `a`.`id`, `a`.`email` FROM `accounts` AS `a` INNER JOIN `players` AS `p` ON `p`.`account_id` = `a`.`id` WHERE `p`.`name` = '$playerName' LIMIT 1;");
		if ($account !== false) {
			mysql_insert("INSERT INTO `znote_paypal` VALUES ('', '$reportId', 'report@admin.".$user_data['name']." to ".$account['email']."', '".$account['id']."', '0', '".$price."')");
			$data = mysql_select_single("SELECT `points` AS `old_points` FROM `znote_accounts` WHERE `account_id`='".$account['id']."';");
			$new_points = $data['old_points'] + $price;
			mysql_update("UPDATE `znote_accounts` SET `points`='$new_points' WHERE `account_id`='".$account['id']."'");
			$messages[] = $playerName . ' has been granted ' . $price . ' points for reports.';
		}
	}
}

if (!empty($messages)) {
	admin_reports_panel_start('Report Result');
	foreach ($messages as $message) echo '<div class="text-xs text-[var(--text-muted)]">' . hhb_tohtml($message) . '</div>';
	admin_reports_panel_end();
}

if (!empty($_GET)) {
	$reportId = getValue($_GET['id']);
	$report = false;
	foreach ($reports as $sid => $sa) foreach ($sa as $rid => $ra) if ($rid == $reportId) $report = $reports[$sid][$reportId];
	if ($report !== false) {
		admin_reports_panel_start('Edit Report #' . $report['id']);
		?>
		<form action="admin_reports.php" method="POST" class="space-y-2">
			<div class="text-xs text-[var(--text-muted)]">Player: <a class="text-[var(--text-link)]" target="_blank" href="characterprofile.php?name=<?php echo urlencode($report['name']); ?>"><?php echo hhb_tohtml($report['name']); ?></a></div>
			<input type="hidden" name="playerName" value="<?php echo hhb_tohtml($report['name']); ?>">
			<input type="hidden" name="id" value="<?php echo (int)$report['id']; ?>">
			<select class="<?php echo $select_class; ?>" name="status">
				<?php foreach ($statusTypes as $sid => $sname): ?><option value="<?php echo (int)$sid; ?>" <?php if ($sid == $report['status']) echo 'selected'; ?>><?php echo hhb_tohtml($sname['label']); ?></option><?php endforeach; ?>
			</select>
			<div class="grid grid-cols-1 sm:grid-cols-[1fr_100px] gap-2">
				<select class="<?php echo $select_class; ?>" name="price"><option value="0">0</option><?php foreach ($config['paypal_prices'] as $price): ?><option value="<?php echo (int)$price; ?>"><?php echo (int)$price; ?></option><?php endforeach; ?></select>
				<input class="<?php echo $input_class; ?>" name="customPoints" type="text" placeholder="Custom">
			</div>
			<?php if (in_array($report['status'], $statusChangeLog)): ?>
				<input type="hidden" name="changelogReportId" value="<?php echo (int)$report['id']; ?>">
				<select class="<?php echo $select_class; ?>" name="changelogValue"><option value="1">No changelog</option><option value="2">Add/update changelog</option></select>
				<textarea class="<?php echo $input_class; ?>" rows="7" maxlength="254" name="changelogText" placeholder="Changelog message"></textarea>
			<?php endif; ?>
			<div class="flex justify-end"><button class="<?php echo $button_class; ?>" type="submit">Update Report</button></div>
		</form>
		<?php
		admin_reports_panel_end();
	}
}

admin_reports_panel_start('Player Reports');
if ($reportsData !== false) {
	foreach ($reports as $statusId => $statusArray) {
		?>
		<div class="pt-3 border-t border-[var(--box-inner-border)]">
			<h3 class="statusType text-xs font-semibold mb-2 cursor-pointer" data-status="<?php echo (int)$statusId; ?>"><?php echo status_label($statusTypes, $statusId); ?> (<span id="status-<?php echo (int)$statusId; ?>">Visible</span>)</h3>
			<div class="space-y-2">
			<?php foreach ($statusArray as $reportId => $report): ?>
				<div class="row<?php echo (int)$report['status']; ?> grid grid-cols-1 sm:grid-cols-[260px_1fr] gap-2 py-2 px-3 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-xs text-[var(--text-muted)]">
					<div>
						<b>Report ID:</b> #<?php echo (int)$report['id']; ?><br>
						<b>Name:</b> <a class="text-[var(--text-link)]" href="characterprofile.php?name=<?php echo urlencode($report['name']); ?>"><?php echo hhb_tohtml($report['name']); ?></a><br>
						<b>Position:</b> /pos <?php echo hhb_tohtml($report['posx'].', '.$report['posy'].', '.$report['posz']); ?><br>
						<b>Reported:</b> <?php echo hhb_tohtml(getClock($report['date'], true, true)); ?><br>
						<b>Status:</b> <?php echo status_label($statusTypes, $report['status']); ?>. <a class="text-[var(--text-link)]" href="?action=edit&name=<?php echo urlencode($report['name']); ?>&id=<?php echo (int)$report['id']; ?>">Edit</a>
					</div>
					<div><?php echo hhb_tohtml($report['report_description']); ?></div>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
} else {
	echo '<div class="text-xs text-[var(--text-muted)] text-center py-4">No reports submitted.</div>';
}
admin_reports_panel_end();
?>
<script type="text/javascript">
function toggle(statusId) {
	var divStatus = 'row' + statusId;
	var msgStatus = 'status-' + statusId;
	var statusElement = document.getElementById(msgStatus);
	if (!statusElement) return;
	statusElement.innerHTML = (statusElement.innerHTML == 'Visible') ? 'Hidden' : 'Visible';
	var elements = document.getElementsByClassName(divStatus);
	for (var i = 0; i < elements.length; i++) elements[i].style.display = (elements[i].style.display == 'none') ? 'grid' : 'none';
}
<?php foreach ($hideStatus as $statusId) echo "toggle($statusId);"; ?>
var st = document.body.querySelectorAll('.statusType');
for (var i = 0; i < st.length; i++) st[i].addEventListener('click', function(e) { toggle(e.currentTarget.getAttribute('data-status')); });
</script>
<?php include 'layout/overall/footer.php'; ?>
