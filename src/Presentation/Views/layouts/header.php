<?php
    // $data được truyền từ Controller
    $pageTitle = $data['pageTitle'] ?? 'Welcome';
    $hotelName = $data['hotelName'] ?? 'Hotel';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-R-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - <?php echo htmlspecialchars($hotelName); ?></title>
    
    <link rel="stylesheet" href="/css/home.css">
</head>
<body>
    <header>
        </header>
    <main>