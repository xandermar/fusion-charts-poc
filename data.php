<?php
header('Content-Type: application/javascript');
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

function getData(){

require_once __DIR__.'/SimpleXLSX.php';

if ( $xlsx = SimpleXLSX::parse('fusioncharts-poc.xlsx') ) {
	// print_r( $xlsx->rows() );

	foreach($xlsx->rows() as $x){
		if(!empty($x[1])){
            $all[] = $x;
            $agency[] = $x[1];
		}
	}

    } else {
        echo SimpleXLSX::parseError();
    }

    $ret='{id: "Agency",parent: "",label: "Agency",value: ""},';
    $agencies = array_unique($agency);

    // render the agencies
    foreach($agencies as $agencyname){
        if($agencyname!='Agency'){
            $ret .= '{id: "'.$agencyname.'",parent: "Agency",label: "'.$agencyname.'",value: ""},';
        }
    }

    // render the program
    foreach($all as $a){
        if(!empty($a[2]) && $a[3]!='Program' && empty($a[3])){
            $ret .= '{id: "'.$a[2].'",parent: "'.$a[1].'",label: "'.$a[2].'",value: "'.$a[4].'"},';
        }
    }

    // render the subs
    foreach($all as $a){
        if(!empty($a[3]) && $a[4]!=' #VALUE! ' && $a[3]!='Sub'){
            $ret .= '{id: "'.$a[3].'",parent: "'.$a[2].'",label: "'.$a[3].'",value: "'.$a[4].'"},';
        }
    }

    return $ret;

}


print 'const chartData = [';

// print '
//         {id: "Agency",parent: "",label: "Agency",value: ""},
//         {id: "Armed Forces Retirement Home Trust Fund",parent: "Agency",label: "Armed Forces Retirement Home Trust Fund",value: "0"},
//         {id: "General Fund: $2,800,000 (until 9/30/2021)",parent: "Armed Forces Retirement Home Trust Fund",label: "General Fund: $2,800,000 (until 9/30/2021)",value: "2800000"},
//         {id:"International Disaster Assistance: $258,000,000 (until expended)",parent: "Armed Forces Retirement Home Trust Fund",label:"International Disaster Assistance: $258,000,000 (until expended)",value: "258000000"},
//       ];
// ';

print getData();

print '];';

print '
      const chartConfig = {
        type: "sunburst",
        renderAt: "chart-container",
        width: "100%",
        height: "400",
        dataFormat: "json",
        dataSource: {
          // Chart Configuration
          chart: {
            theme: "fusion",
            caption: "Agency Awards",
            subcaption: "Click on the segments to Drill-down",
            showPlotBorder: "1",
            animation: "1",
            paletteColors:
              "#D7D8E7, #A88CCC, #77ECC8, #97FAA4, #CFF69D, #EED482, #FFAE91, #FE93B5, #D98ACF, #7BCDE8, #94A8E9",
            animationDuration: "2",
            centerAngle: "360"
          },
          // Chart Data
          data: chartData
        }
      };
      FusionCharts.ready(function() {
        var fusioncharts = new FusionCharts(chartConfig);
        fusioncharts.render();
      });
';