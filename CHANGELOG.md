### Changelog
Module: Module Information
This modul allows to keep information about a module, code-snippet or template/backend-theme.

Please note: This change log may not be accurate

#### 0.7.7
- Bugfixes for multible sections
- Bugfixes inside backend-css for problems within the backend-theme (manage-sections)
- Remove text from frontend-template view.lte
- Bugfixes inside backend for use in more than one section on one page.
- Bugfixes for the file-uploader in backend.
- Bugfix for sorting images in backend via drag&drop.

#### 0.7.6
- Bugfix for install/upgrade from older versions!
- Add readme and prepare for github
- Add missing fields in frontend (e.g. Forum and Web-Link)
- Add link-template for external links (like e.g. forum or web-link) in language-files.

#### 0.7.5
- Some bugfixes for the rating.
- Add missing keys in language-files.
- Add rating-info in backend-interface.
- Minor typos in backend.
- Secure hash for rating

#### 0.7.4
- Rating-Code
- Change table-field type of rating to varchar

#### 0.7.3
- Secure hash for download-counter.

#### 0.7.2
- LEPTON-CMS 2.0 (path problems within L* 1.3.2)
- Bugfix for subdirectories inside the mediafolder in fresh installations.
- Bugfix for table-names
- Bugfix for folder-name
- Bugfix for uninstall
- Fixed some typos

#### 0.7.1
- Add download counter and ajax.
- Add missing language-keys and translations.

#### 0.7.0
- Set version to 0.7.0 to avoid number-conflicts. (See 0.5.0 for details.)

#### 0.6.1
- Private study - canceled

#### 0.6.0
- Private study - canceled

#### 0.5.0
- LEPTON-CMS 1.3.1 (LEPTON-CMS 2.0 recomented!)
- Complete recode the frontend-output/view.
- TWIG-Templates
- Remove jQuery-Admin-Lib plugin support - use header.inc.php instead.
- Add new table for images.
- Multi images support for ordering by drag and drop, set up title, alt and active/non-active.

#### 0.4.7	2012-03-13
- Bugfix inside view.php - remove deprecated function.

#### 0.4.6	2009-06-03
- Bugfix inside "save.php" to avoid conflicts within "<?php" and "?> tags

#### 0.4.5	2009-03-21
- Bugfix in modify.php

#### 0.4.3	2009-01-18
- Fixed: Link-Bug in german-language file.

#### 0.4.2	2009-01-08
- Fixed: typo in the path to the lightbox.js causes problems.

#### 0.4.1	2009-01-05
- Fixed: bug/typo in the path to module "require".
- Fixed: requirements for the PHP version down to 5.1.0

#### 0.4.0	2008-12-30
- Add "require" test to the info file to make some requiremend-test during the installation-process.
 
#### 0.3.0	2008-12-09
- Lightbox2 und xFastTemplate implant.
 
#### 0.2.8	2008-11-15
- Minor changes in the frontend.css
- Add platform and group
- Add require

#### 0.2.7	2008-11-05
- Add new db-field: guid for storing the moudul-guid

#### 0.2.6	2008-10-31
- Add Image/Screen preview to modify.php
- Bugfixes in fileuploads

#### 0.2.5	2008-10-24
- Add missing type "WYSIWYG".
- Remove the short-open-tags

#### 0.2.4	2008-10-22
- Typos in the type-select inside modify, addslashes in save

#### 0.2.3	2008-10-20
- Setting the uploaded File permissions to 0755 - bugfix

#### 0.2.2	2008-10-17
- Minor cosmetic changes in the control-mail.
- Add screen-upload; if the screen file isn't found, the db-table will be updated to ''
- Some modifications inside backend.css and frontend.css

#### 0.2.1	2008-10-15
- Some bugfixes e.g. unlink-issures in save.php

#### 0.2.0	2008-10-10
- AMASP Upload in modify.php and fileupload in save.php.
- E-Mail notification at fileupload.
- New select for "state".

#### 0.1.9	2008-10-08
- Bugfixes inside view.php and function "clean_up_str".
- Change State to "beta".

#### 0.1.8	2008-09-17
- Bugfix for the redeclaration of function "replace_all" in view.php

#### 0.1.7	2008-09-08
- Add some string-cleanings.
- Massiv modify "view.php" for flex. output of "see_also":

#### 0.1.6	2008-08-25
- Search-Support by Ruud

#### 0.1.5	2008-08-25
- Major codechanges inside "view.php" for better output.
- Add language-support (EN, DE, NL) for frontend.
- Thanks to RuudE for the quick NL translation.

#### 0.1.3	2008-08-23
- Major codechanges and db-changes.
- Added language-support (EN) for the backend.
- Added "see also" links in the "view.php".

#### 0.1.0	2008-08-22
- First alpha run
