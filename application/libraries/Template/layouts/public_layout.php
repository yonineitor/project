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
       
        <link rel="icon" href="/assets/img/favicon.png" sizes="32x32" type="image/png">
        
        <style type="text/css">
            [v-cloak]{ display:none; }
        </style>

       	<script type="text/javascript"> 
            APP = {
                _actions: [],
                site_url: function( url ){
                    return "<?= site_url() ?>/" + url; 
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
                }
            };
        </script>
    </head>
    <body cz-shortcut-listen="true" class="bg-light " id="public-layout" >
        <?php if( $flashAlert = \Lib\Redirect::getMessages() ) : 
            $flashAlert = (array)$flashAlert;
            $alertMessage = isset($flashAlert['message']) ? $flashAlert['message'] : '';
            $alertStatus  = isset($flashAlert['status']) ? $flashAlert['status'] : '';
            ?>
            <input type="hidden" id="alertMessage" data-status="<?= $alertStatus;?>" value="<?= $alertMessage;?>" />
        <?php endif;?>
        <?php  //$this->load->view( 'public-parts/header' );  ?>
   
        <?php  $this->load->view( $config['view'] , ['_' => $_ ]  );  ?>
       
        <?php  //$this->load->view( 'public-parts/footer' );  ?>
        <?php echo $config['js']; ?>
</body>
</html>

