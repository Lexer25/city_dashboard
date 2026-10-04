<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?php echo __('log_files'); ?></h3>
  </div>
  <div class="panel-body">

    <!-- Ряд 1: старые логи + framework -->
    <div class="row">

      <!-- Левая колонка: старые логи -->
      <div class="col-md-4">
        <div class="panel panel-primary" style="margin-bottom:0;">
          <div class="panel-heading"><?php echo __('log1'); ?> (<?php echo count($list1); ?>)</div>
          <div class="panel-body" style="max-height:600px; overflow-y:auto;">
            <?php if (count($list1) > 0): ?>
                <?php foreach ($list1 as $relPath => $fullPath): ?>
                    <?php
                        echo HTML::anchor(
                            'dashboard/sendFile?name=' . urlencode($fullPath),
                            HTML::chars($relPath)
                        );
                    ?><br>
                <?php endforeach; ?>
            <?php else: ?>
                <?php echo __('no_log'); ?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Правая колонка: framework -->
      <div class="col-md-8">
        <div class="panel panel-primary" style="margin-bottom:0;">
          <div class="panel-heading">
            <?php echo __('log2'); ?>
            &nbsp;
            <span class="text-muted small">
              <?php echo HTML::chars($fm['path'] === '' ? '/' : '/' . $fm['path']); ?>
            </span>
          </div>
          <div class="panel-body" style="max-height:600px; overflow-y:auto;">

            <?php if ($fm['path'] !== ''): ?>
                <?php
                    echo HTML::anchor(
                        'dashboard/log?path1=' . urlencode($fm['parent'] === NULL ? '' : $fm['parent'])
                            . '&path2=' . urlencode(Arr::get($_GET, 'path2', '')),
                        '.. (вверх)'
                    );
                ?>
                <br>
            <?php endif; ?>

            <?php foreach ($fm['dirs'] as $d): ?>
                <?php
                    echo HTML::anchor(
                        'dashboard/log?path1=' . urlencode($d['rel'])
                            . '&path2=' . urlencode(Arr::get($_GET, 'path2', '')),
                        '[ ' . HTML::chars($d['name']) . ' ]'
                    );
                ?><br>
            <?php endforeach; ?>

            <?php foreach ($fm['files'] as $f): ?>
                <?php
                    $size = number_format($f['size'], 0, '.', ' ');
                    $date = date('d.m.Y H:i', $f['mtime']);
                    echo HTML::anchor(
                        'dashboard/sendFile?name=' . urlencode(
                            $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $f['rel'])
                        ),
                        HTML::chars($f['name'])
                    );
                    echo ' <span class="text-muted small">' . $date . ' · ' . $size . ' B</span>';
                ?><br>
            <?php endforeach; ?>

            <?php if (empty($fm['dirs']) && empty($fm['files'])): ?>
                <span class="text-muted"><?php echo __('no_log'); ?></span>
            <?php endif; ?>

          </div>
        </div>
      </div>

    </div>

    <!-- Ряд 2: ArtonitServices -->
    <div class="row" style="margin-top:15px;">
      <div class="col-md-12">
        <div class="panel panel-primary" style="margin-bottom:0;">
          <div class="panel-heading">
            <?php echo __('log3'); // строка перевода для ArtonitServices ?>
            &nbsp;
            <span class="text-muted small">
              <?php echo HTML::chars($fm_services['path'] === '' ? '/' : '/' . $fm_services['path']); ?>
            </span>
          </div>
          <div class="panel-body" style="max-height:600px; overflow-y:auto;">

            <?php if ($fm_services['path'] !== ''): ?>
                <?php
                    echo HTML::anchor(
                        'dashboard/log?path1=' . urlencode(Arr::get($_GET, 'path1', ''))
                            . '&path2=' . urlencode($fm_services['parent'] === NULL ? '' : $fm_services['parent']),
                        '.. (вверх)'
                    );
                ?>
                <br>
            <?php endif; ?>

            <?php foreach ($fm_services['dirs'] as $d): ?>
                <?php
                    echo HTML::anchor(
                        'dashboard/log?path1=' . urlencode(Arr::get($_GET, 'path1', ''))
                            . '&path2=' . urlencode($d['rel']),
                        '[ ' . HTML::chars($d['name']) . ' ]'
                    );
                ?><br>
            <?php endforeach; ?>

            <?php foreach ($fm_services['files'] as $f): ?>
                <?php
                    $size = number_format($f['size'], 0, '.', ' ');
                    $date = date('d.m.Y H:i', $f['mtime']);
                    echo HTML::anchor(
                        'dashboard/sendFile?name=' . urlencode(
                            $root_services . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $f['rel'])
                        ),
                        HTML::chars($f['name'])
                    );
                    echo ' <span class="text-muted small">' . $date . ' · ' . $size . ' B</span>';
                ?><br>
            <?php endforeach; ?>

            <?php if (empty($fm_services['dirs']) && empty($fm_services['files'])): ?>
                <span class="text-muted"><?php echo __('no_log'); ?></span>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>