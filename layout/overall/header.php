<?php
	require_once 'layout/layout_config.php';
	$launch_seconds = (strtotime($countDown) - time());
	$delay_hide = $launch_seconds + $countDown_hide;
?>
<!DOCTYPE html>
<html lang="en-US">
<head>

		<link rel="stylesheet" href="template/css/style.css" data-precedence="next">
		<link rel="stylesheet" href="template/css/custom1.css" data-precedence="next">
		<link rel="stylesheet" href="template/css/custom2.css" data-precedence="next">
		<link rel="stylesheet" href="template/css/custom3.css" data-precedence="next">
		<style>
		.MenuBox-module__1GrUIG__bodyWrapper {
			display: grid;
			grid-template-rows: 0fr;
			overflow: hidden;
			transition: grid-template-rows 0.25s ease;
		}

		.MenuBox-module__1GrUIG__bodyWrapper.menu-open {
			grid-template-rows: 1fr;
		}

		.MenuBox-module__1GrUIG__bodyOverflow {
			overflow: hidden;
			min-height: 0;
		}
		.news-item .full-text {
			display: none;
		}

		.news-item.open .full-text {
			display: block;
			
		}
		.ticker-content {
			transition: all 0.25s ease;
		}
		table.w-full tr > td {
			padding-top: 0.2rem !important;
			padding-bottom: 0.3rem !important;
		}

		td:last-child svg {
			transition: transform 0.25s ease;
			display: inline-block !important;
			visibility: visible !important;
			opacity: 1 !important;
		}
		table tr.cursor-pointer:hover {
			background: linear-gradient(
				to right,
				rgba(184, 162, 122, 0.8) 0%,
				rgba(205, 185, 145, 0.8) 50%,
				rgba(184, 162, 122, 0.8) 100%
			);
		}
		.site-page-loader {
			position: fixed;
			inset: 0;
			z-index: 99999;
			display: flex;
			align-items: center;
			justify-content: center;
			background: rgba(0, 0, 0, 0.08);
			opacity: 0;
			visibility: hidden;
			pointer-events: none;
			transition: opacity 0.18s ease, visibility 0.18s ease;
		}
		.site-page-loader.is-visible {
			opacity: 1;
			visibility: visible;
			pointer-events: auto;
		}
		.site-page-loader__panel {
			width: auto;
			min-height: 0;
			margin: 0;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			background: transparent;
			border: 0;
			box-shadow: none;
		}
		.site-page-loader__spinner {
			width: 32px;
			height: 32px;
			border: 3px solid rgba(80, 65, 42, 0.2);
			border-top-color: var(--text-muted);
			border-radius: 999px;
			animation: sitePageLoaderSpin 0.8s linear infinite;
		}
		.site-page-loader__text {
			display: none;
		}
		@keyframes sitePageLoaderSpin {
			to {
				transform: rotate(360deg);
			}
		}</style>
		<link rel="preload" href="template/images/logo.png" as="image">
		<style type="text/css" data-styled-jsx="">@keyframes outfitSpriteSteps{0%{background-position:0 0}to{background-position:calc(-1 * var(--sprite-end-x)) 0}}
		</style>
		<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
		<title>TibiaCOM Layout v3</title>
		<meta name="description" content="TibiaCOM, hellgrave exodus the best custom rpg server ever!">
		<meta name="author" content="TibiaCOM">
		<meta name="keywords" content="tibia,otserver,open tibia,exodus,mmorpg,ots,tibia server">
		<meta name="creator" content="TibiaCOM">
		<meta name="publisher" content="TibiaCOM">
		<meta name="robots" content="index, follow">
		<meta property="og:title" content="TibiaCOM">
		<meta property="og:description" content="Exodus, the best server ever made!">
		<meta property="og:url" content="https://opengamescommunity.com">
		<meta property="og:site_name" content="TibiaCOM">
		<meta property="og:locale" content="en_US">
		<meta property="og:image:width" content="1200">
		<meta property="og:image:height" content="630">
		<meta property="og:image:alt" content="TibiaCOM">
		<meta property="og:type" content="website">
		<link rel="shortcut icon" href="template/images/icon.webp">
		<link rel="icon" href="template/images/icon.webp">
		<link rel="apple-touch-icon" href="template/images/icon.webp">
		<meta name="next-size-adjust" content="">
		</head>
		<body class="inter_5901b7c6-module__ec5Qua__variable cinzel_6eb844d1-module__WIhn7G__variable font-sans antialiased bg-gray-950 text-gray-100">
		<div hidden=""></div>
		<div class="min-h-screen relative" style="background-image: url(&quot;template/images/background-default.png&quot;); background-size: cover; background-position: center top; background-attachment: fixed;">
		<?php
		$mobileBoostedCreature = mysql_select_single("
			SELECT `boostname`, `looktype`, `lookhead`, `lookbody`, `looklegs`, `lookfeet`, `lookaddons`, `raceid`
			FROM `boosted_creature`
			ORDER BY `date` DESC
			LIMIT 1;
		");

		$mobileBoostedBoss = mysql_select_single("
			SELECT `boostname`, `looktype`, `lookhead`, `lookbody`, `looklegs`, `lookfeet`, `lookaddons`, `raceid`
			FROM `boosted_boss`
			ORDER BY `date` DESC
			LIMIT 1;
		");

		if ($mobileBoostedCreature === false) {
			$mobileBoostedCreature = array('boostname' => 'Unknown', 'looktype' => 0, 'lookaddons' => 0, 'lookhead' => 0, 'lookbody' => 0, 'looklegs' => 0, 'lookfeet' => 0);
		}
		if ($mobileBoostedBoss === false) {
			$mobileBoostedBoss = array('boostname' => 'Unknown', 'looktype' => 0, 'lookaddons' => 0, 'lookhead' => 0, 'lookbody' => 0, 'looklegs' => 0, 'lookfeet' => 0);
		}
		?>
		<div class="dark-overlay fixed inset-0 pointer-events-none z-0 transition-colors duration-300"></div><header class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-gradient-to-b from-[var(--box-header-from)] to-[var(--box-header-to)] border-b border-[var(--box-header-border)] shadow-lg">
		<div class="flex items-center justify-between px-3 py-2">
		<button class="w-10 h-10 flex items-center justify-center rounded bg-[var(--box-bg)] border border-[var(--box-border)] text-[var(--box-header-text)] hover:bg-[var(--hover-bg)] transition-colors" aria-label="Open menu">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu w-5 h-5" aria-hidden="true">
		<path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg></button><a class="flex-shrink-0" href="/">
		<img alt="Logo" width="94" height="40" decoding="async" data-nimg="1" class="h-10 w-auto site-logo transition-[filter] duration-300" src="template/images/logo.png" style="color: transparent;"></a>
		<div class="flex items-center gap-2"><div class="flex items-center gap-1">
		<div class="relative w-10 h-10 rounded border-2 border-[#3b5068] overflow-visible" title="Boosted Boss: <?php echo htmlspecialchars($mobileBoostedBoss['boostname'], ENT_QUOTES, 'UTF-8'); ?>">
		<div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-[#3b5068] flex items-center justify-center z-10">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-skull w-2.5 h-2.5 text-[#f0e6d2]" aria-hidden="true">
		<path d="m12.5 17-.5-1-.5 1h1z"></path><path d="M15 22a1 1 0 0 0 1-1v-1a2 2 0 0 0 1.56-3.25 8 8 0 1 0-11.12 0A2 2 0 0 0 8 20v1a1 1 0 0 0 1 1z"></path><circle cx="15" cy="12" r="1"></circle><circle cx="9" cy="12" r="1"></circle></svg>
		</div>
		<div style="width: 40px; height: 40px; overflow: hidden;">
		<img src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo (int)$mobileBoostedBoss['looktype']; ?>&addons=<?php echo (int)$mobileBoostedBoss['lookaddons']; ?>&head=<?php echo (int)$mobileBoostedBoss['lookhead']; ?>&body=<?php echo (int)$mobileBoostedBoss['lookbody']; ?>&legs=<?php echo (int)$mobileBoostedBoss['looklegs']; ?>&feet=<?php echo (int)$mobileBoostedBoss['lookfeet']; ?>&v=<?php echo time(); ?>" alt="<?php echo htmlspecialchars($mobileBoostedBoss['boostname'], ENT_QUOTES, 'UTF-8'); ?>" style="width: 40px; height: 40px;">
		</div>
		</div>
		<div class="relative w-10 h-10 rounded border-2 border-[#3b5068] overflow-visible" title="Boosted Creature: <?php echo htmlspecialchars($mobileBoostedCreature['boostname'], ENT_QUOTES, 'UTF-8'); ?>">
		<div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-[#3b5068] flex items-center justify-center z-10">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-swords w-2.5 h-2.5 text-[#f0e6d2]" aria-hidden="true">
		<polyline points="14.5 17.5 3 6 3 3 6 3 17.5 14.5"></polyline><line x1="13" x2="19" y1="19" y2="13"></line><line x1="16" x2="20" y1="16" y2="20"></line><line x1="19" x2="21" y1="21" y2="19"></line><polyline points="14.5 6.5 18 3 21 3 21 6 17.5 9.5"></polyline>
		<line x1="5" x2="9" y1="14" y2="18"></line><line x1="7" x2="4" y1="17" y2="20"></line><line x1="3" x2="5" y1="19" y2="21"></line></svg>
		</div>
		<div style="width: 40px; height: 40px; overflow: hidden;">
		<img src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo (int)$mobileBoostedCreature['looktype']; ?>&addons=<?php echo (int)$mobileBoostedCreature['lookaddons']; ?>&head=<?php echo (int)$mobileBoostedCreature['lookhead']; ?>&body=<?php echo (int)$mobileBoostedCreature['lookbody']; ?>&legs=<?php echo (int)$mobileBoostedCreature['looklegs']; ?>&feet=<?php echo (int)$mobileBoostedCreature['lookfeet']; ?>" alt="<?php echo htmlspecialchars($mobileBoostedCreature['boostname'], ENT_QUOTES, 'UTF-8'); ?>" style="width: 40px; height: 40px;">
		</div>
		</div>
		</div>
		<?php
		$mobileAccountLoggedIn = user_logged_in() === true;
		$mobileAccountHref = $mobileAccountLoggedIn ? 'myaccount.php' : 'login_account.php';
		$mobileAccountLabel = $mobileAccountLoggedIn ? 'My Account' : 'Login';
		$mobileAccountClass = $mobileAccountLoggedIn
			? 'w-10 h-10 flex items-center justify-center rounded bg-gradient-to-b from-[#2e7d32] via-[#1b5e20] to-[#0d3d0d] border border-[#4caf50] text-[#c8e6c9] hover:from-[#388e3c] hover:via-[#2e7d32] hover:to-[#1b5e20] hover:border-[#66bb6a] transition-colors'
			: 'w-10 h-10 flex items-center justify-center rounded bg-[var(--box-bg)] border border-[var(--box-border)] text-[var(--box-header-text)] hover:bg-[var(--hover-bg)] transition-colors';
		?>
		<a class="<?php echo $mobileAccountClass; ?>" aria-label="<?php echo $mobileAccountLabel; ?>" href="<?php echo $mobileAccountHref; ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user w-5 h-5" aria-hidden="true">
		<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
		</a>
		</div>
		</div>
		</header>
		<div class="lg:hidden fixed inset-0 bg-black/60 z-40 transition-opacity duration-300 opacity-0 pointer-events-none"></div>
		<div class="lg:hidden fixed top-0 left-0 bottom-0 w-[260px] max-w-[85vw] bg-[#1a1a1a] z-50 transform transition-transform duration-300 ease-out overflow-y-auto -translate-x-full">
		<div class="sticky top-0 z-10 bg-gradient-to-b from-[var(--box-header-from)] to-[var(--box-header-to)] border-b border-[var(--box-header-border)] px-3 py-3 flex items-center justify-between">
		<a href="/"><img alt="logo" loading="lazy" width="75" height="32" decoding="async" data-nimg="1" class="h-8 w-auto site-logo transition-[filter] duration-300" src="template/images/logo.png" style="color: transparent;"></a>
		<button class="w-8 h-8 flex items-center justify-center rounded bg-[var(--box-bg)] border border-[var(--box-border)] text-[var(--text-muted)] hover:text-white transition-colors" aria-label="Close menu">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-4 h-4" aria-hidden="true"><path d="M18 6 6 18">
		</path><path d="m6 6 12 12"></path></svg>
		</button></div>
		<div class="p-2">
		<a class="flex items-center justify-center gap-1.5 w-full mb-3 bg-gradient-to-b from-[#2e7d32] via-[#1b5e20] to-[#0d3d0d] hover:from-[#388e3c] hover:via-[#2e7d32] hover:to-[#1b5e20] text-[#a5d6a7] hover:text-[#c8e6c9] py-2 text-xs font-semibold transition-all border border-[#4caf50] hover:border-[#66bb6a] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)]" href="/download">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download w-3.5 h-3.5" aria-hidden="true">
		<path d="M12 15V3"></path><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="m7 10 5 5 5-5"></path>
		</svg>Download</a>
		<div class="MenuBox-module__1GrUIG__box">
		<button class="MenuBox-module__1GrUIG__header" type="button">
		<h3 class="MenuBox-module__1GrUIG__headerTitle">News</h3>
		<span class="MenuBox-module__1GrUIG__headerIcon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-3.5 h-3.5" aria-hidden="true">
		<path d="m6 9 6 6 6-6"></path></svg>
		</span>
		</button>
		<div class="MenuBox-module__1GrUIG__bodyWrapper" style="grid-template-rows: 0fr;">
		<div class="MenuBox-module__1GrUIG__bodyOverflow">
		<div class="MenuBox-module__1GrUIG__body">
		<div class="MenuBox-module__1GrUIG__bodyTop"></div>
		<div class="MenuBox-module__1GrUIG__bodyMiddle">
		<nav class="MenuBox-module__1GrUIG__content"><a class="MenuBox-module__1GrUIG__menuLink" href="index.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Latest News</span>
		</a>
		</nav>
		</div>
		<div class="MenuBox-module__1GrUIG__bodyBottom"></div>
		</div></div></div></div>
		<div class="MenuBox-module__1GrUIG__box">
		<button class="MenuBox-module__1GrUIG__header" type="button">
		<h3 class="MenuBox-module__1GrUIG__headerTitle">Account</h3>
		<span class="MenuBox-module__1GrUIG__headerIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-3.5 h-3.5" aria-hidden="true">
		<path d="m6 9 6 6 6-6"></path></svg>
		</span>
		</button>
		<div class="MenuBox-module__1GrUIG__bodyWrapper" style="grid-template-rows: 0fr;">
		<div class="MenuBox-module__1GrUIG__bodyOverflow">
		<div class="MenuBox-module__1GrUIG__body">
		<div class="MenuBox-module__1GrUIG__bodyTop"></div>
		<div class="MenuBox-module__1GrUIG__bodyMiddle">
		<nav class="MenuBox-module__1GrUIG__content"><a class="MenuBox-module__1GrUIG__menuLink " href="register.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Create Account</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="login_account.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Lost Account?</span>
		</a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="myaccount.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Account Management</span>
		</a>
		</nav>
		</div>
		<div class="MenuBox-module__1GrUIG__bodyBottom"></div>
		</div>
		</div>
		</div>
		</div>
		<div class="MenuBox-module__1GrUIG__box">
		<button class="MenuBox-module__1GrUIG__header" type="button">
		<h3 class="MenuBox-module__1GrUIG__headerTitle">Library</h3>
		<span class="MenuBox-module__1GrUIG__headerIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-3.5 h-3.5" aria-hidden="true"><path d="m6 9 6 6 6-6"></path>
		</svg>
		</span>
		</button>
		<div class="MenuBox-module__1GrUIG__bodyWrapper" style="grid-template-rows: 0fr;">
		<div class="MenuBox-module__1GrUIG__bodyOverflow">
		<div class="MenuBox-module__1GrUIG__body">
		<div class="MenuBox-module__1GrUIG__bodyTop"></div>
		<div class="MenuBox-module__1GrUIG__bodyMiddle">
		<nav class="MenuBox-module__1GrUIG__content"><a class="MenuBox-module__1GrUIG__menuLink " href="serverinfo.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Server Info</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="houses.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Houses</span>
		</a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="market.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Market Items</span>
		<span class="MenuBox-module__1GrUIG__badge bg-green-500">NEW</span>
		</a>
		</nav>
		</div>
		<div class="MenuBox-module__1GrUIG__bodyBottom"></div>
		</div>
		</div>
		</div>
		</div>
		<div class="MenuBox-module__1GrUIG__box">
		<button class="MenuBox-module__1GrUIG__header" type="button">
		<h3 class="MenuBox-module__1GrUIG__headerTitle">Community</h3>
		<span class="MenuBox-module__1GrUIG__headerIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-3.5 h-3.5" aria-hidden="true">
		<path d="m6 9 6 6 6-6"></path></svg>
		</span>
		</button>
		<div class="MenuBox-module__1GrUIG__bodyWrapper" style="grid-template-rows: 0fr;">
		<div class="MenuBox-module__1GrUIG__bodyOverflow">
		<div class="MenuBox-module__1GrUIG__body">
		<div class="MenuBox-module__1GrUIG__bodyTop"></div>
		<div class="MenuBox-module__1GrUIG__bodyMiddle">
		<nav class="MenuBox-module__1GrUIG__content">
		<a class="MenuBox-module__1GrUIG__menuLink " href="search_character.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Characters</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="onlinelist.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Who is Online</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="highscores.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Highscores</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="ancestral_highscores.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Ancestral Highscores</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="killers.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Kill Statistics</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="deaths.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Latest Deaths</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="guilds.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Guilds</span></a>
		</nav>
		</div>
		<div class="MenuBox-module__1GrUIG__bodyBottom"></div>
		</div>
		</div>
		</div>
		</div>
		<div class="MenuBox-module__1GrUIG__box">
		<button class="MenuBox-module__1GrUIG__header" type="button">
		<h3 class="MenuBox-module__1GrUIG__headerTitle">Custom</h3>
		<span class="MenuBox-module__1GrUIG__headerIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-3.5 h-3.5" aria-hidden="true">
		<path d="m6 9 6 6 6-6"></path></svg>
		</span>
		</button>
		<div class="MenuBox-module__1GrUIG__bodyWrapper" style="grid-template-rows: 0fr;">
		<div class="MenuBox-module__1GrUIG__bodyOverflow">
		<div class="MenuBox-module__1GrUIG__body">
		<div class="MenuBox-module__1GrUIG__bodyTop"></div>
		<div class="MenuBox-module__1GrUIG__bodyMiddle">
		<nav class="MenuBox-module__1GrUIG__content">
		<a rel="noopener noreferrer" class="MenuBox-module__1GrUIG__menuLink " href="wiki.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Wikipedia</span>
		<span class="MenuBox-module__1GrUIG__badge bg-red-600">HOT</span></a>
		<?php if (!empty($config['enableGuildDynasty'])): ?>
				<a class="MenuBox-module__1GrUIG__menuLink " href="prism_dynasty.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Prism &amp; Dynasty</span>
		<span class="MenuBox-module__1GrUIG__badge bg-amber-600">NEW</span></a>
		<?php endif; ?>
		<a class="MenuBox-module__1GrUIG__menuLink " href="wiki_search.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Wiki Search</span>
		<span class="MenuBox-module__1GrUIG__badge bg-green-500">NEW</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="gems.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Gem System</span>
		<span class="MenuBox-module__1GrUIG__badge bg-green-500">NEW</span></a>
		</nav>
		</div>
		<div class="MenuBox-module__1GrUIG__bodyBottom"></div>
		</div>
		</div>
		</div>
		</div>
		<div class="MenuBox-module__1GrUIG__box">
		<button class="MenuBox-module__1GrUIG__header" type="button">
		<h3 class="MenuBox-module__1GrUIG__headerTitle">Support</h3>
		<span class="MenuBox-module__1GrUIG__headerIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-3.5 h-3.5" aria-hidden="true">
		<path d="m6 9 6 6 6-6"></path></svg>
		</span>
		</button>
		<div class="MenuBox-module__1GrUIG__bodyWrapper" style="grid-template-rows: 0fr;">
		<div class="MenuBox-module__1GrUIG__bodyOverflow">
		<div class="MenuBox-module__1GrUIG__body">
		<div class="MenuBox-module__1GrUIG__bodyTop"></div>
		<div class="MenuBox-module__1GrUIG__bodyMiddle">
		<nav class="MenuBox-module__1GrUIG__content">
		<a class="MenuBox-module__1GrUIG__menuLink " href="helpdesk.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Ticket</span></a>
		</nav>
		</div>
		<div class="MenuBox-module__1GrUIG__bodyBottom"></div>
		</div>
		</div>
		</div>
		</div>
		<div class="MenuBox-module__1GrUIG__box">
		<button class="MenuBox-module__1GrUIG__header" type="button">
		<h3 class="MenuBox-module__1GrUIG__headerTitle">Char Bazaar</h3>
		<span class="MenuBox-module__1GrUIG__headerIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-3.5 h-3.5" aria-hidden="true">
		<path d="m6 9 6 6 6-6"></path></svg>
		</span>
		</button>
		<div class="MenuBox-module__1GrUIG__bodyWrapper" style="grid-template-rows: 0fr;">
		<div class="MenuBox-module__1GrUIG__bodyOverflow">
		<div class="MenuBox-module__1GrUIG__body">
		<div class="MenuBox-module__1GrUIG__bodyTop"></div>
		<div class="MenuBox-module__1GrUIG__bodyMiddle">
		<nav class="MenuBox-module__1GrUIG__content">
		<a class="MenuBox-module__1GrUIG__menuLink " href="auctionchar.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Current Auctions</span></a>
		</nav>
		</div>
		<div class="MenuBox-module__1GrUIG__bodyBottom"></div>
		</div>
		</div>
		</div>
		</div>
		</div>
		</div>
		<div class="min-h-screen flex flex-col relative z-10">
		<div class="hidden lg:block">
		<div class="py-6 flex justify-center">
		<a href="/"><img alt="logo" width="300" height="128" decoding="async" data-nimg="1" class="h-32 w-auto site-logo transition-[filter] duration-300" src="template/images/logo.png" style="color: transparent;"></a>
		</div>
		</div>
		<div class="h-[72px] lg:hidden"></div>
		<div class="w-full max-w-[1280px] mx-auto px-3 flex-1">
		<div class="flex gap-3">
		<div class="hidden lg:block">
		<aside class="w-[226px] flex-shrink-0">
		<div class="LoginBox-module__27JC-W__box">
		<div class="LoginBox-module__27JC-W__header">
		<h3 class="LoginBox-module__27JC-W__headerTitle">Login</h3>
		</div>
		<div class="LoginBox-module__27JC-W__body">
		<div class="LoginBox-module__27JC-W__content">
		<?php if (user_logged_in() === true): ?>
			<p class="text-xs text-[var(--text-muted)] mb-2 truncate">Logged as <strong style="color: #2d5a3a;"><?php echo htmlspecialchars(!empty($user_data['email']) ? $user_data['email'] : $user_data['name'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
			<a class="LoginBox-module__27JC-W__btnPrimary" href="myaccount.php">My Account</a>
			<a class="LoginBox-module__27JC-W__btnSecondary cursor-pointer hover:opacity-80" style="background: linear-gradient(to bottom, #9b2f3a, #7c2430); border-color: #5f1b23; color: #fff;" href="logout.php">
				<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:inline-block;vertical-align:-2px;margin-right:4px;">
					<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" x2="9" y1="12" y2="12"></line>
				</svg>Log out
			</a>
		<?php else: ?>
			<a class="LoginBox-module__27JC-W__btnPrimary" href="login_account.php">Login</a>
			<a class="LoginBox-module__27JC-W__btnSecondary" href="register.php">Create Account</a>
		<?php endif; ?>
		</div>
		</div>
		</div>
		<a class="flex items-center justify-center gap-1.5 w-[206px] mx-[10px] mb-[15px] bg-gradient-to-b from-[#2e7d32] via-[#1b5e20] to-[#0d3d0d] hover:from-[#388e3c] hover:via-[#2e7d32] hover:to-[#1b5e20] text-[#a5d6a7] hover:text-[#c8e6c9] py-1.5 text-xs font-semibold transition-all border border-[#4caf50] hover:border-[#66bb6a] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)]" href="downloads.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download w-3.5 h-3.5" aria-hidden="true">
		<path d="M12 15V3"></path>
		<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="m7 10 5 5 5-5"></path>
		</svg>Download</a>
		<div class="MenuBox-module__1GrUIG__box">
		<button class="MenuBox-module__1GrUIG__header" type="button">
		<h3 class="MenuBox-module__1GrUIG__headerTitle">News</h3>
		<span class="MenuBox-module__1GrUIG__headerIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-3.5 h-3.5" aria-hidden="true">
		<path d="m6 9 6 6 6-6"></path></svg></span>
		</button>
		<div class="MenuBox-module__1GrUIG__bodyWrapper" style="grid-template-rows: 0fr;">
		<div class="MenuBox-module__1GrUIG__bodyOverflow">
		<div class="MenuBox-module__1GrUIG__body">
		<div class="MenuBox-module__1GrUIG__bodyTop">
		</div>
		<div class="MenuBox-module__1GrUIG__bodyMiddle">
		<nav class="MenuBox-module__1GrUIG__content">
		<a class="MenuBox-module__1GrUIG__menuLink" href="index.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Latest News</span></a>
		</nav>
		</div>
		<div class="MenuBox-module__1GrUIG__bodyBottom"></div>
		</div></div>
		</div></div>
		<div class="MenuBox-module__1GrUIG__box">
		<button class="MenuBox-module__1GrUIG__header" type="button">
		<h3 class="MenuBox-module__1GrUIG__headerTitle">Account</h3>
		<span class="MenuBox-module__1GrUIG__headerIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-3.5 h-3.5" aria-hidden="true">
		<path d="m6 9 6 6 6-6"></path></svg></span></button>
		<div class="MenuBox-module__1GrUIG__bodyWrapper" style="grid-template-rows: 0fr;">
		<div class="MenuBox-module__1GrUIG__bodyOverflow"><div class="MenuBox-module__1GrUIG__body">
		<div class="MenuBox-module__1GrUIG__bodyTop"></div>
		<div class="MenuBox-module__1GrUIG__bodyMiddle">
		<nav class="MenuBox-module__1GrUIG__content">
		<a class="MenuBox-module__1GrUIG__menuLink " href="register.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg><span>Create Account</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="myaccount.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg><span>Account Management</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="downloads.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg><span>Download Client</span></a>
		</nav></div><div class="MenuBox-module__1GrUIG__bodyBottom"></div>
		</div></div>
		</div></div>
		<div class="MenuBox-module__1GrUIG__box">
		<button class="MenuBox-module__1GrUIG__header" type="button">
		<h3 class="MenuBox-module__1GrUIG__headerTitle">Library</h3>
		<span class="MenuBox-module__1GrUIG__headerIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-up w-3.5 h-3.5" aria-hidden="true">
		<path d="m18 15-6-6-6 6"></path></svg></span>
		</button>
		<div class="MenuBox-module__1GrUIG__bodyWrapper" style="grid-template-rows: 0fr;">
		<div class="MenuBox-module__1GrUIG__bodyOverflow"><div class="MenuBox-module__1GrUIG__body">
		<div class="MenuBox-module__1GrUIG__bodyTop"></div>
		<div class="MenuBox-module__1GrUIG__bodyMiddle">
		<nav class="MenuBox-module__1GrUIG__content">
		<a class="MenuBox-module__1GrUIG__menuLink " href="serverinfo.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Server Info</span>
		</a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="houses.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Houses</span>
		</a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="market.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Market Items</span>
		<span class="MenuBox-module__1GrUIG__badge bg-green-500">NEW</span>
		</a>
		</nav></div>
		<div class="MenuBox-module__1GrUIG__bodyBottom">
		</div></div>
		</div></div>
		</div>
		<div class="MenuBox-module__1GrUIG__box"><button class="MenuBox-module__1GrUIG__header" type="button">
		<h3 class="MenuBox-module__1GrUIG__headerTitle">Community</h3>
		<span class="MenuBox-module__1GrUIG__headerIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-up w-3.5 h-3.5" aria-hidden="true">
		<path d="m18 15-6-6-6 6"></path></svg></span>
		</button>
		<div class="MenuBox-module__1GrUIG__bodyWrapper" style="grid-template-rows: 0fr;">
		<div class="MenuBox-module__1GrUIG__bodyOverflow"><div class="MenuBox-module__1GrUIG__body">
		<div class="MenuBox-module__1GrUIG__bodyTop"></div><div class="MenuBox-module__1GrUIG__bodyMiddle">
		<nav class="MenuBox-module__1GrUIG__content">
		<a class="MenuBox-module__1GrUIG__menuLink " href="search_character.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Characters</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="onlinelist.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Who is Online</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="highscores.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Highscores</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="ancestral_highscores.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Ancestral Highscores</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="killers.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Kill Statistics</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="deaths.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Latest Deaths</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="guilds.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Guilds</span></a>
		</nav></div>
		<div class="MenuBox-module__1GrUIG__bodyBottom"></div>
		</div></div>
		</div></div>
		<div class="MenuBox-module__1GrUIG__box">
		<button class="MenuBox-module__1GrUIG__header" type="button">
		<h3 class="MenuBox-module__1GrUIG__headerTitle">Custom</h3>
		<span class="MenuBox-module__1GrUIG__headerIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-3.5 h-3.5" aria-hidden="true">
		<path d="m6 9 6 6 6-6"></path></svg></span>
		</button>
		<div class="MenuBox-module__1GrUIG__bodyWrapper" style="grid-template-rows: 0fr;"><div class="MenuBox-module__1GrUIG__bodyOverflow">
		<div class="MenuBox-module__1GrUIG__body">
		<div class="MenuBox-module__1GrUIG__bodyTop"></div>
		<div class="MenuBox-module__1GrUIG__bodyMiddle">
		<nav class="MenuBox-module__1GrUIG__content">
		<a rel="noopener noreferrer" class="MenuBox-module__1GrUIG__menuLink " href="wiki.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Wikipedia</span>
		<span class="MenuBox-module__1GrUIG__badge bg-red-600">HOT</span>
		</a>
		<?php if (!empty($config['enableGuildDynasty'])): ?>
				<a class="MenuBox-module__1GrUIG__menuLink " href="prism_dynasty.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Prism &amp; Dynasty</span>
		<span class="MenuBox-module__1GrUIG__badge bg-amber-600">NEW</span></a>
		<?php endif; ?>
		<a class="MenuBox-module__1GrUIG__menuLink " href="wiki_search.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Wiki Search</span>
		<span class="MenuBox-module__1GrUIG__badge bg-green-500">NEW</span></a>
		<a class="MenuBox-module__1GrUIG__menuLink " href="gems.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Gem System</span>
		<span class="MenuBox-module__1GrUIG__badge bg-green-500">NEW</span></a>
		<nav>
		</div>
		<div class="MenuBox-module__1GrUIG__bodyBottom">
		</div></div>
		</div></div>
		</div>
		<div class="MenuBox-module__1GrUIG__box">
		<button class="MenuBox-module__1GrUIG__header" type="button">
		<h3 class="MenuBox-module__1GrUIG__headerTitle">Support</h3>
		<span class="MenuBox-module__1GrUIG__headerIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-3.5 h-3.5" aria-hidden="true">
		<path d="m6 9 6 6 6-6"></path></svg></span>
		</button>
		<div class="MenuBox-module__1GrUIG__bodyWrapper" style="grid-template-rows: 0fr;">
		<div class="MenuBox-module__1GrUIG__bodyOverflow">
		<div class="MenuBox-module__1GrUIG__body">
		<div class="MenuBox-module__1GrUIG__bodyTop"></div>
		<div class="MenuBox-module__1GrUIG__bodyMiddle">
		<nav class="MenuBox-module__1GrUIG__content">
		<a class="MenuBox-module__1GrUIG__menuLink " href="helpdesk.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg>
		<span>Ticket</span></a>
		</nav></div>
		<div class="MenuBox-module__1GrUIG__bodyBottom"></div>
		</div></div>
		</div></div>
		<div class="MenuBox-module__1GrUIG__box">
		<button class="MenuBox-module__1GrUIG__header" type="button">
		<h3 class="MenuBox-module__1GrUIG__headerTitle">Char Bazaar</h3>
		<span class="MenuBox-module__1GrUIG__headerIcon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-3.5 h-3.5" aria-hidden="true">
		<path d="m6 9 6 6 6-6"></path></svg></span>
		</button>
		<div class="MenuBox-module__1GrUIG__bodyWrapper" style="grid-template-rows: 0fr;">
		<div class="MenuBox-module__1GrUIG__bodyOverflow"><div class="MenuBox-module__1GrUIG__body">
		<div class="MenuBox-module__1GrUIG__bodyTop"></div><div class="MenuBox-module__1GrUIG__bodyMiddle">
		<nav class="MenuBox-module__1GrUIG__content">
		<a class="MenuBox-module__1GrUIG__menuLink " href="auctionchar.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-2.5 h-2.5" aria-hidden="true">
		<path d="m9 18 6-6-6-6"></path></svg><span>Current Auctions</span></a>
		</nav></div>
		<div class="MenuBox-module__1GrUIG__bodyBottom"></div>
		</div></div>
		</div></div>
		</aside></div>
		<div class="flex-1 min-w-0">
		<div class="TopBar-module__LYwH0W__topbar">
		<div class="TopBar-module__LYwH0W__topbarContent">
		<a href="https://discord.gg/D266yR8FsQ" target="_blank" rel="noopener noreferrer" class="TopBar-module__LYwH0W__topbarLink">
		<svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
		<path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"></path></svg>
		<span class="TopBar-module__LYwH0W__labelHideOnMobile">Discord</span>
		</a>
		<a href="/" target="_blank" rel="noopener noreferrer" class="TopBar-module__LYwH0W__topbarLink">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram w-4 h-4" aria-hidden="true">
		<rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line></svg>
		<span class="TopBar-module__LYwH0W__labelHideOnMobile">Instagram</span></a>
		<a class="TopBar-module__LYwH0W__topbarLink TopBar-module__LYwH0W__hideOnMobile" href="downloads.php">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download w-4 h-4" aria-hidden="true">
		<path d="M12 15V3"></path><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
		<path d="m7 10 5 5 5-5"></path></svg><span>Download</span></a>
		<div class="TopBar-module__LYwH0W__separator TopBar-module__LYwH0W__hideOnMobile"></div>
		<?php
		$serverOnline = false;

		if ($config['status']['status_check']) {
			$serverOnline = @fsockopen(
				$config['status']['status_ip'],
				$config['status']['status_port'],
				$errno,
				$errstr,
				1
			);

			if ($serverOnline) {
				fclose($serverOnline);
				$serverOnline = true;
			}
		}
		?>

		<a class="TopBar-module__LYwH0W__onlineCount" href="onlinelist.php">
			<svg xmlns="http://www.w3.org/2000/svg"
				 width="24"
				 height="24"
				 viewBox="0 0 24 24"
				 fill="none"
				 stroke="currentColor"
				 stroke-width="2"
				 stroke-linecap="round"
				 stroke-linejoin="round"
				 class="lucide lucide-users w-4 h-4 <?php echo $serverOnline ? 'text-[#4ade80]' : 'text-red-500'; ?>"
				 aria-hidden="true">
				<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
				<path d="M16 3.128a4 4 0 0 1 0 7.744"></path>
				<path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
				<circle cx="9" cy="7" r="4"></circle>
			</svg>

			<span class="TopBar-module__LYwH0W__onlineNumber <?php echo $serverOnline ? 'online' : 'offline'; ?>">
				<?php echo $serverOnline ? user_count_online() : '0'; ?>
			</span>

			<span class="TopBar-module__LYwH0W__onlineLabel TopBar-module__LYwH0W__labelHideOnMobile">
				<?php echo $serverOnline ? 'Online' : 'Offline'; ?>
			</span>
		</a>
		<div class="TopBar-module__LYwH0W__separator TopBar-module__LYwH0W__hideOnMobile"></div>
		<div class="hidden sm:flex items-center gap-0.5">
		</div></div>
		</div>
		<main>

