<?php
session_start();

// Set session variables
$_SESSION['username'] = 'test_user';
$_SESSION['password'] = 'test_password';

// Display session variables
echo "<h1>Test KYC Session</h1>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Username: " . $_SESSION['username'] . "</p>";
echo "<p>Password: " . $_SESSION['password'] . "</p>";

// Display all session variables
echo "<h2>All Session Variables:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Redirect to KYC route
echo "<h2>Redirect to KYC Route:</h2>";
echo "<p>Click the link below to access the KYC route:</p>";
echo "<a href='/kyc'>Access KYC Route</a>";
?> 