<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
$updateCache = false;
if (user_logged_in()) {
	if (is_admin($user_data)) {
		// variables
		$status = true;
		if (isset($_POST['changelogId'])) $changelogId = (int)$_POST['changelogId'];
		else $status = false;
		if (isset($_POST['changelogText'])) $changelogText = getValue($_POST['changelogText']);
		else $status = false;
		if (isset($_POST['category'])) {
			$category = getValue($_POST['category']);
		} else {
			$category = 'updates';
		}

		$allowedCategories = array('updates', 'community', 'guides', 'events');
		if (!in_array($category, $allowedCategories)) {
			$category = 'updates';
		}
		if (isset($_POST['action'])) $action = (int)$_POST['action'];
		else $action = 0;
		// POST delete
		if (isset($_POST['delete'])) {
			$delete = isset($_POST['delete']) ? (int)$_POST['delete'] : 0;
			if ($delete && $action == 1) {
				mysql_delete("DELETE FROM `znote_changelog` WHERE `id`='$delete' LIMIT 1;");
				echo '<div class="PageBody-module__OAJIWq__body mb-4">
	<div class="PageBody-module__OAJIWq__content">
		<div class="text-xs text-[#22c55e] font-semibold">
			Changelog message deleted!
		</div>
	</div>
</div>';
				$updateCache = true;
			}
		} else {
			if ($status) {
				// POST update
				if ($changelogId > 0) {
					mysql_update("UPDATE `znote_changelog` SET `text`='$changelogText', `category`='$category' WHERE `id`='$changelogId' LIMIT 1;");
					echo '<div class="PageBody-module__OAJIWq__body mb-4">
	<div class="PageBody-module__OAJIWq__content">
		<div class="text-xs text-[#22c55e] font-semibold">
			Changelog message updated!
		</div>
	</div>
</div>';
					$updateCache = true;
				} else {
					// POST create
					$time = time();
					mysql_insert("INSERT INTO `znote_changelog` (`text`, `time`, `report_id`, `status`, `category`) VALUES ('$changelogText', '$time', '0', '35', '$category');");
					echo '<div class="PageBody-module__OAJIWq__body mb-4">
	<div class="PageBody-module__OAJIWq__content">
		<div class="text-xs text-[#22c55e] font-semibold">
			Changelog message created!
		</div>
	</div>
</div>';
					$updateCache = true;
				}
			}
		}
		if ($action === 2) {
			$old = mysql_select_single("SELECT `text`, `category` FROM `znote_changelog` WHERE `id`='$changelogId' LIMIT 1;");
		}
		// HTML to create or update
		?>
		<div class="PageHeader-module__HZea0q__header">
	<div class="PageHeader-module__HZea0q__headerContent">
		<h2 class="PageHeader-module__HZea0q__title">
			<span class="flex items-center gap-2">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-scroll-text w-5 h-5" aria-hidden="true">
					<path d="M15 12h-5"></path>
					<path d="M15 8h-5"></path>
					<path d="M19 17V5a2 2 0 0 0-2-2H4"></path>
					<path d="M8 21h8a2 2 0 0 0 2-2v-1H8a2 2 0 0 1 0-4h10"></path>
				</svg>
				<?php echo ($action === 2) ? 'Update Changelog' : 'Add Changelog'; ?>
			</span>
		</h2>
	</div>
</div>

<div class="PageBody-module__OAJIWq__body mb-4">
	<div class="PageBody-module__OAJIWq__content">
		<form action="" method="POST" class="space-y-4">
			<input name="changelogId" type="hidden" value="<?php echo ($action === 2) ? $changelogId : 0; ?>">

			<div>
				<label class="block text-xs font-semibold mb-2 text-[var(--text-muted)]">Category</label>

				<div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
					<label class="flex items-center gap-2 px-3 py-2 rounded border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)] cursor-pointer text-xs">
						<input type="radio" name="category" value="updates"
						<?php if (($action === 2 && isset($old['category']) && $old['category'] == 'updates') || $action !== 2) echo 'checked'; ?>>
						<span>Updates</span>
					</label>

					<label class="flex items-center gap-2 px-3 py-2 rounded border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)] cursor-pointer text-xs">
						<input type="radio" name="category" value="community"
						<?php if ($action === 2 && isset($old['category']) && $old['category'] == 'community') echo 'checked'; ?>>
						<span>Community</span>
					</label>

					<label class="flex items-center gap-2 px-3 py-2 rounded border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)] cursor-pointer text-xs">
						<input type="radio" name="category" value="guides"
						<?php if ($action === 2 && isset($old['category']) && $old['category'] == 'guides') echo 'checked'; ?>>
						<span>Guides</span>
					</label>

					<label class="flex items-center gap-2 px-3 py-2 rounded border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)] cursor-pointer text-xs">
						<input type="radio" name="category" value="events"
						<?php if ($action === 2 && isset($old['category']) && $old['category'] == 'events') echo 'checked'; ?>>
						<span>Events</span>
					</label>
				</div>
			</div>

			<div>
				<label class="block text-xs font-semibold mb-2 text-[var(--text-muted)]">Message</label>
				<textarea rows="7" maxlength="254" name="changelogText" class="w-full px-3 py-2 text-xs bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] resize-y"><?php echo ($action === 2) ? htmlspecialchars($old['text'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
			</div>

			<div class="flex justify-end">
				<button type="submit" class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-4 py-2 text-[11px] cursor-pointer">
					<?php echo ($action === 2) ? 'Update Changelog' : 'Create Changelog'; ?>
				</button>
			</div>
		</form>
	</div>
</div>
		<?php
	}
}
?>

<?php
$cache = new Cache('engine/cache/changelog');
$cache->useMemory(false);
if ($updateCache === true) {
	$changelogs = mysql_select_multi("SELECT `id`, `text`, `time`, `report_id`, `status`, `category` FROM `znote_changelog` ORDER BY `id` DESC;");

	$cache->setContent($changelogs);
	$cache->save();
} else {
	$changelogs = $cache->load();
}
if (isset($changelogs) && !empty($changelogs) && $changelogs !== false) {
	?>
	<div class="PageHeader-module__HZea0q__header">
	<div class="PageHeader-module__HZea0q__headerContent">
		<h2 class="PageHeader-module__HZea0q__title">
			<span class="flex items-center gap-2">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-history w-5 h-5" aria-hidden="true">
					<path d="M3 3v5h5"></path>
					<path d="M3.05 13a9 9 0 1 0 .5-4"></path>
					<path d="M12 7v5l4 2"></path>
				</svg>
				Changelog
			</span>
		</h2>
	</div>
</div>

<div class="PageBody-module__OAJIWq__body">
	<div class="PageBody-module__OAJIWq__content">
		<div class="space-y-3">
			<?php foreach ($changelogs as $changelog): ?>
				<?php
				$icon = 'updates';
				if (isset($changelog['category']) && !empty($changelog['category'])) {
					$icon = $changelog['category'];
				}
				?>

				<div class="rounded border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)] p-4 shadow-sm">
					<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
						<div class="flex-1 min-w-0">
							<div class="flex items-center gap-2 mb-2">
								<img src="template/images/<?php echo $icon; ?>.gif" alt="<?php echo ucfirst($icon); ?>" style="width:18px;height:18px;">
								
								<span class="inline-flex items-center px-2 py-1 rounded text-[10px] uppercase tracking-wide font-bold bg-[var(--color-primary)] text-white">
									<?php echo htmlspecialchars($icon, ENT_QUOTES, 'UTF-8'); ?>
								</span>

								<span class="text-xs text-[var(--text-muted)]">
									<?php echo getClock((isset($changelog['time'])) ? $changelog['time'] : 0, true, true); ?>
								</span>
							</div>

							<div class="text-sm leading-relaxed text-[var(--text)]">
								<?php echo nl2br($changelog['text']); ?>
							</div>
						</div>

						<?php if (user_logged_in() && is_admin($user_data)): ?>
							<div class="flex flex-row sm:flex-col gap-2 sm:min-w-[120px]">
								<form action="" method="POST">
									<input name="changelogId" type="hidden" value="<?php echo $changelog['id']; ?>">
									<input name="action" type="hidden" value="2">

									<button type="submit" class="w-full inline-flex items-center justify-center font-semibold transition-all duration-150 relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:opacity-90 px-3 py-1 text-[11px] cursor-pointer">
										Update
									</button>
								</form>

								<form action="" method="POST" onsubmit="return confirm('Delete this changelog?');">
									<input name="delete" type="hidden" value="<?php echo $changelog['id']; ?>">
									<input name="action" type="hidden" value="1">

									<button type="submit" class="w-full inline-flex items-center justify-center font-semibold transition-all duration-150 relative bg-red-700 text-white border border-red-800 shadow-[0_1px_2px_rgba(0,0,0,0.5)] hover:bg-red-600 px-3 py-1 text-[11px] cursor-pointer rounded">
										Delete
									</button>
								</form>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
	<?php
} else {
	?>
	<div class="PageHeader-module__HZea0q__header">
		<div class="PageHeader-module__HZea0q__headerContent">
			<h2 class="PageHeader-module__HZea0q__title">
				<span class="flex items-center gap-2">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-history w-5 h-5" aria-hidden="true">
						<path d="M3 3v5h5"></path>
						<path d="M3.05 13a9 9 0 1 0 .5-4"></path>
						<path d="M12 7v5l4 2"></path>
					</svg>
					Changelog
				</span>
			</h2>
		</div>
	</div>

	<div class="PageBody-module__OAJIWq__body">
		<div class="PageBody-module__OAJIWq__content">
			<div class="flex flex-col items-center justify-center text-center py-10">
				<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-scroll-text mb-3 opacity-40" aria-hidden="true">
					<path d="M15 12h-5"></path>
					<path d="M15 8h-5"></path>
					<path d="M19 17V5a2 2 0 0 0-2-2H4"></path>
					<path d="M8 21h8a2 2 0 0 0 2-2v-1H8a2 2 0 0 1 0-4h10"></path>
				</svg>

				<div class="text-sm font-semibold mb-1">
					No changelog entries yet
				</div>

				<div class="text-xs text-[var(--text-muted)]">
					New updates, events and community news will appear here.
				</div>
			</div>
		</div>
	</div>
	<?php
}
include 'layout/overall/footer.php';
?>
