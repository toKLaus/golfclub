<?php

require_once 'vendor/autoload.php';
use Doctrine\DBAL\DriverManager;

$connectionParams = array(
    'dbname' => 'golfbank',
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
);
$conn = DriverManager::getConnection($connectionParams);

$queryBuilder = $conn->createQueryBuilder();


echo "Ich bin da";

if (isset($_POST['submit'])) {

   // $image= addslashes(file_get_contents($_FILES['pic']['tmp_name']));

    $imageData = base64_encode(file_get_contents($_FILES['pic']['tmp_name']));
    $src = 'data: '.mime_content_type($_FILES['pic']['tmp_name']).';base64,'.$imageData;


    $stmt3 = $queryBuilder
        ->insert('course')
        ->values(
            array(
                'name' => '?',
                'parscore' => '?',
                'bild' => '?'
            )
        )
        ->setParameter(0, $_POST['name'])
        ->setParameter(1, $_POST['par'])
        ->setParameter(2, $src);

   if($stmt3){
       echo "success";
   }



    $stmt3->execute();
    header("Location: index.php");
    exit();
}