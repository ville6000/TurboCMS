# TurboCMS

TurboCMS is minimal file based CMS made with PHP.

## Installation

Upload files to your server directory via FTP or some other deployment.

## Configuration

* Copy <code>config.php.default</code> and save the new file as <code>config.php</code> in the same directory. 
* Set your passphrase 

## Usage

TurboCMS uses Mustache.js style tags in the layout. Navigate to /admin in your installation directory, in example <code>http://example.com/admin</code> and update your content.

Example layout.html
<code>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TurboCMS Example</title>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TurboCMS Example</h1>
        </div>
        <div class="content">
            {{main_content}}
        </div>
        <div class="footer">
            {{footer}}
        </div>
    </div>
</body>
</html>
</code>

## Server requirements
* Apache server
* PHP 5.3.0 or newer
