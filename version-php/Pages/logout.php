<?php
session_start();
require_once __DIR__ . '/../Service/AuthService.php';

$authService = new AuthService();

$authService->logout();
header('Location: ../index.php');
exit;
