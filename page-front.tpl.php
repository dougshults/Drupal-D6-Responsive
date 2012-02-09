<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language->language ?>" lang="<?php echo $language->language ?>" dir="<?php echo $language->dir ?>">

<!-- ___________________________ HEAD ___________________________ -->
  <head>

    <title><?php echo $head_title; ?></title>
    
    <?php echo $head; ?>
    <?php echo $styles; ?>
    <?php echo $scripts; ?>

  </head>

<!-- ______________________ BODY BEGINS HERE ______________________ -->
  <body class="<?php echo $body_classes; ?>">

    <div id="skip-nav"><a href="#content">Skip to Content</a></div>  

    <div id="page" class="container">

<!-- ___________________________ HEADER ___________________________ -->
      <?php if ($header): ?>
      <div id="header" class="row">

        <?php if ($logo): ?> 
        <div id="logo">
          <a href="<?php print $base_path ?>" title="<?php print t('Home') ?>"><img src="<?php print $logo ?>" alt="<?php print t('Home') ?>" /></a>
        </div>
        <?php endif; ?>         

        <?php echo $header; ?>

      </div> <!-- ./#header -->
      <?php endif; ?>

<!-- _________________________ CONTENT-AREA _________________________ -->
    <div id="content-area" class="row">

<!-- _________________________ SIDEBAR - FIRST _________________________ -->
        <?php if ($left): ?>
        <div id="sidebar-first" class="<?php echo $sb_first_grid; ?> columns">

          <?php echo $left; ?>     

        </div> <!-- ./#sidebar-left -->
        <?php endif; ?>

<!-- _______________________ (CONTENT) _______________________ -->
        <div id="content" class="<?php echo $content_grid; ?> columns">

          <!-- CONTENT HEADER -->

          <?php if ($breadcrumb or $title or $tabs or $help or $messages or $mission): ?>
          <div id="content-header">

            <?php echo $breadcrumb; ?>

          <?php if ($title): ?>
            <h1 class="title"><?php echo $title; ?></h1>
          <?php endif; ?>

          <?php if ($mission): ?>
            <div id="mission"><?php echo $mission; ?></div>
          <?php endif; ?>

            <?php echo $messages; ?>

          <?php if ($tabs): ?>
            <div class="tabs"><?php echo $tabs; ?></div>
          <?php endif; ?>

            <?php echo $help; ?>

          </div> <!-- ./#content-header -->
          <?php endif; ?>

          <!-- MAIN CONTENT -->

          <div id="main-content">

            <?php echo $content; ?>

          </div> <!-- ./#main content -->

            <?php echo $feed_icons; ?>

          <!-- CONTENT BOTTOM -->

          <?php if ($content_bottom): ?>
          <div id="content-bottom">

            <?php echo $content_bottom; ?>

          </div> <!-- ./#content bottom -->
          <?php endif; ?>

        </div> <!-- ./#content -->

<!-- ________________________ SIDEBAR - LAST _________________________ -->
        <?php if ($right): ?>
        <div id="sidebar-last" class="<?php echo $sb_last_grid; ?> columns">
              
          <?php echo $right; ?>      

        </div> <!-- ./#sidebar-last -->
        <?php endif; ?>


    </div> <!-- ./#content-area -->

<!-- ____________________________ FOOTER ____________________________ -->
      <div id="footer" class="row">

      <?php echo $footer_message; ?>

      <?php echo $footer; ?>
      
      </div> <!-- ./#footer -->

    </div> <!-- ./#page -->

    <?php echo $closure; /* Required for FCK, Google Analytics, etc */ ?>

  </body>
</html>