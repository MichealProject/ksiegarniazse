<section class="container mt-5 mb-5">

    <!-- Nagłówek sekcji -->
    <header>
        <h2 class="mb-4 fw-bold">Twoja historia zamówień</h2>
    </header>

    <?php if (empty($orders)): ?>
        <!-- Komunikat, gdy brak zamówień -->
        <div class="alert alert-light border shadow-sm">
            Nie masz jeszcze żadnych zamówień.
        </div>
    <?php else: ?>

        <?php foreach ($orders as $order): ?>

            <!-- Pojedyncze zamówienie -->
            <div class="card mb-4 shadow rounded">

                <!-- Nagłówek zamówienia -->
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Zamówienie #<?= $order['id_order'] ?></span>
                    <span class="small"><?= $order['order_date'] ?></span>
                </div>

                <!-- Treść zamówienia -->
                <div class="card-body">

                    <?php foreach ($order['items'] as $item): ?>
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <span class="fw-medium"><?= $item['title'] ?> (x<?= $item['quantity'] ?>)</span>
                            <span class="fw-semibold"><?= $item['price'] * $item['quantity'] ?> zł</span>
                        </div>
                    <?php endforeach; ?>

                    <!-- Łączna cena zamówienia -->
                    <div class="d-flex justify-content-between rounded p-2 mt-3 fw-semibold" style="background-color: #f1f1f1">
                        <span>Łączna cena</span>
                        <span><?= $order['total_price'] ?> zł</span>
                    </div>

                    <!-- Status zamówienia -->
                    <span class="badge 
                        <?php 
                            switch (strtolower($order['status'])) {
                                case 'paid': echo 'bg-success'; break;
                                case 'shipped': echo 'bg-warning text-dark'; break;
                                case 'completed': echo 'bg-info text-dark'; break;
                                case 'cancelled': echo 'bg-danger'; break;
                                default: echo 'bg-secondary'; break;
                            } 
                        ?>
                        mt-3"><?= $order['status'] ?></span>

                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</section>