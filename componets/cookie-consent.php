<?php
// Nie pokazuj banera, jeśli użytkownik już wyraził zgodę
if (isset($_COOKIE['user_cookie_consent'])) {
    return;
}
?>

<style>
    .cookie-consent-banner {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: #2d3748;
        color: white;
        padding: 1rem;
        z-index: 1050;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
    }

    .cookie-consent-banner p {
        margin: 0;
        margin-right: 1rem;
    }

    .cookie-consent-banner a {
        color: #63b3ed;
        text-decoration: underline;
    }

    .cookie-consent-banner .btn {
        margin-left: 0.5rem;
    }
</style>

<div id="cookie-banner" class="cookie-consent-banner" role="dialog" aria-live="polite">
    <p>
        Używamy plików cookie, aby poprawić jakość przeglądania, personalizować treści i analizować ruch. Klikając „Akceptuję”, wyrażasz zgodę na używanie przez nas plików cookie.
        <!-- W przyszłości możesz tu dodać link do polityki prywatności -->
        <!-- <a href="?page=privacy-policy">Dowiedz się więcej</a>. -->
    </p>
    <div>
        <button id="accept-cookies" type="button" class="btn btn-success">Akceptuję</button>
        <button id="decline-cookies" type="button" class="btn btn-danger">Odrzuć</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const banner = document.getElementById('cookie-banner');
        const acceptBtn = document.getElementById('accept-cookies');
        const declineBtn = document.getElementById('decline-cookies');

        const fireConsentEvent = () => {
            const event = new Event('consent-given');
            document.dispatchEvent(event);
        };

        // Funkcja do ustawiania ciasteczka
        const setCookie = (name, value, days) => {
            let expires = "";
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            const secureFlag = window.location.protocol === 'https:' ? '; secure' : '';
            // Dodajemy samesite=Strict dla bezpieczeństwa
            document.cookie = name + "=" + (value || "") + expires + "; path=/; samesite=Strict" + secureFlag;
        };

        acceptBtn.addEventListener('click', () => {
            setCookie('user_cookie_consent', 'accepted', 365);
            banner.style.display = 'none';
            fireConsentEvent(); // Uruchom zdarzenie informujące o zgodzie
        });

        declineBtn.addEventListener('click', () => {
            setCookie('user_cookie_consent', 'declined', 365);
            banner.style.display = 'none';
        });

        // Jeśli zgoda została już udzielona przy poprzedniej wizycie, od razu uruchom zdarzenie
        if (document.cookie.includes('user_cookie_consent=accepted')) {
            fireConsentEvent();
        }
    });
</script>