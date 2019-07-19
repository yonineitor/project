# Schema for Codeigniter-MYSQL : Migrations

This is not:
  - Create/Drop database
  - Drop tables
  - Drop columns

Works for:
  - Create tables
  - Modify tables
  - Alter columns

# Installation

Download project on **APPPATH .'third_party/**

Create **application/controllers/Schema.php** and extends Schema_Controller.php
```php
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH . 'third_party/Schema/src/Schema_Controller.php';

class Schema extends \ThirdParty\Schema_Controller
{
	/**
	 * Functions available from Schema_Controller
	 */
	//index
	//login
	//dashboard
	//runmigration
	//logout
}
```

Create file in your **basepath/database/user.yml** this containts your first table
```yaml
user:
    id:
        type: int(8)
        unsigned: true
        auto_increment: true,
        primary: true
    email:
        type: varchar
        constraint: 120
        null: false
    password:
        type: varchar(120)
        null: false
    name:
        type: varchar(120)
        null: false
        default: ''
    company_id:
        type: int(8)
        index: true
        default: 0
        null: false
    comments:
        type: text
        null: true
    status:
        type: smallint(1)
        null: true
    gender:
        type: enum
        constraint: 'MALE,FEMALE'
    created_at:
        type: datetime
        default: '0000-00-00'
        null: true
```
Access to your url *base_url(/schema)* and login with user **admin** and password **secret**

> If you wants to change URL, Users, Tables, Etc. please check Schema/Config.php file

