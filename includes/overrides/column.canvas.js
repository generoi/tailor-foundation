/**
 * Changes here revert tailor changes in v1.7.6
 * Improved - Columns now use percentage widths, instead of a 12 column grid system.
 * @see https://github.com/andrew-worsfold/tailor/commit/50184096c2dc8712dd240f13f61f0e313be305b3
 */
(function ($, _, Tailor, app) {
  app.on('before:start', function () {
    var ColumnView = Tailor.Views.Column;
    var SettingApi = Tailor.Api.Setting;

    /**
     * Return the column count from the value stored (prefixed with `cols_`).
     */
    function parseColumns(value) {
      if (_.isString(value)) {
        value = value.replace('cols_', '');
      }
      if (value === 'auto') {
        return value;
      }
      return parseInt(value, 10);
    }

    /**
     * Return a function that changes the column class for the breakpoint.
     */
    function columnsChangeFn(breakpoint) {
      return function (to, from) {
        to = parseColumns(to);
        from = parseColumns(from);
        if (from && from !== 'auto') {
          this.el.classList.remove(breakpoint + '-' + from);
        }
        if (to !== 'auto') {
          this.el.classList.add(breakpoint + '-' + to);
        }
      };
    }

    /**
     * Based on a set of the columns in the same row, calculate the column
     * count available taking into account `auto` by dividing the remainder.
     */
    function getColumnCountAvailable(models) {
      var device = app.channel.request('sidebar:device');
      var setting = (device === 'desktop') ? 'columns' : 'columns_' + device;
      var columnSpace = 12;
      var autoColumns = 0;
      for (var i = 0, l = models.length; i < l; i++) {
        var loopAtts = models[i].get('atts');
        var cols = parseColumns(loopAtts[setting]);
        if (cols === 'auto') {
          autoColumns++;
        } else {
          columnSpace -= cols;
        }
      }
      if (autoColumns) {
        columnSpace /= autoColumns;
      }
      if (columnSpace < 0) {
        return Math.abs(columnSpace);
      }
      if (columnSpace === 0) {
        return 12;
      }
      return columnSpace;
    }

    SettingApi.onChange('element:columns', columnsChangeFn('large'));
    SettingApi.onChange('element:columns_tablet', columnsChangeFn('medium'));
    SettingApi.onChange('element:columns_mobile', columnsChangeFn('small'));

    ColumnView.prototype.onRenderCollection = function () {
      this.updateClassName(this.model.get('atts'));
      this.$el
        .attr('draggable', true)
        .prepend(
          '<div class="tailor-column__helper">' +
              '<div class="tailor-column__sizer"></div>' +
          '</div>'
      );
    };

    ColumnView.prototype.onResize = function () {
      var columnView = this;
      var device = app.channel.request('sidebar:device');
      var setting = (device === 'desktop') ? 'columns' : 'columns_' + device;

      var model = columnView.model;
      var nextModel = model.collection.findWhere({
        parent: model.get('parent'),
        order: model.get('order') + 1
      });
      var modelsInRow = model.collection.where({
        parent: model.get('parent')
      });

      var atts = model.get('atts');
      var originalWidth = parseColumns(atts[setting] || atts.columns);

      function onResize(e) {
        document.body.classList.add('is-resizing');
        document.body.style.cursor = 'col-resize';

        var columnSpace = getColumnCountAvailable(modelsInRow) || 12;
        var rect = columnView.el.getBoundingClientRect();
        var atts = _.clone(model.get('atts'));
        var nextAtts = _.clone(nextModel.get('atts'));
        var width = parseColumns(atts[setting] || atts.columns);
        var nextWidth = parseColumns(nextAtts[setting] || nextAtts.columns);

        // If the column count is set to `auto` use the column space available.
        if (width === 'auto') {
          width = columnSpace;
        }
        if (nextWidth === 'auto') {
          nextWidth = columnSpace;
        }

        var newWidth = Math.round((e.clientX - rect.left) / (rect.width) * width);

        if (newWidth < 1 || newWidth === width || newWidth > 12) {
          return;
        }

        // If we expand to full row width, expand the next column as well.
        if (newWidth === 12) {
          nextWidth = 12;
        }

        // Save the values using the prefix `cols_`.
        // @see tailor-foundation.php
        atts[setting] = 'cols_' + newWidth;
        model.set('atts', atts, {silent: true});
        model.trigger('change:width', model, atts);

        if (parseColumns(nextAtts[setting] || nextAtts.columns) !== 'auto') {
          nextAtts[setting] = 'cols_' + (nextWidth - (newWidth - width));
          nextModel.set('atts', nextAtts, {silent: true});
          nextModel.trigger('change:width', nextModel, nextAtts);
        }
      }

      function onResizeEnd() {
        document.removeEventListener('mousemove', onResize, false);
        document.removeEventListener('mouseup', onResizeEnd, false);

        document.body.classList.remove('is-resizing');
        document.body.style.cursor = 'default';

        var atts = model.get('atts');
        if (originalWidth !== parseColumns(atts[setting])) {
          app.channel.trigger('element:resize', model);
        }
      }

      document.addEventListener('mousemove', onResize, false);
      document.addEventListener('mouseup', onResizeEnd, false);

      app.channel.trigger('canvas:reset');
      return false;
    };

    ColumnView.prototype.onChangeWidth = function (model, atts) {
      this.updateClassName(atts);
      this.triggerAll('element:refresh', this);
    };

    ColumnView.prototype.updateClassName = function (atts) {
      // Remove old classes.
      this.$el.removeClass(function (index, css) {
        return (css.match(/(^|\s)(small|medium|large)-[0-9]{1,2}/g) || []).join(' ');
      });

      if (parseColumns(atts.columns) > 0) {
        this.el.classList.add('large-' + parseColumns(atts.columns));
      }
      if (parseColumns(atts.columns_tablet, 10) > 0) {
        this.el.classList.add('medium-' + parseColumns(atts.columns_tablet));
      }
      if (parseColumns(atts.columns_mobile, 10) > 0) {
        this.el.classList.add('small-' + parseColumns(atts.columns_mobile));
      }
    };
  });
})(jQuery, window._, window.Tailor, window.app);
