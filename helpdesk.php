<?php
require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php';

function helpdesk_button_class($extra = '') {
	return trim('inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer ' . $extra);
}

function helpdesk_input_class($extra = '') {
	return trim('w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] disabled:opacity-70 disabled:cursor-not-allowed ' . $extra);
}

function render_helpdesk_notice($message, $type = 'info') {
	$textClass = 'text-[var(--text-muted)]';
	if ($type === 'error') {
		$textClass = 'text-red-600';
	} else if ($type === 'success') {
		$textClass = 'text-[#22c55e]';
	}
	?>
	<div class="text-xs <?php echo $textClass; ?>"><?php echo $message; ?></div>
	<?php
}

$view = (isset($_GET['view']) && (int)$_GET['view'] > 0) ? (int)$_GET['view'] : false;
if ($view !== false) {
	if (!empty($_POST['reply_text'])) {

		// Save ticket reply on database
		$query = array(
			'tid'   =>	$view,
			'username'=>	getValue($_POST['username']),
			'message' =>	getValue($_POST['reply_text']),
			'created' =>	time(),
		);
		$fields = '`'. implode('`, `', array_keys($query)) .'`';
		$data = '\''. implode('\', \'', $query) .'\'';
		mysql_insert("INSERT INTO `znote_tickets_replies` ($fields) VALUES ($data)");
		mysql_update("UPDATE `znote_tickets` SET `status`='Player-Reply' WHERE `id`='$view' LIMIT 1;");
	}
	$ticketData = mysql_select_single("SELECT * FROM znote_tickets WHERE id='$view' LIMIT 1;");

	if(!$ticketData || $ticketData['owner'] != $session_user_id) {
		render_helpdesk_notice('You can not view this ticket!', 'error');
		include 'layout/overall/footer.php';
		die;
	}
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					View Ticket #<?php echo (int)$ticketData['id']; ?>
					<?php if ($ticketData['status'] === 'CLOSED'): ?>
						<span class="text-red-600">[CLOSED]</span>
					<?php endif; ?>
				</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="space-y-3">
					<div class="border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)]/30">
						<div class="px-2 py-1.5 text-xs font-semibold bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
							<?php echo htmlspecialchars(getClock($ticketData['creation'], true), ENT_QUOTES, 'UTF-8'); ?> - Created by: <?php echo htmlspecialchars($ticketData['username'], ENT_QUOTES, 'UTF-8'); ?>
						</div>
						<div class="p-2 text-xs text-[var(--text-muted)]">
							<?php echo nl2br(htmlspecialchars($ticketData['message'], ENT_QUOTES, 'UTF-8')); ?>
						</div>
					</div>
	<?php
	$replies = mysql_select_multi("SELECT * FROM znote_tickets_replies WHERE tid='$view' ORDER BY `created`;");
	if ($replies !== false) {
		foreach($replies as $reply) {
			?>
			<div class="border border-[var(--box-inner-border)] bg-[var(--box-inner-bg)]/30">
				<div class="px-2 py-1.5 text-xs font-semibold bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
					<?php echo htmlspecialchars(getClock($reply['created'], true), ENT_QUOTES, 'UTF-8'); ?> - Posted by: <?php echo htmlspecialchars($reply['username'], ENT_QUOTES, 'UTF-8'); ?>
				</div>
				<div class="p-2 text-xs text-[var(--text-muted)]">
					<?php echo nl2br(htmlspecialchars($reply['message'], ENT_QUOTES, 'UTF-8')); ?>
				</div>
			</div>
		<?php
		}
	}
	?>

	<?php if ($ticketData['status'] !== 'CLOSED') { ?>
		<form action="" method="post" class="space-y-2">
			<input type="hidden" name="username" value="<?php echo htmlspecialchars($ticketData['username'], ENT_QUOTES, 'UTF-8'); ?>">
			<label for="reply_text" class="block text-xs font-medium">Reply</label>
			<textarea id="reply_text" class="<?php echo helpdesk_input_class('min-h-[150px] resize-y'); ?>" name="reply_text"></textarea>
			<div class="flex justify-end">
				<button type="submit" class="<?php echo helpdesk_button_class(); ?>">Post Reply</button>
			</div>
		</form>
	<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<?php
} else {

	$account = mysql_select_single("SELECT name,email FROM accounts WHERE id = $session_user_id");
	if (!empty($_POST)) {
		$required_fields = array('username', 'email', 'subject', 'message');
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
			// Reversed this if, so: first check if you need to validate, then validate.
			if ($config['validate_IP'] === true && validate_ip(getIP()) === false) {
				$errors[] = 'Failed to recognize your IP address. (Not a valid IPv4 address).';
			}
		}
	}
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">Latest Tickets</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
	<?php
	$tickets = mysql_select_multi("SELECT id,subject,creation,status FROM znote_tickets WHERE owner=$session_user_id ORDER BY creation DESC");
	if ($tickets !== false) {
		?>
		<div class="overflow-x-auto">
		<table class="w-full text-xs">
			<tr class="bg-[var(--box-inner-bg)] border-b border-[var(--box-inner-border)]">
				<td class="py-1.5 px-2">ID</td>
				<td class="py-1.5 px-2">Subject</td>
				<td class="py-1.5 px-2">Creation</td>
				<td class="py-1.5 px-2">Status</td>
			</tr>
				<?php
				foreach ($tickets as $ticket) {
					echo '<tr class="special border-b border-[var(--box-inner-border)] hover:bg-[var(--hover-bg)]">';
						echo '<td class="py-1 px-2">'. (int)$ticket['id'] .'</td>';
						echo '<td class="py-1 px-2"><a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)] hover:underline" href="helpdesk.php?view='. (int)$ticket['id'] .'">'. htmlspecialchars($ticket['subject'], ENT_QUOTES, 'UTF-8') .'</a></td>';
						echo '<td class="py-1 px-2">'. htmlspecialchars(getClock($ticket['creation'], true), ENT_QUOTES, 'UTF-8') .'</td>';
						echo '<td class="py-1 px-2">'. htmlspecialchars($ticket['status'], ENT_QUOTES, 'UTF-8') .'</td>';
					echo '</tr>';
				}
				?>
		</table>
		</div>
		<?php
	} else {
		render_helpdesk_notice('No tickets yet.');
	}
	?>
			</div>
		</div>
	</div>
	<br>

	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">Helpdesk</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="space-y-3">
	<?php
	if (isset($_GET['success']) && empty($_GET['success'])) {
		render_helpdesk_notice('Congratulations! Your ticket has been created. We will reply up to 24 hours.', 'success');
	} else {

		if (empty($_POST) === false && empty($errors) === true) {
			if ($config['log_ip']) {
				znote_visitor_insert_detailed_data(1);
			}

			//Save ticket on database
			$query = array(
				'owner'   =>	$session_user_id,
				'username'=>	getValue($_POST['username']),
				'subject' =>	getValue($_POST['subject']),
				'message' =>	getValue($_POST['message']),
				'ip'	  =>	getIPLong(),
				'creation' =>	time(),
				'status'  =>	'Open'
			);

			$fields = '`'. implode('`, `', array_keys($query)) .'`';
			$data = '\''. implode('\', \'', $query) .'\'';
			mysql_insert("INSERT INTO `znote_tickets` ($fields) VALUES ($data)");

			header('Location: helpdesk.php?success');
			exit();

		} else if (empty($errors) === false) {
			render_helpdesk_notice(output_errors($errors), 'error');
		}
		?>
		<form action="" method="post" class="space-y-3">
			<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
				<label for="helpdesk_username" class="font-medium">Account Name</label>
				<input id="helpdesk_username" type="text" name="username" value="<?php echo htmlspecialchars($account['name'], ENT_QUOTES, 'UTF-8'); ?>" disabled class="<?php echo helpdesk_input_class(); ?>">
			</div>
			<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
				<label for="helpdesk_email" class="font-medium">Email</label>
				<input id="helpdesk_email" type="text" name="email" value="<?php echo htmlspecialchars($account['email'], ENT_QUOTES, 'UTF-8'); ?>" disabled class="<?php echo helpdesk_input_class(); ?>">
			</div>
			<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
				<label for="helpdesk_subject" class="font-medium">Subject</label>
				<input id="helpdesk_subject" type="text" name="subject" class="<?php echo helpdesk_input_class(); ?>">
			</div>
			<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-start text-xs">
				<label for="helpdesk_message" class="font-medium pt-1">Message</label>
				<textarea id="helpdesk_message" name="message" rows="7" class="<?php echo helpdesk_input_class('resize-y'); ?>"></textarea>
			</div>
				<?php
				if ($config['use_captcha']) {
					?>
					<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
						<div></div>
						<div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($config['captcha_site_key'], ENT_QUOTES, 'UTF-8'); ?>"></div>
					</div>
					<?php
				}
				?>
				<?php
					/* Form file */
					Token::create();
				?>
			<div class="flex justify-end">
				<input type="hidden" name="username" value="<?php echo htmlspecialchars($account['name'], ENT_QUOTES, 'UTF-8'); ?>">
				<button type="submit" class="<?php echo helpdesk_button_class(); ?>">Submit ticket</button>
			</div>
		</form>
		<?php
	}
	?>
				</div>
			</div>
		</div>
	</div>
	<?php
}
include 'layout/overall/footer.php';
?>
