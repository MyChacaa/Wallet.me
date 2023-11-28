$(document).ready(function() {

    $("#cnv_amount").on("keyup",function() {
        $.PW_Convert($(this).val());
      });
    
      $("#cnv_amount").on("keydown",function() {
        $.PW_Convert($(this).val());
      });

      $("#from_currency").on("change",function() {
        $.PW_LoadRates();
        $.PW_Convert($("#cnv_amount").val());
      });

      $("#to_currency").on("change",function() {
        $.PW_LoadRates();
        $.PW_Convert($("#cnv_amount").val());
      });

});

function PW_Load_Gateway_Fields(id) {
	var url = $("#url").val();
	var data_url = url + "requests/PW_Load_Gateway_Fields.php?id="+id;
	$.ajax({
		type: "GET",
		url: data_url,
		dataType: "html",
		success: function (data) {
			$("#pw_gateway_fields").html(data);
		}
	});
}

function PW_GetWalletCurrency(id) {
    var url = $("#url").val();
	var data_url = url + "requests/PW_GetWalletCurrency.php?id="+id;
	$.ajax({
		type: "GET",
		url: data_url,
		dataType: "html",
		success: function (data) {
            $("#send_amount").val("");
            $("#receive_amount").val("");
            $("#c_currency").val(data);
            var fee = $("#d_fee").val();
            var c_currency = $("#c_currency").val();
            var d_currency = $("#d_currency").val();
            if(c_currency !== d_currency) {
                PW_CurrencyConverter(fee,d_currency,c_currency);           
            } else {
                $("#c_fee").val(fee);
                var newfee = fee+" "+c_currency;
                $("#wfee").html(newfee); 
            }
		}
	});
}

function PW_Calculate(amount) {
    var fee = $("#c_fee").val();
    var include_fee = $("#c_include_fee").val();
    var extra_fee = $("#c_extra_fee").val();
    var c_currency = $("#c_currency").val();
    var d_currency = $("#d_currency").val();
    var new_amount = amount - fee;
    if(include_fee == "1") {
        calculate = new_amount * extra_fee;
        calculate = calculate / 100;
        new_amount = new_amount - calculate;
    }
    new_amount = new_amount.toFixed(2);
    if(new_amount > 0) {
        $("#receive_amount").val(new_amount);
    } else {
        $("#receive_amount").val("0");
    }
}

function PW_CurrencyConverter(amount,from,to) {
    var url = $("#url").val();
	var data_url = url + "requests/PW_CurrencyConverter.php?amount="+amount+"&from="+from+"&to="+to;
	$.ajax({
		type: "GET",
		url: data_url,
		dataType: "html",
		success: function (data) {
            $("#c_fee").val(data);
            var c_currency = $("#c_currency").val();
            var newfee = data+" "+c_currency;
            $("#wfee").html(newfee); 
		}
	});
} 

$(function() {
    "use strict";
    $.PW_LoadRates = function() {
        var url = $("#url").val();
        var from_currency = $("#from_currency").val();
        var to_currency = $("#to_currency").val();
        var data_url = url + "requests/PW_Convert.php?from="+from_currency+"&to="+to_currency;
        $.ajax({
            type: "GET",
            url: data_url,
            dataType: "json",
            success: function (data) {
                if(data.status == "success") { 
                    $("#rate_from").val(data.rate_from);
                    $("#rate_to").val(data.rate_to);
                    $("#cnv_to_curr").html(data.currency_to);
                    var crate = data.rate_from+" "+data.currency_from+" = "+data.rate_to+" "+data.currency_to;
                    $("#cnv_rate").html(crate);
                    $("#cnv_fields").show();
                }
            }
        });
    };
  });

$(function() {
    "use strict";
    $.PW_Convert = function(amount) {
        if(amount>0) { 
            var rate_from = $("#rate_from").val();
            var rate_to = $("#rate_to").val();
            if(rate_from < 1) {
                var sum = amount / rate_from;
                var data = sum.toFixed(2);
            } else {
                var sum = amount * rate_to;
                var data = sum.toFixed(2); 
            }
        } else {
            var data = '';
        }
        $("#cnv_receive").val(data);
    };
  });