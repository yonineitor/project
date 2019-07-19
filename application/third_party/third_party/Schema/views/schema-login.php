<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Schema codeigniter</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="CI Schema">
        <meta name="author" content="Jonathan">     
        
        <meta name="robot" content="noindex, nofollow" />
        
        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://bootswatch.com/4/minty/bootstrap.min.css" >
        
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        
        <script type="text/javascript">
            $.base_url = function( url ){return '<?= $site;?>' };
        </script>
        
    </head> 

    <body cz-shortcut-listen="true" style="background-color: #404448;">
            
        <div class="container-fluid" >
            
            <div class="row" style="margin-top: 50px">
                <div class="col-md-4">
                </div>   
                <div class="col-md-4">  
                    <form
                        action="<?= $site ?>/login" 
                        class="form-horizontal jumbotron well",
                        method="post",
                        >
                                            
                        <input style="display:none;" name="input_display_none[]" type="text">
                        <input style="display:none;" name="input_display_none[]" type="password">
                            
                        <h3>Login</h3>
                        <div class="form-group">
                            <input name="user" type="text" id="User"  class="form-control" placeholder="User"  autofocus="true"> 
                        </div>  
                        <div  class="form-group"> 
                            <input name="password" type="password" class="form-control" placeholder="Password" >
                        </div>
                        <div>   
                            <button class="btn btn-primary btn-block" type="submit">Login</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-2">
                </div>
            </div>
        </div>  
    </body>


</html>
