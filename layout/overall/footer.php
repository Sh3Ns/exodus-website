</main>
		</div>
		
		
		
		
		<div class="hidden lg:block">
		<aside class="w-[226px] flex-shrink-0 space-y-3">
		<div class="webshop-widget relative overflow-hidden">
		<div class="webshop-header relative" style="background-image: url(&quot;template/images/header1.png&quot;); background-size: 100% 100%; background-repeat: no-repeat; aspect-ratio: 1684 / 275;">
		<h3 class="absolute inset-0 flex items-center justify-center text-sm uppercase tracking-wider text-center" style="font-family: var(--font-medieval); font-weight: 700; color: rgb(255, 224, 102); text-shadow: rgba(0, 0, 0, 0.8) 1px 1px 2px, rgba(255, 200, 0, 0.4) 0px 0px 8px;">✦ Webshop ✦</h3>
		</div>
		<div class="relative" style="z-index: 2; margin-top: -2px;">
		<div class="absolute inset-0 rounded-sm overflow-hidden" style="z-index: 0;">
		<img alt="" loading="lazy" decoding="async" data-nimg="fill" class="object-cover" src="template/images/exclusive_box.webp" style="position: absolute; height: 100%; width: 100%; inset: 0px; color: transparent;">
		<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20"></div>
		</div>
		<div class="relative" style="z-index: 1; background-image: url(&quot;template/images/content.png&quot;); background-size: 100% 100%; background-repeat: no-repeat;"><div class="relative p-4 pt-12 pb-4 text-center" style="z-index: 2;">
		<p class="text-base mb-3 tracking-wide" style="font-family: var(--font-medieval); font-style: italic; font-weight: 700; color: rgb(255, 224, 102); text-shadow: rgba(0, 0, 0, 0.9) 2px 2px 4px, rgba(0, 0, 0, 0.5) 0px 0px 10px;">
		Exclusive Content</p>
		<a class="webshop-btn block w-full bg-gradient-to-b from-amber-500 via-amber-600 to-amber-700 hover:from-amber-400 hover:via-amber-500 hover:to-amber-600 text-white py-2.5 text-sm font-bold uppercase tracking-wider transition-all border-2 border-amber-400/80 hover:border-amber-300 shadow-[0_2px_8px_rgba(0,0,0,0.5),inset_0_1px_0_rgba(255,255,255,0.3)] hover:shadow-[0_0_15px_rgba(251,191,36,0.5),inset_0_1px_0_rgba(255,255,255,0.3)] rounded" href="shop.php">
		Get Coins</a>
		</div></div>
		</div></div>
		<div class="BoostedBox-module__6IRxPW__box">
		<div class="BoostedBox-module__6IRxPW__header">
		<h3 class="BoostedBox-module__6IRxPW__headerTitle">
		Boosted</h3></div><div class="BoostedBox-module__6IRxPW__body">
		<div class="BoostedBox-module__6IRxPW__content">
		<?php
		$boostedCreature = mysql_select_single("
			SELECT `boostname`, `looktype`, `lookhead`, `lookbody`, `looklegs`, `lookfeet`, `lookaddons`, `raceid`
			FROM `boosted_creature`
			ORDER BY `date` DESC
			LIMIT 1;
		");

		$boostedBoss = mysql_select_single("
			SELECT `boostname`, `looktype`, `lookhead`, `lookbody`, `looklegs`, `lookfeet`, `lookaddons`, `raceid`
			FROM `boosted_boss`
			ORDER BY `date` DESC
			LIMIT 1;
		");
		?>

		<div class="BoostedBox-module__6IRxPW__creaturesContainer">

			<div class="BoostedBox-module__6IRxPW__creatureCard" title="Boosted Boss: <?php echo htmlspecialchars($boostedBoss['boostname']); ?>">
				<div class="BoostedBox-module__6IRxPW__creatureFrame BoostedBox-module__6IRxPW__bossFrame">
					<div class="BoostedBox-module__6IRxPW__creatureIcon BoostedBox-module__6IRxPW__bossIcon">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-skull w-3 h-3" aria-hidden="true">
							<path d="m12.5 17-.5-1-.5 1h1z"></path>
							<path d="M15 22a1 1 0 0 0 1-1v-1a2 2 0 0 0 1.56-3.25 8 8 0 1 0-11.12 0A2 2 0 0 0 8 20v1a1 1 0 0 0 1 1z"></path>
							<circle cx="15" cy="12" r="1"></circle>
							<circle cx="9" cy="12" r="1"></circle>
						</svg>
					</div>

					<div style="width: 56px; height: 56px; overflow: hidden;">
						<img
			src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo (int)$boostedBoss['looktype']; ?>&addons=<?php echo (int)$boostedBoss['lookaddons']; ?>&head=<?php echo (int)$boostedBoss['lookhead']; ?>&body=<?php echo (int)$boostedBoss['lookbody']; ?>&legs=<?php echo (int)$boostedBoss['looklegs']; ?>&feet=<?php echo (int)$boostedBoss['lookfeet']; ?>&v=<?php echo time(); ?>"
			alt="<?php echo htmlspecialchars($boostedBoss['boostname']); ?>"
			style="width: 56px; height: 56px;"
		>
					</div>
				</div>

				<p class="BoostedBox-module__6IRxPW__creatureLabel BoostedBox-module__6IRxPW__bossLabel">Boss</p>
				<p class="BoostedBox-module__6IRxPW__creatureName">
					<?php echo htmlspecialchars($boostedBoss['boostname']); ?>
				</p>
			</div>

			<div class="BoostedBox-module__6IRxPW__divider"></div>

			<div class="BoostedBox-module__6IRxPW__creatureCard" title="Boosted Creature: <?php echo htmlspecialchars($boostedCreature['boostname']); ?>">
				<div class="BoostedBox-module__6IRxPW__creatureFrame BoostedBox-module__6IRxPW__creatureFrameBlue">
					<div class="BoostedBox-module__6IRxPW__creatureIcon BoostedBox-module__6IRxPW__creatureIconBlue">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-swords w-[11px] h-[11px]" aria-hidden="true">
							<polyline points="14.5 17.5 3 6 3 3 6 3 17.5 14.5"></polyline>
							<line x1="13" x2="19" y1="19" y2="13"></line>
							<line x1="16" x2="20" y1="16" y2="20"></line>
							<line x1="19" x2="21" y1="21" y2="19"></line>
							<polyline points="14.5 6.5 18 3 21 3 21 6 17.5 9.5"></polyline>
							<line x1="5" x2="9" y1="14" y2="18"></line>
							<line x1="7" x2="4" y1="17" y2="20"></line>
							<line x1="3" x2="5" y1="19" y2="21"></line>
						</svg>
					</div>

					<div style="width: 56px; height: 56px; overflow: hidden;">
						<img
			src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo (int)$boostedCreature['looktype']; ?>&addons=<?php echo (int)$boostedCreature['lookaddons']; ?>&head=<?php echo (int)$boostedCreature['lookhead']; ?>&body=<?php echo (int)$boostedCreature['lookbody']; ?>&legs=<?php echo (int)$boostedCreature['looklegs']; ?>&feet=<?php echo (int)$boostedCreature['lookfeet']; ?>"
			alt="<?php echo htmlspecialchars($boostedCreature['boostname']); ?>"
			style="width: 56px; height: 56px;"
		>
					</div>
				</div>

				<p class="BoostedBox-module__6IRxPW__creatureLabel BoostedBox-module__6IRxPW__creatureLabelBlue">Creature</p>
				<p class="BoostedBox-module__6IRxPW__creatureName">
					<?php echo htmlspecialchars($boostedCreature['boostname']); ?>
				</p>
			</div>

		</div>
		</div>
		</div></div>
		<div class="DiscordBox-module__Stqp1a__box"><div class="DiscordBox-module__Stqp1a__header">
		<h3 class="DiscordBox-module__Stqp1a__headerTitle">Discord</h3></div>
		<div class="DiscordBox-module__Stqp1a__body">
		<div class="DiscordBox-module__Stqp1a__content">
		<div class="DiscordBox-module__Stqp1a__glow"></div>
		<div class="DiscordBox-module__Stqp1a__iconWrapper">
		<div class="DiscordBox-module__Stqp1a__iconRing"></div>
		<div class="DiscordBox-module__Stqp1a__iconBg">
		<svg class="w-7 h-7 text-white drop-shadow-lg" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
		<path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"></path></svg>
		</div>
		<div class="DiscordBox-module__Stqp1a__onlineIndicator"></div>
		</div>
		<p class="DiscordBox-module__Stqp1a__subtitle">Join our community!</p>
		<a href="https://discord.gg/D266yR8FsQ" target="_blank" rel="noopener noreferrer" class="DiscordBox-module__Stqp1a__button">Join Discord</a>
		</div>
		</div>
		</div>
		<div class="WikiBox-module__PxAH9a__box">
		<div class="WikiBox-module__PxAH9a__header">
		<h3 class="WikiBox-module__PxAH9a__headerTitle">Wiki</h3>
		</div>
		<div class="WikiBox-module__PxAH9a__body">
		<div class="WikiBox-module__PxAH9a__content">
		<div class="WikiBox-module__PxAH9a__glow"></div>
		<div class="WikiBox-module__PxAH9a__iconWrapper">
		<div class="WikiBox-module__PxAH9a__iconRing"></div>
		<div class="WikiBox-module__PxAH9a__iconBg">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open w-7 h-7 text-white drop-shadow-lg" aria-hidden="true">
		<path d="M12 7v14"></path><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"></path></svg></div>
		<div class="WikiBox-module__PxAH9a__sparkle">✦</div>
		</div><p class="WikiBox-module__PxAH9a__subtitle">Guides, monsters, quests and all the knowledge of <?php echo htmlspecialchars($config['gameserver']['name'], ENT_QUOTES, 'UTF-8'); ?>!</p>
		<span class="MenuBox-module__1GrUIG__badge bg-red-600">HOT</span>
		<a href="wiki.php" rel="noopener noreferrer" class="WikiBox-module__PxAH9a__button">Explore the Wiki</a>
		</div></div>
		</div>
		</aside>
		</div></div>
		</div>
		<footer class="bg-gradient-to-b from-[var(--box-header-from)] to-[var(--box-header-to)] border-t border-[var(--box-header-border)] py-3 mt-4">
		<div class="max-w-[1280px] mx-auto px-3">
    <div class="flex items-center justify-between text-white/70 text-xs">
        <span>OpenGamesCommunity 2026 ©</span>

        <span class="inline-flex items-center gap-1.5">
            <span class="relative flex h-1.5 w-1.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full <?php echo $serverOnline ? 'bg-green-400' : 'bg-red-400'; ?> opacity-75"></span>
                <span class="relative inline-flex rounded-full h-1.5 w-1.5 <?php echo $serverOnline ? 'bg-green-500' : 'bg-red-500'; ?>"></span>
            </span>

            <span>
                <?php if ($serverOnline): ?>
                    <?php echo user_count_online(); ?> online
                <?php else: ?>
                    Server Offline
                <?php endif; ?>
            </span>
        </span>
    </div>
</div>
		</footer>
		</div></div>
		<script>
		document.addEventListener('DOMContentLoaded', function () {
			const menuButtons = document.querySelectorAll('.MenuBox-module__1GrUIG__header');

			menuButtons.forEach((button, index) => {
				const wrapper = button.parentElement.querySelector('.MenuBox-module__1GrUIG__bodyWrapper');
				const iconPath = button.querySelector('svg path');
				if (!wrapper) return;

				const titleEl = button.querySelector('.MenuBox-module__1GrUIG__headerTitle');
				const menuKey = 'menuState_' + (titleEl ? titleEl.textContent.trim() : index);

				const savedState = localStorage.getItem(menuKey);
				const shouldOpen = savedState === 'open';

				if (shouldOpen) {
					wrapper.classList.add('menu-open');
					wrapper.style.gridTemplateRows = '1fr';
					if (iconPath) {
						iconPath.setAttribute('d', 'm18 15-6-6-6 6');
					}
				} else {
					wrapper.classList.remove('menu-open');
					wrapper.style.gridTemplateRows = '0fr';
					if (iconPath) {
						iconPath.setAttribute('d', 'm6 9 6 6 6-6');
					}
				}

				button.addEventListener('click', function () {
					const isOpen = wrapper.classList.contains('menu-open');

					if (isOpen) {
						wrapper.classList.remove('menu-open');
						wrapper.style.gridTemplateRows = '0fr';
						localStorage.setItem(menuKey, 'closed');

						if (iconPath) {
							iconPath.setAttribute('d', 'm6 9 6 6 6-6');
						}
					} else {
						wrapper.classList.add('menu-open');
						wrapper.style.gridTemplateRows = '1fr';
						localStorage.setItem(menuKey, 'open');

						if (iconPath) {
							iconPath.setAttribute('d', 'm18 15-6-6-6 6');
						}
					}
				});
			});
		});
		</script>
		<script>
		document.addEventListener('DOMContentLoaded', function () {
			document.querySelectorAll('table tr.cursor-pointer').forEach(row => {
				row.addEventListener('click', function () {
					const content = this.querySelector('.ticker-content');
					const arrow = this.querySelector('td:last-child svg');

					if (!content) return;

					const expanded = content.classList.contains('ticker-expanded');

					if (expanded) {
						content.classList.remove('ticker-expanded');
						content.style.whiteSpace = '';
						content.style.overflow = '';
						content.style.textOverflow = '';
						content.style.display = '';
						content.style.webkitLineClamp = '';
						content.style.webkitBoxOrient = '';

						if (arrow) {
							arrow.style.transform = 'rotate(0deg)';
						}
					} else {
						content.classList.add('ticker-expanded');
						content.style.whiteSpace = 'normal';
						content.style.overflow = 'visible';
						content.style.textOverflow = 'unset';
						content.style.display = 'block';
						content.style.webkitLineClamp = 'unset';
						content.style.webkitBoxOrient = 'unset';

						if (arrow) {
							arrow.style.transform = 'rotate(90deg)';
						}
					}
				});
			});
		});
		</script>
		<script>
		document.addEventListener('DOMContentLoaded', function () {
			const openButton = document.querySelector('button[aria-label="Open menu"]');
			const closeButton = document.querySelector('button[aria-label="Close menu"]');
			const overlay = document.querySelector('.lg\\:hidden.fixed.inset-0.bg-black\\/60');
			const mobileMenu = document.querySelector('.lg\\:hidden.fixed.top-0.left-0.bottom-0.w-\\[260px\\]');

			if (!openButton || !closeButton || !overlay || !mobileMenu) return;

			function openMenu() {
				overlay.classList.remove('opacity-0', 'pointer-events-none');
				overlay.classList.add('opacity-100');

				mobileMenu.classList.remove('-translate-x-full');
				mobileMenu.classList.add('translate-x-0');
			}

			function closeMenu() {
				overlay.classList.remove('opacity-100');
				overlay.classList.add('opacity-0', 'pointer-events-none');

				mobileMenu.classList.remove('translate-x-0');
				mobileMenu.classList.add('-translate-x-full');
			}

			openButton.addEventListener('click', openMenu);
			closeButton.addEventListener('click', closeMenu);
			overlay.addEventListener('click', closeMenu);
		});
		</script>
		<script>
		document.querySelectorAll('.news-item').forEach(item => {
			item.addEventListener('click', function () {
				this.classList.toggle('open');
			});
		});
		</script>
		<div class="site-page-loader" id="sitePageLoader" aria-hidden="true">
			<div class="site-page-loader__panel">
				<div class="site-page-loader__spinner"></div>
				<div class="site-page-loader__text">Loading...</div>
			</div>
		</div>
		<script>
		document.addEventListener('DOMContentLoaded', function () {
			const loader = document.getElementById('sitePageLoader');
			if (!loader) return;
			let loaderTimeout = null;

			function showPageLoader() {
				clearTimeout(loaderTimeout);
				loader.classList.add('is-visible');
				loader.setAttribute('aria-hidden', 'false');
				loaderTimeout = setTimeout(hidePageLoader, 12000);
			}

			function hidePageLoader() {
				clearTimeout(loaderTimeout);
				loader.classList.remove('is-visible');
				loader.setAttribute('aria-hidden', 'true');
			}

			function isRealNavigationLink(link, event) {
				if (!link || !link.href) return false;
				if (event && (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey)) return false;
				if (link.target && link.target !== '_self') return false;
				if (link.hasAttribute('download')) return false;

				const href = link.getAttribute('href') || '';
				if (href === '' || href.charAt(0) === '#' || href.indexOf('javascript:') === 0 || href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) return false;

				const targetUrl = new URL(link.href, window.location.href);
				if (targetUrl.href === window.location.href) return false;

				return true;
			}

			document.addEventListener('click', function (event) {
				const link = event.target.closest('a');
				if (isRealNavigationLink(link, event)) {
					setTimeout(function () {
						if (!event.defaultPrevented) showPageLoader();
					}, 0);
				}
			});

			document.addEventListener('submit', function (event) {
				const form = event.target;
				if (!form || (form.target && form.target !== '_self')) return;
				setTimeout(function () {
					if (!event.defaultPrevented) showPageLoader();
				}, 0);
			});

			window.addEventListener('pageshow', function () {
				hidePageLoader();
			});

			window.addEventListener('focus', function () {
				hidePageLoader();
			});

			document.addEventListener('visibilitychange', function () {
				if (!document.hidden) hidePageLoader();
			});
		});
		</script>
		</body>
		</html>
