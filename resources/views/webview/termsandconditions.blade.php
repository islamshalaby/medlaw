<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Med&Law - Terms and Conditions APP</title>
    <link rel="stylesheet" type="text/css" href="https://www.fontstatic.com/f=zahra-bold" />
    <link rel="stylesheet" href="/css/webview.css">
    <?php if($lang == 'ar'){ ?>
        <style>
            body{
                direction : rtl;
            }
            .text-container .text{
                font-size : 20px;
            }    
        </style>
    <?php } ?>   
</head>
<body>
        <div class="container">
            <div class="image-container">
                <img src="/images/logo.png" alt="Med&Law Logo">
            </div>

            <div class="text-container">
                <h1 class="title" >{{$title}}</h1>
                <p class="text" ><?=$text?></p>
            </div>
        </div>
</body>
</html>