<?php
  echo "<ul>";
  $feed_id_param = $_GET['feed_id'];
  echo "<li>feed_id_param: ";
  var_dump($feed_id_param);
  echo "</li>";

  $feed_ids = '';
  foreach($_GET['feed_id'] as $feed_id) {
    $feed_ids = $feed_ids . $feed_id . ",";
  }
  echo "<li>feed_ids: " . $feed_ids . "</li>";

  echo "<li>length of feed_ids: " . count($_GET['feed_id']) . "</li>";
  echo "</ul>";
?>

<html>
  <body>
    <form action="<?php $_PHP_SELF ?>" method="get">
      <input type="checkbox" name="feed_id[]" value="1" />1
      <input type="checkbox" name="feed_id[]" value="2" />2
      <input type="checkbox" name="feed_id[]" value="3" />3
      <input type="checkbox" name="feed_id[]" value="4" />4
      <button type="submit">SUBMIT</button>
    </form>
  </body>
</html>
