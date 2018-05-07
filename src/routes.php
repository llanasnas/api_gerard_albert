<?php

use Slim\Http\Request;
use Slim\Http\Response;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gelatina";

$GLOBALS['conn'] = new mysqli($servername, $username, $password, $dbname);
$GLOBALS['datos'] = array();

if ($GLOBALS['conn']->connect_error) {
    die("Connection failed: " . $GLOBALS['conn']->connect_error);
}


// Routes
$app->get('/', function (Request $request, Response $response, array $args) {
    $args["code"] = "200";
    $args["msg"] = "LSNote API v0.1";
    $response = $response->withJson($args, 200);
    return $response;
});
// Routes
$app->get('/getAll', function (Request $request, Response $response, array $args) {

    $getAll = "SELECT * FROM notes";
    $result = $GLOBALS['conn']->query($getAll);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($GLOBALS['datos'], $row);
        }
        $args["code"] = "200";
        $args["resp"] = $GLOBALS['datos'];

    } else {
        $args["code"] = "204";
        $args["msg"] = "Notes not found!";
    }
    $response = $response->withJson($args, 200);

    return $response;
});


$app->get('/getPublic', function (Request $request, Response $response, array $args) {

    $getAll = "SELECT * FROM notes WHERE private <> 1";
    $result = $GLOBALS['conn']->query($getAll);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($GLOBALS['datos'], $row);
        }
        $args["code"] = "200";
        $args["resp"] = $GLOBALS['datos'];

    } else {
        $args["code"] = "204";
        $args["msg"] = "Notes not found!";
    }
    $response = $response->withJson($args, 200);

    return $response;
});

$app->get('/getOne/{id}', function (Request $request, Response $response, array $args) {

    $getAll = "SELECT * FROM notes WHERE id = " . $args['id'];
    $result = $GLOBALS['conn']->query($getAll);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($GLOBALS['datos'], $row);
        }
        $args["code"] = "200";
        $args["resp"] = $GLOBALS['datos'];
        $response = $response->withJson($args, 200);
    } else {
        $args["code"] = "204";
        $args["msg"] = "Notes not found!";
    }

    return $response;
});

$app->get('/insert/{title}/{content}/{private}/{tag1}/{tag2}/{tag3}/{tag4}/{book}/{createData}/{lastModification}/{user}', function (Request $request, Response $response, array $args) {
    $query = "INSERT INTO notes VALUES (null,?,?,?,?,?,?,?,?,NOW(),NOW(),?)";
    $insertGelatina = $GLOBALS['conn']->prepare($query);
    $insertGelatina->bind_param("sssssssss", $args['title'], $args['content'], $args['private'], $args['tag1'], $args['tag2'], $args['tag3'], $args['tag4'], $args['book'], $args['user']);


    if ($insertGelatina->execute()) {
        $args["code"] = "200";
        $args["msg"] = "Note inserted!";
        $response = $response->withJson($args, 200);
    }

    return $response;
});

$app->get('/remove/{id}', function (Request $request, Response $response, array $args) {

    $query = "DELETE FROM notes WHERE id = '" . $args['id'] . "'";

    if ($GLOBALS["conn"]->query($query)) {
        $args["code"] = "200";
        $args["msg"] = "note deleted!";
        $response = $response->withJson($args, 200);
    }
    return $response;
});

$app->get('/getAllWithTag/{tag}', function (Request $request, Response $response, array $args) {

    $query = "SELECT * FROM notes WHERE tag1 LIKE ? OR tag2 LIKE ? OR tag3 LIKE ? OR tag4 LIKE ?";
    $stmt = $GLOBALS['conn']->prepare($query);
    $aux = "%" . $args['tag'] . "%";
    $stmt->bind_param("ssss", $aux, $aux, $aux, $aux);
    $result = $stmt->execute();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($GLOBALS['datos'], $row);
        }
        $args["code"] = "200";
        $args["resp"] = $GLOBALS['datos'];

    } else {
        $args["code"] = "204";
        $args["msg"] = "Notes not found!";
    }
    $response = $response->withJson($args, 200);

    return $response;


});

$app->get('/addTagOne/{id}/{tag}', function (Request $request, Response $response, array $args) {

    $query = "SELECT tag1, tag2, tag3, tag4 FROM notes where id = " . $args["id"];
    $result = $GLOBALS['conn']->query($query);

    if ($result->num_rows > 0) {
        $tagUpdatable = "";
        while ($row = $result->fetch_assoc()) {
            if ($row["tag1"] == null) {
                $tagUpdatable = "tag1";
            } else if ($row["tag2"] == null) {
                $tagUpdatable = "tag2";
            } else if ($row["tag3"] == null) {
                $tagUpdatable = "tag3";
            } else if ($row["tag4"] == null) {
                $tagUpdatable = "tag4";
            } else {
                $args["code"] = "204";
                $args["msg"] = "Tags not disponible";
            }
        }
    } else {
        $args["code"] = "204";
        $args["msg"] = "ID not found!";
    }

    $tagUp = "UPDATE notes SET " . $tagUpdatable . " = '" . $args["tag"] . "' WHERE id = " . $args["id"];
    if ($GLOBALS['conn']->query($tagUp) === TRUE) {

        $args["code"] = "204";
        $args["msg"] = "Tag añadido en ". $tagUpdatable;

    }

    $response = $response->withJson($args, 200);

    return $response;


});


?>



