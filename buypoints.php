<?php
require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php';

$pagseguro = $config['pagseguro'];
$paypal = $config['paypal'];
$paygol = $config['paygol'];
$prices = $config['paypal_prices'];
$has_payment_method = $paypal['enabled'] || $paygol['enabled'] || $pagseguro['enabled'];

function buypoints_image_for_index($index) {
	return 'template/images/serviceid_' . min(max((int)$index, 1), 5) . '.png';
}

function buypoints_money($value) {
	return hhb_tohtml(number_format((float)$value, 2, '.', ''));
}
?>

<style>
	.buypoints-method-tab {
		color: var(--text-muted);
		border-bottom: 1px solid transparent;
		padding: 0 18px 10px;
		font-size: 13px;
		font-weight: 700;
		transition: color .15s ease, border-color .15s ease;
	}
	.buypoints-method-tab.is-active,
	.buypoints-method-tab:hover {
		color: #b99638;
		border-bottom-color: #b99638;
	}
	.buypoints-product-card {
		cursor: pointer;
		min-height: 176px;
		background: rgba(115, 96, 62, .18);
		border: 2px solid rgba(80, 65, 42, .28);
		border-radius: 4px;
		transition: border-color .15s ease, background .15s ease, transform .15s ease;
	}
	.buypoints-product-card:hover,
	.buypoints-product-card.is-selected {
		background: rgba(185, 150, 56, .16);
		border-color: rgba(185, 150, 56, .75);
		transform: translateY(-1px);
	}
	.buypoints-product-card input[type="radio"] {
		position: absolute;
		opacity: 0;
		pointer-events: none;
	}
	.buypoints-product-title {
		color: var(--foreground);
	}
</style>

<div class="">
	<div class="PageHeader-module__HZea0q__header ">
		<div class="PageHeader-module__HZea0q__headerContent">
			<h2 class="PageHeader-module__HZea0q__title">
				<span class="flex items-center gap-2">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart w-4 h-4" aria-hidden="true">
						<circle cx="8" cy="21" r="1"></circle>
						<circle cx="19" cy="21" r="1"></circle>
						<path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
					</svg>Select Product
				</span>
			</h2>
		</div>
	</div>
	<div class="PageBody-module__OAJIWq__body ">
		<div class="PageBody-module__OAJIWq__content">
			<?php if (!$has_payment_method): ?>
				<div class="text-center py-4">
					<div class="text-xs text-red-600">Buy Points system is disabled.</div>
					<p class="text-xs text-[var(--text-muted)] mt-2">Sorry, this functionality is disabled.</p>
				</div>
			<?php else: ?>
				<div class="space-y-5">
					<div class="flex flex-wrap items-center gap-x-1 gap-y-3 border-b border-[var(--box-inner-border)]">
						<?php if ($paypal['enabled']): ?>
							<button type="button" class="buypoints-method-tab is-active" data-buypoints-tab="paypal">
								<span class="inline-flex items-center gap-2">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
										<circle cx="8" cy="8" r="6"></circle><path d="M18.09 10.37A6 6 0 1 1 10.34 18"></path>
									</svg>Coins
								</span>
							</button>
						<?php endif; ?>
						<?php if ($paygol['enabled']): ?>
							<button type="button" class="buypoints-method-tab <?php echo !$paypal['enabled'] ? 'is-active' : ''; ?>" data-buypoints-tab="paygol">PayGol</button>
						<?php endif; ?>
						<?php if ($pagseguro['enabled']): ?>
							<button type="button" class="buypoints-method-tab <?php echo (!$paypal['enabled'] && !$paygol['enabled']) ? 'is-active' : ''; ?>" data-buypoints-tab="pagseguro">PagSeguro</button>
						<?php endif; ?>
					</div>

					<div class="space-y-4">
						<?php if ($paypal['enabled']): ?>
							<div class="buypoints-panel" data-buypoints-panel="paypal">
								<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
									<?php
									$product_index = 1;
									foreach ($prices as $price => $points):
										$form_id = 'buypoints-paypal-' . $product_index;
										$image = buypoints_image_for_index($product_index);
										$bonus = $paypal['showBonus'] ? calculate_discount(($paypal['points_per_currency'] * $price), $points) : '';
									?>
										<label class="buypoints-product-card relative flex flex-col items-center justify-center text-center p-4" data-buypoints-card>
											<input type="radio" name="buypoints_product" value="<?php echo $form_id; ?>" data-buypoints-form="<?php echo $form_id; ?>">
											<img src="<?php echo $image; ?>" alt="" class="h-16 w-auto object-contain mb-4">
											<span class="buypoints-product-title text-sm font-bold"><?php echo (int)$points; ?> Coins</span>
											<span class="text-sm font-bold text-[#1d63a4]"><?php echo hhb_tohtml($paypal['currency']); ?> <?php echo buypoints_money($price); ?></span>
											<?php if ($paypal['showBonus']): ?>
												<span class="text-[10px] text-[var(--text-muted)] mt-1"><?php echo hhb_tohtml($bonus); ?> bonus</span>
											<?php endif; ?>
										</label>
										<form id="<?php echo $form_id; ?>" action="https://www.paypal.com/cgi-bin/webscr" method="POST" class="hidden buypoints-payment-form">
											<input type="hidden" name="cmd" value="_xclick">
											<input type="hidden" name="business" value="<?php echo hhb_tohtml($paypal['email']); ?>">
											<input type="hidden" name="item_name" value="<?php echo (int)$points .' shop points on '. hhb_tohtml($config['site_title']); ?>">
											<input type="hidden" name="item_number" value="1">
											<input type="hidden" name="amount" value="<?php echo hhb_tohtml($price); ?>">
											<input type="hidden" name="no_shipping" value="1">
											<input type="hidden" name="no_note" value="1">
											<input type="hidden" name="currency_code" value="<?php echo hhb_tohtml($paypal['currency']); ?>">
											<input type="hidden" name="lc" value="GB">
											<input type="hidden" name="bn" value="PP-BuyNowBF">
											<input type="hidden" name="return" value="<?php echo hhb_tohtml($paypal['success']); ?>">
											<input type="hidden" name="cancel_return" value="<?php echo hhb_tohtml($paypal['failed']); ?>">
											<input type="hidden" name="rm" value="2">
											<input type="hidden" name="notify_url" value="<?php echo hhb_tohtml($paypal['ipn']); ?>">
											<input type="hidden" name="custom" value="<?php echo (int)$session_user_id; ?>">
										</form>
									<?php
										$product_index++;
									endforeach;
									?>
								</div>
							</div>
						<?php endif; ?>

						<?php if ($paygol['enabled']): ?>
							<div class="buypoints-panel <?php echo $paypal['enabled'] ? 'hidden' : ''; ?>" data-buypoints-panel="paygol">
								<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
									<label class="buypoints-product-card relative flex flex-col items-center justify-center text-center p-4" data-buypoints-card>
										<input type="radio" name="buypoints_product" value="buypoints-paygol" data-buypoints-form="buypoints-paygol">
										<img src="<?php echo buypoints_image_for_index(1); ?>" alt="" class="h-16 w-auto object-contain mb-4">
										<span class="buypoints-product-title text-sm font-bold"><?php echo (int)$paygol['points']; ?> Coins</span>
										<span class="text-sm font-bold text-[#1d63a4]"><?php echo hhb_tohtml($paygol['currency']); ?> <?php echo buypoints_money($paygol['price']); ?></span>
										<span class="text-[10px] text-[var(--text-muted)] mt-1">PayGol payment</span>
									</label>
								</div>
								<form id="buypoints-paygol" name="pg_frm" method="post" action="http://www.paygol.com/micropayment/paynow" class="hidden buypoints-payment-form">
									<input type="hidden" name="pg_serviceid" value="<?php echo hhb_tohtml($paygol['serviceID']); ?>">
									<input type="hidden" name="pg_currency" value="<?php echo hhb_tohtml($paygol['currency']); ?>">
									<input type="hidden" name="pg_name" value="<?php echo hhb_tohtml($paygol['name']); ?>">
									<input type="hidden" name="pg_custom" value="<?php echo hhb_tohtml($session_user_id); ?>">
									<input type="hidden" name="pg_price" value="<?php echo hhb_tohtml($paygol['price']); ?>">
									<input type="hidden" name="pg_return_url" value="<?php echo hhb_tohtml($paygol['returnURL']); ?>">
									<input type="hidden" name="pg_cancel_url" value="<?php echo hhb_tohtml($paygol['cancelURL']); ?>">
								</form>
							</div>
						<?php endif; ?>

						<?php if ($pagseguro['enabled']): ?>
							<div class="buypoints-panel <?php echo ($paypal['enabled'] || $paygol['enabled']) ? 'hidden' : ''; ?>" data-buypoints-panel="pagseguro">
								<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
									<label class="buypoints-product-card relative flex flex-col items-center justify-center text-center p-4" data-buypoints-card>
										<input type="radio" name="buypoints_product" value="buypoints-pagseguro" data-buypoints-form="buypoints-pagseguro">
										<img src="<?php echo buypoints_image_for_index(1); ?>" alt="" class="h-16 w-auto object-contain mb-4">
										<span class="buypoints-product-title text-sm font-bold"><?php echo hhb_tohtml($pagseguro['product_name']); ?></span>
										<span class="text-sm font-bold text-[#1d63a4]"><?php echo hhb_tohtml($pagseguro['currency']); ?> <?php echo buypoints_money(((float)$pagseguro['price'] / 100)); ?></span>
										<span class="text-[10px] text-[var(--text-muted)] mt-1">Quantity can be changed below</span>
									</label>
								</div>
								<form id="buypoints-pagseguro" target="pagseguro" action="https://<?php echo hhb_tohtml($pagseguro['urls']['www']); ?>/checkout/checkout.jhtml" method="post" class="hidden buypoints-payment-form">
									<input type="hidden" name="email_cobranca" value="<?php echo hhb_tohtml($pagseguro['email']); ?>">
									<input type="hidden" name="tipo" value="CP">
									<input type="hidden" name="moeda" value="<?php echo hhb_tohtml($pagseguro['currency']); ?>">
									<input type="hidden" name="ref_transacao" value="<?php echo (int)$session_user_id; ?>">
									<input type="hidden" name="item_id_1" value="1">
									<input type="hidden" name="item_descr_1" value="<?php echo hhb_tohtml($pagseguro['product_name']); ?>">
									<input type="hidden" name="item_peso_1" value="0">
									<input type="hidden" name="item_valor_1" value="<?php echo hhb_tohtml($pagseguro['price']); ?>">
									<input id="pagseguro-quantity-hidden" type="hidden" name="item_quant_1" value="1">
								</form>
								<div class="mt-3 max-w-xs">
									<label class="block text-xs font-semibold text-[var(--text-muted)] mb-1" for="pagseguro-quantity">Quantity</label>
									<input id="pagseguro-quantity" type="number" min="1" step="4" value="1" class="w-full px-3 py-1.5 text-xs bg-[var(--box-inner-bg)] border border-[var(--box-inner-border)] rounded focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
								</div>
							</div>
						<?php endif; ?>
					</div>

					<p class="text-xs text-[var(--text-muted)]">* Please note that prices may vary depending on the current exchange rate.</p>

					<label class="flex items-start gap-3 p-3 bg-[var(--box-inner-bg)] rounded border border-[var(--box-inner-border)] text-xs text-[var(--text-muted)]">
						<input id="buypoints-agree" type="checkbox" class="mt-0.5">
						<span>I have read and I agree to the <a href="reules.php" class="text-[var(--text-link)] hover:text-[var(--text-link-hover)]">Rules</a> before buy and will accept to proceed to payment.</span>
					</label>

					<div class="flex justify-end">
						<button id="buypoints-continue" type="button" class="inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed relative bg-gradient-to-b from-[var(--btn-primary-from)] via-[var(--btn-primary-via)] to-[var(--btn-primary-to)] text-[var(--btn-primary-text)] border border-[var(--btn-primary-border)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),inset_0_-1px_0_rgba(0,0,0,0.3),0_1px_2px_rgba(0,0,0,0.5)] hover:from-[var(--btn-primary-hover-from)] hover:via-[var(--btn-primary-hover-via)] hover:to-[var(--btn-primary-hover-to)] hover:text-[var(--btn-primary-hover-text)] hover:border-[var(--btn-primary-hover-border)] hover:opacity-90 active:from-[var(--btn-primary-active-from)] active:via-[var(--btn-primary-active-via)] active:to-[var(--btn-primary-active-to)] px-4 py-1.5 text-[12px] cursor-pointer" disabled>
							Continue
							<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1" aria-hidden="true">
								<path d="m9 18 6-6-6-6"></path>
							</svg>
						</button>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	const tabs = document.querySelectorAll('[data-buypoints-tab]');
	const panels = document.querySelectorAll('[data-buypoints-panel]');
	const cards = document.querySelectorAll('[data-buypoints-card]');
	const agree = document.getElementById('buypoints-agree');
	const continueButton = document.getElementById('buypoints-continue');
	const pagseguroQuantity = document.getElementById('pagseguro-quantity');
	const pagseguroQuantityHidden = document.getElementById('pagseguro-quantity-hidden');

	function selectedRadio() {
		return document.querySelector('input[name="buypoints_product"]:checked');
	}

	function updateContinueState() {
		if (!continueButton) return;
		continueButton.disabled = !(selectedRadio() && agree && agree.checked);
	}

	function clearSelection() {
		document.querySelectorAll('input[name="buypoints_product"]').forEach(function (radio) {
			radio.checked = false;
		});
		cards.forEach(function (card) {
			card.classList.remove('is-selected');
		});
		updateContinueState();
	}

	tabs.forEach(function (tab) {
		tab.addEventListener('click', function () {
			const target = tab.getAttribute('data-buypoints-tab');

			tabs.forEach(function (item) {
				item.classList.toggle('is-active', item === tab);
			});

			panels.forEach(function (panel) {
				panel.classList.toggle('hidden', panel.getAttribute('data-buypoints-panel') !== target);
			});

			clearSelection();
		});
	});

	cards.forEach(function (card) {
		const radio = card.querySelector('input[type="radio"]');
		card.addEventListener('click', function () {
			if (!radio) return;
			cards.forEach(function (item) {
				item.classList.remove('is-selected');
			});
			radio.checked = true;
			card.classList.add('is-selected');
			updateContinueState();
		});
	});

	if (agree) {
		agree.addEventListener('change', updateContinueState);
	}

	if (pagseguroQuantity && pagseguroQuantityHidden) {
		pagseguroQuantity.addEventListener('input', function () {
			pagseguroQuantityHidden.value = pagseguroQuantity.value || '1';
		});
	}

	if (continueButton) {
		continueButton.addEventListener('click', function () {
			const radio = selectedRadio();
			if (!radio || continueButton.disabled) return;

			const form = document.getElementById(radio.getAttribute('data-buypoints-form'));
			if (!form) return;

			if (pagseguroQuantity && pagseguroQuantityHidden) {
				pagseguroQuantityHidden.value = pagseguroQuantity.value || '1';
			}

			form.submit();
		});
	}
});
</script>

<?php include 'layout/overall/footer.php'; ?>
