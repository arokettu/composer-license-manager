Configuration
#############

The plugin is configured in the ``extras`` section of the ``composer.json`` file.

Example
=======

.. code-block:: json

    {
        "extras": {
            "arokettu/composer-license-manager": {
                "licenses": {
                    "allowed": ["MIT", "LGPL-*"],
                    "forbidden": ["GPL-3.0", "AGPL-*"],
                    "allow-empty": true
                }
                "packages": {
                    "allowed": ["foo/bar", "safenamespace/*"]
                },
                "enforced": true
            }
        }
    }

Licenses
========

Licenses section configures desired and undesired licenses.

``"allowed"``
    Whitelisted licenses. Allows globs in prefix form (``*`` as the last character).
    Default: ``["*"]``
``"forbidden"``
    Blacklisted licenses. Allows globs in prefix form (``*`` as the last character).
    Default: ``[]``
``"allow-empty"``
    Allow packages with no license set.
    Default: ``false``

.. note::
    Whitelisting licenses by glob may be unwise.
    For example ``BSD-*`` `will allow <https://spdx.org/licenses/>`__ such licenses as
    ``BSD-Protection`` (non GPL-compatible),
    ``BSD-3-Clause-No-Nuclear-License`` and ``BSD-3-Clause-No-Military-License`` (both non-free)


Check order:

#. exact forbidden licenses
#. exact allowed licenses
#. licenses forbidden by glob
#. licenses allowed by glob

License identifiers are checked in case insensitive manner.
SPDX License expressions like ``(MIT OR LGPL)`` are not evaluated and must be specified exactly.
They are also ignored by globs except for match-all glob ``"*"``.

Multiple specified licenses are treated like a disjunction so if any of the licenses conform to the policy, the package is considered conforming to the policy.

Packages
========

Package exceptions to the policy enforcement.

``"allowed"``
    Whitelisted packages. Allows globs in prefix form (``*`` as the last character).
    Default: ``[]``

Enforcement
===========

``"enforced"``
    If true, the license policy is enforced during package installation and update,
    refer to :ref:`licensepolicyenforcement` for further info.
    Default: ``true``
