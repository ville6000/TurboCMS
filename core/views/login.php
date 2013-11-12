<!doctype html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title><?php echo $siteName; ?> login</title>
    <link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/core/css/turbocms.css"/>
</head>
<body>

<div class="container">
    <div class="header">
        <h1><?php echo $siteName; ?> login</h1>
    </div>
    <div class="content">
        <?php if (isset($message)): ?>
            <div class="message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <h2>Login with passphrase</h2>
        <form action="<?php echo $loginFormUrl; ?>" method="post">
            <div class="form-element">
                <label for="passphrase">Passphrase:</label>
                <input type="password" name="passphrase" id="passphrase"/>
            </div>
            <div class="form-element">
                <input type="submit" value="Login" class="form-submit" />
            </div>
        </form>

        <h2>Login with email address</h2>
        <form action="<?php echo $loginFormUrl; ?>" method="post">
            <div class="form-element">
                <label for="email">Email address:</label>
                <input type="email" name="email" id="email"/>
            </div>
            
            <div class="form-element">
                <input type="submit" value="Login" class="form-submit" />
            </div>
        </form>
    </div>
</div>

</body>
</html>