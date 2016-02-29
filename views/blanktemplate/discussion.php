<div class="cpm-blank-template discussion" style="display: none">
    <div class="cpm-content" >
        <h2>  <?php _e('Discussion', 'cpm') ?> </h2>

        <p>
            <?php _e('Use our built in discussion panel to create an open discussion, a group discussion or a private conversation. Note that the Admin can always moderate these discussions.
', 'cpm') ?>
        </p>

        <p>
            <img src="<?php echo plugins_url('../assets/images/blank_discussion.svg', dirname(__FILE__)) ?> " />
        </p>

  <?php
          $can_create = cpm_user_can_access( $project_id, 'create_message' );
          if ( $can_create ) { ?>
        <div>
            <a class="cpm-btn cpm-plus-white cpm-new-message-btn" href="JavaScript:void(0)" id="cpm-add-message" > <?php _e( 'ADD NEW DISCUSSION', 'cpm' ); ?> </a>
        </div>
        <div class="cpm-new-message-form">
            <h3 ><?php _e( 'Create a new message', 'cpm' ); ?></h3>

            <?php echo cpm_discussion_form( $project_id ); ?>
        </div>

    <?php } ?>

        <h2 class="cpm-why-for"> <?php _e('When to use Discussions?', 'cpm') ?> </h2>

        <ul class="cpm-list">
            <li> <?php _e('To discuss a work matter privately.', 'cpm') ?> </li>
            <li> <?php _e('To exchange files privately.', 'cpm') ?>  </li>
            <li> <?php _e('To discuss in a group.', 'cpm') ?>  </li>
            <li> <?php _e('To create an open discussion visible to all.', 'cpm') ?>  </li>

        </ul>

    </div>
</div>