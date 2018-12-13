
<?php foreach($lang_files as $lfkey => $lf): ?>
    <h3><?php echo $lf['title']; ?></h3>
    <ul id="translationPanelList" class="mb20 mt10 list">
        <?php foreach($lf['files'] as $fkey => $file): ?>
            <li class="list pointer" draggable="true" data-type="<?php echo $lfkey; ?>" data-filename="<?php echo $file['filename']; ?>" data-path="<?php echo $file['path']; ?>" data-lang-path="<?php echo $file['lang_path']; ?>">
                <a class="left title unselectable"><?php echo $file['filename']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>
<script type="text/javascript">

    $$('#translationPanelList li').each(function(item)
    {
        var type = item.getProperty('data-type'),
            filename = item.getProperty('data-filename'),
            path = item.getProperty('data-path'),
            lang_path = item.getProperty('data-lang-path');

        // Display details
        item.getElement('a.title').addEvent('click', function()
        {
            ION.HTML(
                ION.adminUrl + 'translation/edit',
                {
                    'type': type,
                    'filename': filename,
                    'path': path,
                    'lang_path': lang_path
                },
                {'update': 'splitPanel_mainPanel_pad'}
            );
        });
    });

</script>