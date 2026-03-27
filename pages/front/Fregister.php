<style>
    #info{
        display:none;
    }
    #warning,#warning2{
        display: none;
        color:red;
    }
    .invalid{
        color: red;
    }
    .valid{
        color: green;
    }
    #sPage{
        display: none;
    }
    #button2,#sButton{
        display: none;
    }
</style>
<main class="col-11 col-lg-7 form-signin bg-body-tertiary rounded m-auto">
    <form action="?page=register" method="post">
        <h1 class="h3 mb-2 fw-normal">Witamy w Ksiegarni ZSE</h1>
        <p class="text-secondary mb-3">Zarejestruj się.</p>
        <!-- Pierwsza strona formularza -->
        <div id="fPage">
            <div class="form-floating">
                <input type="email" name="email" id="email" pattern="^[^\s@]+@[^\s@]+\.[^\s@]{2,}$" class="form-control" placeholder="email@example.com">
                <label for="email">Adres e-mail:</label>
                <p id="warning"></p> <!-- Powiadomienie o błędach przy wprowadzaniu maila -->
            </div>
            <div class="form-floating position-relative">
                <input type="password" name="pass" id="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,16}" class="form-control" onfocus="showInfo()" onblur="hideInfo()" onkeyup="checkIfValid()" placeholder="Hasło">
                <label for="password">Hasło:</label>
            </div>
            <!-- Dynamiczna informacja o wymaganiach hasła -->
            <div id="info">
                <h3>Hasło musi zawierać:</h3>
                <p id="length" class="invalid">8 do 32 znaków</p>
                <p id="number" class="invalid">Cyfrę</p>
                <p id="upperCase" class="invalid">Wielką literę</p>
                <p id="lowerCase" class="invalid">Małą literę</p>
            </div>
            <div class="form-floating">
                <input type="password" name="pass2" id="password2" class="form-control" placeholder="Powtórz hasło">
                <label for="password2">Powtórz hasło:</label>
                <p id="warning2"></p> <!-- Błędy związane z hasłami -->
            </div>
        </div>
        <!-- Druga strona formularza -->
        <div id="sPage">
            <div class="form-floating">
                <input type="text" name="fName" id="fName" onkeyup="submitCheck()" class="form-control" placeholder="Imię">
                <label for="fName">Imię:</label>
            </div>
            <div class="form-floating">
                <input type="text" name="lName" id="lName" onkeyup="submitCheck()" class="form-control" placeholder="Nazwisko">
                <label for="lName">Nazwisko:</label>
            </div>
            <div>
                <label for="TOS">Akceptuję <a href="tos.html">warunki korzystania ze strony</a></label>
                <input type="checkbox" name="TOS" id="TOS" onchange="submitCheck()" target="_blank">
            </div>
        </div>
        <button type="button" id="button" onclick="nextCheck()" class="btn btn-primary w-100 py-2">Dalej</button> <!-- Przycisk do przejścia na drugą stronę -->
        <button type="button" id="button2" onclick="back()" class="btn btn-primary w-100 py-2">Wróć</button> <!-- Przycisk powrotu do pierwszej strony -->
        <button id="sButton" type="submit" disabled="true" class="btn btn-primary w-100 py-2">Utwórz konto</button>
        <footer>Masz już konto? <a href="Flogin.php">Zaloguj się</a></footer>
    </form>
</main>
