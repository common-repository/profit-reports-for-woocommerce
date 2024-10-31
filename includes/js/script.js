(function($){
  
  $('#status_id').select2({
    placeholder: 'Select Order Statuses'
}).on('select2:unselecting', function() {
    $(this).data('unselecting', true);
}).on('select2:opening', function(e) {
    if ($(this).data('unselecting')) {
        $(this).removeData('unselecting');
        e.preventDefault();
    }
});

  $('#codup_pr_overhead_type').on('change',toggleOptions);

  toggleOptions();

  function toggleOptions() {
    if("1221" == $('#codup_pr_overhead_type').val()) {
      $('#codup_pr_overhead_on').parents('tr').show();
    }
    else {
      $('#codup_pr_overhead_on').parents('tr').hide();
    }
  }

  $('#codup_pr_profit_default_cost').on('blur',validateNumbers);
  $('#codup_pr_profit_default_overhead').on('blur',validateNumbers);

  function validateNumbers() {

    currVal = $(this).val();
    $parent = $(this).parent();

    if(!currVal.match(/^\d+/)) {

      $(this).css("border","1px solid red");

      if(!$parent.find('.codup_pr-err').length) {
        $(this).parent().append("&nbsp;&nbsp;<span class='codup_pr-err text-red'><small>Please enter a numeric value.</small></span>");
        
      }

    }
    else {
      if($parent.find('.codup_pr-err').length) {
          $parent.find('.codup_pr-err').detach();
          $(this).css("border","1px solid rgb(221, 221, 221)");
      }
    }

  }

})(jQuery);
