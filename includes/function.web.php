<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

function protect($string) {
	$protection = htmlspecialchars(trim($string), ENT_QUOTES);
	return $protection;
}

function formatBytes($bytes, $precision = 2) { 
    if ($bytes > pow(1024,3)) return round($bytes / pow(1024,3), $precision)."GB";
    else if ($bytes > pow(1024,2)) return round($bytes / pow(1024,2), $precision)."MB";
    else if ($bytes > 1024) return round($bytes / 1024, $precision)."KB";
    else return ($bytes)."B";
} 

function randomHash($lenght = 7) {
	$random = substr(md5(rand()),0,$lenght);
	return $random;
}

function isValidURL($url) {
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}

function checkAdminSession() {
	if(isset($_SESSION['admin_uid'])) {
		return true;
	} else {
		return false;
	}
}

function isValidUsername($str) {
    return preg_match('/^[a-zA-Z0-9-_]+$/',$str);
}

function isValidEmail($str) {
	return filter_var($str, FILTER_VALIDATE_EMAIL);
}

function checkSession() {
	if(isset($_SESSION['pw_uid'])) {
		return true;
	} else {
		return false;
	}
}

function timeago($time)
{
   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
   $lengths = array("60","60","24","7","4.35","12","10");

   $now = time();

       $difference     = $now - $time;
       $tense         = "ago";

   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
       $difference /= $lengths[$j];
   }

   $difference = round($difference);

   if($difference != 1) {
       $periods[$j].= "s";
   }

   return "$difference $periods[$j] ago ";
}
function PW_secure_directory() {
	global $db, $settings;
	$secure_directory = md5($settings['name']);
	$secure_directory = substr($secure_directory,0,15);
	$secure_directory = md5($secure_directory);
	$secure_directory = substr($secure_directory,0,15);
	$secure_directory = md5($secure_directory);
	$secure_directory = substr($secure_directory,0,20);
	return $secure_directory;
}
function getFiatCurrencies() {
	$currencies = array('AED' => 'AED - United Arab Emirates Dirham',
						'AFN' => 'AFN - Afghanistan Afghani',
						'ALL' => 'ALL - Albania Lek',
						'AMD' => 'AMD - Armenia Dram',
						'ANG' => 'ANG - Netherlands Antilles Guilder',
						'AOA' => 'AOA - Angola Kwanza',
						'ARS' => 'ARS - Argentina Peso',
						'AUD' => 'AUD - Australia Dollar',
						'AWG' => 'AWG - Aruba Guilder',
						'AZN' => 'AZN - Azerbaijan New Manat',
						'BAM' => 'BAM - Bosnia and Herzegovina Convertible Marka',
						'BBD' => 'BBD - Barbados Dollar',
						'BDT' => 'BDT - Bangladesh Taka',
						'BGN' => 'BGN - Bulgaria Lev',
						'BHD' => 'BHD - Bahrain Dinar',
						'BIF' => 'BIF - Burundi Franc',
						'BMD' => 'BMD - Bermuda Dollar',
						'BND' => 'BND - Brunei Darussalam Dollar',
						'BOB' => 'BOB - Bolivia Boliviano',
						'BRL' => 'BRL - Brazil Real',
						'BSD' => 'BSD - Bahamas Dollar',
						'BTN' => 'BTN - Bhutan Ngultrum',
						'BWP' => 'BWP - Botswana Pula',
						'BYR' => 'BYR - Belarus Ruble',
						'BZD' => 'BZD - Belize Dollar',
						'CAD' => 'CAD - Canada Dollar',
						'CDF' => 'CDF - Congo/Kinshasa Franc',
						'CHF' => 'CHF - Switzerland Franc',
						'CLP' => 'CLP - Chile Peso',
						'CNY' => 'CNY - China Yuan Renminbi',
						'COP' => 'COP - Colombia Peso',
						'CRC' => 'CRC - Costa Rica Colon',
						'CUC' => 'CUC - Cuba Convertible Peso',
						'CUP' => 'CUP - Cuba Peso',
						'CVE' => 'CVE - Cape Verde Escudo',
						'CZK' => 'CZK - Czech Republic Koruna',
						'DJF' => 'DJF - Djibouti Franc',
						'DKK' => 'DKK - Denmark Krone',
						'DOP' => 'DOP - Dominican Republic Peso',
						'DZD' => 'DZD - Algeria Dinar',
						'EGP' => 'EGP - Egypt Pound',
						'ERN' => 'ERN - Eritrea Nakfa',
						'ETB' => 'ETB - Ethiopia Birr',
						'EUR' => 'EUR - Euro Member Countries',
						'FJD' => 'FJD - Fiji Dollar',
						'FKP' => 'FKP - Falkland Islands (Malvinas) Pound',
						'GBP' => 'GBP - United Kingdom Pound',
						'GEL' => 'GEL - Georgia Lari',
						'GGP' => 'GGP - Guernsey Pound',
						'GHS' => 'GHS - Ghana Cedi',
						'GIP' => 'GIP - Gibraltar Pound',
						'GMD' => 'GMD - Gambia Dalasi',
						'GNF' => 'GNF - Guinea Franc',
						'GTQ' => 'GTQ - Guatemala Quetzal',
						'GYD' => 'GYD - Guyana Dollar',
						'HKD' => 'HKD - Hong Kong Dollar',
						'HNL' => 'HNL - Honduras Lempira',
						'HPK' => 'HRK - Croatia Kuna',
						'HTG' => 'HTG - Haiti Gourde',
						'HUF' => 'HUF - Hungary Forint',
						'IDR' => 'IDR - Indonesia Rupiah',
						'ILS' => 'ILS - Israel Shekel',
						'IMP' => 'IMP - Isle of Man Pound',
						'INR' => 'INR - India Rupee',
						'IQD' => 'IQD - Iraq Dinar',
						'IRR' => 'IRR - Iran Rial',
						'ISK' => 'ISK - Iceland Krona',
						'JEP' => 'JEP - Jersey Pound',
						'JMD' => 'JMD - Jamaica Dollar',
						'JOD' => 'JOD - Jordan Dinar',
						'JPY' => 'JPY - Japan Yen',
						'KES' => 'KES - Kenya Shilling',
						'KGS' => 'KGS - Kyrgyzstan Som',
						'KHR' => 'KHR - Cambodia Riel',
						'KMF' => 'KMF - Comoros Franc',
						'KPW' => 'KPW - Korea (North) Won',
						'KRW' => 'KRW - Korea (South) Won',
						'KWD' => 'KWD - Kuwait Dinar',
						'KYD' => 'KYD - Cayman Islands Dollar',
						'KZT' => 'KZT - Kazakhstan Tenge',
						'LAK' => 'LAK - Laos Kip',
						'LBP' => 'LBP - Lebanon Pound',
						'LKR' => 'LKR - Sri Lanka Rupee',
						'LRD' => 'LRD - Liberia Dollar',
						'LSL' => 'LSL - Lesotho Loti',
						'LYD' => 'LYD - Libya Dinar',
						'MAD' => 'MAD - Morocco Dirham',
						'MDL' => 'MDL - Moldova Leu',
						'MGA' => 'MGA - Madagascar Ariary',
						'MKD' => 'MKD - Macedonia Denar',
						'MMK' => 'MMK - Myanmar (Burma) Kyat',
						'MNT' => 'MNT - Mongolia Tughrik',
						'MOP' => 'MOP - Macau Pataca',
						'MRO' => 'MRO - Mauritania Ouguiya',
						'MUR' => 'MUR - Mauritius Rupee',
						'MVR' => 'MVR - Maldives (Maldive Islands) Rufiyaa',
						'MWK' => 'MWK - Malawi Kwacha',
						'MXN' => 'MXN - Mexico Peso',
						'MYR' => 'MYR - Malaysia Ringgit',
						'MZN' => 'MZN - Mozambique Metical',
						'NAD' => 'NAD - Namibia Dollar',
						'NGN' => 'NGN - Nigeria Naira',
						'NTO' => 'NIO - Nicaragua Cordoba',
						'NOK' => 'NOK - Norway Krone',
						'NPR' => 'NPR - Nepal Rupee',
						'NZD' => 'NZD - New Zealand Dollar',
						'OMR' => 'OMR - Oman Rial',
						'PAB' => 'PAB - Panama Balboa',
						'PEN' => 'PEN - Peru Nuevo Sol',
						'PGK' => 'PGK - Papua New Guinea Kina',
						'PHP' => 'PHP - Philippines Peso',
						'PKR' => 'PKR - Pakistan Rupee',
						'PLN' => 'PLN - Poland Zloty',
						'PYG' => 'PYG - Paraguay Guarani',
						'QAR' => 'QAR - Qatar Riyal',
						'RON' => 'RON - Romania New Leu',
						'RSD' => 'RSD - Serbia Dinar',
						'RUB' => 'RUB - Russia Ruble',
						'RWF' => 'RWF - Rwanda Franc',
						'SAR' => 'SAR - Saudi Arabia Riyal',
						'SBD' => 'SBD - Solomon Islands Dollar',
						'SCR' => 'SCR - Seychelles Rupee',
						'SDG' => 'SDG - Sudan Pound',
						'SEK' => 'SEK - Sweden Krona',
						'SGD' => 'SGD - Singapore Dollar',
						'SHP' => 'SHP - Saint Helena Pound',
						'SLL' => 'SLL - Sierra Leone Leone',
						'SOS' => 'SOS - Somalia Shilling',
						'SRL' => 'SPL* - Seborga Luigino',
						'SRD' => 'SRD - Suriname Dollar',
						'STD' => 'STD - Sao Tome and Principe Dobra',
						'SVC' => 'SVC - El Salvador Colon',
						'SYP' => 'SYP - Syria Pound',
						'SZL' => 'SZL - Swaziland Lilangeni',
						'THB' => 'THB - Thailand Baht',
						'TJS' => 'TJS - Tajikistan Somoni',
						'TMT' => 'TMT - Turkmenistan Manat',
						'TND' => 'TND - Tunisia Dinar',
						'TOP' => 'TOP - Tonga Paanga',
						'TRY' => 'TRY - Turkey Lira',
						'TTD' => 'TTD - Trinidad and Tobago Dollar',
						'TVD' => 'TVD - Tuvalu Dollar',
						'TWD' => 'TWD - Taiwan New Dollar',
						'TZS' => 'TZS - Tanzania Shilling',
						'UAH' => 'UAH - Ukraine Hryvnia',
						'UGX' => 'UGX - Uganda Shilling',
						'USD' => 'USD - United States Dollar',
						'UYU' => 'UYU - Uruguay Peso',
						'UZS' => 'UZS - Uzbekistan Som',
						'VEF' => 'VEF - Venezuela Bolivar',
						'VND' => 'VND - Viet Nam Dong',
						'VUV' => 'VUV - Vanuatu Vatu',
						'WST' => 'WST - Samoa Tala',
						'XAF' => 'XAF - Communaute Financiere Africaine (BEAC) CFA Franc BEAC',
						'XCD' => 'XCD - East Caribbean Dollar',
						'XDR' => 'XDR - International Monetary Fund (IMF) Special Drawing Rights',
						'XOF' => 'XOF - Communaute Financiere Africaine (BCEAO) Franc',
						'XPF' => 'XPF - Comptoirs Francais du Pacifique (CFP) Franc',
						'YER' => 'YER - Yemen Rial',
						'ZAR' => 'ZAR - South Africa Rand',
						'ZMW' => 'ZMW - Zambia Kwacha',
						'ZWD' => 'ZWD - Zimbabwe Dollar');
	return $currencies;
}

function getCountries() {
	$countries = array('AL' => 'Albania',
	'DZ' => 'Algeria',
	'AD' => 'Andorra',
	'AO' => 'Angola',
	'AI' => 'Anguilla',
	'AG' => 'Antigua &amp; Barbuda',
	'AR' => 'Argentina',
	'AM' => 'Armenia',
	'AW' => 'Aruba',
	'AU' => 'Australia',
	'AT' => 'Austria',
	'AZ' => 'Azerbaijan',
	'BS' => 'Bahamas',
	'BH' => 'Bahrain',
	'BB' => 'Barbados',
	'BY' => 'Belarus',
	'BE' => 'Belgium',
	'BZ' => 'Belize',
	'BJ' => 'Benin',
	'BM' => 'Bermuda',
	'BT' => 'Bhutan',
	'BO' => 'Bolivia',
	'BA' => 'Bosnia &amp; Herzegovina',
	'BW' => 'Botswana',
	'BR' => 'Brazil',
	'VG' => 'British Virgin Islands',
	'BN' => 'Brunei',
	'BG' => 'Bulgaria',
	'BF' => 'Burkina Faso',
	'BI' => 'Burundi',
	'KH' => 'Cambodia',
	'CM' => 'Cameroon',
	'CA' => 'Canada',
	'CV' => 'Cape Verde',
	'KY' => 'Cayman Islands',
	'TD' => 'Chad',
	'CL' => 'Chile',
	'C2' => 'China',
	'CO' => 'Colombia',
	'KM' => 'Comoros',
	'CG' => 'Congo - Brazzaville',
	'CD' => 'Congo - Kinshasa',
	'CK' => 'Cook Islands',
	'CR' => 'Costa Rica',
	'CI' => 'Cote d’Ivoire',
	'HR' => 'Croatia',
	'CY' => 'Cyprus',
	'CZ' => 'Czech Republic',
	'DK' => 'Denmark',
	'DJ' => 'Djibouti',
	'DM' => 'Dominica',
	'DO' => 'Dominican Republic',
	'EC' => 'Ecuador',
	'EG' => 'Egypt',
	'SV' => 'El Salvador',
	'ER' => 'Eritrea',
	'EE' => 'Estonia',
	'ET' => 'Ethiopia',
	'FK' => 'Falkland Islands',
	'FO' => 'Faroe Islands',
	'FJ' => 'Fiji',
	'FI' => 'Finland',
	'FR' => 'France',
	'GF' => 'French Guiana',
	'PF' => 'French Polynesia',
	'GA' => 'Gabon',
	'GM' => 'Gambia',
	'GE' => 'Georgia',
	'DE' => 'Germany',
	'GI' => 'Gibraltar',
	'GR' => 'Greece',
	'GL' => 'Greenland',
	'GD' => 'Grenada',
	'GP' => 'Guadeloupe',
	'GT' => 'Guatemala',
	'GN' => 'Guinea',
	'GW' => 'Guinea-Bissau',
	'GY' => 'Guyana',
	'HN' => 'Honduras',
	'HK' => 'Hong Kong SAR China',
	'HU' => 'Hungary',
	'IS' => 'Iceland',
	'IN' => 'India',
	'ID' => 'Indonesia',
	'IE' => 'Ireland',
	'IL' => 'Israel',
	'IT' => 'Italy',
	'JM' => 'Jamaica',
	'JP' => 'Japan',
	'JO' => 'Jordan',
	'KZ' => 'Kazakhstan',
	'KE' => 'Kenya',
	'KI' => 'Kiribati',
	'KW' => 'Kuwait',
	'KG' => 'Kyrgyzstan',
	'LA' => 'Laos',
	'LV' => 'Latvia',
	'LS' => 'Lesotho',
	'LI' => 'Liechtenstein',
	'LT' => 'Lithuania',
	'LU' => 'Luxembourg',
	'MK' => 'Macedonia',
	'MG' => 'Madagascar',
	'MW' => 'Malawi',
	'MY' => 'Malaysia',
	'MV' => 'Maldives',
	'ML' => 'Mali',
	'MT' => 'Malta',
	'MH' => 'Marshall Islands',
	'MQ' => 'Martinique',
	'MR' => 'Mauritania',
	'MU' => 'Mauritius',
	'YT' => 'Mayotte',
	'MX' => 'Mexico',
	'FM' => 'Micronesia',
	'MD' => 'Moldova',
	'MC' => 'Monaco',
	'MN' => 'Mongolia',
	'ME' => 'Montenegro',
	'MS' => 'Montserrat',
	'MA' => 'Morocco',
	'MZ' => 'Mozambique',
	'NA' => 'Namibia',
	'NR' => 'Nauru',
	'NP' => 'Nepal',
	'NL' => 'Netherlands',
	'AN' => 'Netherlands Antilles',
	'NC' => 'New Caledonia',
	'NZ' => 'New Zealand',
	'NI' => 'Nicaragua',
	'NE' => 'Niger',
	'NG' => 'Nigeria',
	'NU' => 'Niue',
	'NF' => 'Norfolk Island',
	'NO' => 'Norway',
	'OM' => 'Oman',
	'PK' => 'Pakistan',
	'PW' => 'Palau',
	'PA' => 'Panama',
	'PG' => 'Papua New Guinea',
	'PY' => 'Paraguay',
	'PE' => 'Peru',
	'PH' => 'Philippines',
	'PN' => 'Pitcairn Islands',
	'PL' => 'Poland',
	'PT' => 'Portugal',
	'QA' => 'Qatar',
	'RE' => 'Reunion',
	'RO' => 'Romania',
	'RU' => 'Russia',
	'RW' => 'Rwanda',
	'WS' => 'Samoa',
	'SM' => 'San Marino',
	'ST' => 'Sao Tome &amp; Principe',
	'SA' => 'Saudi Arabia',
	'SN' => 'Senegal',
	'RS' => 'Serbia',
	'SC' => 'Seychelles',
	'SL' => 'Sierra Leone',
	'SG' => 'Singapore',
	'SK' => 'Slovakia',
	'SI' => 'Slovenia',
	'SB' => 'Solomon Islands',
	'SO' => 'Somalia',
	'ZA' => 'South Africa',
	'KR' => 'South Korea',
	'ES' => 'Spain',
	'LK' => 'Sri Lanka',
	'SH' => 'St. Helena',
	'KN' => 'St. Kitts &amp; Nevis',
	'LC' => 'St. Lucia',
	'PM' => 'St. Pierre &amp; Miquelon',
	'VC' => 'St. Vincent &amp; Grenadines',
	'SR' => 'Suriname',
	'SJ' => 'Svalbard &amp; Jan Mayen',
	'SZ' => 'Swaziland',
	'SE' => 'Sweden',
	'CH' => 'Switzerland',
	'TW' => 'Taiwan',
	'TJ' => 'Tajikistan',
	'TZ' => 'Tanzania',
	'TH' => 'Thailand',
	'TG' => 'Togo',
	'TO' => 'Tonga',
	'TT' => 'Trinidad &amp; Tobago',
	'TN' => 'Tunisia',
	'TM' => 'Turkmenistan',
	'TC' => 'Turks &amp; Caicos Islands',
	'TV' => 'Tuvalu',
	'UG' => 'Uganda',
	'UA' => 'Ukraine',
	'AE' => 'United Arab Emirates',
	'GB' => 'United Kingdom',
	'US' => 'United States',
	'UY' => 'Uruguay',
	'VU' => 'Vanuatu',
	'VA' => 'Vatican City',
	'VE' => 'Venezuela',
	'VN' => 'Vietnam',
	'WF' => 'Wallis &amp; Futuna',
	'YE' => 'Yemen',
	'ZM' => 'Zambia',
	'ZW' => 'Zimbabwe');
	return $countries;
}

function VerifyGoogleRecaptcha($response) {
	global $db, $settings;
	$secret = $settings['recaptcha_privatekey'];
	$ch = curl_init();
	$url = "https://www.google.com/recaptcha/api/siteverify";
	$data = "secret=$secret&response=$response";
	// Disable SSL verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// Will return the response, if false it print the response
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Set the url
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	// Execute
	$result=curl_exec($ch);
	// Closing
	curl_close($ch);
	$json = json_decode($result, true);
	if($json['success'] == true) {
		return true;
	} else {
		return false;
	}
}
?>