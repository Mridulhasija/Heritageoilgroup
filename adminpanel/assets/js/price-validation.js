function handleDiscountPriceFields() {
    // Get all discount price input elements
    var $discountPriceInputs = $('.price-group .discount-price');

    $discountPriceInputs.each(function() {
        var $priceField = $(this).closest('.price-group').find('.price');

        // Get values from both fields
        var price = parseFloat($priceField.val());
        var discountedPrice = parseFloat($(this).val());

        // Dispose of the popover before showing a new one
        $(this).popover('dispose');

        // Validate and show message
        if (!isNaN(discountedPrice) && discountedPrice <= 0) {
            // Show the popover with placement set to bottom for the error message
            $(this).popover({
                content: 'Discounted price must be greater than 0.',
                placement: 'bottom',
                trigger: 'manual'
            });
            $(this).popover('show');
            $(this).addClass('popoverON');
        } else if (!isNaN(discountedPrice) && discountedPrice >= price) {
            // Show the popover with placement set to bottom for the error message
            $(this).popover({
                content: 'Discounted price must be less than the regular price.',
                placement: 'bottom',
                trigger: 'manual'
            });
            $(this).popover('show');
            $(this).addClass('popoverON');
        } else {
            // Hide popover if valid
            $(this).popover('hide');
            $(this).removeClass('popoverON');
        }
    });
}

function handlePriceFields() {
    // Get all price input elements
    var $priceInputs = $('.price-group .price');

    $priceInputs.each(function() {
        // Get the value of the price input
        var priceValue = parseFloat($(this).val());
        var $discountPriceField = $(this).closest('.price-group').find('.discount-price');

        // Disable the discount-price field if the price is empty
        if ($(this).val().trim() === '' || isNaN(priceValue) || priceValue <= 0) {
            $discountPriceField.prop('disabled', true).val('');
            handleDiscountPriceFields(); // Trigger the discount price validation
        } else {
            // Enable the discount-price field
            $discountPriceField.prop('disabled', false);
        }

        // Dispose of the popover before showing a new one
        $(this).popover('dispose');

        // Validate and show message
        if (!isNaN(priceValue) && priceValue <= 0) {
            // Show the popover with placement set to bottom for the error message
            $(this).popover({
                content: 'Price must be greater than 0.',
                placement: 'bottom',
                trigger: 'manual'
            });
            $(this).popover('show');
            $(this).addClass('popoverON');
        } else {
            // Hide popover if valid
            $(this).popover('hide');
            $(this).removeClass('popoverON');
        }
    });
}

$(document).ready(function() {
    handlePriceFields();
    
    $(document).on('input', '.price-group .price', function() {
        handlePriceFields();
    });

    $(document).on('input', '.discount-price', function() {
        handleDiscountPriceFields();
    });
    
    $(document).on('blur', '.price-group input', function() {
        if ($(this).hasClass('popoverON')) {
            $(this).val('');
            if ($(this).hasClass('price')){
                handlePriceFields();
            }else if ($(this).hasClass('discount-price')){
                handleDiscountPriceFields();
            }
        }
    });
    


});