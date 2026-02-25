 <main class="col-11 col-lg-7 form-signin bg-body-tertiary rounded m-auto">
     <form method="POST" action="?page=login">
         <!-- Logo -->
         <img class="mb-4" src="./files/photos/logo.png" alt="" height="57">
         <!-- Nagłówek formularza -->
         <h1 class="h3 mb-2 fw-normal">Witamy w Ksiegarni ZSE</h1>
         <p class="text-secondary mb-3">Zaloguj sie, do panelu Admina.</p>
         <?php if (isset($error)): ?>
             <div class="alert alert-danger" role="alert"><?= $error ?></div>
         <?php endif; ?>
         <!-- Pole email -->
         <div class="form-floating">
             <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
             <label for="email">Email do konta</label>
         </div>
         <!-- Pole hasło -->
         <div class="form-floating position-relative">
             <input type="password" class="form-control" id="password" name="password" placeholder="Haslo..." required>
             <label for="password">Haslo</label>

             <!-- Ikona oka -->
             <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-2"
                 onclick="togglePassword()" style="background: none; border: none; z-index: 5;">
                 <i id="eyeIcon" class="fa-solid bi-eye-slash"></i>
             </button>
         </div>

         <script>
             function togglePassword() {
                 const passwordField = document.getElementById("password");
                 if (passwordField.type === "password") {
                     passwordField.type = "text";
                 } else {
                     passwordField.type = "password";
                 }
             }
         </script>

         <!-- Link do odzyskiwania hasła -->
         <p class="my-3"><a href="?page=forgotten-password">Nie pamietasz hasla?</a></p>
         <!-- Przycisk logowania -->
         <button class="btn btn-primary w-100 py-2" type="submit">Zaloguj sie</button>
     </form>


 </main>