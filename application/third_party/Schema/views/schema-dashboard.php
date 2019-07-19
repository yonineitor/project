<!DOCTYPE html>
<html lang="es">
    <head>
        
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="" />
        <meta name="author" content="Jonathan" />     
        
        <title>Schema codeigniter</title>


        <style type="text/css">
            <?php  include_once __DIR__ .'/css.php'; ?>
        </style>
        
        <script type="text/javascript">
            <?php include_once __DIR__ .'/js.php'; ?>
        </script>
        
        <script type="text/javascript">
            $.base_url = function( url ){return '<?= $site;?>' + url };
        </script>
    </head> 

    <body cz-shortcut-listen="true" >
        
        <nav class="navbar navbar-light fixed-top bg-light flex-md-nowrap p-0 shadow">
            <a class="navbar-brand col-sm-3 col-md-2 mr-0" data-target="#help" data-toggle="modal" href="#">YML Example</a>
            <ul class="navbar-nav px-3">
                <li class="nav-item text-nowrap">
                    <a class="nav-link" href="<?= $site;?>/logout" >Sign out</a>
                </li>
            </ul>
        </nav>
        <br>
        <div class="container-fluid mt-5"> 
            <div class="row">
                <div class="col-lg-4">
                    
                    <button type="button" id="migrate-all" class="btn btn-primary btn-sm float-right">Migrate all</button>
                    <h4>New Documents</h4>

                    <table class="table table-hover table-small" >
                        <thead class="bg-dark text-light">
                            <tr>
                                <th >File name</th>
                                <th class="text-right" >
                                    
                                </th>
                            </tr>
                        </thead>
                        <tbody>     
                            <?php foreach ($list_schemas as $name => $file) : ?>
                                <tr>
                                    <td><?= $name ?></td>
                                    <td class="text-right"> <button type="button" class="btn btn-success btn-sm migrate-schema new-schema" data-name="<?= $name?>" title="generate migration schema"> 
                                        migrate 
                                    </button></td>
                                </tr>   
                            <?php  endforeach; ?>
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-right text-danger">
                                    <?= count($list_schemas)?> Documents
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <br>
                    <button type="button" id="install-data" class="btn btn-primary btn-sm float-right">Install</button>
                    <h4>Install/Seeds</h4>
                    <table class="table table-hover table-small">
                        <thead class="bg-dark text-light">
                            <tr>
                                <th >File Name</th>
                                <th class="text-right" >
                                    
                                </th>
                            </tr>
                        </thead>
                        <tbody>     
                            <?php foreach ($install_files as $name => $file) : ?>
                                <tr>
                                    <td colspan="2"><?= $name ?></td>
                                </tr>
                            <?php  endforeach; ?>
                        </tbody>
                         <tfoot>
                            <tr>
                                <td colspan="2" class="text-right text-danger">
                                    <?= count($install_files)?> Documents
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <h4>Remember Back up database (ssh)</h4>
                    <div class="bg-danger text-light p-2" >
                        mysqldump -h localhost -u  <?= $username?> -p <?= $database?> --single-transaction --quick --lock-tables=false > db_<?= $database?>_$(date +%F).sql
                    </div>
                </div>
                <!-- -->
                <div class="col-lg-8">
                    <button type="button" id="migrate-selected" disabled="true" class="btn btn-primary btn-sm float-right">Migrate selected</button>
                    <h4>Files Migrated</h4>
                    <table class="table table-hover table-small" >
                        <thead  class="bg-dark text-light">
                            <tr>
                                <th>
                                    <div class="custom-control custom-checkbox">
                                        <input   type="checkbox" class="custom-control-input " id="select-all">
                                        <label class="custom-control-label" for="select-all">All</label>
                                    </div>      
                                </th>
                                <th>File Name</th>
                                <th>Time</th>
                                <th>User</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="add-new-schema">
                            <?php   
                                
                                foreach ($list_schemas_migrated as $key => $value) : 
                                    if( isset($last_modify[$value->name])){
                                        $class= ($last_modify[$value->name]!=$value->last_modify) ? ' btn-danger' : 'btn-warning ';    
                                    }else{
                                        $class= 'btn-default';
                                    }

                            ?>
                                <tr>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input name="customCheck[]" title="generate migration schema" data-remigrate="1" data-name="<?= $value->name?>" type="checkbox" class="custom-control-input" id="customCheck<?= $value->name?>">
                                            <label class="custom-control-label" for="customCheck<?= $value->name?>"></label>
                                        </div>                                    
                                    </td>
                                    <td> <label for="customCheck<?= $value->name?>"><?= $value->name?></label></td>
                                    <td><span id="date-<?= $value->name ?>"><?= time_elapsed_string($value->date) ?></span></td>
                                    <td><?= $value->user?></td>
                                    <td> <button type="button" class="btn <?= $class?> btn-sm migrate-schema" data-remigrate="1" data-name="<?= $value->name?>" title="generate migration schema"> 
                                        re-migrate
                                    </button></td>
                                </tr>
                            <?php  endforeach; ?>
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-right text-danger">
                                    <?= count($list_schemas_migrated)?> Documents
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <?php include_once __DIR__ . '/modal-example.php';?>
        <?php include_once __DIR__ . '/modal-messages.php';?>
        
    </body>
    
    <script >   
        $(document).ready(function(){   

            $("#migrate-all").on("click", function(){
                $(".migrate-schema.new-schema").each(function(){
                    runSchema( this );  
                });
            });
            
            $("body").on( "click", "#install-data", function() {
                runInstall( this );
            });
            
            $("body").on( "click", ".migrate-schema", function() {
                runSchema( this );
            });

            var runSchema = function( element ){
                var url   = $.base_url('/runmigration/'),
                remigrate = $(element).data("remigrate"),
                data      = {
                    name : $(element).data("name")
                };
                
                if($(element).hasClass('btn-default')){
                    alert('This file not found');
                    return false;
                }  
                /*
                if(remigrate){
                    if(!confirm('Are you sure remigrate this file?')){
                        return false;
                    }   
                }
                */

                $(element).attr("disabled", "disabled");
                
                $.get(url, data, function(response){
                    
                    $(".modal-title").html("File: <b>" + data.name+".yml</b>");
                    $("#activeError,#activeSuccess").css('display','none');
                    
                    if(response.status){        
                       
                        if(remigrate){
                            $("#date-"+response.schema_log.name).html(response.schema_log.date);
                        } else{     

                            var htmlCheckBox = '<div class="custom-control custom-checkbox">';
                            htmlCheckBox+='<input name="customCheck[]" value="'+response.schema_log.name+'" type="checkbox" class="custom-control-input" id="customCheck'+response.schema_log.name+'"><label class="custom-control-label" for="customCheck'+response.schema_log.name+'"></label></div>';

                            var item = '<tr>';
                            item+= '<td>'+htmlCheckBox+'</td>';
                            item+= '<td> <label for="">'+response.schema_log.name+'</label></td>';
                            item+= '<td> <span id="date-'+response.schema_log.name+'">'+response.schema_log.date+'</span></td>';
                            item+= '<td>'+response.schema_log.user+'</td>';
                            item+= '<td> <button type="button" class="btn btn-warning btn-xs migrate-schema" data-remigrate="1" data-name="'+response.schema_log.name+'" title="generate migration schema"> re-migrate <i class="glyphicon glyphicon-record"></i></button></td>';
                            item+= '</tr>'; 
                            $("#add-new-schema").prepend(item);
                            $($(element).parent().parent()).remove();
                            //$(element).remove();
                        } 

                        $("#activeSuccess").css('display','block');
                        $("#msg-success").html(response.message_success);
                  
                    } else{     
                        if(response.message_success!=''){
                            $("#activeSuccess").css('display','block');
                        }
                        $("#activeError").css('display','block');
                        $("#msg-error").html(response.msg);
                    }  

                    $(element).removeAttr("disabled");

                    $("#messages").modal();

                }, 'json');

               
                $(this).removeAttr("disabled");  
            }

            var runInstall = function(element){
                var url   = $.base_url('/runinstall/');
                
                $(element).attr("disabled", "disabled");
                
                $.get(url, null, function(response){

                    $(".modal-title").html("Install all data");
                    $("#activeError,#activeSuccess").css('display','none');
                    if(response.status){        
                        $("#activeSuccess").css('display','block');
                        $("#msg-success").html(response.message);
                  
                    } else{     
                        $("#activeError").css('display','block');
                        $("#msg-error").html(response.message);
                    } 
                    console.log(response);
                    
                    $(element).removeAttr("disabled");  

                    $("#messages").modal();

                }, 'json');

                $(element).removeAttr("disabled");  
               
            }
            
            $('#migrate-selected').on('click', function(){
                $('[name="customCheck[]"]:checked').each(function(){
                    runSchema( this );
                });
            });

            $('#select-all').on('change', function(){
                check = $(this).is(':checked');    
                $('[name="customCheck[]"]').prop('checked', check);
                $('#migrate-selected').attr('disabled', !check);

            });
            
            $('[name="customCheck[]"]').on('change',function(){
                var checks = $('[name="customCheck[]"]:checked').length;
                var disabled = checks ? false : true;
                $('#migrate-selected').attr('disabled', disabled );
            });
        });
    </script>
<html> 
<?php
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>