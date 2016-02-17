<?php
/*Métodos para cargar noticias rss en bd utilizando postgres y PHP*/
include 'conexiondb.php';

function cargarFeed(){
$urls_rss=consulta("SELECT * FROM fuente;");
while ($row=  pg_fetch_row($urls_rss)){
    $rss_tags= array(
        'title',
        'link',
        'description',
        'author',
        'pubDate'      
    );
    $rss_item_tag = 'item';
    $rss_url = $row[2];
    $rssfeed= rss_to_array($rss_item_tag,$rss_tags, $rss_url);    
    
    foreach($rssfeed AS $arreglo){
        $titulo=$arreglo['title'];
        $copete=$arreglo['description'];
        $link=$arreglo['link'];
        $fecha=$arreglo['pubDate'];
        $autor=$arreglo['author'];
                
        consulta("INSERT INTO noticia (titulo, copete, link, fecha,  autor, id_fuente) VALUES ( '$titulo', '$copete', '$link', '$fecha', '$autor', $row[0])"); 
    }
    
    
}
pg_free_result($urls_rss);
}
    
/*
 * Pasa los rss obtenidos a un arreglo
 */

function rss_to_array($tag, $array, $url){
    $doc= new DOMDocument();
    $doc->load($url);
    $rss_array=array();
    $items=array();
    foreach($doc->getElementsByTagName($tag) AS $node){
        foreach($array AS $key => $value){
            if($value!=='description'){
                $items[$value]= $node->getElementsByTagName($value)->item(0)->nodeValue;
            }
            else{
                $cadena=  strip_tags($node->getElementsByTagName($value)->item(0)->nodeValue,'<span><div>');
                $items[$value]=$cadena;
            } 
        }
        array_push($rss_array,$items);
    }
    return $rss_array;
}

/*
 * Muestra los 3 últimos rss obtenidos de cada página
 */
function mostrarFeed(){
    $muestra="";
    $fuentes= consulta("SELECT id_fuente, nombre, url, pagina FROM fuente;");
    while($row=pg_fetch_row($fuentes)){
        $noticias= consulta("SELECT * FROM (SELECT n.id_noticia, n.titulo, n.copete, 
         n.link, n.fecha, n.autor FROM noticia as n WHERE n.id_fuente=".$row[0]." ORDER BY  n.id_noticia DESC LIMIT  3) as m ORDER BY m.id_noticia ASC;");
        $muestra.="<h2><a href='".$row[2]."'>".$row[1]."</a></h2>";
        while($row1=pg_fetch_row($muestra)){
            $muestra.= "<h4>".$row1[1]."</h4><p>".$row1[2]."<br>Autor:".$row1[5]."</p><a href='".$row1[3]."'>Ver más</a>";
        }
        pg_free_result($noticias);
    }
    pg_free_result($fuentes);
    echo $muestra;
}