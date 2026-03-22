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

// Dodanie produktu do ulubionych
function addToFav(id) {
    fetch('pages/addToFavorites.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: id })
    });
}

// Ustawienie aktualizacji sumy ceny po zwiększeniu ilości
document.addEventListener('DOMContentLoaded', () => {

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

    refreshCartDisplay();
});