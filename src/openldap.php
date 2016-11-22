<?php namespace Setiawans\OpenLDAP;
/**
 * PHP OpenLDAP
 * 
 * @author   Toriq Setiawan <toriqbagus@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  PHP openLDAP
 */
 
class OpenLDAP 
{
    /**
     * Set the connection to LDAP server.
     *
     * @param string $server
     * @param string $port
     */
    function connect($server, $port) 
    {
        $connection = ldap_connect($server, $port);  // must be a valid LDAP server! 
        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
 
        // PHP Reference says there is no control of connection status in OpenLDAP 2.x.x
        // So we'll use binding function to check connection status.
 
        return $connection;
 
    }
    
    /**
     * Set the connection to LDAP server.
     *
     * @param string $connection
     * @param string $basedn
     * @param string $basepass
     */
    function bind($connection, $basedn, $basepass)
    {
        $ldaprdn  = $basedn;    // ldap rdn or dn 
        $ldappass = $basepass;  // associated password
        $bind = ldap_bind($connection, $ldaprdn, $ldappass);
 
        if ($bind) { 
            return true;
        } 

        return false;
    }
    
    /**
     * Get data with condition.
     *
     * @param string $connection
     * @param string $searchdn
     * @param string $filter
     * @param array $attributes
     */
    function search($connection, $searchdn, $filter, $attributes = array())
    {
        $search = ldap_search($connection, $searchdn, $filter, $attributes);
 
        if ($search) {
            $info = ldap_get_entries($connection, $search);
 
            return $info;
        } 

        return false;

    }

    /**
     * Get count data.
     *
     * @param string $connection
     * @param string $searchdn
     */    
    function countData($connection, $search)
    {
        $count = ldap_count_entries($connection, $search);

        return $count;

    }
    
    /**
     * Add record to LDAP.
     *
     * @param string $connection
     * @param string $adddn
     * @param array $record
     */
    function addRecord($connection, $adddn, $record)
    {
        $addProcess = ldap_add($connection, $adddn, $record);

        if ($addProcess) {
            return true;
        } 

        return false;

    }
    
    /**
     * Edit record LDAP.
     *
     * @param string $connection
     * @param string $modifydn
     * @param array $record
     */
    function modifyRecord($connection, $modifydn, $record)
    {
        $modifyProcess = ldap_modify($connection, $modifydn, $record);
        
        if ($modifyProcess) {
            return true;
        } 

        return false;

    }
    
    /**
     * Delete record LDAP.
     *
     * @param string $connection
     * @param string $dn
     * @param boolean $recursive
     */
    function deleteRecord($connection, $dn, $recursive = false)
    {
        if ($recursive == false) { 
            return (ldap_delete($connection, $dn));

        } else {
            // Search for child entries         
            $sr = ldap_list($connection, $dn, "ObjectClass=*", array(""));
            $info = ldap_get_entries($connection, $sr);
 
            for ($i=0; $i<$info['count']; $i++) {
                // Recursive delete child entries - using myldap_delete to recursive deletion
                $result = myldap_delete($connection, $info[$i]['dn'], $recursive);
                if (!$result) {
                    // return status code if deletion fails.
                    return($result);
                }
            }
            // Delete top dn
            return(ldap_delete($connection, $dn));
        }
    }
    
    /**
     * Close connection to LDAP.
     *
     * @param string $connection
     */
    function close($connection) 
    {
        ldap_close($connection);

        return true;

    }
 
}
 
?>