
$(document).ready(function(){

    checkCheckout();
});

// Update page elements from cart AJAX response
function applyCartResponseUpdates(res){
    if (!res) return;
    if (res.html) {
        $('div#cartModal').html(res.html);
    }
    if (typeof res.item !== 'undefined') {
        $(document).find('span.cart-count').text(res.item);
        if ($(document).find('#itemsCountNumber').length) {
            $(document).find('#itemsCountNumber').text(res.item);
        }
        if ($(document).find('#orderItemsCount').length) {
            $(document).find('#orderItemsCount').text(res.item);
        }
    }
    // extract total from returned cart html
    if (res.html) {
        try {
            var tmp = document.createElement('div');
            tmp.innerHTML = res.html;
            var cartTotal = tmp.querySelector('#cartTotal');
            if (cartTotal) {
                var totalText = cartTotal.textContent.trim();
                if ($(document).find('#pageTotal').length) {
                    $(document).find('#pageTotal').text(totalText);
                }
                if ($(document).find('#orderItemsAmount').length) {
                    $(document).find('#orderItemsAmount').text(totalText);
                }
            }
        } catch (e) {}
    }
}

function normalizeCartQuantity($input){
    var newVal = parseFloat($input.val());
    if (isNaN(newVal) || newVal < 1) {
        newVal = 1;
    }
    var maxQty = parseInt($input.data('max'));
    if (!isNaN(maxQty) && maxQty > 0 && newVal > maxQty) {
        newVal = maxQty;
        $input.val(newVal);
        toastr.warning('Only ' + maxQty + ' items are available.');
    }
    return newVal;
}

$(document).on('submit','form#cart_form', function(e){ 
	e.preventDefault();

	// let size=$(document).find('button.active').data('quantity');
    let size='';

	// if (size.length==0) {
	// 	toastr.error('Please Select A Size At First');

	// 	return;
	// }

    let clickedButton = $(document.activeElement).val();

    let data = $(this).serializeArray();
    data.push({ name: "button", value: clickedButton });



	let url=$(this).attr('action');
	let method=$(this).attr('method');
	$.ajax({
        url: url,
        method: method,
        data: data,
        success: function (res) {
                if (res.success) {
                    toastr.success(res.msg);
                    applyCartResponseUpdates(res);

                    if (res.modal) {
                        $(document).find(res.modal).addClass('hidden');
                    }

                    if(checkCheckout()==false && res.url){
                        document.location.href = res.url;
                    }

                    checkCheckout();
                }else{
                    toastr.error(res.msg);
                }
        },
        error:function (response){
            $.each(response.responseJSON.errors,function(field_name,error){
                toastr.error(error);
            })
        }
    });
});


$(document).on('submit','form#cart_remove_form', function(e) {
    
    
    e.preventDefault(); 
    var ele=$(this);
    swal({
      title: "Are you sure?",
      text: "You want To Delete!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#006400",
      confirmButtonText: "Yes, do it!",
      cancelButtonText: "No, cancel plz!",
      closeOnConfirm: false,
      closeOnCancel: false
    },
    function(isConfirm){
      if (isConfirm) {

        let url=ele.attr('action');
        let method=ele.attr('method');
        let data= ele.serialize();

        $.ajax({
            type: method,
            url: url,
            data: data,
            success: function(res) {
                
                if(res.success==true){
                    toastr.success(res.msg);
                    applyCartResponseUpdates(res);
                    checkCheckout();
                }else if(res.success==false){
                    toastr.error(res.msg);
                }
                
            },
            error:function (response){
                
            }
        });
        swal.close();
      } else {
        swal("Cancelled", "Your imaginary file is safe :)", "error");
      }
    });
});


$(document).on('click','a.cart_remove_form', function(e) {
    
    
    e.preventDefault(); 
    var ele=$(this);
    swal({
      title: "Are you sure?",
      text: "You want To Delete!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#006400",
      confirmButtonText: "Yes, do it!",
      cancelButtonText: "No, cancel plz!",
      closeOnConfirm: false,
      closeOnCancel: false
    },
    function(isConfirm){
      if (isConfirm) {

        let url=ele.attr('href');

        $.ajax({
            type: 'GET',
            url: url,
            data: {},
            success: function(res) {
                
                if(res.success==true){
                    
                    
                    toastr.success(res.msg);
                    
                    if(res.url){
                        document.location.href = res.url;
                    }else{
                        applyCartResponseUpdates(res);
                        checkCheckout();
                    }
                    
                    
                    
                    
                }else if(res.success==false){
                    toastr.error(res.msg);
                }
                
            },
            error:function (response){
                
            }
        });
        swal.close();
      } else {
        swal("Cancelled", "Your imaginary file is safe :)", "error");
      }
    });
});



$(document).on('click','a.cart-dropdown-btn', function(){

    let url=$(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        data: {},
        dataType: "json",
        success: function (res) {
            if (res.success) {
                $('div#cart-dropdown').html(res.html);
            }else{
                toastr.error(res.msg);
            }
           
        }
    });

});

$(document).on('click','.btn_modal', function(e){
    e.preventDefault();
    let url=$(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        data: {},
        dataType: "html",
        success: function (res) {
            if (res) {
                $('div#addressModal').html(res);
                const modal = document.getElementById('addressModal');
                modal.classList.remove('hidden');
            }
           
        }
    });

});


// + and - Button Handler
$(document).on('click','button.qtybtn', function() {
    var $button = $(this);
    var $input = $button.parent().find('input');
    var oldValue = parseFloat($input.val()) || 1;

    if ($button.hasClass('quantity-plus')) {
        var newVal = oldValue + 1;
    } else {
        var newVal = oldValue > 1 ? oldValue - 1 : 1;
    }

    $input.val(newVal);
    updateCart($input);
});

// Items quantity type
let cartTimer;
$(document).on('input', 'input[id^="quantity-"]', function () {
    let $input = $(this);

    clearTimeout(cartTimer);

    cartTimer = setTimeout(function () {
        updateCart($input);
    }, 600); // 500ms পরে call হবে
});


function updateCart($input) {
    var $button = $input.parent(); // যাতে data-href পাওয়া যায়
    let url = $button.closest('div').attr('data-href');
    let segment = $button.closest('div').attr('data-segment');
    let newVal = normalizeCartQuantity($input);

    if (typeof url !== "undefined") {
        $.ajax({
            url: url,
            method: "GET",
            data: {quantity: newVal, segment: segment},
            success: function (res) {
                if (res.success) {
                    toastr.success(res.msg);
                    applyCartResponseUpdates(res);
                    if(checkCheckout()==false && res.url){
                        document.location.href = res.url;
                    }
                    checkCheckout();
                } else {
                    toastr.error(res.msg);
                }
            }
        });
    }
}


// + and - Button Handler
$(document).on('click','button.cart_qtybtn', function() {
    var $button = $(this);
    var $input = $button.parent().find('input');
    var oldValue = parseFloat($input.val()) || 1;

    if ($button.hasClass('quantity-plus')) {
        var newVal = oldValue + 1;
    } else {
        var newVal = oldValue > 1 ? oldValue - 1 : 1;
    }

    $input.val(newVal);
    normalizeCartQuantity($input);
    updateCartQuantity($input);
});

// Items quantity type
let cartQtyTimer;
$(document).on('input', 'input[name="quantity"]', function () {
    let $input = $(this);

    clearTimeout(cartQtyTimer);

    cartQtyTimer = setTimeout(function () {
        updateCartQuantity($input);
    }, 600); // 500ms পরে call হবে
});

// Common Update Function
function updateCartQuantity($input) {
    var $button = $input.parent();
    let url = $button.closest('div').attr('data-href');
    let segment = $button.closest('div').attr('data-segment');
    let newVal = normalizeCartQuantity($input);

    if (typeof url !== "undefined") {
        $.ajax({
            url: url,
            method: "GET",
            data: {quantity: newVal, segment: segment},
            success: function (res) {
                if (res.success) {
                    toastr.success(res.msg);
                    applyCartResponseUpdates(res);
                    if(checkCheckout()==false && res.url){
                        document.location.href = res.url;
                    }
                    checkCheckout();
                } else {
                    toastr.error(res.msg);
                }
            }
        });
    }
}


function checkCheckout(){
    var currentUrl = window.location.pathname;
    if (currentUrl.includes('checkouts')) {
        getCheckoutPage();
        return true;
    }
    return false;
}

function getCheckoutPage(){

    let url=$(document).find('a.checkout_url').attr('href');
    $.ajax({
        url: url,
        method: "GET",
        data: {},
        dataType: "html",
        success: function (res) {
            if (res) {
                $('div#checkout_data').html(res);
                deliveryFunction();
            }
           
        }
    });
}

$(document).on('change','input.delivery_id',function () {
    deliveryFunction();
});


function deliveryFunction(){
    let total=Number($(document).find('span.sub_total').data('totalamount'));
    let totalvendor=Number($(document).find('span.sub_total').data('totalvendor'));
    let charge=Number($('input[name="delivery_id"]:checked').data('price') || 0);
    charge=(charge*totalvendor);
    $('.charge').text(charge);
    $('.total_amount').text(total+charge);
}