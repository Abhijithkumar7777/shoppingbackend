<?php
include 'config.php';

// Adding a new comment to the database
function addComment($product_id, $user_id, $rating, $image, $text) {
    global $conn;
    // Code for escaping special characters 
    $product_id = mysqli_real_escape_string($conn, $product_id);
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $rating = mysqli_real_escape_string($conn, $rating);
    $image = mysqli_real_escape_string($conn, $image);
    $text = mysqli_real_escape_string($conn, $text);

    $sql = "INSERT INTO comments (product_id, user_id, rating, image, text) VALUES ('$product_id', '$user_id', '$rating', '$image', '$text')";

    if ($conn->query($sql) === TRUE) {
        return true; 
    } else {
        return "Error: " . $sql . "<br>" . $conn->error; 
    }
}

// Fetching comments for a product
function getCommentsByProduct($product_id) {
    global $conn;
    $product_id = mysqli_real_escape_string($conn, $product_id);
    $sql = "SELECT * FROM comments WHERE product_id = '$product_id'";
    $result = $conn->query($sql);
    $comments = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
    }
    return $comments;
}

// Updating an existing comment
function updateComment($id, $rating, $image, $text) {
    global $conn;
    $id = mysqli_real_escape_string($conn, $id);
    $rating = mysqli_real_escape_string($conn, $rating);
    $image = mysqli_real_escape_string($conn, $image);
    $text = mysqli_real_escape_string($conn, $text);

    $sql = "UPDATE comments SET rating='$rating', image='$image', text='$text' WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        return true; 
    } else {
        return "Error updating comment: " . $conn->error; 
    }
}

// Deleting a comment
function deleteComment($id) {
    global $conn;
    $id = mysqli_real_escape_string($conn, $id);

    $sql = "DELETE FROM comments WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        return true; 
    } else {
        return "Error deleting comment: " . $conn->error; 
    }
}

// Check whether the request method is GET, POST, PUT, or DELETE
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['product_id'])) {
        $product_id = $_GET['product_id'];
        $comments = getCommentsByProduct($product_id);
        echo json_encode($comments);
    } else {
        //bad request
        http_response_code(400); 
        echo json_encode(array('status' => 'error', 'message' => 'Product ID is required'));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $result = addComment($data['product_id'], $data['user_id'], $data['rating'], $data['image'], $data['text']);
    if ($result === true) {
        echo json_encode(array('status' => 'success', 'message' => 'Comment added successfully'));
    } else {
        // Internal Server Error
        http_response_code(500); 
        echo json_encode(array('status' => 'error', 'message' => $result));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Updating an existing comment
    $data = json_decode(file_get_contents("php://input"), true);
    $result = updateComment($data['id'], $data['rating'], $data['image'], $data['text']);
    if ($result === true) {
        echo json_encode(array('status' => 'success', 'message' => 'Comment updated successfully'));
    } else {
        http_response_code(500); 
        echo json_encode(array('status' => 'error', 'message' => $result));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Deleting a comment
    $data = json_decode(file_get_contents("php://input"), true);
    $result = deleteComment($data['id']);
    if ($result === true) {
        echo json_encode(array('status' => 'success', 'message' => 'Comment deleted successfully'));
    } else {
        http_response_code(500); 
        echo json_encode(array('status' => 'error', 'message' => $result));
    }
} else {
    http_response_code(405); 
    echo json_encode(array('status' => 'error', 'message' => 'Method Not Allowed'));
}
?>
