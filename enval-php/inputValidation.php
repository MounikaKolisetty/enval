<?php
// Function to sanitize input
function sanitize_input($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Function to validate email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate mobile number (Assuming 10-digit format)
function validate_mobile($mobile) {
    return preg_match("/^[0-9]{10}$/", $mobile);
}

// Function to validate required fields
function validate_required_fields($fields) {
    foreach ($fields as $field) {
        if (empty($field)) {
            return false;
        }
    }
    return true;
}

function validate_name($name) {
    // Name should only contain letters and spaces, and be between 2 to 50 characters long
    return preg_match("/^[a-zA-Z\s]{2,50}$/", $name);
}

function validate_designation($designation) {
    // Designation should only contain letters, spaces, and hyphens, and be between 2 to 50 characters long
    return preg_match("/^[a-zA-Z\s\-]{2,50}$/", $designation);
}

function validate_organization($organization) {
    // Organization name can contain letters, numbers, spaces, and common special characters
    return preg_match("/^[a-zA-Z0-9\s\-,.&()]{2,100}$/", $organization);
}

function validate_location($location) {
    // Location can contain letters, spaces, hyphens, commas, and periods (e.g., New York, NY)
    return preg_match("/^[a-zA-Z\s\-,.]{2,100}$/", $location);
}

function validate_message($message) {
    // Message should be 5 to 1000 characters long and only contain valid characters
    return preg_match("/^[a-zA-Z0-9\s.,!?@#$%^&*()_\-+=:;'\"\/\[\]{}]{5,1000}$/", $message);
}

function validate_amount($amount) {
    $amount = filter_var($amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    
    if ($amount === false || !is_numeric($amount) || $amount <= 0) {
        return false;
    }
    
    return $amount; // Return sanitized and validated amount
}

function validate_boolean($value) {
    return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
}

function validate_no_of_trainees($value) {
    return filter_var($value, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 1000]]) !== false;
}

// Validate Title (e.g., Mr., Ms., Dr.)
function validate_title($title) {
    $valid_titles = ['Mr', 'Ms', 'Mrs', 'Dr', 'Prof']; // Allowed titles
    return in_array($title, $valid_titles);
}

// Validate Business Area (Allows alphabets & spaces)
function validate_business_area($area) {
    return preg_match("/^[a-zA-Z\s]{2,100}$/", $area);
}

// Validate Degree Fields (Only alphabets, spaces, and periods)
function validate_degree($degree) {
    return preg_match("/^[a-zA-Z\s.]{2,100}$/", $degree);
}

// Validate Expectations/Purpose/Usage (General validation)
function validate_description($desc) {
    return preg_match("/^[a-zA-Z0-9\s.,!?@#$%^&*()_\-+=:;'\"\/\[\]{}]{5,500}$/", $desc);
}

// Validate Department (Alphanumeric & spaces, 2-100 chars)
function validate_department($department) {
    return preg_match("/^[a-zA-Z0-9\s]{2,100}$/", $department);
}

// Validate VAVME (Alphanumeric & basic special characters, 2-50 chars)
function validate_vavme($vavme) {
    return preg_match("/^[a-zA-Z0-9\s._-]{2,50}$/", $vavme);
}

// Validate Responsibilities (Allows alphanumeric, spaces, and punctuation, 5-500 chars)
function validate_responsibilities($responsibilities) {
    return preg_match("/^[a-zA-Z0-9\s.,!?@#$%^&*()_\-+=:;'\"\/\[\]{}]{5,500}$/", $responsibilities);
}

// Validate Course Title (Letters, numbers, spaces, and dashes, 2-200 chars)
function validate_course_title($courseTitle) {
    return preg_match("/^[a-zA-Z0-9\s-]{2,200}$/", $courseTitle);
}

// Validate Sponsored By (Letters, numbers, spaces, and dashes, 2-150 chars)
function validate_sponsored_by($sponsoredBy) {
    return preg_match("/^[a-zA-Z0-9\s-]{2,150}$/", $sponsoredBy);
}

// Function to verify CAPTCHA
function verify_captcha($captchaResponse, $secretKey) {
    $verifyURL = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captchaResponse";
    $response = file_get_contents($verifyURL);
    $responseData = json_decode($response);
    return $responseData->success;
}

function isValidEmail($email) {
    // Check if email is properly formatted
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    // Extract domain from email
    list($user, $domain) = explode('@', $email);

    // List of known disposable email domains (you can expand this)
    $disposableDomains = [
        'tempmail.com', 'mailinator.com', 'guerrillamail.com',
        '10minutemail.com', 'throwawaymail.com', 'fakeinbox.com'
    ];

    // Check if the email domain is in the disposable list
    if (in_array($domain, $disposableDomains)) {
        return false; // Block disposable emails
    }

    // Check if domain has valid MX records
    return checkdnsrr($domain, 'MX');
}

?>