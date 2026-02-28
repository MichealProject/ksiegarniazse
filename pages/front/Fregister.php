<?php
require __DIR__ . '/../register.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja</title>
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
</head>
<body>
<main>
    <form action="fregister.php" method="post">
        <h1>Utwórz konto</h1>
        <!-- Pierwsza strona formularza -->
        <div id="fPage">
            <div>
                <label for="email">Adres e-mail:</label>
                <input type="email" name="email" id="email" pattern="^[^\s@]+@[^\s@]+\.[^\s@]{2,}$">
                <p id="warning"></p> <!-- Powiadomienie o błędach przy wprowadzaniu maila -->
            </div>
            <div>
                <label for="password">Hasło:</label>
                <input type="password" name="pass" id="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,16}">
            </div>
            <!-- Dynamiczna informacja o wymaganiach hasła -->
            <div id="info">
                <h3>Hasło musi zawierać:</h3>
                <p id="length" class="invalid">8 do 32 znaków</p>
                <p id="number" class="invalid">Cyfrę</p>
                <p id="upperCase" class="invalid">Wielką literę</p>
                <p id="lowerCase" class="invalid">Małą literę</p>
            </div>
            <div>
                <label for="password2">Powtórz hasło:</label>
                <input type="password" name="pass2" id="password2">
                <p id="warning2"></p> <!-- Błędy związane z hasłami -->
            </div>
        </div>
        <!-- Druga strona formularza -->
        <div id="sPage">
            <div>
                <label for="fName">Imię:</label>
                <input type="text" name="fName" id="fName" onkeyup="submitCheck()">
            </div>
            <div>
                <label for="lName">Nazwisko:</label>
                <input type="text" name="lName" id="lName" onkeyup="submitCheck()">
            </div>
            <div>
                <label for="TOS">Akceptuję <a href="tos.html">warunki korzystania ze strony</a></label>
                <input type="checkbox" name="TOS" id="TOS" onchange="submitCheck()">
            </div>
        </div>
        <button type="button" id="button" onclick="nextCheck()">Dalej</button> <!-- Przycisk do przejścia na drugą stronę -->
        <button type="button" id="button2" onclick="back()">Wróć</button> <!-- Przycisk powrotu do pierwszej strony -->
        <button id="sButton" type="submit" disabled="true">Utwórz konto</button>
        <footer>Masz już konto? <a href="Flogin.php">Zaloguj się</a></footer>
    </form>
</main>
    <script src="../../components/js/validation.js">
    </script>
</body>
</html>