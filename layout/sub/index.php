<?php
if ($config['UseChangelogTicker']) {
	//////////////////////
	// Changelog ticker //
	// Load from cache
	$changelogCache = new Cache('engine/cache/changelog');
	$changelogCache->useMemory(false);
	$changelogs = $changelogCache->load();

	if (isset($changelogs) && !empty($changelogs) && $changelogs !== false) {
		?>
		<div class="PageHeader-module__HZea0q__header ">
		<div class="PageHeader-module__HZea0q__headerContent">
		<span class="PageHeader-module__HZea0q__titleIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-radio w-4 h-4" aria-hidden="true">
		<path d="M16.247 7.761a6 6 0 0 1 0 8.478"></path><path d="M19.075 4.933a10 10 0 0 1 0 14.134"></path><path d="M4.925 19.067a10 10 0 0 1 0-14.134"></path><path d="M7.753 16.239a6 6 0 0 1 0-8.478"></path><circle cx="12" cy="12" r="2"></circle></svg>
		</span>
		<h2 class="PageHeader-module__HZea0q__title">News Ticker</h2></div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
		<div class="PageBody-module__OAJIWq__content">
		<div class="overflow-x-auto">
		<table class="w-full text-xs">
		<tbody>
				<?php
				for ($i = 0; $i < count($changelogs) && $i < 5; $i++) {
					?>
					<tr class="border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)] cursor-pointer bg-[var(--box-inner-bg)]/30 [&amp;&gt;td]:py-1">
					<td class="py-1.5 px-2 w-6">
						<?php
						$icon = !empty($changelogs[$i]['category']) ? $changelogs[$i]['category'] : 'updates';
						?>
						<img alt="<?php echo ucfirst($icon); ?>" class="w-4 h-4 rounded object-cover" src="template/images/<?php echo $icon; ?>.gif">
					</td>
						<td class="py-1.5 px-1 text-[var(--text-muted)] whitespace-nowrap text-[10px] w-16"><?php echo getClock($changelogs[$i]['time'], true, true); ?></td>
						<td class="py-1.5 px-2"><span class="text-[11px] text-[var(--foreground)] ticker-content line-clamp-1"><p><?php echo $changelogs[$i]['text']; ?></p></td>
						<td class="py-1.5 px-1 w-4">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-3 h-3 text-[var(--text-muted)] transition-transform duration-200" aria-hidden="true">
						<path d="m9 18 6-6-6-6"></path></svg>
						</td>
					</tr>
					<?php
				}
				?>
		</tbody>
		</table></div></div></div>
			
			
		<?php
	} else echo '<div class="PageHeader-module__HZea0q__header ">
		<div class="PageHeader-module__HZea0q__headerContent">
		<span class="PageHeader-module__HZea0q__titleIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-radio w-4 h-4" aria-hidden="true">
		<path d="M16.247 7.761a6 6 0 0 1 0 8.478"></path><path d="M19.075 4.933a10 10 0 0 1 0 14.134"></path><path d="M4.925 19.067a10 10 0 0 1 0-14.134"></path><path d="M7.753 16.239a6 6 0 0 1 0-8.478"></path><circle cx="12" cy="12" r="2"></circle></svg>
		</span>
		<h2 class="PageHeader-module__HZea0q__title">News Ticker</h2></div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
		<div class="PageBody-module__OAJIWq__content">
		<div class="overflow-x-auto">
		<table class="w-full text-xs">
		<tbody>
		<tr class="border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)] cursor-pointer bg-[var(--box-inner-bg)]/30 [&amp;&gt;td]:py-1"><td class="py-1.5 px-2 w-6"><img alt="Updates" class="w-4 h-4 rounded object-cover" src="template/images/updates.gif"></td>
		<td class="py-1.5 px-1 text-[var(--text-muted)] whitespace-nowrap text-[10px] w-16">-</td>
		<td class="py-1.5 px-2"><span class="text-[11px] text-[var(--foreground)] ticker-content line-clamp-1"><p>
		<strong>No changelogs submitted.</strong></p></span>
		</td><td class="py-1.5 px-1 w-4">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-3 h-3 text-[var(--text-muted)] transition-transform duration-200" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		</td></tr></tbody>
		</table></div></div></div>';
}

$cache = new Cache('engine/cache/news');
if ($cache->hasExpired()) {
	$news = fetchAllNews();
	$cache->setContent($news);
	$cache->save();
} else {
	$news = $cache->load();
}

// Design and present the list
if ($news) {

	$total_news = count($news);
	$row_news = $total_news / $config['news_per_page'];
	$page_amount = ceil($total_news / $config['news_per_page']);
	$current = $config['news_per_page'] * $page;

	function TransformToBBCode($string) {
		$tags = array(
			'[center]{$1}[/center]' => '<center>$1</center>',
			'[b]{$1}[/b]' => '<b>$1</b>',
			'[size={$1}]{$2}[/size]' => '<font size="$1">$2</font>',
			'[img]{$1}[/img]'    => '<a href="$1" target="_BLANK"><img src="$1" alt="image" style="width: 100%"></a>',
			'[link]{$1}[/link]'    => '<a href="$1">$1</a>',
			'[link={$1}]{$2}[/link]'   => '<a href="$1" target="_BLANK">$2</a>',
			'[color={$1}]{$2}[/color]' => '<font color="$1">$2</font>',
			'[*]{$1}[/*]' => '<li>$1</li>',
			'[youtube]{$1}[/youtube]' => '<div class="youtube"><div class="aspectratio"><iframe src="//www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe></div></div>',
		);
		foreach ($tags as $tag => $value) {
			$code = preg_replace('/placeholder([0-9]+)/', '(.*?)', preg_quote(preg_replace('/\{\$([0-9]+)\}/', 'placeholder$1', $tag), '/'));
			$string = preg_replace('/'.$code.'/i', $value, $string);
		}
		return $string;
	}

	if ($view !== "") { // We want to view a specific news post
		$si = false;
		if (ctype_digit($view) === false) {
			for ($i = 0; $i < count($news); $i++) if ($view === urlencode($news[$i]['title'])) $si = $i;
		} else {
			for ($i = 0; $i < count($news); $i++) if ((int)$view === (int)$news[$i]['id']) $si = $i;
		}

		if ($si !== false) {
			echo "hello world!";
			?>
			<div>
				<div class="PageHeader-module__HZea0q__header ">
				<div class="PageHeader-module__HZea0q__headerContent">
				<span class="PageHeader-module__HZea0q__titleIcon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-newspaper w-4 h-4" aria-hidden="true">
				<path d="M15 18h-5"></path><path d="M18 14h-8"></path><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-4 0v-9a2 2 0 0 1 2-2h2"></path>
				<rect width="8" height="4" x="10" y="6" rx="1"></rect></svg></span>
				<h2 class="PageHeader-module__HZea0q__title">News</h2>
				</div>
				</div>
				<div class="PageBody-module__OAJIWq__body ">
				<div class="PageBody-module__OAJIWq__content">
				<div class="space-y-4">
				<div class="bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded p-3 " style="box-shadow: var(--box-inner-shadow);">
				<div class="postHolder">
			<div class="postHolder">
				<div class="well">
					<div class="header">
						<?php echo '<a href="?view='.$news[$si]['id'].'">[#'.$news[$si]['id'].']</a> '. getClock($news[$si]['date'], true) .' by <a href="characterprofile.php?name='. $news[$si]['name'] .'">'. $news[$si]['name'] .'</a> - <b>'. TransformToBBCode($news[$si]['title']) .'</b>'; ?>
					</div>
					<div class="body">
						<p><?php echo TransformToBBCode(nl2br($news[$si]['text'])); ?></p>
					</div>
				</div>
			</div>
			</div>
		</div></div>
		</div>
			<!-- OLD DESIGN: -->
			<?php
		} else {
			?><div>
				<div class="PageHeader-module__HZea0q__header ">
				<div class="PageHeader-module__HZea0q__headerContent">
				<span class="PageHeader-module__HZea0q__titleIcon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-newspaper w-4 h-4" aria-hidden="true">
				<path d="M15 18h-5"></path><path d="M18 14h-8"></path><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-4 0v-9a2 2 0 0 1 2-2h2"></path>
				<rect width="8" height="4" x="10" y="6" rx="1"></rect></svg></span>
				<h2 class="PageHeader-module__HZea0q__title">News</h2>
				</div>
				</div>
				<div class="PageBody-module__OAJIWq__body ">
				<div class="PageBody-module__OAJIWq__content">
				<div class="space-y-4">
				<div class="bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded p-3 " style="box-shadow: var(--box-inner-shadow);">
				<div class="postHolder">
			<table id="news">
				<tr class="yellow">
					<td class="zheadline">News post not found.</td>
				</tr>
				<tr>
					<td>
						<p>We failed to find the post you where looking for.</p>
					</td>
				</tr>
			</table>
			</div></div>
		</div></div>
		</div>
			<?php
		}

	} else { // We want to view latest news or a page of news.
	?>
		<div>
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<span class="PageHeader-module__HZea0q__titleIcon">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-newspaper w-4 h-4" aria-hidden="true">
						<path d="M15 18h-5"></path>
						<path d="M18 14h-8"></path>
						<path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-4 0v-9a2 2 0 0 1 2-2h2"></path>
						<rect width="8" height="4" x="10" y="6" rx="1"></rect>
					</svg>
				</span>
				<h2 class="PageHeader-module__HZea0q__title">News</h2>
			</div>
		</div>

		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="space-y-4">

	<?php

	for ($i = $current; $i < $current + $config['news_per_page']; $i++) {
		if (isset($news[$i])) {
			?>
				
				<div class="bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded p-3" style="box-shadow: var(--box-inner-shadow);">
	<div class="postHolder">
		<div class="well">
			<div class="header">
				<?php echo '<a href="?view='.urlencode($news[$i]['title']).'">'.getClock($news[$i]['date'], true).'</a> by <a href="characterprofile.php?name='. $news[$i]['name'] .'">'. $news[$i]['name'] .'</a> - <b>'. TransformToBBCode($news[$i]['title']) .'</b>'; ?>
			</div>
			<div class="body">
				<p><?php echo TransformToBBCode(nl2br($news[$i]['text'])); ?></p>
			</div>
		</div>
	</div>
</div>
<div class="border-b border-[var(--box-inner-border)] pb-3 last:border-b-0 last:pb-0"></div>
				
				<?php
			}
		}

		echo '<center><select name="newspage" onchange="location = this.options[this.selectedIndex].value;">';

		for ($i = 0; $i < $page_amount; $i++) {

			if ($i == $page) {

				echo '<option value="index.php?page='.$i.'" selected>Page '.$i.'</option>';

			} else {

				echo '<option value="index.php?page='.$i.'">Page '.$i.'</option>';
			}
		}

		echo '</select></center></div></div>
		</div></div>
		';

	}

} else {
	echo '<div>
				<div class="PageHeader-module__HZea0q__header ">
				<div class="PageHeader-module__HZea0q__headerContent">
				<span class="PageHeader-module__HZea0q__titleIcon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-newspaper w-4 h-4" aria-hidden="true">
				<path d="M15 18h-5"></path><path d="M18 14h-8"></path><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-4 0v-9a2 2 0 0 1 2-2h2"></path>
				<rect width="8" height="4" x="10" y="6" rx="1"></rect></svg></span>
				<h2 class="PageHeader-module__HZea0q__title">News</h2>
				</div>
				</div>
				<div class="PageBody-module__OAJIWq__body ">
				<div class="PageBody-module__OAJIWq__content">
				<div class="space-y-4">
				<div class="bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded p-3 " style="box-shadow: var(--box-inner-shadow);">
				<div class="postHolder"><p>No news exist.</p></div></div>
		</div></div>
		</div>';
}
?>
