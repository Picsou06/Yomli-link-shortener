<?php

/*
 *	Password-protected
 *
 *	You can protect the actions of
 *	adding a new link and deleting links.
 *
 *	By default, the admin panel is protected.
 *	The first time you access the admin panel
 *	it will ask you for a new password.
 *	If you forgot your password, just delete
 *	the folder
 *	core/auto_restrict/auto_restrict_files
 *	and the admin panel will ask you for a new
 *	password.
 *
 *	$privateGo = false;	// Password will be asked for deletion only
 *	$privateGo = true;	// Password will be asked for addition as well
 *
 *	Comment if you want to disable all
 * 	security (this include the protection of
 * 	the admin panel) : // $privateGo;
 */
$privateGo = false;

/*
 *	QRCode
 *
 *	Just provide an URL to display QRCode
 *	e.g. https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=
 *
 *	You can use timovn's QRCode-URL if you want
 *	to get a self-hosted API:
 *	https://github.com/timovn/qrcode-url
 *	
 *	Leave empty if you want to disable QRCode
 */
// $qrcodeAPI = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=";
$qrcodeAPI = "http://localhost/vhosts/apps.yom.li/qrcode/?q=";
