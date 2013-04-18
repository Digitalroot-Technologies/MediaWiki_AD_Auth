<?php

    /* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

    /**
     * This file makes MediaWiki check the PC's logged in user for access.
     *
     * This program is free software; you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation; either version 2 of the License, or
     * (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
     * GNU General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License along
     * with this program; if not, write to the Free Software Foundation, Inc.,
     * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
     * http://www.gnu.org/copyleft/gpl.html
     *
     * @package MediaWiki
     * @subpackage Auth_AD
     * @author Nicholas Dunnaway
     * @copyright 2004-2006 php|uber.leet
     * @license http://www.gnu.org/copyleft/gpl.html
     * @CVS: $Id: Auth_AD.php,v 1.0 2006/04/24 09:18:06 nkd Exp $
     * @link http://uber.leetphp.com
     * @version $Revision: 1.0 $
     *
     */

    error_reporting(E_ALL); // Debug

    // First check if class has already been defined.
    if (!class_exists('AuthPlugin'))
    {
        /**
         * Auth Plugin
         *
         */
        require_once './includes/AuthPlugin.php';

    }

    /**
     * Handles the Authentication
     *
     * @package MediaWiki
     * @subpackage Auth_AD
     */
    class Auth_AD extends AuthPlugin
    {

    	/**
    	 * Add a user to the external authentication database.
    	 * Return true if successful.
    	 *
    	 * NOTE: We are not allowed to add users to AD from the
    	 * wiki so this always returns false.
    	 *
    	 * @param User $user
    	 * @param string $password
    	 * @return bool
    	 * @access public
    	 */
    	function addUser( $user, $password )
    	{
    		return false;
    	} // End: addUser()

    	/**
    	 * Check if a username+password pair is a valid login.
    	 * The name will be normalized to MediaWiki's requirements, so
    	 * you might need to munge it (for instance, for lowercase initial
    	 * letters).
    	 *
    	 * Note: This is really easy. It checks if the user typed in the same
    	 *       username as $_ENV['AUTH_USER'].
    	 *
    	 * @param string $username
    	 * @param string $password
    	 * @return bool
    	 * @access public
    	 */
    	function authenticate( $username, $password )
    	{

    		// Clean $username and force lowercase username.
    		$username = htmlentities(strtolower($username), ENT_QUOTES, 'ISO-8859-1');

    		// Break down NT login.
    		$Valid_Username = explode('\\', $_ENV['AUTH_USER']);
            $Valid_Username = $Valid_Username[1];

            // If the username typed in is the same as their NT login then we have a match.
            if (htmlentities(strtolower($Valid_Username), ENT_QUOTES, 'ISO-8859-1') == //<-
                htmlentities(strtolower($username), ENT_QUOTES, 'ISO-8859-1'))
            {
                return true;
            }
    		return false;
    	}

    	/**
    	 * Return true if the wiki should create a new local account automatically
    	 * when asked to login a user who doesn't exist locally but does in the
    	 * external auth database.
    	 *
    	 * If you don't automatically create accounts, you must still create
    	 * accounts in some way. It's not possible to authenticate without
    	 * a local account.
    	 *
    	 * This is just a question, and shouldn't perform any actions.
    	 *
    	 * NOTE: I have set this to true to allow the wiki to create accounts.
    	 *       Without an accout in the wiki database a user will never be
    	 *       able to login and use the wiki. I think the password does not
    	 *       matter as long as authenticate() returns true.
    	 *
    	 * @return bool
    	 * @access public
    	 */
    	function autoCreate()
    	{
    		return true;
    	}

    	/**
    	 * Check to see if external accounts can be created.
    	 * Return true if external accounts can be created.
    	 *
    	 * NOTE: We are not allowed to add users to AD from the
    	 * wiki so this always returns false.
    	 *
    	 * @return bool
    	 * @access public
    	 */
    	function canCreateAccounts()
    	{
    		return false;
    	}

    	/**
    	 * If you want to munge the case of an account name before the final
    	 * check, now is your chance.
    	 */
    	function getCanonicalName( $username )
    	{
            $username = strtolower( $username );
            return ucfirst( $username );
    	}

        /**
    	 * When creating a user account, optionally fill in preferences and such.
    	 * For instance, you might pull the email address or real name from the
    	 * external user database.
    	 *
    	 * The User object is passed by reference so it can be modified; don't
    	 * forget the & on your function declaration.
    	 *
    	 * NOTE: This guesses the email address.
    	 *
    	 * @param User $user
    	 * @access public
    	 */
    	function initUser( &$user )
    	{

    		// Break down NT login.
    		$Valid_Username = explode('\\', $_ENV['AUTH_USER']);
            $Valid_Username = $Valid_Username[1];

            $user->mEmail       = $Valid_Username . '@' . $GLOBALS['wgAD_Domain']; // Set Email Address.
            $user->mRealName    = 'I need to Update My Profile';   // Set Real Name.

    	}

    	/**
    	 * Modify options in the login template.
    	 *
    	 * NOTE: Turned off some Template stuff here. Anyone who knows where
    	 * to find all the template options please let me know. I was only able
    	 * to find a few.
    	 *
    	 * @param UserLoginTemplate $template
    	 * @access public
    	 */
    	function modifyUITemplate( &$template )
    	{
    		$template->set( 'usedomain', false ); // We do not want a domain name.
    		$template->set( 'create', false ); // Remove option to create new accounts from the wiki.
            $template->set( 'useemail', false ); // Disable the mail new password box.
    	}

    	/**
    	 * Set the domain this plugin is supposed to use when authenticating.
    	 *
    	 * NOTE: We do not use this.
    	 *
    	 * @param string $domain
    	 * @access public
    	 */
    	function setDomain( $domain )
    	{
    		$this->domain = $domain;
    	}

    	/**
    	 * Set the given password in the authentication database.
    	 * Return true if successful.
    	 *
    	 * NOTE: We do not want the user to change their password.
    	 *
    	 * @param string $password
    	 * @return bool
    	 * @access public
    	 */
    	function setPassword( $password )
    	{
    		return false;
    	}

    	/**
    	 * Return true to prevent logins that don't authenticate here from being
    	 * checked against the local database's password fields.
    	 *
    	 * This is just a question, and shouldn't perform any actions.
    	 *
    	 * Note: This forces a user to pass Authentication with the above
    	 *       function authenticate().
    	 *
    	 * @return bool
    	 * @access public
    	 */
    	function strict()
    	{
    		return true;
    	}

    	/**
    	 * When a user logs in, optionally fill in preferences and such.
    	 * For instance, you might pull the email address or real name from the
    	 * external user database.
    	 *
    	 * The User object is passed by reference so it can be modified; don't
    	 * forget the & on your function declaration.
    	 *
    	 * NOTE: Not useing right now.
    	 *
    	 * @param User $user
    	 * @access public
    	 */
    	function updateUser( &$user )
    	{
    		return true;
    	}

    	/**
    	 * Check whether there exists a user account with the given name.
    	 * The name will be normalized to MediaWiki's requirements, so
    	 * you might need to munge it (for instance, for lowercase initial
    	 * letters).
    	 *
    	 * NOTE: MediaWiki checks its database for the username. If it has
    	 *       no record of the username it then asks. "Is this really a
    	 *       valid username?" If not then MediaWiki fails Authentication.
    	 *
    	 * @param string $username
    	 * @return bool
    	 * @access public
    	 * @todo write this function.
    	 */
    	function userExists( $username )
    	{

    		// Clean $username and force lowercase username.
    		$username = htmlentities(strtolower($username), ENT_QUOTES, 'ISO-8859-1');

    		// Break down NT login.
    		$Valid_Username = explode('\\', $_ENV['AUTH_USER']);
            $Valid_Username = $Valid_Username[1];

            // print ':' . $username . ':' . $Valid_Username . ':' . $_ENV['AUTH_USER'] . ':'; // Debug

            // If the username typed in is the same as thier NT login then we have a match.
            if (htmlentities(strtolower($Valid_Username), ENT_QUOTES, 'ISO-8859-1') == //<-
                htmlentities(strtolower($username), ENT_QUOTES, 'ISO-8859-1'))
            {
                return true;
            }
            return false; // Fail

    	}

    	/**
    	 * Update user information in the external authentication database.
    	 * Return true if successful.
    	 *
    	 * @param User $user
    	 * @return bool
    	 * @access public
    	 */
    	function updateExternalDB( $user )
    	{
    		return true;
    	}

        /**
    	 * Check to see if the specific domain is a valid domain.
    	 *
    	 * NOTE: Not useing right now.
    	 *
    	 * @param string $domain
    	 * @return bool
    	 * @access public
    	 *
    	 */
    	function validDomain( $domain )
    	{
    		return true;
    	}

    }

?>
