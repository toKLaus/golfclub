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
    ->select("name","parscore", "id","bild")
    ->from("course");

$stmt = $conn->query($queryBuilder);


$courses = $stmt -> fetchAll();


foreach($courses as $course){

  // $course['bild']= base64_encode($course['bild']);

   // $values['test'] = $course['bild'];
}

$view = new \TYPO3Fluid\Fluid\View\TemplateView();

$paths = $view ->getTemplatePaths();
$paths -> setTemplatePathAndFilename(__DIR__ . "/courses.html");
$values["courses"] = $courses;

$view -> assignMultiple($values);

$output = $view-> render();

echo $output;