<?php
include 'config.php';

//Adding an item to the user's cart
function addItemToCart($user_id, $product_id, $quantity) {
    global $conn;
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $product_id = mysqli_real_escape_string($conn, $product_id);
    $quantity = mysqli_real_escape_string($conn, $quantity);

    $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$quantity')";

    if ($conn->query($sql) === TRUE) {
        return true; 
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

//Retrieving cart items for a user
function getCartItems($user_id) {
    global $conn;
    $user_id = mysqli_real_escape_string($conn, $user_id);

    $sql = "SELECT * FROM cart WHERE user_id = '$user_id'";
    $result = $conn->query($sql);

    $cartItems = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cartItems[] = $row;
        }
    }
    return $cartItems;
}

// Updating quantity of an item in the cart
function updateCartItemQuantity($user_id, $product_id, $quantity) {
    global $conn;
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $product_id = mysqli_real_escape_string($conn, $product_id);
    $quantity = mysqli_real_escape_string($conn, $quantity);

    $sql = "UPDATE cart SET quantity = '$quantity' WHERE user_id = '$user_id' AND product_id = '$product_id'";

    if ($conn->query($sql) === TRUE) {
        return true; 
    } else {
        return "Error updating record: " . $conn->error; 
    }
}

// Removing an item from the cart
function removeCartItem($user_id, $product_id) {
    global $conn;
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $product_id = mysqli_real_escape_string($conn, $product_id);

    $sql = "DELETE FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'";

    if ($conn->query($sql) === TRUE) {
        return true; 
    } else {
        return "Error deleting record: " . $conn->error; 
    }
}

// Checkig the request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adding item to cart
    $data = json_decode(file_get_contents("php://input"), true);
    $result = addItemToCart($data['user_id'], $data['product_id'], $data['quantity']);
    if ($result === true) {
        echo json_encode(array('status' => 'success', 'message' => 'Item added to cart successfully..'));
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(array('status' => 'error', 'message' => $result));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieving cart items for a user
    $user_id = $_GET['user_id'];
    $cartItems = getCartItems($user_id);
    echo json_encode($cartItems);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Updating the quantity of an item in the cart
    $data = json_decode(file_get_contents("php://input"), true);
    $result = updateCartItemQuantity($data['user_id'], $data['product_id'], $data['quantity']);
    if ($result === true) {
        echo json_encode(array('status' => 'success', 'message' => 'Quantity updated successfully..'));
    } else {
        http_response_code(500); 
        echo json_encode(array('status' => 'error', 'message' => $result));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Removing an item from the cart
    $data = json_decode(file_get_contents("php://input"), true);
    $result = removeCartItem($data['user_id'], $data['product_id']);
    if ($result === true) {
        echo json_encode(array('status' => 'success', 'message' => 'Item removed from cart successfully..'));
    } else {
        http_response_code(500); 
        echo json_encode(array('status' => 'error', 'message' => $result));
    }
} else {
    // Invalid request method
    http_response_code(405); 
    echo json_encode(array('status' => 'error', 'message' => 'This Method Not Allowed'));
}
?>
