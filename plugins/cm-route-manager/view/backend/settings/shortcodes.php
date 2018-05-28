<?php

use com\cminds\mapsroutesmanager\App;

?>
<?php if (App::isPro()): ?>
	<li><kbd>[route-snippet id=route_id featured="one of: image map" layout="one of: list, tiles" fancy=1]</kbd> - displays route's snippet.</li>
	<li><kbd>[route-map id=route_id graph=1 params=1 map=1 topinfo=0 zoom="number from 1 (world) to 20 (buildings)"
		showdate=1 showtitle=1 showtravelmode=1 width=number mapwidth=number mapheight=number]</kbd> - displays route's map.</li>
	<li><kbd>[cm-routes-map category="id or slug" params=1 author="id or slug" width=number mapwidth=number mapheight=number]</kbd>
		- displays the routes map, optionally from chosen category with the elevation graph and\or route params visible.
		You can also filter routes by custom taxonomies slugs (which you can setup on the Settings page under the Taxonomies tab),
		for example: [cm-routes-map cmmrm_route_type=bicycle]</li>
	<li><kbd>[my-routes-table controls=1 addbtn=1]</kbd> - shows table with user's routes, the same as on the user's dashboard page.</li>
<?php endif; ?>