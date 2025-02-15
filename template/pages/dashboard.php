<!-- Right side column. Contains the navbar and content of the page -->
<aside class="right-side">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><?php _e('Dashboard'); ?></h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="?route=dashboard"><i class="fa fa-dashboard"></i> <?php _e('Home'); ?></a></li>
                    <li class="breadcrumb-item active"><?php _e('Dashboard'); ?></li>
                </ol>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <?php if(!empty($statusmessage)): ?>
            <div class="row">
                <div class='col-md-12'>
                    <div class="alert alert-<?php print $statusmessage["type"]; ?> alert-dismissible" role="alert">
                        <i class="fa fa-<?php echo ($statusmessage["type"] == 'danger' ? 'exclamation-triangle' : 'info-circle'); ?>"></i>
                        <span><?php print __($statusmessage["message"]); ?></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if(file_exists("install") == 1): ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-triangle"></i>
                <span><?php _e('Please delete the "install" directory!'); ?></span>
            </div>
        <?php endif; ?>

		<!-- Stats Overview -->
		<div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?php echo $servers_count; ?></h3>
                        <p><?php _e('Servers'); ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-server"></i>
                    </div>
                    <a href="?route=servers" class="view-all-link">
                        <?php _e('View all'); ?> <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?php echo $websites_count; ?></h3>
                        <p><?php _e('Websites'); ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-globe"></i>
                    </div>
                    <a href="?route=websites" class="view-all-link">
                        <?php _e('View all'); ?> <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?php echo $checks_count; ?></h3>
                        <p><?php _e('Checks'); ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <a href="?route=checks" class="view-all-link">
                        <?php _e('View all'); ?> <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?php echo $contacts_count; ?></h3>
                        <p><?php _e('Contacts'); ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <a href="?route=contacts" class="view-all-link">
                        <?php _e('View all'); ?> <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Map and Overview Section -->
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="map-container">
                    <h3>
                        <i class="fa fa-map-marker"></i>
                        <?php _e('Around the world'); ?>
                    </h3>
                    <?php if(!$isGoogleMaps) { ?>
                        <div class="alert alert-info mb-3">
                            <i class="fa fa-info-circle"></i>
                            <span><?php _e('Add a Google Maps API key in System > Settings to display monitors status on map.'); ?></span>
                        </div>
                    <?php } ?>
                    <div class="map-wrapper">
                        <?php if(!$isGoogleMaps) { ?>
                            <div id="world-map-markers"></div>
                        <?php } else { ?>
                            <div id="googleMap"></div>
                        <?php } ?>
                        <div class="map-controls">
                            <button class="map-control-btn" title="Zoom in"><i class="fa fa-plus"></i></button>
                            <button class="map-control-btn" title="Zoom out"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Servers Overview -->
                <div class="overview-card">
                    <h3>
                        <i class="fa fa-server"></i>
                        <?php _e('Servers Overview'); ?>
                    </h3>
                    <div class="status-message">
                        <i class="fa fa-check-circle"></i>
                        <?php _e('All Systems Operational'); ?>
                    </div>
                    <p class="text-muted mt-2"><?php _e('Hooray! All servers are healthy.'); ?></p>
                </div>

                <!-- Websites Overview -->
                <div class="overview-card">
                    <h3>
                        <i class="fa fa-globe"></i>
                        <?php _e('Websites Overview'); ?>
                    </h3>
                    <div class="status-message">
                        <i class="fa fa-check-circle"></i>
                        <?php _e('All Systems Operational'); ?>
                    </div>
                    <p class="text-muted mt-2"><?php _e('Hooray! All websites are healthy.'); ?></p>
                </div>

                <!-- Checks Overview -->
                <div class="overview-card">
                    <h3>
                        <i class="fa fa-check-circle"></i>
                        <?php _e('Checks Overview'); ?>
                    </h3>
                    <div class="status-message">
                        <i class="fa fa-check-circle"></i>
                        <?php _e('All Systems Operational'); ?>
                    </div>
                    <p class="text-muted mt-2"><?php _e('Hooray! All checks are healthy.'); ?></p>
                </div>
            </div>
        </div>


<?php if(!$isGoogleMaps) { ?>
	<script type="text/javascript">
		$(document).ready(function() {

			/* jVector Maps
		     * ------------
		     * Create a world map with markers
		     */
		    $('#world-map-markers').vectorMap({
		      	map              : 'world_mill_en',
		      	normalizeFunction: 'polynomial',
		      	hoverOpacity     : 0.7,
		      	hoverColor       : false,
		      	backgroundColor  : 'transparent',
		      	regionStyle      : {
			        initial      : {
			          	fill            : 'rgba(210, 214, 222, 1)',
			          	'fill-opacity'  : 1,
			          	stroke          : 'none',
			          	'stroke-width'  : 0,
			          	'stroke-opacity': 1
			        },
			        hover        : {
			          	'fill-opacity': 0.7,
			          	cursor        : 'pointer'
			        },
			        selected     : {
			          	fill: 'yellow'
			        },
			        selectedHover: {}
		        },
		      	markerStyle      : {
		        	initial: {
		          	fill  : '#00a65a',
		          	stroke: '#111'
		        	}
		      	},
				markers          : [
					<?php foreach($checks as $check) {
						$hasGeodata = false;
						if($check['lat'] != "" && $check['lng'] != "") $hasGeodata = true;

						if($hasGeodata) {

							$lat = $check['lat'];
							$lng = $check['lng'];

							if(!is_decimal($lat)) $lat = $lat . "." . rand(1000,9999);
							if(!is_decimal($lng)) $lng = $lng . "." . rand(1000,9999);

							if($check['status'] == 0) $fill = "#CCCCCC";
							if($check['status'] == 1) $fill = "#00a65a";
							if($check['status'] == 2) $fill = "#f39c12";
							if($check['status'] == 3) $fill = "#dd4b39";

							echo "{ latLng: [".$lat.", ".$lng."], name: '".__("Check: ") . $check['name']."', style: {fill: '".$fill."'} },";
						}

					}
					?>
					<?php foreach($servers as $server) {
						$hasGeodata = false;
						if($server['lat'] != "" && $server['lng'] != "") $hasGeodata = true;

						if($hasGeodata) {

							$lat = $server['lat'];
							$lng = $server['lng'];

							if(!is_decimal($lat)) $lat = $lat . "." . rand(1000,9999);
							if(!is_decimal($lng)) $lng = $lng . "." . rand(1000,9999);

							if($server['status'] == 0) $fill = "#CCCCCC";
							if($server['status'] == 1) $fill = "#00a65a";
							if($server['status'] == 2) $fill = "#f39c12";
							if($server['status'] == 3) $fill = "#dd4b39";

							echo "{ latLng: [".$lat.", ".$lng."], name: '".__("Server: ") . $server['name']."', style: {fill: '".$fill."'} },";
						}


					}
					?>
					<?php foreach($websites as $website) {
						$hasGeodata = false;
						if($website['lat'] != "" && $website['lng'] != "") $hasGeodata = true;

						if($hasGeodata) {

							$lat = $website['lat'];
							$lng = $website['lng'];

							if(!is_decimal($lat)) $lat = $lat . "." . rand(1000,9999);
							if(!is_decimal($lng)) $lng = $lng . "." . rand(1000,9999);

							if($website['status'] == 0) $fill = "#CCCCCC";
							if($website['status'] == 1) $fill = "#00a65a";
							if($website['status'] == 2) $fill = "#f39c12";
							if($website['status'] == 3) $fill = "#dd4b39";

							echo "{ latLng: [".$lat.", ".$lng."], name: '".__("Website: ") . $website['name']."', style: {fill: '".$fill."'} },";
						}


					}
					?>

				]
			});

		});
	</script>
<?php } ?>

<?php if($isGoogleMaps) { ?>
	<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo getConfigValue("google_maps_api_key"); ?>"></script>
	<script type="text/javascript">
		var mlocations = [
			<?php foreach($checks as $check) {
				$hasGeodata = false;
				if($check['lat'] != "" && $check['lng'] != "") $hasGeodata = true;

				if($hasGeodata) {

					$lat = $check['lat'];
					$lng = $check['lng'];

					if(!is_decimal($lat)) $lat = $lat . "." . rand(1000,9999);
					if(!is_decimal($lng)) $lng = $lng . "." . rand(1000,9999);

					if($check['status'] == 0) $icon = "check-gray";
					if($check['status'] == 1) $icon = "check-green";
					if($check['status'] == 2) $icon = "check-orange";
					if($check['status'] == 3) $icon = "check-red";

					echo "['".__("Check: ") . $check['name']."', ".$lat.",".$lng.", 2, '".$icon."', '".__("Check: ") . $check['name']."'],";
				}

			}
			?>

			<?php foreach($servers as $server) {
				$hasGeodata = false;
				if($server['lat'] != "" && $server['lng'] != "") $hasGeodata = true;

				if($hasGeodata) {

					$lat = $server['lat'];
					$lng = $server['lng'];

					if(!is_decimal($lat)) $lat = $lat . "." . rand(1000,9999);
					if(!is_decimal($lng)) $lng = $lng . "." . rand(1000,9999);

					if($server['status'] == 0) $icon = "server-gray";
					if($server['status'] == 1) $icon = "server-green";
					if($server['status'] == 2) $icon = "server-orange";
					if($server['status'] == 3) $icon = "server-red";

					echo "['".__("Server: ") . $server['name']."', ".$lat.",".$lng.", 2, '".$icon."', '".__("Server: ") . $server['name']."'],";
				}


			}
			?>
			<?php foreach($websites as $website) {
				$hasGeodata = false;
				if($website['lat'] != "" && $website['lng'] != "") $hasGeodata = true;

				if($hasGeodata) {

					$lat = $website['lat'];
					$lng = $website['lng'];

					if(!is_decimal($lat)) $lat = $lat . "." . rand(1000,9999);
					if(!is_decimal($lng)) $lng = $lng . "." . rand(1000,9999);

					if($website['status'] == 0) $icon = "website-gray";
					if($website['status'] == 1) $icon = "website-green";
					if($website['status'] == 2) $icon = "website-orange";
					if($website['status'] == 3) $icon = "website-red";

					echo "['".__("Website: ") . $website['name']."', ".$lat.",".$lng.", 2, '".$icon."', '".__("Website: ") . $website['name']."'],";
					//echo "{ latLng: [".$lat.", ".$lng."], name: '".__("Website: ") . $website['name']."', style: {fill: '".$fill."'} },";
				}


			}
			?>
		];

		var mapCenter = new google.maps.LatLng(0, 0);
		var bounds = new google.maps.LatLngBounds();

		function initialize() {
			var mapData = {
				center:mapCenter,
				zoom:2,
				mapTypeId:google.maps.MapTypeId.ROADMAP
			};

			var map = new google.maps.Map(document.getElementById("googleMap"),mapData);

			for (i = 0; i < mlocations.length; i++) {
				var infowindow = new google.maps.InfoWindow({
					content: mlocations[i][5]
				});

				var marker = new google.maps.Marker({
					position: new google.maps.LatLng(mlocations[i][1], mlocations[i][2]),
					map: map,
					title: mlocations[i][0],
					icon:'template/images/'+mlocations[i][4]+'.png',
					infowindow: infowindow
				});
				bounds.extend(marker.position);

				marker.addListener('click', function() {
					this.infowindow.open(map, this);
				});


				marker.setMap(map);
			}

			map.fitBounds(bounds);

		}

		google.maps.event.addDomListener(window, 'load', initialize);



	</script>
<?php } ?>
