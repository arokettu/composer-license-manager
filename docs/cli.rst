Command Line Interface
######################

The plugin adds a scan command to the composer.

``licenses:scan``
=================

.. code-block:: bash

    composer licenses:scan

Lists installed packages that do not conform to the license policy.

.. code-block:: none

    $ composer licenses:scan
    npm-asset/jquery-form has forbidden license: (LGPL-2.1+ OR MIT)

or

.. code-block:: none

    $ composer licenses:scan
    All licenses conform to your policy
