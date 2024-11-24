jQuery(function($) {

    let
        $locations_list = $('#bookly-locations'),
        $check_all_button = $('#bookly-check-all'),
        $location_modal = $('#bookly-location-modal'),
        $location_name = $('#bookly-location-name', $location_modal),
        $location_info = $('#bookly-location-info', $location_modal),
        $staff = $('#bookly-js-staff', $location_modal),
        $location_new_title = $('#bookly-new-locations-title', $location_modal),
        $location_edit_title = $('#bookly-edit-locations-title', $location_modal),
        $save_button = $('#bookly-location-save', $location_modal),
        $delete_button = $('#bookly-delete'),
        elements = {
            draghandle: $('<i class="fas fa-fw fa-bars text-muted bookly-cursor-move bookly-js-draghandle" />', {title: BooklyL10n.reorder}).get(0).outerHTML,
            edit_btn: $('<button type="button" class="btn btn-default" data-action="edit">').append($('<i class="far fa-fw fa-edit mr-lg-1" />'), '<span class="d-none d-lg-inline">' + BooklyL10n.edit + '…</span>').get(0).outerHTML
        },
        row
    ;

    /**
     * Init table columns.
     */
    var columns = [
        {
            data: 'position'
        },
        {
            data: null,
            render: function(data, type, row, meta) {
                return elements.draghandle;
            },
            responsivePriority: 0
        }
    ];

    $.each(BooklyL10n.datatables.locations.settings.columns, function(column, show) {
        if (show) {
            if (column == 'staff') {
                columns.push({
                    data: column,
                    render: function(data, type, row, meta) {
                        if (data == 0) {
                            return BooklyL10n.staff.nothingSelected;
                        } else if (data == 1) {
                            return $.fn.dataTable.render.text().display(BooklyL10n.staff.collection[row.staff_id].full_name);
                        } else {
                            if (data == BooklyL10n.staff.count) {
                                return BooklyL10n.staff.allSelected;
                            } else {
                                return data + '/' + BooklyL10n.staff.count;
                            }
                        }
                    }
                });
            } else {
                columns.push({data: column, render: $.fn.dataTable.render.text()});
            }
        }
    });
    columns.push({
        data: null,
        responsivePriority: 1,
        render: function(data, type, row, meta) {
            return elements.edit_btn;
        }
    });
    columns.push({
        data: null,
        responsivePriority: 1,
        render: function(data, type, row, meta) {
            return '<div class="custom-control custom-checkbox">' +
                '<input value="' + row.id + '" id="bookly-dt-' + row.id + '" type="checkbox" class="custom-control-input">' +
                '<label for="bookly-dt-' + row.id + '" class="custom-control-label"></label>' +
                '</div>';
        }
    });

    /**
     * Init DataTables.
     */
    var dt = $locations_list.DataTable({
        paging: false,
        info: false,
        searching: false,
        processing: true,
        responsive: true,
        ajax: {
            url: ajaxurl,
            data: {action: 'bookly_locations_get_locations', csrf_token: BooklyL10nGlobal.csrf_token}
        },
        rowReorder: {
            dataSrc: 'position',
            snapX: true,
            selector: '.bookly-js-draghandle'
        },
        order: [0, 'asc'],
        columnDefs: [
            {visible: false, targets: 0},
            {orderable: false, targets: '_all'}
        ],
        columns: columns,
        language: {
            zeroRecords: BooklyL10n.zeroRecords,
            processing: BooklyL10n.processing
        },
        layout: {
            bottomStart: 'paging',
            bottomEnd: null
        }
    }).on('row-reordered', function(e, diff, edit) {
        let positions = [];
        dt.data().each(function(item) {
            positions.push({position: parseInt(item.position), id: item.id});
        });
        let data = {
            locations: (positions.sort(function(a, b) { return a.position - b.position; }))
                .map(function(value) {
                    return value.id;
                })
        };

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: booklySerialize.buildRequestData('bookly_locations_update_locations_position', data),
            dataType: 'json',
            success: function(response) {

            }
        })
    });

    /**
     * Select all locations.
     */
    $check_all_button.on('change', function() {
        $locations_list.find('tbody input:checkbox').prop('checked', this.checked);
    });

    $locations_list
        // On location select.
        .on('change', 'tbody input:checkbox', function() {
            $check_all_button.prop('checked', $locations_list.find('tbody input:not(:checked)').length == 0);
        })
        // Edit location
        .on('click', '[data-action=edit]', function() {
            row = dt.row($(this).closest('td'));
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookly_locations_get_location_lists',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    location_id: row.data().id,
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $staff.booklyDropdown('setSelected', response.data.staff_id);
                        $location_modal.booklyModal('show');
                    }
                }
            })
        });

    /**
     * On show modal.
     */
    $location_modal
        .on('show.bs.modal', function(e) {
            var data;
            if (row) {
                data = row.data();
                $location_new_title.hide();
                $location_edit_title.show();
            } else {
                data = {title: '', staff_ids: []};
                $location_new_title.show();
                $location_edit_title.hide();
            }
            $location_name.val(data.name);
            $location_info.val(data.info);
        })
        .on('hidden.bs.modal', function() { row = null; });

    /**
     * Staff drop-down.
     */
    $staff.booklyDropdown();

    /**
     * Save location.
     */
    $save_button.on('click', function(e) {
        e.preventDefault();
        let data = booklySerialize.form($(this).closest('form')),
            ladda = Ladda.create(this, {timeout: 2000});
        if (row) {
            data.id = row.data().id;
        }
        ladda.start();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: booklySerialize.buildRequestData('bookly_locations_save_location', data),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (row) {
                        row.data(response.data).draw();
                    } else {
                        dt.row.add(response.data).draw();
                    }
                    $location_modal.booklyModal('hide');
                } else {
                    alert(response.data.message);
                }
                ladda.stop();
            }
        });

    });

    /**
     * Delete locations.
     */
    $delete_button.on('click', function() {
        if (confirm(BooklyL10n.areYouSure)) {
            var ladda = Ladda.create(this);
            ladda.start();

            var data = [];
            var $checkboxes = $locations_list.find('input:checked');
            $checkboxes.each(function() {
                data.push(this.value);
            });

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookly_locations_delete_locations',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    locations: data
                },
                dataType: 'json',
                success: function(response) {
                    ladda.stop();
                    if (response.success) {
                        dt.rows($checkboxes.closest('td')).remove().draw();
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        }
    });

});