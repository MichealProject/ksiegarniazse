<?php
$isDeliveryStep = ($currentStep ?? 'cart') === 'delivery';
$isOrderCompleted = !empty($orderSuccess);
?>

<div class="container my-5">


    <?php if ($isOrderCompleted): ?>
        <section class="card p-4 p-md-5 shadow-sm checkout-success-card mb-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="checkout-success-icon">
                    <i class="bi bi-check-lg"></i>
                </span>
                <div>
                    <h4 class="mb-1">Dziękujemy za zamówienie</h4>
                    <p class="text-muted mb-0">Płatność i realizacja zostaną rozpoczęte po potwierdzeniu.</p>
                </div>
            </div>

            <div class="alert alert-success mb-4" role="alert">
                <?php echo htmlspecialchars($orderSuccess); ?>
            </div>

            <div class="d-flex flex-column flex-md-row gap-3">
                <a href="?page=home" class="btn btn-dark px-4">Wróć na stronę główną</a>
                <a href="?page=cart" class="btn btn-outline-secondary px-4">Nowe zakupy</a>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($orderError)): ?>
        <div class="alert alert-danger shadow-sm mb-4" role="alert">
            <?php echo htmlspecialchars($orderError); ?>
        </div>
    <?php endif; ?>

    <?php if (!$isOrderCompleted && !$isDeliveryStep): ?>
        <div class="row">
            <section class="products col-lg-8">
                <?php if (empty($cartItems)): ?>
                    <section class="card p-4 d-flex flex-column align-items-center justify-content-center cart-item text-center">
                        <img src="graphic/icons/empty_cart.png"
                            style="max-width:120px;"
                            class="mb-3">

                        <h3 class="mb-0">Brak produktów w koszyku</h3>
                    </section>
                <?php else: ?>
                    <?php foreach ($cartItems as $item): ?>
                        <section class="card mb-3 p-3 d-flex flex-row justify-content-between cart-item position-relative"
                                 style="box-shadow: 0px 2px 5px rgb(0 0 0 / 0.15);"
                                 data-id="<?php echo $item['id']; ?>">

                            <div class="d-flex">
                                <img src="graphic/books/<?php echo $item['id']; ?>.jpg"
                                    width="100px"
                                    class="me-3 border border-dark-subtle"
                                    alt="<?php echo htmlspecialchars($item['title']); ?>">

                                <div class="py-3">
                                    <h6 class="fw-bold">
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </h6>
                                    <p style="font-size: 0.9rem;" class="text-success">Wysyłamy w 24h</p>
                                </div>
                            </div>

                            <div class="d-flex flex-column align-items-end">
                                <div class="d-flex align-items-center">
                                    <input type="number"
                                        value="<?php echo $item['quantity']; ?>"
                                        min="1"
                                        max="100"
                                        step="1"
                                        inputmode="numeric"
                                        class="form-control me-3 qty-input"
                                        style="width:70px"
                                        data-price="<?php echo $item['price']; ?>"
                                        data-id="<?php echo $item['id']; ?>">

                                    <strong class="item-total" style="font-size:18px;">
                                        <?php echo number_format($item['price'] * $item['quantity'], 2); ?> zł
                                    </strong>
                                </div>

                                <div class="text-end mt-1">
                                    <small class="text-muted">
                                        Cena: <?php echo number_format($item['price'], 2); ?> zł
                                    </small>
                                </div>
                            </div>

                            <div class="position-absolute bottom-0 end-0 p-3 d-flex gap-4">
                                <button class="btn p-0 border-0 bg-transparent"
                                    onclick="toggleFav(this)"
                                    data-id="<?= $item['id'] ?>">
                                    <i class="bi <?= in_array($item['id'], $favIds) ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i>
                                </button>

                                <button class="btn p-0 border-0 bg-transparent" onclick="updateCart('remove', <?= $item['id']; ?>)">
                                    <i class="bi bi-trash" style="font-size:18px;"></i>
                                </button>
                            </div>
                        </section>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <aside class="col-lg-4 mt-4 mt-lg-0">
                <div class="card p-4 shadow-sm checkout-summary-card" style="position: sticky; top: 80px; transition: top 0.2s;">
                    <div class="d-flex justify-content-between">
                        <span>Wartość produktów</span>
                        <strong id="totalPrice"><?php echo number_format($total, 2); ?> zł</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between fs-5">
                        <strong>Do zapłaty</strong>
                        <strong id="finalPrice"><?php echo number_format($total, 2); ?> zł</strong>
                    </div>

                    <div class="mt-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-truck me-2 fs-4 text-primary"></i>
                            <p class="mb-0" style="font-size: 0.9rem;">
                                Darmowa dostawa dla zamówienia z oznaczeniem Premium: do salonu od 0 zł, kurierem i do punktów odbioru od 40 zł
                            </p>
                        </div>

                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-tag me-2 fs-4 text-success"></i>
                            <p class="mb-0" style="font-size: 0.9rem;">
                                Do 30% zniżki na oznaczone produkty online
                            </p>
                        </div>

                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-shop me-2 fs-4 text-warning"></i>
                            <p class="mb-0" style="font-size: 0.9rem;">
                                15% zniżki przy zakupach od 50 zł w salonach (m.in. na książki, muzykę, zabawki)
                            </p>
                        </div>
                    </div>

                    <?php if (empty($cartItems)): ?>
                        <button class="btn btn-secondary w-100 mt-4" disabled>
                            KOSZYK JEST PUSTY
                        </button>
                    <?php else: ?>
                        <a class="btn btn-danger w-100 mt-4" href="?page=cart&delivery=1">
                            PRZEJDŹ DO DOSTAWY
                        </a>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    <?php elseif (!$isOrderCompleted): ?>
        <div class="row g-4">
            <section class="col-lg-8">
                <section id="deliveryFormCard" class="card p-4 shadow-sm checkout-form-card">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="mb-0">Dane do dostawy</h5>
                        <span class="badge text-bg-dark px-3 py-2">Krok 2</span>
                    </div>

                    <p class="text-muted small mb-4">
                        Uzupełnij dane, a następnie złóż zamówienie.
                    </p>

                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="alert alert-light border small mb-4" role="alert">
                            Możesz kupić bez logowania. Jeśli się zalogujesz, dane zapiszą się do konta.
                        </div>
                    <?php endif; ?>

                    <form method="post" action="?page=cart&delivery=1" class="checkout-form">
                        <input type="hidden" name="action" value="place_order">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="first_name">Imię</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($checkoutData['first_name']); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="last_name">Nazwisko</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($checkoutData['last_name']); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="email">Adres e-mail</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($checkoutData['email']); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="phone">Telefon</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($checkoutData['phone']); ?>" placeholder="np. 600700800" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="street">Ulica i numer</label>
                                <input type="text" class="form-control" id="street" name="street" value="<?php echo htmlspecialchars($checkoutData['street']); ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="postal_code">Kod pocztowy</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($checkoutData['postal_code']); ?>" placeholder="00-000" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="city">Miasto</label>
                                <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($checkoutData['city']); ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="country">Kraj</label>
                                <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($checkoutData['country']); ?>" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="delivery_method">Sposób dostawy</label>
                                <select class="form-select" id="delivery_method" name="delivery_method" required>
                                    <option value="courier" <?php echo $checkoutData['delivery_method'] === 'courier' ? 'selected' : ''; ?>>Kurier</option>
                                    <option value="pickup_point" <?php echo $checkoutData['delivery_method'] === 'pickup_point' ? 'selected' : ''; ?>>Punkt odbioru</option>
                                    <option value="store_pickup" <?php echo $checkoutData['delivery_method'] === 'store_pickup' ? 'selected' : ''; ?>>Odbiór osobisty</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-md-row gap-3 mt-4 pt-3 border-top">
                            <a href="?page=cart" class="btn btn-outline-secondary px-4">
                                WRÓĆ DO KOSZYKA
                            </a>
                            <button type="submit" class="btn btn-success px-4 ms-md-auto">
                                ZAMÓW
                            </button>
                        </div>
                    </form>
                </section>
            </section>

            <aside class="col-lg-4">
                <div class="card p-4 shadow-sm checkout-summary-card checkout-delivery-summary">
                    <h5 class="mb-3">Podsumowanie zamówienia</h5>

                    <div class="checkout-mini-list">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="checkout-mini-item">
                                <div>
                                    <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                    <small><?php echo (int)$item['quantity']; ?> szt.</small>
                                </div>
                                <span><?php echo number_format($item['sum'], 2); ?> zł</span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Wartość produktów</span>
                        <strong><?php echo number_format($total, 2); ?> zł</strong>
                    </div>

                    <div class="d-flex justify-content-between fs-5">
                        <strong>Do zapłaty</strong>
                        <strong><?php echo number_format($total, 2); ?> zł</strong>
                    </div>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</div>
