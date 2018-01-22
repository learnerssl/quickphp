<html>
<head>
    <style>
        html {
            background: #f7f7f7;
        }

        body {
            background: #fff;
            color: #333;
            margin: 10rem auto 0 auto;
            width: 700px;
            height: 400px;
            padding: 1rem 2rem;
            -moz-border-radius: 11px;
            -webkit-border-radius: 11px;
            border-radius: 11px;
            border: 1px solid #dfdfdf;
        }

        a {
            color: #2583ad;
            text-decoration: none;
        }

        a:hover {
            color: #d54e21;
        }

        h1 {
            border-bottom: 1px solid #dadada;
            clear: both;
            color: #666;
            margin: 5px 0 5px 0;
            padding: 0;
            text-align: center;
        }

        h2 {
            text-align: center;
            font-size: 30px;
        }

        p {
            text-align: center;
            font-size: 18px;
        }

        div {
            margin-bottom: 80px;
        }

        ul {
            width: 400px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<h2>
    <img src="http://www.sucaihuo.com/Public/images/err404.gif" alt="404错误"/> Hey? 404
</h2>
<p>
	<?php echo $message; ?>
</p>
<div>
    <ul>
        <li>
            <a href="<?php echo $url; ?>"><span id="seconds_back"></span>返回首页</a>
        </li>
    </ul>
</div>
</body>
<script>
    let all = 3;
    seconds_back();
    if (all > 0) window.setInterval("seconds_back()", 1000);

    function seconds_back() {
        if (all <= 0) {
            window.location.href = '<?php echo $url;?>';
        }
        let obj = document.getElementById("seconds_back");
        if (obj) obj.innerHTML = all + " 秒后";
        all--;
    }
</script>
</html>