<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';
protect_page();
admin_only($user_data);

function admin_helpdesk_panel_start($title) {
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title"><span class="flex items-center gap-2"><?php echo hhb_tohtml($title); ?></span></h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body "><div class="PageBody-module__OAJIWq__content"><div class="space-y-3">
	<?php
}
function admin_helpdesk_panel_end() {
	?></div></div></div></div><br><?php
}

$button_class = 'inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer';
$input_class = 'w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none';

$view = (isset($_GET['view']) && (int)$_GET['view'] > 0) ? (int)$_GET['view'] : false;
if ($view !== false){
	if (!empty($_POST['reply_text'])) {
		sanitize($_POST['reply_text']);
		$query = array(
			'tid' => $view,
			'username' => getValue($_POST['username']),
			'message' => getValue($_POST['reply_text']),
			'created' => time(),
		);
		$fields = '`'. implode('`, `', array_keys($query)) .'`';
		$data = '\''. implode('\', \'', $query) .'\'';
		mysql_insert("INSERT INTO `znote_tickets_replies` ($fields) VALUES ($data)");
		mysql_update("UPDATE `znote_tickets` SET `status`='Staff-Reply' WHERE `id`='$view' LIMIT 1;");
	} else if (!empty($_POST['admin_ticket_close'])) {
		$ticketId = (int) $_POST['admin_ticket_id'];
		mysql_update("UPDATE `znote_tickets` SET `status` = 'CLOSED' WHERE `id` ='$ticketId' LIMIT 1;");
	} else if (!empty($_POST['admin_ticket_open'])) {
		$ticketId = (int) $_POST['admin_ticket_id'];
		mysql_update("UPDATE `znote_tickets` SET `status` = 'Open' WHERE `id` ='$ticketId' LIMIT 1;");
	} else if (!empty($_POST['admin_ticket_delete'])) {
		$ticketId = (int) $_POST['admin_ticket_id'];
		mysql_delete("DELETE FROM `znote_tickets` WHERE `id`='$ticketId' LIMIT 1;");
		header("Location: admin_helpdesk.php");
		exit();
	}

	$ticketData = mysql_select_single("SELECT * FROM znote_tickets WHERE id='$view' LIMIT 1;");
	if(!$ticketData) {
		admin_helpdesk_panel_start('Helpdesk');
		?><div class="text-xs text-red-600">You can not view this ticket.</div><?php
		admin_helpdesk_panel_end();
		include 'layout/overall/footer.php';
		exit();
	}

	admin_helpdesk_panel_start('View Ticket #' . $ticketData['id']);
	?>
	<div class="text-xs text-[var(--text-muted)]">
		Created by <b><?php echo hhb_tohtml($ticketData['username']); ?></b> on <?php echo hhb_tohtml(getClock($ticketData['creation'], true)); ?>
		<span class="ml-2">Status: <b><?php echo hhb_tohtml($ticketData['status']); ?></b></span>
	</div>
	<div class="p-3 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-xs text-[var(--text-muted)]"><?php echo nl2br(hhb_tohtml($ticketData['message'])); ?></div>
	<?php
	$replies = mysql_select_multi("SELECT * FROM znote_tickets_replies WHERE tid='$view' ORDER BY `created`;");
	if ($replies !== false) {
		foreach($replies as $reply) {
			?>
			<div class="p-3 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-xs text-[var(--text-muted)]">
				<div class="font-semibold mb-1"><?php echo hhb_tohtml(getClock($reply['created'], true)); ?> - Posted by <?php echo hhb_tohtml($reply['username']); ?></div>
				<?php echo nl2br(hhb_tohtml($reply['message'])); ?>
			</div>
			<?php
		}
	}
	?>
	<div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-3 border-t border-[var(--box-inner-border)]">
		<form action="" method="post">
			<input type="hidden" name="admin_ticket_id" value="<?php echo (int)$ticketData['id']; ?>">
			<?php if ($ticketData['status'] !== 'CLOSED') { ?>
				<button type="submit" name="admin_ticket_close" value="1" class="<?php echo $button_class; ?> w-full">Close Ticket</button>
			<?php } else { ?>
				<button type="submit" name="admin_ticket_open" value="1" class="<?php echo $button_class; ?> w-full">Open Ticket</button>
			<?php } ?>
		</form>
		<form action="" method="post" onsubmit="return confirm('Are you sure you want to delete this ticket?');">
			<input type="hidden" name="admin_ticket_id" value="<?php echo (int)$ticketData['id']; ?>">
			<button type="submit" name="admin_ticket_delete" value="1" class="<?php echo $button_class; ?> w-full">Delete Ticket</button>
		</form>
	</div>
	<?php if ($ticketData['status'] !== 'CLOSED') { ?>
		<form action="" method="post" class="space-y-2 pt-3 border-t border-[var(--box-inner-border)]">
			<input type="hidden" name="username" value="ADMIN">
			<textarea class="<?php echo $input_class; ?>" name="reply_text" rows="6" placeholder="Reply..."></textarea>
			<div class="flex justify-end"><button type="submit" class="<?php echo $button_class; ?>">Post Reply</button></div>
		</form>
	<?php }
	admin_helpdesk_panel_end();
} else {
	admin_helpdesk_panel_start('Latest Tickets');
	$tickets = mysql_select_multi("SELECT id, subject, creation, status FROM znote_tickets ORDER BY creation DESC");
	if ($tickets !== false) {
		foreach ($tickets as $ticket) {
			?>
			<div class="grid grid-cols-1 sm:grid-cols-[70px_1fr_160px_120px] gap-2 items-center py-2 px-3 bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] text-xs text-[var(--text-muted)]">
				<div>#<?php echo (int)$ticket['id']; ?></div>
				<a class="text-[var(--text-link)] hover:text-[var(--text-link-hover)] font-semibold" href="admin_helpdesk.php?view=<?php echo (int)$ticket['id']; ?>"><button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] w-full cursor-pointer" style="width:150px">View Ticket</button><br></br><?php echo hhb_tohtml($ticket['subject']); ?></a>
				<div><?php echo hhb_tohtml(getClock($ticket['creation'], true)); ?></div>
				<div><?php echo hhb_tohtml($ticket['status']); ?></div>
			</div>
			<?php
		}
	} else {
		?><div class="text-xs text-[var(--text-muted)] text-center py-4">No helpdesk tickets have been submitted.</div><?php
	}
	admin_helpdesk_panel_end();
}
include 'layout/overall/footer.php';
?>
