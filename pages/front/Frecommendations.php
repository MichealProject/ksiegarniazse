<div class="container mt-5 mb-5">

    <!-- Nagłówek -->
    <header>
        <h2 class="mb-4 fw-bold">Najchętniej kupowane książki</h2>
    </header>

    <?php if (!empty($topProducts)): ?>

        <?php
        // Funkcja do bezpiecznego wyświetlania tekstu w HTML
        function e(string $v): string {
            return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
        }
        ?>

        <!-- ===================== LISTA 1 ===================== -->
        <section class="top-products-list1 mb-5">
            <h5 class="mb-3">Top 1-10 <i class="bi bi-star-fill text-warning"></i></h5>

            <div class="position-relative">

                <!-- Przycisk przewijania w lewo -->
                <button id="left1"
                    class="btn btn-secondary position-absolute top-50 start-0 translate-middle-y d-flex align-items-center justify-content-center"
                    style="width:40px;height:40px;z-index:10; border-radius: 0 10% 10% 0;">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <!-- Kontener karuzeli produktów -->
                <div class="d-flex overflow-hidden carousel1" style="gap:1rem;">

                    <?php foreach ($list1 as $book): 
                        $imagePath = "graphic/books/{$book['id_book']}.jpg";
                        if (!file_exists($imagePath)) {
                            $imagePath = "graphic/books/0.jpg";
                        }
                        $productLink = "?page=product&id=".$book['id_book'];
                    ?>

                    <!-- Pojedyncza karta produktu -->
                    <div class="flex-shrink-0">

                        <div class="card shadow book-card position-relative h-100">

                            <!-- Link do produktu -->
                            <a href="<?= e($productLink) ?>" class="stretched-link"></a>

                            <!-- Obraz produktu -->
                            <img src="<?= e($imagePath) ?>" class="card-img-top book-img" alt="<?= e($book['title']) ?>">

                            <!-- Treść karty -->
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= e($book['title']) ?></h5>
                                <p class="card-text small"><?= e($book['authors']) ?></p>
                                <div class="card-footer-wrapper">
                                    <strong class="price"><?= number_format($book['price'], 2) ?> zł</strong>
                                </div>
                            </div>

                            <!-- Przycisk dodania do koszyka -->
                            <button type="button" class="add-to-cart btn btn-outline-primary"
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

                <!-- Przycisk przewijania w prawo -->
                <button id="right1"
                    class="btn btn-secondary position-absolute top-50 end-0 translate-middle-y d-flex align-items-center justify-content-center"
                    style="width:40px;height:40px;z-index:10; border-radius: 10% 0 0 10%;">
                    <i class="bi bi-chevron-right"></i>
                </button>

            </div>
        </section>

        <!-- ===================== LISTA 2 ===================== -->
        <section class="top-products-list2 mb-5">
            <h5 class="mb-3">Top 11-20 <i class="bi bi-fire text-danger"></i></h5>

            <div class="position-relative">

                <!-- Przycisk przewijania w lewo -->
                <button id="left2"
                    class="btn btn-secondary position-absolute top-50 start-0 translate-middle-y d-flex align-items-center justify-content-center"
                    style="width:40px;height:40px;z-index:10; border-radius: 0 10% 10% 0;">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <!-- Kontener karuzeli produktów -->
                <div class="d-flex overflow-hidden carousel2" style="gap:1rem;">

                    <?php foreach ($list2 as $book): 
                        $imagePath = "graphic/books/{$book['id_book']}.jpg";
                        if (!file_exists($imagePath)) {
                            $imagePath = "graphic/books/0.jpg";
                        }
                        $productLink = "?page=product&id=".$book['id_book'];
                    ?>

                    <!-- Pojedyncza karta produktu -->
                    <div class="flex-shrink-0">

                        <div class="card shadow book-card position-relative h-100">

                            <!-- Link do produktu -->
                            <a href="<?= e($productLink) ?>" class="stretched-link"></a>

                            <!-- Obraz produktu -->
                            <img src="<?= e($imagePath) ?>" class="card-img-top book-img" alt="<?= e($book['title']) ?>">

                            <!-- Treść karty -->
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= e($book['title']) ?></h5>
                                <p class="card-text small"><?= e($book['authors']) ?></p>
                                <div class="card-footer-wrapper">
                                    <strong class="price"><?= number_format($book['price'], 2) ?> zł</strong>
                                </div>
                            </div>

                            <!-- Przycisk dodania do koszyka -->
                            <button type="button" class="add-to-cart btn btn-outline-primary"
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

                <!-- Przycisk przewijania w prawo -->
                <button id="right2"
                    class="btn btn-secondary position-absolute top-50 end-0 translate-middle-y d-flex align-items-center justify-content-center"
                    style="width:40px;height:40px;z-index:10; border-radius: 10% 0 0 10%;">
                    <i class="bi bi-chevron-right"></i>
                </button>

            </div>
        </section>

    <?php else: ?>
        <!-- Komunikat, gdy brak rekomendowanych produktów -->
        <div class="alert alert-light border shadow-sm">
            Brak rekomendacji.
        </div>
    <?php endif; ?>

</div>

<!-- Overlay koszyka -->
<div id="cartOverlay" class="cart-overlay">
    <div id="cartModal" class="cart-modal p-3 bg-white rounded shadow">
        <div id="cartModalContent"></div>
    </div>
</div>

<script>
/* ===================== SKRYPT KARUZELI ===================== */
function initCarousel(carouselSelector, leftBtnId, rightBtnId) {

    // Pobranie kontenera i przycisków
    const container = document.querySelector(carouselSelector);
    const leftBtn = document.getElementById(leftBtnId);
    const rightBtn = document.getElementById(rightBtnId);

    // Określa liczbę widocznych kart w zależności od szerokości okna
    function getVisibleCount() {
        const w = window.innerWidth;
        if (w >= 1400) return 6;
        if (w >= 1200) return 5;
        if (w >= 992) return 4;
        if (w >= 768) return 3;
        if (w >= 300) return 2;
        return 1;
    }

    // Aktualizacja szerokości kart
    function updateCardWidths() {
        const visible = getVisibleCount();
        const gap = 16; // px, odpowiada gap:1rem
        const totalGap = gap * (visible - 1);
        const cardWidth = `calc((100% - ${totalGap}px) / ${visible})`;
        container.querySelectorAll('.flex-shrink-0').forEach(card => {
            card.style.width = cardWidth;
        });
    }

    // Włącz/wyłącz widoczność przycisków przewijania
    function updateButtons() {
        const maxScroll = container.scrollWidth - container.clientWidth;
        leftBtn.style.opacity = container.scrollLeft <= 0 ? "0" : "1";
        leftBtn.style.pointerEvents = container.scrollLeft <= 0 ? "none" : "auto";
        rightBtn.style.opacity = container.scrollLeft >= maxScroll - 1 ? "0" : "1";
        rightBtn.style.pointerEvents = container.scrollLeft >= maxScroll - 1 ? "none" : "auto";
    }

    // Przewijanie w prawo
    rightBtn.onclick = () => {
        const cardWidth = container.querySelector('.flex-shrink-0').offsetWidth + 16;
        container.scrollBy({ left: cardWidth * getVisibleCount(), behavior: 'smooth' });
    };

    // Przewijanie w lewo
    leftBtn.onclick = () => {
        const cardWidth = container.querySelector('.flex-shrink-0').offsetWidth + 16;
        container.scrollBy({ left: -cardWidth * getVisibleCount(), behavior: 'smooth' });
    };

    // Aktualizacja przy resize okna
    window.addEventListener('resize', () => {
        updateCardWidths();
        updateButtons();
    });

    // Aktualizacja przy scrollu karuzeli
    container.addEventListener('scroll', updateButtons);

    // Inicjalizacja
    updateCardWidths();
    updateButtons();
}

// Inicjalizacja obu karuzel
initCarousel('.carousel1', 'left1', 'right1');
initCarousel('.carousel2', 'left2', 'right2');
</script>