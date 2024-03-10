<?php
include 'config.php';

// Creating a new order
function createOrder($user_id, $total_amount, $status) {
    global $conn;
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $total_amount = mysqli_real_escape_string($conn, $total_amount);
    $status = mysqli_real_escape_string($conn, $status);

    $sql = "INSERT INTO `order` (user_id, total_amount, status) VALUES ('$user_id', '$total_amount', '$status')";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Retrieving orders by user ID
function getOrdersByUserId($user_id) {
    global $conn;
    $user_id = mysqli_real_escape_string($conn, $user_id);

    $sql = "SELECT * FROM `order` WHERE user_id = '$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $orders = array();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return $orders;
    } else {
        return array(); 
    }
}

// Updating the order status by order ID
function updateOrderStatus($order_id, $status) {
    global $conn;
    $order_id = mysqli_real_escape_string($conn, $order_id);
    $status = mysqli_real_escape_string($conn, $status);

    $sql = "UPDATE `order` SET status = '$status' WHERE id = '$order_id'";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Deleting order by order ID
function deleteOrder($order_id) {
    global $conn;
    $order_id = mysqli_real_escape_string($conn, $order_id);

    $sql = "DELETE FROM `order` WHERE id = '$order_id'";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Checking request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Creating a new order
    $data = json_decode(file_get_contents("php://input"), true);
    $result = createOrder($data['user_id'], $data['total_amount'], $data['status']);
    if ($result === true) {
        $response = array('status' => 'success', 'message' => 'Order created successfully');
        echo json_encode($response);
    } else {
        http_response_code(500);
        echo json_encode(array('status' => 'error', 'message' => $result));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieving the orders by user ID
    if (isset($_GET['user_id'])) {
        $orders = getOrdersByUserId($_GET['user_id']);
        echo json_encode($orders);
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'message' => 'User ID is required'));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Updating order status by order ID
    parse_str(file_get_contents("php://input"), $putParams);
    $result = updateOrderStatus($putParams['order_id'], $putParams['status']);
    if ($result === true) {
        $response = array('status' => 'success', 'message' => 'Order status successfully updated');
        echo json_encode($response);
    } else {
        http_response_code(500);
        echo json_encode(array('status' => 'error', 'message' => $result));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Deleting order by order ID
    parse_str(file_get_contents("php://input"), $deleteParams);
    $result = deleteOrder($deleteParams['order_id']);
    if ($result === true) {
        $response = array('status' => 'success', 'message' => 'Order successfully deleted');
        echo json_encode($response);
    } else {
        http_response_code(500);
        echo json_encode(array('status' => 'error', 'message' => $result));
    }
} else {
    // Invalid request method
    http_response_code(405);
    echo json_encode(array('status' => 'error', 'message' => 'Method Not Allowed'));
}
?>
