<?php
require_once 'vendor/autoload.php';
use Doctrine\DBAL\DriverManager;

if(isset($_GET['id'])) {

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
        ->select("name", "parscore")
        ->from("course")
        ->where("id = ". $_GET['id']);

    $stmt = $conn->query($queryBuilder);


    $courses = $stmt->fetch();

    $queryBuilder2 = $conn->createQueryBuilder();

    $queryBuilder2
        ->select("player", "hits")
        ->from("score")
        ->where("fk_course = ". $_GET['id']);

    $stmt2 = $conn->query($queryBuilder2);


    $scores = $stmt2->fetchAll();

    $view = new \TYPO3Fluid\Fluid\View\TemplateView();

    $paths = $view->getTemplatePaths();
    $paths->setTemplatePathAndFilename(__DIR__ . "/courses_overview.html");
    $values["course_name"] = $courses["name"];
    $values["parscore"] = $courses["parscore"];
    $values["scores"] = $scores;

    $view->assignMultiple($values);

    $output = $view->render();

    echo $output;
} echo "NO ID, ERROR!";
