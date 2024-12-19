<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email Notification to Seller</title>
</head>

<body>
    <p>Hello, {{ $sellerName }}!</p>
    <p>{{ $productManagerName }} has applied for registration under your account.</p>
    <p>Details:</p>
    <ul>
        <li>Name: {{ $productManagerName }}</li>
        <li>Email: {{ $productManagerEmail }}</li>
    </ul>
    <p>Thank you!</p>
</body>

</html>
