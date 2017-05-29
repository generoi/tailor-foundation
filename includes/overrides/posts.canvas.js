(function ($, _, Tailor, app, ElementAPI) {
  ElementAPI.onRender('tailor_posts', function(atts, model) {
    var $el = this.$el;
    var options;
    if (atts.layout == 'carousel') {
      this.$el.find('.slick').slick();
    }
  });
})(jQuery, window._, window.Tailor, window.app, window.Tailor.Api.Element);

