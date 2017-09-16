<?php

//   -------------------------------------------------------------------------------
//  |                  net2ftp: a web based FTP client                              |
//  |              Copyright (c) 2003-2017 by David Gartner                         |
//  |                                                                               |
//  | This program is free software; you can redistribute it and/or                 |
//  | modify it under the terms of the GNU General Public License                   |
//  | as published by the Free Software Foundation; either version 2                |
//  | of the License, or (at your option) any later version.                        |
//  |                                                                               |
//   -------------------------------------------------------------------------------

//   -------------------------------------------------------------------------------
//  | For credits, see the credits.txt file                                         |
//   -------------------------------------------------------------------------------
//  |                                                                               |
//  |                              INSTRUCTIONS                                     |
//  |                                                                               |
//  |  The messages to translate are listed below.                                  |
//  |  The structure of each line is like this:                                     |
//  |     $message["Hello world!"] = "Hello world!";                                |
//  |                                                                               |
//  |  Keep the text between square brackets [] as it is.                           |
//  |  Translate the 2nd part, keeping the same punctuation and HTML tags.          |
//  |                                                                               |
//  |  The English message, for example                                             |
//  |     $message["net2ftp is written in PHP!"] = "net2ftp is written in PHP!";    |
//  |  should become after translation:                                             |
//  |     $message["net2ftp is written in PHP!"] = "net2ftp est ecrit en PHP!";     |
//  |     $message["net2ftp is written in PHP!"] = "net2ftp is geschreven in PHP!"; |
//  |                                                                               |
//  |  Note that the variable starts with a dollar sign $, that the value is        |
//  |  enclosed in double quotes " and that the line ends with a semi-colon ;       |
//  |  Be careful when editing this file, do not erase those special characters.    |
//  |                                                                               |
//  |  Some messages also contain one or more variables which start with a percent  |
//  |  sign, for example %1\$s or %2\$s. The English message, for example           |
//  |     $messages[...] = ["The file %1\$s was copied to %2\$s "]                  |
//  |  should becomes after translation:                                            |
//  |     $messages[...] = ["Le fichier %1\$s a �t� copi� vers %2\$s "]             |
//  |                                                                               |
//  |  When a real percent sign % is needed in the text it is entered as %%         |
//  |  otherwise it is interpreted as a variable. So no, it's not a mistake.        |
//  |                                                                               |
//  |  Between the messages to translate there is additional PHP code, for example: |
//  |      if ($net2ftp_globals["state2"] == "rename") {           // <-- PHP code  |
//  |          $net2ftp_messages["Rename file"] = "Rename file";   // <-- message   |
//  |      }                                                       // <-- PHP code  |
//  |  This code is needed to load the messages only when they are actually needed. |
//  |  There is no need to change or delete any of that PHP code; translate only    |
//  |  the message.                                                                 |
//  |                                                                               |
//  |  Thanks in advance to all the translators!                                    |
//  |  David.                                                                       |
//  |                                                                               |
//   -------------------------------------------------------------------------------


// -------------------------------------------------------------------------
// Language settings
// -------------------------------------------------------------------------

// HTML lang attribute
$net2ftp_messages["en"] = "en";

// HTML dir attribute: left-to-right (LTR) or right-to-left (RTL)
$net2ftp_messages["ltr"] = "ltr";

// CSS style: align left or right (use in combination with LTR or RTL)
$net2ftp_messages["left"] = "left";
$net2ftp_messages["right"] = "right";

// Encoding
$net2ftp_messages["iso-8859-1"] = "iso-8859-1";

// -------------------------------------------------------------------------
// Messages
// -------------------------------------------------------------------------

$net2ftp_messages["%1\$s File"] = "%1\$s File";
$net2ftp_messages["(Note: This link may not work if you don't have your own domain name.)"] = "(Note: This link may not work if you don't have your own domain name.)";
$net2ftp_messages["<b>%1\$s</b> could not be renamed to <b>%2\$s</b>"] = "<b>%1\$s</b> could not be renamed to <b>%2\$s</b>";
$net2ftp_messages["<b>%1\$s</b> was successfully renamed to <b>%2\$s</b>"] = "<b>%1\$s</b> was successfully renamed to <b>%2\$s</b>";
$net2ftp_messages["ARC archive"] = "ARC archive";
$net2ftp_messages["ARJ archive"] = "ARJ archive";
$net2ftp_messages["ASP script"] = "ASP script";
$net2ftp_messages["Action"] = "Action";
$net2ftp_messages["Actions"] = "Actions";
$net2ftp_messages["Add another"] = "Add another";
$net2ftp_messages["Admin functions"] = "Admin functions";
$net2ftp_messages["Adobe Acrobat document"] = "Adobe Acrobat document";
$net2ftp_messages["Advanced"] = "Advanced";
$net2ftp_messages["Advanced FTP functions"] = "Advanced FTP functions";
$net2ftp_messages["Advanced functions"] = "Advanced functions";
$net2ftp_messages["Advanced login"] = "Advanced login";
$net2ftp_messages["All"] = "All";
$net2ftp_messages["All the selected directories and files have been processed."] = "All the selected directories and files have been processed.";
$net2ftp_messages["All the subdirectories and files of the selected directories will also be deleted!"] = "All the subdirectories and files of the selected directories will also be deleted!";
$net2ftp_messages["Alternatively, use net2ftp's normal upload or upload-and-unzip functionality."] = "Alternatively, use net2ftp's normal upload or upload-and-unzip functionality.";
$net2ftp_messages["An error has occured"] = "An error has occured";
$net2ftp_messages["Anonymous"] = "Anonymous";
$net2ftp_messages["Archive <b>%1\$s</b> was not processed because its filename extension was not recognized. Only zip, tar, tgz and gz archives are supported at the moment."] = "Archive <b>%1\$s</b> was not processed because its filename extension was not recognized. Only zip, tar, tgz and gz archives are supported at the moment.";
$net2ftp_messages["Archive contains filenames with ../ or ..\\ - aborting the extraction"] = "Archive contains filenames with ../ or ..\\ - aborting the extraction";
$net2ftp_messages["Archives"] = "Archives";
$net2ftp_messages["Archives entered here will be decompressed, and the files inside will be transferred to the FTP server."] = "Archives entered here will be decompressed, and the files inside will be transferred to the FTP server.";
$net2ftp_messages["Are you sure you want to delete these directories and files?"] = "Are you sure you want to delete these directories and files?";
$net2ftp_messages["Ascending order"] = "Ascending order";
$net2ftp_messages["Automatic"] = "Automatic";
$net2ftp_messages["Back"] = "Back";
$net2ftp_messages["Basic FTP login"] = "Basic FTP login";
$net2ftp_messages["Basic SSH login"] = "Basic SSH login";
$net2ftp_messages["Bitmap file"] = "Bitmap file";
$net2ftp_messages["Bookmark"] = "Bookmark";
$net2ftp_messages["Calculate the size of the selected entries"] = "Calculate the size of the selected entries";
$net2ftp_messages["Cascading Style Sheet"] = "Cascading Style Sheet";
$net2ftp_messages["Case sensitive search"] = "Case sensitive search";
$net2ftp_messages["Changing the directory"] = "Changing the directory";
$net2ftp_messages["Changing to the directory %1\$s: "] = "Changing to the directory %1\$s: ";
$net2ftp_messages["Character encoding: "] = "Character encoding: ";
$net2ftp_messages["Check the SSH server's public key fingerprint"] = "Check the SSH server's public key fingerprint";
$net2ftp_messages["Checking files"] = "Checking files";
$net2ftp_messages["Checking if the FTP module of PHP is installed: "] = "Checking if the FTP module of PHP is installed: ";
$net2ftp_messages["Checking the permissions of the directory on the web server: a small file will be written to the /temp folder and then deleted."] = "Checking the permissions of the directory on the web server: a small file will be written to the /temp folder and then deleted.";
$net2ftp_messages["Chmod"] = "Chmod";
$net2ftp_messages["Chmod also the files within this directory"] = "Chmod also the files within this directory";
$net2ftp_messages["Chmod also the subdirectories within this directory"] = "Chmod also the subdirectories within this directory";
$net2ftp_messages["Chmod directories and files"] = "Chmod directories and files";
$net2ftp_messages["Chmod the selected entries (only works on Unix/Linux/BSD servers)"] = "Chmod the selected entries (only works on Unix/Linux/BSD servers)";
$net2ftp_messages["Choose"] = "Choose";
$net2ftp_messages["Choose a directory"] = "Choose a directory";
$net2ftp_messages["Click to sort by %1\$s in ascending order"] = "Click to sort by %1\$s in ascending order";
$net2ftp_messages["Click to sort by %1\$s in descending order"] = "Click to sort by %1\$s in descending order";
$net2ftp_messages["Closing the file: "] = "Closing the file: ";
$net2ftp_messages["Connecting to a test FTP server: "] = "Connecting to a test FTP server: ";
$net2ftp_messages["Connecting to the FTP server"] = "Connecting to the FTP server";
$net2ftp_messages["Connecting to the FTP server: "] = "Connecting to the FTP server: ";
$net2ftp_messages["Connection settings:"] = "Connection settings:";
$net2ftp_messages["Continue"] = "Continue";
$net2ftp_messages["Copied file %1\$s"] = "Copied file %1\$s";
$net2ftp_messages["Copied file <b>%1\$s</b>"] = "Copied file <b>%1\$s</b>";
$net2ftp_messages["Copy"] = "Copy";
$net2ftp_messages["Copy directories and files"] = "Copy directories and files";
$net2ftp_messages["Copy directory <b>%1\$s</b> to:"] = "Copy directory <b>%1\$s</b> to:";
$net2ftp_messages["Copy file <b>%1\$s</b> to:"] = "Copy file <b>%1\$s</b> to:";
$net2ftp_messages["Copy symlink <b>%1\$s</b> to:"] = "Copy symlink <b>%1\$s</b> to:";
$net2ftp_messages["Copy the selected entries"] = "Copy the selected entries";
$net2ftp_messages["Copying the net2ftp installer script to the FTP server"] = "Copying the net2ftp installer script to the FTP server";
$net2ftp_messages["Could not be saved"] = "Could not be saved";
$net2ftp_messages["Could not connect to SSH server"] = "Could not connect to SSH server";
$net2ftp_messages["Could not copy file %1\$s"] = "Could not copy file %1\$s";
$net2ftp_messages["Could not create directory %1\$s"] = "Could not create directory %1\$s";
$net2ftp_messages["Could not generate a temporary file."] = "Could not generate a temporary file.";
$net2ftp_messages["Could not get fingerprint"] = "Could not get fingerprint";
$net2ftp_messages["Could not get public host key"] = "Could not get public host key";
$net2ftp_messages["Could not unzip entry %1\$s (error code %2\$s)"] = "Could not unzip entry %1\$s (error code %2\$s)";
$net2ftp_messages["Create a new file in directory %1\$s"] = "Create a new file in directory %1\$s";
$net2ftp_messages["Create a website easily using ready-made templates"] = "Create a website easily using ready-made templates";
$net2ftp_messages["Create new directories"] = "Create new directories";
$net2ftp_messages["Create the MySQL database tables"] = "Create the MySQL database tables";
$net2ftp_messages["Created directory %1\$s"] = "Created directory %1\$s";
$net2ftp_messages["Created target subdirectory <b>%1\$s</b>"] = "Created target subdirectory <b>%1\$s</b>";
$net2ftp_messages["Creating a temporary directory on the FTP server"] = "Creating a temporary directory on the FTP server";
$net2ftp_messages["Creating filename: "] = "Creating filename: ";
$net2ftp_messages["Daily limit reached: the file <b>%1\$s</b> will not be transferred"] = "Daily limit reached: the file <b>%1\$s</b> will not be transferred";
$net2ftp_messages["Daily limit reached: you will not be able to transfer data"] = "Daily limit reached: you will not be able to transfer data";
$net2ftp_messages["Data transferred from this IP address today"] = "Data transferred from this IP address today";
$net2ftp_messages["Data transferred to this FTP server today"] = "Data transferred to this FTP server today";
$net2ftp_messages["Date from:"] = "Date from:";
$net2ftp_messages["Dear,"] = "Dear,";
$net2ftp_messages["Decompressing archives and transferring files"] = "Decompressing archives and transferring files";
$net2ftp_messages["Default"] = "Default";
$net2ftp_messages["Delete"] = "Delete";
$net2ftp_messages["Delete directories and files"] = "Delete directories and files";
$net2ftp_messages["Delete the selected entries"] = "Delete the selected entries";
$net2ftp_messages["Deleted file <b>%1\$s</b>"] = "Deleted file <b>%1\$s</b>";
$net2ftp_messages["Deleted subdirectory <b>%1\$s</b>"] = "Deleted subdirectory <b>%1\$s</b>";
$net2ftp_messages["Deleting the file: "] = "Deleting the file: ";
$net2ftp_messages["Descending order"] = "Descending order";
$net2ftp_messages["Details"] = "Details";
$net2ftp_messages["Different target FTP server:"] = "Different target FTP server:";
$net2ftp_messages["Directories"] = "Directories";
$net2ftp_messages["Directories with names containing \' cannot be displayed correctly. They can only be deleted. Please go back and select another subdirectory."] = "Directories with names containing \' cannot be displayed correctly. They can only be deleted. Please go back and select another subdirectory.";
$net2ftp_messages["Directory"] = "Directory";
$net2ftp_messages["Directory <b>%1\$s</b>"] = "Directory <b>%1\$s</b>";
$net2ftp_messages["Directory <b>%1\$s</b> could not be created."] = "Directory <b>%1\$s</b> could not be created.";
$net2ftp_messages["Directory <b>%1\$s</b> successfully chmodded to <b>%2\$s</b>"] = "Directory <b>%1\$s</b> successfully chmodded to <b>%2\$s</b>";
$net2ftp_messages["Directory <b>%1\$s</b> was successfully created."] = "Directory <b>%1\$s</b> was successfully created.";
$net2ftp_messages["Directory Tree"] = "Directory Tree";
$net2ftp_messages["Disabled"] = "Disabled";
$net2ftp_messages["Double-click to go to a subdirectory:"] = "Double-click to go to a subdirectory:";
$net2ftp_messages["Download"] = "Download";
$net2ftp_messages["Download a zip file containing all selected entries"] = "Download a zip file containing all selected entries";
$net2ftp_messages["Download the file %1\$s"] = "Download the file %1\$s";
$net2ftp_messages["Drag and drop one of the links below to the bookmarks bar"] = "Drag and drop one of the links below to the bookmarks bar";
$net2ftp_messages["Due to technical problems the email to <b>%1\$s</b> could not be sent."] = "Due to technical problems the email to <b>%1\$s</b> could not be sent.";
$net2ftp_messages["Edit"] = "Edit";
$net2ftp_messages["Edit the source code of file %1\$s"] = "Edit the source code of file %1\$s";
$net2ftp_messages["Email the zip file in attachment to:"] = "Email the zip file in attachment to:";
$net2ftp_messages["Empty logs"] = "Empty logs";
$net2ftp_messages["Enter the FTP server port (21 for FTP, 22 for FTP SSH or 990 for FTP SSL) - if you're not sure leave it to 21"] = "Enter the FTP server port (21 for FTP, 22 for FTP SSH or 990 for FTP SSL) - if you're not sure leave it to 21";
$net2ftp_messages["Enter your password"] = "Enter your password";
$net2ftp_messages["Enter your username"] = "Enter your username";
$net2ftp_messages["Entries which contain banned keywords can't be managed using net2ftp. This is to avoid Paypal or Ebay scams from being uploaded through net2ftp."] = "Entries which contain banned keywords can't be managed using net2ftp. This is to avoid Paypal or Ebay scams from being uploaded through net2ftp.";
$net2ftp_messages["Example"] = "Example";
$net2ftp_messages["Executable"] = "Executable";
$net2ftp_messages["Execute %1\$s in a new window"] = "Execute %1\$s in a new window";
$net2ftp_messages["FTP mode"] = "FTP mode";
$net2ftp_messages["FTP server"] = "FTP server";
$net2ftp_messages["FTP server port"] = "FTP server port";
$net2ftp_messages["FTP server response:"] = "FTP server response:";
$net2ftp_messages["File"] = "File";
$net2ftp_messages["File <b>%1\$s</b>"] = "File <b>%1\$s</b>";
$net2ftp_messages["File <b>%1\$s</b> could not be moved"] = "File <b>%1\$s</b> could not be moved";
$net2ftp_messages["File <b>%1\$s</b> could not be transferred to the FTP server"] = "File <b>%1\$s</b> could not be transferred to the FTP server";
$net2ftp_messages["File <b>%1\$s</b> has been transferred to the FTP server using FTP mode <b>%2\$s</b>"] = "File <b>%1\$s</b> has been transferred to the FTP server using FTP mode <b>%2\$s</b>";
$net2ftp_messages["File <b>%1\$s</b> is OK"] = "File <b>%1\$s</b> is OK";
$net2ftp_messages["File <b>%1\$s</b> is contains a banned keyword. This file will not be uploaded."] = "File <b>%1\$s</b> is contains a banned keyword. This file will not be uploaded.";
$net2ftp_messages["File <b>%1\$s</b> is too big. This file will not be uploaded."] = "File <b>%1\$s</b> is too big. This file will not be uploaded.";
$net2ftp_messages["File <b>%1\$s</b> was successfully chmodded to <b>%2\$s</b>"] = "File <b>%1\$s</b> was successfully chmodded to <b>%2\$s</b>";
$net2ftp_messages["File: "] = "File: ";
$net2ftp_messages["Files"] = "Files";
$net2ftp_messages["Files entered here will be transferred to the FTP server."] = "Files entered here will be transferred to the FTP server.";
$net2ftp_messages["Files which are too big can't be downloaded, uploaded, copied, moved, searched, zipped, unzipped, viewed or edited; they can only be renamed, chmodded or deleted."] = "Files which are too big can't be downloaded, uploaded, copied, moved, searched, zipped, unzipped, viewed or edited; they can only be renamed, chmodded or deleted.";
$net2ftp_messages["Find files which contain a particular word"] = "Find files which contain a particular word";
$net2ftp_messages["Fingerprint"] = "Fingerprint";
$net2ftp_messages["Follow symlink %1\$s"] = "Follow symlink %1\$s";
$net2ftp_messages["Font file"] = "Font file";
$net2ftp_messages["Forums"] = "Forums";
$net2ftp_messages["GIF file"] = "GIF file";
$net2ftp_messages["GIMP file"] = "GIMP file";
$net2ftp_messages["GZ archive"] = "GZ archive";
$net2ftp_messages["Get fingerprint"] = "Get fingerprint";
$net2ftp_messages["Get the SSH server's public key fingerprint before logging in to verify the server's identity"] = "Get the SSH server's public key fingerprint before logging in to verify the server's identity";
$net2ftp_messages["Getting archive %1\$s of %2\$s from the FTP server"] = "Getting archive %1\$s of %2\$s from the FTP server";
$net2ftp_messages["Getting fingerprint, please wait..."] = "Getting fingerprint, please wait...";
$net2ftp_messages["Getting the FTP server system type: "] = "Getting the FTP server system type: ";
$net2ftp_messages["Getting the FTP system type"] = "Getting the FTP system type";
$net2ftp_messages["Getting the current directory"] = "Getting the current directory";
$net2ftp_messages["Getting the list of directories and files"] = "Getting the list of directories and files";
$net2ftp_messages["Getting the raw list of directories and files: "] = "Getting the raw list of directories and files: ";
$net2ftp_messages["Go"] = "Go";
$net2ftp_messages["Go back"] = "Go back";
$net2ftp_messages["Go to the advanced functions"] = "Go to the advanced functions";
$net2ftp_messages["Go to the login page"] = "Go to the login page";
$net2ftp_messages["Go to the parent directory"] = "Go to the parent directory";
$net2ftp_messages["Go to the subdirectory %1\$s"] = "Go to the subdirectory %1\$s";
$net2ftp_messages["Group"] = "Group";
$net2ftp_messages["HTML file"] = "HTML file";
$net2ftp_messages["HTML templates"] = "HTML templates";
$net2ftp_messages["Help"] = "Help";
$net2ftp_messages["Help Guide"] = "Help Guide";
$net2ftp_messages["IP address: "] = "IP address: ";
$net2ftp_messages["Icons"] = "Icons";
$net2ftp_messages["If the destination file already exists, it will be overwritten"] = "If the destination file already exists, it will be overwritten";
$net2ftp_messages["If you know nothing about this or if you don't trust that person, please delete this email without opening the Zip file in attachment."] = "If you know nothing about this or if you don't trust that person, please delete this email without opening the Zip file in attachment.";
$net2ftp_messages["If you need unlimited usage, please install net2ftp on your own web server."] = "If you need unlimited usage, please install net2ftp on your own web server.";
$net2ftp_messages["If you really need net2ftp to be able to handle big tasks which take a long time, consider installing net2ftp on your own server."] = "If you really need net2ftp to be able to handle big tasks which take a long time, consider installing net2ftp on your own server.";
$net2ftp_messages["If you want to copy the files to another FTP server, enter your login data."] = "If you want to copy the files to another FTP server, enter your login data.";
$net2ftp_messages["Image"] = "Image";
$net2ftp_messages["In order to guarantee the fair use of the web server for everyone, the data transfer volume and script execution time are limited per user, and per day. Once this limit is reached, you can still browse the FTP server but not transfer data to/from it."] = "In order to guarantee the fair use of the web server for everyone, the data transfer volume and script execution time are limited per user, and per day. Once this limit is reached, you can still browse the FTP server but not transfer data to/from it.";
$net2ftp_messages["In order to run it, click on the link below."] = "In order to run it, click on the link below.";
$net2ftp_messages["Information about the sender: "] = "Information about the sender: ";
$net2ftp_messages["Initial directory"] = "Initial directory";
$net2ftp_messages["Install"] = "Install";
$net2ftp_messages["Install software packages"] = "Install software packages";
$net2ftp_messages["Install software packages (requires PHP on web server)"] = "Install software packages (requires PHP on web server)";
$net2ftp_messages["JPEG file"] = "JPEG file";
$net2ftp_messages["Java Upload"] = "Java Upload";
$net2ftp_messages["Java source file"] = "Java source file";
$net2ftp_messages["JavaScript file"] = "JavaScript file";
$net2ftp_messages["Language:"] = "Language:";
$net2ftp_messages["Leave empty if you want to copy the files to the same FTP server."] = "Leave empty if you want to copy the files to the same FTP server.";
$net2ftp_messages["License"] = "License";
$net2ftp_messages["Line"] = "Line";
$net2ftp_messages["List"] = "List";
$net2ftp_messages["List of commands:"] = "List of commands:";
$net2ftp_messages["Logging"] = "Logging";
$net2ftp_messages["Logging into the FTP server"] = "Logging into the FTP server";
$net2ftp_messages["Logging into the FTP server: "] = "Logging into the FTP server: ";
$net2ftp_messages["Logging out of the FTP server"] = "Logging out of the FTP server";
$net2ftp_messages["Login"] = "Login";
$net2ftp_messages["Login!"] = "Login!";
$net2ftp_messages["Logout"] = "Logout";
$net2ftp_messages["MOV movie file"] = "MOV movie file";
$net2ftp_messages["MPEG movie file"] = "MPEG movie file";
$net2ftp_messages["MS Office - Access database"] = "MS Office - Access database";
$net2ftp_messages["MS Office - Excel spreadsheet"] = "MS Office - Excel spreadsheet";
$net2ftp_messages["MS Office - PowerPoint presentation"] = "MS Office - PowerPoint presentation";
$net2ftp_messages["MS Office - Project file"] = "MS Office - Project file";
$net2ftp_messages["MS Office - Visio drawing"] = "MS Office - Visio drawing";
$net2ftp_messages["MS Office - Word document"] = "MS Office - Word document";
$net2ftp_messages["Make a new subdirectory in directory %1\$s"] = "Make a new subdirectory in directory %1\$s";
$net2ftp_messages["Message of the sender: "] = "Message of the sender: ";
$net2ftp_messages["Mobile"] = "Mobile";
$net2ftp_messages["Mod Time"] = "Mod Time";
$net2ftp_messages["Move"] = "Move";
$net2ftp_messages["Move directories and files"] = "Move directories and files";
$net2ftp_messages["Move directory <b>%1\$s</b> to:"] = "Move directory <b>%1\$s</b> to:";
$net2ftp_messages["Move file <b>%1\$s</b> to:"] = "Move file <b>%1\$s</b> to:";
$net2ftp_messages["Move symlink <b>%1\$s</b> to:"] = "Move symlink <b>%1\$s</b> to:";
$net2ftp_messages["Move the selected entries"] = "Move the selected entries";
$net2ftp_messages["Moved directory <b>%1\$s</b>"] = "Moved directory <b>%1\$s</b>";
$net2ftp_messages["Moved file <b>%1\$s</b>"] = "Moved file <b>%1\$s</b>";
$net2ftp_messages["MySQL database"] = "MySQL database";
$net2ftp_messages["MySQL password"] = "MySQL password";
$net2ftp_messages["MySQL password length"] = "MySQL password length";
$net2ftp_messages["MySQL server"] = "MySQL server";
$net2ftp_messages["MySQL username"] = "MySQL username";
$net2ftp_messages["Name"] = "Name";
$net2ftp_messages["New dir"] = "New dir";
$net2ftp_messages["New directory name:"] = "New directory name:";
$net2ftp_messages["New file"] = "New file";
$net2ftp_messages["New file name: "] = "New file name: ";
$net2ftp_messages["New name: "] = "New name: ";
$net2ftp_messages["No data"] = "No data";
$net2ftp_messages["Not yet saved"] = "Not yet saved";
$net2ftp_messages["Note that if you don't open the Zip file, the files inside cannot harm your computer."] = "Note that if you don't open the Zip file, the files inside cannot harm your computer.";
$net2ftp_messages["Note that sending files is not anonymous: your IP address as well as the time of the sending will be added to the email."] = "Note that sending files is not anonymous: your IP address as well as the time of the sending will be added to the email.";
$net2ftp_messages["Note: other users of this computer could click on the browser's Back button and access the FTP server."] = "Note: other users of this computer could click on the browser's Back button and access the FTP server.";
$net2ftp_messages["Note: the target directory must already exist before anything can be copied into it."] = "Note: the target directory must already exist before anything can be copied into it.";
$net2ftp_messages["Note: when you will use this bookmark, a popup window will ask you for your username and password."] = "Note: when you will use this bookmark, a popup window will ask you for your username and password.";
$net2ftp_messages["OK"] = "OK";
$net2ftp_messages["OK. Filename: %1\$s"] = "OK. Filename: %1\$s";
$net2ftp_messages["Old name: "] = "Old name: ";
$net2ftp_messages["One click access (net2ftp won't ask for a password - less safe)"] = "One click access (net2ftp won't ask for a password - less safe)";
$net2ftp_messages["Open"] = "Open";
$net2ftp_messages["OpenOffice - Calc 6.0 spreadsheet"] = "OpenOffice - Calc 6.0 spreadsheet";
$net2ftp_messages["OpenOffice - Calc 6.0 template"] = "OpenOffice - Calc 6.0 template";
$net2ftp_messages["OpenOffice - Draw 6.0 document"] = "OpenOffice - Draw 6.0 document";
$net2ftp_messages["OpenOffice - Draw 6.0 template"] = "OpenOffice - Draw 6.0 template";
$net2ftp_messages["OpenOffice - Impress 6.0 presentation"] = "OpenOffice - Impress 6.0 presentation";
$net2ftp_messages["OpenOffice - Impress 6.0 template"] = "OpenOffice - Impress 6.0 template";
$net2ftp_messages["OpenOffice - Math 6.0 document"] = "OpenOffice - Math 6.0 document";
$net2ftp_messages["OpenOffice - Writer 6.0 document"] = "OpenOffice - Writer 6.0 document";
$net2ftp_messages["OpenOffice - Writer 6.0 global document"] = "OpenOffice - Writer 6.0 global document";
$net2ftp_messages["OpenOffice - Writer 6.0 template"] = "OpenOffice - Writer 6.0 template";
$net2ftp_messages["Opening the file in write mode: "] = "Opening the file in write mode: ";
$net2ftp_messages["Owner"] = "Owner";
$net2ftp_messages["PHP Source"] = "PHP Source";
$net2ftp_messages["PHP script"] = "PHP script";
$net2ftp_messages["PNG file"] = "PNG file";
$net2ftp_messages["Parsing the file"] = "Parsing the file";
$net2ftp_messages["Parsing the list of directories and files"] = "Parsing the list of directories and files";
$net2ftp_messages["Passive mode"] = "Passive mode";
$net2ftp_messages["Password"] = "Password";
$net2ftp_messages["Password length"] = "Password length";
$net2ftp_messages["Perms"] = "Perms";
$net2ftp_messages["Please enter a password."] = "Please enter a password.";
$net2ftp_messages["Please enter a username."] = "Please enter a username.";
$net2ftp_messages["Please enter a valid date in Y-m-d format in the \"from\" textbox."] = "Please enter a valid date in Y-m-d format in the \"from\" textbox.";
$net2ftp_messages["Please enter a valid date in Y-m-d format in the \"to\" textbox."] = "Please enter a valid date in Y-m-d format in the \"to\" textbox.";
$net2ftp_messages["Please enter a valid file size in the \"from\" textbox, for example 0."] = "Please enter a valid file size in the \"from\" textbox, for example 0.";
$net2ftp_messages["Please enter a valid file size in the \"to\" textbox, for example 500000."] = "Please enter a valid file size in the \"to\" textbox, for example 500000.";
$net2ftp_messages["Please enter a valid filename."] = "Please enter a valid filename.";
$net2ftp_messages["Please enter a valid search word or phrase."] = "Please enter a valid search word or phrase.";
$net2ftp_messages["Please enter an FTP server."] = "Please enter an FTP server.";
$net2ftp_messages["Please enter your Administrator username and password."] = "Please enter your Administrator username and password.";
$net2ftp_messages["Please enter your MySQL settings:"] = "Please enter your MySQL settings:";
$net2ftp_messages["Please enter your username and password for FTP server <b>%1\$s</b>."] = "Please enter your username and password for FTP server <b>%1\$s</b>.";
$net2ftp_messages["Please specify a filename"] = "Please specify a filename";
$net2ftp_messages["Please wait..."] = "Please wait...";
$net2ftp_messages["Powered by"] = "Powered by";
$net2ftp_messages["Printing the list of directories and files"] = "Printing the list of directories and files";
$net2ftp_messages["Printing the result"] = "Printing the result";
$net2ftp_messages["Processing archive nr %1\$s: <b>%2\$s</b>"] = "Processing archive nr %1\$s: <b>%2\$s</b>";
$net2ftp_messages["Processing directory <b>%1\$s</b>"] = "Processing directory <b>%1\$s</b>";
$net2ftp_messages["Processing entries within directory <b>%1\$s</b>:"] = "Processing entries within directory <b>%1\$s</b>:";
$net2ftp_messages["Processing entry %1\$s"] = "Processing entry %1\$s";
$net2ftp_messages["Processing of directory <b>%1\$s</b> completed"] = "Processing of directory <b>%1\$s</b> completed";
$net2ftp_messages["Processing the entries"] = "Processing the entries";
$net2ftp_messages["Protocol"] = "Protocol";
$net2ftp_messages["Quicktime movie file"] = "Quicktime movie file";
$net2ftp_messages["RPM"] = "RPM";
$net2ftp_messages["Reading the file"] = "Reading the file";
$net2ftp_messages["Real movie file"] = "Real movie file";
$net2ftp_messages["Refresh"] = "Refresh";
$net2ftp_messages["Rename"] = "Rename";
$net2ftp_messages["Rename directories and files"] = "Rename directories and files";
$net2ftp_messages["Rename the selected entries"] = "Rename the selected entries";
$net2ftp_messages["Requested files"] = "Requested files";
$net2ftp_messages["Restrict the search to:"] = "Restrict the search to:";
$net2ftp_messages["Restrictions:"] = "Restrictions:";
$net2ftp_messages["Results:"] = "Results:";
$net2ftp_messages["Right-click on one of the links below and choose \"Add to Favorites...\""] = "Right-click on one of the links below and choose \"Add to Favorites...\"";
$net2ftp_messages["Right-click on one of the links below and choose \"Bookmark This Link...\""] = "Right-click on one of the links below and choose \"Bookmark This Link...\"";
$net2ftp_messages["Right-click on one of the links below and choose \"Bookmark link...\""] = "Right-click on one of the links below and choose \"Bookmark link...\"";
$net2ftp_messages["Right-click on one the links below and choose \"Add Link to Bookmarks...\""] = "Right-click on one the links below and choose \"Add Link to Bookmarks...\"";
$net2ftp_messages["SSH fingerprint"] = "SSH fingerprint";
$net2ftp_messages["SSH server"] = "SSH server";
$net2ftp_messages["Save"] = "Save";
$net2ftp_messages["Save the zip file on the FTP server as:"] = "Save the zip file on the FTP server as:";
$net2ftp_messages["Saved at %1\$s"] = "Saved at %1\$s";
$net2ftp_messages["Script finished in %1\$s seconds"] = "Script finished in %1\$s seconds";
$net2ftp_messages["Script halted"] = "Script halted";
$net2ftp_messages["Search"] = "Search";
$net2ftp_messages["Search directories and files"] = "Search directories and files";
$net2ftp_messages["Search for a word or phrase"] = "Search for a word or phrase";
$net2ftp_messages["Search results"] = "Search results";
$net2ftp_messages["Searching the files..."] = "Searching the files...";
$net2ftp_messages["Select the directory %1\$s"] = "Select the directory %1\$s";
$net2ftp_messages["Select the file %1\$s"] = "Select the file %1\$s";
$net2ftp_messages["Select the symlink %1\$s"] = "Select the symlink %1\$s";
$net2ftp_messages["Send arbitrary FTP commands"] = "Send arbitrary FTP commands";
$net2ftp_messages["Send arbitrary FTP commands to the FTP server"] = "Send arbitrary FTP commands to the FTP server";
$net2ftp_messages["Sending FTP command %1\$s of %2\$s"] = "Sending FTP command %1\$s of %2\$s";
$net2ftp_messages["Sent via the net2ftp application installed on this website: "] = "Sent via the net2ftp application installed on this website: ";
$net2ftp_messages["Set all permissions"] = "Set all permissions";
$net2ftp_messages["Set all targetdirectories"] = "Set all targetdirectories";
$net2ftp_messages["Set the permissions of directory <b>%1\$s</b> to: "] = "Set the permissions of directory <b>%1\$s</b> to: ";
$net2ftp_messages["Set the permissions of file <b>%1\$s</b> to: "] = "Set the permissions of file <b>%1\$s</b> to: ";
$net2ftp_messages["Set the permissions of symlink <b>%1\$s</b> to: "] = "Set the permissions of symlink <b>%1\$s</b> to: ";
$net2ftp_messages["Setting the passive mode"] = "Setting the passive mode";
$net2ftp_messages["Setting the passive mode: "] = "Setting the passive mode: ";
$net2ftp_messages["Setting the permissions of the temporary directory"] = "Setting the permissions of the temporary directory";
$net2ftp_messages["Settings used:"] = "Settings used:";
$net2ftp_messages["Setup MySQL tables"] = "Setup MySQL tables";
$net2ftp_messages["Shell script"] = "Shell script";
$net2ftp_messages["Shockwave file"] = "Shockwave file";
$net2ftp_messages["Shockwave flash file"] = "Shockwave flash file";
$net2ftp_messages["Should this link not be correct, enter the URL manually in your web browser."] = "Should this link not be correct, enter the URL manually in your web browser.";
$net2ftp_messages["Size"] = "Size";
$net2ftp_messages["Size of selected directories and files"] = "Size of selected directories and files";
$net2ftp_messages["Skin:"] = "Skin:";
$net2ftp_messages["Some additional comments to add in the email:"] = "Some additional comments to add in the email:";
$net2ftp_messages["Someone has requested the files in attachment to be sent to this email account (%1\$s)."] = "Someone has requested the files in attachment to be sent to this email account (%1\$s).";
$net2ftp_messages["Standard"] = "Standard";
$net2ftp_messages["StarOffice - StarCalc 5.x spreadsheet"] = "StarOffice - StarCalc 5.x spreadsheet";
$net2ftp_messages["StarOffice - StarChart 5.x document"] = "StarOffice - StarChart 5.x document";
$net2ftp_messages["StarOffice - StarDraw 5.x document"] = "StarOffice - StarDraw 5.x document";
$net2ftp_messages["StarOffice - StarImpress 5.x presentation"] = "StarOffice - StarImpress 5.x presentation";
$net2ftp_messages["StarOffice - StarImpress Packed 5.x file"] = "StarOffice - StarImpress Packed 5.x file";
$net2ftp_messages["StarOffice - StarMail 5.x mail file"] = "StarOffice - StarMail 5.x mail file";
$net2ftp_messages["StarOffice - StarMath 5.x document"] = "StarOffice - StarMath 5.x document";
$net2ftp_messages["StarOffice - StarWriter 5.x document"] = "StarOffice - StarWriter 5.x document";
$net2ftp_messages["StarOffice - StarWriter 5.x global document"] = "StarOffice - StarWriter 5.x global document";
$net2ftp_messages["Status: <b>This file could not be saved</b>"] = "Status: <b>This file could not be saved</b>";
$net2ftp_messages["Status: Saved on <b>%1\$s</b> using mode %2\$s"] = "Status: Saved on <b>%1\$s</b> using mode %2\$s";
$net2ftp_messages["Status: This file has not yet been saved"] = "Status: This file has not yet been saved";
$net2ftp_messages["Submit"] = "Submit";
$net2ftp_messages["Symlink"] = "Symlink";
$net2ftp_messages["Symlink <b>%1\$s</b>"] = "Symlink <b>%1\$s</b>";
$net2ftp_messages["Symlinks"] = "Symlinks";
$net2ftp_messages["Syntax highlighting powered by <a href=\"http://luminous.asgaard.co.uk\">Luminous</a>"] = "Syntax highlighting powered by <a href=\"http://luminous.asgaard.co.uk\">Luminous</a>";
$net2ftp_messages["TAR archive"] = "TAR archive";
$net2ftp_messages["TIF file"] = "TIF file";
$net2ftp_messages["Table net2ftp_log_access contains duplicate entries."] = "Table net2ftp_log_access contains duplicate entries.";
$net2ftp_messages["Table net2ftp_log_access could not be updated."] = "Table net2ftp_log_access could not be updated.";
$net2ftp_messages["Table net2ftp_log_consumption_ftpserver contains duplicate entries."] = "Table net2ftp_log_consumption_ftpserver contains duplicate entries.";
$net2ftp_messages["Table net2ftp_log_consumption_ftpserver contains duplicate rows."] = "Table net2ftp_log_consumption_ftpserver contains duplicate rows.";
$net2ftp_messages["Table net2ftp_log_consumption_ftpserver could not be updated."] = "Table net2ftp_log_consumption_ftpserver could not be updated.";
$net2ftp_messages["Table net2ftp_log_consumption_ipaddress contains duplicate entries."] = "Table net2ftp_log_consumption_ipaddress contains duplicate entries.";
$net2ftp_messages["Table net2ftp_log_consumption_ipaddress contains duplicate rows."] = "Table net2ftp_log_consumption_ipaddress contains duplicate rows.";
$net2ftp_messages["Table net2ftp_log_consumption_ipaddress could not be updated."] = "Table net2ftp_log_consumption_ipaddress could not be updated.";
$net2ftp_messages["Table net2ftp_log_status contains duplicate entries."] = "Table net2ftp_log_status contains duplicate entries.";
$net2ftp_messages["Table net2ftp_log_status could not be updated."] = "Table net2ftp_log_status could not be updated.";
$net2ftp_messages["Table net2ftp_users contains duplicate rows."] = "Table net2ftp_users contains duplicate rows.";
$net2ftp_messages["Target directory:"] = "Target directory:";
$net2ftp_messages["Target name:"] = "Target name:";
$net2ftp_messages["Test the net2ftp list parsing rules"] = "Test the net2ftp list parsing rules";
$net2ftp_messages["Testing the FTP functions"] = "Testing the FTP functions";
$net2ftp_messages["Text file"] = "Text file";
$net2ftp_messages["The <a href=\"http://www.php.net/manual/en/ref.ftp.php\" target=\"_blank\">FTP module of PHP</a> is not installed.<br /><br /> The administrator of this website should install this FTP module. Installation instructions are given on <a href=\"http://www.php.net/manual/en/ref.ftp.php\" target=\"_blank\">php.net</a>.<br />"] = "The <a href=\"http://www.php.net/manual/en/ref.ftp.php\" target=\"_blank\">FTP module of PHP</a> is not installed.<br /><br /> The administrator of this website should install this FTP module. Installation instructions are given on <a href=\"http://www.php.net/manual/en/ref.ftp.php\" target=\"_blank\">php.net</a>.<br />";
$net2ftp_messages["The FTP module of PHP and/or OpenSSL are not installed.<br /><br /> The administrator of this website should install these. Installation instructions are given on php.net: <a href=\"http://www.php.net/manual/en/ref.ftp.php\" target=\"_blank\">FTP module installation</a> and <a href=\"http://php.net/manual/en/openssl.installation.php\">OpenSSL installation</a>.<br />"] = "The FTP module of PHP and/or OpenSSL are not installed.<br /><br /> The administrator of this website should install these. Installation instructions are given on php.net: <a href=\"http://www.php.net/manual/en/ref.ftp.php\" target=\"_blank\">FTP module installation</a> and <a href=\"http://php.net/manual/en/openssl.installation.php\">OpenSSL installation</a>.<br />";
$net2ftp_messages["The FTP server <b>%1\$s</b> is in the list of banned FTP servers."] = "The FTP server <b>%1\$s</b> is in the list of banned FTP servers.";
$net2ftp_messages["The FTP server <b>%1\$s</b> is not in the list of allowed FTP servers."] = "The FTP server <b>%1\$s</b> is not in the list of allowed FTP servers.";
$net2ftp_messages["The FTP server port %1\$s may not be used."] = "The FTP server port %1\$s may not be used.";
$net2ftp_messages["The FTP transfer mode (ASCII or BINARY) will be automatically determined, based on the filename extension"] = "The FTP transfer mode (ASCII or BINARY) will be automatically determined, based on the filename extension";
$net2ftp_messages["The SQL query nr <b>%1\$s</b> could not be executed."] = "The SQL query nr <b>%1\$s</b> could not be executed.";
$net2ftp_messages["The SQL query nr <b>%1\$s</b> was executed successfully."] = "The SQL query nr <b>%1\$s</b> was executed successfully.";
$net2ftp_messages["The SSH server's fingerprint does not match the fingerprint which was validated previously.<br /><br />Current fingerprint: %1\$s <br />Fingerprint validated previously: %2\$s <br /><br />"] = "The SSH server's fingerprint does not match the fingerprint which was validated previously.<br /><br />Current fingerprint: %1\$s <br />Fingerprint validated previously: %2\$s <br /><br />";
$net2ftp_messages["The chmod nr <b>%1\$s</b> is out of the range 000-777. Please try again."] = "The chmod nr <b>%1\$s</b> is out of the range 000-777. Please try again.";
$net2ftp_messages["The directory <b>%1\$s</b> contains a banned keyword, aborting the move"] = "The directory <b>%1\$s</b> contains a banned keyword, aborting the move";
$net2ftp_messages["The directory <b>%1\$s</b> contains a banned keyword, so this directory will be skipped"] = "The directory <b>%1\$s</b> contains a banned keyword, so this directory will be skipped";
$net2ftp_messages["The directory <b>%1\$s</b> could not be selected - you may not have sufficient rights to view this directory, or it may not exist."] = "The directory <b>%1\$s</b> could not be selected - you may not have sufficient rights to view this directory, or it may not exist.";
$net2ftp_messages["The directory <b>%1\$s</b> could not be selected, so this directory will be skipped"] = "The directory <b>%1\$s</b> could not be selected, so this directory will be skipped";
$net2ftp_messages["The directory <b>%1\$s</b> does not exist or could not be selected, so the directory <b>%2\$s</b> is shown instead."] = "The directory <b>%1\$s</b> does not exist or could not be selected, so the directory <b>%2\$s</b> is shown instead.";
$net2ftp_messages["The email address you have entered (%1\$s) does not seem to be valid.<br />Please enter an address in the format <b>username@domain.com</b>"] = "The email address you have entered (%1\$s) does not seem to be valid.<br />Please enter an address in the format <b>username@domain.com</b>";
$net2ftp_messages["The file %1\$s could not be opened."] = "The file %1\$s could not be opened.";
$net2ftp_messages["The file <b>%1\$s</b> contains a banned keyword, aborting the move"] = "The file <b>%1\$s</b> contains a banned keyword, aborting the move";
$net2ftp_messages["The file <b>%1\$s</b> contains a banned keyword, so this file will be skipped"] = "The file <b>%1\$s</b> contains a banned keyword, so this file will be skipped";
$net2ftp_messages["The file <b>%1\$s</b> is too big to be copied, so this file will be skipped"] = "The file <b>%1\$s</b> is too big to be copied, so this file will be skipped";
$net2ftp_messages["The file <b>%1\$s</b> is too big to be moved, aborting the move"] = "The file <b>%1\$s</b> is too big to be moved, aborting the move";
$net2ftp_messages["The file is too big to be transferred"] = "The file is too big to be transferred";
$net2ftp_messages["The handle of file %1\$s could not be closed."] = "The handle of file %1\$s could not be closed.";
$net2ftp_messages["The handle of file %1\$s could not be opened."] = "The handle of file %1\$s could not be opened.";
$net2ftp_messages["The latest version information could not be retrieved from the net2ftp.com server. Check the security settings of your browser, which may prevent the loading of a small file from the net2ftp.com server."] = "The latest version information could not be retrieved from the net2ftp.com server. Check the security settings of your browser, which may prevent the loading of a small file from the net2ftp.com server.";
$net2ftp_messages["The log tables could not be copied."] = "The log tables could not be copied.";
$net2ftp_messages["The log tables could not be renamed."] = "The log tables could not be renamed.";
$net2ftp_messages["The log tables were copied successfully."] = "The log tables were copied successfully.";
$net2ftp_messages["The log tables were renamed successfully."] = "The log tables were renamed successfully.";
$net2ftp_messages["The maximum execution time is <b>%1\$s seconds</b>"] = "The maximum execution time is <b>%1\$s seconds</b>";
$net2ftp_messages["The maximum size of one file is restricted by net2ftp to <b>%1\$s</b> and by PHP to <b>%2\$s</b>"] = "The maximum size of one file is restricted by net2ftp to <b>%1\$s</b> and by PHP to <b>%2\$s</b>";
$net2ftp_messages["The net2ftp installer script has been copied to the FTP server."] = "The net2ftp installer script has been copied to the FTP server.";
$net2ftp_messages["The new directories will be created in <b>%1\$s</b>."] = "The new directories will be created in <b>%1\$s</b>.";
$net2ftp_messages["The new name may not contain any banned keywords. This entry was not renamed to <b>%1\$s</b>"] = "The new name may not contain any banned keywords. This entry was not renamed to <b>%1\$s</b>";
$net2ftp_messages["The new name may not contain any dots. This entry was not renamed to <b>%1\$s</b>"] = "The new name may not contain any dots. This entry was not renamed to <b>%1\$s</b>";
$net2ftp_messages["The number of files which were skipped is:"] = "The number of files which were skipped is:";
$net2ftp_messages["The oldest log table could not be dropped."] = "The oldest log table could not be dropped.";
$net2ftp_messages["The oldest log table was dropped successfully."] = "The oldest log table was dropped successfully.";
$net2ftp_messages["The online installation is about 1-2 MB and the offline installation is about 13 MB. This 'end-user' java is called JRE (Java Runtime Environment)."] = "The online installation is about 1-2 MB and the offline installation is about 13 MB. This 'end-user' java is called JRE (Java Runtime Environment).";
$net2ftp_messages["The table <b>%1\$s</b> could not be emptied."] = "The table <b>%1\$s</b> could not be emptied.";
$net2ftp_messages["The table <b>%1\$s</b> could not be optimized."] = "The table <b>%1\$s</b> could not be optimized.";
$net2ftp_messages["The table <b>%1\$s</b> was emptied successfully."] = "The table <b>%1\$s</b> was emptied successfully.";
$net2ftp_messages["The table <b>%1\$s</b> was optimized successfully."] = "The table <b>%1\$s</b> was optimized successfully.";
$net2ftp_messages["The target directory <b>%1\$s</b> is the same as or a subdirectory of the source directory <b>%2\$s</b>, so this directory will be skipped"] = "The target directory <b>%1\$s</b> is the same as or a subdirectory of the source directory <b>%2\$s</b>, so this directory will be skipped";
$net2ftp_messages["The target for file <b>%1\$s</b> is the same as the source, so this file will be skipped"] = "The target for file <b>%1\$s</b> is the same as the source, so this file will be skipped";
$net2ftp_messages["The task you wanted to perform with net2ftp took more time than the allowed %1\$s seconds, and therefor that task was stopped."] = "The task you wanted to perform with net2ftp took more time than the allowed %1\$s seconds, and therefor that task was stopped.";
$net2ftp_messages["The total size taken by the selected directories and files is:"] = "The total size taken by the selected directories and files is:";
$net2ftp_messages["The variable <b>consumption_ipaddress_datatransfer</b> is not numeric."] = "The variable <b>consumption_ipaddress_datatransfer</b> is not numeric.";
$net2ftp_messages["The word <b>%1\$s</b> was found in the following files:"] = "The word <b>%1\$s</b> was found in the following files:";
$net2ftp_messages["The word <b>%1\$s</b> was not found in the selected directories and files."] = "The word <b>%1\$s</b> was not found in the selected directories and files.";
$net2ftp_messages["The zip file has been saved on the FTP server as <b>%1\$s</b>"] = "The zip file has been saved on the FTP server as <b>%1\$s</b>";
$net2ftp_messages["The zip file has been sent to <b>%1\$s</b>."] = "The zip file has been sent to <b>%1\$s</b>.";
$net2ftp_messages["This SQL query is going to be executed:"] = "This SQL query is going to be executed:";
$net2ftp_messages["This file is not accessible from the web"] = "This file is not accessible from the web";
$net2ftp_messages["This folder is empty"] = "This folder is empty";
$net2ftp_messages["This function has been disabled by the Administrator of this website."] = "This function has been disabled by the Administrator of this website.";
$net2ftp_messages["This function is available on PHP 5 only"] = "This function is available on PHP 5 only";
$net2ftp_messages["This script runs on your web server and requires PHP to be installed."] = "This script runs on your web server and requires PHP to be installed.";
$net2ftp_messages["This time limit guarantees the fair use of the web server for everyone."] = "This time limit guarantees the fair use of the web server for everyone.";
$net2ftp_messages["This version of net2ftp is up-to-date."] = "This version of net2ftp is up-to-date.";
$net2ftp_messages["Time of sending: "] = "Time of sending: ";
$net2ftp_messages["To prevent this, you must close all browser windows."] = "To prevent this, you must close all browser windows.";
$net2ftp_messages["To save the image, right-click on it and choose 'Save picture as...'"] = "To save the image, right-click on it and choose 'Save picture as...'";
$net2ftp_messages["To set a common target directory, enter that target directory in the textbox above and click on the button \"Set all targetdirectories\"."] = "To set a common target directory, enter that target directory in the textbox above and click on the button \"Set all targetdirectories\".";
$net2ftp_messages["To set all permissions to the same values, enter those permissions above and click on the button \"Set all permissions\""] = "To set all permissions to the same values, enter those permissions above and click on the button \"Set all permissions\"";
$net2ftp_messages["To set all permissions to the same values, enter those permissions and click on the button \"Set all permissions\""] = "To set all permissions to the same values, enter those permissions and click on the button \"Set all permissions\"";
$net2ftp_messages["To use this applet, please install the newest version of Sun's java. You can get it from <a href=\"http://www.java.com/\">java.com</a>. Click on Get It Now."] = "To use this applet, please install the newest version of Sun's java. You can get it from <a href=\"http://www.java.com/\">java.com</a>. Click on Get It Now.";
$net2ftp_messages["Transferring files to the FTP server"] = "Transferring files to the FTP server";
$net2ftp_messages["Transform selected entries: "] = "Transform selected entries: ";
$net2ftp_messages["Transform selected entry: "] = "Transform selected entry: ";
$net2ftp_messages["Troubleshoot an FTP server"] = "Troubleshoot an FTP server";
$net2ftp_messages["Troubleshoot net2ftp on this webserver"] = "Troubleshoot net2ftp on this webserver";
$net2ftp_messages["Troubleshoot your net2ftp installation"] = "Troubleshoot your net2ftp installation";
$net2ftp_messages["Troubleshooting functions"] = "Troubleshooting functions";
$net2ftp_messages["Try to split your task in smaller tasks: restrict your selection of files, and omit the biggest files."] = "Try to split your task in smaller tasks: restrict your selection of files, and omit the biggest files.";
$net2ftp_messages["Two click access (net2ftp will ask for a password - safer)"] = "Two click access (net2ftp will ask for a password - safer)";
$net2ftp_messages["Type"] = "Type";
$net2ftp_messages["Unable to close the handle of the temporary file"] = "Unable to close the handle of the temporary file";
$net2ftp_messages["Unable to connect to FTP server <b>%1\$s</b> on port <b>%2\$s</b>.<br /><br />Are you sure this is the address of the FTP server? This is often different from that of the HTTP (web) server. Please contact your ISP helpdesk or system administrator for help.<br />"] = "Unable to connect to FTP server <b>%1\$s</b> on port <b>%2\$s</b>.<br /><br />Are you sure this is the address of the FTP server? This is often different from that of the HTTP (web) server. Please contact your ISP helpdesk or system administrator for help.<br />";
$net2ftp_messages["Unable to connect to SSH server <b>%1\$s</b> on port <b>%2\$s</b> (%3\$s).<br /><br />Are you sure this is the address of the FTP server? This is often different from that of the HTTP (web) server. Please contact your ISP helpdesk or system administrator for help.<br />"] = "Unable to connect to SSH server <b>%1\$s</b> on port <b>%2\$s</b> (%3\$s).<br /><br />Are you sure this is the address of the FTP server? This is often different from that of the HTTP (web) server. Please contact your ISP helpdesk or system administrator for help.<br />";
$net2ftp_messages["Unable to connect to the MySQL database. Please check your MySQL database settings in net2ftp's configuration file settings.inc.php."] = "Unable to connect to the MySQL database. Please check your MySQL database settings in net2ftp's configuration file settings.inc.php.";
$net2ftp_messages["Unable to connect to the server <b>%1\$s</b>."] = "Unable to connect to the server <b>%1\$s</b>.";
$net2ftp_messages["Unable to copy the file <b>%1\$s</b>"] = "Unable to copy the file <b>%1\$s</b>";
$net2ftp_messages["Unable to copy the local file to the remote file <b>%1\$s</b> using FTP mode <b>%2\$s</b>"] = "Unable to copy the local file to the remote file <b>%1\$s</b> using FTP mode <b>%2\$s</b>";
$net2ftp_messages["Unable to copy the remote file <b>%1\$s</b> to the local file using FTP mode <b>%2\$s</b>"] = "Unable to copy the remote file <b>%1\$s</b> to the local file using FTP mode <b>%2\$s</b>";
$net2ftp_messages["Unable to create a temporary directory (too many tries)"] = "Unable to create a temporary directory (too many tries)";
$net2ftp_messages["Unable to create a temporary directory because (parent directory is not writeable)"] = "Unable to create a temporary directory because (parent directory is not writeable)";
$net2ftp_messages["Unable to create a temporary directory because (unvalid parent directory)"] = "Unable to create a temporary directory because (unvalid parent directory)";
$net2ftp_messages["Unable to create the directory <b>%1\$s</b>"] = "Unable to create the directory <b>%1\$s</b>";
$net2ftp_messages["Unable to create the subdirectory <b>%1\$s</b>. It may already exist. Continuing the copy/move process..."] = "Unable to create the subdirectory <b>%1\$s</b>. It may already exist. Continuing the copy/move process...";
$net2ftp_messages["Unable to create the temporary file. Check the permissions of the %1\$s directory."] = "Unable to create the temporary file. Check the permissions of the %1\$s directory.";
$net2ftp_messages["Unable to delete file <b>%1\$s</b>"] = "Unable to delete file <b>%1\$s</b>";
$net2ftp_messages["Unable to delete the directory <b>%1\$s</b>"] = "Unable to delete the directory <b>%1\$s</b>";
$net2ftp_messages["Unable to delete the file <b>%1\$s</b>"] = "Unable to delete the file <b>%1\$s</b>";
$net2ftp_messages["Unable to delete the local file"] = "Unable to delete the local file";
$net2ftp_messages["Unable to delete the subdirectory <b>%1\$s</b> - it may not be empty"] = "Unable to delete the subdirectory <b>%1\$s</b> - it may not be empty";
$net2ftp_messages["Unable to delete the temporary directory"] = "Unable to delete the temporary directory";
$net2ftp_messages["Unable to delete the temporary file"] = "Unable to delete the temporary file";
$net2ftp_messages["Unable to delete the temporary file %1\$s"] = "Unable to delete the temporary file %1\$s";
$net2ftp_messages["Unable to determine your IP address."] = "Unable to determine your IP address.";
$net2ftp_messages["Unable to execute site command <b>%1\$s</b>"] = "Unable to execute site command <b>%1\$s</b>";
$net2ftp_messages["Unable to execute site command <b>%1\$s</b>. Note that the CHMOD command is only available on Unix FTP servers, not on Windows FTP servers."] = "Unable to execute site command <b>%1\$s</b>. Note that the CHMOD command is only available on Unix FTP servers, not on Windows FTP servers.";
$net2ftp_messages["Unable to execute the SQL query <b>%1\$s</b>."] = "Unable to execute the SQL query <b>%1\$s</b>.";
$net2ftp_messages["Unable to execute the SQL query."] = "Unable to execute the SQL query.";
$net2ftp_messages["Unable to extract the files and directories from the archive"] = "Unable to extract the files and directories from the archive";
$net2ftp_messages["Unable to get the archive <b>%1\$s</b> from the FTP server"] = "Unable to get the archive <b>%1\$s</b> from the FTP server";
$net2ftp_messages["Unable to get the file <b>%1\$s</b> from the FTP server and to save it as temporary file <b>%2\$s</b>.<br />Check the permissions of the %3\$s directory.<br />"] = "Unable to get the file <b>%1\$s</b> from the FTP server and to save it as temporary file <b>%2\$s</b>.<br />Check the permissions of the %3\$s directory.<br />";
$net2ftp_messages["Unable to get the list of packages"] = "Unable to get the list of packages";
$net2ftp_messages["Unable to login to FTP server <b>%1\$s</b> with username <b>%2\$s</b>.<br /><br />Are you sure your username and password are correct? Please contact your ISP helpdesk or system administrator for help.<br />"] = "Unable to login to FTP server <b>%1\$s</b> with username <b>%2\$s</b>.<br /><br />Are you sure your username and password are correct? Please contact your ISP helpdesk or system administrator for help.<br />";
$net2ftp_messages["Unable to login to SSH server <b>%1\$s</b> with username <b>%2\$s</b> (%3\$s).<br /><br />Are you sure your username and password are correct? Please contact your ISP helpdesk or system administrator for help.<br />"] = "Unable to login to SSH server <b>%1\$s</b> with username <b>%2\$s</b> (%3\$s).<br /><br />Are you sure your username and password are correct? Please contact your ISP helpdesk or system administrator for help.<br />";
$net2ftp_messages["Unable to move the directory <b>%1\$s</b>"] = "Unable to move the directory <b>%1\$s</b>";
$net2ftp_messages["Unable to move the file <b>%1\$s</b>"] = "Unable to move the file <b>%1\$s</b>";
$net2ftp_messages["Unable to move the file <b>%1\$s</b>, aborting the move"] = "Unable to move the file <b>%1\$s</b>, aborting the move";
$net2ftp_messages["Unable to move the uploaded file to the temp directory.<br /><br />The administrator of this website has to <b>chmod 777</b> the /temp directory of net2ftp."] = "Unable to move the uploaded file to the temp directory.<br /><br />The administrator of this website has to <b>chmod 777</b> the /temp directory of net2ftp.";
$net2ftp_messages["Unable to open the system log."] = "Unable to open the system log.";
$net2ftp_messages["Unable to open the template file"] = "Unable to open the template file";
$net2ftp_messages["Unable to open the temporary file. Check the permissions of the %1\$s directory."] = "Unable to open the temporary file. Check the permissions of the %1\$s directory.";
$net2ftp_messages["Unable to put the file <b>%1\$s</b> on the FTP server.<br />You may not have write permissions on the directory."] = "Unable to put the file <b>%1\$s</b> on the FTP server.<br />You may not have write permissions on the directory.";
$net2ftp_messages["Unable to read the template file"] = "Unable to read the template file";
$net2ftp_messages["Unable to read the temporary file"] = "Unable to read the temporary file";
$net2ftp_messages["Unable to rename directory or file <b>%1\$s</b> into <b>%2\$s</b>"] = "Unable to rename directory or file <b>%1\$s</b> into <b>%2\$s</b>";
$net2ftp_messages["Unable to select the MySQL database. Please check your MySQL database settings in net2ftp's configuration file settings.inc.php."] = "Unable to select the MySQL database. Please check your MySQL database settings in net2ftp's configuration file settings.inc.php.";
$net2ftp_messages["Unable to select the database <b>%1\$s</b>."] = "Unable to select the database <b>%1\$s</b>.";
$net2ftp_messages["Unable to send the file to the browser"] = "Unable to send the file to the browser";
$net2ftp_messages["Unable to switch to the passive mode on FTP server <b>%1\$s</b>."] = "Unable to switch to the passive mode on FTP server <b>%1\$s</b>.";
$net2ftp_messages["Unable to write a message to the system log."] = "Unable to write a message to the system log.";
$net2ftp_messages["Unable to write the string to the temporary file <b>%1\$s</b>.<br />Check the permissions of the %2\$s directory."] = "Unable to write the string to the temporary file <b>%1\$s</b>.<br />Check the permissions of the %2\$s directory.";
$net2ftp_messages["Unexpected state string: %1\$s. Exiting."] = "Unexpected state string: %1\$s. Exiting.";
$net2ftp_messages["Unrecognized FTP output"] = "Unrecognized FTP output";
$net2ftp_messages["Unzip"] = "Unzip";
$net2ftp_messages["Unzip archive <b>%1\$s</b> to:"] = "Unzip archive <b>%1\$s</b> to:";
$net2ftp_messages["Unzip archives"] = "Unzip archives";
$net2ftp_messages["Unzip the selected archives on the FTP server"] = "Unzip the selected archives on the FTP server";
$net2ftp_messages["Up"] = "Up";
$net2ftp_messages["Update"] = "Update";
$net2ftp_messages["Upload"] = "Upload";
$net2ftp_messages["Upload a new version of the file %1\$s and merge the changes"] = "Upload a new version of the file %1\$s and merge the changes";
$net2ftp_messages["Upload directories and files using a Java applet"] = "Upload directories and files using a Java applet";
$net2ftp_messages["Upload files and archives"] = "Upload files and archives";
$net2ftp_messages["Upload more files and archives"] = "Upload more files and archives";
$net2ftp_messages["Upload new files in directory %1\$s"] = "Upload new files in directory %1\$s";
$net2ftp_messages["Upload to directory:"] = "Upload to directory:";
$net2ftp_messages["Username"] = "Username";
$net2ftp_messages["Version information"] = "Version information";
$net2ftp_messages["View"] = "View";
$net2ftp_messages["View Macromedia ShockWave Flash movie %1\$s"] = "View Macromedia ShockWave Flash movie %1\$s";
$net2ftp_messages["View file %1\$s"] = "View file %1\$s";
$net2ftp_messages["View image %1\$s"] = "View image %1\$s";
$net2ftp_messages["View logs"] = "View logs";
$net2ftp_messages["View the file %1\$s from your HTTP web server"] = "View the file %1\$s from your HTTP web server";
$net2ftp_messages["View the highlighted source code of file %1\$s"] = "View the highlighted source code of file %1\$s";
$net2ftp_messages["WAV sound file"] = "WAV sound file";
$net2ftp_messages["Webmaster's email: "] = "Webmaster's email: ";
$net2ftp_messages["Writing some text to the file: "] = "Writing some text to the file: ";
$net2ftp_messages["Wrong username or password. Please try again."] = "Wrong username or password. Please try again.";
$net2ftp_messages["You did not enter a filename for the zipfile. Go back and enter a filename."] = "You did not enter a filename for the zipfile. Go back and enter a filename.";
$net2ftp_messages["You did not enter your Administrator username or password."] = "You did not enter your Administrator username or password.";
$net2ftp_messages["You did not provide any file to upload."] = "You did not provide any file to upload.";
$net2ftp_messages["You did not provide any text to send by email!"] = "You did not provide any text to send by email!";
$net2ftp_messages["You did not supply a From address."] = "You did not supply a From address.";
$net2ftp_messages["You did not supply a To address."] = "You did not supply a To address.";
$net2ftp_messages["You have logged out from the FTP server. To log back in, <a href=\"%1\$s\" title=\"Login page (accesskey l)\" accesskey=\"l\">follow this link</a>."] = "You have logged out from the FTP server. To log back in, <a href=\"%1\$s\" title=\"Login page (accesskey l)\" accesskey=\"l\">follow this link</a>.";
$net2ftp_messages["Your IP address (%1\$s) is in the list of banned IP addresses."] = "Your IP address (%1\$s) is in the list of banned IP addresses.";
$net2ftp_messages["Your IP address (%1\$s) is not in the list of allowed IP addresses."] = "Your IP address (%1\$s) is not in the list of allowed IP addresses.";
$net2ftp_messages["Your IP address has changed; please enter your password for FTP server <b>%1\$s</b> to continue."] = "Your IP address has changed; please enter your password for FTP server <b>%1\$s</b> to continue.";
$net2ftp_messages["Your browser does not support applets, or you have disabled applets in your browser settings."] = "Your browser does not support applets, or you have disabled applets in your browser settings.";
$net2ftp_messages["Your root directory <b>%1\$s</b> does not exist or could not be selected."] = "Your root directory <b>%1\$s</b> does not exist or could not be selected.";
$net2ftp_messages["Your session has expired; please enter your password for FTP server <b>%1\$s</b> to continue."] = "Your session has expired; please enter your password for FTP server <b>%1\$s</b> to continue.";
$net2ftp_messages["Your task was stopped"] = "Your task was stopped";
$net2ftp_messages["Zip"] = "Zip";
$net2ftp_messages["Zip archive"] = "Zip archive";
$net2ftp_messages["Zip entries"] = "Zip entries";
$net2ftp_messages["Zip the selected entries to save or email them"] = "Zip the selected entries to save or email them";
$net2ftp_messages["en"] = "en";
$net2ftp_messages["files which were last modified"] = "files which were last modified";
$net2ftp_messages["files with a filename like"] = "files with a filename like";
$net2ftp_messages["files with a size"] = "files with a size";
$net2ftp_messages["from"] = "from";
$net2ftp_messages["left"] = "left";
$net2ftp_messages["ltr"] = "ltr";
$net2ftp_messages["net2ftp has tried to determine the directory mapping between the FTP server and the web server."] = "net2ftp has tried to determine the directory mapping between the FTP server and the web server.";
$net2ftp_messages["net2ftp is free software, released under the GNU/GPL license. For more information, go to http://www.net2ftp.com."] = "net2ftp is free software, released under the GNU/GPL license. For more information, go to http://www.net2ftp.com.";
$net2ftp_messages["no - please install it!"] = "no - please install it!";
$net2ftp_messages["not OK"] = "not OK";
$net2ftp_messages["not OK. Check the permissions of the %1\$s directory"] = "not OK. Check the permissions of the %1\$s directory";
$net2ftp_messages["right"] = "right";
$net2ftp_messages["to"] = "to";
$net2ftp_messages["to:"] = "to:";
$net2ftp_messages["yes"] = "yes";

?>