<?php
$host = "localhost";   
$dbname = "bilheteria";    
$username = "root";        
$password = "";


try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

    $db = new PDO($dsn, $username, $password);

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Ligação bem-sucedida à base de dados!";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

?>