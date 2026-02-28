var Input=document.getElementById("password");
var Input2=document.getElementById("password2");
var length=document.getElementById("length");
var number=document.getElementById("number");
var upperCase=document.getElementById("upperCase");
var lowerCase=document.getElementById("lowerCase");
var passwordGood=false;
// Wyświetlanie wymagań dotyczących hasła
Input.onfocus=function(){
    document.getElementById("info").style.display="block";
}
Input.onblur=function(){
    document.getElementById("info").style.display="none";
}

// Funkcja sprawdzająca poprawność wprowadzonego hasła i obsługująca komumnikat
Input.onkeyup=function(){
    const lowerCaseTest=/[a-z]/g;
    const upperCaseTest=/[A-Z]/g;
    const numbersTest=/[0-9]/g;
    var lCaseCheck=false;
    var uCaseCheck=false;
    var numberCheck=false;
    var lengthCheck=false;
    if(Input.value.match(lowerCaseTest)){
        lowerCase.classList.replace('invalid','valid');
        lCaseCheck=true;
    }else{
        lowerCase.classList.replace('valid','invalid');
        lCaseCheck=false;
    }

    if(Input.value.match(upperCaseTest)){
        upperCase.classList.replace('invalid','valid');
        uCaseCheck=true;
    }else{
        upperCase.classList.replace('valid','invalid');
        uCaseCheck=false;
    }

    if(Input.value.match(numbersTest)){
        number.classList.replace('invalid','valid');
        numberCheck=true;
    }else{
        number.classList.replace('valid','invalid');
        numberCheck=false;
    }

    if(Input.value.length>=8 && Input.value.length<=32){
        length.classList.replace('invalid','valid');
        lengthCheck=true;
    }else{
        length.classList.replace('valid','invalid');
        lengthCheck=false;
    }
    if(uCaseCheck==true && lCaseCheck==true && numberCheck==true && lengthCheck==true)
        passwordGood=true;
    else
        passwordGood=false;
}

// Przejście do drugiej strony formularza
function next(){
    document.getElementById("fPage").style.display="none";
    document.getElementById("button").style.display="none";
    document.getElementById("sPage").style.display="block";
    document.getElementById("button2").style.display="block";
    document.getElementById("sButton").style.display="block";
}

// Wysłanie zapytania do php o istnienie emaila
function emailCheck(callback){
    var formData = new FormData();
    formData.append("email",email.value.trim());
    fetch("../../components/emailCheck.php",{
        method: "POST",
        body: formData
    }).then(response => response.text()).then(data => {
        if(data=='exists'){
            callback(false);
        }else{
            callback(true);
        }
    })
}

// Sprawdzenie czy można przejść do drugiej strony formularza
function nextCheck(){
    const emailTest=/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    const emailValue=email.value.trim();
    if(!emailValue.match(emailTest)){
        document.getElementById("warning").textContent="Wpisz poprawny adres e-mail!";
        document.getElementById("warning").style.display="block";
        return;
    }else{
        email.value=emailValue;
        document.getElementById("warning").style.display="none";
    }

    if(Input.value=='' || document.getElementById("email").value==''){
        document.getElementById("warning2").textContent="Pola nie mogą być puste!";
        document.getElementById("warning2").style.display="block";
        return;
    }
    if(passwordGood==false){
        document.getElementById("warning2").textContent="Wpisz poprawne hasło!";
        document.getElementById("warning2").style.display="block";
        return;
    }
    if(Input.value!=Input2.value){
        document.getElementById("warning2").textContent="Hasła nie są takie same!";
        document.getElementById("warning2").style.display="block";
        return;
    }else{
        document.getElementById("warning2").style.display="none";
    }

    emailCheck(function(result){
        if(result == false){
            document.getElementById("warning").textContent="Takie konto już istnieje!";
            document.getElementById("warning").style.display="block";
        }else{
            next();
        }
    });
}

var fName=document.getElementById("fName");
var lName=document.getElementById("lName");

// Sprawdzenie czy pola nie są puste i zaakceptowano TOS
function submitCheck(){
    if(fName.value.trim()!='' && lName.value.trim()!='' && document.getElementById("TOS").checked==true){
        fName.value=fName.value.trim();
        lName.value=lName.value.trim();
        document.getElementById("sButton").disabled=false;
    }else{
        document.getElementById("sButton").disabled=true;
    }
}

// Powrót do pierwszej strony formularza
function back(){
    document.getElementById("sPage").style.display="none";
    document.getElementById("button2").style.display="none";
    document.getElementById("sButton").style.display="none";
    document.getElementById("fPage").style.display="block";
    document.getElementById("button").style.display="block";
}