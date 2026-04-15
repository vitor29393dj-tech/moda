<?php
session_start();
session_destroy(); // Mata todas as sessões ativas
header("Location: acesso.php"); // Te joga de volta para a tela luxuosa
exit;
?>