Dolibarr Module Discount4Lines
========================================

This module for Dolibarr ERP/CRM allows you to apply a discount percentage onto lines of propale, order and invoices.


Install
-------

### Manually

- Make sure Dolibarr (>= 3.3.x) is already installed and configured on your workstation or development server.

- In your Dolibarr installation directory, edit the ```htdocs/conf/conf.php``` file

- Find the following lines:
    ```php
    //$dolibarr_main_url_root_alt ...
    //$dolibarr_main_document_root_alt ...
    ```

- Uncomment these lines (delete the leading ```//```) and assign a sensible value according to your Dolibarr installation

    For example :

    - UNIX:
        ```php
        $dolibarr_main_url_root = 'http://localhost/Dolibarr/htdocs';
        $dolibarr_main_document_root = '/var/www/Dolibarr/htdocs';
        $dolibarr_main_url_root_alt = '/custom';
        $dolibarr_main_document_root_alt = '/var/www/Dolibarr/htdocs/custom';
        ```

    For more information about the ```conf.php``` file take a look at the conf.php.example file.

*Note that for Dolibarr versions before 3.5, the ```$dolibarr_main_url_root_alt``` has to be an absolute path*

- Clone the repository in ```$dolibarr_main_document_root_alt/mymodule```

*(You may have to create the ```htdocs/custom``` directory first if it doesn't exist yet.)*
```sh
git clone git@git.aternatik.net:dolibarr/discount4lines.git discount4lines
```


Contributions
-------------

Feel free to contribute and report defects on our [issue tracker](https://git.aternatik.net/dolibarr/discount4lines/issues).

Licenses
--------

### Main code

![GPLv3 logo](img/gplv3.png)

GPLv3 or (at your option) any later version.

See [COPYING](COPYING) for more information.

### Other Licenses


#### [GNU Licenses logos](https://www.gnu.org/graphics/license-logos.html)

Public domain

#### Documentation

All texts and readmes.

![GFDL logo](img/gfdl.png)