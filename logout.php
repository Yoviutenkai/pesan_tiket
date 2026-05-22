<?php
require_once __DIR__ . '/config/auth.php';

session_destroy();
redirect('index.php');
