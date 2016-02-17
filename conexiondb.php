<?php
 function consulta($query, $coding="UTF8"){
    $cadena= "host=localhost port=5432 dbname=Feed_UNCo user=postgres password=aterrador00";
    $dbcon= pg_connect($cadena);
    pg_set_client_encoding($dbcon, $coding);
    $result = pg_query($dbcon,$query);
    if(!$result){
       //echo "Error no funciona la consulta \n".$query;
    }
    else {
        pg_close($dbcon);
          return $result;          
       }
 }
