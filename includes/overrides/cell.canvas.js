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
      switch (value) {
        case 'full':
          return 12;
        case 'auto':
        case 'shrink':
          return value;
        default:
          return parseInt(value, 10);
      }
    }

    /**
     * Return a function that changes the column class for the breakpoint.
     */
    function columnsChangeFn(breakpoint) {
      return function (to, from) {
        console.log('columns change from ' + from + ' to ' + to);
        to = parseColumns(to);
        from = parseColumns(from);

        switch (from) {
          case 'full':
            this.el.classList.remove(breakpoint + '-12');
            break;
          case 'auto':
          case 'shrink':
            var class_name = (breakpoint !== 'small') ? breakpoint + '-' + from : from;
            this.el.classList.remove(class_name);
            break;
          default:
            if (from > 0) {
              this.el.classList.remove(breakpoint + '-' + from);
            }
            break;
        }

        switch (to) {
          case 'full':
            this.el.classList.add(breakpoint + '-12');
            break;
          case 'auto':
          case 'shrink':
            var class_name = (breakpoint !== 'small') ? breakpoint + '-' + to : to;
            this.el.classList.add(class_name);
            break;
          default:
            if (to > 0) {
              this.el.classList.add(breakpoint + '-' + to);
            }
            break;
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
          // New row.
          if (columnSpace === 0) {
            columnSpace = 12;
          }
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
      var rowView = columnView._parent;
      var nextModel = model.collection.findWhere({
        parent: model.get('parent'),
        order: model.get('order') + 1
      });
      var modelsInRow = model.collection.where({
        parent: model.get('parent')
      });

      var atts = model.get('atts');
      var originalWidth = parseColumns(atts[setting] || atts.columns);

      var rowRect = rowView.el.getBoundingClientRect();
      var initialWidth = columnView.el.getBoundingClientRect().width;
      var initialColumns = Math.round((initialWidth / rowRect.width) * 12);

      function onResize(e) {
        document.body.classList.add('is-resizing');
        document.body.style.cursor = 'col-resize';

        var rect = columnView.el.getBoundingClientRect();
        var atts = _.clone(model.get('atts'));
        var width = parseColumns(atts[setting] || atts.columns);

        // If the column count is not specified, calculate it.
        if (width === 'auto' || width == 'shrink') {
          width = initialColumns;
          console.log('setting initial columsn');
        }

        var newWidth = Math.round((e.clientX - rect.left) / (rect.width) * width);
        console.log('width: ' + width);
        console.log('newWidth: ' + newWidth);

        if (newWidth < 1 || newWidth === width || newWidth > 12) {
          return;
        }
        // Save the values using the prefix `cols_`.
        // @see tailor-foundation.php
        atts[setting] = 'cols_' + newWidth;
        model.set('atts', atts, {silent: true});
        model.trigger('change:width', model, atts);
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
      this.$el.removeClass('auto medium-auto large-auto shrink medium-shrink large-shrink');
      this.$el.removeClass(function (index, css) {
        return (css.match(/(^|\s)(small|medium|large)-[0-9]{1,2}/g) || []).join(' ');
      });

      var columns = {
        'large': parseColumns(atts.columns),
        'medium': parseColumns(atts.columns_tablet),
        'small': parseColumns(atts.columns_mobile),
      }
      var that = this;
      _.each(columns, function (columns, breakpoint) {
        console.log(breakpoint + ' '+ columns);
        switch (columns) {
          case 'full':
            that.el.classList.add(breakpoint + '-12');
            break;
          case 'auto':
          case 'shrink':
            var class_name = (breakpoint !== 'small') ? breakpoint + '-' + columns : columns;
            that.el.classList.add(class_name);
            break;
          default:
            if (columns > 0) {
              that.el.classList.add(breakpoint + '-' + columns);
            }
            break;
        }
      });
    };
  });
})(jQuery, window._, window.Tailor, window.app);
