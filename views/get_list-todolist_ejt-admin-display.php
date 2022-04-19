<h1>
  <?php _e('EJT Tasks List', 'todolist_ejt'); ?>
</h1>
<p>
  <?php _e('You may manage all tasks for specific user. Choose user you want to edit. <strong>Deafult user is You.</strong><br />
All actions are immediately saved on the server.', 'todolist_ejt'); ?>
</p>
<p>
  <?php _e('Use [todoList] to display tasksTable on front.', 'todolist_ejt'); ?>
</p>

<?php echo do_shortcode('[todoList]'); ?>