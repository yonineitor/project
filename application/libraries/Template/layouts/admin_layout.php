<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
		
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        
        <meta name="robot" content="noindex, nofollow" />
        
        <title><?php echo $config['title'] ?></title>
        <?php echo $config['css']; ?>
        
        <link rel="shortcut icon" href="/assets/img/favicon.ico" sizes="16x16" type="image/x-icon">
        <link rel="icon" href="/assets/img/favicon.ico" sizes="16x16" type="image/x-icon">
        
       	<script type="text/javascript"> 
            const domain = "<?= site_url() ?>";
            APP = {
                _actions: [],
                privileges: null,
                site_url: function( url ){
                    return domain + url;
                },
                postLoad: function( $callBack ){
                    if($callBack && {}.toString.call($callBack) === '[object Function]')
                        this._actions.push($callBack);
                },
                start: function(){
                    for( var i = 0; i< this._actions.length; i++)
                    {
                        this._actions[i]( this, i );
                    }
                },
                getUser: function(){
                    return <?php echo json_encode(\Lib\Auth::user()); ?>
                },
            };
            
            APP.privileges = <?php echo json_encode(\Model\Roles\Privileges::userPrivileges());?>;
        </script>
    </head>
    <body id="page-top" >
        <?php if( $flashAlert = \Lib\Redirect::getMessages() ) : 
            $flashAlert = (array)$flashAlert;
            $alertMessage = isset($flashAlert['message']) ? $flashAlert['message'] : '';
            $alertStatus  = isset($flashAlert['status']) ? $flashAlert['status'] : '';
            ?>
            <input type="hidden" id="alertMessage" data-status="<?= $alertStatus;?>" value="<?= $alertMessage;?>" />
        <?php endif;?>
        <nav class="navbar navbar-expand navbar-dark bg-dark static-top">
            <a class="navbar-brand mr-1" href="<?= site_url('/')?>">Project</a>
            <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Navbar Search -->
            <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="<?= _l('Search for...')?>" aria-label="Search" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>

            <ul class="navbar-nav ml-auto ml-md-0">
                <li class="nav-item dropdown no-arrow mx-1">
                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-sliders-h"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="alertsDropdown">
                        <?php if(priv('priv_setting_user_view') ): ?>
                        <a class="dropdown-item" href="<?= site_url('user')?>">User manager</a>
                        <?php endif; ?>
                        <?php if(priv('priv_setting_roles_manager') ): ?>
                        <a class="dropdown-item" href="<?= site_url('roles')?>">Role manager</a>
                        <?php endif;?>
                    </div>
                </li>
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Full name <i class="fas fa-user-circle fa-fw"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="<?= site_url('profile');?>"><?= _l('Profile');?></a>
                        <a class="dropdown-item" href="<?= site_url('profile/logOut');?>"><?= _l('Logout');?></a>
                    </div>
                </li>
            </ul>
        </nav>
        
        <div id="wrapper">
            <?php $this->load->view( 'admin-parts/section-menu' ); ?>
            <div id="content-wrapper">
                <div class="container-fluid">
                    <?php  $this->load->view( $config['view'] , ['_' => $_ ]  );  ?>
                </div>
            </div>
        </div>
		<?php echo $config['js']; ?>
    </body>
</html>
