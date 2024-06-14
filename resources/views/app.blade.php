<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite('resources/sass/app.scss')

    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <title>Roast</title>

    <script type='text/javascript'>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>

<div id="app">
{{--    <router-link to="/home">首页</router-link>--}}
{{--    <router-link to="/about">关于我们</router-link>--}}
    <router-view></router-view>
</div>

<!-- 引入高德地图-->
<script src="https://webapi.amap.com/maps?v=1.4.15&key=af5985d1449b1e1978a2487e732c0114"></script>


@vite('resources/js/app.js')

</body>
</html>
