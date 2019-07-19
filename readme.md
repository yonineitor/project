# CodeIgniter best practices

Proyecto de integracion con varias librerias para el framework codeigniter.

  - [HMVC Modular](#CI_HMVC_Modular_extension_13)
  - [Easy Schema database in YAML](#Schema_migration_22)
  - [Easy Model access](#Model_Access_53)
  - [User and Role manager](#User_and_role_manager_98)
  - [Bootstrap 4 and Font Awesome](#anchor_bootstrap)
  - [VUEjs](#anchor_vuejs) 

### CI HMVC Modular extension
Se utiliza esta libreria para facilitar el desarrollo, en ella ya se encuentran los modulos basicos que son:
- Login
- User
- Roles
- Profile
- Activity
- Dashbard

Para acceder a un modulo solo se agrega el slug del nombre ejemplo. (domain.test/user)

### Schema migration
Una forma facil de agregar/editar campos de la base de datos sin tener que entrar de lleno en la programación.

Acceder al sitio: domain.test/migration

user: admin

password: secret

Ruta de configuración: application/third_party/Schema/Config.php

Schema de la tabla usuarios:
```yml
user:
    id:
        type: int(8)
        unsigned: true
        auto_increment: true
        primary: true
    email:
        type: varchar
        constraint: 120
        null: false
    password:
        type: varchar(120)
        null: false
    created_at:
        type: datetime
        default: null
        null: true
```

Podrá crear o editar los archivos tipo yml que se encuentren en application/database/migration/*.yml
### Model Access
Acceder a los datos es mas facil que nunca con el Query Builder y metodos magicos que integra el PHP
Ejemplo en el modulo Product Controller 
```php
<?php
use Core\Web_Controller;
use Model\Product;
class Product_Controller extends Web_Controller{
    //url: domain.test/product/home
    function home()
    {
        $product = Product::insert([
            'name' => 'Random uniqid '.uniqid()
        ]);
        //pretty print data
        pr( $product->result() );
        
        //update product
        $product->update([
            'name' => 'my new name'
        ]);
        //print only field name
        pr($product->result('name') );
        for($i=0; $i<5 ; $i++)
        {
            Product::insert([
                'name' => 'Random uniqid '.uniqid()
            ]);
        }
        
        //print all products
        pr( Product::getAll()->result() );
        
        //delete first product
        $pr->delete();
    }
}
```
Se puede más facil?
```php
<?php
$productData = new Core\Model('product');
//print all products
pr( $productData->getAll()->result() );
```
### User and role manager modules
Con los modulos por default veras una manera mas sencilla de programar
- Usuario y Perfil
- Roles y permisos
- Actividad
- Login

De una manera sencilla puedes crear el usuario default desde domain.test/migration
Editando los archivos de instalacion > application/database/install/user_install.php

### Libs assets > Bootstrap 4 & Fontawesome
Las nuevas versiones de bootstrap y fontawesome hasta la fecha de Mayo 2019
[Bootstrap 4]
[Fontawesome]

### VUEjs

[VUEjs]