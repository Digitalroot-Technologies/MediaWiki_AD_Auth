MediaWiki_AD_Auth
=================

MediaWiki authentication plug-in. This plug-in reads the username off the NT Login. This plug-in is used for active directory authentication for MediaWiki.


How it works: 
This plug-in checks the username of the user logged into the PC. All the user needs to do is login 
to the Wiki using their NT (Windows/Network) username. Leave the password blank. The plug-in does not 
even check the password. 

If the username given matches the username of the current computer then the user passes authentication. 

Requirements:
=================

        Network Domain, Active Directory, IIS 5 or 6.

        Note: You might be able to get this to work with other setups but above is what I used to build it.

INSTALL:
=================

Your wiki dir needs to be protected using NTFS permissions. No anon access. I will create a walk-through on 
http://uber.leetphp.com with more info on how to setup this plug-in. 

Put Auth_AD.php in /extensions/

Open LocalSettings.php. Put this at the bottom of the file. Edit as needed.
 
 /*-----------------[ Everything below this line. ]-----------------*/
 
 // This requires a user be logged into the wiki to make changes.
 //$wgWhitelistEdit = true; // MediaWiki 1.4 Settings
 $wgGroupPermissions['*']['edit'] = false; // MediaWiki 1.5 or 1.6 Settings
 
 // Specify who may create new accounts: 0 means no, 1 means yes
 //$wgWhitelistAccount = array ( 'user' => 0, 'sysop' => 1, 'developer' => 1 ); // MediaWiki 1.4 Settings
 $wgGroupPermissions['*']['createaccount'] = false; // MediaWiki 1.5 or 1.6 Settings
 
 // AD User Database Plugin.
 require_once './extensions/Auth_AD.php';
 
 
 $wgAD_Domain = 'example.com';	// Name of your company's domain
                                // i.e. If your work email is
                                // myname@example.com.
                                // set this to example.com
 
 $wgAuth = new Auth_AD();	// Auth_AD
