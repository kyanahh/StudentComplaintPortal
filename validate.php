<?php
session_start();

$userAnswer = $_POST['captcha'] ?? '';
$correctAnswer = $_SESSION['captcha_sum'] ?? null;

if ($userAnswer == $correctAnswer) {
    echo "✅ CAPTCHA passed. You are logged in!";
} else {
    echo "❌ CAPTCHA failed. Please try again.";
}
?>
