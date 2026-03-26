<div class="row g-0 mx-0">
    <!-- Główna zawartość strony -->
    <div class='d-flex flex-column col-12 col-lg-10 offset-0 offset-lg-1 px-5 py-3 pb-2'>
            
        <section class="container mb-5">

            <div id="mainBanner" class="carousel slide shadow" data-bs-pause="false" data-bs-ride="carousel">

                <div class="carousel-inner">
                    <!-- Slide 1-4 -->
                    <?php 
                    $banners = ['1','2','3','4'];
                    foreach ($banners as $i => $num): 
                        $active = $i === 0 ? 'active' : '';
                    ?>
                        <div class="carousel-item <?= $active ?>">
                            <img src="graphic/banner/<?= $num ?>.jpg" class="d-block w-100 banner-img" alt="Baner <?= $num ?>">
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Strzałka lewa -->
                <button class="carousel-control-prev d-flex align-items-center justify-content-center custom-arrow left-arrow"
                        type="button" data-bs-target="#mainBanner" data-bs-slide="prev">
                    <i class="bi bi-chevron-left fs-5 text-dark"></i>
                </button>

                <!-- Strzałka prawa -->
                <button class="carousel-control-next d-flex align-items-center justify-content-center custom-arrow right-arrow"
                        type="button" data-bs-target="#mainBanner" data-bs-slide="next">
                    <i class="bi bi-chevron-right fs-5 text-dark"></i>
                </button>
            </div>
        </section>

        <h2 class="mb-4">Nasze książki</h2>

        <div class="row">
            <?php 
                // Funkcja escapująca HTML
                function e(string $v): string {
                    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
                }

                // Pętla po książkach
                foreach ($books as $book): 
                    // Przygotowanie ścieżki do grafiki
                    $fileName = $book['id_book'];
                    $imagePath = "graphic/books/$fileName.jpg";
                    if (!file_exists($imagePath)) {
                        $imagePath = 'graphic/books/0.jpg';
                    }

                    // Link do podstrony produktu
                    $productLink = "?page=product&id=".$book['id_book'];
            ?>
                <!-- Dane produktu i jego wygląd -->
                <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-6 mb-4">
                    <div class="card shadow book-card position-relative" >

                        <!-- Klikalność całej karty -->
                        <a href="<?= e($productLink) ?>" class="stretched-link"></a>

                        <!-- Obraz -->
                        <img src="<?= e($imagePath) ?>" class="card-img-top book-img" alt="<?= e($book['title']) ?>">

                        <!-- Treść pod obrazem -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= e($book['title']) ?></h5>
                            <p class="card-text small"><?= e($book['category']) ?></p>

                            <div class="card-footer-wrapper">
                                <strong class="price"><?= number_format($book['price'], 2) ?> zł</strong>
                            </div>
                        </div>

                        <!-- Przycisk koszyka -->
                        <button type="button"
                            class="add-to-cart btn btn-outline-primary"
                            data-id="<?= $book['id_book'] ?>"
                            data-title="<?= htmlspecialchars($book['title']) ?>"
                            data-price="<?= $book['price'] ?>"
                            onclick="addToCart(this)">
                            <i class="bi bi-cart"></i>
                        </button>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="cartOverlay" class="cart-overlay">
            <div id="cartModal" class="cart-modal p-3 bg-white rounded shadow">
                <div id="cartModalContent"></div>
            </div>
        </div>
        <!-- Paginacja -->
        <?php if ($totalPages > 1): 
            $range = 3;
            $start = max(1, $pageNumber - $range);
            $end = min($totalPages, $pageNumber + $range);
        ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Lewa strzałka -->
                    <li class="page-item <?= $pageNumber <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pageNumber=<?= $pageNumber - 1 ?>">&lt;</a>
                    </li>
                    <!-- Środkowe zakładki -->
                    <?php if ($start > 1): ?>
                        <li class="page-item"><a class="page-link" href="?page=1">1</a></li>
                        <?php if ($start > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= $i === $pageNumber ? 'active' : '' ?>">
                            <a class="page-link" href="?pageNumber=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item"><a class="page-link" href="?page=<?= $totalPages ?>"><?= $totalPages ?></a></li>
                    <?php endif; ?>
                    <!-- Prawa strzałka -->
                    <li class="page-item <?= $pageNumber >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pageNumber=<?= $pageNumber + 1 ?>">&gt;</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>
