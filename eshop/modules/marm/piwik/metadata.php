<?php
	/**
	 * Module-Metadata for the marmPiwik.
	 * @author Joscha Krug <krug@marmalade.de>
	 */

	$sMetadataVersion = '1.0';

	$aModule = array(
		'author'       => 'marmalade.de',
		'description'  => array(
			'de' => 'Tracking mit Piwik',
			'en' => 'Tracking with Piwik'
		),
		'email'        => 'krug@marmalade.de',
		'extend' => array(
			'oxoutput'  => 'marm/piwik/marm_piwik_oxoutput'
		),
		'id'           => 'marmpiwik',
		'title'        => 'marmalade.de Piwik',
		'thumbnail'    => 'marmalade_logo.png',
		'url'          => 'http://www.marmalade.de',
		'version'      => '0.5'
	);