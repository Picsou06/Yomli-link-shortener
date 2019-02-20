<!DOCTYPE html>
<html>
<head>
	<title>Yomli Go</title>
	<link rel="shortcut icon" href="./tpl/go.png"/>
	<style type="text/css">
		.hidden {
			display: none;
		}
		body {
			background: url(./tpl/go.png) no-repeat top center;
			padding-top:50px;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", Helvetica, Arial, sans-serif;
		}
		section {
			max-width:800px;
			margin:0 auto;
		}
		input {
			width: 300px;
			padding: 5px;
			border-radius:3px;
			background:rgba(0,0,0,0.1);
			border:1px solid rgba(0,0,0,0.2);
			color:rgba(0,0,0,0.8);
			display: block;
			margin: 5px auto;
		}
		input[type=submit], a.button {
			width:inherit;
			background:rgba(0,0,0,0.7);
			border:1px solid rgba(0,0,0,0.8);
			color:white;
			font-style:normal;
			font-weight:normal;
			padding: 5px;
			margin: 10px;
			border-radius:5px;
			display: inline-block;
		}
		input[name=del] {
			width:620px;
		}
		form {
			text-align: center;
		}
		h1, h2, h3, h4, p {
			text-align:center;
		}
		h1 {
			margin-bottom: 45px;
		}
		h3 {
			font-weight: normal;
		}
		h4 {
			padding-bottom: 25px;
			border-bottom: 1px solid rgba(0,0,0,0.2);
		}
		footer {
			max-width:800px;
			margin:0 auto;
			opacity:0.2;
			margin-top:50px;
			padding-top:25px;
			border-top:1px solid black;
			text-align: center;
		}
		a {
			text-decoration:none;
			color:black;
			font-weight:bold;
		}
		img.qrcode {
			margin: 10px auto;
			display: block;
		}
		a.button {
			display:inline-block;
			width:1.5em;
			height:1.5em;
		}
		a.admin, a.back {
			position: absolute;
			top: 25px;
		}
		a.admin {
			right: 25px;
		}
		a.back {
			left: 25px;
		}
		a#clipboard {
			display: inline-block;
			margin: 0 10px;
			position: relative;
		}
		a#clipboard.copied::after, input[type=text].copied + a::after { /* Input text can't have pseudo-elements, so we have an anchor nearby */
			content: "Copié";
			display: inline-block;
			border: 1px solid rgba(0,0,0,0.1);
			color: initial;
			background: rgba(200,200,200,0.9);
			font-size: 0.8em;
			padding: 3px;
			border-radius: 5px;
			position: absolute;
			left: 3em;
			font-weight: normal;
		}
		input[type=text].copied + a::after {
			left: 1em;
		}
		a.button.text {
			width: inherit;
			height: inherit;
			font-size: 0.78em;
		}
		a.button.red {
			background:rgba(161,14,5,0.7);
			border:1px solid rgba(161,14,5,0.9);
		}
		input[type=text].copied + a {
			position: relative;
		}
		table {
			margin: 25px auto;
			border: 1px solid rgba(0,0,0,0.2);
			border-collapse: collapse;
			border-spacing: 0;
			max-width:800px;
		}
		table th, table td {
			padding: 0.5em 1em;
			border-left: 1px solid rgba(0,0,0,0.2);
			margin: 0;
		}
		table th {
			background-color: rgba(0,0,0,0.1);
		}
		table td {
			background-color: transparent;
			color:rgba(0,0,0,0.8);
		}
		table tr td:first-child {
			width: 100%;
			white-space: no-wrap;
			word-break: break-all;
			overflow: hidden;
		}
		table input[type=text] {
			max-width: 6em;
		}
		@media (max-width:800px){
			input {
				display:block;
				width:80%!important;
				margin:10px auto;
				padding:15px;
				font-size:1.2em;
			}
			section {
				text-align: center;
			}
			table th, table td {
				display: block;
			}
			table td {
				text-align: left;
			}
			table tr {
				border-bottom: 1px solid rgba(0,0,0,0.2);
			}
			table a.button {
				display: inline-block;
			}
			table tr td:last-child {
				text-align: right;
			}
		}
	</style>
</head>
<body><section>
	<h1><a href="index.php">Yomli Go</a></h1>
	<h4>Raccourcisseur d’URL minimaliste</h4>
