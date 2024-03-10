<?php
include 'config.php';

// Adding a new product to the database
function addProduct($description, $image, $pricing, $shipping_cost) {
    global $conn;
    $sql = "INSERT INTO Product (description, image, pricing, shipping_cost)
            VALUES ('$description', '$image', '$pricing', '$shipping_cost')";
    if ($conn->query($sql) === TRUE) {
        return true; 
    } else {
        return "Error: " . $sql . "<br>" . $conn->error; 
    }
}

// Retrieving all products from the database
function getAllProducts() {
    global $conn;
    $sql = "SELECT * FROM Product";
    $result = $conn->query($sql);
    $products = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

//Code for updating an existing product in the database
function updateProduct($id, $description, $image, $pricing, $shipping_cost) {
    global $conn;
    $sql = "UPDATE Product SET description='$description', image='$image', pricing='$pricing', shipping_cost='$shipping_cost' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return true; 
    } else {
        return "Error updating product: " . $conn->error; 
    }
}

// Deleting a product from the database
function deleteProduct($id) {
    global $conn;
    $sql = "DELETE FROM Product WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return true; 
    } else {
        return "Error deleting product: " . $conn->error; 
    }
}

// Checking if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieving all products
    $products = getAllProducts();
    echo json_encode($products);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $description = $data['description'];
    $image = $data['image'];
    $pricing = $data['pricing'];
    $shipping_cost = $data['shipping_cost'];

    $result = addProduct($description, $image, $pricing, $shipping_cost);

    // Checking whether the product was added successfully
    if ($result === true) {
        $response = array('status' => 'success', 'message' => 'Product added successfully');
        echo json_encode($response);
    } else {
        $response = array('status' => 'error', 'message' => $result);
        echo json_encode($response);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id'];
    $description = $data['description'];
    $image = $data['image'];
    $pricing = $data['pricing'];
    $shipping_cost = $data['shipping_cost'];

    $result = updateProduct($id, $description, $image, $pricing, $shipping_cost);

    // Checking if product was updated successfully
    if ($result === true) {
        $response = array('status' => 'success', 'message' => 'Product updated successfully');
        echo json_encode($response);
    } else {
        $response = array('status' => 'error', 'message' => $result);
        echo json_encode($response);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id'];

    // Deleting the product from the database
    $result = deleteProduct($id);

    // Checking if product was deleted successfully
    if ($result === true) {
        $response = array('status' => 'success', 'message' => 'Product deleted successfully');
        echo json_encode($response);
    } else {
        $response = array('status' => 'error', 'message' => $result);
        echo json_encode($response);
    }
} else {
    //Showing Invalid request method(indicating the method not allowed)
    http_response_code(405); 
    echo json_encode(array('status' => 'error', 'message' => 'This method Not Allowed'));
}
?>
