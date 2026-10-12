<?php require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php';

if (empty($_POST) === false) {
	// $_POST['']
	$required_fields = array('name', 'selected_town');
	foreach($_POST as $key=>$value) {
		if (empty($value) && in_array($key, $required_fields) === true) {
			$errors[] = 'You need to fill in all fields.';
			break 1;
		}
	}

	// check errors (= user exist, pass long enough
	if (empty($errors) === true) {
		if (!Token::isValid($_POST['token'])) {
			$errors[] = 'Token is invalid.';
		}
		$_POST['name'] = validate_name($_POST['name']);
		if ($_POST['name'] === false) {
			$errors[] = 'Your name can not contain more than 2 words.';
		} else {
			if (user_character_exist($_POST['name']) !== false) {
				$errors[] = 'Sorry, that character name already exist.';
			}
			if (!preg_match("/^[a-zA-Z ]+$/", $_POST['name'])) {
				$errors[] = 'Your name may only contain a-z, A-Z and spaces.';
			}
			if (strlen($_POST['name']) < $config['minL'] || strlen($_POST['name']) > $config['maxL']) {
				$errors[] = 'Your character name must be between ' . $config['minL'] . ' - ' . $config['maxL'] . ' characters long.';
			}
			// name restriction
			$resname = explode(" ", $_POST['name']);
			$username = $_POST['name'];
			foreach($resname as $res) {
				if(in_array(strtolower($res), $config['invalidNameTags'])) {
						$errors[] = 'Your username contains a restricted word.';
				}
				if(strlen($res) == 1) {
					$errors[] = 'Too short words in your name.';
				}
			}
			if(in_array(strtolower($username), $config['creatureNameTags'])) {
				$errors[] = 'Your username contains a creature name.';
			}
			// Validate vocation id
			if (!in_array((int)$_POST['selected_vocation'], $config['available_vocations'])) {
				$errors[] = 'Permission Denied. Wrong vocation.';
			}
			// Validate town id
			if (!in_array((int)$_POST['selected_town'], $config['available_towns'])) {
				$errors[] = 'Permission Denied. Wrong town.';
			}
			// Validate gender id
			if (!in_array((int)$_POST['selected_gender'], array(0, 1))) {
				$errors[] = 'Permission Denied. Wrong gender.';
			}
			if (vocation_id_to_name($_POST['selected_vocation']) === false) {
				$errors[] = 'Failed to recognize that vocation, does it exist?';
			}
			if (town_id_to_name($_POST['selected_town']) === false) {
				$errors[] = 'Failed to recognize that town, does it exist?';
			}
			if (gender_exist($_POST['selected_gender']) === false) {
				$errors[] = 'Failed to recognize that gender, does it exist?';
			}
			// Char count
			$char_count = user_character_list_count($session_user_id);
			if ($char_count >= $config['max_characters'] && !is_admin($user_data)) {
				$errors[] = 'Your account is not allowed to have more than '. $config['max_characters'] .' characters.';
			}
			if (validate_ip(getIP()) === false && $config['validate_IP'] === true) {
				$errors[] = 'Failed to recognize your IP address. (Not a valid IPv4 address).';
			}
		}
	}
}
?>

<?php
if (isset($_GET['success']) && empty($_GET['success'])) {
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-check w-4 h-4" aria-hidden="true"><path d="m16 11 2 2 4-4"></path><path d="M16 19h6"></path><circle cx="9" cy="7" r="4"></circle><path d="M15 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path></svg>
						Character Created
					</span>
				</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="space-y-3">
					<div class="text-xs text-[#22c55e] font-semibold">Congratulations! Your character has been created. See you in-game!</div>
					<div class="flex justify-end">
						<a href="myaccount.php">
							<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer">
								Back to My Account
							</button>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
} else {
	if (empty($_POST) === false && empty($errors) === true) {
		if ($config['log_ip']) {
			znote_visitor_insert_detailed_data(2);
		}
		//Register
		$character_data = array(
			'name'		=>	format_character_name($_POST['name']),
			'account_id'=>	$session_user_id,
			'vocation'	=>	$_POST['selected_vocation'],
			'town_id'	=>	$_POST['selected_town'],
			'sex'		=>	$_POST['selected_gender'],
			'lastip'	=>	getIPLong(),
			'created'	=>	time()
		);

		user_create_character($character_data);
		header('Location: createcharacter.php?success');
		exit();
		//End register
	}
	?>
	<div class="">
		<div class="PageHeader-module__HZea0q__header ">
			<div class="PageHeader-module__HZea0q__headerContent">
				<h2 class="PageHeader-module__HZea0q__title">
					<span class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-plus w-4 h-4" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" x2="19" y1="8" y2="14"></line><line x1="22" x2="16" y1="11" y2="11"></line></svg>
						Create Character
					</span>
				</h2>
			</div>
		</div>
		<div class="PageBody-module__OAJIWq__body ">
			<div class="PageBody-module__OAJIWq__content">
				<div class="space-y-3">
					<div class="mb-2">
						<a href="myaccount.php">
							<button class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left w-3 h-3 mr-1" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>
								Back to My Account
							</button>
						</a>
					</div>

					<?php if (empty($errors) === false): ?>
						<div class="text-xs text-[#ef4444] font-semibold"><?php echo output_errors($errors); ?></div>
					<?php endif; ?>

					<form action="" method="post" class="space-y-3">
						<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
							<label for="character_name" class="font-medium">Name</label>
							<input class="w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none" type="text" name="name" id="character_name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : ''; ?>">
						</div>

						<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
							<label for="selected_vocation" class="font-medium">Vocation</label>
							<select class="w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none" name="selected_vocation" id="selected_vocation">
								<?php foreach ($config['available_vocations'] as $id): ?>
									<option value="<?php echo $id; ?>"<?php echo (isset($_POST['selected_vocation']) && (int)$_POST['selected_vocation'] === (int)$id) ? ' selected' : ''; ?>><?php echo vocation_id_to_name($id); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
							<label for="selected_gender" class="font-medium">Gender</label>
							<select class="w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none" name="selected_gender" id="selected_gender">
								<option value="1"<?php echo (isset($_POST['selected_gender']) && (int)$_POST['selected_gender'] === 1) ? ' selected' : ''; ?>>Male (boy)</option>
								<option value="0"<?php echo (isset($_POST['selected_gender']) && (int)$_POST['selected_gender'] === 0) ? ' selected' : ''; ?>>Female (girl)</option>
							</select>
						</div>

						<?php
						$available_towns = $config['available_towns'];
						if (count($available_towns) > 1):
							?>
							<div class="grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-1 sm:gap-3 items-center text-xs">
								<label for="selected_town" class="font-medium">Town</label>
								<select class="w-full px-2 py-1 text-xs bg-[var(--input-bg)] text-[var(--input-text)] border border-[var(--input-border)] focus:outline-none" name="selected_town" id="selected_town">
									<?php foreach ($available_towns as $tid): ?>
										<option value="<?php echo $tid; ?>"<?php echo (isset($_POST['selected_town']) && (int)$_POST['selected_town'] === (int)$tid) ? ' selected' : ''; ?>><?php echo town_id_to_name($tid); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<?php
						else:
							$default_town = (int)end($available_towns);
							?>
							<input type="hidden" name="selected_town" value="<?php echo $default_town; ?>">
							<?php
						endif;

						Token::create();
						?>

						<div class="flex justify-end">
							<button type="submit" class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-80 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-3 py-1 text-[11px] cursor-pointer">
								Create Character
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php
}
include 'layout/overall/footer.php';
?>
