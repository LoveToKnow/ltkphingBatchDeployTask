About
-----

BatchDeployTask is an extension of the DbDeployTask to allow PHP scripts to be run instead
of the SQL scripts that DbDeployTask supports.

BatchDeployTask will use the same change log database that DbDeployTask, but it will set
the delta set name to "Batch" to distinguish it from DbDeployTask delta sets.


Installation
------------

Copy the tasks/user/BatchDeployTask.php file into your Phing installation directory.


License
-------

See the LICENSE file.
