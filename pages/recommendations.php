<?php
// Pobranie najlepiej sprzedających się produktów
$topProducts = [];

$sql = "
    SELECT 
    b.id_book,
    b.title,
    b.price,
    b.cover_type,
    b.id_category,
    GROUP_CONCAT(CONCAT(a.name, ' ', a.surname) SEPARATOR ', ') AS authors
FROM books b
JOIN book_authors ba ON b.id_book = ba.id_book
JOIN authors a ON ba.id_author = a.id_author
GROUP BY b.id_book, b.title, b.price, b.cover_type, b.id_category
ORDER BY b.id_book ASC
LIMIT 20;
";

$result = mysqli_query($mysql, $sql);
while ($book = mysqli_fetch_assoc($result)) {
    $book['cover_image'] = "graphic/books/{$book['id_book']}.jpg";
    $topProducts[] = $book;
}

// Dzieli produkty na 2 listy
$list1 = array_slice($topProducts, 0, 10);
$list2 = array_slice($topProducts, 10, 10);

// Przekazujemy dane do frontendu
$template->setTitle("Rekomendowane");
ob_start();
include 'front/Frecommendations.php';
$content = ob_get_clean();
$display->toDisplay($content);