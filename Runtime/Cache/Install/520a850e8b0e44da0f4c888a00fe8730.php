<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>安装</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- zui -->
    <link href="/Public/zui/css/zui.css" rel="stylesheet">
    <script src="/Public/static/jquery-1.10.2.min.js"></script>
</head>

<body style="background:  rgb(230, 234, 234)">
<div class="container" style="background: white;margin-top: 50px;margin-bottom: 50px;width:800px">
    <div class="with-padding row">
        <ul class="nav nav-secondary">
            
    <li class="active"><a href="javascript:;">1.安装协议</a></li>
    <li><a href="javascript:;">2.环境检测</a></li>
    <li><a href="javascript:;">3.创建数据库</a></li>
    <li><a href="javascript:;">4.安装</a></li>
    <li><a href="javascript:;">5.完成</a></li>

        </ul>
        <div>

        </div>
        <div class="article">
            


            <div>
                
    <a class="btn btn-primary btn-block btn-large" href="<?php echo U('Install/step1');?>">同意安装协议</a>
    <a class="btn btn-default  btn-large btn-block" style="background: white;" href="http://暂时没有">不同意</a>

            </div>
        </div>
    </div>
    <style>
        body{
            font-family: Arial, "Microsoft Yahei",'新宋体';
        }
    </style>

</div>

</body>
</html>