function wp360toggleCustomFun(elm){
    jQuery(elm).toggle()
}
jQuery(document).ready(function($){
    $(document).ready(function() {
        var currentIndex = 0;
        function addNewItem() {
          currentIndex++; // Increment the item index
          var newItem = $('.invoiceItem:first').clone(); // Clone the first invoiceItem
          newItem.find('input').val('');
          newItem.find('input').attr('name', function(index, attr) {
            return attr.replace(/\[0\]/g, '[' + currentIndex + ']');
          });
          newItem.insertBefore('.addInvoiceItemCon');
          $('.removeInvoiceItem').toggle(currentIndex > 0);
        }
        function removeLastItem() {
          if (currentIndex > 0) {
            $('.invoiceItem:last').remove();
            currentIndex--;
            $('.removeInvoiceItem').toggle(currentIndex > 0);
          }
        }
        $('.addInvoiceItem').on('click', function() {
            addNewItem();
        });
        $('.removeInvoiceItem').on('click', function() {
            removeLastItem();
        });
      });      
      $(document).on('change keydown keyup', '.itemsCon input', function(){
        let qty = 0;
        let unitPrice = 0;
        let itemPrice = 0;
        let totalPrice = 0;
        $('.itemsCon .invoiceItem').each(function(index){
            qty = $(this).find('.qtyField').val()
            unitPrice = $(this).find('.unitPriceField').val()
            itemPrice = qty * unitPrice
            totalPrice = totalPrice + itemPrice;
            qty = 0;
            unitPrice = 0;
            itemPrice = 0;
        })
        $('#totalAmountField').val(totalPrice)
      })
      $('.hideToggleCTRL').on('change', function(){
        $('.toggleHide').toggleClass('wp360-inovice-hideElm');
      })
})
