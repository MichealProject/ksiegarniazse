// Pobieranie koszyka z ciasteczek
function getCartFromCookie() {
    const cookie = document.cookie
        .split('; ')
        .find(row => row.startsWith('cart='));

    if (!cookie) return [];

    try {
        return JSON.parse(decodeURIComponent(cookie.split('=')[1]));
    } catch {
        return [];
    }
}

// Dodawanie produktu do koszyka
function addToCart(btn) {
    const id = btn.dataset.id;
    const title = btn.dataset.title;
    const price = btn.dataset.price;
    const img = "graphic/books/" + id + ".jpg";

    // Wysłanie żądania do pliku php
    fetch('pages/cart_components/addToCart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: id })
    })
    .then(res => res.json())
    .then(data => {

        if (data.status === "ok") {

            // Odświeża licznik produktów i podsumowanie koszyka
            refreshCartDisplay();

            const overlay = document.getElementById("cartOverlay");
            const modalContent = document.getElementById("cartModalContent");

            if (!overlay || !modalContent) return;

            // Pokazanie okna po dodaniu produktu do koszyka
            modalContent.innerHTML = `
                <div class="d-flex m-1 align-items-center justify-content-between position-relative">
                    <div class="d-flex">
                        <img class="border border-dark-subtle me-3" src="${img}" width="100">
                        <div class="py-4">
                            <h5 class="fw-bold">${title}</h5>
                            <small class="text-success">Dodano do koszyka</small><br>
                            <small>${price} zł</small>
                        </div>
                    </div>

                    <button class="btn btn-sm btn-primary d-flex ms-3 position-absolute bottom-0 end-0"
                        onclick="location.href='?page=cart'">
                        <span>Do koszyka</span>
                        <i class="bi bi-chevron-right ps-1"></i>
                    </button>

                    <button class="btn btn-sm position-absolute top-0 end-0" onclick="closeCartModal()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            `;

            overlay.classList.add("show");

            overlay.onclick = function(e) {
                if (e.target === overlay) closeCartModal();
            };
        }
    })
    .catch(err => console.error(err));
}

// Zamykanie okna dodanego produktu do koszyka
function closeCartModal() {
    document.getElementById("cartOverlay")?.classList.remove("show");
}

// Odświeżanie ilości produktów w koszyku, ceny produktów i wyświetlanie braku produktów w koszyku
function refreshCartDisplay() {
    const badge = document.getElementById('cart-count');

    let totalItems = 0;
    let totalPrice = 0;

    const cartItems = document.querySelectorAll('.cart-item');

    // Aktualizacja licznika produktó i sumy cen w koszyku
    if (cartItems.length > 0) {

        // Podliczanie ilości i cen produktów
        cartItems.forEach(item => {
            const qtyInput = item.querySelector('.qty-input');
            if (!qtyInput) return;

            const quantity = parseInt(qtyInput.value) || 0;
            const price = parseFloat(qtyInput.dataset.price) || 0;

            totalItems += quantity;
            totalPrice += price * quantity;
        });

        // Aktualizacja licznika w nav
        badge.textContent = `(${totalItems})`;
        badge.style.display = totalItems > 0 ? 'inline-block' : 'none';

        // Aktualizacja sumy produktów
        document.querySelectorAll('#totalPrice').forEach(el => el.textContent = totalPrice.toFixed(2) + " zł");

        // Doliczenie kosztu dostawy w przypadku ceny poniżej 40zł
        if(totalPrice<40 && totalPrice > 0){
            totalPrice+=18.5;
        }

        document.querySelectorAll('#finalPrice').forEach(el => el.textContent = totalPrice.toFixed(2) + " zł");

        return;
    }

    // Aktualizacja licznika produktów dla niezalogowanego użytkownika 
    const cart = getCartFromCookie() || [];

    if (cart.length > 0) {
        totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        badge.textContent = `(${totalItems})`;
        badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
        return;
    }

    // Aktualizacja licznika produktów dla zalogowanego użytkownika
    fetch('pages/cart_components/getCartCount.php')
        .then(res => res.json())
        .then(data => {
            if (data.status === "ok") {
                badge.textContent = `(${data.count})`;
                badge.style.display = data.count > 0 ? 'inline-block' : 'none';
            }
        });

    // Wyświetlenie komunikatu o pustym koszyku
    const productsSection = document.querySelector('.products');
    if (productsSection && productsSection.children.length === 0) {
        productsSection.innerHTML = `
            <section class="card p-4 d-flex flex-column align-items-center justify-content-center cart-item text-center">
                <img src="graphic/icons/empty_cart.png" style="max-width:120px;" class="mb-3">
                <h3 class="mb-0">Brak produktów w koszyku</h3>
            </section>
        `;
    }
}

// Aktualizacja koszyka, zmiana ilości lub usunięcie
function updateCart(action, product_id, quantity = null) {

    // Przygotowanie żądania do pliku PHP
    const body = { action, product_id };
    if (quantity !== null) body.quantity = quantity;

    // Wysłąnie żądania do pliku PHP
    fetch('pages/cart_components/cartAction.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    })
    .then(res => res.json())
    .then(data => {

        if (data.status === 'ok') {

            // Usuwanie produktu z koszyka
            if (action === 'remove') {

                const card = document.querySelector(`.cart-item[data-id="${product_id}"]`);
                if (card) card.remove();

                // Sprawdzenie czy są jeszcze produkty w koszyku
                const remaining = document.querySelectorAll('.cart-item');

                if (remaining.length === 0) {
                    const container = document.querySelector('.products');
                    // Wyświetlenie komunikatu o pustym koszyku
                    container.innerHTML = `
                        <section class="card p-4 d-flex flex-column align-items-center justify-content-center cart-item text-center">
                            <img src="graphic/icons/empty_cart.png"
                                style="max-width:120px;"
                                class="mb-3">

                            <h3 class="mb-0">Brak produktów w koszyku</h3>
                        </section>
                    `;
                }
            }

            refreshCartDisplay();
        }
    });
}

// Funkcja do dodawania/usunięcia produktu z ulubionych
function toggleFav(btn) {
    const id = btn.dataset.id;
    const icon = btn.querySelector('i');

    fetch('pages/fav_components/favAction.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'toggle', product_id: id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.redirect) {
            window.location.href = data.redirect;
            return;
        }
        if (data.status === 'ok') {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                icon.classList.toggle('bi-heart');
                icon.classList.toggle('bi-heart-fill');
                icon.classList.toggle('text-danger');
            }
        } else {
            console.error(data.message || 'Błąd dodawania do ulubionych');
        }
    })
    .catch(err => console.error(err));
}

// Usuwanie jednego produktu z ulubionych
function removeFav(btn) {
    const productId = btn.dataset.id;

    fetch('pages/fav_components/favAction.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'toggle', product_id: productId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'ok') {
            const card = document.querySelector(`.fav-item[data-id="${productId}"]`);
            if (card) card.remove();

            // Aktualizuje licznik ulubionych
            const favCountEl = document.getElementById('favCount');
             if (favCountEl) {
                let currentCount = parseInt(favCountEl.textContent) || 0;

                if (currentCount > 1) {
                    favCountEl.textContent = currentCount - 1;
                } else {
                    favCountEl.textContent = 0;

                    // Nie wyświetla elementów jeśli nie ma produktów
                    document.querySelectorAll('.hideFavEl').forEach(el => {
                        el.style.display = 'none';
                    });
                }
            }

            // Wyświetlana zawartość jeśli lista jest pusta
            if (document.querySelectorAll('.fav-item').length === 0) {
                document.querySelector('.favorites-container').innerHTML = `
                    <section class="card p-4 d-flex flex-column align-items-center justify-content-center text-center">
                        <img src="graphic/icons/list.png" style="max-width:120px;" class="mb-3">
                        <h3 class="mb-0">Twoja lista jest pusta</h3>
                    </section>
                `;
            }
        }
    })
    .catch(err => console.error(err));
}

// Przeniesienie wszystkich ulubionych produktów do koszyka
function moveAllToCart() {
    const items = document.querySelectorAll('.fav-quantity');
    const products = [];

    items.forEach(input => {
        const id = input.dataset.id;
        const quantity = parseInt(input.value) || 1;
        products.push({ product_id: id, quantity: Math.min(quantity, 100) });
    });

    if (products.length === 0) return;

    fetch('pages/fav_components/favAction.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'move_to_cart', products: products })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'ok') {
            showToast('Produkty zostały przeniesione do koszyka!');
            refreshCartCount();
        }
    })
    .catch(err => console.error(err));
}

// Czyszczenie wszystkich ulubionych produktów
function clearFav() {
    fetch('pages/fav_components/favAction.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'clear_all' })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'ok') {
            location.reload();
        }
    })
    .catch(err => console.error(err));
}

// Funkcja do małego powiadomienia
function showToast(message) {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;

    toastContainer.classList.remove('d-none');

    toastContainer.innerHTML = `
        <div class="align-items-center text-bg-success border-0 rounded-2 p-3 d-flex">
            <div class="toast-body flex-grow-1">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white ms-2"></button>
        </div>
    `;

    toastContainer.querySelector('.btn-close').addEventListener('click', () => {
        toastContainer.classList.add('d-none');
    });

    setTimeout(() => {
        toastContainer.classList.add('d-none');
    }, 2500);
}

// Odświeżenie licznika koszyka
function refreshCartCount() {
    fetch('pages/cart_components/getCartCount.php')
        .then(res => res.json())
        .then(data => {
            if (data.status === 'ok') {
                const badge = document.getElementById('cart-count');
                badge.textContent = `(${data.count})`;
                badge.style.display = data.count > 0 ? 'inline-block' : 'none';
            }
        });
}

// Funkcja przewijania produktów rekomendowanych
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
        const gap = 16;
        const totalGap = gap * (visible - 1);
        const cardWidth = `calc((100% - ${totalGap}px) / ${visible})`;
        container.querySelectorAll('.flex-shrink-0').forEach(card => {
            card.style.width = cardWidth;
        });
    }

    // Włączanie / wyłączanie widoczności przycisków przewijania
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

    // Aktualizacja przy zmianie rozmiaru okna
    window.addEventListener('resize', () => {
        updateCardWidths();
        updateButtons();
    });

    // Aktualizacja przy przewijaniu karuzeli
    container.addEventListener('scroll', updateButtons);

    // Inicjalizacja
    updateCardWidths();
    updateButtons();
}

// Inicjalizacja obu karuzel dla produktów rekomendowanych
initCarousel('.carousel1', 'left1', 'right1');
initCarousel('.carousel2', 'left2', 'right2');

document.addEventListener('DOMContentLoaded', () => {

    // Ustawienie aktualizacji sumy ceny po zwiększeniu ilości dla koszyka
    document.querySelectorAll('.qty-input').forEach(input => {

        let timeout = null;

        input.addEventListener('input', () => {

            // Ustawienie max i min ilości dla produktu
            let value = input.value.replace(/\D/g, '');
            let quantity = parseInt(value) || 1;

            if (quantity > 100) quantity = 100;
            if (quantity < 1) quantity = 1;

            input.value = quantity;

            const price = parseFloat(input.dataset.price);
            const id = input.dataset.id;

            const total = price * quantity;

            input.closest('.cart-item')
                .querySelector('.item-total')
                .textContent = total.toFixed(2) + ' zł';

            refreshCartDisplay();

            // Zmniejszenie ilości wysłanych zapytań przy szybkim zmienianiu ilości
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                updateCart('update_quantity', id, quantity);
            }, 400);
        });

    });

    document.querySelectorAll('.fav-quantity').forEach(input => {
    
        let timeout = null;
    
        input.addEventListener('input', () => {
            // Ograniczenie wartości min/max
            let value = input.value.replace(/\D/g, '');
            let quantity = parseInt(value) || 1;
    
            if(quantity > 100) quantity = 100;
            if(quantity < 1) quantity = 1;
    
            input.value = quantity;
    
            const id = input.dataset.id;
    
            // Zmniejszenie ilości zapytań przy szybkim wpisywaniu
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                fetch('pages/fav_components/getFavCount.php', {
                    method: 'POST',
                    headers: {'Content-Type':'application/json'},
                    body: JSON.stringify({id_book: id, quantity: quantity})
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status !== 'ok'){
                        console.error('Nie udało się zaktualizować ilości');
                    }
                });
            }, 400);
        });
    
    });

    refreshCartDisplay();
});


