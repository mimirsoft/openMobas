<?php
/*
 * 
    This file is part of WebPropMan
    Copyright (C) 2011, Kevin Milhoan

    WebPropMan is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    WebPropMan is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with WebPropMan.  If not, see <http://www.gnu.org/licenses/>.

   Contact MimirSoft at mimirsoft@gmail.com or www.mimirsoft.com

*
*/
class RBACException extends Exception{
    public $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
}

class RBAC
{
	private $dbh;
	private $user;
	private $rbac_user;
	private $user_session;
	
	public function __construct($dbh, $user, $rbac_user, $user_session)
	{
	
		$this->dbh = $dbh;
		$this->user = $user;
		$this->rbac_user = $rbac_user;
		$this->user_session = $user_session;
		
	}
	
public function get_all_roles($sortby)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_roles ORDER BY :1:");
    $stmt->execute($sortby);
    $users = $stmt->fetchall_assoc();
    return $users;

}

public function get_all_domains($sortby, $sortby2)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_domains ORDER BY :1:, :2:");
    $stmt->execute($sortby, $sortby2);
    $users = $stmt->fetchall_assoc();
    return $users;

}
public function get_all_permissions($sortby, $sortby2)
{
    $stmt = $this->dbh->prepare("SELECT rbac_roles_has_domain_privileges.is_allowed, 
                                  rbac_roles_has_domain_privileges.id, 
                                  rbac_roles.name AS role_name, 
                                  rbac_roles.importance AS importance, 
                                  rbac_domains.name AS domain_name, 
                                  rbac_domains.is_singular AS is_singular, 
                                  rbac_privileges.name AS privileges_name 
                             FROM rbac_roles_has_domain_privileges
                       INNER JOIN rbac_domains
                               ON rbac_domains.domain_id = rbac_roles_has_domain_privileges.domain_id 
                       INNER JOIN rbac_privileges
                               ON rbac_privileges.privilege_id = rbac_roles_has_domain_privileges.privilege_id 
                       INNER JOIN rbac_roles
                               ON rbac_roles.role_id = rbac_roles_has_domain_privileges.role_id 
                         ORDER BY :1:, :2:");
    $stmt->execute($sortby, $sortby2);
    $users = $stmt->fetchall_assoc();
    return $users;

}

public function get_all_domains_mtm_objects($sortby, $sortby2, $sortby3)
{
    $stmt = $this->dbh->prepare("SELECT rbac_domains_has_objects.id, 
                                  rbac_domains.name AS domain_name, 
                                  rbac_domains.is_singular AS is_singular, 
                                  rbac_objects.name AS object_name 
                             FROM rbac_domains_has_objects
                       INNER JOIN rbac_domains
                               ON rbac_domains.domain_id = rbac_domains_has_objects.domain_id 
                       INNER JOIN rbac_objects
                               ON rbac_objects.object_id = rbac_domains_has_objects.object_id 
                         ORDER BY :1:, :2:");
    $stmt->execute($sortby, $sortby2);
    $users = $stmt->fetchall_assoc();
    return $users;

}
public function get_all_objects($sortby)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_objects ORDER BY :1:");
    $stmt->execute($sortby);
    $users = $stmt->fetchall_assoc();
    return $users;
}
public function get_role_by_name($name)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_roles  WHERE name = :1:");
    $stmt->execute($name);
    $users = $stmt->fetch_assoc();
    return $users;
}

public function count_objects()
{
    $stmt = $this->dbh->prepare("SELECT COUNT(*) as count FROM rbac_objects");
    $stmt->execute();
    $count = $stmt->fetch_assoc();
    return $count;

}
public function count_domains()
{
    $stmt = $this->dbh->prepare("SELECT COUNT(*) as count FROM rbac_domains");
    $stmt->execute();
    $count = $stmt->fetch_assoc();
    return $count;

}
public function count_dmtmo()
{
    $stmt = $this->dbh->prepare("SELECT COUNT(*) as count FROM rbac_domains_has_objects");
    $stmt->execute();
    $count = $stmt->fetch_assoc();
    return $count;

}
public function get_object_by_ID($id)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_objects WHERE object_id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}
public function get_all_unused_objects($id)
{
    $stmt = $this->dbh->prepare("SELECT *
                             FROM rbac_objects
                         WHERE object_id NOT IN ($id)
                        ORDER BY name");
    $stmt->execute($id);
    $users = $stmt->fetchall_assoc();
    return $users;
}

public function delete_object($id)
{
    $obj = $this->get_object_by_ID($id);
    $domain = $this->get_domain_by_name($obj['name']);
    $this->delete_domain($domain['domain_id']);
    $stmt = $this->dbh->prepare("DELETE FROM rbac_objects WHERE object_id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}
public function delete_domain($id)
{
    $stmt = $this->dbh->prepare("DELETE FROM rbac_domains WHERE domain_id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}
public function delete_dmtmo($id)
{
    $stmt = $this->dbh->prepare("DELETE FROM rbac_domains_has_objects WHERE id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}
public function get_domain_by_ID($id)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_domains WHERE domain_id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}
public function get_permission_by_ID($id)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_roles_has_domain_privileges WHERE id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}
public function get_all_dmtmo_by_domain_iD($id)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_domains_has_objects WHERE domain_id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetchall_assoc();
    return $users;
}
public function get_domain_by_name($id)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_domains WHERE name = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}

public function update_object($id, $name, $desc)
{
    $stmt = $this->dbh->prepare("UPDATE rbac_objects 
                                SET name=:2:, 
                                    description=:3:
                            WHERE object_id=:1:");
    $stmt->execute($id, $name, $desc);
}
public function create_domain($id, $name, $desc, $singular)
{
    $stmt = $this->dbh->prepare("INSERT INTO  rbac_domains 
                                SET domain_id=:1:,
                                    name=:2:, 
                                    description=:3:,
                                    is_singular=:4: ");
    $stmt->execute($id, $name, $desc, $singular);
    return $stmt->dbh->insert_id;
}

public function update_domain($id, $name, $desc)
{
    $stmt = $this->dbh->prepare("UPDATE rbac_domains 
                                SET name=:2:, 
                                    description=:3:
                            WHERE domain_id=:1:");
    $stmt->execute($id, $name, $desc);
}
public function create_domain_mtm_object($id, $domain, $obj)
{
    $stmt = $this->dbh->prepare("INSERT INTO  rbac_domains_has_objects 
                                SET domain_id=:2:, 
                                    object_id=:3:,
                                    id=:1:");
    $stmt->execute($id, $domain, $obj);
    $id = $stmt->dbh->insert_id;
    RBAC::flush_domain_permissions($id);
}

public function create_object($id, $name, $desc)
{
/*Domain all_objects with id 1 already exists.
New object inserted deed with id 3
New relationship between all_objects (1) and deed (3) has been added.
New singular domain inserted deed with id 5
New singluar relationship between deed (5) and deed (3) has been added.
*/
    $object_id = $this->get_object_by_name($name);
    if($object_id)
    {
        throw new RBACException("Action already exists.");
    }
 

    
    
    //Check if domain all_objects exists
    $domain_id = $this->get_domain_by_name("all_objects");
    //if not, create it
    if(!$domain_id)
    {   
        $this->create_domain('NULL', "all_objects", "All Objects", 0);
        $domain_id = $this->get_domain_by_name("all_objects");
    }
    //create the new object
    $stmt = $this->dbh->prepare("INSERT INTO rbac_objects 
                                SET object_id=:1:, 
                                    name=:2:, 
                                    description=:3:");
    $stmt->execute($id, $name, $desc);
    $obj_id = $stmt->dbh->insert_id;
    // add the object to the domain 'all_objects'
    $this->create_domain_mtm_object('NULL', $domain_id['domain_id'], $obj_id);
    //create new singular domain for the object
    $domain_id = rbac::create_domain('NULL', $name, $desc, 1);
    $this->create_domain_mtm_object('NULL', $domain_id, $obj_id);


}


public function create_permission($id, $role, $privilege, $domain, $is_allowed)
{
    $stmt = $this->dbh->prepare("INSERT INTO  rbac_roles_has_domain_privileges 
                                SET id=:1:, 
                                    role_id=:2:,
                                    privilege_id=:3:,
                                    domain_id=:4:,
                                    is_allowed=:5:");
    $stmt->execute($id, $role, $privilege, $domain, $is_allowed);
    $this->flush_role_permissions($role);
    
}
public function update_permission($id, $role, $privilege, $domain, $is_allowed)
{
    $stmt = $this->dbh->prepare("UPDATE  rbac_roles_has_domain_privileges 
                                SET role_id=:2:,
                                    privilege_id=:3:,
                                    domain_id=:4:,
                                    is_allowed=:5:
                                WHERE id=:1:");
    $stmt->execute($id, $role, $privilege, $domain, $is_allowed);
    $this->flush_role_permissions($role);
}
public function delete_permission($id)
{
    $stmt = $this->dbh->prepare("DELETE FROM rbac_roles_has_domain_privileges WHERE id = :1:");
    $stmt->execute($id);
    $this->flush_role_permissions($role);
    return $users;
}
public function get_all_users_mtm_roles($sortby, $sortby2)
{
    $stmt = $this->dbh->prepare("SELECT *
                             FROM rbac_users_has_roles
                       INNER JOIN user_main
                               ON user_main.user_id = rbac_users_has_roles.user_id 
                       INNER JOIN rbac_roles
                               ON rbac_roles.role_id = rbac_users_has_roles.role_id 
                         ORDER BY :1:, :2:");
    $stmt->execute($sortby, $sortby2);
    $users = $stmt->fetchall_assoc();
    return $users;

}
public function get_all_users_with_role($role)
{
    $stmt = $this->dbh->prepare("SELECT *
                             FROM rbac_users_has_roles
                       INNER JOIN user_main
                               ON user_main.user_id = rbac_users_has_roles.user_id 
                       INNER JOIN rbac_roles
                               ON rbac_roles.role_id = rbac_users_has_roles.role_id 
                         WHERE rbac_users_has_roles.role_id=:1:");
    $stmt->execute($role);
    $users = $stmt->fetchall_assoc();
    return $users;

}
public function get_all_roles_with_privilege($priv)
{
    $stmt = $this->dbh->prepare("SELECT *
                             FROM rbac_roles_has_domain_privileges
                            WHERE privilege_id=:1:");
    $stmt->execute($priv);
    $users = $stmt->fetchall_assoc();
    return $users;
}
public function get_all_roles_with_domain($domain)
{
    if($domain == 'NULL')
    {
    	return;
    }
    else{
    
	$stmt = $this->dbh->prepare("SELECT *
                             FROM rbac_roles_has_domain_privileges
                                WHERE domain_id=:1:");
    $stmt->execute($domain);
    $users = $stmt->fetchall_assoc();
    return $users;
    }
}

public function create_users_mtm_roles($id, $user, $role)
{
    $stmt = $this->dbh->prepare("INSERT INTO  rbac_users_has_roles
                                SET user_id=:2:, 
                                    role_id=:3:,
                                    id=:1:");
    $stmt->execute($id, $user, $role);
    $this->flush_user_permissions($user);
}

public function update_users_mtm_roles($id, $user, $role)
{
    $stmt = $this->dbh->prepare("UPDATE rbac_users_has_roles
                              SET user_id=:2:, 
                                  role_id=:3:
                            WHERE id=:1:");
    $stmt->execute($id, $user, $role);
    $this->flush_user_permissions($user);
}

public function get_users_mtm_roles_by_ID($id)
{
    $stmt = $this->dbh->prepare("SELECT *
                             FROM rbac_users_has_roles
                       WHERE id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;

}
public function delete_users_mtm_roles_by_ID($role_id)
{
    $row = $this->get_users_mtm_roles_by_ID($role_id);
    $stmt = $this->dbh->prepare("DELETE FROM rbac_users_has_roles WHERE id = :1:");
    $stmt->execute($role_id);
    $this->flush_user_permissions($row['user_id']);
    return;
}
public function flush_user_permissions($id)
{
    $this->user_session->DeleteAllUsersSessions($id);
}
public function flush_role_permissions($role)
{
    //getall users with role $role
    $users = $this->get_all_users_with_role($role);
    //then delete all sessions for person
    foreach($users as $row)
    {
        $this->user_session->DeleteAllUsersSessions($row['user_id']);
    }
}
public function flush_privilege_permissions($priv)
{
    //getall roles with domain
    $roles = $this->get_all_roles_with_privilege($priv);
    foreach($roles as $role)
    {
        //getall users with role $role
        $users = $this->get_all_users_with_role($role['role_id']);
        //then delete all sessions for person
        foreach($users as $row)
        {
            $this->user_session->DeleteAllUsersSessions($row['user_id']);
        }
    }
}
public function flush_domain_permissions($domain)
{
    //getall roles with domain
    $roles = $this->get_all_roles_with_domain($domain);
    foreach($roles as $role)
    {
        //getall users with role $role
        $users = $this->get_all_users_with_role($role['role_id']);
        //then delete all sessions for person
        foreach($users as $row)
        {
            $this->user_session->DeleteAllUsersSessions($row['user_id']);
        }
    }
}

/*

This is the privilege action block, identicle to the object_domains block


*/

public function get_all_privileges($sortby, $sortby2)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_privileges ORDER BY :1:, :2:");
    $stmt->execute($sortby, $sortby2);
    $users = $stmt->fetchall_assoc();
    return $users;

}


public function get_all_privileges_mtm_actions($sortby, $sortby2)
{
    $stmt = $this->dbh->prepare("SELECT rbac_privileges_has_actions.id, 
                                  rbac_privileges.name AS privilege_name, 
                                  rbac_privileges.is_singular AS is_singular, 
                                  rbac_actions.name AS action_name 
                             FROM rbac_privileges_has_actions
                       INNER JOIN rbac_privileges
                               ON rbac_privileges.privilege_id = rbac_privileges_has_actions.privilege_id 
                       INNER JOIN rbac_actions
                               ON rbac_actions.action_id = rbac_privileges_has_actions.action_id 
                         ORDER BY :1:, :2:");
    $stmt->execute($sortby, $sortby2);
    $users = $stmt->fetchall_assoc();
    return $users;

}
public function get_all_actions($sortby)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_actions ORDER BY :1:");
    $stmt->execute($sortby);
    $users = $stmt->fetchall_assoc();
    return $users;
}


public function count_actions()
{
    $stmt = $this->dbh->prepare("SELECT COUNT(*) as count FROM rbac_actions");
    $stmt->execute();
    $count = $stmt->fetch_assoc();
    return $count;

}
public function count_privileges()
{
    $stmt = $this->dbh->prepare("SELECT COUNT(*) as count FROM rbac_privileges");
    $stmt->execute();
    $count = $stmt->fetch_assoc();
    return $count;

}
public function count_pmtma()
{
    $stmt = $this->dbh->prepare("SELECT COUNT(*) as count FROM rbac_privileges_has_actions");
    $stmt->execute();
    $count = $stmt->fetch_assoc();
    return $count;

}
public function get_action_by_ID($id)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_actions WHERE action_id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}
public  function delete_action($id)
{
    $obj = $this->get_object_by_ID($id);
    $domain = $this->get_domain_by_name($obj['name']);
    $this->delete_domain($domain['domain_id']);
    $stmt = $this->dbh->prepare("DELETE FROM rbac_actions WHERE action_id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}
public function delete_privilege($id)
{
    $stmt = $this->dbh->prepare("DELETE FROM rbac_privileges WHERE privilege_id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}
public function delete_pmtma($id)
{
    $stmt = $this->dbh->prepare("DELETE FROM rbac_privileges_has_actions WHERE id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}
public function get_privilege_by_ID($id)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_privileges WHERE privilege_id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}
public function get_privilege_by_name($id)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_privileges WHERE name = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}
public function get_all_unused_actions($id)
{
    $stmt = $this->dbh->prepare("SELECT *
                             FROM rbac_actions
                         WHERE action_id NOT IN ($id)
                        ORDER BY name");
    $stmt->execute($id);
    $users = $stmt->fetchall_assoc();
    return $users;
}
public function get_all_pmtma_by_privilege_iD($id)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_privileges_has_actions WHERE privilege_id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetchall_assoc();
    return $users;
}
public function update_action($id, $name, $desc)
{
    $stmt = $this->dbh->prepare("UPDATE rbac_actions
                                SET name=:2:, 
                                    description=:3:
                            WHERE action_id=:1:");
    $stmt->execute($id, $name, $desc);
}
public function create_privilege($id, $name, $desc, $singular)
{
    $stmt = $this->dbh->prepare("INSERT INTO  rbac_privileges
                                SET privilege_id=:1:,
                                    name=:2:, 
                                    description=:3:,
                                    is_singular=:4: ");
    $stmt->execute($id, $name, $desc, $singular);
    return $stmt->dbh->insert_id;
}

public function update_privilege($id, $name, $desc)
{
    $stmt = $this->dbh->prepare("UPDATE rbac_privileges
                                SET name=:2:, 
                                    description=:3:
                            WHERE privilege_id=:1:");
    $stmt->execute($id, $name, $desc);
}

public function create_privilege_mtm_action($id, $domain, $obj)
{
    $stmt = $this->dbh->prepare("INSERT INTO  rbac_privileges_has_actions
                                SET privilege_id=:2:, 
                                    action_id=:3:,
                                    id=:1:");
    $stmt->execute($id, $domain, $obj);
    $this->flush_privilege_permissions($id);
}

public function get_action_by_name($id)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_actions WHERE name = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}

public function check_privilege_mtm_action($priv, $action)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_privileges_has_actions
                            WHERE privilege_id = :1:
                              AND action_id=:2:");
    $stmt->execute($priv, $action);
    $users = $stmt->fetch_assoc();
    return $users;
}

public function check_domain_mtm_object($dom, $obj)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_domains_has_objects
                            WHERE domain_id = :1:
                              AND object_id=:2:");
    $stmt->execute($dom, $obj);
    $users = $stmt->fetch_assoc();
    return $users;
}
public function get_object_by_name($id)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_objects WHERE name = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}

public function create_action($id, $name, $desc)
{
 
    $action_id = $this->get_action_by_name($name);
    if($action_id)
    {
        throw new RBACException("Action already exists.");
    }
    
    //Check if domain all_objects exists
    $privilege_id = $this->get_privilege_by_name("all_actions");
    //if not, create it
    if(!$privilege_id)
    {   
        $this->create_privilege('NULL', "all_actions", "All Actions", 0);
        $privilege_id = $this->get_privilege_by_name("all_actions");
    }
    //create the new object
    $stmt = $this->dbh->prepare("INSERT INTO rbac_actions
                                SET action_id=:1:, 
                                    name=:2:, 
                                    description=:3:");
    $stmt->execute($id, $name, $desc);
    $action_id = $stmt->dbh->insert_id;
    // add the object to the domain 'all_objects'
    $this->create_privilege_mtm_action('NULL', $privilege_id['privilege_id'], $action_id);
    //create new singular domain for the object
    $domain_id = $this->create_privilege('NULL', $name, $desc, 1);
    $this->create_privilege_mtm_action('NULL', $domain_id, $action_id);


}

public function create_roles($id, $name, $desc, $imp)
{

/*Domain all_objects with id 1 already exists.
New object inserted deed with id 3
New relationship between all_objects (1) and deed (3) has been added.
New singular domain inserted deed with id 5
New singluar relationship between deed (5) and deed (3) has been added.
*/
    $stmt = $this->dbh->prepare("INSERT INTO rbac_roles
                                SET role_id=:1:, 
                                    name=:2:, 
                                   description=:3:,
                                   importance=:4:");
    $stmt->execute($id, $name, $desc, $imp);
    return $stmt->dbh->insert_id;
}

public function get_role_by_ID($id)
{
    $stmt = $this->dbh->prepare("SELECT * FROM rbac_roles WHERE role_id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}
public function update_role($id, $name, $desc, $imp)
{
    $stmt = $this->dbh->prepare("UPDATE rbac_roles
                                SET name=:2:, 
                                    description=:3:,
                                    importance=:4:
                            WHERE role_id=:1:");
    $stmt->execute($id, $name, $desc, $imp);
}
public function delete_role($id)
{
    $stmt = $this->dbh->prepare("DELETE FROM rbac_roles WHERE role_id = :1:");
    $stmt->execute($id);
    $users = $stmt->fetch_assoc();
    return $users;
}
public function verify_rbac($module)
{
	include("../$module/{$module}_rbac.php");
	foreach($actions as $value)
	{
		try{
			$this->create_action("NULL", $value, $action_description[$value]);
		}
		catch(RBACException $e)
		{
			echo $e->message;
		}
	}
	foreach($objects as $value)
	{
		try{
			$this->create_object("NULL", $value, $object_description[$value]);
		}
		catch(RBACException $e)
		{
			echo $e->message;
		}
	}
	if (is_array($privileges)) {
	foreach($privileges as $key=>$priv_array)
	{
		//Check if privilege exists
		$privilege_id = $this->get_privilege_by_name($key);
		//if not, create it
		if(!$privilege_id)
		{
			$privilege_id = $this->create_privilege('NULL', $key, $key, 0);
			$privilege_id = $this->get_privilege_by_name($key);

		}
		foreach($priv_array as $value)
		{
			$id = $this->get_action_by_name($value);
			$check = $this->check_privilege_mtm_action($privilege_id['privilege_id'], $id['action_id']);
			if(!$check)
			{
				$this->create_privilege_mtm_action("NULL", $privilege_id['privilege_id'], $id['action_id']);
			}

		}

	}
	}
	if(is_array($domains)) {
	foreach($domains as $key=>$dom_array)
	{
		//Check if privilege exists
		$domain_id = $this->get_domain_by_name($key);
		//if not, create it
		if(!$domain_id)
		{
			$domain_id = $this->create_domain("NULL", $key, $key, 0);
			$domain_id = $this->get_domain_by_name("all_objects");
		}
		foreach($dom_array as $value)
		{
			$id = $this->get_object_by_name($value);
			$check = $this->check_domain_mtm_object($domain_id['domain_id'], $id['object_id']);
			if(!$check)
			{
				$this->create_domain_mtm_object("NULL", $domain_id['domain_id'], $id['object_id']);
			}

		}
	}
	}
	
}
public function cascade_session_permissions()
{
	$stmt = $this->dbh->prepare("UPDATE user_session SET session_data='';");
	$stmt->execute();
	//echo $stmt->query;
	unset($_SESSION['rbac_bv']);
	/*    $dbh = new DB_Mysql();
	 $stmt = $dbh->prepare("SELECT session_id, session_data FROM user_session");
	$stmt->execute();
	//get all sessions
	// for each sesions, pull the session data, delete the stored permissions, restore the data
	while($row = $stmt->fetch_assoc())
	{
	echo $row['session_data'];
	$session = unserialize($row['session_data']);
	print_r($session);
	unset($session['rbac_bv']);
	$sess_data = serialize($session);
	$stmt = $dbh->prepare("UPDATE user_session SET session_data = '".mysql_real_escape_string($sess_data)."' WHERE session_id = ".$row['session_id']);
	}
	*/

}

}
?>
