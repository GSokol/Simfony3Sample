(function($, ymaps) {
  var map, createAssetBox = function() {
    var assetBoxDiv = $('<div>');
    assetBoxDiv.addClass('box assetBox');
    assetBoxDiv.html('<div class="boxHeader">' +
        '<p>Активы</p>' +
      '</div>');
    $('.LeftPrat').append(assetBoxDiv);
  }, updateMap = function () {
    if (map == undefined) {
      map = new ymaps.Map('_y_map', {
        center: [62.359397, 74.32672],
        zoom: 3,
        controls: ['zoomControl',],
      });
    } else {
      map.geoObjects.removeAll();
    }

    var requestData = _.object($('form').serializeArray().map(function(v) {
      return [v.name, v.value];
    }));

    $.ajax({
      url: '/assets/find',
      method: 'GET',
      contentType: 'JSON',
      data: requestData,
      success: function(data, statusText, jqXHR) {
        if ($('.assetBox').length == 0) {
          createAssetBox();
        }
        $('.assetBox').html('<div class="boxHeader">' +
              '<p>Активы</p>' +
            '</div>');
        $.each(data, function() {
          var placemark = new ymaps.Placemark([this.lat, this.long], {
            hintContent: this.title,
          }, {
            iconLayout: 'default#image',
            iconImageHref: '/static/img/placemark.gif',
            iconImageSize: [16, 16],
          }), active = $('<div>'), id = this.id;
          map.geoObjects.add(placemark);
          active.addClass('containerText asset asset-id-' + this.id);
          var activeHtml = '<p><b>' + this.title + '</b></p>' +
            '<p>' + this.address + '</p>' +
            '<p><b>Персонал:</b>' + this.staff + '</p>';
          if (this.turnover != undefined) {
            activeHtml += '<p><b>Оборот:</b>' + this.turnover + '</p>';
          }
          active.html(activeHtml);
          $('.assetBox').append(active);
          placemark.events.add('click', function() {
            $('.asset').removeClass('selected');
            $('.asset-id-' + id).addClass('selected');
          });
        });
      }
    });
  };

  $(document).ready(function() {
    $.ajax({
      url: '/assets/filters',
      method: 'GET',
      contentType: 'JSON',
      success: function(data, statusText, jqXHR) {
        $.each(data, function() {
          if (this.type == 'float') {
            var div = $('<div>'), dataName = this.data_name;
            div.html(
              '<label>' + this.name + ':</label> ' +
              '<span class="' + dataName + '-value-text">' +
                this.min_value +' -- ' + this.max_value +
              '</span> ' + this.currency +
              '<input type="hidden" class="' + dataName + '_min-value" name="min_' + dataName + '" >' +
              '<input type="hidden" class="' + dataName + '_max-value" name="max_' + dataName + '" >' +
              '<div class="' + dataName + '-slide-range"></div>'
            );
            $('.filters').append(div);
            $('.' + dataName + '-slide-range').slider({
              range: true,
              min: this.min_value,
              max: this.max_value,
              values: [this.min_value, this.max_value],
              slide: function(event, ui) {
                $('.' + dataName + '-value-text').text(ui.values[0] + ' -- ' + ui.values[1]);
                $('.' + dataName + '_min-value').val(ui.values[0]);
                $('.' + dataName + '_max-value').val(ui.values[1]);
              }
            });
          }
        });

        $('form').submit(function(e) {
          e.preventDefault();
          updateMap();
        });
      }
    });

    ymaps.ready(updateMap);

    //$('.Center').height($('.container').innerHeight());

    $('.filterBox .last input').click(function(e) {
      e.preventDefault();
      // update data
    });
  });
})(jQuery, ymaps);
