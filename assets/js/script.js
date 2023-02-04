jQuery(document).ready(function ($) {
    $("#sly_invest_contact_form").on('submit', function (e) {
        e.preventDefault();

        // document.getElementById("send_pdf").textContent = 'Sending...';
        // document.getElementById("send_pdf").style.opacity = '.5';

        // let productId = $(this).data("product-id");
        let fname = $("#fname").val();
        let lname = $("#lname").val();
        let email = $("#email").val();
        let listname = $("#list_name").val();
        let placename = $("#place_name").val();
        let amount = $("#amount").val();
        let amountLeft = $("#amount_left").val();
        let tot_amount = $("#tot_amount").val();
        let item_ID = $("#item-id").val();
        let user_ID = $("#user-id").val();

        // var data = jQuery(this).serialize();

        let data = {
            'nonce': skyInvesContactFrom.nonce,
            'action': 'sky_invest_cf',
            'fname':fname,
            'lname':lname,
            'email':email,
            'list_name':listname,
            'place_name':placename,
            'amount':amount,
            'amount_left':amountLeft,
            'tot_amount':tot_amount,
            'item_id':item_ID,
            'user_id':user_ID,
        };

        $.ajax({
            type: "POST",
            url: skyInvesContactFrom.url,
            data: data,
            beforeSend: function () {

            },
            success: function (response) {
                console.log(response);
                $("#inv-success").show();
                setTimeout(() => {
                    $("#sktyInvestContact").hide();
                    window.location = '/thank-you';
                    // location.reload();
                  
                }, 2000);
                
            },
            error: function (xhr) {
                console.log(xhr.statusText + xhr.responseText);
            },
        });
    });

    // Investment update from fronend 

    $("#sly_invest_update").on('submit', function (e) {
        e.preventDefault();

        // document.getElementById("send_pdf").textContent = 'Sending...';
        // document.getElementById("send_pdf").style.opacity = '.5';

        // let productId = $(this).data("product-id");
        let inv_item_ID = $("#listItemID").val();
        

        // var data = jQuery(this).serialize();

        let data = {
            'nonce': skyInvesContactFrom.nonce,
            'action': 'sky_update_item',
            'inv_item_ID':inv_item_ID,
        };

        $.ajax({
            type: "POST",
            url: skyInvesContactFrom.url,
            data: data,
            beforeSend: function () {

            },
            success: function (response) {
                console.log(response);
                $("#messageUndoeInv").show();
                // $("#inv-success").show();

                
                setTimeout(() => {
                    // $("#sktyInvestContact").hide();
                    window.close();
                    location.reload();
                    // window.location = '/thank-you';
                    // location.reload();
                  
                }, 2000);
                
            },
            error: function (xhr) {
                console.log(xhr.statusText + xhr.responseText);
            },
        });
    });
});

