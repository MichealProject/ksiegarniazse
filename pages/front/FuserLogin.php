<main class="col-11 col-lg-7 form-signin bg-body-tertiary rounded m-auto">
    <h1 class="h3 mb-2 fw-normal">Witamy w Ksiegarni ZSE</h1>
    <p class="text-secondary mb-3">Zaloguj się do konta użytkownika</p>
    <form method="post" action="?page=userLogin">
        <!-- Pole email -->
        <div class="form-floating mb-3">
            <input type="email" name="email" id="email" class="form-control" placeholder="email@przykladowy.com" pattern="^[^\s@]+@[^\s@]+\.[^\s@]{2,}$">
            <label for="email">Adres e-mail:</label>
        </div>
        <!-- Pole hasła -->
        <div class="form-floating">
            <input type="password" name="pass" id="pass" class="form-control" placeholder="Hasło..." pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,16}">
            <label for="pass">Hasło:</label>
            <!-- Włącz/wyłącz jawne hasło -->
            <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-2"
                 onclick="togglePassword()" style="background: none; border: none; z-index: 5;">
                 <i id="eyeIcon" class="fa-solid bi-eye-slash"></i>
             </button>
        </div>
        <!-- Przycisk niewylogowywania -->
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="stay" name="logout" id="logout">
            <label class="form-check-label" for="logout">Nie wylogowuj mnie</label>
        </div>
        <?php if (isset($err)): ?>
             <div class="alert alert-danger" role="alert"><?= $err ?></div>
         <?php endif; ?>
        <p class="my-3"><a href="?page=forgotten-password">Nie pamietasz hasla?</a></p>
        <p class="my-3">Nie masz konta?<a href="?page=register">Utwórz je!</a></p>
        <button type="submit" class="btn btn-primary w-100 py-2">Zaloguj się</button>
    </form>
</main>
<!-- Funkcja włącz/wyłącz jawne wyświetlanie hasła-->
<script>
function togglePassword() {
    const passwordField = document.getElementById("pass");
    if (passwordField.type === "password") {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
}
</script>
