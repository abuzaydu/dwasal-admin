$(document).ready(function(){ 	
	 $(document).on('click', '#getSale', function(e){  
     e.preventDefault();  
     var saleid = $(this).data('id');    
	  $('#sale-detail').hide();
     $('#sale-data-loader').show();  
     $.ajax({
          url: 'sale-info/'+saleid,
          type: 'GET'
     })
     .done(function(data){
          console.log(data); 
          $('#sale-detail').hide();
		$('#sale-detail').show();
          $('#shop_name').html(data.shop.display_name);
          $('#street').html(data.shop.street);
          $('#district').html(data.shop.district);
          $('#city').html(data.shop.city);
          $('#tel').html(data.shop.tel);
          $('#mobile').html(data.shop.mobile);
          $('#email').html(data.shop.email);
		$('#saleid').html(data.recno);
          $('#date').html(data.date);
          $('#customer_name').html(data.customer.name);
          $('#pay_type').html(data.sale.pay_type);
          var trHTML = "";
          $.each(data.items, function(i, item){
               trHTML += '<tr><td>' +(i+1)+'</td><td>' +item.name+ '</td><td>' +item.quantity_sold+ '</td><td>' +item.retail_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+ '</td><td>' +item.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+ '</td></tr>';
          });
          $('#sale_items').html(trHTML);
          $('#subtotal').html((data.sale.sale_amount - data.sale.tax_amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
          $('#vat').html(data.sale.tax_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
          $('#total').html(data.sale.sale_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
          $('#paid').html(data.sale.sale_amount_paid.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
		$('#change').html(data.change.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
          $('#due').html(data.due.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
          $('#sale-data-loader').hide();
     })
     .fail(function(){
          $('#sale-detail').html('Error, Please try again...');
          $('#sale-data-loader').hide();
     });
    });	
});
