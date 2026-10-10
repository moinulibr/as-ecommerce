$(document).ready(function(){
    $(".via_card2").click(function(){
        $(".via_card_box").slideDown("fast");
        $(".paypal_box").slideUp("fast");
    });
    $("#debitorcredit").click(function(){
        $(".via_card_box").slideDown("fast");
        $(".paypal_box").slideUp("fast");
    });
    $(".via_paypal").click(function(){
        $(".via_card_box").slideUp("fast");
        $(".paypal_box").slideDown("fast");
    });
    $(".case_on_delivery2").click(function(){
        $(".via_card_box").slideUp("fast");
        $(".paypal_box").slideUp("fast");
    });
    $(".cuppon").click(function(){
        $(".cuppon-input").slideToggle("fast");
    })
});


/*$(document).on('submit','form#checkout_form', function(e) {
    e.preventDefault(); 
    $('span.textdanger').text('');

    let ele=$('form#checkout_form');
    var url=ele.attr('action');
    var method=ele.attr('method');
    var formData = ele.serialize();

    $.ajax({
        type: method,
        url: url,
        data: formData,
        success: function(res) {
            if(res.success==true){
                toastr.success(res.msg);
                if(res.url){
                    document.location.href = res.url;
                }else{
                    window.location.reload();
                }
                
            }else if(res.success==false){
                toastr.error(res.msg);
            }
            
        },
        error:function (response){
            if(response.status==401){
                window.location.href = '/user-login';
            }

            $.each(response.responseJSON.errors, function(field_name, errorArray) {
                // 1. Get the first error message string
                let errorMessage = errorArray[0]; 
                
                // 2. Find the error element (Assumes HTML has class="error-inputname")
                let errorContainer = $(document).find('.error-' + field_name);
                
                // 3. Inject the Tailwind-styled error message
                errorContainer.html(`<span class="text-sm text-red-600 font-medium textdanger">${errorMessage}</span>`);
                
                // 4. Trigger the Toastr alert
                toastr.error(errorMessage);
            });

        }
    });
});*/
$(document).on('submit', 'form#checkout_form', function(e) {
    e.preventDefault(); 
    $('span.textdanger').text('');

    let ele = $(this);
    var url = ele.attr('action');
    var method = ele.attr('method');
    var formData = ele.serialize();

    // Elements for UI Loading Feedback
    let submitBtn = ele.find('button[type="submit"]');
    let cartIcon = $('#btn_cart_icon');
    let spinner = $('#btn_spinner');
    let btnText = $('#btn_text');

    // Preserve original text in case of error
    let originalText = btnText.html();

    // Start Loading State UI
    submitBtn.prop('disabled', true);
    cartIcon.addClass('hidden');
    spinner.removeClass('hidden');
    btnText.text('Processing Order... Please Wait');

    $.ajax({
        type: method,
        url: url,
        data: formData,
        success: function(res) {
            if (res.success == true) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(res.msg || 'Order Placed Successfully!');
                }
                
                // Keep loading UI visible until redirection completes
                btnText.text('Redirecting...');
                
                if (res.url) {
                    document.location.href = res.url;
                } else {
                    window.location.reload();
                }
            } else {
                // Reset Button State on Error
                submitBtn.prop('disabled', false);
                cartIcon.removeClass('hidden');
                spinner.addClass('hidden');
                btnText.html(originalText);

                if (typeof toastr !== 'undefined') {
                    toastr.error(res.msg || 'Something went wrong!');
                }
            }
        },
        error: function(response) {
            // Reset Button State on Exception/Validation Error
            submitBtn.prop('disabled', false);
            cartIcon.removeClass('hidden');
                spinner.addClass('hidden');
            btnText.html(originalText);

            if (response.status == 401) {
                window.location.href = '/user-login';
                return;
            }

            if (response.responseJSON && response.responseJSON.errors) {
                $.each(response.responseJSON.errors, function(field_name, errorArray) {
                    let errorMessage = errorArray[0]; 
                    let errorContainer = $(document).find('.error-' + field_name);
                    
                    if (errorContainer.length > 0) {
                        errorContainer.html(`<span class="text-sm text-red-600 font-medium textdanger">${errorMessage}</span>`);
                    }

                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    }
                });
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Server error occurred. Please try again.');
                }
            }
        }
    });
});



$(document).on('submit','form#ajax_form', function(e) {
    e.preventDefault(); 
    $('span.textdanger').text('');
    var url=$(this).attr('action');
    var method=$(this).attr('method');
    var formData = new FormData($(this)[0]);
    
    let button=$(this).find('[type="submit"]');
    button.attr("disabled", "disabled");
    $.ajax({
        type: method,
        url: url,
        data: formData,
        async: false,
        processData: false,
        contentType: false,
        success: function(res) {
            
            if(res.status==true){

                swal(res.msg);
                $('div#common_modal').modal('hide');
                if(res.function){
                    jQuery("input#search").change();
                }else if(res.url){
                    setTimeout(function() { 
                        document.location.href = res.url;
                    }, 2000);
                    
                }else{
                    setTimeout(function() { 
                        window.location.reload();
                    }, 2000);
                    
                }
            }else if(res.status==false){
                swal(res.msg);
            }
            
        },
        error:function (response){
            if(response.status==401){
                window.location.href = '/user-login';
            }
            button.removeAttr("disabled");
            $.each(response.responseJSON.errors,function(field_name,error){
                $(document).find('[name='+field_name+']').after('<span class="textdanger" style="color:red">' +error+ '</span>')
              
            })
        }
    });
});


$(document).on('submit','form#review_form', function(e) {
    e.preventDefault(); 
    $('span.textdanger').text('');
    var url=$(this).attr('action');
    var method=$(this).attr('method');
    var formData = new FormData($(this)[0]);
    
    let button=$(this).find('[type="submit"]');
    button.attr("disabled", "disabled");
    $.ajax({
        type: method,
        url: url,
        data: formData,
        async: false,
        processData: false,
        contentType: false,
        success: function(res) {
            
            if(res.status==true){

                if(res.view){
                    $('.review_list').html(res.view);
                }
                swal(res.msg);
                if(res.url){
                    setTimeout(function() { 
                        document.location.href = res.url;
                    }, 2000);
                    
                }else{
                    setTimeout(function() { 
                        window.location.reload();
                    }, 2000);
                    
                }
            }else if(res.status==false){
                swal(res.msg);
            }
            
        },
        error:function (response){
            if(response.status==401){
                
                window.location.href =
                    '/login?redirect=' + encodeURIComponent(window.location.href);
                
    
            }
            button.removeAttr("disabled");
            $.each(response.responseJSON.errors,function(field_name,error){
                $(document).find('[name='+field_name+']').after('<span class="textdanger" style="color:red">' +error+ '</span>')
              
            })
        }
    });
});



/*$(document).on('click','button#coupon_apply', function(e) {
    e.preventDefault(); 
    $('span.textdanger').text('');

    var url=$(this).data('url');
    var code=$(document).find('input#coupon_code').val();
    var method='GET';
    
    if(code.length>0){
        $.ajax({
            type: method,
            url: url,
            data: {code},
            success: function(res) {
                if(res.success){
                    toastr.success(res.msg);
                    //window.location.reload();
                }
                else if(res.success===false){
                    toastr.error(res.msg);
                }
                
            },
            error:function (response){
                $.each(response.responseJSON.errors,function(field_name,error){
                    $(document).find('[name='+field_name+']').after('<span class="textdanger" style="color:red">' +error+ '</span>');
                })
            }
        });
    }
    
});

$(document).on('click','button#coupon_apply', function(e) {
    e.preventDefault(); 

    $('span.textdanger').text('');

    var url=$(this).data('href');
    var code=$(document).find('input#coupon_code').val();
    var method='GET';
    
    if(code.length>0){
        $.ajax({
            type: method,
            url: url,
            data: {code},
            success: function(res) {
                if(res.success){
                    toastr.success(res.msg);
                    //window.location.reload();
                }
                else if(res.success===false){
                    toastr.error(res.msg);
                }
                
            },
            error:function (response){
                $.each(response.responseJSON.errors,function(field_name,error){
                    $(document).find('[name='+field_name+']').after('<span class="textdanger" style="color:red">' +error+ '</span>');
                })
            }
        });
    }
    
});

$(document).on('click','button#coupon_apply', function(e) {
    e.preventDefault(); 
    $('span.textdanger').text('');

    var url=$(this).data('url');
    var code=$(document).find('input#coupon_code').val();
    var method='GET';
    
    if(code.length>0){
        $.ajax({
            type: method,
            url: url,
            data: {code},
            success: function(res) {
                if(res.success){
                    toastr.success(res.msg);
                    //window.location.reload();
                }
                else if(res.success===false){
                    toastr.error(res.msg);
                }
                
            },
            error:function (response){
                $.each(response.responseJSON.errors,function(field_name,error){
                    $(document).find('[name='+field_name+']').after('<span class="textdanger" style="color:red">' +error+ '</span>');
                })
            }
        });
    }
    
});
*/