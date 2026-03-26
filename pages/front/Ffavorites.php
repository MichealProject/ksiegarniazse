<?php 
function e($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); } 
?>

<div class="container pt-5">

    <!-- Małe powiadomienie -->
    <div id="toastContainer" class="d-none position-fixed bottom-0 end-0 d-flex flex-column gap-2 p-5" style="z-index:999;"></div>

    <!-- Nagłówek -->
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h2><?= e($user['name']) ?>, to Twoja lista ulubionych</h2>
    </header>

    <?php if(empty($favorites)): ?>
        <!-- Licznik produktów -->
        <section class="card p-4 d-flex flex-column align-items-center justify-content-center text-center">
            <img src="graphic/icons/list.png" style="max-width:120px;" class="mb-3">
            <h3 class="mb-0">Twoja lista jest pusta</h3>
        </section>
    <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <p class="hideFavEl">Liczba produktów na liście: <span id="favCount"><?= $favCount ?></span></p>
            <?php if(!empty($favorites)): ?>
                <button class="hideFavEl btn btn-outline-danger btn-sm" onclick="clearFav()">Wyczyść listę</button>
            <?php endif; ?>
        </div>
        <!-- Produkty na liście -->
        <section class="list-group favorites-container">
            <?php foreach($favorites as $item): ?>
                <?php
                    $fileName = $item['id_book'];
                    $imagePath = 'graphic/books/'.$fileName.'.jpg';
                    if(!file_exists($imagePath)) $imagePath = 'graphic/books/0.jpg';
                ?>
                <div class="list-group-item d-flex align-items-center justify-content-between gap-3 py-3 fav-item" data-id="<?= $item['id_book'] ?>">
                    
                    <div style="flex-shrink:0;">
                        <img src="<?= e($imagePath) ?>" alt="<?= e($item['title']) ?>" 
                             style="width:120px; height:180px; object-fit:cover;">
                    </div>

                    <div class="flex-grow-1">
                        <h5><?= e($item['title']) ?></h5>
                        <p class="mb-1">Cena: <?= number_format($item['price'],2,',',' ') ?> zł</p>
                        <p class="mb-1 <?= $item['stock'] > 0 ? 'text-success' : 'text-danger' ?>"><?= $item['stock'] > 0 ? 'Dostępna' : 'Niedostępna' ?></p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <input type="number" class="form-control form-control-sm fav-quantity" 
                               value="<?= (int)$item['quantity'] ?>" min="1" max="100" 
                               data-id="<?= $item['id_book'] ?>" style="width:70px;">
                        
                        <button class="btn btn-danger btn-sm rounded-1" data-id="<?= $item['id_book'] ?>" onclick="removeFav(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <div class="mt-4">
            <button class="hideFavEl btn btn-primary" onclick="moveAllToCart()">Przenieś wszystkie do koszyka</button>
        </div>
    <?php endif; ?>
</div>