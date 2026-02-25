<?php
// miejsce na kod backendowy, np. pobieranie danych do wyświetlenia na stronie głównej

// Przekazujemy dane do pliku frontendowego
ob_start();
include 'front/Fhome.php';
$content = ob_get_clean();
$display->toDisplay($content);
