<?php 
require_once 'engine/init.php';
include 'layout/overall/header.php'; ?>

<div class="space-y-3"><div class="">
<div class="PageHeader-module__HZea0q__header ">
<div class="PageHeader-module__HZea0q__headerContent">
<h2 class="PageHeader-module__HZea0q__title">
<span class="flex items-center gap-2">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-in w-4 h-4" aria-hidden="true">
<path d="m10 17 5-5-5-5"></path><path d="M15 12H3"></path><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path></svg>Login</span></h2>
</div></div><div class="PageBody-module__OAJIWq__body "><div class="PageBody-module__OAJIWq__content">
<p class="text-xs mb-4">Access your account to manage your characters and participate in the community.</p>
<form class="space-y-3" action="login.php" method="post">
<div class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-4">
<label class="block text-xs font-medium"><span class="flex items-center gap-1">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user w-3 h-3" aria-hidden="true">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>Account Name</span>
</label>
<label class="hidden md:block text-xs font-medium">
<span class="flex items-center gap-1">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-key-round w-3 h-3" aria-hidden="true">
<path d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z"></path><circle cx="16.5" cy="7.5" r=".5" fill="currentColor">
</circle></svg>Password</span></label><div class="hidden md:block w-[72px]"></div></div>
<div class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-4 items-start"><div>
<input placeholder="Account Name" class="w-full px-3 py-2 text-xs bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent" type="text" name="username" id="login_username"></div>
<div><label class="block md:hidden text-xs font-medium mb-1">
<span class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock w-3 h-3" aria-hidden="true">
<rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>Password</span></label>
<input placeholder="Your password" class="w-full px-3 py-2 text-xs bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent" type="password" name="password" id="login_password">
</div>
<?php if ($config['twoFactorAuthenticator']): ?>
<div><label class="block md:hidden text-xs font-medium mb-1">
<span class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock w-3 h-3" aria-hidden="true">
<rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>Password</span></label>
<input placeholder="Your password" class="w-full px-3 py-2 text-xs bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent" type="password" name="password" id="login_password">
</div>
<?php endif; ?>
<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 text-[11px] py-2 cursor-pointer" 
type="submit" class="submitButton">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-in w-3 h-3 mr-1" aria-hidden="true">
<path d="m10 17 5-5-5-5"></path><path d="M15 12H3"></path><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path></svg>Login</button>
<?php
				/* Form file */
				Token::create();
			?>
</div><a class="inline-flex items-center gap-1 text-[10px] text-[var(--text-link)] hover:text-[var(--text-link-hover)] transition-colors" href="recovery.php?mode=password">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-key-round w-3 h-3" aria-hidden="true">
<path d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z"></path><circle cx="16.5" cy="7.5" r=".5" fill="currentColor">
</circle></svg>Forgot password</a> <a class="inline-flex items-center gap-1 text-[10px] text-[var(--text-link)] hover:text-[var(--text-link-hover)] transition-colors" href="recovery.php?mode=username">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user w-3 h-3" aria-hidden="true">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>Forgot Username?</a></form>

</div></div></div></div>

<?php include 'layout/overall/footer.php'; ?>