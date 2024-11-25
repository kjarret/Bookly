(function ($) {
  let calendar;

  let Calendar = function ($container, options) {
    let obj = this;
    jQuery.extend(obj.options, options);

    // Special locale for moment
    moment.locale("bookly", {
      months: obj.options.l10n.datePicker.monthNames,
      monthsShort: obj.options.l10n.datePicker.monthNamesShort,
      weekdays: obj.options.l10n.datePicker.dayNames,
      weekdaysShort: obj.options.l10n.datePicker.dayNamesShort,
      meridiem: function (hours, minutes, isLower) {
        return hours < 12
          ? obj.options.l10n.datePicker.meridiem[isLower ? "am" : "AM"]
          : obj.options.l10n.datePicker.meridiem[isLower ? "pm" : "PM"];
      },
    });

    let viewTypes = {
      dayGridMonth: "resourceTimelineMonth",
      timeGridWeek: "resourceTimelineWeek",
      resourceTimeGridDay: "resourceTimelineDay",
    };

    // Settings for Event Calendar
    let settings = {
      view: "timeGridWeek",
      customButtons: {
        /* unchanged */
      },
      views: {
        /* unchanged */
      },
      nowIndicator: true,
      hiddenDays: obj.options.l10n.hiddenDays,
      slotDuration: obj.options.l10n.slotDuration,
      slotMinTime: obj.options.l10n.slotMinTime,
      slotMaxTime: obj.options.l10n.slotMaxTime,
      scrollTime: obj.options.l10n.scrollTime,
      moreLinkContent: function (arg) {
        return obj.options.l10n.more.replace("%d", arg.num);
      },
      flexibleSlotTimeLimits: true,
      eventStartEditable: false,
      eventDurationEditable: false,
      allDaySlot: false,
      allDayContent: obj.options.l10n.allDay,
      slotLabelFormat: function (date) {
        return moment(date)
          .locale("bookly")
          .format(obj.options.l10n.mjsTimeFormat);
      },
      eventTimeFormat: function (start, end) {
        if (start.getTime() === end.getTime()) {
          return moment(start)
            .locale("bookly")
            .format(obj.options.l10n.mjsTimeFormat);
        } else {
          return (
            moment(start)
              .locale("bookly")
              .format(obj.options.l10n.mjsTimeFormat) +
            " - " +
            moment(end).locale("bookly").format(obj.options.l10n.mjsTimeFormat)
          );
        }
      },
      dayHeaderFormat: function (date) {
        return moment(date).locale("bookly").format("ddd, D");
      },
      listDayFormat: function (date) {
        return moment(date).locale("bookly").format("dddd");
      },
      firstDay: obj.options.l10n.datePicker.firstDay,
      locale: obj.options.l10n.locale.replace("_", "-"),
      buttonText: {
        /* unchanged */
      },
      noEventsContent: obj.options.l10n.noEvents,
      eventSources: [
        {
          url: ajaxurl,
          method: "POST",
          extraParams: function () {
            return {
              action: "bookly_get_staff_appointments",
              csrf_token: BooklyL10nGlobal.csrf_token,
              staff_ids: obj.options.getStaffMemberIds(),
              location_ids: obj.options.getLocationIds(),
              service_ids: obj.options.getServiceIds(),
            };
          },
        },
      ],
      eventContent: function (arg) {
        if (arg.event.display === "background") {
          return "";
        }
        let event = arg.event;
        let props = event.extendedProps;
        let nodes = [];
        let $time = $('<div class="ec-event-time"/>');
        let $title = $('<div class="ec-event-title"/>');

        $time.append(props.header_text || arg.timeText);
        nodes.push($time.get(0));
        if (arg.view.type === "listWeek") {
          let dot = $('<div class="ec-event-dot"></div>').css(
            "border-color",
            event.backgroundColor
          );
          nodes.push($("<div/>").append(dot).get(0));
        }
        $title.append(props.desc || "");
        nodes.push($title.get(0));

        return { domNodes: nodes };
      },
      eventDidMount: function (arg) {
        // Apply location-specific colors
        if (arg.event.extendedProps.location_color) {
          $(arg.el).css(
            "background-color",
            arg.event.extendedProps.location_color
          );
        }
      },
      eventClick: function (arg) {
        /* unchanged */
      },
      dateClick: function (arg) {
        /* unchanged */
      },
      noEventsClick: function (arg) {
        /* unchanged */
      },
      loading: function (isLoading) {
        /* unchanged */
      },
      viewDidMount: function (view) {
        /* unchanged */
      },
      theme: function (theme) {
        /* unchanged */
      },
    };

    // Initialize the calendar
    calendar = new window.EventCalendar(
      $container.get(0),
      $.extend(true, {}, settings, obj.options.calendar)
    );

    // Additional event listeners
    $(".ec-toolbar .ec-title", $container).on("click", function () {
      let picker = $(this).data("daterangepicker");
      picker.setStartDate(calendar.getOption("date"));
      picker.setEndDate(calendar.getOption("date"));
    });
  };

  Calendar.prototype.options = {
    calendar: {},
    getCurrentStaffId: function () {
      return -1;
    },
    getStaffMemberIds: function () {
      return [this.getCurrentStaffId()];
    },
    getServiceIds: function () {
      return ["all"];
    },
    getLocationIds: function () {
      return ["all"];
    },
    refresh: function () {},
    viewChanged: function () {},
    l10n: {},
  };

  window.BooklyCalendar = Calendar;
})(jQuery);
