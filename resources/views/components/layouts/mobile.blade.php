<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>{{ $title ?? 'Cek Herbal Hoaks' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
    </style>
</head>

<body class="bg-white sm:bg-gray-100 flex justify-center items-center min-h-full sm:min-h-screen p-0 m-0">
    <div class="w-full min-h-screen sm:min-h-0 sm:max-w-md sm:h-[90vh] bg-white flex flex-col justify-between sm:shadow-2xl sm:rounded-3xl overflow-hidden relative">
        {{ $slot }}
    </div>
</body>

</html>