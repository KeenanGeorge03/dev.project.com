<?php

	// function to get all subjects from database.
	function get_all_identifications($dbh){
		$fetch_results = $dbh->prepare('SELECT

										 a.rb_name,
										 a.rb_mac_address,
										 a.radio_name,
										 a.mode,
										 a.ssid,
										 a.frequency,
										 a.scan_list,
										 a.wireless_protocol,
										 a.date_time,
										 b.tx_ccq,
										 b.rx_ccq

										FROM rb_identity_tbl as a
										inner join
										rb_registration_tbl as b on b.rb_mac_address = a.rb_mac_address

										where a.rb_mac_address in(SELECT DISTINCT rb_mac_address from rb_identity_tbl)

										ORDER BY a.date_time desc
										LIMIT 0,1');
		
     	$fetch_results->execute();
     	$results = $fetch_results->fetchAll();
     	return $results;
	}
?>


