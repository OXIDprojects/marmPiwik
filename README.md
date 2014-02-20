marmalade :: Piwik
==================
Integrates the Pwiki tracking code into OXID eShop

Installation / Config
---------------------


marmalade :: Piwik
==================
Integriert den Piwik-Trackingcode in OXID eShop


Installation / Config
---------------------

*    backup shop and database

*    copy the files from the repository to your shop in the directory
     `modules/marm/piwik/`

*    Create the empty file `vendormetadata.php` in the directory above
     `modules/marm/`

*    Enable module under Extension > Module > marmalade.de Piwik

*    Set up your PIWIK variables in Master Settings > Core Settings > Piwik setup

	 Piwik URL should be without piwik.php ending. like "www.example.org/piwik/"

*    Clean your language cache (cleanup eShop tmp directory)


If you don't see Master Settings > Core Settings > Piwik setup tab, logout and login again to admin section.


Change Log
----------

1. Added "Help Icons" Submitted By Marcus Pleintinger on 18.10.2012

2. Fixed price format Submitted By Andreas Pollak on 25.10.2012

3. use oxid v4.7 modules structure (all files in modules directory)

4. Exclude trackingcode from mails

