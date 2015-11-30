<?php

include_once 'includes/password_manager.php';

$plain_password = 'george';
$original = create_hash($plain_password);

echo $original;

echo "<br />";

$password = 'sha256:1000:tnyxtlPZfhAZ4ChDgnZYClz3+TNQCO/c:uNYrfmuV40BKrbVz5A1q2dIjfE6DUhwO';

echo validate_password($plain_password,$password);

?>