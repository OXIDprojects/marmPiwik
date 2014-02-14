<?php
	/**
	 * Module-Metadata for the marmPiwik.
	 * @author Joscha Krug <krug@marmalade.de>
	 */

	$sMetadataVersion = '1.1';

	$aModule = array(
		'id'		=> 'marmpiwik',
		'title'		=> 'marmalade.de Piwik',
		'thumbnail'	=> 'marmalade_logo.png',
		'url'		=> 'https://github.com/OXIDprojects/marmPiwik',
		'version'	=> '0.6',
		'author'	=> 'marmalade.de',
		'email'		=> 'krug@marmalade.de',
		
		'description'	=> array(
			'de'		=> 'Tracking mit Piwik',
			'en'		=> 'Tracking with Piwik'
		),
		
		'extend' => array(
			'oxoutput'		=> 'marm/piwik/core/marm_piwik_oxoutput'
		),
		
		'files' => array(
			'marm_piwik_setup'	=> 'marm/piwik/controllers/admin/marm_piwik_setup.php',
			'marm_piwik'		=> 'marm/piwik/core/marm_piwik.php',
		),
		
		'templates' => array(
			'marm_piwik_setup.tpl'	=> 'marm/piwik/views/admin/tpl/marm_piwik_setup.tpl'
		)
		
	); //
