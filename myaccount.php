<?php require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php';

function render_myaccount_notice($title, $message, $type = 'info') {
	$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
	$text_class = ($type === 'error') ? 'text-red-600' : (($type === 'success') ? 'text-[#22c55e]' : 'text-[var(--text-muted)]');

	echo '
		<div class="">
			<div class="PageHeader-module__HZea0q__header ">
				<div class="PageHeader-module__HZea0q__headerContent">
					<h2 class="PageHeader-module__HZea0q__title">
						<span class="flex items-center gap-2">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-exclamation-point w-4 h-4" aria-hidden="true">
								<path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path>
							</svg>' . $title . '
						</span>
					</h2>
				</div>
			</div>
			<div class="PageBody-module__OAJIWq__body ">
				<div class="PageBody-module__OAJIWq__content">
					<div class="space-y-3">
						<div class="text-center py-4">
							<div class="text-xs ' . $text_class . '">' . $message . '</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br>
	';
}

#region CANCEL CHARACTER DELETE
$undelete_id = @$_GET['cancel_delete_id'];
if($undelete_id) {
	$undelete_id = (int)$undelete_id;
	$undelete_q1 = mysql_select_single("
		SELECT 
			`character_name` 
		FROM `znote_deleted_characters` 
		WHERE `done` = 0 
		AND `id` = {$undelete_id} 
		AND `original_account_id` = {$session_user_id} 
		AND NOW() < `time`
	");
	if($undelete_q1) {
		mysql_delete('DELETE FROM `znote_deleted_characters` WHERE `id` = ' . $undelete_id);
		render_myaccount_notice('Information', 'Pending delete of <b>' . htmlspecialchars($undelete_q1['character_name'], ENT_QUOTES, 'UTF-8') . '</b> has been successfully canceled.', 'success');
	}
}
#endregion

// Variable used to check if main page should be rendered after handling POST (Change comment page)
$render_page = true;

// Handle GET (verify email)
if (isset($_GET['authenticate']) && $config['mailserver']['myaccount_verify_email']):
	// If we need to process email verification
	if (isset($_GET['u']) && isset($_GET['k'])) {
		// Authenticate user, fetch user id and activation key
		$auid = (isset($_GET['u']) && (int)$_GET['u'] > 0) ? (int)$_GET['u'] : false;
		$akey = (isset($_GET['k']) && (int)$_GET['k'] > 0) ? (int)$_GET['k'] : false;
		if ($auid !== false && $akey !== false) {
			// Find a match
			$user = mysql_select_single("SELECT `id`, `active`, `active_email` FROM `znote_accounts` WHERE `account_id`='{$auid}' AND `activekey`='{$akey}' LIMIT 1;");
			if ($user !== false) {
				$user = (int) $user['id'];
				$active = (int) $user['active'];
				$active_email = (int) $user['active_email'];
				$verify_points = ($active_email == 0 && $config['mailserver']['verify_email_points'] > 0)
					? ", `points` = `points` + {$config['mailserver']['verify_email_points']}"
					: '';
				// Enable the account to login
				if ($active == 0 || $active_email == 0) {
					$new_activeKey = rand(100000000, 999999999);
					mysql_update("UPDATE `znote_accounts` SET `active`='1', `active_email`='1', `activekey`='{$new_activeKey}' {$verify_points} WHERE `id`= {$user} LIMIT 1;");
				}
				$email_message = '<p>Your email has been verified.</p>';
				if ($verify_points !== '') {
					$email_message .= "<p>As thanks for having a verified email, you have received <a href='shop.php'>" . (int)$config['mailserver']['verify_email_points'] . " shop points</a>!</p>";
				}
				render_myaccount_notice('Congratulations!', $email_message, 'success');
				$user_znote_data['active_email'] = 1;
				// Todo: Bonus points as thanks for verifying email
			} else {
				render_myaccount_notice('Authentication failed', '<p>Either the activation link is wrong, or your account is already activated.</p>', 'error');
			}
		} else {
			render_myaccount_notice('Authentication failed', '<p>Either the activation link is wrong, or your account is already activated.</p>', 'error');
		}
	} else { // We need to send email verification
		$verify_account_id = (int)$session_user_id;
		$user = mysql_select_single("SELECT `id`, `activekey`, `active_email` FROM `znote_accounts` WHERE `account_id`='{$verify_account_id}' LIMIT 1;");
		if ($user !== false) {
			$thisurl = config('site_url') . "/myaccount.php";
			$thisurl .= "?authenticate&u=".$verify_account_id."&k=".$user['activekey'];

			$mailer = new Mail($config['mailserver']);

			$title = "Please authenticate your email at {$_SERVER['HTTP_HOST']}.";

			$body = "<h1>Please click on the following link to authenticate your account:</h1>";
			$body .= "<p><a href='{$thisurl}'>{$thisurl}</a></p>";
			$body .= "<p>Thank you for verifying your email and enjoy your stay at {$config['mailserver']['fromName']}.</p>";
			$body .= "<hr><p>I am an automatic no-reply e-mail. Any emails sent back to me will be ignored.</p>";

			$user_name = ($config['ServerEngine'] !== 'OTHIRE') ? $user_data['name'] : $user_data['id'];
			//echo "<h1>" . $title . "<h1>" . $body;
			$mailer->sendMail($user_data['email'], $title, $body, $user_name);
			render_myaccount_notice(
				'Email authentication sent',
				'<p>We have sent you an email with a verification link to your email address: <strong>' . htmlspecialchars($user_data['email'], ENT_QUOTES, 'UTF-8') . '</strong></p><p>If you can\'t find the email within 5 minutes, check your <strong>junk/trash inbox (spam filter)</strong> as it may be misplaced there.</p>',
				'success'
			);
		} else {
			render_myaccount_notice('Authentication failed', '<p>Failed to verify user when trying to send a verification email.</p>', 'error');
		}
	}
endif;

// Handle POST
if (!empty($_POST['selected_character'])) {
	if (!empty($_POST['action'])) {
		// Validate token
		if (!Token::isValid($_POST['token'])) {
			exit();
		}
		// Sanitize values
		$action = getValue($_POST['action']);
		$char_name = getValue($_POST['selected_character']);

		// Handle actions
		switch($action) {
			// Change character comment PAGE2 (Success).
			case 'update_comment':
				if ((int)user_character_account_id($char_name) === $session_user_id) {
					user_update_comment(user_character_id($char_name), getValue($_POST['comment']));
					render_myaccount_notice('Information', 'Successfully updated comment.', 'success');
				}
				break;
			// end

			// Hide character
			case 'toggle_hide':
				$hide = (user_character_hide($char_name) == 1 ? 0 : 1);
				if ((int)user_character_account_id($char_name) === $session_user_id) {
					user_character_set_hide(user_character_id($char_name), $hide);
				}
				break;
			// end

			// DELETE character
			case 'delete_character':
				if ((int)user_character_account_id($char_name) === $session_user_id) {
					$charid = user_character_id($char_name);
					if ($charid !== false) {
						if (!user_is_online_10($charid)) {
							if (guild_leader_gid($charid) === false) user_delete_character_soft($charid);
							else render_myaccount_notice('Information', 'Character is leader of a guild, you must disband the guild or change leadership before deleting character.', 'error');
						} else render_myaccount_notice('Information', 'Character must be offline first.', 'error');
					}
				}
				break;
			// end

			// CHANGE character name
			case 'change_name':
				$errors = array();
				$oldname = $char_name;
				$newname = isset($_POST['newName']) ? getValue($_POST['newName']) : '';

				$player = false;
				$player = mysql_select_single("SELECT `id`, `account_id` FROM `players` WHERE `name` = '$oldname'");
				if ($player === false) {
					render_myaccount_notice('Information', 'Character could not be found.', 'error');
					break;
				}
				$player['online'] = (user_is_online_10($player['id'])) ? 1 : 0;

				// Check if user is online
				if ($player['online'] == 1) {
					$errors[] = 'Character must be offline first.';
				}

				// Check if player has bough ticket
				$accountId = $player['account_id'];
				$order = mysql_select_single("SELECT `id`, `account_id` FROM `znote_shop_orders` WHERE `type`='4' AND `account_id` = '$accountId' LIMIT 1;");
				if ($order === false) {
					$errors[] = 'Did not find any name change tickets, buy them in our <a href="shop.php">shop!</a>';
				}

				// Check if player and account matches
				if ($order !== false && ($session_user_id != $accountId || $session_user_id != $order['account_id'])) {
					if (empty($errors)) {
						$errors[] = 'Failed to sync your account. :|';
					}
				}

				if (empty($errors)) {
					$newname = validate_name($newname);
					if ($newname === false) {
						$errors[] = 'Your name can not contain more than 2 words.';
					} else {
						if (empty($newname)) {
							$errors[] = 'Please enter a name!';
						} else if (user_character_exist($newname) !== false) {
							$errors[] = 'Sorry, that character name already exist.';
						} else if (!preg_match("/^[a-zA-Z_ ]+$/", $newname)) {
							$errors[] = 'Your name may only contain a-z, A-Z and spaces.';
						} else if (strlen($newname) < $config['minL'] || strlen($newname) > $config['maxL']) {
							$errors[] = 'Your character name must be between ' . $config['minL'] . ' - ' . $config['maxL'] . ' characters long.';
						} else if (!ctype_upper($newname[0])) {
							$errors[] = 'The first letter of a name has to be a capital letter!';
						}

						// name restriction
						$resname = explode(" ", $_POST['newName']);
						foreach($resname as $res) {
							if(in_array(strtolower($res), $config['invalidNameTags'])) {
								$errors[] = 'Your username contains a restricted word.';
							} else if(strlen($res) == 1) {
								$errors[] = 'Too short words in your name.';
							}
						}
					}
				}

				if (!empty($newname) && empty($errors)) {
					render_myaccount_notice('Information', 'You have successfully changed your character name to <b>' . htmlspecialchars($newname, ENT_QUOTES, 'UTF-8') . '</b>.', 'success');
					mysql_update("UPDATE `players` SET `name`='$newname' WHERE `id`='".$player['id']."' LIMIT 1;");
					mysql_delete("DELETE FROM `znote_shop_orders` WHERE `id`='".$order['id']."' LIMIT 1;");

				} else if (!empty($errors)) {
					render_myaccount_notice('Information', output_errors($errors), 'error');
				}

				break;
			// end

			// Change character sex
			case 'change_gender':
				if ((int)user_character_account_id($char_name) === $session_user_id) {
					$char_id = (int)user_character_id($char_name);
					$account_id = user_character_account_id($char_name);

					$chr_data['online'] = user_is_online_10($char_id) ? 1 : 0;
					if ($chr_data['online'] != 1) {
						// Verify that we are not messing around with data
						if ($account_id != $user_data['id']) {
							render_myaccount_notice('Information', 'Something went wrong, try relogging.', 'error');
							break;
						}

						// Fetch character tickets
						$tickets = shop_account_gender_tickets($account_id);
						if ($tickets !== false || $config['free_sex_change'] == true) {
							// They are allowed to change gender
							$last = false;
							$infinite = ($config['free_sex_change'] == true);
							$tks = 0;
							// Do we have any infinite tickets?
							if ($tickets !== false) {
								foreach ($tickets as $ticket) {
									if ($ticket['count'] == 0) $infinite = true;
									else if ($ticket['count'] > 0 && $infinite === false) $tks += (int)$ticket['count'];
								}
								if ($infinite === true) $tks = 0;
								$dbid = (int)$tickets[0]['id'];
								// If they dont have unlimited tickets, remove a count from their ticket.
								if ($tickets[0]['count'] > 1) { // Decrease count
									$tks--;
									$tkr = ((int)$tickets[0]['count'] - 1);
									shop_update_row_count($dbid, $tkr);
								} else if ($tickets[0]['count'] == 1) { // Delete record
									shop_delete_row_order($dbid);
									$tks--;
								}
							}

							// Change character gender:
							//
							user_character_change_gender($char_name);
							$gender_message = 'You have successfully changed gender on character <b>'. htmlspecialchars($char_name, ENT_QUOTES, 'UTF-8') .'</b>.';
							if ($tks > 0) {
								$gender_message .= '<br>You have '. (int)$tks .' gender change tickets left.';
							} else if ($infinite !== true) {
								$gender_message .= '<br>You are out of tickets.';
							}
							render_myaccount_notice('Information', $gender_message, 'success');
						} else render_myaccount_notice('Information', 'You don\'t have any character gender tickets, buy them in the <a href="shop.php">SHOP</a>!', 'error');
					} else render_myaccount_notice('Information', 'Your character must be offline.', 'error');
				}
				break;
			// end

			// Change character comment PAGE1:
			case 'change_comment':
				$render_page = false; // Regular "myaccount" page should not render
				if ((int)user_character_account_id($char_name) === $session_user_id) {
					$comment_data = user_znote_character_data(user_character_id($char_name), 'comment');
					?>
					<div class=""><div class="PageHeader-module__HZea0q__header "><div class="PageHeader-module__HZea0q__headerContent">
					<h2 class="PageHeader-module__HZea0q__title"><span class="flex items-center gap-2">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-exclamation-point w-4 h-4" aria-hidden="true">
					<path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>Change comment on:</span>
					</h2></div></div><div class="PageBody-module__OAJIWq__body "><div class="PageBody-module__OAJIWq__content"><div class="space-y-3">
					<div class="text-center py-4">
					<!-- Changing comment MARKUP -->
					
					<form action="" method="post" class="space-y-3">
					<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
						<input class="w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none" type="hidden" name="action" value="update_comment">
						<input class="w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none" type="text" name="selected_character" id="new_email" value="<?php echo htmlspecialchars($char_name, ENT_QUOTES, 'UTF-8'); ?>" readonly="readonly">
					</div>

					<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
						<label for="flag_select" class="font-medium">Comment:</label>
						<textarea name="comment" class="w-full px-3 py-1.5 pr-10 text-xs bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]" cols="70" rows="10"><?php echo htmlspecialchars($comment_data['comment'], ENT_QUOTES, 'UTF-8'); ?></textarea>
					</div>

					<?php Token::create(); ?>

					<div class="flex justify-end">
						<button type="submit" value="Update Comment" class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer">Update Settings</button>
					</div>
				</form>
					</div></div></div></div></div>
					<?php
				}
				break;
			//end
		}
	}
}

if ($render_page) {
	$char_count = user_character_list_count($session_user_id);
	$pending_delete = user_pending_deletes($session_user_id);
	if ($pending_delete) {
		$pending_delete_rows = '';

		foreach($pending_delete as $delete) {
			if(new DateTime($delete['time']) > new DateTime()) {
				$pending_delete_rows .= '
					<div class="items-center py-3 border-b border-[var(--box-inner-border)] last:border-b-0" style="display: grid; grid-template-columns: minmax(0, 1fr) 150px; gap: 16px;">
						<p class="text-xs text-[var(--text-muted)]" style="margin: 0;"><b>CAUTION!</b> Your character with name <b>' . htmlspecialchars($delete['character_name'], ENT_QUOTES, 'UTF-8') . ' will be deleted on ' . htmlspecialchars($delete['time'], ENT_QUOTES, 'UTF-8') . '</b>.</p>
						<a href="myaccount.php?cancel_delete_id=' . (int)$delete['id'] . '" style="width: 150px;">
							<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer" style="width: 150px;">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings w-3 h-3 mr-1" aria-hidden="true">
								<path d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915"></path><circle cx="12" cy="12" r="3"></circle>
								</svg>Cancel
							</button>
						</a>
					</div>
				';
			} else {
				user_delete_character(user_character_id($delete['character_name']));
				mysql_update('UPDATE `znote_deleted_characters` SET `done` = 1 WHERE `id` = '. $delete['id']. '');
				render_myaccount_notice('Information', '<b>Character ' . htmlspecialchars($delete['character_name'], ENT_QUOTES, 'UTF-8') . ' has been deleted</b>. This operation was requested by owner of this account.', 'success');
				$char_count--;
			}
		}

		if ($pending_delete_rows !== '') {
			echo '
			<div class=""><div class="PageHeader-module__HZea0q__header "><div class="PageHeader-module__HZea0q__headerContent">
					<h2 class="PageHeader-module__HZea0q__title"><span class="flex items-center gap-2">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-exclamation-point w-4 h-4" aria-hidden="true">
					<path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>Information</span>
					</h2></div></div><div class="PageBody-module__OAJIWq__body "><div class="PageBody-module__OAJIWq__content"><div class="space-y-3">
					' . $pending_delete_rows . '
					</div></div></div></div><br></br>
			';
		}
	}

	$open_tickets = mysql_select_multi("SELECT `id`, `subject`, `creation`, `status` FROM `znote_tickets` WHERE `owner` = " . (int)$session_user_id . " AND `status` != 'CLOSED' ORDER BY `creation` DESC;");
	if ($open_tickets !== false) {
		?>
		<div class="">
			<div class="PageHeader-module__HZea0q__header ">
				<div class="PageHeader-module__HZea0q__headerContent">
					<h2 class="PageHeader-module__HZea0q__title">
						<span class="flex items-center gap-2">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square-warning w-4 h-4" aria-hidden="true">
								<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
								<path d="M12 7v4"></path>
								<path d="M12 15h.01"></path>
							</svg>
							My Reports
						</span>
					</h2>
				</div>
			</div>
			<div class="PageBody-module__OAJIWq__body ">
				<div class="PageBody-module__OAJIWq__content">
					<div class="space-y-3">
						<?php foreach ($open_tickets as $ticket): ?>
							<div class="items-center py-3 border-b border-[var(--box-inner-border)] last:border-b-0" style="display: grid; grid-template-columns: minmax(0, 1fr) 150px; gap: 16px;">
								<p class="text-xs text-[var(--text-muted)]" style="margin: 0;">
									<b>Ticket #<?php echo (int)$ticket['id']; ?></b>
									<?php echo htmlspecialchars($ticket['subject'], ENT_QUOTES, 'UTF-8'); ?>
									<span class="text-[10px]">(<?php echo htmlspecialchars($ticket['status'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars(getClock($ticket['creation'], true), ENT_QUOTES, 'UTF-8'); ?>)</span>
								</p>
								<a href="helpdesk.php?view=<?php echo (int)$ticket['id']; ?>" style="width: 150px;">
									<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer" style="width: 150px;">
										View Ticket
									</button>
								</a>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div><br>
		<?php
	}

	?>
	<?php if (user_logged_in() === true && is_admin($user_data)): ?>

<div class="">
<div class="PageHeader-module__HZea0q__header ">
<div class="PageHeader-module__HZea0q__headerContent">
<h2 class="PageHeader-module__HZea0q__title">
<span class="flex items-center gap-2">
	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check w-4 h-4" aria-hidden="true">
		<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.5 3.8 17 5 19 5a1 1 0 0 1 1 1z"></path>
		<path d="m9 12 2 2 4-4"></path>
	</svg>
	Admin Panel
</span>

</h2></div></div>
<div class="PageBody-module__OAJIWq__body ">
<div class="PageBody-module__OAJIWq__content"><div class="space-y-3">
<div class="flex flex-col gap-3">
<div class="flex items-start gap-3 flex-1 min-w-0">
<div class="flex-shrink-0">
<div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg flex items-center justify-center shadow-lg border-2 bg-gradient-to-br from-emerald-600 to-emerald-800 border-emerald-400">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check w-4 h-4" aria-hidden="true">
		<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.5 3.8 17 5 19 5a1 1 0 0 1 1 1z"></path>
		<path d="m9 12 2 2 4-4"></path>
	</svg>
</div></div>
<div class="flex-1 min-w-0"><p class="text-sm font-bold text-[#22c55e]">
    Admin Panel</p>
<p class="text-xs text-[var(--text-muted)] mt-0.5">Quick access to website administration tools.</p>
</div>
</div>

<div class="pt-3 border-t border-[var(--box-inner-border)]">
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
<?php
$admin_links = array(
	'admin.php' => 'Admin',
	'admin_auction.php' => 'Auctions',
	'admin_helpdesk.php' => 'Helpdesk',
	'changelog.php' => 'Changelogs',
	'admin_news.php' => 'News',
	'admin_reports.php' => 'Reports',
	'admin_shop_add.php' => 'Add Shop Item',
	'admin_shop.php' => 'Shop',
	'admin_skills.php' => 'Skills',
);
foreach ($admin_links as $admin_link => $admin_label):
?>
<a class="flex-1 sm:flex-initial" href="<?php echo htmlspecialchars($admin_link, ENT_QUOTES, 'UTF-8'); ?>">
<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] w-full cursor-pointer">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check w-3 h-3 mr-1" aria-hidden="true">
<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.5 3.8 17 5 19 5a1 1 0 0 1 1 1z"></path>
<path d="m9 12 2 2 4-4"></path>
</svg><?php echo htmlspecialchars($admin_label, ENT_QUOTES, 'UTF-8'); ?></button></a>
<?php endforeach; ?>
</div></div></div>


<br>
</div></div></div></div><br>
<?php endif; ?>

	<div class="">
<div class="PageHeader-module__HZea0q__header ">
<div class="PageHeader-module__HZea0q__headerContent">
<h2 class="PageHeader-module__HZea0q__title">
<span class="flex items-center gap-2">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-crown w-4 h-4" aria-hidden="true">
<path d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z"></path>
<path d="M5 21h14"></path></svg>Account Status</span>
</h2></div></div>
<div class="PageBody-module__OAJIWq__body ">
<div class="PageBody-module__OAJIWq__content"><div class="space-y-3">
<div class="flex justify-between items-start gap-4">
<div class="flex items-start gap-3 flex-1 min-w-0">
<div class="flex-shrink-0">
<?php
$hasPremium = ($user_data['premdays'] > 0);
?>
<div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg flex items-center justify-center shadow-lg border-2 <?php echo $hasPremium ? 'bg-gradient-to-br from-emerald-600 to-emerald-800 border-emerald-400' : 'bg-gradient-to-br from-red-700 to-red-900 border-red-500'; ?>">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-crown w-6 h-6 sm:w-7 sm:h-7 <?php echo $hasPremium ? 'text-emerald-200' : 'text-red-200'; ?>" aria-hidden="true">
<path d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z">
</path><path d="M5 21h14"></path></svg>
</div></div>
<div class="flex-1 min-w-0"><p class="text-sm font-bold <?php echo $hasPremium ? 'text-[#22c55e]' : 'text-[#ef4444]'; ?>">
    <?php echo $hasPremium ? 'VIP Account' : 'Free Account'; ?>
</p>
<?php 
			// Todo: If premdays is less than 2, inform how many hours are left.
			if ($user_data['premdays'] != 0) echo "<p class='text-xs text-[var(--text-muted)] mt-0.5'>You have {$user_data['premdays']} remaining premium account days.</p>"; 
			else echo '<p class="text-xs text-[var(--text-muted)] mt-0.5">You are free account.</p>';
			if ($config['mailserver']['myaccount_verify_email']):
				?><br><p>Email: <?php echo $user_data['email'];
				if ($user_znote_data['active_email'] == 1) {
					?> (Verified).<?php
				} else {
					?><br><strong>Your email is not verified! <a href="?authenticate">Please verify it</a>.</strong></p><?php
				}
			endif; ?>
</div>



</div>
<div class="flex flex-col gap-2 shrink-0">
<a class="flex-1 sm:flex-initial" href="settings.php">
<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] w-full sm:min-w-[130px] cursor-pointer">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings w-3 h-3 mr-1" aria-hidden="true">
<path d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915"></path><circle cx="12" cy="12" r="3"></circle>
</svg>Manage Account</button></a>
<a class="flex-1 sm:flex-initial" href="shop.php">
<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] w-full sm:min-w-[130px] cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-crown w-3 h-3 mr-1 text-[#c4a24d]" aria-hidden="true">
<path d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z"></path><path d="M5 21h14"></path>
</svg>Buy VIP Time</button></a><a class="flex-1 sm:flex-initial" href="buypoints.php">
<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] w-full sm:min-w-[130px] cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-coins w-3 h-3 mr-1 text-amber-400" aria-hidden="true"><circle cx="8" cy="8" r="6"></circle>
<path d="M18.09 10.37A6 6 0 1 1 10.34 18"></path><path d="M7 6h1v4"></path><path d="m16.71 13.88.7.71-2.82 2.82"></path>
</svg>Buy Coins</button></a>
</div></div><div class="pt-3 border-t border-[var(--box-inner-border)]"><div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-3">
<div class="flex items-center gap-2 p-2 bg-[var(--box-inner-bg)] rounded border border-[var(--box-inner-border)]">
<div class="w-8 h-8 bg-gradient-to-br from-amber-600 to-amber-800 rounded flex items-center justify-center flex-shrink-0"><span class="text-amber-200 text-lg"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-green-200" aria-hidden="true">
        <circle cx="12" cy="12" r="10"></circle>
        <path d="M2 12h20"></path>
        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
    </svg></span>
</div><p class="text-[10px] text-[var(--text-muted)] leading-tight">Explore the world and discover exclusive hunting zones</p></div><div class="flex items-center gap-2 p-2 bg-[var(--box-inner-bg)] rounded border border-[var(--box-inner-border)]">
<div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-blue-800 rounded flex items-center justify-center flex-shrink-0"><span class="text-blue-200 text-lg">🏋️</span></div>
<p class="text-[10px] text-[var(--text-muted)] leading-tight">Exercise weapons auto-start after the previous one ends</p></div><div class="flex items-center gap-2 p-2 bg-[var(--box-inner-bg)] rounded border border-[var(--box-inner-border)]">
<div class="w-8 h-8 bg-gradient-to-br fromcursor-pointer-purple-600 to-purple-800 rounded flex items-center justify-center flex-shrink-0"><span class="text-purple-200 text-lg">✨</span></div>
<p class="text-[10px] text-[var(--text-muted)] leading-tight">Get benefits like your own house or access to restricted areas</p></div></div>
<div class="mt-2 text-center">
</div>
<br>
<?php
		if ($config['twoFactorAuthenticator']) {
			$query = mysql_select_single("SELECT `secret` FROM `accounts` WHERE `id`='".(int)$session_user_id."' LIMIT 1;");
			$status = ($query['secret'] === NULL) ? false : true;
			?><p>Account security with Two-factor Authentication: <a href="twofa.php"><?php echo ($status) ? 'Enabled' : 'Disabled'; ?></a></p><?php
		}
		?></div></div></div></div></div>
<br>


		<?php
		if ($config['twoFactorAuthenticator']) {
			$query = mysql_select_single("SELECT `secret` FROM `accounts` WHERE `id`='".(int)$session_user_id."' LIMIT 1;");
			$status = ($query['secret'] === NULL) ? false : true;
			?><p>Account security with Two-factor Authentication: <a href="twofa.php"><?php echo ($status) ? 'Enabled' : 'Disabled'; ?></a></p><?php
		}
		?>
		
		<div class="">
		<div class="PageHeader-module__HZea0q__header ">
		<div class="PageHeader-module__HZea0q__headerContent">
		<h2 class="PageHeader-module__HZea0q__title">
		<span class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users w-4 h-4" aria-hidden="true">
		<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><path d="M16 3.128a4 4 0 0 1 0 7.744"></path><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
		<circle cx="9" cy="7" r="4"></circle></svg>My Characters: <?php echo $char_count; ?></span></h2></div></div>
		<?php
		// Echo character list!
		$char_array = user_character_list($user_data['id']);
		// Design and present the list
		if ($char_array) {
			?>
		<div class="PageBody-module__OAJIWq__body">
    <div class="PageBody-module__OAJIWq__content">
        <div class="space-y-2">

            <?php foreach ($char_array as $value): ?>
                <div class="flex items-center justify-between py-2 px-3 bg-[var(--box-inner-bg)] rounded border border-[var(--box-inner-border)] group">

                    <a class="flex items-center gap-3 flex-1 min-w-0 hover:opacity-80 transition-opacity" href="characterprofile.php?name=<?php echo urlencode($value['name']); ?>">

                        <div class="w-10 h-10 bg-gradient-to-br from-[var(--box-header-from)] to-[var(--box-header-to)] rounded flex items-center justify-center flex-shrink-0">
                            <span class="text-[12px] font-bold text-white">
                                <?php echo $value['level']; ?>
                            </span>
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-[var(--text-link)] group-hover:text-[var(--text-link-hover)] truncate">
                                <?php echo $value['name']; ?>
                            </p>

                            <p class="text-xs text-[var(--text-muted)] truncate">
                                <?php echo $value['vocation']; ?>
                            </p>
                        </div>
                    </a>

                    <div class="flex items-center gap-1 ml-3 flex-shrink-0">
					<form action="" method="post" class="m-0">
						<input type="hidden" name="selected_character" value="<?php echo htmlspecialchars($value['name']); ?>">
						
						<select name="action" onchange="handleCharacterAction(this)" class="text-[10px] px-2 py-1 rounded border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)] text-[var(--text-muted)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)]">
							<option value="none" selected>Action</option>
							<option value="toggle_hide">Toggle hide</option>
							<option value="change_comment">Comment</option>
							<option value="change_gender">Gender</option>
							<option value="change_name">Rename</option>
							<option value="delete_character">Delete</option>
						</select>

						<div class="rename-fields hidden mt-2 flex items-center gap-1">
							<input type="text" name="newName" placeholder="New name" class="w-36 px-2 py-1 text-[10px] bg-[var(--box-inner-bg)] text-[var(--text-muted)] border border-[var(--box-inner-border)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)]">
							<button type="submit" class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-2 py-1 text-[10px] cursor-pointer">Save</button>
						</div>
						<?php Token::create(); ?>
					</form>
				</div>

                </div>
            <?php endforeach; ?>

            <div class="flex justify-end pt-3 mt-3 border-t border-[var(--box-inner-border)]">
                <a href="createcharacter.php">
                    <button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-plus w-3 h-3 mr-1" aria-hidden="true">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <line x1="19" x2="19" y1="8" y2="14"></line>
                            <line x1="22" x2="16" y1="11" y2="11"></line>
                        </svg>
                        Create Character
                    </button>
                </a>
            </div>

        </div>
    </div>
</div></div>
		
		
			
			<!-- FORMS TO EDIT CHARACTER-->
			<form action="" method="post">
				<table class="table">
					<tr>
						<td>
							<select id="selected_character" name="selected_character" class="form-control">
								<?php foreach ($char_array as $character): ?>
									<option value="<?php echo htmlspecialchars($character['name'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($character['name'], ENT_QUOTES, 'UTF-8'); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
						<td>
							<select id="action" name="action" class="form-control" onChange="changedOption(this)">
								<option value="none" selected>Select action</option>
								<option value="toggle_hide">Toggle hide</option>
								<option value="change_comment">Change comment</option>
								<option value="change_gender">Change gender</option>
								<option value="change_name">Change name</option>
								<option value="delete_character" class="needconfirmation">Delete character</option>
							</select>
						</td>
						<td id="submit_form">
							<?php
								/* Form file */
								Token::create();
							?>
							<input id="submit_button" type="submit" value="Submit" class="btn btn-primary btn-block"></input>
						</td>
					</tr>
				</table>
			</form>
			<?php
		} else {
			?>
			<div class="PageBody-module__OAJIWq__body">
				<div class="PageBody-module__OAJIWq__content">
					<div class="text-center py-4 text-xs text-[var(--text-muted)]">
						You don't have any characters. Why don't you <a href="createcharacter.php">create one</a>?
					</div>
				</div>
			</div>
			</div>
			<?php
		}
		?>
	<script>
		function handleCharacterAction(select) {
			var form = select.form;
			var renameFields = form.querySelector('.rename-fields');

			if (select.value === 'change_name') {
				renameFields.classList.remove('hidden');
				renameFields.querySelector('input[name="newName"]').focus();
				return;
			}

			renameFields.classList.add('hidden');

			if (select.value === 'delete_character') {
				var character = form.querySelector('input[name="selected_character"]').value;
				if (!confirm('Do you really want to DELETE character: ' + character + '?')) {
					select.value = 'none';
					return;
				}
			}

			if (select.value !== 'none') {
				form.submit();
			}
		}

		function changedOption(e) {
			// If selection is 'Change name' add a name field in the form
			// Else remove name field if it exists
			if (e.value == 'change_name') {
				var lastCell = document.getElementById('submit_form');
				var x = document.createElement('TD');
				x.id = "new_name";
				x.innerHTML = '<input type="text" name="newName" placeholder="New Name" class="form-control">';
				lastCell.parentNode.insertBefore(x, lastCell);
			} else {
				var child = document.getElementById('new_name');
				if (child) {
					child.parentNode.removeChild(child);
				}
			}
		}
	</script>
	<script src="engine/js/jquery-1.10.2.min.js" type="text/javascript"></script>
	<script>
		$(document).ready(function(){
			$("#submit_button").click(function(e){
				if ($("#action").find(":selected").attr('class') == "needconfirmation") {
					var r = confirm("Do you really want to DELETE character: "+$('#selected_character').find(":selected").text()+"?")
					if (r == false) {
						e.preventDefault();
					}
				}
			});
		});
	</script>
	<?php
}
include 'layout/overall/footer.php';
?>
