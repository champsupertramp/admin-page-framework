<?php

include( dirname( __FILE__ ) . '/APF_Widget.php' );
new APF_Widget( 
    __( 'Admin Page Framework', 'admin-page-framework-demo' ) // the widget title
);

include( dirname( __FILE__ ) . '/APF_Widget_CustomFieldTypes.php' );
new APF_Widget_CustomFieldTypes( 
    __( 'APF - Advanced', 'admin-page-framework-demo' ) 
);

include( dirname( __FILE__ ) . '/APF_Widget_Example.php' );
new APF_Widget_Example( 
    __( 'APF - GitHub Button', 'admin-page-framework-demo' ) 
);