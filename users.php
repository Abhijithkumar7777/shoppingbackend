<?php
include 'config.php';

// Creating a new user
function createUser($email, $password, $username, $purchase_history, $shipping_address) {
    global $conn;
    $sql = "INSERT INTO User (email, password, username, purchase_history, shipping_address)
            VALUES ('$email', '$password', '$username', '$purchase_history', '$shipping_address')";
    if ($conn->query($sql) === TRUE) {
        return true; 
    } else {
        return "Error: " . $sql . "<br>" . $conn->error; 
    }
}

// Retrieving all users
function getAllUsers() {
    global $conn;
    $sql = "SELECT * FROM User";
    $result = $conn->query($sql);
    $users = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    return $users;
}

// Retrieving a single user by ID
function getUserById($user_id) {
    global $conn;
    $sql = "SELECT * FROM User WHERE id = $user_id";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    } else {
        return "User not found";
    }
}

// Updating an existing user
function updateUser($user_id, $email, $password, $username, $purchase_history, $shipping_address) {
    global $conn;
    
    //Code for avoiding single quotes in shipping_address
    $shipping_address_escaped = mysqli_real_escape_string($conn, $shipping_address);

    //SQL query 
    $sql = "UPDATE User SET email='$email', password='$password', username='$username', purchase_history='$purchase_history', shipping_address='$shipping_address_escaped' WHERE id=$user_id";

    if ($conn->query($sql) === TRUE) {
        return true; 
    } else {
        return "Error updating user: " . $conn->error; 
    }
}


// Deleting a user
function deleteUser($user_id) {
    global $conn;
    $sql = "DELETE FROM User WHERE id=$user_id";
    if ($conn->query($sql) === TRUE) {
        return true; 
    } else {
        return "Error deleting user: " . $conn->error; 
    }
}

//HTTP request 
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieving all users
    $users = getAllUsers();
    echo json_encode($users);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $email = $data['email'];
    $password = $data['password'];
    $username = $data['username'];
    $purchase_history = $data['purchase_history'];
    $shipping_address = $data['shipping_address'];

    // Adding the user to the database
    $result = createUser($email, $password, $username, $purchase_history, $shipping_address);

    // Checking whether the user was added successfully
    if ($result === true) {
        $response = array('status' => 'success', 'message' => 'User added successfully');
        echo json_encode($response);
    } else {
        $response = array('status' => 'error', 'message' => $result);
        echo json_encode($response);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    $user_id = $data['id'];
    $email = $data['email'];
    $password = $data['password'];
    $username = $data['username'];
    $purchase_history = $data['purchase_history'];
    $shipping_address = $data['shipping_address'];

    // Updating the user in the database
    $result = updateUser($user_id, $email, $password, $username, $purchase_history, $shipping_address);

    // Checking- whether the user was updated successfully
    if ($result === true) {
        $response = array('status' => 'success', 'message' => 'User updated successfully');
        echo json_encode($response);
    } else {
        $response = array('status' => 'error', 'message' => $result);
        echo json_encode($response);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = $data['id'];

    // Deleting the user from the database
    $result = deleteUser($user_id);

    // Check whether the user was deleted successfully
    if ($result === true) {
        $response = array('status' => 'success', 'message' => 'User deleted successfully..');
        echo json_encode($response);
    } else {
        $response = array('status' => 'error', 'message' => $result);
        echo json_encode($response);
    }
} else {
    // Invalid request 
    http_response_code(405); 
    echo json_encode(array('status' => 'error', 'message' => 'Method Not Allowed'));
}
?>
