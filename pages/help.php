<?php
ob_start();

// Ładowanie zawartości strony w zależności od parametru task i zmiana tytułu
$task = $_GET['task'] ?? 'faq';

switch ($task) {

    case 'faq':
        $template->setTitle("FAQ i pomoc");
        include 'front/Ffaq.html';
        break;

    case 'contact':
        $template->setTitle("Kontakt");
        include 'front/Fcontact.html';
        break;

    default:
        http_response_code(404);
        include '404.php'; 
        break;
}

// Przekazujemy dane do pliku frontendowego
$content = ob_get_clean();
$display->toDisplay($content);