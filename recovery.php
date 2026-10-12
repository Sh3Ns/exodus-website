<?php
require_once 'engine/init.php';
logged_in_redirect();
include 'layout/overall/header.php';

function recovery_panel_start($title, $icon = 'key') {
	$icons = array(
		'key' => '<path d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z"></path><circle cx="16.5" cy="7.5" r=".5" fill="currentColor"></circle>',
		'alert' => '<path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"></path>',
	);
	$iconPath = isset($icons[$icon]) ? $icons[$icon] : $icons['key'];
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title"><span class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide w-4 h-4" aria-hidden="true"><?php echo $iconPath; ?></svg><?php echo hhb_tohtml($title); ?></span></h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body "><div class="PageBody-module__OAJIWq__content"><div class="space-y-3">
	<?php
}

function recovery_panel_end() {
	?></div></div></div></div><?php
}

function recovery_notice($title, $message, $type = 'info') {
	$class = ($type === 'success') ? 'text-[var(--color-success)]' : (($type === 'error') ? 'text-[#ef4444]' : 'text-[var(--text-muted)]');
	?>
	<div class="p-3 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-xs <?php echo $class; ?>">
		<div class="font-semibold mb-1"><?php echo hhb_tohtml($title); ?></div>
		<div><?php echo $message; ?></div>
	</div>
	<?php
}

$button_class = 'inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer';
$input_class = 'w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none';
$mode = (isset($_GET['mode']) && !empty($_GET['mode'])) ? getValue($_GET['mode']) : false;
$notice = false;

if (!$config['mailserver']['accountRecovery']) {
	recovery_panel_start('Account Recovery', 'alert');
	recovery_notice('System Disabled', 'The admin has disabled automatic account recovery.', 'error');
	recovery_panel_end();
	include 'layout/overall/footer.php';
	exit();
}

$email = (isset($_POST['email']) && !empty($_POST['email'])) ? getValue($_POST['email']) : false;
$character = (isset($_POST['character']) && !empty($_POST['character'])) ? getValue($_POST['character']) : false;
$password = (isset($_POST['password']) && !empty($_POST['password'])) ? getValue($_POST['password']) : false;
$username = (isset($_POST['username']) && !empty($_POST['username'])) ? getValue($_POST['username']) : false;

if (!empty($_POST)) {
	$status = true;
	if ($config['use_captcha'] && !verifyGoogleReCaptcha($_POST['g-recaptcha-response'])) {
		$status = false;
	}

	if (!$status) {
		$notice = array('Captcha Verification Failed', 'Captcha image verification was submitted wrong.', 'error');
	} else if (!$username) {
		$salt = '';
		if ($config['ServerEngine'] != 'TFS_03') {
			$password = sha1($password);
		} else {
			if (config('salt') === true) {
				$saltdata = mysql_select_single("SELECT `salt` FROM `accounts` WHERE `email`='$email' LIMIT 1;");
				if ($saltdata !== false) $salt .= $saltdata['salt'];
			}
			$password = sha1($salt.$password);
		}

		if ($config['ServerEngine'] != 'OTHIRE') {
			$user = mysql_select_single("SELECT `p`.`id` AS `player_id`, `a`.`name` FROM `players` `p` INNER JOIN `accounts` `a` ON `p`.`account_id` = `a`.`id` WHERE `p`.`name` = '$character' AND `a`.`email` = '$email' AND `a`.`password` = '$password' LIMIT 1;");
		} else {
			$user = mysql_select_single("SELECT `p`.`id` AS `player_id`, `a`.`id` AS `name` FROM `players` `p` INNER JOIN `accounts` `a` ON `p`.`account_id` = `a`.`id` WHERE `p`.`name` = '$character' AND `a`.`email` = '$email' AND `a`.`password` = '$password' LIMIT 1;");
		}

		if ($user !== false) {
			$mailer = new Mail($config['mailserver']);
			$title = "$_SERVER[HTTP_HOST]: Your username";
			$body = "<h1>Account Recovery</h1>";
			$body .= "<p>Your username is: <b>$user[name]</b><br>";
			$body .= "Enjoy your stay at ".$config['mailserver']['fromName'].". <br>";
			$body .= "<hr>I am an automatic no-reply e-mail. Any emails sent back to me will be ignored.</p>";
			$mailer->sendMail($email, $title, $body, $user['name']);
			$notice = array('Account Found', 'We have sent your username to <b>' . hhb_tohtml($email) . '</b>.<br>If you cannot find the email within 5 minutes, check your junk/trash inbox.', 'success');
		} else {
			$notice = array('Account Recovery Failed', 'Submitted data is wrong.', 'error');
		}
	} else if (!$password) {
		$newpass = rand(100000000, 999999999);
		$salt = '';
		if ($config['ServerEngine'] != 'TFS_03') {
			$password = sha1($newpass);
		} else {
			if (config('salt') === true) {
				$saltdata = mysql_select_single("SELECT `salt` FROM `accounts` WHERE `email`='$email' LIMIT 1;");
				if ($saltdata !== false) $salt .= $saltdata['salt'];
			}
			$password = sha1($salt.$newpass);
		}

		if ($config['ServerEngine'] != 'OTHIRE') {
			$user = mysql_select_single("SELECT `p`.`id` AS `player_id`, `a`.`name`, `a`.`id` AS `account_id` FROM `players` `p` INNER JOIN `accounts` `a` ON `p`.`account_id` = `a`.`id` WHERE `p`.`name` = '$character' AND `a`.`email` = '$email' AND `a`.`name` = '$username' LIMIT 1;");
		} else {
			$user = mysql_select_single("SELECT `p`.`id` AS `player_id`, `a`.`id` AS `account_id` FROM `players` `p` INNER JOIN `accounts` `a` ON `p`.`account_id` = `a`.`id` WHERE `p`.`name` = '$character' AND `a`.`email` = '$email' AND `a`.`id` = '$username' LIMIT 1;");
		}

		if ($user !== false) {
			mysql_update("UPDATE `accounts` SET `password`='$password' WHERE `id`='".$user['account_id']."' LIMIT 1;");
			$mailer = new Mail($config['mailserver']);
			$title = "$_SERVER[HTTP_HOST]: Your new password";
			$body = "<h1>Account Recovery</h1>";
			$body .= "<p>Your new password is: <b>$newpass</b><br>";
			$body .= "We recommend you to login and change it before you continue playing. <br>";
			$body .= "Enjoy your stay at ".$config['mailserver']['fromName'].". <br>";
			$body .= "<hr>I am an automatic no-reply e-mail. Any emails sent back to me will be ignored.</p>";
			$mailer->sendMail($email, $title, $body, isset($user['name']) ? $user['name'] : $username);
			$notice = array('Account Found', 'We have sent your new password to <b>' . hhb_tohtml($email) . '</b>.<br>If you cannot find the email within 5 minutes, check your junk/trash inbox.', 'success');
		} else {
			$notice = array('Account Recovery Failed', 'Submitted data is wrong.', 'error');
		}
	} else {
		$password = sha1($password);
		$user = mysql_select_single("SELECT `a`.`id`, `a`.`name`, `za`.`activekey` FROM `accounts` AS `a` INNER JOIN `znote_accounts` AS `za` ON `a`.`id` = `za`.`account_id` WHERE `a`.`name`='{$username}' AND `a`.`password`='{$password}' AND `a`.`email`='{$email}' LIMIT 1;");
		if ($user !== false) {
			$recoverylink = $config['site_url'] . '/recovery.php?a='.$user['id'].'&k='.$user['activekey'];
			$mailer = new Mail($config['mailserver']);
			$title = $config['site_title'].": Remove Two-Factor Authentication link";
			$body = "<h1>Remove Two-Factor Authentication</h1>";
			$body .= "<p>If you really want to remove Two-Factor Authentication, click on the following link:<br>";
			$body .= "<a href='$recoverylink' target='_BLANK'>$recoverylink</a><br>";
			$body .= "Enjoy your stay at ".$config['mailserver']['fromName'].". <br>";
			$body .= "<hr>I am an automatic no-reply e-mail. Any emails sent back to me will be ignored.</p>";
			$mailer->sendMail($email, $title, $body, $user['name']);
			$notice = array('Confirm Your Action Through Email', 'We have sent a confirmation link to <b>' . hhb_tohtml($email) . '</b>.<br>You must click the link before we remove Two-factor authentication.', 'success');
		} else {
			$notice = array('Account Recovery Failed', 'Submitted data is wrong.', 'error');
		}
	}
} else {
	$a = (isset($_GET['a']) && !empty($_GET['a'])) ? (int)$_GET['a'] : false;
	$k = (isset($_GET['k']) && !empty($_GET['k'])) ? (int)$_GET['k'] : false;
	if ($a !== false && $k !== false) {
		$account = mysql_select_single("SELECT `a`.`id`, `a`.`secret`, `za`.`secret` FROM `accounts` AS `a` INNER JOIN `znote_accounts` AS `za` ON `a`.`id`=`za`.`account_id` WHERE `a`.`id`='$a' AND `za`.`activekey`='$k' LIMIT 1;");
		if ($account !== false) {
			mysql_update("UPDATE `accounts` SET `secret`=NULL WHERE `id`='$a' LIMIT 1;");
			mysql_update("UPDATE `znote_accounts` SET `secret`=NULL WHERE `account_id`='$a' LIMIT 1;");
			$notice = array('Two-Factor Authentication Disabled', 'You may now login with just your username and password.', 'success');
		} else {
			$notice = array('Failed To Verify Your Request', 'We are unable to authenticate your account.', 'error');
		}
	}
}

recovery_panel_start('Account Recovery', ($notice && $notice[2] === 'error') ? 'alert' : 'key');
if ($notice !== false) {
	recovery_notice($notice[0], $notice[1], $notice[2]);
	?><a class="<?php echo $button_class; ?>" href="login_account.php">Back to Login</a><?php
} else if (in_array($mode, array('username', 'password', 'token'))) {
	?>
	<p class="text-xs text-[var(--text-muted)]">Fill in the information below and we will send the recovery details to your email.</p>
	<form action="" method="POST" class="space-y-3">
		<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs"><label for="email" class="font-medium">Email</label><input id="email" class="<?php echo $input_class; ?>" type="text" name="email" placeholder="name@mail.com"></div>
		<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs"><label for="character" class="font-medium">Character</label><input id="character" class="<?php echo $input_class; ?>" type="text" name="character"></div>
		<?php if ($mode === 'password' || $mode === 'token'): ?>
			<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs"><label for="username" class="font-medium">Username</label><input id="username" class="<?php echo $input_class; ?>" type="text" name="username"></div>
		<?php endif; ?>
		<?php if ($mode === 'username' || $mode === 'token'): ?>
			<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs"><label for="password" class="font-medium">Password</label><input id="password" class="<?php echo $input_class; ?>" type="password" name="password"></div>
		<?php endif; ?>
		<?php if ($config['use_captcha']): ?><div class="g-recaptcha" data-sitekey="<?php echo $config['captcha_site_key']; ?>"></div><?php endif; ?>
		<div class="flex justify-between gap-2 flex-wrap"><a class="<?php echo $button_class; ?>" href="recovery.php">Back</a><button class="<?php echo $button_class; ?>" type="submit">Recover Account</button></div>
	</form>
	<?php
} else {
	?>
	<p class="text-xs text-[var(--text-muted)]">Choose what you need to recover.</p>
	<div class="grid grid-cols-1 sm:grid-cols-<?php echo $config['twoFactorAuthenticator'] ? '3' : '2'; ?> gap-2">
		<a class="<?php echo $button_class; ?> w-full" href="?mode=username">Recover Username</a>
		<a class="<?php echo $button_class; ?> w-full" href="?mode=password">Recover Password</a>
		<?php if ($config['twoFactorAuthenticator']): ?><a class="<?php echo $button_class; ?> w-full" href="?mode=token">Remove Two-Factor</a><?php endif; ?>
	</div>
	<p class="text-[10px] text-[var(--text-muted)]">You will need access to the email attached to the account.</p>
	<?php
}
recovery_panel_end();

include 'layout/overall/footer.php';
?>
