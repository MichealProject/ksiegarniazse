<?php
// Ilość produktów na jednej stronie
$limit = 18;

// Pobieranie page z URL
$pageNumber = filter_input(INPUT_GET, 'pageNumber', FILTER_VALIDATE_INT);
$pageNumber = $pageNumber && $pageNumber > 0 ? $pageNumber : 1;

// Obliczenie offsetu dla SQL, od którego rekordu zacząć pobieranie
$offset = ($pageNumber - 1) * $limit;

// Liczenie książek w bazie
$countQuery = "SELECT COUNT(id_book) as total FROM books";
$countResult = mysqli_query($mysql, $countQuery);
$totalRow = mysqli_fetch_assoc($countResult);
$totalBooks = (int)$totalRow['total'];

// Obliczanie ilości podstron z książkami
$totalPages = (int)ceil($totalBooks / $limit);

// Przygotowanie i wykonanie zapytania pobierającego dane książek
$stmt = mysqli_prepare($mysql,"SELECT books.id_book as id_book, title, categories.name as category, authors.name as name, authors.surname as surname, price FROM books 
JOIN categories ON categories.id_category = books.id_category 
JOIN book_authors ON books.id_book = book_authors.id_book
JOIN authors ON authors.id_author = book_authors.id_author
LIMIT ? OFFSET ?");
mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$books = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Przekazujemy dane do pliku frontendowego
ob_start();
include 'front/Fhome.php';
$content = ob_get_clean();
$display->toDisplay($content);
