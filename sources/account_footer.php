



        <script src="<?php echo filter_var($settings['url']); ?>assets/wallet/js/core/popper.min.js"></script>
        <script src="<?php echo filter_var($settings['url']); ?>assets/wallet/js/core/bootstrap.min.js"></script>
        <script src="<?php echo filter_var($settings['url']); ?>assets/wallet/js/plugins/smooth-scrollbar.min.js"></script>
        <script src="<?php echo filter_var($settings['url']); ?>assets/wallet/js/soft-ui-dashboard.min.js?v=1.0.1"></script>
  
        <script src="<?php echo filter_var($settings['url']); ?>assets/wallet/js/flatpickr.min.js"></script> 
        <script src="<?php echo filter_var($settings['url']); ?>assets/js/jquery-1.12.4.min.js"></script>
        <script src="<?php echo filter_var($settings['url']); ?>assets/js/popper.min.js"></script>
        <script src="<?php echo filter_var($settings['url']); ?>assets/js/bootstrap.min.js"></script>
        <script src="<?php echo filter_var($settings['url']); ?>assets/js/slick.min.js"></script>
        <script src="<?php echo filter_var($settings['url']); ?>assets/js/jquery.peity.min.js"></script>
        <script src="<?php echo filter_var($settings['url']); ?>assets/js/jquery.slimscroll.min.js"></script>
        <script src="<?php echo filter_var($settings['url']); ?>assets/js/custom.js"></script>
        <script src="<?php echo filter_var($settings['url']); ?>assets/js/wallet.js"></script>
        <script src="<?php echo filter_var($settings['url']); ?>assets/plugins/jquery-ui/jquery-ui.min.js"></script>
        <script type="text/javascript">
        $('#datepicker1').datepicker({ dateFormat: "dd-mm-yy"});
        $('#datepicker2').datepicker({ dateFormat: "dd-mm-yy"});
        if ( window.history.replaceState ) {
          window.history.replaceState( null, null, window.location.href );
        }
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
          var options = {
            damping: '0.5'
          }
          Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
        </script>
        <?php if ($m["live_chat"] == "1") { ?>
        <?= $settings['live_chat_code'] ?>
        <?php } ?>
    </body>
</html>