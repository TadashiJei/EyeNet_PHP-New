<?php
// Get server info
$server = getRowById("app_servers", $_GET['id']);
$latest = Server::latestData($server['id']);
$qstats = [];

if(!empty($latest)) {
    $qstats = Server::quickStats($latest['data'], $server['type']);
}

// Get incidents
$unresolved_incidents = getTableFiltered("app_servers_incidents", "serverid", $server['id'], "status[!]", 1);
$unresolved_status = "success";
foreach($unresolved_incidents as $incident) {
    if($incident['status'] == 3) $unresolved_status = "danger";
    if($incident['status'] == 2 && $unresolved_status != "danger") $unresolved_status = "warning";
}

// Get alerts
$alerts = getTableFiltered("app_servers_alerts", "serverid", $server['id']);

// Get section
if(!isset($_GET['section'])) $_GET['section'] = "overview";
$section = $_GET['section'];

// Get history for charts
if(!isset($_SESSION['range_start'])) $_SESSION['range_start'] = date("Y-m-d H:i:s", strtotime('-1 hour'));
if(!isset($_SESSION['range_end'])) $_SESSION['range_end'] = date("Y-m-d H:i:s");
$history = $database->select("app_servers_history", "*", [ "serverid" => $server['id'], "timestamp[<>]" => [$_SESSION['range_start'], $_SESSION['range_end']], "ORDER" => ["id" => "ASC"] ]);
?>

<aside class="right-side">
    <!-- Content Header -->
    <section class="content-header">
        <h1><?php echo $server['name']; ?><small> <?php echo smartDate($latest['timestamp']); ?></small></h1>
        <ol class="breadcrumb">
            <li><a href="?route=dashboard"><i class="fa fa-dashboard"></i> <?php _e('Home'); ?></a></li>
            <li><a href="?route=servers"><?php _e('Servers'); ?></a></li>
            <li class="active"><?php echo $server['name']; ?></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <?php if(!empty($statusmessage)): ?>
            <div class="row"><div class='col-md-12'><div class="alert alert-<?php print $statusmessage["type"]; ?> alert-auto" role="alert"><?php print __($statusmessage["message"]); ?></div></div></div>
        <?php endif; ?>
        
        <div class='row'>
            <div class='col-md-12'>
                <!-- Custom Tabs -->
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li <?php if ($section == "" or $section == "overview") echo 'class="active"'; ?> >
                            <a href="?route=servers/manage-macos&id=<?php echo $server['id']; ?>&section=" ><?php _e('Overview'); ?></a>
                        </li>
                        <li <?php if ($section == "system") echo 'class="active"'; ?> >
                            <a href="?route=servers/manage-macos&id=<?php echo $server['id']; ?>&section=system"><?php _e('System'); ?></a>
                        </li>
                        <li <?php if ($section == "power") echo 'class="active"'; ?> >
                            <a href="?route=servers/manage-macos&id=<?php echo $server['id']; ?>&section=power"><?php _e('Power'); ?></a>
                        </li>
                        <li <?php if ($section == "thermal") echo 'class="active"'; ?> >
                            <a href="?route=servers/manage-macos&id=<?php echo $server['id']; ?>&section=thermal"><?php _e('Thermal'); ?></a>
                        </li>
                        <li <?php if ($section == "security") echo 'class="active"'; ?> >
                            <a href="?route=servers/manage-macos&id=<?php echo $server['id']; ?>&section=security"><?php _e('Security'); ?></a>
                        </li>
                        <li <?php if ($section == "alerting") echo 'class="active"'; ?> >
                            <a href="?route=servers/manage-macos&id=<?php echo $server['id']; ?>&section=alerting"><?php _e('Alerting'); ?></a>
                        </li>
                        <li <?php if ($section == "incidents") echo 'class="active"'; ?> >
                            <a href="?route=servers/manage-macos&id=<?php echo $server['id']; ?>&section=incidents"><?php _e('Incidents'); ?></a>
                        </li>

                        <div class="btn-group pull-right" style="padding:6px;">
                            <?php if ($section == "alerting") { ?>
                                <a data-toggle='tooltip' title='Add Alert' class="btn btn-primary btn-flat btn-sm" href="#" onClick='showM("?modal=serveralerts/add&reroute=servers/manage-macos&routeid=<?php echo $server['id']; ?>");return false'><i class="fa fa-plus"></i> ADD ALERT</a>
                            <?php } ?>
                            
                            <button type="button" class="btn btn-default btn-flat btn-sm pull-right" id="daterange-btn">
                                <i class="fa fa-calendar fa-fw"></i> <span><?php _e('Date Range'); ?></span> <i class="fa fa-caret-down fa-fw"></i>
                            </button>
                            
                            <form role="form" method="post" enctype="multipart/form-data" id="rangeForm">
                                <input type="hidden" name="action" value="setRange">
                                <input type="hidden" name="range_start" id="range_start" value="">
                                <input type="hidden" name="range_end" id="range_end" value="">
                                <input type="hidden" name="range_label" id="range_label" value="">
                                <input type="hidden" name="asset" value="server-<?php echo $_GET['id']; ?>">
                                <input type="hidden" name="route" value="<?php echo $_GET['route']; ?>">
                                <input type="hidden" name="routeid" value="<?php echo $_GET['id']; ?>">
                                <input type="hidden" name="section" value="<?php if(!empty($_GET['section'])) echo $_GET['section']; ?>">
                            </form>
                        </div>
                    </ul>


                            <li <?php if ($section == "power") echo 'class="active"'; ?>>
                                <a href="?route=servers/manage-macos&id=<?php echo $server['id']; ?>&section=power"><?php _e('Power'); ?></a>
                            </li>
                            <li <?php if ($section == "thermal") echo 'class="active"'; ?>>
                                <a href="?route=servers/manage-macos&id=<?php echo $server['id']; ?>&section=thermal"><?php _e('Thermal'); ?></a>
                            </li>
                            <li <?php if ($section == "security") echo 'class="active"'; ?>>
                                <a href="?route=servers/manage-macos&id=<?php echo $server['id']; ?>&section=security"><?php _e('Security'); ?></a>
                            </li>
                            <li <?php if ($section == "alerting") echo 'class="active"'; ?>>
                                <a href="?route=servers/manage-macos&id=<?php echo $server['id']; ?>&section=alerting"><?php _e('Alerting'); ?></a>
                            </li>
                            <li <?php if ($section == "incidents") echo 'class="active"'; ?>>
                                <a href="?route=servers/manage-macos&id=<?php echo $server['id']; ?>&section=incidents"><?php _e('Incidents'); ?></a>
                            </li>
                        </ul>
                        <?php if(in_array("editServer",$perms)) { ?>
                            <div class="tab-button">
                                <a data-toggle='tooltip' title='Add Alert' class="btn btn-primary btn-flat btn-sm" href="#" onClick='showM("?modal=serveralerts/add&reroute=servers/manage-macos&routeid=<?php echo $server['id']; ?>");return false'><i class="fa fa-plus"></i> ADD ALERT</a>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="tab-content">
                        <!-- Overview Tab -->
                        <div class="tab-pane <?php if ($section == "" || $section == "overview") echo 'active'; ?>" id="overview">
                            <?php if(empty($history)) { ?>
                                <div class="alert alert-warning" role="alert">
                                    <h4><i class="icon fa fa-warning"></i> <?php _e('No data!'); ?></h4>
                                    <?php _e('No data available for the selected period or no data has been received yet.'); ?>
                                </div>
                            <?php } else { ?>
                                <div class='row'>
                                    <div class='col-md-8'>
                                        <!-- System Overview -->
                                        <div class="box box-primary">
                                            <div class="box-header">
                                                <h3 class="box-title"><?php _e('System Overview'); ?></h3>
                                            </div>
                                            <div class="box-body">
                                                <div class="row">
                                                    <!-- CPU Usage -->
                                                    <div class="col-md-3">
                                                        <div class="info-box">
                                                            <span class="info-box-icon bg-aqua"><i class="fa fa-microchip"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text"><?php _e('CPU Usage'); ?></span>
                                                                <span class="info-box-number"><?php echo isset($qstats['cpuused']) ? $qstats['cpuused'] . '%' : 'N/A'; ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Memory Usage -->
                                                    <div class="col-md-3">
                                                        <div class="info-box">
                                                            <span class="info-box-icon bg-green"><i class="fa fa-memory"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text"><?php _e('Memory Usage'); ?></span>
                                                                <span class="info-box-number">
                                                                    <?php 
                                                                    if(isset($qstats['ramreal']) && isset($qstats['ramtotal'])) {
                                                                        echo round(($qstats['ramreal'] / $qstats['ramtotal']) * 100) . '%';
                                                                    } else {
                                                                        echo 'N/A';
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Battery Status -->
                                                    <?php if(isset($qstats['macos']['power']['battery_percentage'])): ?>
                                                    <div class="col-md-3">
                                                        <div class="info-box">
                                                            <span class="info-box-icon bg-yellow">
                                                                <i class="fa fa-battery-<?php echo floor($qstats['macos']['power']['battery_percentage']/25); ?>"></i>
                                                            </span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text"><?php _e('Battery'); ?></span>
                                                                <span class="info-box-number">
                                                                    <?php echo $qstats['macos']['power']['battery_percentage']; ?>%
                                                                </span>
                                                                <span class="info-box-text">
                                                                    <?php echo isset($qstats['macos']['power']['battery_health']) ? $qstats['macos']['power']['battery_health'] : ''; ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>

                                                    <!-- CPU Temperature -->
                                                    <?php if(isset($qstats['macos']['thermal']['cpu_temperature'])): ?>
                                                    <div class="col-md-3">
                                                        <div class="info-box">
                                                            <span class="info-box-icon bg-red"><i class="fa fa-thermometer-half"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text"><?php _e('CPU Temp'); ?></span>
                                                                <span class="info-box-number">
                                                                    <?php echo $qstats['macos']['thermal']['cpu_temperature']; ?>°C
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Performance Charts -->
                                        <div class="box box-primary">
                                            <div class="box-header">
                                                <h3 class="box-title"><?php _e('Performance Metrics'); ?></h3>
                                            </div>
                                            <div class="box-body">
                                                <div class="chart">
                                                    <canvas id="performanceChart" style="height:250px"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='col-md-4'>
                                        <!-- Alerts & Incidents -->
                                        <?php if(!empty($unresolved_incidents)) { ?>
                                            <div class="box box-<?php echo $unresolved_status; ?> box-solid">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title"><?php _e('Incidents'); ?></h3>
                                                    <div class="pull-right box-tools">
                                                        <button type="button" class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="box-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th><?php _e('Type'); ?></th>
                                                                    <th><?php _e('Status'); ?></th>
                                                                    <th><?php _e('Time'); ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach($unresolved_incidents as $incident) { ?>
                                                                <tr>
                                                                    <td><?php echo ucfirst($incident['type']); ?></td>
                                                                    <td>
                                                                        <?php if($incident['status'] == 2) { ?>
                                                                            <span class="label label-warning"><?php _e('Warning'); ?></span>
                                                                        <?php } elseif($incident['status'] == 3) { ?>
                                                                            <span class="label label-danger"><?php _e('Critical'); ?></span>
                                                                        <?php } ?>
                                                                    </td>
                                                                    <td><?php echo smartDate($incident['starttime']); ?></td>
                                                                </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <!-- System Info -->
                                        <div class="box box-primary">
                                            <div class="box-header">
                                                <h3 class="box-title"><?php _e('System Information'); ?></h3>
                                            </div>
                                            <div class="box-body">
                                                <table class="table table-striped">
                                                    <tr>
                                                        <td><strong><?php _e('OS Version'); ?></strong></td>
                                                        <td><?php echo isset($qstats['macos']['system']['os_version']) ? $qstats['macos']['system']['os_version'] : 'N/A'; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong><?php _e('Model'); ?></strong></td>
                                                        <td><?php echo isset($qstats['macos']['system']['model']) ? $qstats['macos']['system']['model'] : 'N/A'; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong><?php _e('Uptime'); ?></strong></td>
                                                        <td><?php echo isset($qstats['uptime']) ? $qstats['uptime'] : 'N/A'; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong><?php _e('Last Update'); ?></strong></td>
                                                        <td><?php echo smartDate($latest['timestamp']); ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- System Tab -->
                        <div class="tab-pane <?php if ($section == "system") echo 'active'; ?>" id="system">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-primary">
                                        <div class="box-header">
                                            <h3 class="box-title"><?php _e('System Resources'); ?></h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <!-- CPU Usage Chart -->
                                                    <div id="cpu-chart" style="height: 300px;"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <!-- Memory Usage Chart -->
                                                    <div id="memory-chart" style="height: 300px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Power Tab -->
                        <div class="tab-pane <?php if ($section == "power") echo 'active'; ?>" id="power">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-primary">
                                        <div class="box-header">
                                            <h3 class="box-title"><?php _e('Power Management'); ?></h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <!-- Battery Status -->
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-green"><i class="fa fa-battery-full"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text"><?php _e('Battery Status'); ?></span>
                                                            <span class="info-box-number">
                                                                <?php 
                                                                    $battery_percent = isset($qstats['macos']['power']['battery_percentage']) ? $qstats['macos']['power']['battery_percentage'] : 'N/A';
                                                                    echo $battery_percent . '%';
                                                                ?>
                                                            </span>
                                                            <span class="progress-description">
                                                                <?php echo isset($qstats['macos']['power']['battery_health']) ? $qstats['macos']['power']['battery_health'] : 'N/A'; ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thermal Tab -->
                        <div class="tab-pane <?php if ($section == "thermal") echo 'active'; ?>" id="thermal">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-primary">
                                        <div class="box-header">
                                            <h3 class="box-title"><?php _e('Thermal Monitoring'); ?></h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <!-- CPU Temperature -->
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-red"><i class="fa fa-thermometer-half"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text"><?php _e('CPU Temperature'); ?></span>
                                                            <span class="info-box-number">
                                                                <?php 
                                                                    $cpu_temp = isset($qstats['macos']['thermal']['cpu_temperature']) ? $qstats['macos']['thermal']['cpu_temperature'] : 'N/A';
                                                                    echo $cpu_temp . '°C';
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Tab -->
                        <div class="tab-pane <?php if ($section == "security") echo 'active'; ?>" id="security">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-primary">
                                        <div class="box-header">
                                            <h3 class="box-title"><?php _e('Security Status'); ?></h3>
                                        </div>
                                        <div class="box-body">
                                            <table class="table table-striped">
                                                <tr>
                                                    <td><strong><?php _e('FileVault Status'); ?></strong></td>
                                                    <td>
                                                        <?php 
                                                            $filevault = isset($qstats['macos']['security']['filevault_enabled']) ? $qstats['macos']['security']['filevault_enabled'] : false;
                                                            echo $filevault ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>';
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php _e('System Integrity Protection'); ?></strong></td>
                                                    <td>
                                                        <?php 
                                                            $sip = isset($qstats['macos']['security']['sip_enabled']) ? $qstats['macos']['security']['sip_enabled'] : false;
                                                            echo $sip ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>';
                                                        ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alerting Tab -->
                        <div class="tab-pane <?php if ($section == "alerting") echo 'active'; ?>" id="alerting">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-primary">
                                        <div class="box-header">
                                            <h3 class="box-title"><?php _e('Configured Alerts'); ?></h3>
                                            <?php if(in_array("editServer",$perms)) { ?>
                                                <div class="box-tools pull-right">
                                                    <a data-toggle='tooltip' title='Add Alert' class="btn btn-primary btn-flat btn-sm" href="#" onClick='showM("?modal=serveralerts/add&reroute=servers/manage-macos&routeid=<?php echo $server['id']; ?>&section=alerting");return false'><i class="fa fa-plus"></i> <?php _e('Add Alert'); ?></a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="box-body">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php _e('Type'); ?></th>
                                                        <th><?php _e('Trigger'); ?></th>
                                                        <th><?php _e('Actions'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($alerts as $alert) { ?>
                                                    <tr>
                                                        <td><?php echo ucfirst($alert['type']); ?></td>
                                                        <td><?php echo $alert['trigger']; ?></td>
                                                        <td>
                                                            <?php if(in_array("editServer",$perms)) { ?>
                                                                <a href="#" onClick='showM("?modal=serveralerts/edit&reroute=servers/manage-macos&routeid=<?php echo $server['id']; ?>&id=<?php echo $alert['id']; ?>&section=alerting");return false' class="btn btn-success btn-flat btn-sm"><i class="fa fa-edit"></i></a>
                                                                <a href="#" onClick='showM("?modal=serveralerts/delete&reroute=servers/manage-macos&routeid=<?php echo $server['id']; ?>&id=<?php echo $alert['id']; ?>&section=alerting");return false' class="btn btn-danger btn-flat btn-sm"><i class="fa fa-trash-o"></i></a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Incidents Tab -->
                        <div class="tab-pane <?php if ($section == "incidents") echo 'active'; ?>" id="incidents">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-primary">
                                        <div class="box-header">
                                            <h3 class="box-title"><?php _e('Incident History'); ?></h3>
                                        </div>
                                        <div class="box-body">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php _e('Type'); ?></th>
                                                        <th><?php _e('Status'); ?></th>
                                                        <th><?php _e('Start Time'); ?></th>
                                                        <th><?php _e('End Time'); ?></th>
                                                        <th><?php _e('Comment'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($unresolved_incidents as $incident) { ?>
                                                    <tr>
                                                        <td><?php echo ucfirst($incident['type']); ?></td>
                                                        <td>
                                                            <?php if($incident['status'] == 2) { ?>
                                                                <span class="label label-warning"><?php _e('Warning'); ?></span>
                                                            <?php } elseif($incident['status'] == 3) { ?>
                                                                <span class="label label-danger"><?php _e('Critical'); ?></span>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?php echo smartDate($incident['starttime']); ?></td>
                                                        <td>
                                                            <?php if($incident['end_time'] != "0000-00-00 00:00:00") echo smartDate($incident['end_time']); else { ?>
                                                                <a href="#" onClick='showM("?modal=serveralerts/markResolved&reroute=servers/manage-macos&routeid=<?php echo $server['id']; ?>&id=<?php echo $incident['id']; ?>&section=incidents");return false'><?php _e("Mark Resolved"); ?></a>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $incident['comment']; ?>
                                                            <?php if($incident['ignore'] == '1') { ?> [<?php _e('IGNORED'); ?>]<?php } ?>
                                                            <a href="#" onClick='showM("?modal=serveralerts/editComment&reroute=servers/manage-macos&routeid=<?php echo $server['id']; ?>&id=<?php echo $incident['id']; ?>&section=incidents");return false'><?php if($incident['comment'] == "") _e("Add"); else _e("Edit"); ?></a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>
</aside>

<script type="text/javascript">
    $(document).ready(function() {
        <?php if(isset($_GET['popinstall']) || empty($latest)) { ?>
            showM("?modal=servers/install-macos&id=<?php echo $server['id']; ?>");
        <?php } ?>

        // Initialize performance chart
        <?php if (!empty($history) && ($section == "" || $section == "overview")) { ?>
            var ctx = document.getElementById('performanceChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [<?php 
                        foreach($history as $item) {
                            echo "'" . date('H:i', strtotime($item['timestamp'])) . "',";
                        }
                    ?>],
                    datasets: [{
                        label: 'CPU Usage (%)',
                        data: [<?php 
                            foreach($history as $item) {
                                $data = json_decode(gzuncompress($item['data']), true);
                                $cpu = isset($data['cpu']) ? $data['cpu'] : 0;
                                echo $cpu . ",";
                            }
                        ?>],
                        borderColor: '#3e95cd',
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        <?php } ?>
    });
</script>
