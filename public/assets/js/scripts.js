toastr.options = {
    "closeButton": true,
    "debug": false,
    "progressBar": true,
    "preventDuplicates": false,
    "positionClass": "toast-top-center",
    "onclick": null,
    "showDuration": "400",
    "hideDuration": "1000",
    "timeOut": "7000",
    "extendedTimeOut": "3000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}

const PROJECT = function(){
    
    let vm = this;

    vm.site_url = function(url)
    {
        return APP.site_url(url);
    }

    vm.elapseTime = function(time)
    {
        let t = time || null;
        return moment(t).fromNow();
    }

    vm.timeFormat = function( time, format)
    {
        let f = format || 'MM/DD/Y hh:mm A';
        return moment(time).format(f);
    }

    vm.redirect = function( url ){
        let fullUrl = this.site_url(url); 
        window.location = fullUrl
    }
    
    vm.setSetting = function( $name, value){
        $.ajax({
            method:'POST',
            url:vm.site_url('/setting/setValue/' + $name ),
            data:{ value: value},
            success:function( response ){
                console.log("response", response);
            }
        })
    }
    
    vm.error = function( msg ){
        Swal.fire(
            'Please refresh your browser',
            msg,
            'error'
        );
    }
}

$(document).ready(function(){ 
    let alertMessage = $('#alertMessage');
    
    if(alertMessage.length)
    {
        let status  = $(alertMessage).data('status');
        let message = $(alertMessage).val();
        if( status == 0 )
        {
            Swal.fire({
                type: 'error',
                title: 'Something went wrong!',
                html: message,
                //footer: '<a href>Why do I have this issue?</a>'
            });
        }
        else
        {
            Swal.fire({
                type: 'success',
                title: message,
                //text: message,
                //footer: '<a href>Why do I have this issue?</a>'
            });
        }
    }
    
    APP.guid = function(){
        var nav = window.navigator;
        var guid = nav.mimeTypes.length;
        guid += nav.userAgent.replace(/\D+/g, '');
        guid += nav.plugins.length;
        guid += nav.productSub;
        guid += nav.platform;
        //guid += screen.height || '';
        //guid += screen.width || '';
        //guid += screen.pixelDepth || '';

        return guid;
    }
    
    APP.loading = {
        show:function( element ){
            var loadingHTML = '<div style="margin-left: 30%;">';
            loadingHTML+='<div class="uil-squares-css" style="transform:scale(0.6);">';
            loadingHTML+='<div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div></div></div>';
            $(element).css('display','block').html(loadingHTML);
        },
        hidden: function(element){
            $(element ).css('display','none');
        }
    }
    
    APP.start();
    
    $('input[type="text"]').on('focus',function(){
        this.select();
    });
    
});