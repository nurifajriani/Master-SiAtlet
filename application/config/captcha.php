<?php

$config = array(
	'ContactCaptcha' => array(
		'UserInputID' => 'CaptchaCode',
		'CodeLength' => CaptchaRandomization::GetRandomCodeLength(4, 6),
		'ImageStyle' => ImageStyle::AncientMosaic,
	),

	'LoginCaptcha' => array(
		'UserInputID' => 'CaptchaCode',
		'CodeLength' => CaptchaRandomization::GetRandomCodeLength(4, 6),
		'ImageStyle' => array(
			ImageStyle::Radar,
			ImageStyle::Collage,
			ImageStyle::Fingerprints,
		),
	),

);