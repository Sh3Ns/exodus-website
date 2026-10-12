<?php require_once 'engine/init.php';
protect_page();

if (empty($_POST) === false) {
	/* Token used for cross site scripting security */
	if (!isset($_POST['token']) || !Token::isValid($_POST['token'])) {
		$errors[] = 'Token is invalid.';
	}

	$required_fields = array('current_password', 'new_password', 'new_password_again');

	foreach($_POST as $key=>$value) {
		if (empty($value) && in_array($key, $required_fields) === true) {
			$errors[] = 'You need to fill in all fields.';
			break 1;
		}
	}

	if (empty($errors) === true) {
		$pass_data = user_data($session_user_id, 'password');
		//$pass_data['password'];
		// $_POST['']

		// .3 compatibility
		if ($config['ServerEngine'] == 'TFS_03' && $config['salt'] === true) {
			$salt = user_data($session_user_id, 'salt');
		}
		if (sha1($_POST['current_password']) === $pass_data['password'] || $config['ServerEngine'] == 'TFS_03' && $config['salt'] === true && sha1($salt['salt'].$_POST['current_password']) === $pass_data['password']) {
			if (trim($_POST['new_password']) !== trim($_POST['new_password_again'])) {
				$errors[] = 'Your new passwords do not match.';
			} else if (strlen($_POST['new_password']) < 6) {
				$errors[] = 'Your new passwords must be at least 6 characters.';
			} else if (strlen($_POST['new_password']) > 100) {
				$errors[] = 'Your new passwords must be less than 100 characters.';
			}
		} else {
			$errors[] = 'Your current password is incorrect.';
		}
	}

	if (empty($errors) === true) {
		if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10' || $config['ServerEngine'] == 'OTHIRE') {
			user_change_password($session_user_id, $_POST['new_password']);
		} else if ($config['ServerEngine'] == 'TFS_03') {
			user_change_password03($session_user_id, $_POST['new_password']);
		}
		header('Location: changepassword.php?success');
		exit();
	}
}

include 'layout/overall/header.php'; ?>

<div id="account-settings" class="mt-3">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-key w-4 h-4" aria-hidden="true"><path d="m15.5 7.5 2.3 2.3a1 1 0 0 0 1.4 0l2.1-2.1a1 1 0 0 0 0-1.4L19 4"></path><path d="m21 2-9.6 9.6"></path><circle cx="7.5" cy="15.5" r="5.5"></circle></svg>
						Change Password
					</span>
				</h2>
			</div>
		</div>

<?php
$password_message = '';
$password_success = (isset($_GET['success']) && empty($_GET['success']));
if ($password_success) {
	$password_message = 'Your password has been changed.<br>You will need to login again with the new password.';
	session_destroy();
} else if (empty($errors) === false) {
	$password_message = output_errors($errors);
}
?>
	<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<?php if (!empty($password_message)): ?>
					<div class="mb-3 text-xs font-semibold <?php echo $password_success ? 'text-[var(--color-success)]' : 'text-[#ef4444]'; ?>">
						<?php echo $password_message; ?>
					</div>
					<?php if ($password_success): ?>
						<meta http-equiv="refresh" content="2;url=index.php">
					<?php endif; ?>
				<?php endif; ?>
				<div class="flex justify-end">
						<a href="settings.php"><button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer">Back to Settings</button></a>
					</div>
				<form action="" method="post" class="space-y-3">
					<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
						<label for="current_password" class="font-medium">Current Password</label>
						<input class="w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none" type="password" name="current_password" id="current_password">
					</div>

					<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
						<label for="new_password" class="font-medium">New Password</label>
						<input class="w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none" type="password" name="new_password" id="new_password">
					</div>

					<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
						<label for="new_password_again" class="font-medium">Password Again</label>
						<input class="w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none" type="password" name="new_password_again" id="new_password_again">
					</div>

					<?php Token::create(); ?>

					<div class="flex justify-end">
						<button type="submit" value="Change password" class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer">Update Password</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php include 'layout/overall/footer.php'; ?>
