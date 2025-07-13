$(document).ready(function () {
    $('#searchToggleBtn').click(function (e) {
      e.preventDefault();
      $('#mobileSearchBox').slideToggle();
    });
  
    // pop up for subscription
    $('#subscribeForm').on('submit', function (e) {
      e.preventDefault();
      var email = $('#subscriberEmail').val().trim();
      if (email === "") {
        alert("Please enter your email!");
        return;
      }
      $.post('includes/subscribe.php', { email: email }, function(response) {
        // Optionally check response for success
        var modal = new bootstrap.Modal(document.getElementById('subscribeModal'));
        modal.show();
        $('#subscriberEmail').val('');
      });
    });
  });
