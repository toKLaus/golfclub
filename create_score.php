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


if (isset($_GET["platz"]) && isset($_GET["player"]) && isset($_GET["hits"])) {

    $stmt = $queryBuilder
        ->insert('score')
        ->values(
            array(
                'fk_course' => '?',
                'player' => '?',
                'hits' => '?'
            )
        )
        ->setParameter(0, $_GET['platz'])
        ->setParameter(1, $_GET['player'])
        ->setParameter(2, $_GET['hits']);


    $stmt->execute();
    header("Location: index.php");
    exit();
}

