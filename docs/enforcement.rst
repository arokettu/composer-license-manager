.. _licensepolicyenforcement:

Policy Enforcement
##################

Theory
======

The plugin removes non-conforming packages from the package pool before the solver stage.
That, to my knowledge, is the only stage in Composer 2.x where writing ``composer.lock`` with an invalid set of packages can be prevented.

Effects
=======

Update: no enforcement
----------------------

In non enforcement mode, that you may enable, the non conforming packages in the pool will be printed:

.. note::
    The excluded package list may be long because of new and old dependencies and whatever junk solver may process to create an installable set of packages.

.. code-block::

    $ composer require npm-asset/jquery-form
    Using version ^4.3 for npm-asset/jquery-form
    ./composer.json has been updated
    Running composer update npm-asset/jquery-form
    Loading composer repositories with package information
    Some packages do not conform to the license policy:
    1. npm-asset/jquery-form: (LGPL-2.1+ OR MIT)
    2. npm-asset/jquery: (no license set)
    3. npm-asset/shelljs: BSD-3-Clause
                    <...>
    119. npm-asset/rc: (no license set)
    120. npm-asset/ieee754: BSD-3-Clause
    121. npm-asset/ini: (no license set)
    Updating dependencies
    Lock file operations: 2 installs, 0 updates, 0 removals
      - Locking npm-asset/jquery (3.6.0)
      - Locking npm-asset/jquery-form (4.3.0)
    Writing lock file
    Installing dependencies from lock file (including require-dev)
    Package operations: 2 installs, 0 updates, 0 removals
      - Installing npm-asset/jquery (3.6.0): Extracting archive
      - Installing npm-asset/jquery-form (4.3.0): Extracting archive
    Generating autoload files
    8 packages you are using are looking for funding.
    Use the `composer fund` command to find out more!

Update: enforcement
-------------------

In the default enforcement mode the packages are removed from the solver pool so you will likely get a solver exception:

.. note::
    You may also have an outdated package installed in case of re-licensing or dependency graph change.
    Running ``composer outdated`` after ``composer update`` is strongly recommended if the excluded list is of nonzero length.

.. code-block:: none

    $ composer require npm-asset/jquery-form
    Using version ^4.3 for npm-asset/jquery-form
    ./composer.json has been updated
    Running composer update npm-asset/jquery-form
    Loading composer repositories with package information
    Some packages do not conform to the license policy:
    1. npm-asset/jquery-form: (LGPL-2.1+ OR MIT)
    2. npm-asset/jquery: (no license set)
    3. npm-asset/shelljs: BSD-3-Clause
                    <...>
    119. npm-asset/rc: (no license set)
    120. npm-asset/ieee754: BSD-3-Clause
    121. npm-asset/ini: (no license set)
    Updating dependencies
    Your requirements could not be resolved to an installable set of packages.

      Problem 1
        - Root composer.json requires npm-asset/jquery-form ^4.3, found npm-asset/jquery-form[4.3.0] but these were not loaded, likely because it conflicts with another require.

    You can also try re-running composer require with an explicit version constraint, e.g. "composer require npm-asset/jquery-form:*" to figure out if any version is installable, or "composer require npm-asset/jquery-form:^2.1" if you know which you need.

    Installation failed, reverting ./composer.json and ./composer.lock to their original content.

Install: enforcement
--------------------

This also covers ``composer install`` in case you have an outdated ``vendor`` dir where the License Manager is present or if the License Manager is installed globally:

.. code-block:: none

    $ composer install

    Installing dependencies from lock file (including require-dev)
    Verifying lock file contents can be installed on current platform.
    Warning: The lock file is not up to date with the latest changes in composer.json. You may be getting outdated dependencies. It is recommended that you run `composer update` or `composer update <package name>`.
    Some packages do not conform to the license policy:
    1. npm-asset/jquery-form: (LGPL-2.1+ OR MIT)


      [LogicException]
      Fixed package npm-asset/jquery-form 4.3.0 was not added to solver pool.
