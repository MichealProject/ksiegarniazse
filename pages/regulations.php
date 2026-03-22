<?php
ob_start();
// Zmiana tytułu strony
$template->setTitle("Regulamin sklepu");
include 'front/Fregulations.html';
// Przekazujemy dane do pliku frontendowego
$content = ob_get_clean();
$display->toDisplay($content);