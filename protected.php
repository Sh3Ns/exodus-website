<?php
require_once 'engine/init.php';
// To direct users here, add: protect_page(); Here before loading header.
include 'layout/overall/header.php';

$is_logged_in = user_logged_in() === true;
$title = $is_logged_in ? 'Restricted Area' : 'Login Required';
$message = $is_logged_in
	? 'This area is protected and cannot be accessed directly.'
	: 'You need to be logged in before you can access this page.';
$icon_bg = $is_logged_in
	? 'bg-gradient-to-br from-amber-600 to-amber-800 border-amber-400'
	: 'bg-gradient-to-br from-red-700 to-red-900 border-red-500';
$icon_text = $is_logged_in ? 'text-amber-200' : 'text-red-200';
?>

<div class="">
	<div class="PageHeader-module__HZea0q__header ">
		<div class="PageHeader-module__HZea0q__headerContent">
			<h2 class="PageHeader-module__HZea0q__title">
				<span class="flex items-center gap-2">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock-keyhole w-4 h-4" aria-hidden="true">
						<circle cx="12" cy="16" r="1"></circle>
						<rect x="3" y="10" width="18" height="12" rx="2"></rect>
						<path d="M7 10V7a5 5 0 0 1 10 0v3"></path>
					</svg><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
				</span>
			</h2>
		</div>
	</div>
	<div class="PageBody-module__OAJIWq__body ">
		<div class="PageBody-module__OAJIWq__content">
			<div class="space-y-3">
				<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
					<div class="flex items-start gap-3 flex-1 min-w-0">
						<div class="flex-shrink-0">
							<div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg flex items-center justify-center shadow-lg border-2 <?php echo $icon_bg; ?>">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-alert w-6 h-6 sm:w-7 sm:h-7 <?php echo $icon_text; ?>" aria-hidden="true">
									<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
									<path d="M12 8v4"></path>
									<path d="M12 16h.01"></path>
								</svg>
							</div>
						</div>
						<div class="flex-1 min-w-0">
							<p class="text-sm font-bold <?php echo $is_logged_in ? 'text-[#c4a24d]' : 'text-[#ef4444]'; ?>">
								<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
							</p>
							<p class="text-xs text-[var(--text-muted)] mt-0.5">
								<?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
							</p>
						</div>
					</div>
					<div class="flex flex-col sm:flex-row gap-2 shrink-0">
						<?php if ($is_logged_in): ?>
							<a class="flex-1 sm:flex-initial" href="myaccount.php">
								<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] w-full sm:min-w-[130px] cursor-pointer">
									My Account
								</button>
							</a>
						<?php else: ?>
							<a class="flex-1 sm:flex-initial" href="login_account.php">
								<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] w-full sm:min-w-[130px] cursor-pointer">
									Login
								</button>
							</a>
							<a class="flex-1 sm:flex-initial" href="register.php">
								<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] w-full sm:min-w-[130px] cursor-pointer">
									Create Account
								</button>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
include 'layout/overall/footer.php'; ?>
