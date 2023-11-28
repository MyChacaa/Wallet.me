<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
?>



            <br>
            
          </div></div>
          <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
          </aside>
        
        </div>



        <script src="<?= $settings['url']; ?>assets/new/plugins/jquery/jquery.min.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/plugins/jquery-ui/jquery-ui.min.js"></script>
        <script>
          $.widget.bridge('uibutton', $.ui.button)
        </script>
        <script src="<?= $settings['url'] ?>assets/new/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        
        <script src="<?= $settings['url'] ?>assets/new/plugins/chart.js/Chart.min.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/plugins/sparklines/sparkline.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/plugins/jqvmap/jquery.vmap.min.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/plugins/jquery-knob/jquery.knob.min.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/plugins/moment/moment.min.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/plugins/daterangepicker/daterangepicker.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/plugins/summernote/summernote-bs4.min.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        
        <script src="<?= $settings['url'] ?>assets/new/plugins/codemirror/codemirror.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/plugins/codemirror/mode/css/css.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/plugins/codemirror/mode/xml/xml.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/plugins/codemirror/mode/htmlmixed/htmlmixed.js"></script>
        
        <script src="<?= $settings['url'] ?>assets/new/dist/js/adminlte.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/dist/js/demo.js"></script>
        <script src="<?= $settings['url'] ?>assets/new/dist/js/pages/dashboard.js"></script>
        
        
        
        
        
        
        <script>
          $(function () {
            // Summernote
            $('#summernote').summernote()
        
            // CodeMirror
            CodeMirror.fromTextArea(document.getElementById("codeMirrorDemo"), {
              mode: "htmlmixed",
              theme: "monokai"
            });
          })
        </script>
        
        <script>
        if ( window.history.replaceState ) {
          window.history.replaceState( null, null, window.location.href );
        }
        </script>

    </body>
</html>