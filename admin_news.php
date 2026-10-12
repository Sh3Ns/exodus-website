<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';
protect_page();
admin_only($user_data);

$message = '';
$mode = '';
$edit = false;

function refresh_news_cache() {
	$cache = new Cache('engine/cache/news');
	$news = fetchAllNews();
	$cache->setContent($news);
	$cache->save();
}

function admin_news_panel_start($title) {
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header "><div class="PageHeader-module__HZea0q__headerContent"><h2 class="PageHeader-module__HZea0q__title"><span class="flex items-center gap-2"><?php echo hhb_tohtml($title); ?></span></h2></div></div>
		<div class="PageBody-module__OAJIWq__body "><div class="PageBody-module__OAJIWq__content"><div class="space-y-3">
	<?php
}
function admin_news_panel_end() {
	?></div></div></div></div><br><?php
}

$input_class = 'w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none';
$select_class = 'w-full px-2 py-1 text-xs bg-[var(--box-inner-bg)] text-[var(--text-muted)] border border-[var(--box-inner-border)] focus:outline-none';
$button_class = 'inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer';

if (empty($_POST) === false && !empty($_POST['option'])) {
	list($action, $id) = explode('!', sanitize($_POST['option']));
	$id = (int)$id;

	if ($action === 'd') {
		mysql_delete("DELETE FROM `znote_news` WHERE `id`='$id';");
		refresh_news_cache();
		$message = 'News deleted.';
	} else if ($action === 'a') {
		$mode = 'add';
	} else if ($action === 'i') {
		list($charid, $title, $text) = array((int)$_POST['selected_char'], mysql_znote_escape_string($_POST['title']), mysql_znote_escape_string($_POST['text']));
		$date = time();
		mysql_insert("INSERT INTO `znote_news` (`title`, `text`, `date`, `pid`) VALUES ('$title', '$text', '$date', '$charid');");
		refresh_news_cache();
		$message = 'News created successfully.';
	} else if ($action === 's') {
		list($title, $text) = array(mysql_znote_escape_string($_POST['title']), mysql_znote_escape_string($_POST['text']));
		mysql_update("UPDATE `znote_news` SET `title`='$title',`text`='$text' WHERE `id`='$id';");
		refresh_news_cache();
		$message = 'News successfully updated.';
	} else if ($action === 'e') {
		$news = fetchAllNews();
		foreach ($news as $n) if ($n['id'] == $id) $edit = $n;
		$mode = 'edit';
	}
}

if ($message !== '') {
	admin_news_panel_start('News Result');
	?><div class="text-xs text-[#22c55e]"><?php echo hhb_tohtml($message); ?></div><?php
	admin_news_panel_end();
}

if ($mode === 'add' || $mode === 'edit') {
	$char_array = user_character_list($user_data['id']);
	$count = 0;
	?>
	<script src="engine/js/nicedit.js" type="text/javascript"></script>
	<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>
	<?php admin_news_panel_start(($mode === 'add') ? 'Create News' : 'Edit News'); ?>
	<form action="" method="post" class="space-y-2">
		<input type="hidden" name="option" value="<?php echo ($mode === 'add') ? 'i!0' : 's!' . (int)$edit['id']; ?>">
		<?php if ($mode === 'add'): ?>
			<select class="<?php echo $select_class; ?>" name="selected_char">
				<?php
				if ($char_array !== false) {
					foreach ($char_array as $name) {
						$name = $name['name'];
						$charD = user_character_data(user_character_id($name), 'group_id', 'id');
						if ($charD['group_id'] > 1) {
							echo '<option value="'. (int)user_character_id($name) .'">'. hhb_tohtml($name) .'</option>';
							$count++;
						}
					}
				}
				?>
			</select>
		<?php endif; ?>
		<input class="<?php echo $input_class; ?>" type="text" name="title" value="<?php echo ($mode === 'edit') ? hhb_tohtml($edit['title']) : ''; ?>" placeholder="Title">
		<textarea class="<?php echo $input_class; ?>" name="text" id="area1" rows="10" placeholder="Contents..."><?php echo ($mode === 'edit') ? hhb_tohtml($edit['text']) : ''; ?></textarea>
		<div class="flex justify-end"><button class="<?php echo $button_class; ?>" type="submit"><?php echo ($mode === 'add') ? 'Create News' : 'Save Changes'; ?></button></div>
	</form>
	<?php if ($mode === 'add' && $count === 0): ?><div class="text-xs text-red-600">ERROR: No GMs or Tutors on this account.</div><?php endif; ?>
	<?php admin_news_panel_end(); ?>
	<?php
}

admin_news_panel_start('News Admin Panel');
?>
<form action="" method="post" class="flex justify-end">
	<input type="hidden" name="option" value="a!0">
	<button class="<?php echo $button_class; ?>" type="submit">Create new article</button>
</form>
<?php
$news = fetchAllNews();
if ($news !== false) {
	foreach ($news as $n) {
		?>
		<div class="grid grid-cols-1 sm:grid-cols-[150px_120px_1fr_70px_80px] gap-2 items-center py-2 px-3 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-xs text-[var(--text-muted)]">
			<div><?php echo hhb_tohtml(getClock($n['date'], true)); ?></div>
			<a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)]" href="characterprofile.php?name=<?php echo urlencode($n['name']); ?>"><?php echo hhb_tohtml($n['name']); ?></a>
			<div class="font-semibold"><?php echo hhb_tohtml($n['title']); ?></div>
			<form action="" method="post"><input type="hidden" name="option" value="e!<?php echo (int)$n['id']; ?>"><button class="<?php echo $button_class; ?> w-full" type="submit">Edit</button></form>
			<form action="" method="post" onsubmit="return confirm('Delete this news article?');"><input type="hidden" name="option" value="d!<?php echo (int)$n['id']; ?>"><button class="<?php echo $button_class; ?> w-full" type="submit">Delete</button></form>
		</div>
		<?php
	}
} else {
	?><div class="text-xs text-[var(--text-muted)] text-center py-4">No news articles found.</div><?php
}
admin_news_panel_end();
include 'layout/overall/footer.php';
?>
