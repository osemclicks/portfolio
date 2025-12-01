<?php
/**
 * Contact Form Submission Handler - AJAX
 */
header('Content-Type: application/json');

require_once '../config/config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

// Get form data
$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$message = sanitize($_POST['message'] ?? '');
$csrf_token = $_POST['csrf_token'] ?? '';

// Validate CSRF token
if (!verifyCSRFToken($csrf_token)) {
    $response['message'] = 'Invalid request. Please refresh the page and try again.';
    echo json_encode($response);
    exit;
}

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required.';
}

if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!validateEmail($email)) {
    $errors[] = 'Invalid email address.';
}

if (!empty($phone) && !validatePhone($phone)) {
    $errors[] = 'Invalid phone number.';
}

if (empty($message)) {
    $errors[] = 'Message is required.';
}

if (!empty($errors)) {
    $response['message'] = implode(' ', $errors);
    echo json_encode($response);
    exit;
}

// Insert into database
$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare("
        INSERT INTO contact_submissions (name, email, phone, message) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$name, $email, $phone, $message]);
    
    $response['success'] = true;
    $response['message'] = 'Thank you for your message! We will get back to you soon.';
} catch (PDOException $e) {
    $response['message'] = 'An error occurred. Please try again later or contact us directly via email.';
}

echo json_encode($response);
