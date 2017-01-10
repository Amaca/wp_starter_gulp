
        </div>
        <!-- /Main -->
        
        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                
                    <?php language_selector(); ?>
                    <?php 
                    $menuParameters = array(
                        'menu' => 'Footer Menu', 
                        'container'       => false,
                        'container_class' => false,
                        'echo'            => false,
                        'menu_id'         => false,
                        'items_wrap'      => '%3$s',
                        'depth'           => 0,
                    );
                    echo strip_tags(wp_nav_menu( $menuParameters ), '<a>' );
                    ?>  
            </div>
        </footer>
        <!-- /Footer -->

    </div>
    <!-- /Wrapper -->
    
    <?php wp_footer(); ?>
</body>
</html>