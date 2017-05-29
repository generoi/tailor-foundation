(function ($, _, Tailor, app) {
  var ButtonGroup = Tailor.Controls.ButtonGroup;

  var originalOnFieldChange = ButtonGroup.prototype.onFieldChange;

  ButtonGroup.prototype.onFieldChange = function (e) {
    var button = e.currentTarget;
    var isActive = button.classList.contains('active');
    if (!isActive) {
      originalOnFieldChange.call(this, e);
    }
    // Add toggle functionality.
    else {
      button.classList.remove('active');
      this.setValue('');
    }
  };

})(jQuery, window._, window.Tailor, window.app);
