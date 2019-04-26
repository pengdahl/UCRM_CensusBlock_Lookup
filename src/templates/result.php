<?php
   if($result['fips']) {
     echo "Census Block: " . htmlspecialchars($result['fips'], ENT_QUOTES) . "<br>" . PHP_EOL;
   }
   if($result['status']) {
     echo "Status Message: " . htmlspecialchars($result['status'], ENT_QUOTES) . "<br>" . PHP_EOL;
   }
?>
