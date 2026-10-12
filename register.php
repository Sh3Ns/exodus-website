<?php
require_once 'engine/init.php';
logged_in_redirect();
include 'layout/overall/header.php';
require_once('config.countries.php');

if (empty($_POST) === false) {
	// $_POST['']
	$required_fields = array('username', 'password', 'password_again', 'email', 'selected');
	foreach($_POST as $key=>$value) {
		if (empty($value) && in_array($key, $required_fields) === true) {
			$errors[] = 'You need to fill in all fields.';
			break 1;
		}
	}

	// check errors (= user exist, pass long enough
	if (empty($errors) === true) {
		/* Token used for cross site scripting security */
		if (!Token::isValid($_POST['token'])) {
			$errors[] = 'Token is invalid.';
		}

		if ($config['use_captcha']) {
			if(!verifyGoogleReCaptcha($_POST['g-recaptcha-response'])) {
				$errors[] = "Please confirm that you're not a robot.";
			}
		}

		if (user_exist($_POST['username']) === true) {
			$errors[] = 'Sorry, that username already exist.';
		}

		// Don't allow "default admin names in config.php" access to register.
		$isNoob = in_array(strtolower($_POST['username']), $config['page_admin_access']) ? true : false;
		if ($isNoob) {
			$errors[] = 'This account name is blocked for registration.';
		}
		if ($config['client'] >= 830) {
			if (preg_match("/^[a-zA-Z0-9]+$/", $_POST['username']) == false) {
				$errors[] = 'Your account name can only contain characters a-z, A-Z and 0-9.';
			}
		} else {
			if (preg_match("/^[0-9]+$/", $_POST['username']) == false) {
				$errors[] = 'Your account can only contain numbers 0-9.';
			}
			if ((int)$_POST['username'] < 100000 || (int)$_POST['username'] > 999999999) {
				$errors[] = 'Your account number must be a value between 6-8 numbers long.';
			}
		}
		// name restriction
		$resname = explode(" ", $_POST['username']);
		foreach($resname as $res) {
			if(in_array(strtolower($res), $config['invalidNameTags'])) {
				$errors[] = 'Your username contains a restricted word.';
			}
			else if(strlen($res) == 1) {
				$errors[] = 'Too short words in your name.';
			}
		}
		if (strlen($_POST['username']) > 32) {
			$errors[] = 'Your account name must be less than 33 characters.';
		}
		// end name restriction
		if (strlen($_POST['password']) < 6) {
			$errors[] = 'Your password must be at least 6 characters.';
		}
		if (strlen($_POST['password']) > 29) {
			$errors[] = 'Your password must be less than 30 characters.';
		}
		if ($_POST['password'] !== $_POST['password_again']) {
			$errors[] = 'Your passwords do not match.';
		}
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
			$errors[] = 'A valid email address is required.';
		}
		if (user_email_exist($_POST['email']) === true) {
			$errors[] = 'That email address is already in use.';
		}
		if ($_POST['selected'] != 1) {
			$errors[] = 'You are only allowed to have an account if you accept the rules.';
		}
		if ($config['validate_IP'] === true) {
			if (validate_ip(getIP()) === false) {
				$errors[] = 'Failed to recognize your IP address. (Not a valid IPv4 address).';
			}
		}
		if (strlen($_POST['flag']) < 1) {
			$errors[] = 'Please choose country.';
		}
	}
}

?>
<div class="space-y-3">
<div class="">
<div class="PageHeader-module__HZea0q__header ">
<div class="PageHeader-module__HZea0q__headerContent">
<h2 class="PageHeader-module__HZea0q__title">
<span class="flex items-center gap-2">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-plus w-4 h-4" aria-hidden="true">
<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" x2="19" y1="8" y2="14"></line>
<line x1="22" x2="16" y1="11" y2="11"></line></svg>Create Account</span></h2></div></div>
<div class="PageBody-module__OAJIWq__body ">
<div class="PageBody-module__OAJIWq__content">
<?php
if (isset($_GET['success']) && empty($_GET['success'])) {
	if ($config['mailserver']['register']) {
		?>
		<h1>Email authentication required</h1>
		<p>We have sent you an email with an activation link to your submitted email address.</p>
		<p>If you can't find the email within 5 minutes, check your <strong>junk/trash inbox (spam filter)</strong> as it may be mislocated there.</p>
		<br></br>
		<?php
	} else echo 'Congratulations! Your account has been created. You may now login to create a character.';
} elseif (isset($_GET['authenticate']) && empty($_GET['authenticate'])) {
	// Authenticate user, fetch user id and activation key
	$auid = (isset($_GET['u']) && (int)$_GET['u'] > 0) ? (int)$_GET['u'] : false;
	$akey = (isset($_GET['k']) && (int)$_GET['k'] > 0) ? (int)$_GET['k'] : false;
	// Find a match
	$user = mysql_select_single("SELECT `id`, `active`, `active_email` FROM `znote_accounts` WHERE `account_id`='$auid' AND `activekey`='$akey' LIMIT 1;");
	if ($user !== false) {
		$user = (int) $user['id'];
		$active = (int) $user['active'];
		$active_email = (int) $user['active_email'];
		// Enable the account to login
		if ($active == 0 || $active_email == 0) {
			mysql_update("UPDATE `znote_accounts` SET `active`='1', `active_email`='1' WHERE `id`= $user LIMIT 1;");
		}
		echo '<h1>Congratulations!</h1> <p>Your account has been created. You may now login to create a character.</p>';
	} else {
		echo '<h1>Authentication failed</h1> <p>Either the activation link is wrong, or your account is already activated.</p>';
	}
} else {
	if (empty($_POST) === false && empty($errors) === true) {
		if ($config['log_ip']) {
			znote_visitor_insert_detailed_data(1);
		}

		//Register
		$register_data = array(
			'name'		=>	$_POST['username'],
			'password'	=>	$_POST['password'],
			'email'		=>	$_POST['email'],
			'created'	=>	time(),
			'ip'		=>	getIPLong(),
			'flag'		=> 	$_POST['flag']
		);

		user_create_account($register_data, $config['mailserver']);
		if (!$config['mailserver']['debug']) header('Location: register.php?success');
		exit();
		//End register

	} else if (empty($errors) === false){
		echo '<font color="red"><b>';
		echo output_errors($errors);
		echo '</b></font>';
	}
?>	
<form class="space-y-4" action="" method="post">

<div class="grid grid-cols-1  gap-4">
<div>
<label class="block text-xs font-medium mb-1">
<span class="flex items-center gap-1">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail w-3 h-3" aria-hidden="true">
<path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect>
</svg>Email</span></label>
<input placeholder="your@email.com" class="w-full px-3 py-2 text-xs bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent" type="text" name="email"></div>
</div>
<div class="grid grid-cols-1 gap-4">
    <div>
        <label class="block text-xs font-medium mb-1">
            <span class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user w-3 h-3" aria-hidden="true">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Account Name
            </span>
        </label>

        <input placeholder="Account Name" class="w-full px-3 py-2 text-xs bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent" type="text" name="username">
    </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div>
<label class="block text-xs font-medium mb-1"><span class="flex items-center gap-1">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock w-3 h-3" aria-hidden="true">
<rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>Password</span>
</label><div class="relative">
<input placeholder="Minimum 8 characters" class="w-full px-3 py-2 pr-8 text-xs bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent" type="password" name="password">
</div></div>
<div>
<label class="block text-xs font-medium mb-1"><span class="flex items-center gap-1">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock w-3 h-3" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
<path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>Confirm Password</span></label>
<input placeholder="Repeat your password" class="w-full px-3 py-2 text-xs bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent" type="password" name="password_again">
</div>
</div>
<div class="grid grid-cols-1  gap-4">
<div>
<label class="block text-xs font-medium mb-1">
<span class="flex items-center gap-1">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-flag w-3 h-3" aria-hidden="true">
                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                    <line x1="4" x2="4" y1="22" y2="15"></line>
                </svg>Country</span></label>
<select name="flag" class="px-2 py-1 text-xs bg-[var(--box-content-bg)] border border-[var(--box-inner-border)] rounded text-[var(--foreground)] shadow-[inset_0_1px_2px_rgba(0,0,0,0.1)] w-full sm:w-40"><option value="">(Please choose)</option>
			<?php
					foreach(array('pl', 'se', 'br', 'us', 'gb', ) as $c)
						echo '<option value="' . $c . '">' . $config['countries'][$c] . '</option>';

						echo '<option value="">----------</option>';
						foreach($config['countries'] as $code => $c)
							echo '<option value="' . $code . '">' . $c . '</option>';
					?></select></div>
</div>
<?php
			if ($config['use_captcha']) {
				?>
<div class="grid grid-cols-1  gap-4">
<div>
<label class="block text-xs font-medium mb-1">
<span class="flex items-center gap-1">
<div class="g-recaptcha" data-sitekey="<?php echo $config['captcha_site_key']; ?>"></div></span></label>
<input placeholder="your@email.com" class="w-full px-3 py-2 text-xs bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent" type="text" name="email"></div>
</div>
<?php
			}
			?>
<div class="pt-2 border-t border-[var(--box-inner-border)] space-y-3">
    
    <label class="flex items-start gap-3 text-xs cursor-pointer">
        <input class="w-4 h-4 mt-0.5 rounded border-[var(--box-inner-border)] bg-[var(--box-inner-bg)]" type="checkbox" name="confirmAgeContent">
        <span>
            I confirm that I am 13 years or older. I understand that I will not be able to access game content that has age restrictions for 18 years or older.
        </span>
    </label>

    <label class="flex items-start gap-3 text-xs cursor-pointer">
        <input class="w-4 h-4 mt-0.5 rounded border-[var(--box-inner-border)] bg-[var(--box-inner-bg)]" type="checkbox" id="agreeTermsCheckbox">
        <span>
            I have read and accept the 
            <a target="_blank" rel="noopener noreferrer" class="text-[var(--text-link)] hover:text-[var(--text-link-hover)]" href="rules.php">Game Rules</a>, 
            <a target="_blank" rel="noopener noreferrer" class="text-[var(--text-link)] hover:text-[var(--text-link-hover)]" href="/legal/agreement">Terms of Service</a> and 
            <a target="_blank" rel="noopener noreferrer" class="text-[var(--text-link)] hover:text-[var(--text-link-hover)]" href="/legal">Privacy Policy</a>
        </span>
    </label>

    <input type="hidden" name="selected" id="selectedRulesValue" value="0">
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('agreeTermsCheckbox');
    const hiddenInput = document.getElementById('selectedRulesValue');

    checkbox.addEventListener('change', function () {
        hiddenInput.value = this.checked ? '1' : '0';
    });
});
</script>
<?php
				/* Form file */
				Token::create();
			?>
<div class="flex justify-center">
    <input class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer" type="submit" value="Create Account">
</div>

</form>
</div></div>
</div></div>
<?php
}
include 'layout/overall/footer.php';
?>
