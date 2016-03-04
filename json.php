<?php
include'rss_bd.php';
cargarFeed();
//mostrarFeed();
header('Content-Type: application/json');
echo generarJson();
        
