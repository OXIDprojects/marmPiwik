marmalade :: Piwik
==================
Integriert den Piwik-Trackingcode in OXID eShop


Installation / Config
---------------------

*    Backup von Shop und Datenbank erstellen

*    Dateien/Ordner des Repositories im Shop in das folgende Verzeichnis kopieren
     `modules/marm/piwik/`
	 
*    erstellen Sie eine leere Datei `vendormetadata.php` im übergeordneten Verzeichnis
     `modules/marm/`
	
*    aktivieren Sie das Modul im Backend

*    PIWIK-Daten eintragen in 'Stammdaten > Grundeinstellungen > Piwik'

	 Piwik URL ohne Angabe von piwik.php. z.B "www.example.org/piwik/"

*    tmp-Ordner leeren


Wenn 'Stammdaten > Grundeinstellungen > Piwik' nicht sichtbar ist, aus Admin-Bereich ausloggen und erneut einloggen.

Change Log
----------

1. Added "Help Icons" Submitted By Marcus Pleintinger on 18.10.2012

2. Fixed price format Submitted By Andreas Pollak on 25.10.2012

3. use oxid v4.7 modules structure (all files in modules directory)

4. Exclude trackingcode from mails