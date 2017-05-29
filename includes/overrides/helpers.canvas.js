(function ($, _, SettingAPI, app) {
  function getFoundationPrefix(string) {
    switch (getMediaQuery(string)) {
      case 'tablet':
        return 'medium-'
      case '':
        return 'large-'
      default:
        return '';
    }
  }

  function getMediaQuery(string) {
    var query = '';
    _.each(['_tablet', '_mobile'], function(target) {
      if (string.substring(string.length - target.length) == target) {
        query = target.substring(1);
      }
    });
    return query;
  }

  app.on('start', function () {
    // Horizontal alignment
    _.each([
      'horizontal_alignment',
      'horizontal_alignment_tablet',
      'horizontal_alignment_mobile'
    ], function(id) {
      SettingAPI.onChange('element:' + id, function(to, from, model) {
        var prefix = getFoundationPrefix(id);
        var media = getMediaQuery(id);
        if (media !== '') {
          media = '-' + media;
        }

        if (from) {
          this.el.classList.remove(prefix + 'text-' + from);
        }
        this.el.classList.add(prefix + 'text-' + to);
        // Remove tailors own class.
        this.el.classList.remove('u-text-' + to + media);
      });
    });

    // Vertical alignment
    _.each([
      'vertical_alignment',
      'vertical_alignment_tablet',
      'vertical_alignment_mobile'
    ], function(id) {
      SettingAPI.onChange('element:' + id, function(to, from, model) {
        var prefix = getFoundationPrefix(id);
        var media = getMediaQuery(id);
        if (media !== '') {
          media = '-' + media;
        }

        if (from) {
          this.el.classList.remove(prefix + 'text-' + from);
        }
        this.el.classList.add(prefix + 'text-' + to);
        // Remove tailors own class.
        this.el.classList.remove('u-text-' + to + media);
      });
    });
  });
})(jQuery, window._, window.Tailor.Api.Setting, window.app);
