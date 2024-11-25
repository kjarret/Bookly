<?php defined('ABSPATH') || exit; // Exit if accessed directly
use Bookly\Backend\Components\Dialogs\TableSettings;
use Bookly\Backend\Components\Support;
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e('Locations', 'bookly') ?></h4>
        <?php Support\Buttons::render($self::pageSlug()) ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="form-row justify-content-end">
                <div class="col-auto">
                    <?php Buttons::renderAdd(null, 'w-100 mb-3', __('Add Location', 'bookly'), array('data-toggle' => 'bookly-modal', 'data-target' => '#bookly-location-modal')) ?>
                </div>
                <?php TableSettings\Dialog::renderButton('locations') ?>
            </div>

            <table id="bookly-locations" class="table table-striped w-100">
                <thead>
                    <tr>
                        <th></th>
                        <th width="24"></th>
                        <?php foreach ($datatable['settings']['columns'] as $column => $show): ?>
                            <?php if ($show): ?>
                                <th><?php echo $datatable['titles'][$column] ?></th>
                            <?php endif ?>
                        <?php endforeach ?>
                        <th width="75"></th>
                        <th width="16">
                            <?php Inputs::renderCheckBox(null, null, null, array('id' => 'bookly-check-all')) ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datatable['data'] as $row): ?>
                        <tr>
                            <td></td>
                            <td>
                                <div
                                    style="width: 20px; height: 20px; background-color: <?php echo esc_attr($row['color']); ?>; border: 1px solid #ddd;">
                                </div>
                            </td>
                            <?php foreach ($row as $key => $value): ?>
                                <?php if ($datatable['settings']['columns'][$key]): ?>
                                    <td><?php echo esc_html($value); ?></td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <td>
                                <?php Buttons::renderEdit(null, null, array('data-id' => $row['id'])); ?>
                            </td>
                            <td>
                                <?php Inputs::renderCheckBox(null, null, $row['id'], array('id' => 'bookly-check-' . $row['id'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="text-right mt-3">
                <?php Buttons::renderDelete() ?>
            </div>
        </div>
        <?php TableSettings\Dialog::render() ?>
    </div>

    <?php include '_modal.php' ?>
</div>