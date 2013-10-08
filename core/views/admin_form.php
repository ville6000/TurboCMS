<!doctype html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title>TurboCMS</title>
    <link rel="stylesheet" type="text/css" href="core/css/turbocms.css"/>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>TurboCMS</h1>
    </div>
    <div class="content">
        <h2>Save content for defined regions</h2>
        <form action="" method="post">
            <?php foreach ($keys as $key => $value): ?>
                <div class="form-element">
                    <label for="<?php echo $key; ?>"><?php echo $key; ?></label>
                    <textarea name="<?php echo $key; ?>" id="<?php echo $key; ?>" cols="30" rows="10"><?php echo $value; ?></textarea>
                </div>
            <?php endforeach; ?>

            <div class="form-element">
                <input type="submit" value="Save" class="form-submit" />
            </div>
        </form>
    </div>
</div>

</body>
</html>










