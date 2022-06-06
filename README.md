# Composer License Manager

[![Packagist](https://img.shields.io/packagist/v/arokettu/composer-license-manager.svg?style=flat-square)](https://packagist.org/packages/arokettu/composer-license-manager)
[![Packagist](https://img.shields.io/packagist/l/arokettu/composer-license-manager.svg?style=flat-square)](https://opensource.org/licenses/MIT)
[![Gitlab pipeline status](https://img.shields.io/gitlab/pipeline/sandfox/composer-license-manager/master.svg?style=flat-square)](https://gitlab.com/sandfox/composer-license-manager/-/pipelines)

License management plugin for Composer.

## Features

The plugin is configured in the ``extras`` section of the ``composer.json`` file.

```json
{
    "extras": {
        "arokettu/composer-license-manager": {
            "licenses": {
                "allowed": ["MIT", "LGPL-*"],
                "forbidden": ["GPL-3.0", "AGPL-*"],
                "allow-empty": true
            },
            "packages": {
                "allowed": ["foo/bar", "safenamespace/*"]
            },
            "enforced": true
        }
    }
}
```

### Scan for undesired licenses

Run ``composer licenses:scan`` to check installed packages for undesired licenses.

### Policy enforcement

With `"enforced": true` (default setting) the plugin will prevent installation of packages with undesired licenses during `composer install` and `composer update`. 

## Installation

```sh 
composer require 'arokettu/composer-license-manager'
```

## Documentation

Read full documentation here: <https://sandfox.dev/php/composer-license-manager.html>

Also on Read the Docs: <https://composer-license-manager.readthedocs.io/>

## Support

Please file issues on our main repo at GitLab: <https://gitlab.com/sandfox/composer-license-manager/-/issues>

Feel free to ask any questions in our room on Gitter: <https://gitter.im/arokettu/community>

## License

The library is available as open source under the terms of the [MIT License].

[MIT License]:  https://opensource.org/licenses/MIT
