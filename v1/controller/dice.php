<?php

require_once('db.php');
require_once('../model/Dice.php');
require_once('../model/Response.php');

// connect to database
try {
    $readDB = DB::connectReadDB();
}
catch (PDOException $ex) {
    erorr_log("Connection error - ".$ex, 0);
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Database conection error");
    $response->send();
    exit;
}

// handle options request method for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Max-Age: 86400');
    $response = new Response();
    $response->setHttpStatusCode(200);
    $response->setSuccess(true);
    $response->send();
}

// dice actions

if (array_key_exists("diceid", $_GET)) {

    $diceid = $_GET['diceid'];
    // validate dice ID
    if ($diceid == '' || !is_numeric($diceid)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Dice ID cannot be blank or non-numeric");
        $response->send();
        exit;
    }

    // ********************************
    // Get a single die
    // ********************************
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        try {
            $query = $readDB->prepare('SELECT id, name, color, value, image FROM tbldice WHERE id = :diceid');
            $query->bindParam(':diceid', $diceid, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("Dice not found");
                $response->send();
                exit;
            }

            // retrieve the dice
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $dice = new Dice($row['id'], $row['name'], $row['color'], $row['value'], $row['image']);
                $diceArray[] = $dice->returnDiceAsArray();
            }

            // set up return data
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['dice'] = $diceArray;

            // create new response with the return data
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;
        }
        catch (DiceException $ex) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage($ex->getMessage());
            $response->send();
            exit;
        }
        catch (PDOException $ex) {
            error_log("Database query error - ".$ex, 0);
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Failed to get dice");
            $response->send();
            exit;
        }
    }    
    else {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Request method not allowed");
        $response->send();
        exit;
    }
}
// ********************************
// Get dice by color
// ********************************
elseif (array_key_exists("color", $_GET)) {

    $color = $_GET['color'];

    if ($color !== 'White' && $color !== 'Black' && $color !== 'Red') {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Color filter must be White, Black, or Red");
        $response->send();
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        try {
            $query = $readDB->prepare('SELECT id, name, color, value, image FROM tbldice WHERE color = :color');
            $query->bindParam(':color', $color, PDO::PARAM_STR);
            $query->execute();

            $rowCount = $query->rowCount();
            $taskArray = array();

            // retrieve the dice
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
               $dice = new Dice($row['id'], $row['name'], $row['color'], $row['value'], $row['image']);
               $diceArray[] = $dice->returnDiceAsArray();
            }

            // set up return data
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['dice'] = $diceArray;

            // create new response with the return data
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;
        }
        catch (DiceException $ex) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage($ex->getMessage());
            $response->send();
            exit;
        }
        catch (PDOException $ex) {
            error_log("Database query error - ".$ex, 0);
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Failed to get dice from color filter");
            $response->send();
            exit;
        }
    }
    else {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Request method not allowed");
        $response->send();
        exit;
    }
}
// ********************************
// Get all dice
// ********************************

// this is the /dice route
elseif (empty($_GET)) {

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        try {
            $query = $readDB->prepare('SELECT id, name, color, value, image FROM tbldice');
            $query->execute();

            $rowCount = $query->rowCount();
            $diceArray = array();

            // retrieve the dice
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $dice = new Dice($row['id'], $row['name'], $row['color'], $row['value'], $row['image']);
                $diceArray[] = $dice->returnDiceAsArray();
            }
 
            // set up return data
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['dice'] = $diceArray;

            // create new response with the return data
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;
        }
        catch (DiceException $ex) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage($ex->getMessage());
            $response->send();
            exit;
        }
        catch (PDOException $ex) {
            error_log("Database query error - ".$ex, 0);
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Failed to get dice from all dice");
            $response->send();
            exit;
        }
    }    
    else {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Request method not allowed");
        $response->send();
        exit;
    }
}
// ********************************
// Endpoint wasn't found
// ********************************
else {
    $response = new Response();
    $response->setHttpStatusCode(404);
    $response->setSuccess(false);
    $response->addMessage("Endpoint not found");
    $response->send();
    exit;
}