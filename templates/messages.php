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
    <body class="messages-body">
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <span class="navbar-brand">Message board</span>
                </div>
                <?php if ($guest_mode) {?>
                    <p class="navbar-text navbar-right">Please log in to leave comments
                        <a href="index.php" class="btn btn-primary"><i class="fa fa-sign-in"></i> Log in</a>
                    </p>
                <?php }else{ $full_user_name = $user['first_name'] . ' ' . $user['last_name']; ?>
                    <p class='navbar-text navbar-right'><i class='fa fa-user'></i> <?= $full_user_name?>
                        <a href='logout.php' class='btn btn-primary'><i class='fa fa-sign-out'></i> Log out</a>
                    </p>
                <?php } ?>
            </div>
        </nav>
        
        <div class="container">
            <?php if (!$guest_mode) { ?>
            <form method="post" action="messages.php" data-operation-add='true' data-toggle="validator" role="form">
                <div class="form-group">
                    <label for="new-message">Add message:</label>
                    <textarea id="new-message" class="form-control" rows="5" placeholder="message..." required data-message-type='0'></textarea>
                </div>
                <button type="submit" class="btn btn-default">Add</button>
                <button type="reset" class="btn btn-default">Clear</button>
            </form>
            <?php } ?>
            <br>
        </div>
        
        <div class="container">
            <ul id="messages" class="media-list"></ul>
        </div>
        
        <script src="public/js/jquery-3.1.1.min.js" type="text/javascript"></script>
        <script src="public/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="public/js/app.js" type="text/javascript"></script>
    </body>
</html>