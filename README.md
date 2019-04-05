## Overview

This helper was originally created for using from within a plugin on some 
old WordPress installations running in different environments.

It does it's best to fetch the content using php-curl, file_get_contents and pure sockets.
Supports HTTPS. Enjoy!

## Installation

```bash
composer require exfriend/download
```

## Usage

```php
$html = download( 'https://example.com' );
```

## Credits

Vladislav Otchenashev (vlad@serpentine.io)

[Serpentine.io](https://serpentine.io)
