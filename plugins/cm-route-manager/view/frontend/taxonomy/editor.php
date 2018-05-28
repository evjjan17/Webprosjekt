<?php

use com\cminds\mapsroutesmanager\helper\FormHtml;

?>
<div class="cmmrm-field">
	<label><?php echo $label; ?>:</label>
	<?php echo FormHtml::selectBox($fieldName, $options, $currentValue); ?>
</div>