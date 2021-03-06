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

$queryBuilder
    ->select("name","id")
    ->from("course");

$stmt = $conn->query($queryBuilder);


$courses = $stmt -> fetchAll();

$view = new \TYPO3Fluid\Fluid\View\TemplateView();

$paths = $view ->getTemplatePaths();
$paths -> setTemplatePathAndFilename(__DIR__ . "/index.html");
$values["courses"] = $courses;

$view -> assignMultiple($values);

$output = $view-> render();

echo $output;
