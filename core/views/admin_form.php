<!doctype html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title><?php echo $siteName; ?> admin</title>
    <link rel="stylesheet" type="text/css" href="core/css/turbocms.css"/>
    <script src="core/ckeditor/ckeditor.js"></script>
</head>
<body>

<div class="container">
    <div class="header">
        <h1><?php echo $siteName; ?> admin</h1>
    </div>
    <div class="content">
        <h2>Save content for defined regions</h2>
        <form action="" method="post">
            <?php foreach ($keys as $key => $value): ?>
                <div class="form-element">
                    <label for="<?php echo $key; ?>"><?php echo $key; ?></label>
                    <textarea class="ckeditor" name="<?php echo $key; ?>" id="<?php echo $key; ?>" cols="30" rows="10"><?php echo $value; ?></textarea>
                </div>
            <?php endforeach; ?>

            <div class="form-element">
                <input type="submit" value="Save" class="form-submit" />
            </div>
        </form>
	    <?php if (isset($loginMethod)): ?>
	        <p>
		        <?php echo $loginMethod; ?>
	        </p>
	    <?php endif; ?>
        <p>
            <a href="<?php echo $logoutUrl; ?>">Log out</a>
        </p>
    </div>
</div>

</body>
</html>










