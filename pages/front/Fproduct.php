<?php
// Funkcja escapująca HTML
function e($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
$inStock = (int)$product['stock'] > 0;

// Przygotowanie ścieśki do grafiki
$fileName = $product['id_book'];

// Ścieżka do grafiki
$imagePath = 'graphic/books/'.$fileName.'.jpg';

if (!file_exists($imagePath)) {
    $imagePath = 'graphic/books/0.jpg';
}
?>

<div class="container pt-5">

    <section class="row g-5">

        <!-- Lewa część z grafiką książki -->
        <div class="col-lg-7 d-flex justify-content-center position-relative fp-left-col">

            <!-- Grafika książki -->
            <div class="bg-white py-4 d-inline-block">
                <img src="<?= e($imagePath) ?>" class="img-fluid fp-book-image">
            </div>

            <!-- Przycisk ulubione -->
            <div class="position-absolute top-0 end-0 mt-3 me-4">
                <button class="btn d-flex align-items-center justify-content-center rounded-circle fp-favorite-btn">
                    <i class="bi bi-heart" style="font-size: 1.1rem;"></i>
                </button>
            </div>

        </div>

        <!-- Prawa część z opisem -->
        <div class="col-lg-5 fp-right-col">

            <h1 class="fw-bold"><?= e($product['title']) ?></h1>

            <!-- Autorzy -->
            <p class="text-muted mb-2">
                <?php foreach ($authors as $index => $author): ?>
                    <?= e($author['name'] . ' ' . $author['surname']) ?>
                    <?= $index < count($authors) - 1 ? ', ' : '' ?>
                <?php endforeach; ?>
            </p>

            <span class="badge bg-dark mb-3"><?= e($product['category']) ?></span>

            <hr>

            <!-- Cena -->
            <h2 class="text-primary fw-bold">
                <?= number_format((float)$product['price'],2,',',' ') ?> zł
            </h2>

            <!-- Status -->
            <?php if ($inStock): ?>
                <p class="text-success fw-semibold mb-1">
                    <i class="bi bi-check-circle-fill"></i>
                    Dostępna (<?= (int)$product['stock'] ?> szt.)
                </p>
                <p class="text-success small">
                    <i class="bi bi-shop"></i>
                    Dostępna w ksiegarni
                </p>
            <?php else: ?>
                <p class="text-danger fw-semibold">
                    <i class="bi bi-x-circle-fill"></i>
                    Książka niedostępna
                </p>
            <?php endif; ?>

            <!-- Koszyk -->
            <button class="btn btn-primary w-100 btn-lg"
                    data-id="<?= $product['id_book'] ?>"
                    data-title="<?= htmlspecialchars($product['title']) ?>"
                    data-price="<?= $product['price'] ?>"
                    onclick="addToCart(this)">
                    <i class="bi bi-cart-plus"></i>
                    Dodaj do koszyka
            </button>
            <div id="cartOverlay" class="cart-overlay">
                <div id="cartModal" class="cart-modal p-3 bg-white rounded shadow">
                    <div id="cartModalContent"></div>
                </div>
            </div>
            <hr class="my-4">

            <!-- Informacje o zamówieniu -->
            <div class="small text-muted">

                <p class="mb-2">
                    <i class="bi bi-truck"></i>
                    Dostawa za darmo do salonu
                </p>

                <p class="mb-2">
                    <i class="bi bi-arrow-repeat"></i>
                    Zwrot do 14 dni
                </p>

                <p class="mb-2">
                    <i class="bi bi-clock"></i>
                    Przewidywany czas wysyłki: 1 dzień roboczy
                </p>

            </div>

            <hr class="my-4">

            <!-- Informacje o produkcie -->
            <div class="row small">
                <div class="col-md-6">
                    <strong>Okładka:</strong> <?= e($product['cover_type']) ?>
                </div>
                <div class="col-md-6">
                    <strong>Liczba stron:</strong> <?= (int)$product['pages'] ?>
                </div>
                <div class="col-md-6 mt-2">
                    <strong>Data wydania:</strong> <?= e($product['release_date']) ?>
                </div>
                <div class="col-md-6 mt-2">
                    <strong>Wydawca:</strong> <?= e($product['supplier']) ?>
                </div>
            </div>

        </div>
    </section>

    <hr class="my-4">

    <!-- Opis -->
    <article class="mt-5">
        <h3>Opis książki</h3>
        <p class="lead">
            <?= nl2br(e($product['description'])) ?>
        </p>
    </article>

    <!-- Szczegółowe informacje -->
    <section class="mt-5">
        <h4>Szczegółowe informacje</h4>
        <table class="table table-striped mt-3">
            <tr>
                <th>Tytuł</th>
                <td><?= e($product['title']) ?></td>
            </tr>
            <tr>
                <th>Kategoria</th>
                <td><?= e($product['category']) ?></td>
            </tr>
            <tr>
                <th>Autorzy</th>
                <td>
                    <?php foreach ($authors as $index => $author): ?>
                        <?= e($author['name']." ".$author['surname']) ?>
                        <?= $index < count($authors)-1 ? ', ' : '' ?>
                    <?php endforeach; ?>
                </td>
            </tr>
            <tr>
                <th>Liczba stron</th>
                <td><?= (int)$product['pages'] ?></td>
            </tr>
            <tr>
                <th>Typ okładki</th>
                <td><?= e($product['cover_type']) ?></td>
            </tr>
            <tr>
                <th>Data wydania</th>
                <td><?= e($product['release_date']) ?></td>
            </tr>
            <tr>
                <th>Wydawca</th>
                <td><?= e($product['supplier']) ?></td>
            </tr>
        </table>
    </section>

</div>