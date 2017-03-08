<?php
$vk_auth_url = $vk_api_url . '?' . urldecode(http_build_query($vk_api_parameters));
$facebook_auth_url = $facebook_api_url . '?' . urldecode(http_build_query($facebook_api_parameters));
$google_auth_url = $google_api_url . '?' . urldecode(http_build_query($google_api_parameters));
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Comments board</title>
        <link href="public/css/font-awesome.min.css" rel="stylesheet">
        <link href="public/css/bootstrap.min.css" rel="stylesheet">
        <link href="public/css/style.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <div class="mainbox col-md-4 col-md-offset-4">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="panel-title text-center">Log in</div>
                    </div>
                    <div class="panel-body text-center">
                        <div class="btn-group" role="group" aria-label="...">
                            <a id="facebook-auth" href="<?= $facebook_auth_url ?>" class="btn btn-primary"><i class="fa fa-facebook"></i> Facebook</a>
                            <a id="google-auth" href="<?= $google_auth_url ?>" class="btn btn-primary"><i class="fa fa-google-plus"></i> Google+</a>
                            <a id="vk-auth" href="<?= $vk_auth_url ?>" class="btn btn-primary"><i class="fa fa-vk"></i> vk.com</a>
                        </div>
                        <div class="text-center">
                            <p class='login-description'>Or log in as a guest without the ability to leave comments</p>
                            <a href="/messages.php" class="btn btn-default">
                                Continue as a guest
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="public/js/jquery-3.1.1.min.js" type="text/javascript"></script>
        <script src="public/js/bootstrap.min.js" type="text/javascript"></script>
    </body>
</html>