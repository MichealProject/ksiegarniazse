<div class="container my-5">
    <header>
        <h3 class="mb-4">Produkty w koszyku</h3>
    </header>

    <div class="row">
        
        <!-- Produkty -->
        <section class="products col-lg-8">
            <?php if(empty($cartItems)): ?>
                <section class="card p-4 d-flex flex-column align-items-center justify-content-center cart-item text-center">
                    <img src="graphic/icons/empty_cart.png"
                        style="max-width:120px;"
                        class="mb-3">

                    <h3 class="mb-0">Brak produktów w koszyku</h3>
                </section>
            <?php else: ?>
                <?php foreach ($cartItems as $item): ?>

                    <section class="card mb-3 p-3 d-flex flex-row justify-content-between cart-item position-relative"
                             data-id="<?php echo $item['id']; ?>">

                        <!-- Zdjęcie i dane produktu -->
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

                        <!-- Ilość, cena -->
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

                        <!-- Ikony usuń i ulubione -->
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

        <!-- Podsumowanie i płatnośc -->
        <aside class="col-lg-4">
            <div class="card p-4 shadow-sm"
                     style="position: sticky; top: 80px; transition: top 0.2s;">

                <div class="d-flex justify-content-between">
                    <span>Wartość produktów</span>
                    <strong id="totalPrice">
                        <?php echo number_format($total, 2); ?> zł
                    </strong>
                </div>

                <hr>

                <div class="d-flex justify-content-between fs-5">
                    <strong>Do zapłaty</strong>
                    <strong id="finalPrice">
                        <?php echo number_format($total, 2); ?> zł
                    </strong>
                </div>

                <!-- Informacje -->
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

                <button class="btn btn-danger w-100 mt-4">
                    PRZEJDŹ DO DOSTAWY
                </button>

            </div>
        </aside>

    </div>
</div>
