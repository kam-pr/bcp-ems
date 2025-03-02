<?php


function getTable()
{
    return isset($_GET['table']);
}

function getData()
{
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    return $data !== null;
}

function getInsertQuery() // : string
{
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    $column_names = '';
    $values = '';

    foreach ($data as $key => $value) {
        //echo "Key: " . $key . " - Value: " . $value . "\n";
        $column_names = $column_names . $key . ',';
        $values .= "'" . $value . "',";
    }

    $column_names = rtrim($column_names, ',');
    $values = rtrim($values, ',');
    $values = '(' . $values . ');';
    $column_names = '(' . $column_names . ')';
    $query_built = $column_names . ' VALUES ' . $values;
    return 'INSERT INTO ' . '`' . $_GET['table'] . '`' . ' ' . $query_built;
}

// Check if the form data is sent via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the POST data

    #$password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hashing the password
    try {

        include '../connection.php';
        // Create a PDO instance
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (getTable() && getData()) {
            $sql = getInsertQuery();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $lastInsertId = $pdo->lastInsertId();
            http_response_code(201);
            echo json_encode(['id' => $lastInsertId, 'message' => 'Record created.']);
        }

    } catch (PDOException $e) {
        $message = $e->getMessage();
        http_response_code(400);
        echo json_encode([
            "message" => 'There was a problem in the request.',
            "error" => $message
        ]);
        //echo "Error: " . $e->getMessage();
    }

    // Close the connection
    $pdo = null;
}
?>