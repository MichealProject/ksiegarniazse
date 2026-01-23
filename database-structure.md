# 📚 System bazy danych – Księgarnia internetowa

## 1. Opis projektu

Projekt przedstawia relacyjną bazę danych dla systemu księgarni internetowej.
Baza danych obsługuje sprzedaż książek, klientów, pracowników, zamówienia,
płatności oraz dostawców.

Zastosowano klucze obce, triggery oraz procedury składowane w celu zapewnienia
spójności danych oraz automatyzacji logiki biznesowej.

---

## 2. Technologie

- System bazy danych: MySQL / MariaDB  
- Model danych: relacyjny  
- Wykorzystane mechanizmy:
  - FOREIGN KEY
  - TRIGGER
  - PROCEDURE
  - ENUM

---

## 3. Struktura bazy danych

### Tabele

- customers – klienci sklepu  
- employees – pracownicy księgarni  
- departments – działy pracowników  
- books – książki dostępne w sprzedaży  
- authors – autorzy książek  
- categories – kategorie książek  
- suppliers – dostawcy książek  
- orders – zamówienia  
- order_items – pozycje zamówień  
- payments – płatności  
- addresses – adresy klientów  
- book_authors – relacja książka–autor  

---

## 4. Relacje między tabelami

- departments → employees  
- customers → orders  
- employees → orders  
- orders → order_items  
- books → order_items  
- books → categories  
- books → suppliers  
- books ↔ authors (book_authors)  
- orders → payments  

Wszystkie relacje zostały zabezpieczone kluczami obcymi.

---

## 5. Triggery

Zaimplementowane triggery realizują następujące funkcje:

- automatyczne przeliczanie wartości zamówienia (`total_price`)
- aktualizacja stanu magazynowego książek
- blokada zakupu przy braku dostępnego towaru
- automatyczna zmiana statusu zamówienia po dokonaniu płatności

---

## 6. Procedury składowane

Baza danych zawiera procedury składowane obsługujące logikę systemu:

- create_order – tworzenie nowego zamówienia  
- add_book_to_order – dodanie książki do zamówienia  
- remove_book_from_order – usunięcie pozycji z zamówienia  
- update_order_status – zmiana statusu zamówienia  
- register_payment – rejestracja płatności  
- add_book – dodanie nowej książki  
- get_order_details – pobranie szczegółów zamówienia  

Procedury upraszczają obsługę bazy danych oraz zapewniają spójność danych.

---

## 7. Przykładowy przebieg zamówienia

1. Utworzenie zamówienia  
2. Dodanie książek do zamówienia  
3. Automatyczne przeliczenie ceny oraz aktualizacja magazynu  
4. Rejestracja płatności  
5. Zmiana statusu zamówienia na „paid”

---

## 8. Normalizacja

Baza danych spełnia zasady:
- 1NF – brak powtarzających się danych
- 2NF – pełna zależność od klucza głównego
- 3NF – brak zależności przechodnich

---

## 9. Możliwości rozbudowy

- system opinii klientów
- koszyk zakupowy
- promocje i rabaty
- raporty sprzedażowe
- role i uprawnienia użytkowników

---

## 10. Informacje końcowe

Projekt został przygotowany jako system bazy danych dla księgarni internetowej
i może być wykorzystany jako podstawa do aplikacji webowej lub desktopowej.
