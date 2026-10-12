<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>

<div class="space-y-3">
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download w-4 h-4" aria-hidden="true"><path d="M12 15V3"></path><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="m7 10 5 5 5-5"></path></svg>Download Client
					</span>
				</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<p class="text-xs mb-4">Download our custom client to start playing on <?php echo htmlspecialchars($config['gameserver']['name'], ENT_QUOTES, 'UTF-8'); ?>. Choose your operating system below.</p>
				<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
					<div class="bg-[var(--box-inner-bg)] rounded p-4 border border-[var(--box-inner-border)] text-center">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-monitor w-10 h-10 mx-auto mb-3" aria-hidden="true">
							<rect width="20" height="14" x="2" y="3" rx="2"></rect><line x1="8" x2="16" y1="21" y2="21"></line><line x1="12" x2="12" y1="17" y2="21"></line>
						</svg>
						<h3 class="text-sm font-semibold mb-2">Windows</h3>
						<p class="text-[10px] mb-3">Windows 10/11 64-bit</p>
						<a href="<?php echo $config['client_download']; ?>" target="_blank" rel="noopener noreferrer">
							<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-4 py-1.5 text-xs w-full cursor-pointer">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download w-4 h-4 mr-2" aria-hidden="true"><path d="M12 15V3"></path><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
								<path d="m7 10 5 5 5-5"></path>
							</svg>Download</button>
						</a>
					</div>
					<div class="bg-[var(--box-inner-bg)] rounded p-4 border border-[var(--box-inner-border)] text-center">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-apple w-10 h-10 mx-auto mb-3" aria-hidden="true">
							<path d="M12 6.528V3a1 1 0 0 1 1-1h0"></path>
							<path d="M18.237 21A15 15 0 0 0 22 11a6 6 0 0 0-10-4.472A6 6 0 0 0 2 11a15.1 15.1 0 0 0 3.763 10 3 3 0 0 0 3.648.648 5.5 5.5 0 0 1 5.178 0A3 3 0 0 0 18.237 21"></path>
						</svg>
						<h3 class="text-sm font-semibold mb-2">macOS</h3>
						<p class="text-[10px] mb-3">Coming soon</p>
						<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-secondary-from)] via-[var(--btn-secondary-via)] to-[var(--btn-secondary-to)] text-[var(--btn-secondary-text)] border border-[var(--btn-secondary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-secondary-hover-from)] hover:via-[var(--btn-secondary-hover-via)] hover:to-[var(--btn-secondary-hover-to)] hover:text-[var(--btn-secondary-hover-text)] hover:border-[var(--btn-secondary-hover-border)] active:from-[var(--btn-secondary-active-from)] active:via-[var(--btn-secondary-active-via)] active:to-[var(--btn-secondary-active-to)] px-4 py-1.5 text-xs w-full" disabled="">Not Available
						</button>
					</div>
					<div class="bg-[var(--box-inner-bg)] rounded p-4 border border-[var(--box-inner-border)] text-center">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-terminal w-10 h-10 mx-auto mb-3" aria-hidden="true"><path d="M12 19h8"></path>
						<path d="m4 17 6-6-6-6"></path></svg>
						<h3 class="text-sm font-semibold mb-2">Linux</h3>
						<p class="text-[10px] mb-3">Coming soon</p>
						<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-secondary-from)] via-[var(--btn-secondary-via)] to-[var(--btn-secondary-to)] text-[var(--btn-secondary-text)] border border-[var(--btn-secondary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-secondary-hover-from)] hover:via-[var(--btn-secondary-hover-via)] hover:to-[var(--btn-secondary-hover-to)] hover:text-[var(--btn-secondary-hover-text)] hover:border-[var(--btn-secondary-hover-border)] active:from-[var(--btn-secondary-active-from)] active:via-[var(--btn-secondary-active-via)] active:to-[var(--btn-secondary-active-to)] px-4 py-1.5 text-xs w-full" disabled="">Not Available</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield w-4 h-4" aria-hidden="true">
							<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
						</svg>System Requirements
					</span>
				</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
					<div>
						<h3 class="font-semibold text-sm mb-3">Minimum</h3>
						<ul class="text-xs space-y-2">
							<li>
								<span class="font-medium">OS:</span> Windows 10 64-bit
							</li>
							<li>
								<span class="font-medium">CPU:</span> Intel Core i3 or equivalent
							</li>
							<li>
								<span class="font-medium">RAM:</span> 4 GB
							</li>
							<li>
								<span class="font-medium">GPU:</span> DirectX 11 compatible
							</li>
							<li>
								<span class="font-medium">Storage:</span> 2 GB available space
							</li>
							<li>
								<span class="font-medium">Network:</span> Broadband connection
							</li>
						</ul>
					</div>
					<div>
						<h3 class="font-semibold text-sm mb-3">Recommended</h3>
						<ul class="text-xs space-y-2">
							<li>
								<span class="font-medium">OS:</span> Windows 11 64-bit
							</li>
							<li>
								<span class="font-medium">CPU:</span> Intel Core i5 or better
							</li>
							<li>
								<span class="font-medium">RAM:</span> 8 GB
							</li>
							<li>
								<span class="font-medium">GPU:</span> NVIDIA GTX 1050 or better
							</li>
							<li>
								<span class="font-medium">Storage:</span> SSD with 4 GB space
							</li>
							<li>
								<span class="font-medium">Network:</span> Stable broadband connection
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-zap w-4 h-4" aria-hidden="true">
							<path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"></path>
						</svg>Client Features
					</span>
				</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
					<li class="flex items-center gap-2">
						<span class="w-1.5 h-1.5 bg-[var(--color-success)] rounded-full"></span>Anti-aliased graphics
					</li>
					<li class="flex items-center gap-2">
						<span class="w-1.5 h-1.5 bg-[var(--color-success)] rounded-full"></span>Custom UI themes (Mobile/Retro)
					</li>
					<li class="flex items-center gap-2">
						<span class="w-1.5 h-1.5 bg-[var(--color-success)] rounded-full"></span>Built-in minimap
					</li>
					<li class="flex items-center gap-2">
						<span class="w-1.5 h-1.5 bg-[var(--color-success)] rounded-full"></span>Action bar system
					</li>
					<li class="flex items-center gap-2">
						<span class="w-1.5 h-1.5 bg-[var(--color-success)] rounded-full"></span>Auto-loot feature
					</li>
					<li class="flex items-center gap-2">
						<span class="w-1.5 h-1.5 bg-[var(--color-success)] rounded-full"></span>Quest tracker
					</li>
					<li class="flex items-center gap-2">
						<span class="w-1.5 h-1.5 bg-[var(--color-success)] rounded-full"></span>In-game market browser
					</li>
					<li class="flex items-center gap-2">
						<span class="w-1.5 h-1.5 bg-[var(--color-success)] rounded-full"></span>And a lot of content!
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<?php
include 'layout/overall/footer.php'; ?>
