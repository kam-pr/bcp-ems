<?php
include '../connection.php';

if (isset($_GET['table'])) {

    $params = $_GET;
    $keys = array_keys($params); // Get all the keys (parameter names)
    $filter = '';

    for ($i = 0; $i < count($params); $i++) {
        $key = $keys[$i];

        // Skip the 'table' key as it isn't part of the WHERE clause
        if ($key != 'table') {
            $value = $params[$key]; // Get the value associated with the key

            // Build the WHERE part of the query, adding AND if needed
            if ($filter) {
                $filter .= " AND ";
            }

            // Safely add the condition to the WHERE clause
            $filter .= "$key = '$value'";
        }
    }

    // If there are any conditions, you can prepend with WHERE
    if ($filter) {
        $filter = "WHERE " . $filter;
    }

    // Echo the filter or use it in your query
    $filter = 'SELECT * FROM ' . $_GET['table'] . ' ' . $filter;
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare($filter);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if any rows were returned
    if ($results) {
        echo json_encode($results);
    } else {
        //http_response_code(400);
        echo json_encode([]);
    }

    // Create a PDO instance


}
?>