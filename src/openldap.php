<?php 
/**
 * PHP OpenLDAP
 * 
 * @author   Toriq Setiawan <toriqbagus@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  PHP openLDAP
 */
namespace Setiawans\OpenLDAP;

use Log;
 
class OpenLDAP 
{
    private $connection;

    public function __construct()
    {
        $host = config('ldap.host');
        $port = config('ldap.port');

        $this->connection = $this->connect($host, $port);
    }

    public function __destruct()
    {
        if (! is_null($this->connection))
            ldap_unbind($this->connection);
    }

    /**
     * Set the connection to LDAP server.
     *
     * @param string $host
     * @param string $port
     */
    function connect($host, $port) 
    {
        $connection = ldap_connect($host, $port);  // must be a valid LDAP server! 
        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
 
        // PHP Reference says there is no control of connection status in OpenLDAP 2.x.x
        // So we'll use binding function to check connection status.
        return $connection;
 
    }

    /**
     * Authenticate to LDAP server.
     *
     * @param string $username
     * @param string $password
     */
    public function authenticate($username, $password)
    {
        if (empty($username) or empty($password)) {
            Log::error('Error binding to LDAP: username or password empty');
            return false;
        }

        $ldapRdn = config('ldap.login_attribute') . "=" . $username . "," . config('ldap.basedn');
        $isConnected = $this->bind($this->connection, $ldapRdn, $password);

        return $isConnected;
    }
    
    /**
     * Set the connection to LDAP server.
     *
     * @param string $connection
     * @param string $ldaprdn
     * @param string $password
     */
    function bind($connection, $ldaprdn, $password)
    {
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
    public function search($searchdn, $filter, $attributes = array())
    {
        $search = ldap_search($this->connection, $searchdn, $filter, $attributes);
 
        if ($search) {
            $info = ldap_get_entries($this->connection, $search);
 
            return $info;
        } 

        return false;

    }

    /**
     * Get count data.
     *
     * @param string $searchdn
     */    
    public function countData($search)
    {
        $count = ldap_count_entries($this->connection, $search);

        return $count;

    }
    
    /**
     * Add record to LDAP.
     *
     * @param string $connection
     * @param string $adddn
     * @param array $record
     */
    public function addRecord($connection, $adddn, $record)
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
    public function modifyRecord($connection, $modifydn, $record)
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
    public function deleteRecord($connection, $dn, $recursive = false)
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
    public function close($connection) 
    {
        ldap_close($connection);

        return true;
    }

    /**
     * Get user data from LDAP server.
     *
     * @param string $identifier
     * @param string $attr
     */
    public function getUserData($identifier, $attr='')
    {
        $ldapFilter = "(&(" . config('ldap.login_attribute') . "=". $identifier . "))";
        if (!is_array($attr)) {
            $attr = array();
        }

        $userInfo = $this->search($this->connection, config('ldap.basedn'), $ldapFilter, $attr);
    
        if ($userInfo) {
            return $userInfo[0];
        } else {
            return false;
        }
    }

    /**
     * Get group list from LDAP server.
     */
    public function getGroupList()
    {
        $ldapFilter = "(cn=*)";
        $attr = array("cn", "gidNumber");
        $info = $this->search($this->connection, config('ldap.groupdn'), $ldapFilter, $attr);

        $groupList = array();
        foreach ($info as $each)
        {
            if (!empty($each["cn"][0]))
                $groupList[] = $each["cn"][0];
        }

        return $groupList;
    }

    /**
     * Get which group from user.
     */
    public function whichGroup($identifier)
    {
        $gidnumber = strval($this->getUserData($identifier)['gidnumber'][0]);

        return $this->groupList[$gidnumber];
    }

    public function groupIsOK()
    {
        return false;
    }
 
}
 
?>